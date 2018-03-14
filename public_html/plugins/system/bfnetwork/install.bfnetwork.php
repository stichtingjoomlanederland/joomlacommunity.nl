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
defined('_JEXEC') or die();

class plgsystembfnetworkInstallerScript
{
    /**
     * @param $type
     * @param $parent
     *
     * @return bool
     */
    public function preflight($type, $parent)
    {
        return TRUE;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return TRUE;
    }

    /**
     * Pass request to our abstracted out function
     *
     * @param $parent
     *
     * @return bool
     */
    public function update($parent)
    {
        return TRUE;
    }

    /**
     * Pass request to our abstracted out function
     *
     * @param $parent
     */
    public function install($parent)
    {
        return $this->registerSiteToMyJoomla('install');
    }

    /**
     * Send the most basic information back to myJoomla service so
     * that we can register your site, after which we use a dedicated secure
     * connection through out connector.
     *
     * Some people call this "calling home" which has a negative reputation
     * However the whole premise of the SasS myJoomla.com is an active connection between
     * your site and our service so this is perfectly acceptable and lets us know your site
     * has a connector installed so we can continue with the installation/connection process
     *
     * @param $type string update|install
     */
    public function registerSiteToMyJoomla($type)
    {
        /**
         * Init some Joomla Classes we will need...
         * @todo Confirm these are available back to Joomla 1.5.0
         */
        $config  = JFactory::getConfig();
        $version = new JVersion ();

        // init our data holder...
        $data = new stdClass();

        // Is this an install or an update
        $data->type = $type;

        /**
         * Get the friendly name of the website
         * Bloody Joomla version issues here too...
         */
        if (method_exists($config, 'getValue')) { //Old Joomla Versions
            $data->friendlyname = $config->getValue('config.sitename');
        } else {
            $data->friendlyname = $config->get('sitename');
        }

        // Get Joomla's Site URL
        $data->siteurl = str_replace('/administrator/', '/', JURI::base());

        // get Joomla's version number
        $data->version = $version->getShortVersion();

        /**
         * Check for our version file in two locations, again Joomla being a pain and changing the paths in 2.5.0+
         */
        if (file_exists('../plugins/system/bfnetwork/VERSION')) {

            $data->connectorversion = file_get_contents('../plugins/system/bfnetwork/VERSION');

        } else if (file_exists('../plugins/system/bfnetwork/bfnetwork/VERSION')) {

            $data->connectorversion = file_get_contents('../plugins/system/bfnetwork/bfnetwork/VERSION');
        }

        /**
         * Get the has form the URL, crazy way to do it for maximum compatibility with
         * crappy servers!
         *
         * @todo test on all versions of Joomla
         */
        if (count($_FILES) && array_key_exists('install_package', $_FILES['install_package'])) { // Install by Zip file Upload
            $data->hash = trim(str_replace(array('connector_',
                                                 '(1)',
                                                 '(2)',
                                                 '(3)',
                                                 '(4)',
                                                 '(5)',
                                                 '.zip'), '', $_FILES['install_package']['name']));
        } else if (array_key_exists('install_url', $_POST)) { // Install by Install URL Pasted
            $data->hash = str_replace(array(
                                          'https://local-manage.myjoomla.com/register/site/connect/',
                                          'https://staging.myjoomla.com/register/site/connect/',
                                          'https://manage.myjoomla.com/register/site/connect/'
                                      ),
                                      '',
                                      $_POST['install_url']);
        } else {
            if (count($_FILES) && array_key_exists('install_package', $_FILES)) { // Install by Zip file Upload
                $data->hash = trim(str_replace(array('connector_',
                    '(1)',
                    '(2)',
                    '(3)',
                    '(4)',
                    '(5)',
                    '.zip'), '', $_FILES['install_package']['name']));

            }
        }

        /**
         *  If in local development which developing myJoomla.com services
         *  If developing we want to see what happens instead of having it happen in the background :)
         */
        if (getenv('APPLICATION_ENV') == 'local' && ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' OR $_SERVER['REMOTE_ADDR'] == '::1')) {

            $registerUrl = 'https://local-manage.myjoomla.com/register/site/yooohooo/';
            $url         = $registerUrl . base64_encode(json_encode($data));

            echo '<a href="' . $url . '">TEST</a>';
            echo json_encode($data);
            echo var_dump(str_replace(array('connector_',
                                            '(1)',
                                            '(2)',
                                            '(3)',
                                            '(4)',
                                            '(5)',
                                            '.zip'), '', $_FILES['install_package']['name']));
            die;
        } else {

            if (@file_exists('./bfnetwork/STAGING')) {
                $registerUrl = 'https://staging.myjoomla.com/register/site/yooohooo/';
            } else {
                $registerUrl = 'https://manage.myjoomla.com/register/site/yooohooo/';
            }

            $url = trim($registerUrl . base64_encode(json_encode($data)));


            $options = array(
                'http'=>array(
                    'method'=>"GET",
                    'header'=>"Accept-language: en\r\n" .
                        "User-Agent: ".$_SERVER['HTTP_HOST']."\r\n"
                )
            );

            $context = stream_context_create($options);

            // get the data from the request
            $ok = @file_get_contents($url, false, $context);

            /**
             * ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT **
             */
            if (!$ok) {

                $ch = curl_init();

                // Set up bare minimum CURL Options needed for myJoomla.com
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_HOST']);

                // Attempt to download using CURL and CURLOPT_SSL_VERIFYPEER set to TRUE
                $ok = curl_exec($ch);

                // Did we succeed in getting something?
                if (!$ok) {

                    /**
                     * ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT **
                     *
                     * Ok try without validation of the SSL (gulp) but this is needed on some servers without a pem file
                     * and we need to be compatible as possible - even on crappy webhosts when they need us most ;-(
                     */
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                    //  Second Attempt to download using CURL and CURLOPT_SSL_VERIFYPEER set to FALSE (gulp)
                    curl_exec($ch);
                }

                curl_close($ch);
            }

            if (!$ok) {
                echo 'We could not auto register to myJoomla.com so you will have to click the manual check button on the awaiting myJoomla.com connection screen';
            }
        }
    }

    /**
     * Pass request to our abstracted out function
     *
     * @param $type
     * @param $parent
     */
    public function postflight($type, $parent)
    {
        return $this->registerSiteToMyJoomla($type);
    }
}
