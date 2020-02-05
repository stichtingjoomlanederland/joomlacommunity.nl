<?php

/*
 * @package   bfNetwork
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020 Blue Flame Digital Solutions Ltd. All rights reserved.
 * @license   GNU General Public License version 3 or later
 *
 * @see       https://mySites.guru/
 * @see       https://www.phil-taylor.com/
 *
 * @author    Phil Taylor / Blue Flame Digital Solutions Limited.
 *
 * bfNetwork is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * bfNetwork is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this package.  If not, see http://www.gnu.org/licenses/
 *
 * If you have any questions regarding this code, please contact phil@phil-taylor.com
 */

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 08 Mar 1978 05:00:00 GMT'); // Phil Taylor's Birthday and Time :-)
header('Content-type: application/json');

// buffer it!
//ob_start();

$isWin = ('WIN' == substr(PHP_OS, 0, 3));
$sep   = $isWin ? ';' : ':';
@ini_set('include_path', dirname(__FILE__).$sep.ini_get('include_path'));

// Allow persistent overide of the config\

require 'bfPreferences.php';
$preferences = new bfPreferences();
$preferences = $preferences->getPreferences();

if (!defined('_BF_LOG')) {
    define('_BF_LOG', $preferences->_BF_LOG);
}

// Attempt to screw up mysql if we can
@ini_set('mysql.connect_timeout', 300);
@ini_set('default_socket_timeout', 300);

// Set timezone
date_default_timezone_set('UTC'); // Should be "UTC"!

// Attempt to ensure we can access the internet on crap configured hosts
@ini_set('allow_url_fopen', 1);

// Get time limits
define('_BF_ORIGINAL_TIME_LIMIT', @ini_get('max_execution_time'));

// Set memory limits - Yes I know 1024M is a large, but hey ;-)
define('_BF_ORIGINAL_MEMORY_LIMIT', @ini_set('memory_limit', '1024M'));

// Set no display errors to the screen, prevent leaks of information
define('_BF_ORIGINAL_DISPLAY_ERRORS', @ini_set('display_errors', 0));

// Debug mode - never enable this on a live site! default: FALSE
define('_BF_API_DEBUG', false); //should always be  FALSE

// NEVER EVER DEFINE THIS AS TRUE ON A LIVE SITE - WILL leak all replies as non-encrypted!
define('_BF_API_REPLY_DEBUG_NEVER_ENABLE_THIS_EVER_WILL_LEAK_CONFIDENTIAL_INFO_IN_RESPONSES', false); //should always be FALSE

// used in bfAuditor    default: FALSE
define('_BF_CONFIG_RESET_STATE_ON_UPGRADE', false);

// used in bfAuditor    default: 0, 10, 20
define('_BF_CONFIG_FILES_TIMER_ONE', 0);

// used in bfAuditor    default: half of _BF_CONFIG_FILES_TIMER_ONE
define('_BF_CONFIG_FILES_TIMER_TWO', 0);

// used in bfAuditor    default: 0, 10, 20
define('_BF_CONFIG_FOLDERS_TIMER_ONE', 0);

// used in bfAuditor    default: half of _BF_CONFIG_FOLDERS_TIMER_ONE
define('_BF_CONFIG_FOLDERS_TIMER_TWO', 0);

// used in bfAuditor    default: 0, 10, 20
define('_BF_CONFIG_DEEPSCAN_TIMER_ONE', 1);

// not yet used   default: 5
define('_BF_CONFIG_ERROR_RESUME_RETRY_LIMIT', 5);

/**
 * Ok so I know we are using a raw request here... but we want to configure the defaults, log and timer BEFORE
 * we decrypt the encrypted request.
 *
 * we DONT so anything based on the unencrypted data apart from set hardcoded values - there is nothing that can
 * be hacked here,
 */
$allowedValues = array(
    'CRAPPYWEBHOST',
    'FIVE_SECOND_TIMEOUT',
    'SNAIL',
    'DEFAULT',
    'FAST',
);

if (!array_key_exists('SPEED', $_REQUEST)
    || !in_array($_REQUEST['SPEED'], $allowedValues)
    || !@$_REQUEST['SPEED']
) {
    $_REQUEST['SPEED'] = 'DEFAULT';
}

define('_BF_SPEED', $_REQUEST['SPEED']);

