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
class AliceCoreDomainChecksRequirementsPhp extends AliceCoreDomainChecksAbstract
{
	public function __construct($logFile = null)
	{
		parent::__construct(10, JText::_('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_PHP_VERSION'), $logFile);
		$this->setCheckLangKey('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_PHP_VERSION');
	}

	public function check()
	{
		$handle = @fopen($this->logFile, 'r');
		$found  = false;

		if ($handle === false)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName . ' Test error, could not open backup log file.');

			return false;
		}

		// PHP information is on a single line, so I can start reading one line at time
		while (($line = fgets($handle)) !== false)
		{
			$pos = strpos($line, '|PHP Version');

			if ($pos !== false)
			{
				$found   = true;
				$version = trim(substr($line, strpos($line, ':', $pos) + 1));

				// PHP too old (well, this should never happen)
				if (version_compare($version, '5.3', 'lt'))
				{
					fclose($handle);
					AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Test failed, detected version: ' . $version);

					$this->setResult(-1);
					$this->setErrLangKey('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_PHP_VERSION_ERR_TOO_NEW');
					throw new Exception(JText::sprintf('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_PHP_VERSION_ERR_TOO_NEW', $version));
				}
				/*
				elseif(version_compare($version, '5.5', 'ge'))
				{
                    fclose($handle);
					AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Test failed, detected version: '.$version);

                    $this->setResult(-1);
					throw new Exception(JText::sprintf('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_PHP_VERSION_ERR_TOO_OLD', $version));
				}
				*/

				break;
			}
		}

		if ($found)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Test passed, detected version: ' . $version);
		}
		else
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName . " Test error, couldn't detect PHP version.");
		}

		fclose($handle);

		return true;
	}

	public function getSolution()
	{
		return JText::_('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_PHP_VERSION_SOLUTION');
	}
}
