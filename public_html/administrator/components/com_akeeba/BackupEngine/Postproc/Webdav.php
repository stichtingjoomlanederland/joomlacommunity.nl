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
use Akeeba\Engine\Postproc\Connector\Davclient as ConnectorDavclient;
use Psr\Log\LogLevel;

class Webdav extends Base
{
	/** @var string Username */
	protected $username;

	/** @var string Password */
	protected $password;

	/** @var int The retry count of this file (allow up to 2 retries after the first upload failure) */
	protected $tryCount = 0;

	/** @var ConnectorDavclient  WebDAV client */
	protected $webdav;

	/** @var string The currently configured directory */
	protected $directory;

	/** @var string The key used for storing settings in the Configuration registry */
	protected $settingsKey = 'webdav';

	public function __construct()
	{
		$this->can_download_to_browser = false;
		$this->can_delete = true;
		$this->can_download_to_file = true;
	}

	public function processPart($absolute_filename, $upload_as = null)
	{
		$settings = $this->getSettings();

		if ($settings === false)
		{
			return false;
		}

		$directory = $this->directory;

		$basename = empty($upload_as) ? basename($absolute_filename) : $upload_as;

		try
		{
			$result = $this->putFile($absolute_filename, $directory, $basename);
		}
		catch (\Exception $e)
		{
			$result = false;
			$this->setWarning($e->getMessage());
		}

		if ($result === false)
		{
			// Let's retry
			$this->tryCount++;
			// However, if we've already retried twice, we stop retrying and call it a failure
			if ($this->tryCount > 2)
			{
				return false;
			}

			return -1;
		}
		else
		{
			// Upload complete. Reset the retry counter.
			$this->tryCount = 0;

			return true;
		}
	}

	public function downloadToFile($remotePath, $localFile, $fromOffset = null, $length = null)
	{
		// Get settings
		$settings = $this->getSettings();
		if ($settings === false)
		{
			return false;
		}

		if (!is_null($fromOffset))
		{
			// Ranges are not supported
			return -1;
		}

		// Download the file
		$done = false;
		try
		{
			$handle = fopen($localFile, 'w+');
			$this->webdav->request('GET', $remotePath, $handle);
			$done = true;

			fclose($handle);
		}
		catch (\Exception $e)
		{
			fclose($handle);
		}

		if (!$done)
		{
			try
			{
				$handle = fopen($localFile, 'w+');
				$this->webdav->request('GET', $remotePath, $handle);
				fclose($handle);
			}
			catch (\Exception $e)
			{
				fclose($handle);
				$this->setWarning($e->getMessage());

				return false;
			}
		}

		return true;
	}

	public function delete($path)
	{
		// Get settings
		$settings = $this->getSettings();

		if ($settings === false)
		{
			return false;
		}

		// Remove starting slash, or CloudMe will read it as an absolute path
		$path = '/' . ltrim($path, '/');

		$done = false;
		try
		{
			$this->webdav->request('DELETE', $path);
			$done = true;
		}
		catch (\Exception $e)
		{
			//Do nothing
		}

		if (!$done)
		{
			try
			{
				$this->webdav->request('DELETE', $path);
			}
			catch (\Exception $e)
			{
				$this->setWarning($e->getMessage());

				return false;
			}
		}

		return true;
	}

	protected function getSettings()
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		$username = trim($config->get('engine.postproc.' . $this->settingsKey . '.username', ''));
		$password = trim($config->get('engine.postproc.' . $this->settingsKey . '.password', ''));
		$url = trim($config->get('engine.postproc.' . $this->settingsKey . '.url', ''));

		$this->directory = $config->get('volatile.postproc.directory', null);

		if (empty($this->directory))
		{
			$this->directory = $config->get('engine.postproc.' . $this->settingsKey . '.directory', '');
		}

		// Sanity checks
		if (empty($username) || empty($password))
		{
			$this->setError('You have not linked Akeeba Backup with your WebDav account');

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

		$settings = array(
			'baseUri'  => $url,
			'userName' => $username,
			'password' => $password,
		);

		// Last chance to modify the settings!
		$this->modifySettings($settings);

		$this->webdav = new ConnectorDavclient($settings);
		$this->webdav->addTrustedCertificates(AKEEBA_CACERT_PEM);

		return true;
	}

	/**
	 * Last chance to modify the settings before we create a WebDAV client.
	 *
	 * @param   array  $settings
	 *
	 * @return  void
	 */
	protected function modifySettings(array &$settings)
	{
		// No changes made in the default class. Only subclasses implement this.
	}

	protected function putFile($absolute_filename, $directory, $basename)
	{
		// Normalize double slashes
		$directory = str_replace('//', '/', $directory);
		$basename  = str_replace('//', '/', $basename);

		// Store the absolute remote path in the class property
		// Let's remove the starting slash, otherwise it read as an absolute path
		$this->remote_path = trim(trim($directory, '/') . '/' . trim($basename, '/'), '/');
		$this->remote_path = str_replace('//', '/', $this->remote_path);

		// A directory is supplied, let's check if it really exists or not
		if ($directory)
		{
			$checkPath = '';
			$parts = explode('/', $directory);

			foreach ($parts as $part)
			{
				if (!$part)
				{
					continue;
				}

				$checkPath .= $part . '/';

				try
				{
					Factory::getLog()->log(LogLevel::DEBUG, "Checking if the following remote path exists: " . $checkPath);
					$this->webdav->propFind($checkPath, array('{DAV:}resourcetype'));
				}
				catch (\Exception $e)
				{
					Factory::getLog()->log(LogLevel::DEBUG, "Received the following exception while checking if the remote folder exists: " . $e->getCode() . ' - ' . $e->getMessage());

					// If the folder doesn't exists an error 404 is returned
					if ($e->getCode() != 404)
					{
						Factory::getLog()->log(LogLevel::DEBUG, "Error code different than 404, this means that a real error occurred");

						return false;
					}

					Factory::getLog()->log(LogLevel::DEBUG, "Remote path $checkPath does not exists, I'm going to create it");

					// Folder doesn't exist, let's create it
					try
					{
						$this->webdav->request('MKCOL', $checkPath);
					}
					catch (\Exception $e)
					{
						Factory::getLog()->log(LogLevel::DEBUG, "The following error occurred while creating the remote folder $checkPath: " . $e->getCode() . ' - ' . $e->getMessage());

						return false;
					}

					Factory::getLog()->log(LogLevel::DEBUG, "Remote path $checkPath created");
				}
			}
		}

		$this->remote_path = '/' . ltrim($this->remote_path, '/');

		$result = $this->webdav->request('PUT', $this->remote_path, file_get_contents($absolute_filename));

		return $result;
	}
}
