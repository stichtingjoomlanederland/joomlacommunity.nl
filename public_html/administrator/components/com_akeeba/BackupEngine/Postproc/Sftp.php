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
use Akeeba\Engine\Util\Transfer\Sftp as SftpTransfer;
use Psr\Log\LogLevel;

class Sftp extends Base
{
	public function __construct()
	{
		$this->can_delete              = true;
		$this->can_download_to_browser = false;
		$this->can_download_to_file    = true;
	}

	public function processPart($absolute_filename, $upload_as = null)
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		$host      = $config->get('engine.postproc.sftp.host', '');
		$port      = $config->get('engine.postproc.sftp.port', 22);
		$user      = $config->get('engine.postproc.sftp.user', '');
		$pass      = $config->get('engine.postproc.sftp.pass', 0);
		$privKey   = $config->get('engine.postproc.sftp.privkey', '');
		$pubKey    = $config->get('engine.postproc.sftp.pubkey', '');
		$directory = $config->get('volatile.postproc.directory', null);

		if (empty($directory))
		{
			$directory = $config->get('engine.postproc.sftp.initial_directory', '');
		}

		// You can't fix stupid, but at least you get to shout at them
		if (strtolower(substr($host, 0, 7)) == 'sftp://')
		{
			$this->setWarning('YOU ARE *** N O T *** SUPPOSED TO ENTER THE sftp:// PROTOCOL PREFIX IN THE FTP HOSTNAME FIELD OF THE Upload to Remote SFTP POST-PROCESSING ENGINE. I am trying to fix your bad configuration setting, but the backup might fail anyway. You MUST fix this in your configuration.');

			Factory::getLog()
			       ->log(LogLevel::WARNING, 'YOU ARE *** N O T *** SUPPOSED TO ENTER THE sftp:// PROTOCOL PREFIX IN THE FTP HOSTNAME FIELD OF THE Upload to Remote SFTP POST-PROCESSING ENGINE.')
			;
			Factory::getLog()
			       ->log(LogLevel::WARNING, 'I am trying to fix your bad configuration setting, but the backup might fail anyway. You MUST fix this in your configuration.')
			;
			$host = substr($host, 7);
		}


		// Process the initial directory
		$directory = '/' . ltrim(trim($directory), '/');

		// Parse tags
		$directory = Factory::getFilesystemTools()->replace_archive_name_variables($directory);
		$config->set('volatile.postproc.directory', $directory);

		// Connect to the SFTP server
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . ':: Connecting to remote SFTP');

		$options = array(
			'host'       => $host,
			'port'       => $port,
			'username'   => $user,
			'password'   => $pass,
			'directory'  => $directory,
			'privateKey' => $privKey,
			'publicKey'  => $pubKey,
		);

		try
		{
			$sftphandle = new SftpTransfer($options);
		}
		catch (\RuntimeException $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		if (!$this->sftp_chdir($directory, $sftphandle))
		{
			$this->setWarning("Invalid initial directory $directory for the remote SFTP server");

			return false;
		}

		$realdir  = substr($directory, -1) == '/' ? substr($directory, 0, strlen($directory) - 1) : $directory;
		$basename = empty($upload_as) ? basename($absolute_filename) : $upload_as;
		$realname = $realdir . '/' . $basename;

		// Store the absolute remote path in the class property
		$this->remote_path = $realname;

		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . ":: Starting SFTP upload of $absolute_filename");

		try
		{
			$sftphandle->upload($absolute_filename, $realname);
		}
		catch (\RuntimeException $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		return true;
	}

	public function delete($path)
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		$host    = $config->get('engine.postproc.sftp.host', '');
		$port    = $config->get('engine.postproc.sftp.port', 21);
		$user    = $config->get('engine.postproc.sftp.user', '');
		$pass    = $config->get('engine.postproc.sftp.pass', 0);
		$privKey = $config->get('engine.postproc.sftp.privkey', '');
		$pubKey  = $config->get('engine.postproc.sftp.pubkey', '');

		$directory = dirname($path);

		// Connect to the FTP server
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::delete() -- Connecting to remote SFTP');

		$options = array(
			'host'       => $host,
			'port'       => $port,
			'username'   => $user,
			'password'   => $pass,
			'directory'  => $directory,
			'privateKey' => $privKey,
			'publicKey'  => $pubKey,
		);

		try
		{
			$sftphandle = new SftpTransfer($options);
		}
		catch (\RuntimeException $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		// Change to initial directory
		if (!$this->sftp_chdir($directory, $sftphandle))
		{
			$this->setWarning("Invalid initial directory $directory for the remote SFTP server");

			return false;
		}

		$res = $sftphandle->delete($path);

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

		$host    = $config->get('engine.postproc.sftp.host', '');
		$port    = $config->get('engine.postproc.sftp.port', 21);
		$user    = $config->get('engine.postproc.sftp.user', '');
		$pass    = $config->get('engine.postproc.sftp.pass', 0);
		$privKey = $config->get('engine.postproc.sftp.privkey', '');
		$pubKey  = $config->get('engine.postproc.sftp.pubkey', '');

		$directory = dirname($remotePath);

		// Connect to the FTP server
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::delete() -- Connecting to remote SFTP');

		$options = array(
			'host'       => $host,
			'port'       => $port,
			'username'   => $user,
			'password'   => $pass,
			'directory'  => $directory,
			'privateKey' => $privKey,
			'publicKey'  => $pubKey,
		);

		try
		{
			$sftphandle = new SftpTransfer($options);
		}
		catch (\RuntimeException $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		// Change to initial directory
		if (!$this->sftp_chdir($directory, $sftphandle))
		{
			$this->setWarning("Invalid initial directory $directory for the remote SFTP server");

			return false;
		}

		try
		{
			$sftphandle->download($remotePath, $localFile);
		}
		catch (\RuntimeException $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Changes to the requested directory in the remote server. You give only the
	 * path relative to the initial directory and it does all the rest by itself,
	 * including doing nothing if the remote directory is the one we want. If the
	 * directory doesn't exist, it creates it.
	 *
	 * @param   string       $dir
	 * @param   SftpTransfer $sftphandle
	 *
	 * @return  boolean
	 */
	protected function sftp_chdir($dir, &$sftphandle)
	{
		// Calculate "real" (absolute) SFTP path
		$result = $sftphandle->isDir($dir);

		if ($result === false)
		{
			// The directory doesn't exist, let's try to create it...
			if (!$this->makeDirectory($dir, $sftphandle))
			{
				return false;
			}
		}

		// Update the private "current remote directory" variable
		return true;
	}

	/**
	 * Creates a nested directory structure on the remote SFTP server
	 *
	 * @param   string       $dir
	 * @param   SftpTransfer $sftphandle
	 *
	 * @return  boolean
	 */
	protected function makeDirectory($dir, &$sftphandle)
	{
		$alldirs     = explode('/', $dir);
		$previousDir = '';

		foreach ($alldirs as $curdir)
		{
			// Avoid empty dir
			if (!$curdir)
			{
				continue;
			}

			$check = $previousDir . '/' . $curdir;

			if (!$sftphandle->isDir($check))
			{
				if ($sftphandle->mkdir($check) === false)
				{
					$this->setWarning('Could not create SFTP directory ' . $check);

					return false;
				}
			}

			$previousDir = $check;
		}

		return true;
	}
}
