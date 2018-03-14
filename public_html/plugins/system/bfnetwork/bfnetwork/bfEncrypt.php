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
header('X-MYJOOMLA: HIT');

/**
 * Some high level request checks
 * Only accept POSTs, if a GET then just expose the fact we are listening
 */
count($_POST) or die('Ready');

/**
 * Provide some code so that pathetic extensions code can be worked around.
 */
require './bfWorkarounds.php';

/**
 * Compose the Endpoint for Validation
 *
 * Note: The use of md5 here serves to clean to a known 32 string, we dont care if the data is real or fake at this
 * point as that is validated by the myJoomla.com endpoint.
 *
 * The only weakness here is if our service is compromised or your DNS is compromised, however small a chance that is
 * the request is still encrypted, and will have to pass decryption validation later on, so is still secure.
 *
 * An UNIQUE_REQUEST_ID can only be used once!
 */
switch ($_POST['APPLICATION_ENV']) { // Switch from insecure $_POST to a known clean value locally
    case'development':
    case 'local':
        $APPLICATION_ENV = 'development';
        $urlPattern = 'https://local-manage.myjoomla.com/validate/?%s=%s';
        break;
    case 'staging':
        $APPLICATION_ENV = 'staging';
        $urlPattern = 'https://manage.myjoomla.com/validate/?%s=%s';
        break;
    default:
        // If brute force attempt to inject fake APPLICATION_ENV we reset to production
        $APPLICATION_ENV = 'production';
        $urlPattern = 'https://manage.myjoomla.com/validate/?%s=%s';
        break;
}

$validationUrl = sprintf($urlPattern, md5($_POST['UNIQUE_REQUEST_ID']), md5(base64_encode(json_encode($_POST))));

/**
 * Allow override of validation method CURL/file_get_contents
 * Yes we are using a $_POST var here, but again we are only using it as a configuration switch and no evaluation is made
 * It an attacker switched on the _POST['VM'] nothing bad happens and they gain nothing
 */
switch ($_POST['VM']) { // Switch from insecure $_POST to a known clean value locally
    case 'C' . 'U' . 'R' . 'L':
        $overrideVMethod = 'C' . 'U' . 'R' . 'L';
        break;
    default:
    case 'FILE':
        $overrideVMethod = 'FILE';
        break;
}

/**
 * Call validation service to validate the request
 *
 * Call back to myjoomla.com to authenticate that the request is genuine and from our service
 * If not a validated request fail to process anything else past this point.
 * Requests are valid if they have a valid UNIQUE_REQUEST_ID and the message has not been tampered with
 * The UNIQUE_REQUEST_ID also contains other security like, but limited to, time and site specific hashes
 *
 *  Ok so on some crappy servers - that firewall the outgoing requests, we cannot call home to validate the request
 *  so we need a way to "switch off" the validation process - this introduces a lesser level of security though :-(
 */

if ($overrideVMethod !== 'C' . 'U' . 'R' . 'L' && ini_get('allow_url_fopen') == TRUE && in_array('https', stream_get_wrappers())) {

    // Just so we can debug
    $vMethod = 'file_get_contents';

    $options = array(
        'http' => array(
            'method' => "GET",
            'header' => "User-Agent: myJoomla.com validation with file_get_contents\r\n"
        )
    );

    // create a new context for the request
    $context = stream_context_create($options);

    // safe as we explicitly set the url and the params as md5 strings above
    $validationResultHash = file_get_contents($validationUrl, FALSE, $context);

} else {
    /**
     * If we cannot use file_get_contents because the https stream wrapper was removed from PHP
     * or if allow_url_fopen is disabled then we will need to runt he requests with curl :-(
     */

    // Just so we can debug
    $vMethod = 'c' . 'u' . 'r' . 'l';

    // init
    $ch = curl_init();

    // configure CURL request
    curl_setopt($ch, CURLOPT_URL, $validationUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'myJoomla.com validation with c' . 'u' . 'r' . 'l');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    // run curl request
    $validationResultHash = curl_exec($ch);

    // did it work?
    if (FALSE == $validationResultHash) {
        /**
         * ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT **
         *
         * Ok try without validation of the SSL (gulp) but this is needed on some servers without a pem file
         * and we need to be compatible as possible - even on crappy webhosts when they need us most ;-(
         *
         * disabling the verification of the certificates, you leave the door open to potential MITM attacks HOWEVER
         * we are only sending md5's and expecting a 0 or hash back, no sensitive values are being sent, and all our
         * requests expire after 2 mins anyway, so even if a MITM attack was occurring there is absolutely no way to
         * exploit a site with the request or response of our service.
         */
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        $vMethod = 'C' . 'U' . 'R' . 'L' . ' with C' . 'U' . 'R' . 'L' . 'OPT_SSL_VERIFYPEER False';
        $validationResultHash = curl_exec($ch);

        if (FALSE == $validationResultHash) {
            echo 'C' . 'U' . 'R' . 'L ERROR: ';
            echo curl_error($ch);
            die;
        }
    }

    curl_close($ch);
}

