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
class AcuDownload
{
    /**
     * Parameters passed from the GUI when importing from URL
     *
     * @var  array
     */
    private $params = array();

    /**
     * The download adapter which will be used by this class
     *
     * @var  AcuDownloadInterface
     */
    private $adapter = NULL;

    public function __construct()
    {
        // Find the best fitting adapter
        $allAdapters = AcuDownload::getFiles(dirname(__FILE__) . '/adapter', array(), array('abstract.php'));
        $priority    = 0;

        foreach ($allAdapters as $adapterInfo) {
            $adapter = new $adapterInfo['classname'];

            if (!$adapter->isSupported()) {
                continue;
            }

            if ($adapter->priority > $priority) {
                $this->adapter = $adapter;
                $priority      = $adapter->priority;
            }
        }
    }

    /**
     * Forces the use of a specific adapter
     *
     * @param  $className  The name of the class or the name of the adapter, e.g. 'AcuDownloadAdapterCurl' or 'curl'
     */
    public function setAdapter($className)
    {
        $adapter = NULL;

        if (class_exists($className, TRUE)) {
            $adapter = new $className;
        } elseif (class_exists('AcuDownloadAdapter' . ucfirst($className))) {
            $className = 'AcuDownloadAdapter' . ucfirst($className);
            $adapter   = new $className;
        }

        if (is_object($adapter) && ($adapter instanceof AcuDownloadInterface)) {
            $this->adapter = $adapter;
        }
    }

