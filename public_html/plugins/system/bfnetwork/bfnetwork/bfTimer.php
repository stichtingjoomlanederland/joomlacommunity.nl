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

/**
 * Some of this taken from Akeeba Backup.
 *
 * @copyright Copyright (c)2009 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 */
class bfTimer
{
    /**
     * @var int Maximum execution time allowance per step
     */
    private $max_exec_time = null;

    /**
     * @var int Timestamp of execution start
     */
    public $start_time = null;

    /**
     * Public constructor, creates the timer object and calculates the execution
     * time limits.
     */
    public function __construct()
    {
        // Initialize start time
        $this->start_time = $this->microtime_float();

        // Get PHP's maximum execution time (our upper limit)
        if (@function_exists('ini_get')) {
            $php_max_exec_time = @ini_get('max_execution_time');

            if ((!is_numeric($php_max_exec_time)) || (0 == $php_max_exec_time)) {
                // If we have no time limit, set a hard limit of about 10
                // seconds
                // (safe for Apache and IIS timeouts, verbose enough for users)
                $php_max_exec_time = _BF_CONFIG_PHP_MAX_EXEC_TIME;
            }
        } else {
            // If ini_get is not available, use a rough default
            $php_max_exec_time = _BF_CONFIG_PHP_MAX_EXEC_TIME;
        }

        // Apply an arbitrary correction to counter Decryption load time
        --$php_max_exec_time;
        --$php_max_exec_time;

        // Apply bias
        $this->max_exec_time = $php_max_exec_time;
        // Use the most appropriate time limit value

        // Overrule EVERYthing above :-) set hard limit
        if (_BF_CONFIG_PHP_MAX_EXEC_TIME_HARD_LIMIT !== null) {
            $this->max_exec_time = _BF_CONFIG_PHP_MAX_EXEC_TIME_HARD_LIMIT;
        }

        // crappy webhost
        if (ini_get('max_execution_time') < $this->max_exec_time) {
            $this->max_exec_time = ini_get('max_execution_time');
            --$this->max_exec_time;
        }
    }

    /**
     * @return bfTimer
     */
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new bfTimer();
        }

        return $instance;
    }

    /**
     * Wake-up function to reset internal timer when we get unserialized.
     */
    public function __wakeup()
    {
        // Re-initialize start time on wake-up
        $this->start_time = $this->microtime_float();
    }

    /**
     * Gets the number of seconds left, before we hit the "must break" threshold.
     *
     * @return float
     */
    public function getTimeLeft()
    {
        return $this->max_exec_time - $this->getRunningTime();
    }

    /**
     * Gets the time elapsed since object creation/unserialization, effectively
     * how
     * long Akeeba Engine has been processing data.
     *
     * @return float
     */
    public function getRunningTime()
    {
        return $this->microtime_float() - $this->start_time;
    }

    /**
     * Returns the current timestamp in decimal seconds.
     */
    public function microtime_float()
    {
        list($usec, $sec) = explode(' ', microtime());

        return (float) $usec + (float) $sec;
    }

    /**
     * Reset the timer.
     * It should only be used in CLI mode!
     */
    public function resetTime()
    {
        $this->start_time = $this->microtime_float();
    }

    /**
     * @return int|string|null
     */
    public function getMaxTime()
    {
        return $this->max_exec_time;
    }

    /**
     * @return float|int|null
     */
    public function getStartTime()
    {
        return $this->start_time;
    }
}
