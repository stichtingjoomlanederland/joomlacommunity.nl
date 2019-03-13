<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Check if the user add one or more additional database, but the connection details are wrong
 * In such cases Akeeba Backup will receive an error, halting the whole backup process
 */
class AliceCoreDomainChecksRuntimeerrorsDbaddwrong extends AliceCoreDomainChecksAbstract
{
	public function __construct($logFile = null)
	{
		parent::__construct(90, 'COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_DBADD_WRONG', $logFile);
	}

	public function check()
	{
		$handle  = @fopen($this->logFile, 'r');
		$profile = 0;
		$error   = false;

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
				preg_match('/profile\s+#(\d+)/', $line, $matches);

				if (isset($matches[1]))
				{
					$profile = $matches[1];
				}

				break;
			}
		}

		fclose($handle);

		// Mhm... no profile ID? Something weird happened better stop here and mark the test as skipped
		if ( !$profile)
		{
			$this->setResult(0);
			$this->setErrLangKey('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_DBADD_NO_PROFILE');

			throw new Exception(JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_DBADD_NO_PROFILE'));
		}

		// Do I have to switch profile?
		$container   = \FOF30\Container\Container::getInstance('com_akeeba');
		$cur_profile = $container->platform->getSessionVar('profile', null, 'akeeba');

		if ($cur_profile != $profile)
		{
			$container->platform->setSessionVar('profile', $profile, 'akeeba');
		}

		$filters = \Akeeba\Engine\Factory::getFilters();
		$multidb = $filters->getFilterData('multidb');

		foreach ($multidb as $addDb)
		{
			$options = array(
				'driver'   => $addDb['driver'],
				'host'     => $addDb['host'],
				'port'     => $addDb['port'],
				'user'     => $addDb['username'],
				'password' => $addDb['password'],
				'database' => $addDb['database'],
				'prefix'   => $addDb['prefix'],
			);

			try
			{
				$db = JDatabaseDriver::getInstance($options);
				$db->connect();
				$db->disconnect();
			}
			catch (Exception $e)
			{
				$error = true;
			}
		}

		// If needed set the old profile again
		if ($cur_profile != $profile)
		{
			$container->platform->setSessionVar('profile', $cur_profile, 'akeeba');
		}

		if ($error)
		{
			$this->setResult(-1);
			$this->setErrLangKey('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_DBADD_WRONG_ERROR');

			throw new Exception(JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_DBADD_WRONG_ERROR'));
		}

		return true;
	}

	public function getSolution()
	{
		// Test skipped? No need to provide a solution
		if ($this->getResult() === 0)
		{
			return '';
		}

		return JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_DBADD_WRONG_SOLUTION');
	}
}
