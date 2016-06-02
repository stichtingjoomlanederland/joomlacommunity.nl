<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 * @since     3.2.5
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class AkeebaModelConfigs extends F0FModel
{
	/**
	 * Save the engine configuration
	 */
	public function saveEngineConfig()
	{
		$data = $this->getState('engineconfig', array());

		// Forbid stupidly selecting the site's root as the output or temporary directory
		if (array_key_exists('akeeba.basic.output_directory', $data))
		{
			$folder = $data['akeeba.basic.output_directory'];
			$folder = Factory::getFilesystemTools()->translateStockDirs($folder, true, true);

			$check = Factory::getFilesystemTools()->translateStockDirs('[SITEROOT]', true, true);

			if ($check == $folder)
			{
				JError::raiseWarning(503, JText::_('COM_AKEEBA_CONFIG_OUTDIR_ROOT'));
				$data['akeeba.basic.output_directory'] = '[DEFAULT_OUTPUT]';
			}
		}

		// Unprotect the configuration and merge it
		$config = Factory::getConfiguration();
		$protectedKeys = $config->getProtectedKeys();
		$config->resetProtectedKeys();
		$config->mergeArray($data, false, false);
		$config->setProtectedKeys($protectedKeys);

		// Save configuration
		Platform::getInstance()->save_configuration();
	}

	/**
	 * Test the FTP connection.
	 *
	 * @return bool|string  True on success, error message on failure
	 */
	public function testFTP()
	{
		$config = array(
			'host'    => $this->getState('host'),
			'port'    => $this->getState('port'),
			'user'    => $this->getState('user'),
			'pass'    => $this->getState('pass'),
			'initdir' => $this->getState('initdir'),
			'usessl'  => $this->getState('usessl'),
			'passive' => $this->getState('passive'),
		);

		// Check for bad settings
		if (substr($config['host'], 0, 6) == 'ftp://')
		{
			return JText::_('COM_AKEEBA_CONFIG_FTPTEST_BADPREFIX');
		}

		// Perform the FTP connection test
		$test = new \Akeeba\Engine\Archiver\Directftp();
		$test->initialize('', $config);
		$errors = $test->getError();

		if (empty($errors) || $test->connect_ok)
		{
			$result = true;
		}
		else
		{
			$result = $errors;
		}

		return $result;
	}

	/**
	 * Test the SFTP connection.
	 *
	 * @return array|bool  True on success, error message on failure.
	 */
	public function testSFTP()
	{
		$config = array(
			'host'    => $this->getState('host'),
			'port'    => $this->getState('port'),
			'user'    => $this->getState('user'),
			'pass'    => $this->getState('pass'),
			'privkey' => $this->getState('privkey'),
			'pubkey'  => $this->getState('pubkey'),
			'initdir' => $this->getState('initdir'),
		);

		// Check for bad settings
		if (substr($config['host'], 0, 7) == 'sftp://')
		{
			return JText::_('COM_AKEEBA_CONFIG_SFTPTEST_BADPREFIX');
		}

		// Perform the FTP connection test
		$test = new \Akeeba\Engine\Archiver\Directsftp();
		$test->initialize('', $config);
		$errors = $test->getWarnings();

		if (empty($errors) || $test->connect_ok)
		{
			$result = true;
		}
		else
		{
			$result = $errors;
		}

		return $result;
	}

	/**
	 * Opens an OAuth window for the selected post-processing engine
	 *
	 * @return boolean
	 */
	public function dpeOuthOpen()
	{
		$engine = $this->getState('engine');
		$params = $this->getState('params', array());

		// Get a callback URI for OAuth 2
		$params['callbackURI'] = JUri::base() . '/index.php?option=com_akeeba&view=config&task=dpecustomapiraw&engine=' . $engine;

		// Get the Input object
		$params['input'] = $this->input->getData();

		// Get the engine
		$engineObject = Factory::getPostprocEngine($engine);

		if ($engineObject === false)
		{
			return false;
		}

		$engineObject->oauthOpen($params);
	}

	/**
	 * Runs a custom API call for the selected post-processing engine
	 *
	 * @return boolean
	 */
	public function dpeCustomAPICall()
	{
		$engine = $this->getState('engine');
		$method = $this->getState('method');
		$params = $this->getState('params', array());

		// Get the Input object
		$params['input'] = $this->input->getData();

		$engineObject = Factory::getPostprocEngine($engine);

		if ($engineObject === false)
		{
			return false;
		}

		return $engineObject->customApiCall($method, $params);
	}
}
