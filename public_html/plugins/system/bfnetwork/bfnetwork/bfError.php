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

// Taken from http://php.net/manual/en/function.set-error-handler.php

if (class_exists('bfLog')) {
    class bfError
    {
        /**
         * CATCHABLE ERRORS.
         *
         * @param $number
         * @param $message
         * @param $file
         * @param $line
         */
        public static function captureNormal($number, $message, $file, $line)
        {
            if (2048 == $number) {
                return;
            } // E_ALL
            if (8192 == $number) {
                return;
            } // deprecated
            if ('syntax error, unexpected \'(\' in Unknown on line 13' == $message) {
                return;
            } // Crappy Virtuemart Language Issues

            bfLog::log('!!!!!! ERROR !!!!!! = '.$message.' in file '.$file.' line: '.$line);
        }

        /**
         * EXTENSIONS.
         *
         * @param $exception
         */
        public static function captureException($exception)
        {
            /*
             * Ignore these
             * 09:07:51UTC !!!!!! ERROR !!!!!! = fopen(/dev/urandom) [function.fopen]: failed to open stream: Operation not permitted
             * 09:07:51UTC !!!!!! ERROR !!!!!! = fopen() [function.fopen]: open_basedir restriction in effect. File(/dev/urandom) is not within the allowed path(s): (/usr/local/php/lib/php/:/home/www/:/usr/bin/:/tmp:/usr/local/php52/lib/php/)
             */
            if (preg_match('/dev\/urandom/', $exception->getMessage())) {
                return;
            }

            bfLog::log('!!!!!! EXCEPTION !!!!!! ='.$exception->getMessage().$exception->getFile().':'.$exception->getLine());
        }

        /**
         *UNCATCHABLE ERRORS.
         */
        public static function captureShutdown()
        {
            if (defined('_BF_LAST_BREATH')) {
                bfLog::log('Tock with dying breath said:  '._BF_LAST_BREATH);
            } else {
                bfLog::log('Tock');
            }
        }
    }

    set_error_handler(array('bfError', 'captureNormal'));
    set_exception_handler(array('bfError', 'captureException'));
    register_shutdown_function(array('bfError', 'captureShutdown'));
}