    /**
     * Used to decode the $params array
     *
     * @param   string $key     The parameter key you want to retrieve the value for
     * @param   mixed  $default The default value, if none is specified
     *
     * @return  mixed  The value for this parameter key
     */
    private function getParam($key, $default = NULL)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        } else {
            return $default;
        }
    }

    /**
     * Download data from a URL and return it
     *
     * @param   string $url The URL to download from
     *
     * @return  bool|string  The downloaded data or false on failure
     */
    public function getFromURL($url)
    {
        try {
            return $this->adapter->downloadAndReturn($url);
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * Performs the staggered download of file.
     *
     * @param   array $params A parameters array, as sent by the user interface
     *
     * @return  array  A return status array
     */
    public function importFromURL($params)
    {
        $this->params = $params;

        // Fetch data
        $filename    = $this->getParam('file');
        $frag        = $this->getParam('frag', -1);
        $totalSize   = $this->getParam('totalSize', -1);
        $doneSize    = $this->getParam('doneSize', -1);
        $maxExecTime = $this->getParam('maxExecTime', 5);
        $runTimeBias = $this->getParam('runTimeBias', 75);
        $minExecTime = $this->getParam('minExecTime', 1);

        $localFilename = 'myjoomla-upgradefile.zip';

        // This would have been //JFactory::getConfig()->get('tmp_path', JPATH_ROOT . '/tmp');
        $tmpDir = dirname(__FILE__) . '/../../tmp';
        $tmpDir = rtrim($tmpDir, '/\\');

        /**
         * debugMsg('Importing from URL');
         * debugMsg('  file      : ' . $filename);
         * debugMsg('  frag      : ' . $frag);
         * debugMsg('  totalSize : ' . $totalSize);
         * debugMsg('  doneSize  : ' . $doneSize);
         * /**/

        // Init retArray
        $retArray = array(
            "status"    => TRUE,
            "error"     => '',
            "frag"      => $frag,
            "totalSize" => $totalSize,
            "doneSize"  => $doneSize,
            "percent"   => 0,
        );

        try {
            $timerParameters = array(
                'min_exec_time' => $minExecTime,
                'max_exec_time' => $maxExecTime,
                'run_time_bias' => $runTimeBias,
            );
            $timer           = new AcuTimer($timerParameters);
            $start           = $timer->getRunningTime(); // Mark the start of this download
            $break           = FALSE; // Don't break the step

            // Figure out where on Earth to put that file
            $local_file = $tmpDir . '/' . $localFilename;

            //debugMsg("- Importing from $filename");

            while (($timer->getTimeLeft() > 0) && !$break) {
                // Do we have to initialize the file?
                if ($frag == -1) {
                    //debugMsg("-- First frag, killing local file");
                    // Currently downloaded size
                    $doneSize = 0;

                    if (@file_exists($local_file)) {
                        @unlink($local_file);
                    }

                    // Delete and touch the output file
                    $fp = @fopen($local_file, 'wb');

                    if ($fp !== FALSE) {
                        @fclose($fp);
                    }

                    // Init
                    $frag = 0;

                    //debugMsg("-- First frag, getting the file size");
                    $retArray['totalSize'] = $this->adapter->getFileSize($filename);
                    $totalSize             = $retArray['totalSize'];
                }

                // Calculate from and length
                $length = 1048576;
                $from   = $frag * $length;
                $to     = $length + $from - 1;

                // Try to download the first frag
                $required_time = 1.0;
                //debugMsg("-- Importing frag $frag, byte position from/to: $from / $to");

                try {
                    $result = $this->adapter->downloadAndReturn($filename, $from, $to);

                    if ($result === FALSE) {
                        throw new Exception(JText::sprintf('COM_CMSUPDATE_ERR_LIB_COULDNOTDOWNLOADFROMURL', $filename), 500);
                    }
                } catch (Exception $e) {
                    $result = FALSE;
                    $error  = $e->getMessage();
                }

                if ($result === FALSE) {
                    // Failed download
                    if ($frag == 0) {
                        // Failure to download first frag = failure to download. Period.
                        $retArray['status'] = FALSE;
                        $retArray['error']  = $error;

                        //debugMsg("-- Download FAILED");

                        return $retArray;
                    } else {
                        // Since this is a staggered download, consider this normal and finish
                        $frag = -1;
                        //debugMsg("-- Import complete");
                        $totalSize = $doneSize;
                        $break     = TRUE;
                    }
                }

                // Add the currently downloaded frag to the total size of downloaded files
                if ($result) {
                    $filesize = strlen($result);
                    //debugMsg("-- Successful download of $filesize bytes");
                    $doneSize += $filesize;

                    // Append the file
                    $fp = @fopen($local_file, 'ab');

                    if ($fp === FALSE) {
                        //debugMsg("-- Can't open local file $local_file for writing");
                        // Can't open the file for writing
                        $retArray['status'] = FALSE;
                        $retArray['error']  = JText::sprintf('COM_CMSUPDATE_ERR_LIB_COULDNOTWRITELOCALFILE', $local_file);

                        return $retArray;
                    }

                    fwrite($fp, $result);
                    fclose($fp);

                    //debugMsg("-- Appended data to local file $local_file");

                    $frag++;

                    //debugMsg("-- Proceeding to next fragment, frag $frag");

                    if (($filesize < $length) || ($filesize > $length)) {
                        // A partial download or a download larger than the frag size means we are done
                        $frag = -1;
                        //debugMsg("-- Import complete (partial download of last frag)");
                        $totalSize = $doneSize;
                        $break     = TRUE;
                    }
                }

                // Advance the frag pointer and mark the end
                $end = $timer->getRunningTime();

                // Do we predict that we have enough time?
                $required_time = max(1.1 * ($end - $start), $required_time);

                if ($required_time > (10 - $end + $start)) {
                    $break = TRUE;
                }

                $start = $end;
            }

            if ($frag == -1) {
                $percent = 100;
            } elseif ($doneSize <= 0) {
                $percent = 0;
            } else {
                if ($totalSize > 0) {
                    $percent = 100 * ($doneSize / $totalSize);
                } else {
                    $percent = 0;
                }
            }

            // Update $retArray
            $retArray = array(
                "status"    => TRUE,
                "error"     => '',
                "frag"      => $frag,
                "totalSize" => $totalSize,
                "doneSize"  => $doneSize,
                "percent"   => $percent,
            );
        } catch (Exception $e) {
            //debugMsg("EXCEPTION RAISED:");
            //debugMsg($e->getMessage());
            $retArray['status'] = FALSE;
            $retArray['error']  = $e->getMessage();
        }

        return $retArray;
    }

    /**
     * This method will crawl a starting directory and get all the valid files
     * that will be analyzed by __construct. Then it organizes them into an
     * associative array.
     *
     * @param   string $path          Folder where we should start looking
     * @param   array  $ignoreFolders Folder ignore list
     * @param   array  $ignoreFiles   File ignore list
     *
     * @return  array   Associative array, where the `fullpath` key contains the path to the file,
     *                  and the `classname` key contains the name of the class
     */
    protected static function getFiles($path, array $ignoreFolders = array(), array $ignoreFiles = array())
    {
        $return = array();

        $files = self::scanDirectory($path, $ignoreFolders, $ignoreFiles);

        // Ok, I got the files, now I have to organize them
        foreach ($files as $file) {
            $clean = str_replace($path, '', $file);
            $clean = trim(str_replace('\\', '/', $clean), '/');

            $parts = explode('/', $clean);

            $return[] = array(
                'fullpath'  => $file,
                'classname' => 'AcuDownloadAdapter' . ucfirst(basename($parts[0], '.php'))
            );
        }

        return $return;
    }

    /**
     * Recursive function that will scan every directory unless it's in the
     * ignore list. Files that aren't in the ignore list are returned.
     *
     * @param   string $path          Folder where we should start looking
     * @param   array  $ignoreFolders Folder ignore list
     * @param   array  $ignoreFiles   File ignore list
     *
     * @return  array   List of all the files
     */
    protected static function scanDirectory($path, array $ignoreFolders = array(), array $ignoreFiles = array())
    {
        $return = array();

        $handle = @opendir($path);

        if (!$handle) {
            return $return;
        }

        while (($file = readdir($handle)) !== FALSE) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $fullpath = $path . '/' . $file;

            if ((is_dir($fullpath) && in_array($file, $ignoreFolders)) || (is_file($fullpath) && in_array($file, $ignoreFiles))) {
                continue;
            }

            if (is_dir($fullpath)) {
                $return = array_merge(self::scanDirectory($fullpath, $ignoreFolders, $ignoreFiles), $return);
            } else {
                $return[] = $path . '/' . $file;
            }
        }

        return $return;
    }
}