/**
 * Check if request was authenticated
 *
 * Note: Again only using md5 to compare strings, no additional security is provided by the use of md5
 * Note: There is no valuable data hashed to make these md5 strings just randomisers
 */
if ("f59fbbcf2dc5e3888a079d34f821a75a" !== md5(trim($validationResultHash))) {
    echo 'Request not validated by myJoomla.com - This is fatal and means that YOUR server cannot send a request OUT to our service over https on port 443 using c' . 'u' . 'r' . 'l or file_get_contents() - this normally means your server is blocking OUTGOING requests with a firewall or misconfiguration.';
    echo '<br/><br/>Debug: We tried the validation with PHP methods: ' . $vMethod;
    echo '<br/><br/>Debug: env was ' . $APPLICATION_ENV;
    echo '<br/><br/>Debug: response was ' . print_r($validationResultHash, TRUE);
    die();
}


/* IF WE GET HERE THEN THE REQUEST IS VALIDATED AS GENUINE, BUT IS STILL ENCRYPTED */

// Require our config file
require 'bfConfig.php';

// Require some logging
require 'bfLog.php';
bfLog::init();

// Require special error handling
require 'bfError.php';

// Require Timer - will init the logger too
require 'bfTimer.php';
bfTimer::getInstance();

// Require decryption classes
require 'Crypt/RSA.php';
require 'Crypt/RC4.php';

// Set up the decryption
$rsa = new Crypt_RSA ();
$rsa->loadKey(file_get_contents('Keys/private.key'));
bfLog::log('RSA Key loaded');

// Just in case - crappy servers :-(
global $dataObj;
global $rc4_key;

// Only handle two types of POST
switch (@$_POST ['METHOD']) {

    /**
     * If our method is encrpyted (99.9% of our traffic) then decrypted it
     */
    case 'Encrypted' :
        define('BF_REQUEST_ENCRYPTED', TRUE);
        $dataObj = bfEncrypt::decrypt($rsa, TRUE);
        bfLog::log('Request Decrypted');
        break;

    /**
     * If our method is an encrypted header, with some files unencrypted (1
     * call of our traffic) then decrypt the header, if ok then allow
     * proceed.
     */
    case 'EncryptedHeaderWithNotEncryptedData' :

        // This is an NOTENCRYPTED connection - beware!
        define('BF_REQUEST_ENCRYPTED', FALSE);
        $dataObj = bfEncrypt::decrypt($rsa);
        bfLog::log('Request EncryptedHeaderWithNotEncryptedData Decrypted');
        break;

    default :
        die (json_encode(array(
            'METHOD'       => 'NOTENCRYPTED',
            'RESULT'       => bfReply::ERROR,
            'NOTENCRYPTED' => array(
                'msg' => 'Failed Method'
            )
        )));
        break;
}

/**
 * If we have got here then we have already passed through decrypting
 * the encrypted header and so we are sure we are now secure and no one
 * else cannot run the code below.
 */

// If we get here then we are through the decryption process
define('BF_REQUEST_METHOD', $_POST ['METHOD']);

// Set unique host id for this site
if (property_exists($dataObj, 'SET_HOST_ID')) {
    file_put_contents('HOST_ID', $dataObj->SET_HOST_ID);
}

