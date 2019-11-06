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
 * This file contains code that can only be run on PHP 5.3.0+ servers.
 *
 * As mysites.guru service has to support PHP 5.2 for idiot servers with crappy
 * webhosting the main connector needs to be fully PHP 5.2 compliant most of the time
 *
 * The following code will ONLY run with a decent PHP version.
 */
final class bfPHPFiveThreePlusOnly
{
    public function getAkeebaConfig($configConfiguration)
    {
        $key = Akeeba\Engine\Factory::getSecureSettings()->getKey();

        return Akeeba\Engine\Factory::getSecureSettings()->decryptSettings($configConfiguration, $key);
    }
}
