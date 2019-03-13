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
use Akeeba\Engine\Util\Transfer\Ftp as TransferFtp;
use Psr\Log\LogLevel;

class Ftp extends Base
{
	public function __construct()
	{
		$this->can_delete              = true;
		$this->can_download_to_browser = true;
		$this->can_download_to_file    = true;
	}

	public function processPart($absolute_filename, $upload_as = null)
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		$host      = $config->get('engine.postproc.ftp.host', '');
		$port      = $config->get('engine.postproc.ftp.port', 21);
		$user      = $config->get('engine.postproc.ftp.user', '');
		$pass      = $config->get('engine.postproc.ftp.pass', 0);
		$directory = $config->get('volatile.postproc.directory', null);

		if (empty($directory))
		{
			$directory = $config->get('engine.postproc.ftp.initial_directory', '');
		}

		$subdir  = trim($config->get('engine.postproc.ftp.subdirectory', ''), '/');
		$ssl     = $config->get('engine.postproc.ftp.ftps', 0) == 0 ? false : true;
		$passive = $config->get('engine.postproc.ftp.passive_mode', 0) == 0 ? false : true;

		// You can't fix stupid, but at least you get to shout at them
		if (strtolower(substr($host, 0, 6)) == 'ftp://')
		{
			$this->setWarning('YOU ARE *** N O T *** SUPPOSED TO ENTER THE ftp:// PROTOCOL PREFIX IN THE FTP HOSTNAME FIELD OF THE Upload to Remote FTP POST-PROCESSING ENGINE. I am trying to fix your bad configuration setting, but the backup might fail anyway. You MUST fix this in your configuration.');

			Factory::getLog()
			       ->log(LogLevel::WARNING, 'YOU ARE *** N O T *** SUPPOSED TO ENTER THE ftp:// PROTOCOL PREFIX IN THE FTP HOSTNAME FIELD OF THE Upload to Remote FTP POST-PROCESSING ENGINE.')
			;
			Factory::getLog()
			       ->log(LogLevel::WARNING, 'I am trying to fix your bad configuration setting, but the backup might fail anyway. You MUST fix this in your configuration.')
			;
			$host = substr($host, 6);
		}

		// Process the initial directory
		$directory = '/' . ltrim(trim($directory), '/');

		// Parse tags
		$directory = Factory::getFilesystemTools()->replace_archive_name_variables($directory);
		$config->set('volatile.postproc.directory', $directory);

