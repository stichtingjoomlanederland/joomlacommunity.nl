<?php
/**
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019 Blue Flame Digital Solutions Ltd. All rights reserved.
 * @license   GNU General Public License version 3 or later
 *
 * @see      https://mySites.guru/
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
 */
/**
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
 * The main class autoloader for the Akeeba CMS Update library.
 */
class AcuAutoloader
{
    /**
     * An instance of this autoloader.
     *
     * @var AcuAutoloader
     */
    public static $autoloader = null;

    /**
     * The path to the ACU library's root directory.
     *
     * @var string
     */
    public static $acuPath = null;

    /**
     * Initialise this autoloader.
     *
     * @return AcuAutoloader
     */
    public static function init()
    {
        if (null == self::$autoloader) {
            self::$autoloader = new self();
        }

        return self::$autoloader;
    }

    /**
     * Public constructor. Registers the autoloader with PHP.
     */
    public function __construct()
    {
        self::$acuPath = realpath(dirname(__FILE__));

        spl_autoload_register(array($this, 'autoload_acu_core'));
    }

    /**
     * The actual autoloader.
     *
     * @param string $class_name The name of the class to load
     */
    public function autoload_acu_core($class_name)
    {
        // Make sure the class has a FOF prefix
        if ('Acu' != substr($class_name, 0, 3)) {
            return;
        }

        // Remove the prefix
        $class = substr($class_name, 3);

        // Change from camel cased (e.g. DownloadCurl) into a lowercase array (e.g. 'download','curl')
        $class = preg_replace('/(\s)+/', '_', $class);
        $class = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $class));
        $class = explode('_', $class);

        // First try finding in structured directory format (preferred)
        $path = self::$acuPath.'/'.implode('/', $class).'.php';

        if (@file_exists($path)) {
            include_once $path;
        }

        // Then try the duplicate last name structured directory format (not recommended)

        if (!class_exists($class_name, false)) {
            reset($class);
            $lastPart = end($class);
            $path     = self::$acuPath.'/'.implode('/', $class).'/'.$lastPart.'.php';

            if (@file_exists($path)) {
                include_once $path;
            }
        }
    }
}
