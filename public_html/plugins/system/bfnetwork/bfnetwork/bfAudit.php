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

// Decrypt or die
require 'bfEncrypt.php';

/**
 * If we have got here then we have already passed through decrypting
 * the encrypted header and so we are sure we are now secure and no one
 * else cannot run the code below.....
 * ... ...
 */

// Get the steps
require 'bfStep.php';

if (!file_exists('bfAuditor.php')) {
    bfEncrypt::reply(bfReply::ERROR, 'Your pathetic web host (let me guess, HostGator/DreamHost?) has deleted bfAuditor.php file believing it to be a hackers tool, they really have no right to indiscriminately delete YOUR sites files. This is not my problem, shout at your webhost!');
}

// Get the gutsy auditor tool
require 'bfAuditor.php';

// Tick over... inject the decrypted object
$scanner = new bfAudit ($dataObj);

// Tick Tock...
bfLog::log('Tick');
$scanner->tick();
