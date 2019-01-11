<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Check if the user added the site database as additional database. Some servers won't allow more than one connection
 * to the same database, causing the backup process to fail
 */
class AliceCoreDomainChecksRuntimeerrorsDbaddjsame extends AliceCoreDomainChecksAbstract
{
	public function __construct($logFile = null)
	{
		parent::__construct(100, 'COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_DBADD_JSAME', $logFile);
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

		$config  = $container->platform->getConfig();
		$filters = \Akeeba\Engine\Factory::getFilters();
		$multidb = $filters->getFilterData('multidb');

		$jdb = array(
			'driver'   => $config->get('dbtype'),
			'host'     => $config->get('host'),
			'username' => $config->get('user'),
			'password' => $config->get('password'),
			'database' => $config->get('db'),
		);

		foreach ($multidb as $addDb)
		{
			$options = array(
				'driver'   => $addDb['driver'],
				'host'     => $addDb['host'],
				'username' => $addDb['username'],
				'password' => $addDb['password'],
				'database' => $addDb['database'],
			);

			// It's the same database used by Joomla, this could led to errors
			if ($jdb == $options)
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
			$this->setErrLangKey('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_DBADD_JSAME_ERROR');

			throw new Exception(JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_DBADD_JSAME_ERROR'));
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

		return JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_DBADD_JSAME_SOLUTION');
	}
}
