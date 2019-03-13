<?php
/**
 * Akeeba Engine
 * The PHP-only site backup engine
 *
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Archiver;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Util\Transfer\Ftp;
use Akeeba\Engine\Util\Transfer\FtpCurl;
use Psr\Log\LogLevel;

/**
 * Direct Transfer Over FTP Over cURL archiver class
 *
 * Transfers the files to a remote FTP server instead of putting them in
 * an archive
 *
 */
class Directftpcurl extends Directftp
{
	/** @var Ftp FTP resource handle */
	private $ftpTransfer;

	/** @var string FTP hostname */
	private $host;

	/** @var string FTP port */
	private $port;

	/** @var string FTP username */
	private $user;

	/** @var string FTP password */
	private $pass;

	/** @var bool Should we use FTP over SSL? */
	private $usessl;

	/** @var bool Should we use passive FTP? */
	private $passive;

	/** @var bool Enable the passive mode workaround? */
	private $passiveWorkaround = true;

	/** @var string FTP initial directory */
	private $initdir;

	/** @var bool Could we connect to the server? */
	public $connect_ok = false;

	/**
	 * Initialises the archiver class, seeding the remote installation
	 * from an existent installer's JPA archive.
	 *
	 * @param string $targetArchivePath Absolute path to the generated archive (ignored in this class)
	 * @param array  $options           A named key array of options (optional)
	 *
	 * @return  void
	 */
	public function initialize($targetArchivePath, $options = array())
	{
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: new instance");

		$registry = Factory::getConfiguration();

		$this->host    = $registry->get('engine.archiver.directftpcurl.host', '');
		$this->port    = $registry->get('engine.archiver.directftpcurl.port', '21');
		$this->user    = $registry->get('engine.archiver.directftpcurl.user', '');
		$this->pass    = $registry->get('engine.archiver.directftpcurl.pass', '');
		$this->initdir = $registry->get('engine.archiver.directftpcurl.initial_directory', '');
		$this->usessl  = $registry->get('engine.archiver.directftpcurl.ftps', false);
		$this->passive = $registry->get('engine.archiver.directftpcurl.passive_mode', true);
		$this->passiveWorkaround = $registry->get('engine.archiver.directftpcurl.passive_mode_workaround', true);

		if (isset($options['host']))
		{
			$this->host = $options['host'];
		}

		if (isset($options['port']))
		{
			$this->port = $options['port'];
		}

		if (isset($options['user']))
		{
			$this->user = $options['user'];
		}

		if (isset($options['pass']))
		{
			$this->pass = $options['pass'];
		}

		if (isset($options['initdir']))
		{
			$this->initdir = $options['initdir'];
		}

		if (isset($options['usessl']))
		{
			$this->usessl = $options['usessl'];
		}

		if (isset($options['passive']))
		{
			$this->passive = $options['passive'];
		}

		if (isset($options['passive_fix']))
		{
			$this->passiveWorkaround = $options['passive_fix'] ? true : false;
		}

		// You can't fix stupid, but at least you get to shout at them
		if (strtolower(substr($this->host, 0, 6)) == 'ftp://')
		{
			Factory::getLog()->log(LogLevel::WARNING, 'YOU ARE *** N O T *** SUPPOSED TO ENTER THE ftp:// PROTOCOL PREFIX IN THE FTP HOSTNAME FIELD OF THE DirectFTP ARCHIVER ENGINE.');
			Factory::getLog()->log(LogLevel::WARNING, 'I am trying to fix your bad configuration setting, but the backup might fail anyway. You MUST fix this in your configuration.');
			$this->host = substr($this->host, 6);
		}

		$this->connect_ok = $this->connectFTP();

		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: FTP connection status: " . ($this->connect_ok ? 'success' : 'FAIL'));
	}

	/**
	 * Tries to connect to the remote FTP server and change into the initial directory
	 *
	 * @return bool True is connection successful, false otherwise
	 */
	protected function connectFTP()
	{
		Factory::getLog()->log(LogLevel::DEBUG, 'Connecting to remote FTP');

        $options = array(
			'host'        => $this->host,
			'port'        => $this->port,
			'username'    => $this->user,
			'password'    => $this->pass,
			'directory'   => $this->initdir,
			'ssl'         => $this->usessl,
			'passive'     => $this->passive,
			'passive_fix' => $this->passiveWorkaround,
		);

        try
        {
            $this->ftpTransfer = new FtpCurl($options);
        }
        catch(\RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

		$this->resetErrors();

		return true;
	}
}
