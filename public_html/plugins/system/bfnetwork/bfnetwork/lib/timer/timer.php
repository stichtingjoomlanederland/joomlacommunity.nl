<?php
/**
 * @package   Blue Flame Network (bfNetwork)
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017 Blue Flame Digital Solutions Ltd. All rights reserved.
 * @license   GNU General Public License version 3 or later
 * @link      https://myJoomla.com/
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
 */
/**
 * @package    AkeebaCMSUpdate
 * @copyright  Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 * @license    GNU General Public License version 3, or later
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
 */

/**
 * The Timer class is used to intelligently prevent timeout errors when
 * performing long operations.
 */
class AcuTimer
{
    /**
     * Maximum execution time allowance per step.
     *
     * @var  integer
     */
    private $max_exec_time = NULL;

    /**
     * Minimum execution time per step.
     *
     * @var  integer
     */
    private $min_exec_time = NULL;

    /**
     * Timestamp of execution start
     *
     * @var  integer
     */
    private $start_time = NULL;

    /**
     * Public constructor, creates the timer object and calculates the
     * execution time limits.
     *
     * @param   array $params The configuration parameters for the timer
     *
     * @return  AcuTimer
     */
    public function __construct($params = array())
    {
        if (!is_array($params)) {
            $params = array();
        }

        $defaultParams = array(
            'max_exec_time' => 14,
            'min_exec_time' => 1,
            'run_time_bias' => 75,
        );

        $params = array_merge($defaultParams, $params);

        // Initialize start time
        $this->start_time = $this->microtime_float();

        // Store the minimum execution time
        $this->min_exec_time = $params['min_exec_time'];

        // Get configured max time per step and bias
        $config_max_exec_time = $params['max_exec_time'];
        $bias                 = $params['run_time_bias'] / 100;

        // Get PHP's maximum execution time (our upper limit)
        if (@function_exists('ini_get')) {
            $php_max_exec_time = @ini_get("maximum_execution_time");

            if ((!is_numeric($php_max_exec_time)) || ($php_max_exec_time == 0)) {
                // If we have no time limit, set a hard limit of about 10 seconds
                // (safe for Apache and IIS timeouts, verbose enough for users)
                $php_max_exec_time = 14;
            }
        } else {
            // If ini_get is not available, use a rough default
            $php_max_exec_time = 14;
        }

        // Apply an arbitrary correction to counter CMS load time
        $php_max_exec_time = $php_max_exec_time - max($php_max_exec_time * 0.1, 1);

        // Apply bias
        $php_max_exec_time    = $php_max_exec_time * $bias;
        $config_max_exec_time = $config_max_exec_time * $bias;

        // Use the most appropriate time limit value
        if ($config_max_exec_time > $php_max_exec_time) {
            $this->max_exec_time = $php_max_exec_time;
        } else {
            $this->max_exec_time = $config_max_exec_time;
        }
    }

    /**
     * Wake-up function to reset internal timer when we get unserialized
     *
     * @return  void
     */
    public function __wakeup()
    {
        // Re-initialize start time on wake-up
        $this->start_time = $this->microtime_float();
    }

    /**
     * Gets the number of seconds left, before we hit the "must stop" threshold
     *
     * @return  float  The time left in decimal seconds
     */
    public function getTimeLeft()
    {
        return $this->max_exec_time - $this->getRunningTime();
    }

    /**
     * Gets the time elapsed since object creation/unserialization, effectively how
     * long you have been processing data since you instantiated AcuTimer.
     *
     * @return  float  The number of elapsed time in decimal seconds
     */
    public function getRunningTime()
    {
        return $this->microtime_float() - $this->start_time;
    }

    /**
     * Returns the current timestamp in decimal seconds
     *
     * @return  float  Current timestamp in decimal seconds
     */
    private function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());

        return ((float)$usec + (float)$sec);
    }

    /**
     * Enforce the minimum execution time. Call this at the end of your long
     * processing to make sure that it doesn't take less time than the
     * minimum execution time. This is used to avoid being blocked by
     * overzealous server protection solutions.
     *
     * @return  void
     */
    public function enforce_min_exec_time()
    {
        // Try to get a sane value for PHP's maximum_execution_time INI parameter
        if (@function_exists('ini_get')) {
            $php_max_exec = @ini_get("maximum_execution_time");
        } else {
            $php_max_exec = 10;
        }

        if (($php_max_exec == "") || ($php_max_exec == 0)) {
            $php_max_exec = 10;
        }

        // Decrease $php_max_exec time by 500 msec we need (approx.) to tear down
        // the application, as well as another 500msec added for rounding
        // error purposes. Also make sure this is never going to be less than 0.
        $php_max_exec = max($php_max_exec * 1000 - 1000, 0);

        // Get the "minimum execution time per step" Akeeba Backup configuration variable
        $minexectime = $this->min_exec_time;

        if (!is_numeric($minexectime)) {
            $minexectime = 0;
        }

        // Make sure we are not over PHP's time limit!
        if ($minexectime > $php_max_exec) {
            $minexectime = $php_max_exec;
        }

        // Get current running time
        $elapsed_time = $this->getRunningTime() * 1000;

        // Only run a sleep delay if we haven't reached the minexectime execution time
        if (($minexectime > $elapsed_time) && ($elapsed_time > 0)) {
            $sleep_msec = $minexectime - $elapsed_time;

            if (function_exists('usleep')) {
                usleep(1000 * $sleep_msec);
            } elseif (function_exists('time_nanosleep')) {
                $sleep_sec  = floor($sleep_msec / 1000);
                $sleep_nsec = 1000000 * ($sleep_msec - ($sleep_sec * 1000));
                time_nanosleep($sleep_sec, $sleep_nsec);
            } elseif (function_exists('time_sleep_until')) {
                $until_timestamp = time() + $sleep_msec / 1000;
                time_sleep_until($until_timestamp);
            } elseif (function_exists('sleep')) {
                $sleep_sec = ceil($sleep_msec / 1000);
                sleep($sleep_sec);
            }
        } elseif ($elapsed_time > 0) {
            // No sleep required, even if user configured us to be able to do so.
        }
    }

    /**
     * Reset the timer. It should only be used in CLI mode!
     *
     * @return  void
     */
    public function resetTime()
    {
        $this->start_time = $this->microtime_float();
    }
}
