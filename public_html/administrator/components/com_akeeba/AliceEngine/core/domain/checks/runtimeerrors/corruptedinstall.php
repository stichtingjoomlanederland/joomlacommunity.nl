<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Checks if the user is using a too old or too new PHP version
 */
class AliceCoreDomainChecksRuntimeerrorsCorruptedinstall extends AliceCoreDomainChecksAbstract
{
	public function __construct($logFile = null)
	{
		parent::__construct(60, 'COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_CORRUPTED_INSTALL', $logFile);
	}

	public function check()
	{
		$handle = @fopen($this->logFile, 'r');
		$error  = false;

		if ($handle === false)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName . ' Test error, could not open backup log file.');

			return false;
		}

		while (($line = fgets($handle)) !== false)
		{
			$pos = strpos($line, '|Loaded profile');

			if ($pos !== false)
			{
				// Ok, I just passed the "Loaded profile" line, let's see if it's a broken install
				$line = fgets($handle);

				$logline = trim(substr($line, 24));

				// Empty line?? Most likely it's a broken install
				if ($logline == '|')
				{
					$error = true;
				}

				break;
			}
		}

		fclose($handle);

		if ($error)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName . " Test error, most likely this installation is broken");

			$this->setResult(-1);
			$this->setErrLangKey('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_CORRUPTED_INSTALL_ERROR');

			throw new Exception(JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_CORRUPTED_INSTALL_ERROR'));
		}

		AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName . " Test passed, installation seems ok.");

		return true;
	}

	public function getSolution()
	{
		return JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_CORRUPTED_INSTALL_SOLUTION');
	}
}
