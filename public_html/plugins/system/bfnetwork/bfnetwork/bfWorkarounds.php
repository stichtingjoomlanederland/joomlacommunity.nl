<?php

/*
 * @package   bfNetwork
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019 Blue Flame Digital Solutions Ltd. All rights reserved.
 * @license   GNU General Public License version 3 or later
 *
 * @see       https://myJoomla.guru/
 * @see       https://myWP.guru/
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
 * Provide some code so that pathetic sh404sef doesn't cause Internal Server Errors
 * We tried to "educate" them but they do not care enough to understand what they are doing is against Best Practice
 * and placing global functions in the global scope is highly frowned upon by professional developers, would have been better to
 * have created these as namespaces static methods.
 */
if (!function_exists('wbStartsWith')) {
    /**
     * Used by the installer plugin for sh404sef.
     *
     * @copyright Copyright (c) 2011 - 2017 - Weeblr,llc
     * @License GNU General Public License
     *
     * @param $haystack
     * @param $needles
     *
     * @return bool
     */
    function wbStartsWith($haystack, $needles)
    {
        if (is_string($needles)) {
            return !empty($needles) && 0 === strpos($haystack, $needles);
        } elseif (is_array($needles)) {
            foreach ($needles as $needle) {
                if (!empty($needle) && 0 === strpos($haystack, $needle)) {
                    return true;
                }
            }
        }

        return false;
    }
}
