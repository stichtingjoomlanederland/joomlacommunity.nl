<?php
/**
 * Akeeba Engine
 * The PHP-only site backup engine
 *
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Postproc;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Postproc\Connector\Ovh as OvhConnector;
use Psr\Log\LogLevel;

/**
 * A post processing engine used to upload files to OVH object storage
 */
class Ovh extends Base
{
	/**
	 * Public constructor. Initialises the advertised properties of this processing engine
	 */
	public function __construct()
	{
		$this->can_delete              = true;
		$this->can_download_to_file    = true;
		$this->can_download_to_browser = false;
	}

	/**
	 * Uploads a backup archive part to CloudFiles
	 *
	 * @param string $absolute_filename
	 * @param null   $upload_as
	 *
	 * @return bool|int
	 */
	public function processPart($absolute_filename, $upload_as = null)
	{
		$settings = $this->_getEngineSettings();

		if ($settings === false)
		{
			return false;
		}

		/** @var string $projectid */
		/** @var string $username */
		/** @var string $password */
		/** @var string $containerurl */
		/** @var string $directory */
		extract($settings);

		// Calculate relative remote filename
		$filename = empty($upload_as) ? basename($absolute_filename) : $upload_as;

		if (!empty($directory) && ($directory != '/'))
		{
			$filename = $directory . '/' . $filename;
		}

		// Store the absolute remote path in the class property
		$this->remote_path = $filename;

		try
		{
			Factory::getLog()->log(LogLevel::DEBUG, 'Authenticating to OVH');

			// Create the API connector object
			$ovh = new OvhConnector($projectid, $username, $password);
			$ovh->setStorageEndpoint($containerurl);

			// Authenticate
			$ovh->getToken();

			// Upload the file
			Factory::getLog()->log(LogLevel::DEBUG, 'Uploading ' . basename($absolute_filename));
			$input = array(
				'file' => $absolute_filename
			);
			$ovh->putObject($input, $filename, 'application/octet-stream');
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Implements object deletion
	 */
	public function delete($path)
	{
		$settings = $this->_getEngineSettings();

		if ($settings === false)
		{
			return false;
		}

		/** @var string $projectid */
		/** @var string $username */
		/** @var string $password */
		/** @var string $containerurl */
		/** @var string $directory */
		extract($settings);

		try
		{
			Factory::getLog()->log(LogLevel::DEBUG, 'Authenticating to OVH');

			// Create the API connector object
			$ovh = new OvhConnector($projectid, $username, $password);
			$ovh->setStorageEndpoint($containerurl);

			// Authenticate
			$ovh->getToken();

			// Delete the file
			Factory::getLog()->log(LogLevel::DEBUG, 'Deleting ' . $path);
			$ovh->deleteObject($path);
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		return true;
	}

	public function downloadToFile($remotePath, $localFile, $fromOffset = null, $length = null)
	{
		$settings = $this->_getEngineSettings();

		if ($settings === false)
		{
			return false;
		}

		/** @var string $projectid */
		/** @var string $username */
		/** @var string $password */
		/** @var string $containerurl */
		/** @var string $directory */
		extract($settings);

		try
		{
			Factory::getLog()->log(LogLevel::DEBUG, 'Authenticating to OVH');

			// Create the API connector object
			$ovh = new OvhConnector($projectid, $username, $password);
			$ovh->setStorageEndpoint($containerurl);

			// Authenticate
			$ovh->getToken();

			// Do we need to set a range header?
			$headers = array();

			if (!is_null($fromOffset) && is_null($length))
			{
				$headers['Range'] = 'bytes=' . $fromOffset;
			}
			elseif (!is_null($fromOffset) && !is_null($length))
			{
				$headers['Range'] = 'bytes=' . $fromOffset . '-' . ($fromOffset + $length - 1);
			}
			elseif (!is_null($length))
			{
				$headers['Range'] = 'bytes=0-' . ($fromOffset + $length);
			}

			if (!empty($headers))
			{
				Factory::getLog()->log(LogLevel::DEBUG, 'Sending Range header «' . $headers['Range'] . '»');
			}

			$fp = @fopen($localFile, 'wb');

			if ($fp === false)
			{
				throw new \Exception("Can't open $localFile for writing");
			}

			Factory::getLog()->log(LogLevel::DEBUG, 'Downloading ' . $remotePath);
			$ovh->downloadObject($remotePath, $fp, $headers);

			@fclose($fp);
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Returns the post-processing engine settings in array format. If something is amiss it returns boolean false.
	 *
	 * @return array|bool
	 */
	protected function _getEngineSettings()
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		$projectid    = trim($config->get('engine.postproc.ovh.projectid', ''));
		$username     = trim($config->get('engine.postproc.ovh.username', ''));
		$password     = trim($config->get('engine.postproc.ovh.password', ''));
		$containerurl = $config->get('engine.postproc.ovh.containerurl', 0);
		$directory    = $config->get('volatile.postproc.directory', null);

		if (empty($directory))
		{
			$directory = $config->get('engine.postproc.ovh.directory', 0);
		}

		// Sanity checks
		if (empty($projectid))
		{
			$this->setWarning('You have not set up your OVH Project ID');

			return false;
		}

		if (empty($username))
		{
			$this->setWarning('You have not set up your OVH OpenStack Username');

			return false;
		}

		if (empty($password))
		{
			$this->setWarning('You have not set up your OVH OpenStack Password');

			return false;
		}

		if (empty($containerurl))
		{
			$this->setWarning('You have not set up your Container URL');

			return false;
		}

		if (!function_exists('curl_init'))
		{
			$this->setWarning('cURL is not enabled, please enable it in order to post-process your archives');

			return false;
		}

		// Fix the directory name, if required
		if (!empty($directory))
		{
			$directory = trim($directory);
			$directory = ltrim(Factory::getFilesystemTools()->TranslateWinPath($directory), '/');
		}
		else
		{
			$directory = '';
		}

		// Parse tags
		$directory = Factory::getFilesystemTools()->replace_archive_name_variables($directory);
		$config->set('volatile.postproc.directory', $directory);

		return array(
			'projectid'    => $projectid,
			'username'     => $username,
			'password'     => $password,
			'containerurl' => $containerurl,
			'directory'    => $directory,
		);
	}
}
