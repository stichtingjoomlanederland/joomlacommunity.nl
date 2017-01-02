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

// Enable Akeeba Engine
define('AKEEBAENGINE', 1);

use Akeeba\Engine\Platform;
use Akeeba\Engine\Factory;

/**
 * Admin Tools File Alteration Monitor (PHP File Change Scanner) CLI application
 */
class AdminToolsFAM extends AdmintoolsCliBase
{
	/**
	 * The main entry point of the application
	 */
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

		$verboseMode = $this->input->get('quiet', -1, 'int') == -1;

		if ($verboseMode)
		{
			$year = gmdate('Y');
			echo <<<ENDBLOCK
Admin Tools PHP File Scanner CLI $version ($date)
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

		$start_scan = time();

		// Attempt to use an infinite time limit, in case you are using the PHP CGI binary instead
		// of the PHP CLI binary. This will not work with Safe Mode, though.
		$safe_mode = true;

		if (function_exists('ini_get'))
		{
			$safe_mode = ini_get('safe_mode');
		}

		if (!$safe_mode && function_exists('set_time_limit'))
		{
			if ($verboseMode)
			{
				echo "Unsetting time limit restrictions.\n";
			}

			@set_time_limit(0);
		}
		elseif (!$safe_mode)
		{
			if ($verboseMode)
			{
				echo "Could not unset time limit restrictions; you may get a timeout error\n";
			}
		}
		else
		{
			if ($verboseMode)
			{
				echo "You are using PHP's Safe Mode; you may get a timeout error\n";
			}
		}

		if ($verboseMode)
		{
			echo "\n";
		}

		// Log some paths
		if ($verboseMode)
		{
			echo "Site paths determined by this script:\n";
			echo "JPATH_BASE : " . JPATH_BASE . "\n";
			echo "JPATH_ADMINISTRATOR : " . JPATH_ADMINISTRATOR . "\n\n";
		}

		// Load the engine
		$factoryPath = JPATH_ADMINISTRATOR . '/components/com_admintools/engine/Factory.php';
		define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_admintools');
		define('AKEEBAROOT', JPATH_ADMINISTRATOR . '/components/com_admintools/engine');
		if (!file_exists($factoryPath))
		{
			echo "ERROR!\n";
			echo "Could not load the backup engine; file does not exist. Technical information:\n";
			echo "Path to " . basename(__FILE__) . ": " . __DIR__ . "\n";
			echo "Path to factory file: $factoryPath\n";
			die("\n");
		}
		else
		{
			try
			{
				require_once $factoryPath;
			}
			catch (Exception $e)
			{
				echo "ERROR!\n";
				echo "Backup engine returned an error. Technical information:\n";
				echo "Error message:\n\n";
				echo $e->getMessage() . "\n\n";
				echo "Path to " . basename(__FILE__) . ":" . __DIR__ . "\n";
				echo "Path to factory file: $factoryPath\n";
				die("\n");
			}
		}

		// Load the platform
		Platform::addPlatform('filescan', JPATH_ADMINISTRATOR . '/components/com_admintools/platform/Filescan');

		// Forced CLI mode settings
		define('AKEEBA_PROFILE', 1);
		define('AKEEBA_BACKUP_ORIGIN', 'cli');

		// Load the engine configuration
		Platform::getInstance()->load_configuration(1);

		// Reset Kettenrad and its storage
		Factory::resetState(array(
			'maxrun' => 0
		));

		Factory::getFactoryStorage()->reset(AKEEBA_BACKUP_ORIGIN);

		// Setup
		$kettenrad = Factory::getKettenrad();

		$configOverrides['volatile.core.finalization.action_handlers'] = array(
			new Akeeba\Engine\Finalization\Email()
		);

		$configOverrides['volatile.core.finalization.action_queue'] = array(
			'remove_temp_files',
			'update_statistics',
			'update_filesizes',
			'apply_quotas',
			'send_scan_email'
		);

		// Apply the configuration overrides, please
		$platformOverrides = Platform::getInstance()->configOverrides;
		Platform::getInstance()->configOverrides = array_merge($platformOverrides, $configOverrides);

		$options = array(
			'description' => '',
			'comment'     => '',
			'jpskey'      => ''
		);

		$kettenrad->setup($options);

		// Dummy array so that the loop iterates once
		$array = array(
			'HasRun' => 0,
			'Error'  => ''
		);

		$warnings_flag = false;

		$this->out("Starting file scanning");
		$this->out("");

		while (($array['HasRun'] != 1) && (empty($array['Error'])))
		{
			// Recycle the database connection to minimise problems with database timeouts
			$db = Factory::getDatabase();
			$db->close();
			$db->open();

			Factory::getLog()->open(AKEEBA_BACKUP_ORIGIN);
			Factory::getLog()->unpause();

			// Apply engine optimization overrides
			$config = Factory::getConfiguration();
			$config->set('akeeba.tuning.min_exec_time', 0);
			$config->set('akeeba.tuning.nobreak.beforelargefile', 1);
			$config->set('akeeba.tuning.nobreak.afterlargefile', 1);
			$config->set('akeeba.tuning.nobreak.proactive', 1);
			$config->set('akeeba.tuning.nobreak.finalization', 1);
			$config->set('akeeba.tuning.settimelimit', 0);
			$config->set('akeeba.tuning.nobreak.domains', 0);


			$kettenrad->tick();

			Factory::getTimer()->resetTime();

			$array		 = $kettenrad->getStatusArray();

			Factory::getLog()->close();

			$time		 = date('Y-m-d H:i:s \G\M\TO (T)');
			$memusage	 = $this->memUsage();

			$warnings		 = "no warnings issued (good)";
			$stepWarnings	 = false;

			if (!empty($array['Warnings']))
			{
				$warnings_flag	 = true;
				$warnings		 = "POTENTIAL PROBLEMS DETECTED; " . count($array['Warnings']) . " warnings issued (see below).\n";
				foreach ($array['Warnings'] as $line)
				{
					$warnings .= "\t$line\n";
				}
				$stepWarnings = true;
				$kettenrad->resetWarnings();
			}

			if (($verboseMode) || $stepWarnings)
			{
				echo <<<ENDSTEPINFO
Last Tick   : $time
Memory used : $memusage
Warnings    : $warnings


ENDSTEPINFO;
			}
		}

		// Clean up
		Factory::getFactoryStorage()->reset(AKEEBA_BACKUP_ORIGIN);

		$exitCode = 0;

		if (!empty($array['Error']))
		{
			echo "An error has occurred:\n{$array['Error']}\n\n";

			$exitCode = 2;
		}
		else
		{
			if ($verboseMode)
			{
				echo "File scanning job finished successfully after approximately " . $this->timeago($start_scan, time(), '', false) . "\n";
			}

			$exitCode = 0;
		}

		if ($warnings_flag)
		{
			$exitCode = 1;

			if ($verboseMode)
			{
				$exitCode = 1;
				echo "\n" . str_repeat('=', 79) . "\n";
				echo "!!!!!  W A R N I N G  !!!!!\n\n";
				echo "Admin Tools issued warnings during the scanning process. You have to review them\n";
				echo "and make sure that your scan has completed successfully.\n";
				echo "\n" . str_repeat('=', 79) . "\n";
			}
		}

		if ($verboseMode)
		{
			echo "Peak memory usage: " . $this->peakMemUsage() . "\n\n";
		}

		$this->close($exitCode);
	}
}

// Load the version file
require_once JPATH_ADMINISTRATOR . '/components/com_admintools/version.php';

AdmintoolsCliBase::getInstance('AdminToolsFAM')->execute();
