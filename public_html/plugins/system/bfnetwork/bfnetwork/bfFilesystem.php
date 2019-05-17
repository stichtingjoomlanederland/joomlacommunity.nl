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

class Bf_Filesystem
{
    /**
     * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
     * @license   GNU General Public License version 3, or later
     *
     * @param string $file
     * @param string $buffer
     *
     * @return bool
     */
    public static function _write($file, $buffer)
    {
        // Initialize variables
        jimport('joomla.client.helper');
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.path');

        $FTPOptions = JClientHelper::getCredentials('ftp');

        // If the destination directory doesn't exist we need to create it
        if (!file_exists(dirname($file))) {
            JFolder::create(dirname($file));
        }

        if (1 == $FTPOptions['enabled']) {
            // Connect the FTP client
            jimport('joomla.client.ftp');
            if (version_compare(JVERSION, '3.0', 'ge')) {
                $ftp = JClientFTP::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);
            } else {
                $ftp = JFTP::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);
            }

            // Translate path for the FTP account and use FTP write buffer to
            // file
            $file = JPath::clean(str_replace(JPATH_ROOT, $FTPOptions['root'], $file), '/');
            $ret  = $ftp->write($file, $buffer);
        } else {
            if (!is_writable($file)) {
                chmod($file, 0755);
            }
            if (!is_writable($file)) {
                chmod($file, 0777);
            }
            $ret = @file_put_contents($file, $buffer);
            chmod($file, 0644);
        }
        if (!$ret) {
            jimport('joomla.filesystem.file');
            JFile::write($file, $buffer);
        }

        return $ret;
    }

    /**
     * @param        $path
     * @param string $filter
     * @param bool   $recurse
     * @param bool   $fullpath
     *
     * @return array
     */
    public static function readDirectory($path, $filter = '.', $recurse = false,
                                  $fullpath = false)
    {
        $arr = array();
        if (!@is_dir($path)) {
            return $arr;
        }
        $handle = opendir($path);

        while ($file = readdir($handle)) {
            $dir   = Bf_Filesystem::pathName($path.'/'.$file, false);
            $isDir = is_dir($dir);
            if (('.' != $file) && ('..' != $file)) {
                if (preg_match("/$filter/", $file)) {
                    if ($fullpath) {
                        $arr[] = trim(
                            Bf_Filesystem::pathName($path.'/'.$file, false));
                    } else {
                        $arr[] = trim($file);
                    }
                }
                if ($recurse && $isDir) {
                    $arr2 = Bf_Filesystem::readDirectory($dir, $filter,
                                                         $recurse, $fullpath);
                    $arr = array_merge($arr, $arr2);
                }
            }
        }
        closedir($handle);
        asort($arr);

        return $arr;
    }

    /**
     * @param      $p_path
     * @param bool $p_addtrailingslash
     *
     * @return mixed|string
     */
    public static function pathName($p_path, $p_addtrailingslash = true)
    {
        $retval = '';

        $isWin = ('WIN' == substr(PHP_OS, 0, 3));

        if ($isWin) {
            $retval = str_replace('/', '\\', $p_path);
            if ($p_addtrailingslash) {
                if ('\\' != substr($retval, -1)) {
                    $retval .= '\\';
                }
            }

            // Check if UNC path
            $unc = '\\\\' == substr($retval, 0, 2) ? 1 : 0;

            // Remove double \\
            $retval = str_replace('\\\\', '\\', $retval);

            // If UNC path, we have to add one \ in front or everything breaks!
            if (1 == $unc) {
                $retval = '\\'.$retval;
            }
        } else {
            $retval = str_replace('\\', '/', $p_path);
            if ($p_addtrailingslash) {
                if ('/' != substr($retval, -1)) {
                    $retval .= '/';
                }
            }

            // Check if UNC path
            $unc = '//' == substr($retval, 0, 2) ? 1 : 0;

            // Remove double //
            $retval = str_replace('//', '/', $retval);

            // If UNC path, we have to add one / in front or everything breaks!
            if (1 == $unc) {
                $retval = '/'.$retval;
            }
        }

        return $retval;
    }

    /**
     * returns the directories in the path
     * if append path is set then this path will appended to the results.
     *
     * @param string      $path
     * @param bool|string $appendPath
     *
     * @return array
     */
    public static function getDirectories($path, $appendPath = false)
    {
        if (is_dir($path)) {
            $contents = scandir($path); //open directory and get contents
            if (is_array($contents)) { //it found files
                $returnDirs = false;
                foreach ($contents as $dir) {
                    //validate that this is a directory
                    if (is_dir($path.'/'.$dir) &&
                        '.' != $dir && '..' != $dir && '.svn' != $dir
                    ) {
                        $returnDirs[] = $appendPath.$dir;
                    }
                }

                if ($returnDirs) {
                    return $returnDirs;
                }
            }
        }
    }

    /**
     * adds a complete directory path
     * eg: /my/own/path
     * will create
     * >my
     * >>own
     * >>>path.
     *
     * @param string $base
     * @param string $path
     *
     * @return bool
     */
    public static function makeRecursive($base, $path)
    {
        $pathArray = explode('/', $path);
        if (is_array($pathArray)) {
            $strPath = null;
            foreach ($pathArray as $path) {
                if (!empty($path)) {
                    $strPath .= '/'.$path;
                    if (!is_dir($base.$strPath)) {
                        if (!self::make($base.$strPath)) {
                            return false;
                        }
                    }
                }
            }

            return true;
        }
    }

    /**
     * this is getting a little extreme i know
     * but it will help out later when we want to keep updated indexes
     * for right now, not much.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function make($path)
    {
        return mkdir($path, 0777);
    }

    /**
     * deletes a directory recursively
     * Bf_Filesystem::deleteRecursive(JPATH_ROOT .'/tmp', TRUE);.
     *
     * @param string $target
     * @param bool   $ignoreWarnings
     * @param array  $msg
     *
     * @return bool
     */
    public static function deleteRecursive($target, $ignoreWarnings = false, $msg = array())
    {
        $exceptions = array('.', '..');
        if (!$sourceDir = @opendir($target)) {
            if (true !== $ignoreWarnings) {
                $msg['result']   = 'failure';
                $msg['errors'][] = $target;

                return $msg;
            }
        }

        if (!$sourceDir) {
            return;
        }

        while (false !== ($sibling = readdir($sourceDir))) {
            if (!in_array($sibling, $exceptions)) {
                $object = str_replace('//', '/', $target.'/'.$sibling);
                if (is_dir($object)) {
                    $msg = Bf_Filesystem::deleteRecursive($object,
                                                          $ignoreWarnings, $msg);
                }

                if (is_file($object)) {
                    bfLog::log('Deleting '.$object);

                    $result = unlink($object);
                    if ($result) {
                        $msg['deleted_files'][] = $object;
                    }
                    if (!$result) {
                        $msg['errors'][] = $object;
                    }
                }
            }
        }

        closedir($sourceDir);

        if (is_dir($target)) {
            bfLog::log('Deleting '.$target);

            if ($result = rmdir($target)) {
                $msg['deleted_folders'][] = $target;
                $msg['result']            = 'success';
            } else {
                bfLog::log(sprintf('Deleting %S FAILED', $target));
                $msg['result'] = 'failure';
            }
        } else {
        }

        return $msg;
    }
}
