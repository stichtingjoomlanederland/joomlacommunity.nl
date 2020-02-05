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

class bfLog
{
    const FILE = '/tmp/log.php';

    /**
     * To log do bfLog::log('something');.
     *
     * @param      $msg
     * @param bool $truncate
     */
    public static function log($msg, $truncate = false, $forceThisLineThisTime = false)
    {
        $preferences = new bfPreferences();
        $prefs       = $preferences->getPreferences();

        if (false === $forceThisLineThisTime && false == $prefs->_BF_LOG) {
            return; // Dont log this call...
        }

        if (!is_string($msg)) {
            ob_start();
            var_dump($msg);
            $msg = ob_get_contents();
            ob_end_clean();
        }

        if (file_exists(dirname(__FILE__).bfLog::FILE)) {
            $logSize = number_format(filesize(dirname(__FILE__).bfLog::FILE) / 1024 / 1024, 2);
            if ($logSize > 10) {
                $truncate = true;
            }
        }
        $template = '%s  | %s';
        $msg      = sprintf($template,
            self::getTimestamp(),
            $msg);

        if (true === $truncate) {
            bfLog::truncate();
        }

        file_put_contents(dirname(__FILE__).bfLog::FILE, $msg.PHP_EOL, FILE_APPEND);
    }

    /**
     * I know this looks stupid now, but it allows custimisation of the timestamp in future.
     *
     * @return bool|string
     */
    public static function getTimestamp()
    {
        return date('H:i:s');
    }

    /**
     * Truncate the log file, prepare a new one.
     */
    public static function truncate()
    {
        $preferences = new bfPreferences();
        $prefs       = $preferences->getPreferences();

        bflog::checkPermissions();

        @unlink('tmp/log.tmp');
        @unlink('tmp/log.php');

        @file_put_contents(dirname(__FILE__).bfLog::FILE, '<?php die(); ?>'.PHP_EOL);

        bfLog::log('Log file truncated');

        // populate the config into the log
        bfLog::log('PHP Max Memory = '.ini_get('memory_limit'));
        bfLog::log('PHP ini_setted Max Time = '.ini_get('max_execution_time'));
        bfLog::log('PHP bfTimer Max Time = '.bfTimer::getInstance()
                ->getMaxTime());
    }

    /**
     * Require all we need to work.
     */
    public static function checkPermissions()
    {
        // attempt to ensure our tmp folder is writable
        if (!is_writeable(dirname(__FILE__).'/tmp')) {
            @chmod(dirname(__FILE__).'/tmp', 0755);
        }

        // Argh!
        if (!is_writeable(dirname(__FILE__).'/tmp')) {
            @chmod(dirname(__FILE__).'/tmp', 0777);
        }

        // Give Up!
        if (!is_writeable(dirname(__FILE__).'/tmp')) {
            die('Our '.dirname(__FILE__).'/tmp folder on your site is not writable!');
        }

        // attempt to ensure our folder is writable
        if (!is_writeable(dirname(__FILE__))) {
            @chmod(dirname(__FILE__), 0755);
        }

        // Argh!
        if (!is_writeable(dirname(__FILE__))) {
            @chmod(dirname(__FILE__), 0777);
        }

        // Give Up!
        if (!is_writeable(dirname(__FILE__))) {
            die(dirname(__FILE__).'/ folder not writeable');
        }
    }

    public static function convert($size)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');

        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2).' '.$unit[$i];
    }

    /**
     * Check we have permissions to write a log file.
     */
    public static function init()
    {
        bfLog::checkPermissions();
    }

    public static function getLog()
    {
        return file_get_contents(dirname(__FILE__).bfLog::FILE);
    }

    /**
     * bfLog::getTail();.
     *
     * @param string $filename
     * @param int    $n
     *
     * @return array
     */
    public static function getTail($filename = null, $n = 1000)
    {
        if (null === $filename) {
            $filename = dirname(__FILE__).bfLog::FILE;
        }

        $buffer_size = 512;

        $fp = fopen($filename, 'r');
        if (!$fp) {
            return array();
        }

        fseek($fp, 0, SEEK_END);
        $pos = ftell($fp);

        $input      = '';
        $line_count = 0;

        while ($line_count < $n + 1) {
            // read the previous block of input
            $read_size = $pos >= $buffer_size ? $buffer_size : $pos;
            fseek($fp, $pos - $read_size, SEEK_SET);

            // prepend the current block, and count the new lines
            $input      = fread($fp, $read_size).$input;
            $line_count = substr_count(ltrim($input), "\n");

            // if $pos is == 0 we are at start of file
            $pos -= $read_size;
            if (!$pos) {
                break;
            }
        }

        // close the file pointer
        fclose($fp);

        // return the last 50 lines found
        return array_reverse(array_slice(explode("\n", rtrim($input)), -$n));
    }
}
