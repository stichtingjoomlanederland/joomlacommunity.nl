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

define('_BF_IN_UPGRADE', 1);

try {
    require 'bfEncrypt.php';

    /*
     * If we have got here then we have already passed through decrypting
     * the encrypted header and so we are sure we are now secure and no one
     * else cannot run the code below.
     */

    // need Zip to decompress
    if (!class_exists('Bf_Zip')) {
        require 'bfZip.php';
    }

    // attempt to ensure our folder is writable
    if (!is_writeable('.')) {
        @chmod('.', 0755);
    }

    /*
     * ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT **
     * We tried 755 and that never worked so we are forced into this :-(
     */
    if (!is_writeable('.')) {
        @chmod('.', 0777);
    }

    // Give Up!
    if (!is_writeable('.')) {
        throw new Exception('bfNetwork Folder not writeable');
    }

    // check file is from myJoomla.com for security

    // Allow for local development with a local endpoint
    switch ($_POST['APPLICATION_ENV']) { // Switch from insecure $_POST to a known clean value locally
        case'development':
        case 'local':
            // Never used on public servers
            $upgradeFile = 'https://local-maintain.myjoomla.com/public/connector';
            break;
        case 'staging':
            // staging Mode Endpoint - by invitation only - email phil@phil-taylor.com for early access!
            $upgradeFile = 'https://staging.myjoomla.com/public/connector';
            break;
        default:
            // Production Mode Endpoint... ...
            $upgradeFile = 'https://cdn.myjoomla.com/public/connector';
            break;
    }

    $method = 'F';
    // Attempt to download using file_get_contents - quickest and easiest and works well on *most* servers!!
    $upgradeFileContent = file_get_contents($upgradeFile);

    if (!$upgradeFileContent) {
        $method = 'C';

        $ch = curl_init();

        // Set up bare minimum CURL Options needed for myJoomla.com
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $upgradeFile);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Attempt to download using CURL and CURLOPT_SSL_VERIFYPEER set to TRUE
        $upgradeFileContent = curl_exec($ch);

        // Did we succeed in getting something?????
        if (!$upgradeFileContent) {
            $method = 'CV';
            /*
             * ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT **
             *
             * Ok try without validation of the SSL (gulp) but this is needed on some servers without a pem file
             * and we need to be compatible as possible - even on crappy webhosts when they need us most ;-(
             */
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            //  Second Attempt to download using CURL and CURLOPT_SSL_VERIFYPEER set to FALSE (gulp)
            $upgradeFileContent = curl_exec($ch);
        }

        curl_close($ch);
    }

    // Did we succeed in getting something?
    if (!$upgradeFileContent) {
        throw new Exception('Could not download connector upgrade file using file_get_contents or curl functions - contact Phil for support');
    }

    // Remember: The upgrade file DOESN'T contain any security keys! This is a good thing!

    // Save the Zip File - first removing any existing file
    @unlink('upgrade.zip');
    if (!file_put_contents('upgrade.zip', $upgradeFileContent)) {
        throw new Exception('Could not auto upgrade (save upgrade file failed) - you need to install a new connector manually (Debug: '.$method.'|'.is_writable('.').'|'.file_exists('upgrade.zip').'|'.strlen($upgradeFileContent).')');
    }

    // Load the Zip file
    $zip = new Bf_Zip('upgrade.zip');

    // Extract the Zip file
    if (!$zip->extract(PCLZIP_OPT_PATH, './', PCLZIP_OPT_REMOVE_PATH, 'bfnetwork', PCLZIP_OPT_REPLACE_NEWER)) {
        throw new Exception('Could not auto upgrade (Extract Error) - you need to install a new connector manually');
    }

    // .. @todo check each file is valid against some kind of hash to prevent modifications client side

    // cleanup old files
    $oldFiles = array(
        'upgrade.zip',
        './bfViewLog.php',
        './bfDev.php',
        './bfDb.php',
        './bfMysql.php',
        './j25_30_bfnetwork.xml', // dont get confused with the one in the folder above this.
        './install.bfnetwork.php',
        './bfnetwork.xml',
        './bfJson.php',
        './tmp/log.tmp',
        './tmp/tmp.ob',
    );

    foreach ($oldFiles as $file) {
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    // cleanup
    if (file_exists('../j25_30_bfnetwork.xml')) {
        @copy('../j25_30_bfnetwork.xml', '../bfnetwork.xml');
        @unlink('../j25_30_bfnetwork.xml');
    }

    // Reply with a great big high five!
    bfEncrypt::reply(bfReply::SUCCESS, array(
        'version' => file_get_contents('VERSION'),
    ));
} catch (Exception $e) {
    bfEncrypt::reply(bfReply::ERROR, 'EXCEPTION: '.$e->getMessage());
}
