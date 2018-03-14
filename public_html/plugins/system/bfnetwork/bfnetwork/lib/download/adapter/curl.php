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

/**
 * A download adapter using the cURL PHP integration
 */
class AcuDownloadAdapterCurl extends AcuDownloadAdapterAbstract implements AcuDownloadInterface
{
    public function __construct()
    {
        $this->priority              = 110;
        $this->supportsFileSize      = TRUE;
        $this->supportsChunkDownload = TRUE;
        $this->name                  = 'c' . 'u' . 'r' . 'l';
        $this->isSupported           = function_exists('c' . 'u' . 'r' . 'l' . '_init') && function_exists('c' . 'u' . 'r' . 'l' . '_exec') && function_exists('c' . 'u' . 'r' . 'l' . '_close');
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
     * @param   string  $url  The remote file's URL
     * @param   integer $from Byte range to start downloading from. Use null for start of file.
     * @param   integer $to   Byte range to stop downloading. Use null to download the entire file ($from is ignored)
     *
     * @return  string  The raw file data retrieved from the remote URL.
     *
     * @throws  Exception  A generic exception is thrown on error
     */
    public function downloadAndReturn($url, $from = NULL, $to = NULL, $nofollow = FALSE)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

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
            curl_setopt($ch, CURLOPT_RANGE, "$from-$to");
        }

        if (!@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1) && !$nofollow) {
            // Safe Mode is enabled. We have to fetch the headers and
            // parse any redirections present in there.
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            // Get the headers.
            $data = curl_exec($ch);
            curl_close($ch);

            // Init
            $newURL = $url;

            // Parse the headers.
            $lines = explode("\n", $data);

            foreach ($lines as $line) {
                if (substr($line, 0, 9) == "Location:") {
                    $newURL = trim(substr($line, 9));
                }
            }

            if ($url != $newURL) {
                return $this->downloadAndReturn($newURL);
            } else {
                return $this->downloadAndReturn($newURL, NULL, NULL, TRUE);
            }
        } else {
            @curl_setopt($ch, CURLOPT_MAXREDIRS, 20);

            if (function_exists('set_time_limit')) {
                set_time_limit(0);
            }
        }


        $result = curl_exec($ch);

        $errno       = curl_errno($ch);
        $errmsg      = curl_error($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($result === FALSE) {
            $error = JText::sprintf('COM_CMSUPDATE_ERR_LIB_' . 'C' . 'U' . 'R' . 'L' . '_ERROR' . $errmsg, $errno, $errmsg);
        } elseif ($http_status > 299) {
            $result = FALSE;
            $errno  = $http_status;
            $error  = JText::sprintf('COM_CMSUPDATE_ERR_LIB_HTTPERROR', $http_status);
        }

        curl_close($ch);

        if ($result === FALSE) {
            throw new Exception($error, $errno);
        } else {
            return $result;
        }
    }

    /**
     * Get the size of a remote file in bytes
     *
     * @param   string $url The remote file's URL
     *
     * @return  integer  The file size, or -1 if the remote server doesn't support this feature
     */
    public function getFileSize($url)
    {
        $result = -1;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $data = curl_exec($ch);
        curl_close($ch);

        if ($data) {
            $content_length = "unknown";
            $status         = "unknown";

            if (preg_match("/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches)) {
                $status = (int)$matches[1];
            }

            if (preg_match("/Content-Length: (\d+)/", $data, $matches)) {
                $content_length = (int)$matches[1];
            }

            if ($status == 200 || ($status > 300 && $status <= 308)) {
                $result = $content_length;
            }
        }

        return $result;
    }
}
