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
use Akeeba\Engine\Postproc\Connector\Sugarsync as ConnectorSugarsync;
use Akeeba\Engine\Postproc\Connector\Sugarsync\Exception\Base as SugarsyncException;

/**
 * SugarSync post-processing class for Akeeba Backup
 */
class Sugarsync extends Base
{
	public function __construct()
	{
		$this->can_delete = true;
		$this->can_download_to_file = true;
		$this->can_download_to_browser = false;
	}

	public function processPart($absolute_filename, $upload_as = null)
	{
		$settings = $this->_getEngineSettings();
		if ($settings === false)
		{
			return false;
		}
		extract($settings);

		// Calculate relative remote filename
		$filename = empty($upload_as) ? basename($absolute_filename) : $upload_as;

		if (empty($directory) || ($directory == '/'))
		{
			$directory = '';
		}

		// Store the absolute remote path in the class property
		$this->remote_path = $directory . '/' . $filename;

		// Connect and send
		try
		{
			$config = array(
				'access'   => base64_decode('TnpZek1UUTFNVEk1TWpnMk1UWTVORGt3Tnc='),
				'private'  => base64_decode('T0RnNE4yVTJaakZtTURKa05HSTFaRGxtTkdVNU1qZzFZVE5oWW1VMVltVQ=='),
				'email'    => $email,
				'password' => $password
			);
			$ss = new ConnectorSugarsync($config);
			$ss->uploadFile($directory, $filename, $absolute_filename);
		}
		catch (SugarsyncException $e)
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
		extract($settings);

		// Connect and delete
		try
		{
			$config = array(
				'access'   => base64_decode('TnpZek1UUTFNVEk1TWpnMk1UWTVORGt3Tnc='),
				'private'  => base64_decode('T0RnNE4yVTJaakZtTURKa05HSTFaRGxtTkdVNU1qZzFZVE5oWW1VMVltVQ=='),
				'email'    => $email,
				'password' => $password
			);
			$ss = new ConnectorSugarsync($config);
			$ss->deleteFile($path);
		}
		catch (SugarsyncException $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		return true;
	}

	public function downloadToFile($remotePath, $localFile, $fromOffset = null, $length = null)
	{
		if (!is_null($fromOffset) || !is_null($length))
		{
			return -1;
		}

		$settings = $this->_getEngineSettings();
		if ($settings === false)
		{
			return false;
		}
		extract($settings);

		try
		{
			$config = array(
				'access'   => base64_decode('TnpZek1UUTFNVEk1TWpnMk1UWTVORGt3Tnc='),
				'private'  => base64_decode('T0RnNE4yVTJaakZtTURKa05HSTFaRGxtTkdVNU1qZzFZVE5oWW1VMVltVQ=='),
				'email'    => $email,
				'password' => $password
			);
			$ss = new ConnectorSugarsync($config);
			$dummy = null;
			$ss->downloadFile($remotePath, $dummy, $localFile);
		}
		catch (SugarsyncException $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		return true;
	}

	protected function  _getEngineSettings()
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		$email = trim($config->get('engine.postproc.sugarsync.email', ''));
		$password = trim($config->get('engine.postproc.sugarsync.password', ''));
		$directory = $config->get('volatile.postproc.directory', null);

		if (empty($directory))
		{
			$directory = $config->get('engine.postproc.sugarsync.directory', 0);
		}

		// Sanity checks
		if (empty($email))
		{
			$this->setWarning('You have not set up your SugarSync email address');

			return false;
		}

		if (empty($password))
		{
			$this->setWarning('You have not set up your SugarSync password');

			return false;
		}

        if(!function_exists('curl_init'))
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
			'email'     => $email,
			'password'  => $password,
			'directory' => $directory,
		);
	}
}
