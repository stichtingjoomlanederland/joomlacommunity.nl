<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Checks for supported DB type and version
 */
class AliceCoreDomainChecksRequirementsDb extends AliceCoreDomainChecksAbstract
{
	public function __construct($logFile = null)
	{
		parent::__construct(20, 'COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE', $logFile);
	}

	public function check()
	{
		// Instead of reading the log, I can simply take the JDatabase object and test it
		$db        = JFactory::getDbo();

		$connector = strtolower($db->name);
		$version   = $db->getVersion();

		AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Detected database connector: ' . $connector);
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Detected database version: ' . $version);

		if (($connector == 'mysql') || ($connector == 'mysqli') || ($connector == 'pdomysql'))
		{
			if (version_compare($version, '5.0.47', 'lt'))
			{
				$this->setResult(-1);
				$this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_VERSION_TOO_OLD', $version));
				throw new Exception(JText::sprintf('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_VERSION_TOO_OLD', $version));
			}
		}
		elseif ($connector == 'oracle')
		{
			$this->setResult(-1);
			$this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_UNSUPPORTED', 'Oracle'));
			throw new Exception(JText::sprintf('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_UNSUPPORTED', 'Oracle'));
		}
		elseif ($connector == 'pdo')
		{
			$this->setResult(-1);
			$this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_UNSUPPORTED', 'PDO'));
			throw new Exception(JText::sprintf('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_UNSUPPORTED', 'PDO'));
		}
		elseif ($connector == 'postgresql')
		{
			if (version_compare($version, '8.3.18', 'lt'))
			{
				$this->setResult(-1);
				$this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_VERSION_TOO_OLD', $version));
				throw new Exception(JText::sprintf('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_VERSION_TOO_OLD', $version));
			}
		}
		elseif ($connector == 'sqlsrv' || $connector == 'sqlzure')
		{
			if (version_compare($version, '10.50.1600.1', 'lt'))
			{
				$this->setResult(-1);
				$this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_VERSION_TOO_OLD', $version));
				throw new Exception(JText::sprintf('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_VERSION_TOO_OLD', $version));
			}
		}
		elseif ($connector == 'sqlite')
		{
			$this->setResult(-1);
			$this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_UNSUPPORTED', 'SQLite'));
			throw new Exception(JText::sprintf('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_UNSUPPORTED', 'SQLite'));
		}
		else
		{
			// Unknown database type, throw exception
			$this->setResult(-1);
			$this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_UNKNOWN', $connector));
			throw new Exception(JText::sprintf('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_UNKNOWN', $connector));
		}

		return true;
	}

	public function getSolution()
	{
		return JText::_('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_DATABASE_SOLUTION');
	}
}