// Some basic tests, not really security but some basics
if (!is_object($dataObj)) {
    die (json_encode(array(
        'METHOD'       => 'NOTENCRYPTED',
        'RESULT'       => bfReply::ERROR,
        'NOTENCRYPTED' => array(
            'msg' => 'The connector you have installed currently on your site doesnt match the last one we generated for this site, and thus the encryption certificates are invalid and we cannot encrypt/decrypt data with them - you need to delete this site from myJoomla.com and start the connection process again from scratch, making sure you install the exact connector generated in the process, as each is unique.'
        )
    )));
}

/* SOME CONSTs to make things easy on the eye */

final class bfReply
{
    const SUCCESS = 'SUCCESS';
    const FAILURE = 'FAILURE';
    const ERROR = 'ERROR';
    const NEEDSCONNECTORUPGRADE = 'NEEDSCONNECTORUPGRADE';
}

class bfEncrypt
{

    /**
     * Decrypt the incoming encrypted data
     * Will be encrypted with the public part of the key pair
     *
     * @param Crypt_Rsa $rsa
     * @param bool $enc
     *
     * @return mixed stdClass
     */
    static public function decrypt($rsa, $enc = FALSE)
    {
        $start = time();
        bfLog::log('Starting Decryption....');
        // Create an empty class
        $dataObj = new stdClass ();

        // If its a normal encrypted request - 99.9% of our requests
        if ($enc === TRUE) {
            $header = json_decode($rsa->decrypt(base64_decode($_POST ['ENCRYPTED'])));
        } else {
            // if an encrypted request, with an encrypted header, and non encrypted body 0.1% of our requests
            $header = json_decode($rsa->decrypt(base64_decode($_POST ['ENCRYPTED_HEADER'])));
        }

        if (!$header) {
            // If we get here then then the KEYS are wrong, or the request are
            // wrong
            die (json_encode(array(
                'METHOD'       => 'NOTENCRYPTED',
                'RESULT'       => bfReply::ERROR,
                'NOTENCRYPTED' => array(
                    'msg' => 'The connector you have installed currently on your site doesnt match the last one we generated for this site, and thus the encryption certificates are invalid and we cannot encrypt/decrypt data with them - you need to delete this site from myJoomla.com and start the connection process again from scratch, making sure you install the exact connector generated in the process, as each is unique.'
                )
            )));
        }

        /**
         * If we have got here then we have already passed through decrypting
         * the encrypted header and so we are sure we are now secure and no one
         * else cannot run the code below.
         */

        // When we get here we are DECRYPTED :-)
        bfLog::log('Finished Decryption.... took ' . (time() - $start) . ' seconds');

        // Set the encryption key to send data back with
        define('RC4_KEY', $header->RC4_KEY);

        // attempt to ensure our tmp folder is writable
        if (!is_writeable(dirname(__FILE__) . '/tmp')) {
            @chmod(dirname(__FILE__) . '/tmp', 0755);
        }

        // Argh!
        if (!is_writeable(dirname(__FILE__) . '/tmp')) {
            @chmod(dirname(__FILE__) . '/tmp', 0777);
        }

        // Give Up!
        if (!is_writeable(dirname(__FILE__) . '/tmp')) {
            bfEncrypt::reply(bfReply::ERROR, dirname(__FILE__) . '/tmp folder not writeable');
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
            bfEncrypt::reply(bfReply::ERROR, dirname(__FILE__) . '/ folder not writeable');
        }

        // check Version - do I need an upgrade before I proceed?
        $myVersion = file_get_contents('./VERSION');
        if (!defined('_BF_IN_UPGRADE') && $myVersion != $header->REQ_CLIENT_VERSION) {

            // Force a client connector upgrade
            bfEncrypt::reply(bfReply::NEEDSCONNECTORUPGRADE, bfReply::NEEDSCONNECTORUPGRADE);
        }

        // If a fully encrypted request then return all the data
        if ($enc === TRUE) {
            return $header;
        }

        // If a partially encrypted request, then we have an encrypted header,
        // and some non-encrypted body
        // check the checksum from the encrypted part of the request to prevent
        // spoofing
        if ("ENCRYPTED_HEADER" == $header->checksum) {
            // get the timestamp of the request
            $dataObj->timestamp = $header->timestamp;

            // get the non encrypted vars from the request
            if (array_key_exists('NOTENCRYPTED', $_POST)) {
                $dataObj->NOTENCRYPTED = $_POST ['NOTENCRYPTED'];
                foreach ($dataObj->NOTENCRYPTED as $k => $v) {
                    $dataObj->$k = $v;
                }
            }

            return $dataObj;
        } else {
            // If we get here then then the KEYS are wrong, or the request are
            // wrong
            die (json_encode(array(
                'METHOD'       => 'NOTENCRYPTED',
                'RESULT'       => bfReply::ERROR,
                'NOTENCRYPTED' => array(
                    'msg' => 'The connector you have installed currently on your site doesnt match the last one we generated for this site, and thus the encryption certificates are invalid and we cannot encrypt/decrypt data with them - you need to delete this site from myJoomla.com and start the connection process again from scratch, making sure you install the exact connector generated in the process, as each is unique.'
                )
            )));
        }
    }