switch ($_REQUEST['SPEED']) {
    case 'FAST':
        @ini_set('max_execution_time', 90);

        // used in bfConfig     default: Something stupid large like 90
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME_INI', 90);

        // used in bfTimer      default: 10
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME', 60);

        // used in bfTimer      default: null
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME_HARD_LIMIT', 60);

        // number of folders to scan at a time  default: same as _BF_CONFIG_FILES_TIMER_ONE
        define('_BF_CONFIG_FILES_COUNT_ONE', 60);

        // number of folders to scan at a time  default: same as _BF_CONFIG_FOLDERS_TIMER_ONE
        define('_BF_CONFIG_FOLDERS_COUNT_ONE', 60);

        // number of folders to scan at a time  default: same as _BF_CONFIG_DEEPSCAN_TIMER_ONE
        define('_BF_CONFIG_DEEPSCAN_COUNT_ONE', 60);

        // used in bfAuditor    default: half of _BF_CONFIG_DEEPSCAN_TIMER_ONE
        define('_BF_CONFIG_DEEPSCAN_TIMER_TWO', 0);
        break;
    case 'SNAIL':
        @ini_set('max_execution_time', 60);

        // used in bfConfig     default: Something stupid large like 90
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME_INI', 60);

        // used in bfTimer      default: 10
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME', 10);

        // used in bfTimer      default: null
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME_HARD_LIMIT', 10);

        // number of folders to scan at a time  default: same as _BF_CONFIG_FILES_TIMER_ONE
        define('_BF_CONFIG_FILES_COUNT_ONE', 10);

        // number of folders to scan at a time  default: same as _BF_CONFIG_FOLDERS_TIMER_ONE
        define('_BF_CONFIG_FOLDERS_COUNT_ONE', 10);

        // number of folders to scan at a time  default: same as _BF_CONFIG_DEEPSCAN_TIMER_ONE
        define('_BF_CONFIG_DEEPSCAN_COUNT_ONE', 10);

        // used in bfAuditor    default: half of _BF_CONFIG_DEEPSCAN_TIMER_ONE
        define('_BF_CONFIG_DEEPSCAN_TIMER_TWO', 0);

        break;

    case '20SECGATEWAYTIMEOUT':
    case 'FIVE_SECOND_TIMEOUT':
        @ini_set('max_execution_time', 60);

        // used in bfConfig     default: Something stupid large like 90
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME_INI', 60);

        // used in bfTimer      default: 10
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME', 5);

        // used in bfTimer      default: null
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME_HARD_LIMIT', 5);

        // number of folders to scan at a time  default: same as _BF_CONFIG_FILES_TIMER_ONE
        define('_BF_CONFIG_FILES_COUNT_ONE', 5);

        // number of folders to scan at a time  default: same as _BF_CONFIG_FOLDERS_TIMER_ONE
        define('_BF_CONFIG_FOLDERS_COUNT_ONE', 5);

        // number of folders to scan at a time  default: same as _BF_CONFIG_DEEPSCAN_TIMER_ONE
        define('_BF_CONFIG_DEEPSCAN_COUNT_ONE', 5);

        // used in bfAuditor    default: half of _BF_CONFIG_DEEPSCAN_TIMER_ONE
        define('_BF_CONFIG_DEEPSCAN_TIMER_TWO', 0);
        break;

    case 'CRAPPYWEBHOST':

        @ini_set('max_execution_time', 60);

        // used in bfConfig     default: Something stupid large like 90
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME_INI', 60);

        // used in bfTimer      default: 10
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME', 20);

        // used in bfTimer      default: null
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME_HARD_LIMIT', 20);

        // number of folders to scan at a time  default: same as _BF_CONFIG_FILES_TIMER_ONE
        define('_BF_CONFIG_FILES_COUNT_ONE', 5);

        // number of folders to scan at a time  default: same as _BF_CONFIG_FOLDERS_TIMER_ONE
        define('_BF_CONFIG_FOLDERS_COUNT_ONE', 5);

        // number of folders to scan at a time  default: same as _BF_CONFIG_DEEPSCAN_TIMER_ONE
        define('_BF_CONFIG_DEEPSCAN_COUNT_ONE', 5);

        // used in bfAuditor    default: half of _BF_CONFIG_DEEPSCAN_TIMER_ONE
        define('_BF_CONFIG_DEEPSCAN_TIMER_TWO', 5);
        break;

    case 'DEFAULT':
    default:
        @ini_set('max_execution_time', 60);

        // used in bfConfig     default: Something stupid large like 90
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME_INI', 60);

        // used in bfTimer      default: 10
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME', 20);

        // used in bfTimer      default: null
        define('_BF_CONFIG_PHP_MAX_EXEC_TIME_HARD_LIMIT', 20);

        // number of folders to scan at a time  default: same as _BF_CONFIG_FILES_TIMER_ONE
        define('_BF_CONFIG_FILES_COUNT_ONE', 20);

        // number of folders to scan at a time  default: same as _BF_CONFIG_FOLDERS_TIMER_ONE
        define('_BF_CONFIG_FOLDERS_COUNT_ONE', 20);

        // number of folders to scan at a time  default: same as _BF_CONFIG_DEEPSCAN_TIMER_ONE
        define('_BF_CONFIG_DEEPSCAN_COUNT_ONE', 20);

        // used in bfAuditor    default: half of _BF_CONFIG_DEEPSCAN_TIMER_ONE
        define('_BF_CONFIG_DEEPSCAN_TIMER_TWO', 0);
        break;
}

// Set a very high upper limit - bfTimer will attempt to clear WAYYYYY before this is hit
@set_time_limit(_BF_CONFIG_PHP_MAX_EXEC_TIME_INI);
