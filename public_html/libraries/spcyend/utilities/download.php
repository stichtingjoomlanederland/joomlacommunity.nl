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
 * @since       1.1.0
 */
class CYENDDownload {

    /**
     * Constructor.
     *
     * @since   1.1.0
     *
     */
    public function __construct() {
        JFactory::getLanguage()->load('lib_spcyend', JPATH_SITE); //Load library language
    }

    /**
     * Method to force file download 
     *
     * @param   string  $filename    Full file local path
     *
     * @return  bolean  True success, or false in failure
     *
     * @since   1.1.0
     */
    public function forceDownload($filename) {
        // required for IE, otherwise Content-disposition is ignored
        if (ini_get('zlib.output_compression'))
            ini_set('zlib.output_compression', 'Off');

        // addition by Jorg Weske
        $file_extension = strtolower(substr(strrchr($filename, "."), 1));

        if ($filename == "") {
            JError::raiseError(403, JText::_('File not defined'));
            return false;
        } elseif (!file_exists($filename)) {
            JError::raiseError(403, JText::_('File not found'));
            return false;
        };


        $ctype = $this->mimeTypes($file_extension);

        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false); // required for certain browsers
        header("Content-Type: $ctype");
        header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($filename));
        ob_clean();
        flush();
        //readfile($filename);
        $this->readfile_chunked("$filename");
        exit;
    }

    /**
     * Method to return mime type
     *
     * @param   string  $ext    the file extension
     *
     * @return  string  The mime type
     *
     * @since   1.1.0
     */
    public function mimeTypes($ext) {
        $file = JPATH_BASE . "/media/spcyend/various/mime.types";
        if (!is_file($file) || !is_readable($file))
            return false;
        $types = array();
        $fp = fopen($file, "r");
        while (false != ($line = fgets($fp, 4096))) {
            if (!preg_match("/^\s*(?!#)\s*(\S+)\s+(?=\S)(.+)/", $line, $match))
                continue;
            $tmp = preg_split("/\s/", trim($match[2]));
            foreach ($tmp as $type)
                $types[strtolower($type)] = $match[1];
        }
        fclose($fp);
        $ctype = "application/force-download";
        if (isset($types[$ext]))
            $ctype = $types[$ext];

        return $ctype;
    }

    /**
     * Method to read file in chunks
     *
     * @param   string  $filename    Full path file name
     *
     * @return  boolean  True or False
     *
     * @since   1.1.0
     */
    public function readfile_chunked($filename, $retbytes = true) {
        $chunksize = 1 * (1024 * 1024); // how many bytes per chunk
        $buffer = '';
        $cnt = 0;
        // $handle = fopen($filename, 'rb');
        $handle = fopen($filename, 'rb');
        if ($handle === false) {
            return false;
        }
        while (!feof($handle)) {
            $buffer = fread($handle, $chunksize);
            echo $buffer;
            ob_flush();
            flush();
            if ($retbytes) {
                $cnt += strlen($buffer);
            }
        }
        $status = fclose($handle);
        if ($retbytes && $status) {
            return $cnt; // return num. bytes delivered like readfile() does.
        }
        return $status;
    }

}
