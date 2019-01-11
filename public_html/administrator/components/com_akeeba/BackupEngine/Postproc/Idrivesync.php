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
use Akeeba\Engine\Postproc\Connector\Idrivesync as ConnectorIdrivesync;

class Idrivesync extends Base
{
	/** @var string iDriveSync login email / username */
	protected $email;

	/** @var string iDriveSync password */
	protected $password;

	/** @var string iDriveSync private key */
	protected $pvtkey;

	/** @var ConnectorIdrivesync The iDriveSync API instance */
	private $idrivesync;

	/** @var string The currently configured directory */
	private $directory;

	public function __construct()
	{
		$this->can_download_to_browser = false;
		$this->can_delete = true;
		$this->can_download_to_file = true;
	}

	public function processPart($absolute_filename, $upload_as = null)
	{
		$settings = $this->_getSettings();
		if ($settings === false)
		{
			return false;
		}

		$directory = $this->directory;

		// Store the absolute remote path in the class property
		$this->remote_path = $directory . '/' . basename($absolute_filename);

		try
		{
			$this->idrivesync->uploadFile($absolute_filename, '/' . $directory . '/');
			$result = true;
		}
		catch (\Exception $e)
		{
			$result = false;
			$this->setWarning('iDriveSync error' . $e->getMessage() . ' -- Remote path: ' . $directory);
		}

		$this->idrivesync = null;

		if ($result === false)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function downloadToFile($remotePath, $localFile, $fromOffset = null, $length = null)
	{
		// Get settings
		$settings = $this->_getSettings();
		if ($settings === false)
		{
			return false;
		}

		if (!is_null($fromOffset))
		{
			// Ranges are not supported
			return -1;
		}

		// Get the remote path's components
		$remoteDirectory = trim(dirname($remotePath), '/');
		$remoteFilename = basename($remotePath);

		// Download the file
		$done = false;
		try
		{
			$this->idrivesync->downloadFile($remotePath, $localFile);
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getMessage());
			$this->idrivesync = null;

			return false;
		}

		$this->idrivesync = null;

		return true;
	}

	public function delete($path)
	{
		// Get settings
		$settings = $this->_getSettings();
		if ($settings === false)
		{
			return false;
		}

		$done = false;
		try
		{
			$this->idrivesync->deleteFile($path);
			$done = true;
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getMessage());
			$this->idrivesync = null;

			return false;
		}

		$this->idrivesync = null;

		return true;
	}

	protected function _getSettings()
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		$username = trim($config->get('engine.postproc.idrivesync.username', ''));
		$password = trim($config->get('engine.postproc.idrivesync.password', ''));
		$pvtkey = trim($config->get('engine.postproc.idrivesync.pvtkey', ''));
		$this->directory = $config->get('volatile.postproc.directory', null);
		$newendpoint = $config->get('engine.postproc.idrivesync.newendpoint', false);

		if (empty($this->directory))
		{
			$this->directory = $config->get('engine.postproc.idrivesync.directory', '');
		}

		// Sanity checks
		if (empty($username) || empty($password))
		{
			$this->setError('You have not set up the connection to your iDriveSync account');

			return false;
		}

        if(!function_exists('curl_init'))
        {
            $this->setWarning('cURL is not enabled, please enable it in order to post-process your archives');

            return false;
        }

		// Fix the directory name, if required
		if (!empty($this->directory))
		{
			$this->directory = trim($this->directory);
			$this->directory = ltrim(Factory::getFilesystemTools()->TranslateWinPath($this->directory), '/');
		}
		else
		{
			$this->directory = '';
		}

		// Parse tags
		$this->directory = Factory::getFilesystemTools()->replace_archive_name_variables($this->directory);
		$config->set('volatile.postproc.directory', $this->directory);

		try
		{
			$this->idrivesync = new ConnectorIdrivesync($username, $password, $pvtkey, $newendpoint);
		}
		catch(\Exception $e)
		{
			$this->setWarning('iDriveSync '.$e->getCode().' '.$e->getMessage());
			return false;
		}

		return true;
	}
}