    /**
     * Output the json with encrypted params
     *
     * @param CONST|string $result from the bfReply:: namespace
     * @param string $msg Normally JSON
     */
    static public function reply($result = 'NOT_SET', $msg = 'NOT_SET')
    {
        if ($result === bfReply::ERROR) {
            bfLog::log('ERROR = ' . json_encode($msg));
        }

        // remove any stray output
        echo ' '; // must have something to clean else warning occurs
        $contents = ob_get_contents();

        if (trim($contents)) {
            bfLog::log('Buffer Contents Found:  ' . $contents);
        }

        // tmp debug the buffer
        if (TRUE === _BF_API_DEBUG && $contents) {
            bfLog::log('WE HAVE AN OUTPUT BUFFER - Saving to file for debugging');
            file_put_contents(dirname(__FILE__) . '/tmp/tmp.ob', $contents);
        }

        ob_clean();
        // ahhh nice and clean again

        $returnJson = new stdClass ();
        $returnJson->METHOD = 'Encrypted'; // This is NOT encrypted
        $returnJson->RESULT = $result; // This is NOT encrypted

        // This is encrypted
        $returnJson->ENCRYPTED = bfEncrypt::getEncrypted($msg);

        // This is NOT encrypted
        $returnJson->CLIENT_VER = file_get_contents('./VERSION');

        /**
         * DO NOT ENABLE DEBUG THIS - It will mean that replies are sent as
         * encrypted AND non-encrypted
         * and so this is insecure (albeit very useful during development!)
         */
        $isLocalDevelopmentServer = (defined('APPLICATION_ENV') && (APPLICATION_ENV == 'development' || APPLICATION_ENV == 'local') ? TRUE : FALSE);
        if ($isLocalDevelopmentServer || TRUE === _BF_API_DEBUG && TRUE === _BF_API_REPLY_DEBUG_NEVER_ENABLE_THIS_EVER_WILL_LEAK_CONFIDENTIAL_INFO_IN_RESPONSES) {
            $returnJson->DEBUG = json_encode($msg);
        }

        bfLog::log('Returning encrypted status to server');
        die(json_encode($returnJson));
    }

    /**
     * Encrypt a string using the RC4 Key provided in the encrypted request
     * from the service backend
     *
     * @param string $msg
     *
     * @return string Base64encoded message
     */
    static public function getEncrypted($msg)
    {
        $start = time();
        bfLog::log('Starting Encryption....');
        // check our msg is a string
        if (is_object($msg) || is_array($msg)) {
            $msg = json_encode($msg);
        }

        // init a RC4 encryption routine - MUCH faster than public/private key
        $rc4 = new Crypt_RC4 ();

        if (!defined('RC4_KEY')) {
            bfLog::log('NO RC4_KEY FOUND!!');
            die ('No Encryption Key');
        }

        // Use the one time encryption key the requester provided
        $rc4->setKey(RC4_KEY);

        // encrypt the data
        $encrypted = $rc4->encrypt($msg);

        // return the data, encoded just in case
        $str = base64_encode($encrypted);
        bfLog::log('Finished Encryption.... took ' . (time() - $start) . ' seconds');

        return $str;
    }
}