		// Connect to the FTP server
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . ':: Connecting to remote FTP');

		$options = array(
			'host'      => $host,
			'port'      => $port,
			'username'  => $user,
			'password'  => $pass,
			'directory' => $directory,
			'ssl'       => $ssl,
			'passive'   => $passive,
		);

		try
		{
			$ftp = new TransferFtp($options);
		}
		catch (\RuntimeException $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		// If supplied, change to the subdirectory
		if ($subdir)
		{
			$subdir = trim(Factory::getFilesystemTools()->replace_archive_name_variables($subdir), '/');

			if (!$ftp->isDir($directory . '/' . $subdir))
			{
				// Got an error? This means that the directory doesn't exist, let's try to create it
				if (!$ftp->mkdir($directory . '/' . $subdir))
				{
					// Ok, I really can't do anything, let's stop here
					$this->setWarning("Could not create the subdirectory $subdir in the remote FTP server");

					return false;
				}
				else
				{
					// Let's move into the new directory
					if (!$ftp->isDir($directory . '/' . $subdir))
					{
						// This should never happen, anyway better be safe than sorry
						$this->setWarning("Could not move into the subdirectory $subdir in the remote FTP server");

						return false;
					}
				}
			}
		}

		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . ":: Starting FTP upload of $absolute_filename");
		$realdir = substr($directory, -1) == '/' ? substr($directory, 0, strlen($directory) - 1) : $directory;

		if ($subdir)
		{
			$realdir .= '/' . $subdir;
		}

		$basename = empty($upload_as) ? basename($absolute_filename) : $upload_as;
		$realname = $realdir . '/' . $basename;

		try
		{
			$res = $ftp->upload($absolute_filename, $realname);
		}
		catch (\Exception $e)
		{
			$res = false;
			$this->setWarning($e->getMessage());
		}

		// Store the absolute remote path in the class property
		$this->remote_path = $realname;

		if (!$res)
		{
			// If the file was unreadable, just skip it...
			if (is_readable($absolute_filename))
			{
				$this->setWarning('Uploading ' . $absolute_filename . ' has failed.');

				return false;
			}
			else
			{
				$this->setWarning('Uploading ' . $absolute_filename . ' has failed because the file is unreadable.');

				return true;
			}
		}
		else
		{
			return true;
		}
	}

	public function delete($path)
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		$host    = $config->get('engine.postproc.ftp.host', '');
		$port    = $config->get('engine.postproc.ftp.port', 21);
		$user    = $config->get('engine.postproc.ftp.user', '');
		$pass    = $config->get('engine.postproc.ftp.pass', 0);
		$ssl     = $config->get('engine.postproc.ftp.ftps', 0) == 0 ? false : true;
		$passive = $config->get('engine.postproc.ftp.passive_mode', 0) == 0 ? false : true;

		$directory = dirname($path);

		// Connect to the FTP server
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::delete() -- Connecting to remote FTP');

		$options = array(
			'host'      => $host,
			'port'      => $port,
			'username'  => $user,
			'password'  => $pass,
			'directory' => $directory,
			'ssl'       => $ssl,
			'passive'   => $passive,
		);

		try
		{
			$ftp = new TransferFtp($options);
		}
		catch (\RuntimeException $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		try
		{
			$res = $ftp->delete($path);
		}
		catch (\Exception $e)
		{
			$res = false;
			$this->setWarning($e->getMessage());
		}

		if (!$res)
		{
			$this->setWarning('Deleting ' . $path . ' has failed.');

			return false;
		}
		else
		{
			return true;
		}
	}

	public function downloadToFile($remotePath, $localFile, $fromOffset = null, $length = null)
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		$host    = $config->get('engine.postproc.ftp.host', '');
		$port    = $config->get('engine.postproc.ftp.port', 21);
		$user    = $config->get('engine.postproc.ftp.user', '');
		$pass    = $config->get('engine.postproc.ftp.pass', 0);
		$ssl     = $config->get('engine.postproc.ftp.ftps', 0) == 0 ? false : true;
		$passive = $config->get('engine.postproc.ftp.passive_mode', 0) == 0 ? false : true;

		$directory = dirname($remotePath);

		// Connect to the FTP server
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::delete() -- Connecting to remote FTP');

		$options = array(
			'host'      => $host,
			'port'      => $port,
			'username'  => $user,
			'password'  => $pass,
			'directory' => $directory,
			'ssl'       => $ssl,
			'passive'   => $passive,
		);

		try
		{
			$ftp = new TransferFtp($options);
		}
		catch (\RuntimeException $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		try
		{
			$result = $ftp->download($remotePath, $localFile);
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		return $result;
	}

	/**
	 * Returns an FTP/FTPS URL for directly downloading the requested file (lame and functional)
	 */
	public function downloadToBrowser($remotePath)
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		$host = $config->get('engine.postproc.ftp.host', '');
		$port = $config->get('engine.postproc.ftp.port', 21);
		$user = $config->get('engine.postproc.ftp.user', '');
		$pass = $config->get('engine.postproc.ftp.pass', 0);
		$ssl  = $config->get('engine.postproc.ftp.ftps', 0) == 0 ? false : true;

		$uri = $ssl ? 'ftps://' : 'ftp://';

        if ($user && $pass)
		{
			$uri .= urlencode($user) . ':' . urlencode($pass) . '@';
		}
		$uri .= $host;

        if ($port && ($port != 21))
		{
			$uri .= ':' . $port;
		}

		if (substr($remotePath, 0, 1) != '/')
		{
			$uri .= '/';
		}
		$uri .= $remotePath;

		return $uri;
	}
}
