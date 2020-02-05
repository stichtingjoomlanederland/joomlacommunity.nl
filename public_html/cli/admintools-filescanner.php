<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Define ourselves as a parent file
use Akeeba\AdminTools\Site\Model\Scans;
use FOF30\Container\Container;
use Joomla\CMS\Factory;

// Boilerplate -- START
define('_JEXEC', 1);

foreach ([__DIR__, getcwd()] as $curdir)
{
	if (file_exists($curdir . '/defines.php'))
	{
		define('JPATH_BASE', realpath($curdir . '/..'));
		require_once $curdir . '/defines.php';

		break;
	}

	if (file_exists($curdir . '/../includes/defines.php'))
	{
		define('JPATH_BASE', realpath($curdir . '/..'));
		require_once $curdir . '/../includes/defines.php';

		break;
	}
}

defined('JPATH_LIBRARIES') || die ('This script must be placed in or run from the cli folder of your site.');

require_once JPATH_LIBRARIES . '/fof30/Cli/Application.php';
// Boilerplate -- END

// Enable Akeeba Engine
define('AKEEBAENGINE', 1);

// Load the version file
require_once JPATH_ADMINISTRATOR . '/components/com_admintools/version.php';

/**
 * Admin Tools File Alteration Monitor (PHP File Change Scanner) CLI application
 */
class AdminToolsFAM extends FOFApplicationCLI
{
	/**
	 * The main entry point of the application
	 */
	public function doExecute()
	{
		// Load the language files
		$paths = [JPATH_ADMINISTRATOR, JPATH_ROOT];
		$jlang = Factory::getLanguage();
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

		$version = ADMINTOOLS_VERSION;
		$date    = ADMINTOOLS_DATE;

		$phpversion     = PHP_VERSION;
		$phpenvironment = PHP_SAPI;

		$verboseMode = $this->input->get('quiet', -1, 'int') == -1;

		if ($verboseMode)
		{
			$year   = gmdate('Y');
			$header = <<<ENDBLOCK
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
			$this->out($header);
		}

		$start_scan = time();

		if (function_exists('set_time_limit'))
		{
			if ($verboseMode)
			{
				$this->out("Unsetting time limit restrictions.");
			}

			@set_time_limit(0);
		}
		else
		{
			if ($verboseMode)
			{
				$this->out("Could not unset time limit restrictions; you may get a timeout error");
			}
		}

		if ($verboseMode)
		{
			$this->out('');
		}

		// Log some paths
		if ($verboseMode)
		{
			$this->out('Site paths determined by this script:');
			$this->out(sprintf("JPATH_BASE : %s", JPATH_BASE));
			$this->out(sprintf("JPATH_ADMINISTRATOR : %s", JPATH_ADMINISTRATOR));
			$this->out('');
		}

		$container = Container::getInstance('com_admintools');
		/** @var Scans $model */
		$model = $container->factory->model('Scans')->tmpInstance();

		$model->removeIncompleteScans();

		$this->out("Starting file scanning");
		$this->out("");

		$warnings_flag = false;
		$ret           = $model->startScan('cli');

		while ($ret['status'] && !$ret['done'] && empty($ret['error']))
		{
			$time         = date('Y-m-d H:i:s \G\M\TO (T)');
			$memusage     = $this->memUsage();
			$warnings     = "no warnings issued (good)";
			$stepWarnings = false;

			if (!empty($ret['warnings']))
			{
				$warnings_flag = true;
				$stepWarnings  = true;

				$warnings = sprintf("POTENTIAL PROBLEMS DETECTED; %s warnings issued (see below).\n", count($ret['warnings']));

				foreach ($ret['Warnings'] as $line)
				{
					$warnings .= "\t$line\n";
				}
			}


			if (($verboseMode) || $stepWarnings)
			{
				$stepInfo = <<<ENDSTEPINFO
Last Tick   : $time
Memory used : $memusage
Warnings    : $warnings

ENDSTEPINFO;
				$this->out($stepInfo);
			}

			$ret = $model->stepScan();
		}

		if (!empty($ret['error']))
		{
			$this->out('An error has occurred:');
			$this->out($ret['error']);
			$this->out();

			$exitCode = 2;
		}
		else
		{
			if ($verboseMode)
			{
				$this->out(sprintf("File scanning job finished successfully after approximately %s", $this->timeago($start_scan, time(), '', false)));
			}

			$exitCode = 0;
		}

		if ($warnings_flag)
		{
			$exitCode = 1;

			if ($verboseMode)
			{
				$exitCode = 1;
				$this->out('');
				$this->out(str_repeat('=', 79));
				$this->out('');
				$this->out('!!!!!  W A R N I N G  !!!!!');
				$this->out('');
				$this->out('Admin Tools issued warnings during the scanning process. You have to review them');
				$this->out('and make sure that your scan has completed successfully.');
				$this->out('');
				$this->out(str_repeat('=', 79));
				$this->out('');
			}
		}

		if ($verboseMode)
		{
			$this->out(sprintf("Peak memory usage: %s", $this->peakMemUsage()));
			$this->out();
		}

		$this->close($exitCode);
	}
}

FOFApplicationCLI::getInstance('AdminToolsFAM')->execute();
