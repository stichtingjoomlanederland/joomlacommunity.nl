<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Define ourselves as a parent file
define('_JEXEC', 1);

// Setup and import the base CLI script
$minphp = '5.4.0';
$curdir = __DIR__;

require_once __DIR__ . '/../administrator/components/com_admintools/assets/cli/base.php';

class AdmintoolsDbrepair extends AdmintoolsCliBase
{
	public function flushAssets()
	{
		// This is an empty function since JInstall will try to flush the assets even if we're in CLI (!!!)
		return true;
	}

	public function execute()
	{
		// Load the language files
		$paths	 = array(JPATH_ADMINISTRATOR, JPATH_ROOT);
		$jlang	 = JFactory::getLanguage();
		$jlang->load('com_admintools', $paths[0], 'en-GB', true);
		$jlang->load('com_admintools', $paths[1], 'en-GB', true);
		$jlang->load('com_admintools' . '.override', $paths[0], 'en-GB', true);
		$jlang->load('com_admintools' . '.override', $paths[1], 'en-GB', true);

		$debugmessage = '';

		if ($this->input->get('debug', -1, 'int') != -1)
		{
			if (!defined('AKEEBADEBUG'))
			{
				define('AKEEBADEBUG', 1);
			}

			$debugmessage = "*** DEBUG MODE ENABLED ***\n";
			ini_set('display_errors', 1);
		}

		$version		 = ADMINTOOLS_VERSION;
		$date			 = ADMINTOOLS_DATE;

		$phpversion		 = PHP_VERSION;
		$phpenvironment	 = PHP_SAPI;

		if ($this->input->get('quiet', -1, 'int') == -1)
		{
			$year = gmdate('Y');
			echo <<<ENDBLOCK
Admin Tools DB Repair CLI $version ($date)
Copyright (c) 2010-$year Akeeba Ltd / Nicholas K. Dionysopoulos
-------------------------------------------------------------------------------
Admin Tools is Free Software, distributed under the terms of the GNU General
Public License version 3 or, at your option, any later version.
This program comes with ABSOLUTELY NO WARRANTY as per sections 15 & 16 of the
license. See http://www.gnu.org/licenses/gpl-3.0.html for details.
-------------------------------------------------------------------------------
You are using PHP $phpversion ($phpenvironment)
$debugmessage


ENDBLOCK;
		}

		// Attempt to use an infinite time limit, in case you are using the PHP CGI binary instead
		// of the PHP CLI binary. This will not work with Safe Mode, though.
		$safe_mode = true;

		if (function_exists('ini_get'))
		{
			$safe_mode = ini_get('safe_mode');
		}

		if (!$safe_mode && function_exists('set_time_limit'))
		{
			if ($this->input->get('quiet', -1, 'int') == -1)
			{
				echo "Unsetting time limit restrictions.\n";
			}

			@set_time_limit(0);
		}
		elseif (!$safe_mode)
		{
			if ($this->input->get('quiet', -1, 'int') == -1)
			{
				echo "Could not unset time limit restrictions; you may get a timeout error\n";
			}
		}
		else
		{
			if ($this->input->get('quiet', -1, 'int') == -1)
			{
				echo "You are using PHP's Safe Mode; you may get a timeout error\n";
			}
		}

		if ($this->input->get('quiet', -1, 'int') == -1)
		{
			echo "\n";
		}

		$container = \FOF30\Container\Container::getInstance('com_admintools', [], 'admin');
		/** @var \Akeeba\AdminTools\Admin\Model\DatabaseTools $model */
		$model = $container->factory->model('DatabaseTools')->tmpInstance();
		$table = '';

		do
		{
			$table = $model->repairAndOptimise($table, true);
		}
		while ($table);

		$dbType = $container->db->name;
		$isMySQL = strpos($dbType, 'mysql') !== false;
		
		if (!$isMySQL)
		{
			$this->out("You are not using a MySQL database, there's nothing to do here.");
			$this->close();
		}

		$this->out("Table optimization is now complete.");
	}
}

// Load the version file
require_once JPATH_ADMINISTRATOR . '/components/com_admintools/version.php';

// Instanciate and run the application
AdmintoolsCliBase::getInstance('AdmintoolsDbrepair')->execute();