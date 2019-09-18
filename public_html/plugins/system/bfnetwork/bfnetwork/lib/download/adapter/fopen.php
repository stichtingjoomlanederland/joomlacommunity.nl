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
 * A download adapter using URL fopen() wrappers.
 */
class AcuDownloadAdapterFopen extends AcuDownloadAdapterAbstract implements AcuDownloadInterface
{
    public function __construct()
    {
        $this->priority              = 100;
        $this->supportsFileSize      = false;
        $this->supportsChunkDownload = true;
        $this->name                  = 'fopen';

        // If we are not allowed to use ini_get, we assume that URL fopen is
        // disabled.
        if (!function_exists('ini_get')) {
            $this->isSupported = false;
        } else {
            $this->isSupported = ini_get('allow_url_fopen');
        }
    }

    /**
     * Download a part (or the whole) of a remote URL and return the downloaded
     * data. You are supposed to check the size of the returned data. If it's
     * smaller than what you expected you've reached end of file. If it's empty
     * you have tried reading past EOF. If it's larger than what you expected
     * the server doesn't support chunk downloads.
     *
     * If this class' supportsChunkDownload returns false you should assume
     * that the $from and $to parameters will be ignored.
     *
     * @param string $url  The remote file's URL
     * @param int    $from Byte range to start downloading from. Use null for start of file.
     * @param int    $to   Byte range to stop downloading. Use null to download the entire file ($from is ignored)
     *
     * @return string the raw file data retrieved from the remote URL
     *
     * @throws Exception A generic exception is thrown on error
     */
    public function downloadAndReturn($url, $from = null, $to = null)
    {
        if (empty($from)) {
            $from = 0;
        }

        if (empty($to)) {
            $to = 0;
        }

        if ($to < $from) {
            $temp = $to;
            $to   = $from;
            $from = $temp;
            unset($temp);
        }

        if (!(empty($from) && empty($to))) {
            $options = array(
                'http' => array(
                    'method' => 'GET',
                    'header' => "Range: bytes=$from-$to\r\n",
                ), 'ssl' => array(
                    'verify_peer'      => false, // FFS!!! CRAP SERVERS
                    'verify_peer_name' => false, // FFS!!! CRAP SERVERS
                ),
            );
            $context = stream_context_create($options);
            $result  = @file_get_contents($url, false, $context, $from - $to + 1);
        } else {
            $options = array(
                'http' => array(
                    'method' => 'GET',
                ), 'ssl' => array(
                    'verify_peer'      => false, // FFS!!! CRAP SERVERS
                    'verify_peer_name' => false, // FFS!!! CRAP SERVERS
                ),
            );
            $context = stream_context_create($options);
            $result  = @file_get_contents($url, false, $context);
        }

        if (false === $result) {
            $error = JText::sprintf('COM_CMSUPDATE_ERR_LIB_FOPEN_ERROR');
            throw new Exception($error, 1);
        } else {
            return $result;
        }
    }
}
