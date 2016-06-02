<?php
/**
 *  @package AkeebaBackup
 *  @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  --
 *
 *  Unlike the other CRON scripts this one cannot use the AkeebaCliBase class.
 *  JUpdater expects the calling application to extend JApplication :(
 */

use Akeeba\Engine\Platform;

// Define ourselves as a parent file
define('_JEXEC', 1);

// Setup and import the base CLI script
$minphp = '5.4.0';
$curdir = __DIR__;

require_once __DIR__ . '/../administrator/components/com_akeeba/Master/Cli/Base.php';

// Enable and include Akeeba Engine
define('AKEEBAENGINE', 1);

if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
{
    throw new RuntimeException('FOF 3.0 is not installed', 500);
}

/**
 * Akeeba Backup Update application
 */
class AkeebaBackupUpdate extends AkeebaCliBase
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
        $jlang->load('com_akeeba', $paths[0], 'en-GB', true);
        $jlang->load('com_akeeba', $paths[1], 'en-GB', true);
        $jlang->load('com_akeeba' . '.override', $paths[0], 'en-GB', true);
        $jlang->load('com_akeeba' . '.override', $paths[1], 'en-GB', true);


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

        $version		 = AKEEBA_VERSION;
        $date			 = AKEEBA_DATE;

        $phpversion		 = PHP_VERSION;
        $phpenvironment	 = PHP_SAPI;

        if ($this->input->get('quiet', -1, 'int') == -1)
        {
            $year = gmdate('Y');
            echo <<<ENDBLOCK
Akeeba Backup Update $version ($date)
Copyright (c) 2006-$year Akeeba Ltd / Nicholas K. Dionysopoulos
-------------------------------------------------------------------------------
Akeeba Backup is Free Software, distributed under the terms of the GNU General
Public License version 3 or, at your option, any later version.
This program comes with ABSOLUTELY NO WARRANTY as per sections 15 & 16 of the
license. See http://www.gnu.org/licenses/gpl-3.0.html for details.
-------------------------------------------------------------------------------
You are using PHP $phpversion ($phpenvironment)
$debugmessage
Checking for new Akeeba Backup versions


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

        // Load the engine
        $factoryPath = JPATH_ADMINISTRATOR . '/components/com_akeeba/BackupEngine/Factory.php';
        define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_akeeba');
        define('AKEEBAROOT', JPATH_ADMINISTRATOR . '/components/com_akeeba/akeeba');
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

		// Assign the correct platform
		Platform::addPlatform('joomla3x', JPATH_COMPONENT_ADMINISTRATOR . '/BackupPlatform/Joomla3x');

        $container = \FOF30\Container\Container::getInstance('com_akeeba');
        /** @var \Akeeba\Backup\Site\Model\Updates $updateModel */
        $updateModel = $container->factory->model('Updates')->tmpInstance();

        $result = $updateModel->autoupdate();

        echo implode("\n", $result['message']);

        $this->close(0);
    }
}

// Load the version file
require_once JPATH_ADMINISTRATOR . '/components/com_akeeba/version.php';

// Instanciate and run the application
AkeebaCliBase::getInstance('AkeebaBackupUpdate')->execute();