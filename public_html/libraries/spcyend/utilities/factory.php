<?php

/**
 * @package		SP Libraries
 * @subpackage	Utilites
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		http://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('JPATH_PLATFORM') or die;

/**
 * SPGeneral is a class with various frequent used functions
 *
 * @package     spcyend.utilities.factory
 * @subpackage  Utilities
 * @since       1.0.0
 */
class CYENDFactory {

    /**
     * Constructor.
     *
     * @since   1.0.0
     *
     */
    public function __construct() {
        JFactory::getLanguage()->load('lib_spcyend', JPATH_SITE); //Load library language
    }

    /**
     * Method to get a model object, loading it if required.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  object  The model.
     *
     * @since   1.0.0
     */
    public static function getModel($name = '', $prefix = '', $config = array()) {
        return JModelLegacy::getInstance($name, $prefix, array('ignore_request' => true));
    }

    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $name     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $options  Configuration array for model. Optional.
     *
     * @return  JTable  A JTable object
     *
     * @since   1.0.0
     */
    public static function getTable($name = '', $prefix = 'JTable', $options = array()) {
        return JTable::getInstance($name, $prefix, $options);
    }

    /**
     * Method to write to a log file
     *
     * @param   string  $message    The message to write
     * @param   string  $mode       Define is new file 'w'. Optional.
     * @param   array   $fileName   Log full path file name. Optional.
     *
     * @return  boolean True if success, false if failure
     *
     * @since   1.0.0
     */
    public static function writeLog($message, $mode = 'a', $fileName = null) {
        if (is_null($fileName))
            $fileName = JPATH_COMPONENT_ADMINISTRATOR . '/log.htm';
        $handle = fopen($fileName, $mode);
        if ($handle) {
            fwrite($handle, $message);
            fflush($handle);
            fclose($handle);
        }
        return true;
    }

    /**
     * Method for debuggin. Write a variable, or message on screen.
     *
     * @param   string  $msg    The message to write
     *
     * @return  string  The message
     *
     * @since   1.0.0
     */
    public static function print_r($msg) {
        $return = '<pre>' . print_r($msg, true) . '</pre>';
        echo $return;
        return $return;
    }

    /**
     * Enqueue a system message.
     *
     * @param   string  $msg   The message to enqueue.
     * @param   string  $type  The message type. Default is message.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public static function enqueueMessage($msg, $type = 'message') {
        JFactory::getApplication()->enqueueMessage($msg, $type);
    }

    /**
     * Get current component name
     *
     * @return  string
     *
     * @since   1.0.0
     */
    public static function getComponentName() {
        return JRequest::getCmd('option');
    }

    /**
     * Execute an SQL query
     *
     * @param JDatabase $db The database object to query
     * 
     * @return  boolean, True succes, or False for failure
     *
     * @since   2.0.0
     */
    public static function execute($db) {
        try {
            $db->execute();
        } catch (RuntimeException $e) {
            //JError::raiseWarning(500, $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Get current url
     *
     * @return  string
     *
     * @since   1.0.0
     */
    public static function getCurrentUrl() {
        $pageURL = 'http';
        if (@$_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /**
     * Encrypt a string bassed on a salt keyword
     *
     * @return  string
     *
     * @since   3.1.1
     */
    public static function encrypt($decrypted, $salt) {
        // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
        $key = hash('SHA256', $salt, true);
        // Build $iv and $iv_base64.  We use a block size of 128 bits (AES compliant) and CBC mode.  (Note: ECB mode is inadequate as IV is not used.)
        srand();
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
        if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22)
            return false;
        // Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
        // We're done!
        return $iv_base64 . $encrypted;
    }

    /**
     * decryt a string bassed on a salt keyword
     *
     * @return  string
     *
     * @since   3.1.1
     */
    public static function decrypt($encrypted, $salt) {
        // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
        $key = hash('SHA256', $salt, true);
        // Retrieve $iv which is the first 22 characters plus ==, base64_decoded.
        $iv = base64_decode(substr($encrypted, 0, 22) . '==');
        // Remove $iv from $encrypted.
        $encrypted = substr($encrypted, 22);
        // Decrypt the data.  rtrim won't corrupt the data because the last 32 characters are the md5 hash; thus any \0 character has to be padding.
        if (!($decrypted = @rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4")))
            return false;
        // Retrieve $hash which is the last 32 characters of $decrypted.
        $hash = substr($decrypted, -32);
        // Remove the last 32 characters from $decrypted.
        $decrypted = substr($decrypted, 0, -32);
        // Integrity check.  If this fails, either the data is corrupted, or the password/salt was incorrect.
        if (md5($decrypted) != $hash)
            return false;
        // Yay!
        return $decrypted;
    }

    /**
     * Fixes a string to be able to be json_decoded
     * 
     * @param string $input
     * @return string
     */
    public static function json_decode_fix($input) {
        //strip html tags
        $result0 = strip_tags($input);

        //ms-windows characters
        $search = array(
            0 => "″",
            1 => "”",
            2 => "“"
        );

        $result1 = str_replace($search, "\"", $result0);
        $result2 = iconv('UTF-8', 'ASCII//TRANSLIT', $result1);

        $result = utf8_encode($result2);

        return $result;
    }
    
    /**
     * Return in array browser name, version
     * 
     * @return array Browser elements
     */
    public static function getBrowser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }

}
