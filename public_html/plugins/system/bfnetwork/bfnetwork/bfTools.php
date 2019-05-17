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

require 'bfEncrypt.php';

/**
 * If we have got here then we have already passed through decrypting
 * the encrypted header and so we are sure we are now secure and no one
 * else cannot run the code below.
 *
 * I'M NOT PROUD OF THIS FILE - it has grown a lot over the years and there are a lot of workarounds so that we can be
 * fully compatible from Joomla 1.5.0 to the latest Joomla version, and on all the crazy configurations of webservers.
 */
final class bfTools
{
    /**
     * We pass the command to run as a simple integer in our encrypted
     * request this is mainly to speed up the decryption process, plus its a
     * single digit(or 2) rather than a huge string to remember :-).
     */
    private $_methods = array(
        1   => 'getCoreHashFailedFileList',
        2   => 'downloadfile',
        3   => 'restorefile',
        4   => 'getSuspectContentFileList',
        5   => 'deleteFile',
        6   => 'checkFTPLayer',
        7   => 'disableFTPLayer',
        8   => 'checkNewDBCredentials',
        9   => 'testDbCredentials',
        10  => 'getFolderPermissions',
        11  => 'setFolderPermissions',
        12  => 'getHiddenFolders',
        13  => 'deleteFolder',
        14  => 'getInstallationFolders',
        15  => 'getRecentlyModified',
        16  => 'getFilePermissions',
        17  => 'setFilePermissions',
        18  => 'getErrorLogs',
        19  => 'getEncrypted',
        20  => 'getUser',
        21  => 'setUser',
        22  => 'setDbPrefix',
        23  => 'setDbCredentials',
        24  => 'getBakTables',
        25  => 'deleteBakTables',
        26  => 'getHtaccessFiles',
        27  => 'setHtaccess',
        28  => 'getUpdatesCount',
        29  => 'getUpdatesDetail',
        30  => 'getDotfiles',
        31  => 'getArchivefiles',
        32  => 'getLargefiles',
        33  => 'fixDbSchema',
        34  => 'getDbSchemaVersion',
        35  => 'checkGoogleFile',
        36  => 'toggleOnline',
        37  => 'getOfflineStatus',
        38  => 'getRobotsFile',
        39  => 'saveRobotsFile',
        40  => 'getTmpfiles',
        41  => 'clearTmpFiles',
        42  => 'getFlufffiles',
        43  => 'clearFlufffiles',
        44  => 'getRenamedToHide',
        45  => 'getPhpinwrongplace',
        46  => 'doExtensionUpgrade',
        47  => 'toggleCache',
        48  => 'getCacheStatus',
        49  => 'checkAkeebaOutputDirectory',
        50  => 'eolsecuritystatus',
        51  => 'applyeolpatch',
        52  => 'getMailerFileList',
        53  => 'getUploaderFileList',
        54  => 'getNonCoreFileList',
        55  => 'saveFile',
        56  => 'getZerobyteFiles',
        57  => 'deleteZerobyteFiles',
        58  => 'getMissingCoreFiles',
        59  => 'restoreAllMissingFiles',
        60  => 'getJoomlaLogTmpConfig',
        61  => 'getActivityLog',
        62  => 'getBFPluginStatus',
        63  => 'getMD5PasswordUsers',
        64  => 'getSessionGCStatus',
        65  => 'setSessionGCStatus',
        66  => 'get2FAPlugins',
        67  => 'enable2FAPlugins',
        68  => 'setLogTmpPaths',
        69  => 'removeLiveSite',
        70  => 'getConfiguredLiveSite',
        71  => 'getSEFConfig',
        72  => 'setSEFConfig',
        73  => 'getAdminFilterFixed',
        74  => 'setAdminFilterFixed',
        75  => 'getPlaintextpasswords',
        76  => 'setPlaintextpasswords',
        77  => 'getUploadsettingsfixed',
        78  => 'setUploadsettingsfixed',
        79  => 'getMailtofrienddisabled',
        80  => 'setMailtofrienddisabled',
        81  => 'getDebugMode',
        82  => 'setDebugMode',
        83  => 'getErrorReporting',
        84  => 'setErrorReporting',
        85  => 'getTemplatePositionDisplay',
        86  => 'setTemplatePositionDisplay',
        87  => 'getCookieSettings',
        88  => 'setCookieSettings',
        89  => 'getSQLFiles',
        90  => 'getCaptchaConfig',
        91  => 'setCaptchaConfig',
        92  => 'doExtensionInstallFromUrl',
        93  => 'getSuperAdmins',
        94  => 'getGroups',
        95  => 'getUseractionlogenabled',
        96  => 'setUseractionlogenabled',
        97  => 'getPrivacyConsentPluginEnabled',
        98  => 'setPrivacyConsentPluginEnabled',
        99  => 'getUseractionlogiplogenabled',
        100 => 'setUseractionlogiplogenabled',
        101 => 'getSystemLogRotationEnabled',
        102 => 'setSystemLogRotationEnabled',
        103 => 'getPurge30Days',
        104 => 'setPurge30Days',
        105 => 'getGzip',
        106 => 'setGzip',
        107 => 'getSessionlifetime',
        108 => 'setSessionlifetime',
        109 => 'getPHPinifiles',
        110 => 'getModifiedfilessincelastaudit',
        111 => 'setAdminHtaccess',
        112 => 'getAdminHtaccess',
        113 => 'getUserRegistration',
        114 => 'setUserRegistration',
        115 => 'getPostInstallMessages',
    );

    private $fluffFiles = array(
        '/.drone.yml',
        '/robots.txt.dist',
        '/web.config.txt',
        '/joomla.xml',
        '/build.xml',
        '/LICENSE.txt',
        '/README.txt',
        '/htaccess.txt',
        '/LICENSES.php',
        '/configuration.php-dist',
        '/CHANGELOG.php',
        '/COPYRIGHT.php',
        '/CREDITS.php',
        '/INSTALL.php',
        '/LICENSE.php',
        '/CONTRIBUTING.md',
        '/phpunit.xml.dist',
        '/README.md',
        '/.travis.yml',
        '/travisci-phpunit.xml',
        '/images/banners/osmbanner1.png',
        '/images/banners/osmbanner2.png',
        '/images/banners/shop-ad-books.jpg',
        '/images/banners/shop-ad.jpg',
        '/images/banners/white.png',
        '/images/headers/blue-flower.jpg',
        '/images/headers/maple.jpg',
        '/images/headers/raindrops.jpg',
        '/images/headers/walden-pond.jpg',
        '/images/headers/windows.jpg',
        '/images/joomla_black.gif',
        '/images/joomla_black.png',
        '/images/joomla_green.gif',
        '/images/joomla_logo_black.jpg',
        '/images/powered_by.png',
        '/images/sampledata/fruitshop/apple.jpg',
        '/images/sampledata/fruitshop/bananas_2.jpg',
        '/images/sampledata/fruitshop/fruits.gif',
        '/images/sampledata/fruitshop/tamarind.jpg',
        '/images/sampledata/parks/animals/180px_koala_ag1.jpg',
        '/images/sampledata/parks/animals/180px_wobbegong.jpg',
        '/images/sampledata/parks/animals/200px_phyllopteryx_taeniolatus1.jpg',
        '/images/sampledata/parks/animals/220px_spottedquoll_2005_seanmcclean.jpg',
        '/images/sampledata/parks/animals/789px_spottedquoll_2005_seanmcclean.jpg',
        '/images/sampledata/parks/animals/800px_koala_ag1.jpg',
        '/images/sampledata/parks/animals/800px_phyllopteryx_taeniolatus1.jpg',
        '/images/sampledata/parks/animals/800px_wobbegong.jpg',
        '/images/sampledata/parks/banner_cradle.jpg',
        '/images/sampledata/parks/landscape/120px_pinnacles_western_australia.jpg',
        '/images/sampledata/parks/landscape/120px_rainforest_bluemountainsnsw.jpg',
        '/images/sampledata/parks/landscape/180px_ormiston_pound.jpg',
        '/images/sampledata/parks/landscape/250px_cradle_mountain_seen_from_barn_bluff.jpg',
        '/images/sampledata/parks/landscape/727px_rainforest_bluemountainsnsw.jpg',
        '/images/sampledata/parks/landscape/800px_cradle_mountain_seen_from_barn_bluff.jpg',
        '/images/sampledata/parks/landscape/800px_ormiston_pound.jpg',
        '/images/sampledata/parks/landscape/800px_pinnacles_western_australia.jpg',
        '/images/sampledata/parks/parks.gif',
    );

    /**
     * Pointer to the Joomla Database Object.
     *
     * @var JDatabaseMysql
     */
    private $_db;

    /**
     * Incoming decrypted vars from the request.
     *
     * @var stdClass
     */
    private $_dataObj;

    /**
     * PHP 5 Constructor,
     * I inject the request to the object.
     *
     * @param stdClass $dataObj
     */
    public function __construct($dataObj)
    {
        // init Joomla
        require 'bfInitJoomla.php';

        // Set the request vars
        $this->_dataObj = $dataObj;

        // set the db object
        $this->_db = JFactory::getDBO();
    }

    /**
     * I'm the controller - I run methods based on the request integer.
     */
    public function run()
    {
        if (property_exists($this->_dataObj, 'c')) {
            $c = (int) $this->_dataObj->c;
            if (array_key_exists($c, $this->_methods)) {
                bfLog::log('Calling methd '.$this->_methods[$c]);
                // call the right method
                $this->{$this->_methods[$c]} ();
            } else {
                // Die if an unknown function
                bfEncrypt::reply('error', 'No Such method #err1 - '.$c);
            }
        } else {
            // Die if an unknown function
            bfEncrypt::reply('error', 'No Such method #err2');
        }
    }

    /**
     * Get the post install messages from a Joomla 3+ site.
     */
    public function getPostInstallMessages()
    {
        // bail early if we cannot
        if (!file_exists(JPATH_LIBRARIES.'/fof/include.php')) {
            bfEncrypt::reply('success', array());
        }

        // fire up RAD/fof
        require_once JPATH_LIBRARIES.'/fof/include.php';
        $model = FOFModel::getTmpInstance('Messages', 'PostinstallModel');
        $items = $model->getItemList();

        // load language layer to translate strings
        $lang = JFactory::getLanguage();

        // ensure we only show valid messages
        $model->onProcessList($items);

        $messages = array();

        // translate and compile the messages
        foreach ($items as $item) {
            $lang->load($item->language_extension, JPATH_ADMINISTRATOR, 'en-GB', true);
            $messages[] = array(
                'title' => JText::_($item->title_key),
                'desc'  => JText::_($item->description_key),
            );
        }

        bfEncrypt::reply('success', $messages);
    }

    /**
     * 113
     * Get User Registration Enable/Disable status.
     */
    public function getUserRegistration()
    {
        bfEncrypt::reply('success', array('enabled' => (int) JComponentHelper::getParams('com_users')->get('allowUserRegistration')));
    }

    /**
     * 114
     * Enable User Registration.
     */
    public function setUserRegistration()
    {
        $this->_db->setQuery("SELECT params FROM `#__extensions` WHERE `name` = 'com_users'");

        $params = \json_decode($this->_db->LoadResult());

        // enabled
        $params->allowUserRegistration = 0;

        $this->_db->setQuery("UPDATE `#__extensions` set params = '".\json_encode($params)."' WHERE `name` = 'com_users'");
        $this->_db->query();

        return $this->getUserRegistration();
    }

    /**
     * 111
     * Enable /administrator/.htaccess restriction on apache.
     */
    public function setAdminHtaccess()
    {
        require 'lib/AdminTools/Model/AdminPassword/AdminPassword.php';

        $p           = new \Akeeba\AdminTools\Admin\Model\AdminPassword();
        $p->username = $this->_dataObj->u;
        $p->password = $this->_dataObj->p;

        if (!$p->protect()) {
            bfEncrypt::reply('error', 'Could not enable administrator .htaccess for some unknown reason :-( ');
        }

        bfEncrypt::reply('success', array(
                'enabled'  => 1,
                'username' => $this->_dataObj->u,
                'password' => $this->_dataObj->p,
            )
        );
    }

    /**
     * 112
     * Enable /administrator/.htaccess restriction on apache.
     */
    public function getAdminHtaccess()
    {
        require 'lib/AdminTools/Model/AdminPassword/AdminPassword.php';

        $obj = new \Akeeba\AdminTools\Admin\Model\AdminPassword();

        bfEncrypt::reply('success', array(
                'enabled' => $obj->isLocked(),
            )
        );
    }

    /**
     * Get the value of $gzip from /configuration.php.
     */
    public function getGzip()
    {
        bfEncrypt::reply('success', array(
                'enabled' => JFactory::getApplication()->getCfg('gzip', '0'),
            )
        );
    }

    /**
     * set the value of $gzip in /configuration.php.
     */
    public function setGzip()
    {
        return $this->_setConfigParam('gzip', 1, 'int');
    }

    /**
     * Get the config for session time.
     */
    public function getSessionlifetime()
    {
        bfEncrypt::reply('success', array(
                     'lifetime' => JFactory::getApplication()->getCfg('lifetime', 0),
                 )
             );
    }

    /**
     * set the session time to a sensibel recommend default.
     */
    public function setSessionlifetime()
    {
        $this->_setConfigParam('lifetime', 15, 'int');
    }

    /**
     * Get the number of days to delete logs after from the System - User Actions Log.
     *
     * @return int
     */
    public function setPurge30Days()
    {
        $this->_db->setQuery("SELECT params FROM `#__extensions` WHERE `name` = 'PLG_SYSTEM_ACTIONLOGS'");

        $params = \json_decode($this->_db->LoadResult());

        // enabled
        $params->logDeletePeriod = 30;

        $this->_db->setQuery("UPDATE `#__extensions` set params = '".\json_encode($params)."' WHERE `name` = 'PLG_SYSTEM_ACTIONLOGS'");
        $this->_db->query();

        return $this->getPurge30Days();
    }

    /**
     * 109
     * Gets php.ini and .user.ini files.
     */
    private function getPHPinifiles()
    {
        // make sure we only retrieve a small dataset
        $limitstart = (int) $this->_dataObj->ls;
        $sort       = $this->_dataObj->s;

        if (!$sort) {
            $sort = 'filewithpath';
        }

        if (!in_array($sort, array('filewithpath', 'filemtime'))) {
            die('Invalid Sort');
        }

        if ('filemtime' == $sort) {
            $sort = 'filemtime DESC';
        }

        $limit = (int) $this->_dataObj->limit;

        // Set the query
        $this->_db->setQuery('SELECT id, iscorefile, filewithpath, filemtime, fileperms, `size`, iscorefile from bf_files
                                WHERE filewithpath LIKE "%php.ini%" OR filewithpath LIKE "%.user.ini%"
                                ORDER BY '.$sort.'
                                LIMIT '.(int) $limitstart.', '.$limit);

        // Get an object list of files
        $files = $this->_db->loadObjectList();

        // see how many files there are in total without a limit
        $this->_db->setQuery('SELECT count(*) from bf_files WHERE filewithpath LIKE "%php.ini%" OR filewithpath LIKE "%.user.ini%"');
        $count = $this->_db->loadResult();

        // Only show files that still exist on the hard drive
        $existingFiles = array();
        foreach ($files as $k => $file) {
            if (file_exists(JPATH_BASE.$file->filewithpath)) {
                $existingFiles[] = $file;
            } else {
                $this->_db->setQuery(sprintf('DELETE FROM bf_files WHERE filewithpath = "%s"',
                    $file->filewithpath));
                $this->_db->query();

                --$count;
            }
        }

        // return an encrypted reply
        bfEncrypt::reply('success', array(
            'files' => $existingFiles,
            'total' => $count,
        ));
    }

    /**
     * Get the number of days to delete logs after from the System - User Actions Log.
     *
     * @return int
     */
    public function getPurge30Days()
    {
        if (version_compare(JVERSION, '3.9.0', '<')) {
            return false;
        }

        $this->_db->setQuery("SELECT params FROM `#__extensions` WHERE `name` = 'PLG_SYSTEM_ACTIONLOGS'");

        $params = $this->_db->LoadResult();

        if ('{}' == $params) {
            bfEncrypt::reply('success', array(
                    'days' => null,
                )
            );
        }

        $params = json_decode($params);

        bfEncrypt::reply('success', array(
                'days' => $params->logDeletePeriod,
            )
        );
    }

    /**
     * Joomla 3.9.0+ enable system log rotation.
     *
     * @return mixed
     */
    public function setSystemLogRotationEnabled()
    {
        $this->_db->setQuery("UPDATE `#__extensions` set enabled = 1 WHERE `name` = 'plg_system_logrotation'");
        $this->_db->query();

        return $this->getSystemLogRotationEnabled();
    }

    /**
     * Joomla 3.9.0+ check for system log rotation.
     *
     * @return mixed
     */
    public function getSystemLogRotationEnabled()
    {
        $this->_db->setQuery("SELECT count(*) FROM `#__extensions` WHERE `name` = 'plg_system_logrotation' and enabled = 1");

        bfEncrypt::reply('success', array(
                'enabled' => $this->_db->LoadResult(),
            )
        );
    }

    /**
     * Joomla 3.9.0+ enable IP logging in user action logging.
     *
     * @return mixed
     */
    public function setUseractionlogiplogenabled()
    {
        $this->_db->setQuery("SELECT params FROM `#__extensions` WHERE `name` = 'com_actionlogs'");

        $params = json_decode($this->_db->LoadResult());

        // enabled
        $params->ip_logging = 1;

        $this->_db->setQuery("UPDATE `#__extensions` set params = '".json_encode($params)."' WHERE `name` = 'com_actionlogs'");
        $this->_db->query();

        return $this->getUseractionlogiplogenabled();
    }

    /**
     * Joomla 3.9.0+ Check for plg_privacy_actionlogs enabled.
     *
     * @return mixed
     */
    public function getUseractionlogiplogenabled()
    {
        $this->_db->setQuery("SELECT params FROM `#__extensions` WHERE `name` = 'com_actionlogs'");

        $params = json_decode($this->_db->LoadResult());

        bfEncrypt::reply('success', array(
                'enabled' => $params->ip_logging,
            )
        );
    }

    /**
     * Joomla 3.9.0+ Check for plg_privacy_actionlogs enabled.
     *
     * @return mixed
     */
    public function setUseractionlogenabled()
    {
        $this->_db->setQuery("UPDATE `#__extensions` set enabled = 1 WHERE `name` = 'PLG_ACTIONLOG_JOOMLA'");
        $this->_db->query();
        $this->_db->setQuery("UPDATE `#__extensions` set enabled = 1 WHERE `name` = 'PLG_SYSTEM_ACTIONLOGS'");
        $this->_db->query();

        return $this->getUseractionlogenabled();
    }

    /**
     * Joomla 3.9.0+ Check for plg_privacy_actionlogs enabled.
     *
     * @return mixed
     */
    public function getUseractionlogenabled()
    {
        $this->_db->setQuery("SELECT count(*) FROM `#__extensions` WHERE (`name` = 'PLG_ACTIONLOG_JOOMLA' or `name` = 'PLG_SYSTEM_ACTIONLOGS') and enabled = 1");

        bfEncrypt::reply('success', array(
                'enabled' => 2 == $this->_db->LoadResult() ? 1 : 0,
            )
        );
    }

    /**
     * Joomla 3.9.0+ Check for plg_system_privacyconsent enabled.
     *
     * @return mixed
     */
    public function setPrivacyConsentPluginEnabled()
    {
        $this->_db->setQuery("UPDATE `#__extensions` set enabled = 1 WHERE `name` = 'plg_system_privacyconsent'");
        $this->_db->query();

        return $this->getUseractionlogenabled();
    }

    /**
     * Joomla 3.9.0+ Check for plg_system_privacyconsent enabled.
     *
     * @return mixed
     */
    public function getPrivacyConsentPluginEnabled()
    {
        $this->_db->setQuery("SELECT count(*) FROM `#__extensions` WHERE `name` = 'plg_system_privacyconsent' and enabled = 1");

        bfEncrypt::reply('success', array(
                'enabled' => $this->_db->LoadResult(),
            )
        );
    }

    /**
     * Check several EOL files for security patches.
     */
    public function eolsecuritystatus()
    {
        $data = array();

        /**
         * Joomla 1,5 & 2.5 Series
         * [20151201] - Core - Remote Code Execution Vulnerability.
         *
         * @see    http://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2015-8562
         * @secure md5 debug.php    Joomla 2.5.x    54a2f22406d8ee4b281d1a4543cb072b
         * @secure md5 session.php  Joomla 2.5.x    e9ac6f13100536eefa9241191c85c4b0
         * @secure md5 session.php  Joomla 1.5.x    63651a22d38b69f66959199955c5490c
         */
        $file  = JPATH_BASE.'/libraries/joomla/session/session.php';
        $file2 = JPATH_BASE.'/plugins/system/debug/debug.php';

        if (file_exists($file)) {
            $data['CVE20158562']['session'] = md5_file($file);
        } else {
            $data['CVE20158562']['session'] = 'NON_EXIST';
        }

        if (file_exists($file2)) {
            $data['CVE20158562']['debug'] = md5_file($file2);
        } else {
            $data['CVE20158562']['debug'] = 'NON_EXIST';
        }

        /**
         * Joomla 1,5.xxx.
         *
         * @see    http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_item_id=31626
         * @secure md5 media.php 3de2ea3338d49956b5dabf3a3fa1200d
         */
        $file = JPATH_BASE.'/administrator/components/com_media/helpers/media.php';

        if (file_exists($file)) {
            $data['fileupload_15']['media'] = md5_file($file);
        } else {
            $data['fileupload_15']['media'] = 'NON_EXIST';
        }

        /**
         * Joomla 1.5.xxx.
         *
         * @see    http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_item_id=31626
         * @secure md5 file.php 0eabdf91e2c7a26493eeb3dbe7a3fb39
         */
        $file = JPATH_BASE.'/libraries/joomla/filesystem/file.php';

        if (file_exists($file)) {
            $data['fileupload_15']['file'] = md5_file($file);
        } else {
            $data['fileupload_15']['file'] = 'NON_EXIST';
        }

        bfEncrypt::reply('success', array(
            'data' => $data,
        ));
    }

    public function applyeolpatch()
    {
        $i            = 0;
        $filesToPatch = array();

        if (preg_match('/^1\.5/', JVERSION)) {
            $filesToPatch[] = array(
                'source'      => 'https://cdn.myjoomla.com/public/patchfile/1',
                'destination' => JPATH_BASE.'/libraries/joomla/filesystem/file.php',
            );
            $filesToPatch[] = array(
                'source'      => 'https://cdn.myjoomla.com/public/patchfile/2',
                'destination' => JPATH_BASE.'/administrator/components/com_media/helpers/media.php',
            );
            $filesToPatch[] = array(
                'source'      => 'https://cdn.myjoomla.com/public/patchfile/3',
                'destination' => JPATH_BASE.'/libraries/joomla/session/session.php',
            );
        } elseif (preg_match('/^2\.5/', JVERSION)) {
            $filesToPatch[] = array(
                'source'      => 'https://cdn.myjoomla.com/public/patchfile/4',
                'destination' => JPATH_BASE.'/libraries/joomla/session/session.php',
            );
            $filesToPatch[] = array(
                'source'      => 'https://cdn.myjoomla.com/public/patchfile/5',
                'destination' => JPATH_BASE.'/plugins/system/debug/debug.php',
            );
        }

        foreach ($filesToPatch as $fileToPatch) {
            $source = base64_decode(file_get_contents($fileToPatch['source']));

            if (!is_writable($fileToPatch['destination'])) {
                bfEncrypt::reply('error', array(
                    'msg' => 'File NOT patched as it is unwritable: '.$fileToPatch['destination'],
                ));
            }

            if (!$source) {
                bfEncrypt::reply('error', array(
                    'msg' => 'File NOT patched as no source for it: '.$fileToPatch['destination'],
                ));
            }

            if (file_put_contents($fileToPatch['destination'], $source)) {
                ++$i;
            } else {
                bfEncrypt::reply('error', array(
                    'msg' => 'File NOT patched - no idea why :-( we coult not write to the file ',
                ));
            }

            unset($source);
        }

        bfEncrypt::reply('success', array(
            'msg' => $i.' File(s) patched!',
        ));
    }

    /**
     * Load Flash Upload Settings from params from com_media without using a helper. and then remove swf and application/x-shockwave-flash.
     */
    public function setUploadsettingsfixed()
    {
        $this->_db->setQuery("select params from #__extensions where element = 'com_media'");
        $params = json_decode($this->_db->LoadResult());

        $items = explode(',', $params->upload_extensions);
        foreach ($items as $k => $item) {
            if ('swf' == strtolower(trim($item))) {
                unset($items[$k]);
            }
        }
        $params->upload_extensions = implode(',', $items);

        $items = explode(',', $params->upload_mime);
        foreach ($items as $k => $item) {
            if ('application/x-shockwave-flash' == strtolower(trim($item))) {
                unset($items[$k]);
            }
        }
        $params->upload_mime = implode(',', $items);
        $sql                 = sprintf("UPDATE #__extensions set `params` = '%s' WHERE `element` = 'com_media'", json_encode($params));
        $this->_db->setQuery($sql);
        $this->_db->query();

        $this->getUploadsettingsfixed();
    }

    /**
     * Load Flash Upload Settings from params from com_media without using a helper.
     */
    public function getUploadsettingsfixed()
    {
        $this->_db->setQuery("select params from #__extensions where element = 'com_media'");
        $params = json_decode($this->_db->LoadResult());
        if (
            !preg_match('/swf/ism', $params->upload_extensions)
            &&
            !preg_match('/application\/x-shockwave-flash/ism', $params->upload_mime)
        ) {
            bfEncrypt::reply('success', array('uploadsettingsfixed' => 1));
        } else {
            bfEncrypt::reply('success', array('uploadsettingsfixed' => 0));
        }
    }

    /**
     * Method to delete a named file when we know its id.
     */
    private function deleteFile()
    {
        // Get the filewithpath based on the id
        $this->_db->setQuery('SELECT filewithpath from bf_files WHERE id = '.(int) $this->_dataObj->file_id);
        $filewithpath = $this->_db->loadResult();

        // check that the file we got form the database matches to the path we think it should be
        if ($this->_dataObj->filewithpath != $filewithpath) {
            bfEncrypt::reply('failure', array(
                'msg' => 'File Not matching: '.$this->_dataObj->filewithpath.' !== '.$filewithpath,
            ));
        }

        // If the file doesnt exist then remove from cache and reply
        if (!file_exists(JPATH_BASE.$filewithpath)) {
            $this->_db->setQuery('DELETE FROM bf_files WHERE id = '.(int) $this->_dataObj->file_id);
            $this->_db->query();
            bfEncrypt::reply('failure', array(
                'msg' => 'File doesn\'t exist: '.$filewithpath,
            ));
        }

        // Attempt to force deletion
        if (!is_writable(JPATH_BASE.$filewithpath)) {
            @chmod(JPATH_BASE.$filewithpath, 0777);
        }

        // delete the file, making sure we prefix with a path
        if (@unlink(JPATH_BASE.$filewithpath)) {
            $this->_db->setQuery('DELETE FROM bf_files WHERE id = '.(int) $this->_dataObj->file_id);
            $this->_db->query();

            // File deleted - say yes
            bfEncrypt::reply('success', array(
                'msg' => 'File deleted: '.$filewithpath,
            ));
        } else {
            // File deleted - say no
            bfEncrypt::reply('failure', array(
                'msg' => 'File Not Deleted: '.$filewithpath,
            ));
        }
    }

    /**
     * I delete a folder.
     */
    private function deleteFolder()
    {
        // Require more complex methods for dealing with files
        require 'bfFilesystem.php';

        // init our return msg
        $msg = array();

        // hidden or normal - needed for ALL deletes
        $type = $this->_dataObj->type;

        // switch on type
        if ('hidden' == $type) {
            // get the folders cache id
            $folder_id = $this->_dataObj->fid;

            // init
            $msgToReturn                    = array();
            $msgToReturn['deleted_files']   = 0;
            $msgToReturn['deleted_folders'] = 0;
            $msgToReturn['left']            = 0;

            // Do we want to delete all hidden folders?
            if ('ALL' == $folder_id) { // All meaning all hidden folders, not ALL folders in our db!!
                $this->_dataObj->ls    = 0;
                $this->_dataObj->limit = 999999999;

                // get all the hidden folders
                $folders = $this->getHiddenFolders(true);
                bfLog::log('Deleting this many folders : '.count($folders));

                // foreach hidden folder, delete that hidden folder recursivly
                foreach ($folders as $folder) {
                    // delete recursive
                    bfLog::log('Deleting folder: '.JPATH_BASE.$folder->folderwithpath);
                    $msg = Bf_Filesystem::deleteRecursive(JPATH_BASE.$folder->folderwithpath, true, $msg);

                    $this->_db->setQuery('DELETE FROM bf_folders WHERE folderwithpath LIKE "'.$folder->folderwithpath.'%"');
                    $this->_db->loadResult();
                    $this->_db->setQuery('DELETE FROM bf_files WHERE filewithpath LIKE "'.$folder->folderwithpath.'%"');
                    $this->_db->loadResult();

                    // oh dear we failed
                    if ('failure' == $msg['result']) {
                        $msgToReturn                    = array();
                        $msgToReturn['deleted_files']   = count(@$msg['deleted_files']);
                        $msgToReturn['deleted_folders'] = count(@$msg['deleted_folders']);
                        $msgToReturn['left']            = $this->getHiddenFolders(true);

                        // send back the error message
                        bfEncrypt::reply('failure', array(
                            'msg' => 'Problem!: '.json_encode($msgToReturn),
                        ));
                    }
                }
            } else {
                // select the folder to delete
                $this->_db->setQuery('SELECT folderwithpath FROM bf_folders WHERE id = '.(int) $folder_id);
                $folderwithpath = $this->_db->loadResult();

                // if the folder is not there
                if (!$folderwithpath) {
                    bfEncrypt::reply('failure', array(
                        'msg' => 'Folder Not Found #msg2#: '.$folderwithpath,
                    ));
                }

                $msg = Bf_Filesystem::deleteRecursive(JPATH_BASE.$folderwithpath, true, $msg);
            }

            // if we deleted some folders
            if (count($msg['deleted_folders'])) {
                foreach ($msg['deleted_folders'] as $folder) {
                    $fwp = str_replace('//', '/', str_replace(JPATH_BASE, '', $folder));

                    $sql = "DELETE FROM bf_folders where folderwithpath = '".$fwp."'";

                    $this->_db->setQuery($sql);
                    $this->_db->query();
                }
            }

            // if we deleted some files
            if (count($msg['deleted_files'])) {
                foreach ($msg['deleted_files'] as $file) {
                    $fwp = str_replace('//', '/', str_replace(JPATH_BASE, '', $file));

                    $sql = "DELETE FROM bf_files where filewithpath = '".$fwp."'";
                    $this->_db->setQuery($sql);
                    $this->_db->query();
                }
            }

            // reply back with our warning or success message
            $msgToReturn                    = array();
            $msgToReturn['deleted_files']   = count($msg['deleted_files']);
            $msgToReturn['deleted_folders'] = count($msg['deleted_folders']);
            $msgToReturn['left']            = count($this->getHiddenFolders(true));

            bfEncrypt::reply('success', array(
                'msg' => json_encode($msgToReturn),
            ));
        }

        if ($type = 'deleteinstallation') {
            $folders = $this->getFolders(JPATH_BASE);

            foreach ($folders as $folder) {
                if (preg_match('/installation|installation.old|docs\/installation|install|installation.bak|installation.old|installation.backup|installation.delete/i', $folder)) {
                    $installationFolders[] = $folder;
                }
            }

            foreach ($installationFolders as $folderwithpath) {
                bfLog::log('Deleting folder: '.$folderwithpath);
                $msg = Bf_Filesystem::deleteRecursive(JPATH_BASE.$folderwithpath, true, $msg);
            }

            bfEncrypt::reply('success', array(
                'msg' => 'ok',
            ));
        }
    }

    /**
     * @param bool $internal
     *
     * @return array|mixed
     */
    private function getHiddenFolders($internal = false)
    {
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;

        if (!$limitstart) {
            $limitstart = 0;
        }
        if (!$limit) {
            $limit = '9999999999999999';
        }
        $this->_db->setQuery('SELECT * FROM bf_folders WHERE folderwithpath LIKE "%/.%" LIMIT '.(int) $limitstart.', '.$limit);
        $folders = $this->_db->loadObjectList();

        if (true === $internal) {
            return $folders;
        }

        $this->_db->setQuery('SELECT count(*) FROM bf_folders WHERE folderwithpath LIKE "%/.%"');
        $count = $this->_db->loadResult();

        bfEncrypt::reply('success', array(
            'files' => $folders,
            'total' => $count,
        ));
    }

    /**
     * Function taken from Akeeba filesystem.php.
     *
     * Akeeba Engine
     * The modular PHP5 site backup engine
     *
     * @copyright Copyright (c)2009 Nicholas K. Dionysopoulos
     * @license   GNU GPL version 3 or, at your option, any later version
     *
     * @version   Id: scanner.php 158 2010-06-10 08:46:49Z nikosdion
     */
    private function getFolders($folder)
    {
        // Initialize variables
        $arr   = array();
        $false = false;

        $folder = trim($folder);

        if (!is_dir($folder) && !is_dir($folder.DIRECTORY_SEPARATOR) || is_link($folder.DIRECTORY_SEPARATOR) || is_link($folder) || !$folder) {
            return $false;
        }

        if (@file_exists($folder.DIRECTORY_SEPARATOR.'.myjoomla.ignore.folder')) {
            return array();
        }

        $handle = @opendir($folder);
        if (false === $handle) {
            $handle = @opendir($folder.DIRECTORY_SEPARATOR);
        }
        // If directory is not accessible, just return FALSE
        if (false === $handle) {
            return $false;
        }

        while ((false !== ($file = @readdir($handle)))) {
            if (('.' != $file) && ('..' != $file) && (null != trim($file))) {
                $ds    = ('' == $folder) || (DIRECTORY_SEPARATOR == $folder) || (DIRECTORY_SEPARATOR == @substr($folder, -1)) || (DIRECTORY_SEPARATOR == @substr($folder, -1)) ? '' : DIRECTORY_SEPARATOR;
                $dir   = trim($folder.$ds.$file);
                $isDir = @is_dir($dir);
                if ($isDir) {
                    $arr[] = $this->cleanupFileFolderName(str_replace(JPATH_BASE, '', $folder.DIRECTORY_SEPARATOR.$file));
                }
            }
        }
        @closedir($handle);

        return $arr;
    }

    /**
     * Clean up a string, a path name.
     *
     * @param string $str
     *
     * @return string
     */
    private function cleanupFileFolderName($str)
    {
        $str = str_replace('////', '/', $str);
        $str = str_replace('///', '/', $str);
        $str = str_replace('//', '/', $str);
        $str = str_replace('\\/', '/', $str);
        $str = str_replace('\\t', '/t', $str);
        $str = str_replace("\/", '/', $str);

        return addslashes($str);
    }

    /**
     * I get the number of core files that failed the hash checking.
     */
    private function getCoreHashFailedFileList()
    {
        // set up the limit and limit start for the SQL
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;
        $this->_db->setQuery('SELECT id, filewithpath, filemtime, fileperms FROM bf_files WHERE hashfailed = 1 LIMIT '.$limitstart.', '.$limit);

        // Get the files from the cache
        $files = $this->_db->loadObjectList();

        // get the count as well, for pagination
        $this->_db->setQuery('SELECT count(*) from bf_files WHERE hashfailed = 1');
        $count = $this->_db->loadResult();

        // send back the totals
        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    /**
     * I get list of database tables that begin with bak_.
     */
    private function deleteBakTables()
    {
        $tables = $this->getBakTables(true);

        // for all the bak tables
        foreach ($tables as $table) {
            // compose the sql query
            $this->_db->setQuery('DROP TABLE '.$table[0]);

            // delete the bak_tables
            $this->_db->query();
        }

        $count = count($tables);

        // send back the totals
        bfEncrypt::reply('success', array(
            'tables' => $tables,
            'total'  => $count,
        ));
    }

    /**
     * I get list of database tables that begin with bak_.
     */
    private function getBakTables($internal = false)
    {
        // Get the database name
        $config = JFactory::getApplication();
        $dbname = $config->getCfg('db', '');

        // compose the sql query
        $this->_db->setQuery("SHOW TABLES WHERE `Tables_in_{$dbname}` like 'bak_%'");

        // Get the bak_tables
        $tables = $this->_db->loadRowList();

        // return array if we are internally calling this method
        if (true === $internal) {
            return $tables;
        }

        // count them
        $count = count($tables);

        // send back the totals
        bfEncrypt::reply('success', array(
            'tables' => $tables,
            'total'  => $count,
        ));
    }

    /**
     * get the value of the $live_site var from configuration.php.
     */
    private function getConfiguredLiveSite()
    {
        // send back the totals
        bfEncrypt::reply('success', array(
            'live_site' => JFactory::getApplication()->getCfg('live_site', ''),
        ));
    }

    /**
     * Get a list of folders with 777 permissions.
     */
    private function getFolderPermissions()
    {
        // set up the limit and the limitstart SQL
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;
        $this->_db->setQuery('SELECT `id`, `folderwithpath`, `folderinfo` from bf_folders WHERE folderinfo IN ("777", "351", "311") LIMIT '.$limitstart.', '.$limit);

        // get the files
        $files = $this->_db->loadObjectList();

        // get the count for pagination
        $this->_db->setQuery('SELECT count(*) from bf_folders WHERE `folderinfo` IN ("777", "351", "311")');
        $count = $this->_db->loadResult();

        // send back the totals
        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    /**
     * Get a list of files with 777 permissions.
     */
    private function getFilePermissions()
    {
        // set up the limit and the limitstart SQL
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;
        $this->_db->setQuery('SELECT id, filewithpath, fileperms from bf_files WHERE fileperms = "0777" OR fileperms = "777" LIMIT '.(int) $limitstart.', '.$limit);

        // get the files
        $files = $this->_db->loadObjectList();

        // get the count for pagination
        $this->_db->setQuery('SELECT count(*) from bf_files WHERE fileperms = "0777" OR fileperms = "777"');
        $count = $this->_db->loadResult();

        // send back the totals
        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    /**
     * Set the permissions on files that have 777 perms to be 644.
     */
    private function setFilePermissions()
    {
        $fixed  = 0;
        $errors = 0;

        $this->_db->setQuery('SELECT id, filewithpath from bf_files WHERE fileperms = "0777" OR fileperms = "777"');
        $files = $this->_db->loadObjectList();
        foreach ($files as $file) {
            if (@chmod(JPATH_BASE.$file->filewithpath, 0644)) {
                ++$fixed;
                $this->_db->setQuery('UPDATE bf_files SET fileperms = "0644" WHERE id = "'.(int) $file->id.'"');
                $this->_db->query();
            } else {
                ++$errors;
            }
        }

        $this->_db->setQuery('SELECT count(*) FROM bf_folders WHERE folderinfo LIKE "%777%"');
        $folders_777 = $this->_db->LoadResult();

        $res           = new stdClass();
        $res->errors   = $errors;
        $res->fixed    = $fixed;
        $res->leftover = $folders_777;

        bfEncrypt::reply('success', $res);
    }

    /**
     * Return the list of files that have been flagged as containing mail commands or text.
     */
    private function getUploaderFileList()
    {
        // make sure we only retrieve a small dataset
        $limitstart = (int) $this->_dataObj->ls;
        $sort       = $this->_dataObj->s;

        if (!$sort) {
            $sort = 'filewithpath';
        }

        if (!in_array($sort, array('filewithpath', 'filemtime'))) {
            die('Invalid Sort');
        }

        if ('filemtime' == $sort) {
            $sort = 'filemtime DESC';
        }

        $limit = (int) $this->_dataObj->limit;

        // Set the query
        $this->_db->setQuery('SELECT id, iscorefile, filewithpath, filemtime, fileperms, `size`, iscorefile from bf_files
                                WHERE uploader = 1
                                ORDER BY '.$sort.'
                                LIMIT '.(int) $limitstart.', '.$limit);

        // Get an object list of files
        $files = $this->_db->loadObjectList();

        // see how many files there are in total without a limit
        $this->_db->setQuery('SELECT count(*) from bf_files WHERE uploader = 1');
        $count = $this->_db->loadResult();

        // Only show files that still exist on the hard drive
        $existingFiles = array();
        foreach ($files as $k => $file) {
            if (file_exists(JPATH_BASE.$file->filewithpath)) {
                $existingFiles[] = $file;
            } else {
                $this->_db->setQuery(sprintf('DELETE FROM bf_files WHERE filewithpath = "%s"',
                    $file->filewithpath));
                $this->_db->query();

                --$count;
            }
        }

        // return an encrypted reply
        bfEncrypt::reply('success', array(
            'files' => $existingFiles,
            'total' => $count,
        ));
    }

    /**
     * Return the list of files that have been flagged as containing mail commands or text.
     */
    private function getMailerFileList()
    {
        // make sure we only retrieve a small dataset
        $limitstart = (int) $this->_dataObj->ls;
        $sort       = $this->_dataObj->s;

        if (!$sort) {
            $sort = 'filewithpath';
        }

        if (!in_array($sort, array('filewithpath', 'filemtime'))) {
            die('Invalid Sort');
        }

        if ('filemtime' == $sort) {
            $sort = 'filemtime DESC';
        }

        $limit = (int) $this->_dataObj->limit;

        // Set the query
        $this->_db->setQuery('SELECT id, iscorefile, filewithpath, filemtime, fileperms, `size`, iscorefile from bf_files
                                WHERE mailer = 1
                                ORDER BY '.$sort.'
                                LIMIT '.(int) $limitstart.', '.$limit);

        // Get an object list of files
        $files = $this->_db->loadObjectList();

        // see how many files there are in total without a limit
        $this->_db->setQuery('SELECT count(*) from bf_files WHERE mailer = 1');
        $count = $this->_db->loadResult();

        // Only show files that still exist on the hard drive
        $existingFiles = array();
        foreach ($files as $k => $file) {
            if (file_exists(JPATH_BASE.$file->filewithpath)) {
                $existingFiles[] = $file;
            } else {
                $this->_db->setQuery(sprintf('DELETE FROM bf_files WHERE filewithpath = "%s"',
                    $file->filewithpath));
                $this->_db->query();

                --$count;
            }
        }

        // return an encrypted reply
        bfEncrypt::reply('success', array(
            'files' => $existingFiles,
            'total' => $count,
        ));
    }

    /**
     * Return the list of files that have been flagged as containing patterns that match our suspect patterns
     * These maybe false positives for suspect content, but might be examples of bad code standards like using
     * ../../../ or eval() method.
     */
    private function getSuspectContentFileList()
    {
        // make sure we only retrieve a small dataset
        $limitstart = (int) $this->_dataObj->ls;
        $sort       = $this->_dataObj->s;

        if (!$sort) {
            $sort = 'filewithpath';
        }

        if (!in_array($sort, array('filewithpath', 'filemtime'))) {
            die('Invalid Sort');
        }

        if ('filemtime' == $sort) {
            $sort = 'filemtime DESC';
        }

        $limit = (int) $this->_dataObj->limit;

        // Set the query
        $this->_db->setQuery('SELECT id, iscorefile, filewithpath, filemtime, fileperms, `size`, iscorefile, hacked, currenthash from bf_files
                                WHERE suspectcontent = 1 OR hacked = 1
                                ORDER BY '.$sort.'
                                LIMIT '.(int) $limitstart.', '.$limit);

        // Get an object list of files
        $files = $this->_db->loadObjectList();

        // see how many files there are in total without a limit
        $this->_db->setQuery('SELECT count(*) from bf_files WHERE suspectcontent = 1 OR hacked = 1');
        $count = $this->_db->loadResult();

        // Only show files that still exist on the hard drive
        $existingFiles = array();
        foreach ($files as $k => $file) {
            if (file_exists(JPATH_BASE.$file->filewithpath)) {
                $existingFiles[] = $file;
            } else {
                $this->_db->setQuery(sprintf('DELETE FROM bf_files WHERE filewithpath = "%s"',
                    $file->filewithpath));
                $this->_db->query();

                --$count;
            }
        }

        // return an encrypted reply
        bfEncrypt::reply('success', array(
            'files' => $existingFiles,
            'total' => $count,
        ));
    }

    /**
     * Get SQL files found.
     */
    private function getSQLFiles()
    {
        // make sure we only retrieve a small dataset
        $limitstart = (int) $this->_dataObj->ls;
        $sort       = $this->_dataObj->s;

        if (!$sort) {
            $sort = 'filewithpath';
        }

        if (!in_array($sort, array('filewithpath', 'filemtime'))) {
            die('Invalid Sort');
        }

        if ('filemtime' == $sort) {
            $sort = 'filemtime DESC';
        }

        $limit = (int) $this->_dataObj->limit;

        // Set the query
        $this->_db->setQuery('SELECT * FROM bf_files WHERE 
        (
        (filewithpath LIKE \'%.sql\' or filewithpath LIKE \'%sql/site.%\')
        and 
        (iscorefile = 0 or iscorefile is null)
        )
                                ORDER BY '.$sort.'
                                LIMIT '.(int) $limitstart.', '.$limit);

        // Get an object list of files
        $files = $this->_db->loadObjectList();

        // see how many files there are in total without a limit
        $this->_db->setQuery('SELECT count(*)  FROM bf_files WHERE 
        (
        (filewithpath LIKE \'%.sql\' or filewithpath LIKE \'%sql/site.%\')
        and 
        (iscorefile = 0 or iscorefile is null)
        )');
        $count = $this->_db->loadResult();

        // Only show files that still exist on the hard drive
        $existingFiles = array();
        foreach ($files as $k => $file) {
            if (file_exists(JPATH_BASE.$file->filewithpath)) {
                $existingFiles[] = $file;
            } else {
                $this->_db->setQuery(sprintf('DELETE FROM bf_files WHERE filewithpath = "%s"',
                    $file->filewithpath));
                $this->_db->query();

                --$count;
            }
        }

        // return an encrypted reply
        bfEncrypt::reply('success', array(
            'files' => $existingFiles,
            'total' => $count,
        ));
    }

    /**
     * Return the list of files that have been flagged as containing patterns that match our suspect patterns
     * These maybe false positives for suspect content, but might be examples of bad code standards like using
     * ../../../ or eval() method.
     */
    private function getNonCoreFileList()
    {
        // make sure we only retrieve a small dataset
        $limitstart = (int) $this->_dataObj->ls;
        $sort       = $this->_dataObj->s;

        if (!$sort) {
            $sort = 'filewithpath';
        }

        if (!in_array($sort, array('filewithpath', 'filemtime'))) {
            die('Invalid Sort');
        }

        if ('filemtime' == $sort) {
            $sort = 'filemtime DESC';
        }

        $limit = (int) $this->_dataObj->limit;

        // Set the query
        $this->_db->setQuery('SELECT id, iscorefile, filewithpath, filemtime, fileperms, `size`, iscorefile from bf_files
                                WHERE iscorefile IS NULL
                                ORDER BY '.$sort.'
                                LIMIT '.(int) $limitstart.', '.$limit);

        // Get an object list of files
        $files = $this->_db->loadObjectList();

        // see how many files there are in total without a limit
        $this->_db->setQuery('SELECT count(*) from bf_files WHERE iscorefile IS NULL');
        $count = $this->_db->loadResult();

        // Only show files that still exist on the hard drive
        $existingFiles = array();
        foreach ($files as $k => $file) {
            if (file_exists(JPATH_BASE.$file->filewithpath)) {
                $existingFiles[] = $file;
            } else {
                $this->_db->setQuery(sprintf('DELETE FROM bf_files WHERE filewithpath = "%s"',
                    $file->filewithpath));
                $this->_db->query();

                --$count;
            }
        }

        // return an encrypted reply
        bfEncrypt::reply('success', array(
            'files' => $existingFiles,
            'total' => $count,
        ));
    }

    /**
     * @param bool $internal
     *
     * @return array|mixed
     */
    private function getInstallationFolders($internal = false)
    {
        $folders = $this->getFolders(JPATH_BASE);
        foreach ($folders as $folder) {
            if (preg_match('/installation|installation.old|docs\/installation|install|installation.bak|installation.old|installation.backup|installation.delete/i', $folder)) {
                $installationFolders[] = $folder;
            }
        }

        bfEncrypt::reply('success', array(
            'files' => $installationFolders,
            'total' => count($installationFolders),
        ));
    }

    /**
     * @param bool $internal
     *
     * @return array|mixed
     */
    private function getRecentlyModified($internal = false)
    {
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;
        if (!$limitstart) {
            $limitstart = 0;
        }
        if (!$limit) {
            $limit = '9999999999999999';
        }

        $sql = "SELECT * FROM bf_files WHERE filemtime > '".strtotime('-3 days', time())."' ORDER BY filemtime DESC LIMIT ".(int) $limitstart.', '.$limit;
        $this->_db->setQuery($sql);
        $files = $this->_db->LoadObjectList();

        if (true === $internal) {
            return $files;
        }

        $this->_db->setQuery("SELECT count(*) FROM bf_files WHERE filemtime > '".strtotime('-3 days', time())."'");
        $count = $this->_db->loadResult();

        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    /**
     * @param bool $internal
     *
     * @return array|mixed
     */
    private function getHtaccessFiles($internal = false)
    {
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;
        if (!$limitstart) {
            $limitstart = 0;
        }
        if (!$limit) {
            $limit = '9999999999999999';
        }

        $sql = "SELECT * FROM bf_files WHERE filewithpath LIKE '%/.htaccess' ORDER BY filewithpath DESC LIMIT ".(int) $limitstart.', '.$limit;
        $this->_db->setQuery($sql);
        $files = $this->_db->LoadObjectList();

        if (true === $internal) {
            return $files;
        }

        $this->_db->setQuery("SELECT count(*) FROM bf_files WHERE filewithpath LIKE '%/.htaccess'");
        $count = $this->_db->loadResult();

        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    /**
     * @param bool $internal
     *
     * @return array|mixed
     */
    private function getLargefiles($internal = false)
    {
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;
        $order      =  $this->_dataObj->orderby;

        if (!in_array($order, array('filewithpath', 'filemtime', 'size'))) {
            $order = 'filewithpath';
        }

        if (!$limitstart) {
            $limitstart = 0;
        }

        if (!$limit) {
            $limit = '9999999999999999';
        }

        $sql = 'SELECT * FROM bf_files WHERE SIZE > 2097152 ORDER BY '.$order.' DESC LIMIT '.(int) $limitstart.', '.$limit;

        $this->_db->setQuery($sql);
        $files = $this->_db->LoadObjectList();

        if (true === $internal) {
            return $files;
        }

        $this->_db->setQuery('SELECT COUNT(*) FROM bf_files WHERE SIZE > 2097152');
        $count = (int) $this->_db->loadResult();

        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    /**
     * @param bool $internal
     *
     * @return array|mixed
     */
    private function getArchivefiles($internal = false)
    {
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;
        if (!$limitstart) {
            $limitstart = 0;
        }
        if (!$limit) {
            $limit = '9999999999999999';
        }

        $sql = 'SELECT * FROM bf_files WHERE
        filewithpath LIKE "%.zip"
        OR filewithpath LIKE "%.tar"
        OR filewithpath LIKE "%.tar.gz"
        OR filewithpath LIKE "%.bz2"
        OR filewithpath LIKE "%.gzip"
        OR filewithpath LIKE "%.bzip2" ORDER BY filemtime DESC LIMIT '.(int) $limitstart.', '.$limit;
        $this->_db->setQuery($sql);
        $files = $this->_db->LoadObjectList();

        if (true === $internal) {
            return $files;
        }

        $this->_db->setQuery('SELECT count(*) FROM bf_files WHERE
        filewithpath LIKE "%.zip"
        OR filewithpath LIKE "%.tar"
        OR filewithpath LIKE "%.tar.gz"
        OR filewithpath LIKE "%.bz2"
        OR filewithpath LIKE "%.gzip"
        OR filewithpath LIKE "%.bzip2"');
        $count = (int) $this->_db->loadResult();

        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    /**
     * @param bool $internal
     *
     * @return array|mixed
     */
    private function getPhpinwrongplace($internal = false)
    {
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;
        if (!$limitstart) {
            $limitstart = 0;
        }
        if (!$limit) {
            $limit = '9999999999999999';
        }

        $sql = 'SELECT * FROM bf_files AS b WHERE filewithpath REGEXP "^/images/.*\.php$" ORDER BY filemtime DESC LIMIT '.(int) $limitstart.', '.$limit;
        $this->_db->setQuery($sql);
        $files = $this->_db->LoadObjectList();

        if (true === $internal) {
            return $files;
        }

        $count = (int) count($files);

        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    /**
     * @param bool $internal
     *
     * @return array|mixed
     */
    private function getTmpfiles($internal = false)
    {
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;
        if (!$limitstart) {
            $limitstart = 0;
        }
        if (!$limit) {
            $limit = '9999999999999999';
        }

        $sql = 'SELECT * FROM bf_files WHERE
        filewithpath LIKE "/tmp%"
        AND
                filewithpath != "/tmp/index.html"
        ORDER BY filemtime DESC LIMIT '.(int) $limitstart.', '.$limit;
        $this->_db->setQuery($sql);
        $files = $this->_db->LoadObjectList();

        if (true === $internal) {
            return $files;
        }

        $this->_db->setQuery('SELECT count(*) FROM bf_files WHERE
        filewithpath LIKE "/tmp%"
        AND
                filewithpath != "/tmp/index.html"
        ORDER BY filemtime');
        $count = (int) $this->_db->loadResult();

        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    private function clearFluffFiles()
    {
        require 'bfFilesystem.php';

        foreach ($this->fluffFiles as $file) {
            // ensure we are based correctly
            $fileWithPath = JPATH_BASE.$file;

            // Remove File.
            unlink($fileWithPath);
        }

        $this->getFlufffiles(true);
    }

    /**
     * @param bool $internal
     *
     * @return array|mixed
     */
    private function getFlufffiles($internal = false)
    {
        $files               = array();
        $files['present']    = array();
        $files['notpresent'] = array();

        foreach ($this->fluffFiles as $file) {
            // ensure we are based correctly
            $fileWithPath = JPATH_BASE.$file;

            // determine if the file is present or not
            if (@file_exists($fileWithPath)) { //@ to avoid any nasty warnings
                $files['present'][] = $file;
            } else {
                $files['notpresent'][] = $file;
            }
        }

        bfEncrypt::reply('success', array(
            'total' => count($files['present']),
            'files' => $files,
        ));
    }

    /**
     * @param bool $internal
     *
     * @return array|mixed
     */
    private function getRenamedToHide($internal = false)
    {
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;
        if (!$limitstart) {
            $limitstart = 0;
        }
        if (!$limit) {
            $limit = '9999999999999999';
        }

        $sql = 'SELECT * FROM bf_files WHERE
                                filewithpath LIKE "%.backup%"
                                OR
                                filewithpath LIKE "%.bak%"
                                OR
                                filewithpath LIKE "%.old%"
                                ORDER BY filemtime DESC LIMIT '.(int) $limitstart.', '.$limit;
        $this->_db->setQuery($sql);
        $files = $this->_db->LoadObjectList();

        if (true === $internal) {
            return $files;
        }

        $this->_db->setQuery('SELECT count(*) FROM bf_files WHERE
                                filewithpath LIKE "%.backup%"
                                OR
                                filewithpath LIKE "%.bak%"
                                OR
                                filewithpath LIKE "%.old%"');
        $count = $this->_db->loadResult();

        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    private function clearTmpFiles()
    {
        require 'bfFilesystem.php';

        $filesAndFolders = Bf_Filesystem::readDirectory(JPATH_ROOT.'/tmp', '.', true);

        foreach ($filesAndFolders as $pointer) {
            $pointer = JPATH_ROOT.'/tmp/'.$pointer;

            if (is_dir($pointer)) {
                bfLog::log('Deleting '.$pointer);
                Bf_Filesystem::deleteRecursive($pointer, true);
            } else {
                bfLog::log('Deleting '.$pointer);
                unlink($pointer);
            }
        }

        file_put_contents(JPATH_ROOT.'/tmp/index.html', '<html><body bgcolor="#FFFFFF"></body></html> ');

        $sql = 'DELETE FROM bf_files WHERE
                  filewithpath LIKE "/tmp%"
                    AND
                  filewithpath != "/tmp/index.html"';
        $this->_db->setQuery($sql);
        $this->_db->query();

        bfEncrypt::reply('success', array(
            'res' => true,
        ));
    }

    /**
     * @param bool $internal
     *
     * @return array|mixed
     */
    private function getDotfiles($internal = false)
    {
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;
        if (!$limitstart) {
            $limitstart = 0;
        }
        if (!$limit) {
            $limit = '9999999999999999';
        }

        $sql = 'SELECT * FROM bf_files WHERE filewithpath LIKE "%/.%" ORDER BY filemtime DESC LIMIT '.(int) $limitstart.', '.$limit;
        $this->_db->setQuery($sql);
        $files = $this->_db->LoadObjectList();

        if (true === $internal) {
            return $files;
        }

        $this->_db->setQuery('SELECT count(*) FROM bf_files WHERE filewithpath LIKE "%/.%"');
        $count = $this->_db->loadResult();

        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    /**
     * Find files which have zero bytes (no content) as they just litter the webspace
     * and run up inode counts. Joomla doesnt rely on zero byte files, we have seen "other hack cleanup companies"
     * litter the webspace with zero byte files and so this tool deletes those too.
     *
     * @param bool $internal
     *
     * @return mixed
     */
    private function getZerobyteFiles($internal = false)
    {
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;
        if (!$limitstart) {
            $limitstart = 0;
        }
        if (!$limit) {
            $limit = '9999999999999999';
        }

        $sql = 'SELECT * FROM bf_files WHERE size = 0 ORDER BY filemtime DESC LIMIT '.(int) $limitstart.', '.$limit;
        $this->_db->setQuery($sql);
        $files = $this->_db->LoadObjectList();

        if (true === $internal) {
            return $files;
        }

        $this->_db->setQuery('SELECT count(*) FROM bf_files WHERE size = 0');
        $count = $this->_db->loadResult();

        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    /**
     * Restore core files from a trusted source.
     *
     * This source (corefiles.myjoomla.io) is checked hourly for integrity, if you are concerned about MITM Attacks, well, if your server
     * is compromised enough for a MITM Attack then you have bigger issues, plus this is how Joomla updates happen
     * anyway so no additional security issues are created with this code!
     */
    private function restoreAllMissingFiles()
    {
        $url         = 'https://corefiles.myjoomla.io/%s%s?raw';
        $restored    = 0;
        $notRestored = 0;

        // Crappy Servers Alert!
        @set_time_limit(3600);

        $files = $this->getMissingCoreFiles(true);
        foreach ($files as $file) {
            $downloadUrl = sprintf($url, JVERSION, $file->filewithpath);

            $restoreToFile = JPATH_BASE.$file->filewithpath;

            // check folder and path to folder exists
            $folder = dirname($restoreToFile);
            if (!file_exists($folder)) {
                @mkdir($folder, 0755, true);
            }

            $content = file_get_contents($downloadUrl);

            if ($content && file_exists($folder) && file_put_contents($restoreToFile, $content)) {
                // Set correct permissions @ for crappy servers
                @chmod($restoreToFile, 0644);

                // Update the cache database tables so we dont have to run a new audit right away
                $sql = "INSERT INTO `bf_files` 
                (`id`, `filewithpath`, `fileperms`, `filemtime`, `toggler`, `currenthash`, `lasthash`, `iscorefile`, `hashfailed`, `hashchanged`, `hacked`, `suspectcontent`, `falsepositive`, `mailer`, `uploader`, `encrypted`, `queued`, `size`)
                VALUES
                (NULL, '%s', '0644', '%s', NULL, '%s', '%s', 1, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, 0, %s)";

                $sql = sprintf($sql, $file->filewithpath, time(), md5_file($restoreToFile), md5_file($restoreToFile), filesize($restoreToFile));
                $this->_db->setQuery($sql);
                $this->_db->query();

                ++$restored;
            } else {
                ++$notRestored;
            }
        }

        bfEncrypt::reply('success', array(
            'total'       => count($files),
            'restored'    => $restored,
            'notrestored' => $notRestored,
        ));
    }

    private function getMissingCoreFiles($internal = false)
    {
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;
        if (!$limitstart) {
            $limitstart = 0;
        }
        if (!$limit) {
            $limit = '9999999999999999';
        }

        $sql = " FROM `bf_core_hashes`
                    WHERE filewithpath NOT IN (
                        SELECT filewithpath from bf_files
                    )
                    AND filewithpath NOT LIKE '/installation/%'
                    AND filewithpath != '/robots.txt.dist'
                    AND filewithpath != '/administrator/manifests/packages/pkg_weblinks.xml'
                    AND filewithpath != '/'
                    AND filewithpath != '/robots.txt.dist'
                    AND filewithpath != '/web.config.txt'
                    AND filewithpath != '/joomla.xml'
                    AND filewithpath != '/build.xml'
                    AND filewithpath != '/LICENSE.txt'
                    AND filewithpath != '/README.txt'
                    AND filewithpath != '/htaccess.txt'
                    AND filewithpath != '/LICENSES.php'
                    AND filewithpath != '/configuration.php-dist'
                    AND filewithpath != '/CHANGELOG.php'
                    AND filewithpath != '/COPYRIGHT.php'
                    AND filewithpath != '/CREDITS.php'
                    AND filewithpath != '/INSTALL.php'
                    AND filewithpath != '/LICENSE.php'
                    AND filewithpath != '/CONTRIBUTING.md'
                    AND filewithpath != '/phpunit.xml.dist'
                    AND filewithpath != '/.drone.yml'
                    AND filewithpath != '/README.md'
                    AND filewithpath != '/.travis.yml'
                    AND filewithpath != '/travisci-phpunit.xml'
                    AND filewithpath != '/images/banners/osmbanner1.png'
                    AND filewithpath != '/images/banners/osmbanner2.png'
                    AND filewithpath != '/images/banners/shop-ad-books.jpg'
                    AND filewithpath != '/images/banners/shop-ad.jpg'
                    AND filewithpath != '/images/banners/white.png'
                    AND filewithpath != '/images/headers/blue-flower.jpg'
                    AND filewithpath != '/images/headers/maple.jpg'
                    AND filewithpath != '/images/headers/raindrops.jpg'
                    AND filewithpath != '/images/headers/walden-pond.jpg'
                    AND filewithpath != '/images/headers/windows.jpg'
                    AND filewithpath != '/images/joomla_black.gif'
                    AND filewithpath != '/images/joomla_black.png'
                    AND filewithpath != '/images/joomla_green.gif'
                    AND filewithpath != '/images/joomla_logo_black.jpg'
                    AND filewithpath != '/images/powered_by.png'
                    AND filewithpath != '/images/sampledata/fruitshop/apple.jpg'
                    AND filewithpath != '/images/sampledata/fruitshop/bananas_2.jpg'
                    AND filewithpath != '/images/sampledata/fruitshop/fruits.gif'
                    AND filewithpath != '/images/sampledata/fruitshop/tamarind.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/180px_koala_ag1.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/180px_wobbegong.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/200px_phyllopteryx_taeniolatus1.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/220px_spottedquoll_2005_seanmcclean.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/789px_spottedquoll_2005_seanmcclean.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/800px_koala_ag1.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/800px_phyllopteryx_taeniolatus1.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/800px_wobbegong.jpg'
                    AND filewithpath != '/images/sampledata/parks/banner_cradle.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/120px_pinnacles_western_australia.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/120px_rainforest_bluemountainsnsw.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/180px_ormiston_pound.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/250px_cradle_mountain_seen_from_barn_bluff.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/727px_rainforest_bluemountainsnsw.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/800px_cradle_mountain_seen_from_barn_bluff.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/800px_ormiston_pound.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/800px_pinnacles_western_australia.jpg'
                    AND filewithpath != '/images/sampledata/parks/parks.gif' ORDER BY filewithpath DESC ";

        $limitIt = 'LIMIT '.(int) $limitstart.', '.$limit;
        $this->_db->setQuery('SELECT * '.$sql.$limitIt);
        $files = $this->_db->LoadObjectList();

        foreach ($files as $k => $file) {
            if (file_exists(JPATH_BASE.$file->filewithpath)) {
                unset($files[$k]);
            }
        }

        if (true === $internal) {
            return $files;
        }

        $this->_db->setQuery('SELECT count(*) '.$sql.$limitIt);
        $count = $this->_db->loadResult();

        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    /**
     * Tool57
     * Delete files which have zero bytes (no content) as they just litter the webspace
     * and run up inode counts. Joomla doesnt rely on zero byte files, we have seen "other hack cleanup companies"
     * litter the webspace with zero byte files and so this tool deletes those too.
     */
    private function deleteZerobyteFiles()
    {
        $sql = 'SELECT filewithpath FROM bf_files WHERE size = 0';
        $this->_db->setQuery($sql);
        $files = $this->_db->LoadObjectList();

        $filesDeleted = array();
        $count        = 0;

        foreach ($files as $file) {
            $fullFilePath = JPATH_BASE.$file->filewithpath;
            if (@unlink($fullFilePath)) {
                ++$count;
                $filesDeleted[] = $file->filewithpath;

                $sql = sprintf('DELETE FROM bf_files WHERE filewithpath = " % s"', $file->filewithpath);
                $this->_db->setQuery($sql);
                $this->_db->query();
            }
        }

        bfEncrypt::reply('success', array(
            'files' => $filesDeleted,
            'total' => $count,
        ));
    }

    /**
     * @param bool $internal
     *
     * @return array|mixed
     */
    private function getEncrypted($internal = false)
    {
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;
        if (!$limitstart) {
            $limitstart = 0;
        }
        if (!$limit) {
            $limit = '9999999999999999';
        }

        $sql = 'SELECT * FROM bf_files WHERE encrypted = 1 ORDER BY filemtime DESC LIMIT '.(int) $limitstart.', '.$limit;
        $this->_db->setQuery($sql);
        $files = $this->_db->LoadObjectList();

        if (true === $internal) {
            return $files;
        }

        $this->_db->setQuery('SELECT count(*) FROM bf_files WHERE encrypted = 1');
        $count = $this->_db->loadResult();

        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    /**
     * @param bool $internal
     *
     * @return JUser|mixed|object
     */
    private function getUser($internal = false)
    {
        switch ($this->_dataObj->searchfield) {
            case 'username':
                $sql = "SELECT * FROM #__users WHERE username = '%s'";
                $sql = sprintf($sql, $this->_dataObj->searchvalue);
                $this->_db->setQuery($sql);
                $row = $this->_db->loadObject();
                break;
            case 'id':
                $row = new JUser();
                $row->load((int) $this->_dataObj->searchvalue);
                break;
        }

        if ($row->id) {
            // NEVER let the users password leave the remote site
            $row->password = '**REMOVED**';
        }

        if (true === $internal) {
            return $row;
        }

        bfEncrypt::reply('success', array(
            'user' => $row,
        ));
    }

    /**
     * remove £live_site from the configuration.php.
     *
     * @throws exception Exception
     */
    private function removeLiveSite()
    {
        // Require more complex methods for dealing with files
        require 'bfFilesystem.php';

        try {
            $config = JFactory::getConfig();

            if (version_compare(JVERSION, '3.0', 'ge')) {
                $config->set('live_site', '');
            } else {
                $config->setValue('config.live_site', '');
            }

            $newConfig = $config->toString('PHP', array(
                'class'      => 'JConfig',
                'closingtag' => false,
            ));

            // On some occasions, Joomla! 1.6 ignores the configuration and
            // produces "class c". Let's fix this!
            $newConfig = str_replace('class c {', 'class JConfig {', $newConfig);
            $newConfig = str_replace('namespace c;', '', $newConfig);

            // Try to write out the configuration.php
            $filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'configuration.php';
            $result   = Bf_Filesystem::_write($filename, $newConfig);
            if (false !== $result) {
                bfEncrypt::reply('success', array());
            } else {
                bfEncrypt::reply(bfReply::ERROR, array(
                    'msg' => 'Could Not Save Config',
                ));
            }
        } catch (Exception $e) {
            bfEncrypt::reply(bfReply::ERROR, array(
                'msg' => $e->getMessage(),
            ));
        }
    }

    /**
     * set the log_path and tmp_path to sane defaults.
     *
     * @throws exception Exception
     */
    private function setLogTmpPaths()
    {
        // Require more complex methods for dealing with files
        require 'bfFilesystem.php';

        try {
            // sane and recommended defaults
            $logpath = JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator/logs';
            $tmpath  = JPATH_ROOT.DIRECTORY_SEPARATOR.'tmp';

            // force creation and set sane permissions
            @mkdir($logpath);
            @mkdir($tmpath);
            @chmod($logpath, 0755);
            @chmod($tmpath, 0755);

            $config = JFactory::getConfig();

            if (version_compare(JVERSION, '3.0', 'ge')) {
                $config->set('log_path', $logpath);
                $config->set('tmp_path', $tmpath);
            } else {
                $config->setValue('config.log_path', $logpath);
                $config->setValue('config.tmp_path', $tmpath);
            }

            $newConfig = $config->toString('PHP', array(
                'class'      => 'JConfig',
                'closingtag' => false,
            ));

            // On some occasions, Joomla! 1.6 ignores the configuration and
            // produces "class c". Let's fix this!
            $newConfig = str_replace('class c {', 'class JConfig {', $newConfig);
            $newConfig = str_replace('namespace c;', '', $newConfig);

            // Try to write out the configuration.php
            $filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'configuration.php';
            $result   = Bf_Filesystem::_write($filename, $newConfig);
            if (false !== $result) {
                bfEncrypt::reply('success', array(
                    'log_path'    => $logpath,
                    'tmp_path'    => $tmpath,
                    'config_file' => $filename,
                ));
            } else {
                bfEncrypt::reply(bfReply::ERROR, array(
                    'msg' => 'Could Not Save Config',
                ));
            }
        } catch (Exception $e) {
            bfEncrypt::reply(bfReply::ERROR, array(
                'msg' => $e->getMessage(),
            ));
        }
    }

    /**
     * Enable SEF and SEF Rewrite.
     */
    private function setSEFConfig()
    {
        // Require more complex methods for dealing with files
        require 'bfFilesystem.php';

        try {
            $config = JFactory::getConfig();

            // Our sane defaults
            $sef         = 1;
            $sef_rewrite = 1;
            $sef_suffix  = 0;

            if (version_compare(JVERSION, '3.0', 'ge')) {
                $config->set('sef', $sef);
                $config->set('sef_rewrite', $sef_rewrite);
                $config->set('sef_suffix', $sef_suffix);
            } else {
                $config->setValue('config.sef', $sef);
                $config->setValue('config.sef_rewrite', $sef_rewrite);
                $config->setValue('config.sef_suffix', $sef_suffix);
            }

            $newConfig = $config->toString('PHP', array(
                'class'      => 'JConfig',
                'closingtag' => false,
            ));

            // On some occasions, Joomla! 1.6 ignores the configuration and
            // produces "class c". Let's fix this!
            $newConfig = str_replace('class c {', 'class JConfig {', $newConfig);
            $newConfig = str_replace('namespace c;', '', $newConfig);

            // Try to write out the configuration.php
            $filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'configuration.php';
            $result   = Bf_Filesystem::_write($filename, $newConfig);
            if (false !== $result) {
                bfEncrypt::reply('success', $this->getSEFConfig());
            } else {
                bfEncrypt::reply(bfReply::ERROR, array(
                    'msg' => 'Could Not Save Config',
                ));
            }
        } catch (Exception $e) {
            bfEncrypt::reply(bfReply::ERROR, array(
                'msg' => $e->getMessage(),
            ));
        }
    }

    /**
     * Get the settings for the SEF from Joomla Global Config.
     *
     * public $sef = '1';
     * public $sef_rewrite = '0';
     * public $sef_suffix = '0';
     */
    private function getSEFConfig()
    {
        $config = JFactory::getConfig();

        if (version_compare(JVERSION, '3.0', 'ge')) {
            $data = array(
                'sef'         => $config->get('sef'),
                'sef_rewrite' => $config->get('sef_rewrite'),
                'sef_suffix'  => $config->get('sef_suffix'),
            );
        } else {
            $data = array(
                'sef'         => $config->getValue('config.sef'),
                'sef_rewrite' => $config->getValue('config.sef_rewrite'),
                'sef_suffix'  => $config->getValue('config.sef_suffix'),
            );
        }

        bfEncrypt::reply('success', $data);
    }

    /**
     * Set Cookie Settings right.
     */
    private function setCookieSettings()
    {
        // Require more complex methods for dealing with files
        require 'bfFilesystem.php';

        try {
            $config = JFactory::getConfig();

            if (version_compare(JVERSION, '3.0', 'ge')) {
                $config->set('cookie_domain', '');
                $config->set('cookie_path', '');
            } else {
                $config->setValue('config.cookie_domain', '');
                $config->setValue('config.cookie_path', '');
            }

            $newConfig = $config->toString('PHP', array(
                'class'      => 'JConfig',
                'closingtag' => false,
            ));

            // On some occasions, Joomla! 1.6 ignores the configuration and
            // produces "class c". Let's fix this!
            $newConfig = str_replace('class c {', 'class JConfig {', $newConfig);
            $newConfig = str_replace('namespace c;', '', $newConfig);

            // Try to write out the configuration.php
            $filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'configuration.php';
            $result   = Bf_Filesystem::_write($filename, $newConfig);
            if (false !== $result) {
                bfEncrypt::reply('success', $this->getCookieSettings());
            } else {
                bfEncrypt::reply(bfReply::ERROR, array(
                    'msg' => 'Could Not Save Config',
                ));
            }
        } catch (Exception $e) {
            bfEncrypt::reply(bfReply::ERROR, array(
                'msg' => $e->getMessage(),
            ));
        }
    }

    /**
     * Get the settings for the cookie from config.
     *
     * public $cookie_domain
     * public $cookie_path
     */
    private function getCookieSettings()
    {
        $config = JFactory::getConfig();

        if (version_compare(JVERSION, '3.0', 'ge')) {
            $data = array(
                'cookie_domain' => $config->get('cookie_domain'),
                'cookie_path'   => $config->get('cookie_path'),
            );
        } else {
            $data = array(
                'cookie_domain' => $config->getValue('config.cookie_domain'),
                'cookie_path'   => $config->getValue('config.cookie_path'),
            );
        }

        bfEncrypt::reply('success', $data);
    }

    /**
     * @throws exception Exception
     */
    private function setDbPrefix()
    {
        // Require more complex methods for dealing with files
        require 'bfFilesystem.php';

        $prefix = $this->_dataObj->prefix;
        try {
            $prefix = $this->_validateDbPrefix($prefix);

            /**
             * Performs the actual schema change.
             *
             * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
             * @license   GNU General Public License version 3, or later
             *
             * @param $prefix string
             *                The new prefix
             *
             * @return bool False if the schema could not be changed
             */
            $config = JFactory::getConfig();
            if (version_compare(JVERSION, '3.0', 'ge')) {
                $oldprefix = $config->get('dbprefix', '');
                $dbname    = $config->get('db', '');
            } else {
                $oldprefix = $config->getValue('config.dbprefix', '');
                $dbname    = $config->getValue('config.db', '');
            }

            $db  = $this->_db;
            $sql = "SHOW TABLES WHERE `Tables_in_{$dbname}` like '{$oldprefix}%'";
            $db->setQuery($sql);

            if (version_compare(JVERSION, '3.0', 'ge')) {
                $oldTables = $db->loadColumn();
            } else {
                $oldTables = $db->loadResultArray();
            }

            if (empty($oldTables)) {
                throw new Exception('Could not find any tables with the old prefix to change to the new prefix');
            }

            foreach ($oldTables as $table) {
                $newTable = $prefix.substr($table, strlen($oldprefix));
                $sql      = "RENAME TABLE `$table` TO `$newTable`";
                $db->setQuery($sql);
                if (!$db->query()) {
                    // Something went wrong; I am pulling the plug and hope for
                    // the best
                    throw new Exception('Something went wrong; I am pulling the plug and hope for the best - Contact our support URGENTLY');
                }
            }

            /**
             * Updates the configuration.php file with the given prefix.
             *
             * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
             * @license   GNU General Public License version 3, or later
             *
             * @param $prefix string
             *                The prefix to write to the configuration.php file
             *
             * @return bool False if writing to the file was not possible
             */
            // Load the configuration and replace the db prefix
            $config = JFactory::getConfig();
            if (version_compare(JVERSION, '3.0', 'ge')) {
                $oldprefix = $config->get('dbprefix', $prefix);
            } else {
                $oldprefix = $config->getValue('config.dbprefix', $prefix);
            }
            if (version_compare(JVERSION, '3.0', 'ge')) {
                $config->set('dbprefix', $prefix);
            } else {
                $config->setValue('config.dbprefix', $prefix);
            }

            $newConfig = $config->toString('PHP', array(
                'class'      => 'JConfig',
                'closingtag' => false,
            ));

            // On some occasions, Joomla! 1.6 ignores the configuration and
            // produces "class c". Let's fix this!
            $newConfig = str_replace('class c {', 'class JConfig {', $newConfig);
            $newConfig = str_replace('namespace c;', '', $newConfig);

            if (version_compare(JVERSION, '3.0', 'ge')) {
                $config->set('dbprefix', $oldprefix);
            } else {
                $config->setValue('config.dbprefix', $oldprefix);
            }

            // Try to write out the configuration.php
            $filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'configuration.php';
            $result   = Bf_Filesystem::_write($filename, $newConfig);
            if (false !== $result) {
                bfEncrypt::reply('success', array(
                    'prefix' => $prefix,
                ));
            } else {
                bfEncrypt::reply(bfReply::ERROR, array(
                    'msg' => 'Could Not Save Config',
                ));
            }
        } catch (Exception $e) {
            bfEncrypt::reply(bfReply::ERROR, array(
                'msg' => $e->getMessage(),
            ));
        }
    }

    /**
     * Validates a prefix.
     * The prefix must be 3-6 lowercase characters followed by
     * an underscore and must not alrady exist in the current database. It must
     * also not be jos_ or bak_.
     *
     * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
     *
     * @param $prefix string
     *                The prefix to check
     *
     * @throws exception
     *
     * @return string bool validated prefix or false if the prefix is invalid
     */
    private function _validateDbPrefix($prefix)
    {
        // Check that the prefix is not jos_ or bak_
        if (('jos_' == $prefix) || ('bak_' == $prefix)) {
            throw new exception('Cannot be a standard prefix like jos_ or bak_');
        }

        // Check that we're not trying to reuse the same prefix
        $config = JFactory::getConfig();
        if (version_compare(JVERSION, '3.0', 'ge')) {
            $oldprefix = $config->get('dbprefix', '');
        } else {
            $oldprefix = $config->getValue('config.dbprefix', '');
        }
        if ($prefix == $oldprefix) {
            throw new exception('Cannot be the same as existing prefix');
        }

        // Check the length
        $pLen = strlen($prefix);
        if (($pLen < 4) || ($pLen > 6)) {
            throw new exception('Prefix must be between 4 and 6 chars');
        }

        // Check that the prefix ends with an underscore
        if ('_' != substr($prefix, -1)) {
            throw new exception('Prefix must end with an underscore');
        }

        // Check that the part before the underscore is lowercase letters
        $valid = preg_match('/[\w]_/i', $prefix);
        if (0 === $valid) {
            throw new exception('Prefix must be all lowercase');
        }

        // Turn the prefix into lowercase
        $prefix = strtolower($prefix);

        // Check if the prefix already exists in the database
        $db = $this->_db;
        if (version_compare(JVERSION, '3.0', 'ge')) {
            $dbname = $config->get('db', '');
        } else {
            $dbname = $config->getValue('config.db', '');
        }
        $sql = "SHOW TABLES WHERE `Tables_in_{$dbname}` like '{$prefix}%'";
        $db->setQuery($sql);
        if (version_compare(JVERSION, '3.0', 'ge')) {
            $existing_tables = $db->loadColumn();
        } else {
            $existing_tables = $db->loadResultArray();
        }
        if (count($existing_tables)) {
            // Sometimes we have false alerts, e.g. a prefix of dev_ will match
            // tables starting with dev15_ or dev16_
            $realCount = 0;
            foreach ($existing_tables as $check) {
                if (substr($check, 0, $pLen) == $prefix) {
                    ++$realCount;
                    break;
                }
            }
            if ($realCount) {
                throw new exception('Prefix already exists in the database');
            }
        }

        return $prefix;
    }

    /**
     * Update details of a user, including a hashed password.
     *
     * @todo Not sure this is ever called anymore (April 2018)
     */
    private function setUser()
    {
        $email    = $this->_dataObj->email;
        $pass     = $this->_dataObj->password;
        $username = $this->_dataObj->username;
        $where    = $this->_dataObj->where;

        if (!$email || !$pass || !$username || !$where) {
            bfEncrypt::reply('failure', array(
                'msg' => 'Not all required parts set',
            ));
        }

        $sql = 'UPDATE #__users SET username="%s", password="%s", email ="%s" WHERE %s';
        $sql = sprintf($sql, $username, $pass, $email, $where);
        $this->_db->setQuery($sql);
        $id = $this->_db->query();

        bfEncrypt::reply('success', array(
            'usersaved' => $id,
        ));
    }

    /**
     * @param bool $internal
     *
     * @return array|mixed
     */
    private function getErrorLogs($internal = false)
    {
        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;

        if (!$limitstart) {
            $limitstart = 0;
        }
        if (!$limit) {
            $limit = '9999999999999999'; //pah
        }

        $sql = "SELECT * FROM bf_files WHERE filewithpath LIKE '%error_log' ORDER BY filemtime DESC LIMIT ".(int) $limitstart.', '.$limit;
        $this->_db->setQuery($sql);
        $files = $this->_db->LoadObjectList();

        if (true === $internal) {
            return $files;
        }

        $this->_db->setQuery("SELECT count(*) FROM bf_files WHERE filewithpath LIKE '%error_log'");
        $count = $this->_db->loadResult();

        bfEncrypt::reply('success', array(
            'files' => $files,
            'total' => $count,
        ));
    }

    /**
     * Save the robots.txt file.
     */
    private function saveRobotsFile()
    {
        if (file_put_contents(JPATH_BASE.'/robots.txt', base64_decode($this->_dataObj->filecontents))) {
            bfEncrypt::reply('success', array(
                'msg' => 'File saved!',
            ));
        } else {
            bfEncrypt::reply('error', array(
                'msg' => 'File could not be saved!',
            ));
        }
    }

    /**
     * ok ok I know this looks bad, it probably is, but this allows a subscriber to edit a file on
     * myJoomla.com and then save the contents back to myJoomla.com.
     *
     * In order to get to this method a lot of security jumps have to have gone through already
     *
     * Its not as insecure as first seen... promise :)
     */
    private function saveFile()
    {
        require 'bfFilesystem.php';

        if (file_exists(JPATH_BASE.$this->_dataObj->filename) && !is_writable(JPATH_BASE.$this->_dataObj->filename)) {
            bfEncrypt::reply('error', array(
                'msg' => 'File not saved - as file is unwritable!',
            ));
        }

        if (!file_exists(dirname(JPATH_BASE.$this->_dataObj->filename))) {
            if (!@mkdir(dirname(JPATH_BASE.$this->_dataObj->filename), 0755, true)) {
                bfEncrypt::reply('error', array(
                    'msg' => 'File not saved - could not create folder paths!',
                ));
            }
        }

        $content = base64_decode($this->_dataObj->filecontents);

        if (!$content) {
            bfEncrypt::reply('error', array(
                'msg' => 'File not saved - as no content sent to save into the file!',
            ));
        }

        if (@Bf_Filesystem::_write(JPATH_BASE.$this->_dataObj->filename, $content)) {
            bfEncrypt::reply('success', array(
                'msg' => 'File saved!',
            ));
        } else {
            bfEncrypt::reply('error', array(
                'msg' => 'No idea why, but file content could not be saved to '.JPATH_BASE.$this->_dataObj->filename,
            ));
        }
    }

    /**
     * get the contents of the robots.txt only if it exists in the cache tables.
     */
    private function getRobotsFile()
    {
        $this->_db->setQuery('SELECT id from bf_files WHERE filewithpath = "/robots.txt"');
        $id = $this->_db->loadResult();
        if (!$id) {
            $obj               = new stdclass();
            $obj->filename     = '';
            $obj->filemd5      = md5('');
            $obj->filewithpath = '';
            $obj->filecontents = base64_encode('Could not load content for your own security, run a full audit before attempting to edit file content with myJoomla.com');
            $obj->filesize     = 0;
            $obj->basepath     = JPATH_BASE;
            $obj->writeable    = 0;

            bfEncrypt::reply('success', array(
                'file' => $obj,
            ));
        }
        $this->downloadfile($id);
    }

    /**
     * @param null $file_id
     */
    private function downloadfile($file_id = null)
    {
        if (null === $file_id) {
            $file_id = (int) $this->_dataObj->f;
        }

        $this->_db->setQuery('SELECT filewithpath from bf_files WHERE id = '.$file_id);

        $filename     = $this->_db->loadResult();
        $filewithpath = JPATH_BASE.$filename;

        if (file_exists($filewithpath)) {
            $contents              = file_get_contents($filewithpath);
            $contentsbase64_encode = base64_encode($contents);
            $obj                   = new stdclass();
            $obj->filename         = $filename;
            $obj->filemd5          = md5($contents);
            $obj->filewithpath     = $filewithpath;
            $obj->filecontents     = $contentsbase64_encode;
            $obj->filesize         = filesize($filewithpath);
            $obj->basepath         = JPATH_BASE;
            $obj->writeable        = is_writable($filewithpath);

            bfEncrypt::reply('success', array(
                'file' => $obj,
            ));
        } else {
            bfEncrypt::reply('error', array(
                'msg' => 'File No Longer Exists!',
            ));
        }
    }

    private function restorefile()
    {
        // Require more complex methods for dealing with files
        require 'bfFilesystem.php';

        // get the cached data on the file
        $this->_db->setQuery('SELECT filewithpath FROM bf_files WHERE id = '.$this->_dataObj->fileid);
        $file_to_restore_nopath = $this->_db->loadResult();
        $file_to_restore        = JPATH_BASE.$file_to_restore_nopath;

        $new_file_contents = base64_decode($this->_dataObj->filecontents);
        $new_md5           = md5($new_file_contents);
        if ($new_md5 !== $this->_dataObj->md5) {
            bfEncrypt::reply('failure', 'MD5 Check 1 Failed');
        }

        $this->_db->setQuery('SELECT hash FROM bf_core_hashes WHERE filewithpath = "'.$file_to_restore_nopath.'"');
        $core_md5 = $this->_db->loadResult();
        if ($core_md5 !== $this->_dataObj->md5) {
            bfEncrypt::reply('failure', 'MD5 Check 2 Failed');
        }

        $backup = file_get_contents($file_to_restore);
        Bf_Filesystem::_write($file_to_restore, $new_file_contents);

        if (md5_file($file_to_restore) !== $this->_dataObj->md5) {
            Bf_Filesystem::_write($file_to_restore, $backup);
            bfEncrypt::reply('failure', 'MD5 Check 3 Failed');
        }

        $this->_db->setQuery("UPDATE bf_files SET suspectcontent = 0 , hashfailed = 0 where filewithpath = '".$file_to_restore_nopath."'");
        $this->_db->query();

        bfEncrypt::reply('success', 'Restored OK');
    }

    private function checkFTPLayer()
    {
        $config     = JFactory::getApplication();
        $ftp_pass   = $config->getCfg('ftp_pass', '');
        $ftp_user   = $config->getCfg('ftp_user', '');
        $ftp_enable = $config->getCfg('ftp_enable', '');
        $ftp_host   = $config->getCfg('ftp_host', '');
        $ftp_root   = $config->getCfg('ftp_root', '');
        if ($ftp_pass || $ftp_user || '1' == $ftp_enable || $ftp_host || $ftp_root) {
            bfEncrypt::reply('success', 1);
        } else {
            bfEncrypt::reply('success', 0);
        }
    }

    private function disableFTPLayer()
    {
        $config      = JFactory::getApplication();
        $config_file = JPATH_BASE.'/configuration.php';

        $ftp_pass   = $config->getCfg('ftp_pass', '');
        $ftp_user   = $config->getCfg('ftp_user', '');
        $ftp_enable = $config->getCfg('ftp_enable', '');
        $ftp_host   = $config->getCfg('ftp_host', '');
        $ftp_root   = $config->getCfg('ftp_root', '');

        $config_txt = file_get_contents(JPATH_BASE.'/configuration.php');
        $config_txt = str_replace("\$ftp_enable = '1';", "\$ftp_enable = '0';", $config_txt);
        $config_txt = str_replace("\$ftp_pass = '".$ftp_pass."';", "\$ftp_pass = '';", $config_txt);
        $config_txt = str_replace("\$ftp_user = '".$ftp_user."';", "\$ftp_user = '';", $config_txt);
        $config_txt = str_replace("\$ftp_host = '".$ftp_host."';", "\$ftp_host = '';", $config_txt);
        $config_txt = str_replace("\$ftp_root = '".$ftp_root."';", "\$ftp_root = '';", $config_txt);

        @chmod($config_file, 0777);
        if (file_put_contents($config_file, $config_txt)) {
            @chmod($config_file, 0644);
            bfEncrypt::reply('success', 1);
        } else {
            bfEncrypt::reply('failure', 'Could not write configuration.php to '.$config_file);
        }
    }

    private function setFolderPermissions()
    {
        $fixed  = 0;
        $errors = 0;

        $this->_db->setQuery('SELECT id, folderwithpath from bf_folders WHERE folderinfo = "777"');
        $folders = $this->_db->loadObjectList();
        foreach ($folders as $folder) {
            if (@chmod(JPATH_BASE.$folder->folderwithpath, 0755)) {
                ++$fixed;
                $this->_db->setQuery('UPDATE bf_folders SET folderinfo = "755" WHERE id = "'.(int) $folder->id.'" AND folderinfo = "777"');
                $this->_db->query();
            } else {
                ++$errors;
            }
        }

        $this->_db->setQuery('SELECT count(*) FROM bf_folders WHERE folderinfo LIKE "%777%"');
        $folders_777 = $this->_db->LoadResult();

        $res           = new stdClass();
        $res->errors   = $errors;
        $res->fixed    = $fixed;
        $res->leftover = $folders_777;

        bfEncrypt::reply('success', $res);
    }

    /**
     * I do some sanity checks then enable .htaccess.
     */
    private function setHtaccess()
    {
        // Require more complex methods for dealing with files
        require 'bfFilesystem.php';

        // init bfDatabase

        // To
        $htaccess = JPATH_BASE.DIRECTORY_SEPARATOR.'.htaccess';

        // From
        $htaccesstxt = JPATH_BASE.DIRECTORY_SEPARATOR.'htaccess.txt';

        $res = new stdClass();
        if (file_exists($htaccess)) {
            $res->result = 'ERROR';
            $res->msg    = '.htaccess file already exists!';
            bfEncrypt::reply(bfReply::SUCCESS, $res);
        }

        if (!file_exists($htaccesstxt)) {
            $res->result = 'ERROR';
            $res->msg    = 'htaccess.txt file not found, cannot proceed';
            bfEncrypt::reply(bfReply::SUCCESS, $res);
        }

        // Test we are on apache
        if (!preg_match('/Apache|LiteSpeed/i', $_SERVER['SERVER_SOFTWARE'])) {
            $res->result = 'ERROR';
            $res->msg    = 'Server reported its not running Apache/LiteSpeed, but is running '.$_SERVER['SERVER_SOFTWARE'];
            bfEncrypt::reply(bfReply::SUCCESS, $res);
        }

        $didItWork = Bf_Filesystem::_write($htaccess, file_get_contents($htaccesstxt));

        if (false == $didItWork) {
            $res->result = 'ERROR';
            $res->msg    = 'Could not copy htaccess.txt to .htaccess';
            bfEncrypt::reply(bfReply::SUCCESS, $res);
        }

        $res->result = 'SUCCESS';
        $res->msg    = '.htaccess enabled! - Go and test your site!';
        bfEncrypt::reply(bfReply::SUCCESS, $res);
    }

    /**
     * I set the new database credentials in /configuration.php after some testing.
     */
    private function setDbCredentials()
    {
        // Require more complex methods for dealing with files
        require 'bfFilesystem.php';

        $password = $this->_dataObj->p;
        $user     = $this->_dataObj->u;

        $res = $this->testDbCredentials(true);
        if ('error' == $res->result) {
            bfEncrypt::reply(bfReply::ERROR, $res);
        }
        /**
         * Updates the configuration.php file with the given prefix
         * (some code from below).
         *
         * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
         * @license   GNU General Public License version 3, or later
         *
         * @param $prefix string
         *                The prefix to write to the configuration.php file
         *
         * @return bool False if writing to the file was not possible
         */
        // Load the configuration and replace the db prefix
        $config = JFactory::getConfig();
        if (version_compare(JVERSION, '3.0', 'ge')) {
            $olduser     = $config->get('user');
            $oldpassword = $config->get('password');
            $host        = $config->get('host');
        } else {
            $olduser     = $config->getValue('config.user');
            $oldpassword = $config->getValue('configpassword');
            $host        = $config->getValue('host');
        }

        if (version_compare(JVERSION, '3.0', 'ge')) {
            $config->set('user', $user);
            $config->set('password', $password);
        } else {
            $config->setValue('config.user', $user);
            $config->setValue('config.password', $password);
        }

        $newConfig = $config->toString('PHP', 'config', array(
            'class' => 'JConfig',
        ));

        // On some occasions, Joomla! 1.6 ignores the configuration and
        // produces "class c". Let's fix this!
        $newConfig = str_replace('class c {', 'class JConfig {', $newConfig);

        // Try to write out the configuration.php
        $filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'configuration.php';
        $result   = Bf_Filesystem::_write($filename, $newConfig);

        // reconnect db! to use new credentials
        $newConnectionOptions['user']     = $user;
        $newConnectionOptions['password'] = $password;
        $newConnectionOptions['host']     = $host;

        // make new db connection
        $db = JDatabase::getInstance($newConnectionOptions);
        $db->setQuery('SHOW DATABASES  where `Database` NOT IN ("test", "information_schema", "mysql")');
        $dbs_visible = count($db->loadObjectList());

        if (false !== $result) {
            bfEncrypt::reply('success', array(
                'msg'         => 'Config saved!',
                'dbs_visible' => $dbs_visible,
            ));
        } else {
            bfEncrypt::reply(bfReply::ERROR, array(
                'msg' => 'Could Not Save Config',
            ));
        }
    }

    /**
     * @param bool $internal
     *
     * @return stdClass
     */
    private function testDbCredentials($internal = false)
    {
        try {
            $config = JFactory::getApplication();

            $pass = $this->_dataObj->p;
            $user = $this->_dataObj->u;

            $host = $config->getCfg('host', '');
            $db   = $config->getCfg('db', '');

            if (function_exists('mysql_connect')) {
                $link = @mysql_connect($host, $user, $pass);
            } else {
                $link = @mysqli_connect($host, $user, $pass);
            }

            $msg = new stdClass();

            if (!$link) {
                if (function_exists('mysql_connect')) {
                    $msg->msg = trim(mysql_error().' Could not connect to mysql server with supplied credentials');
                } else {
                    $msg->msg = trim(mysqli_error().' Could not connect to mysql server with supplied credentials');
                }
                $msg->result = 'error';
                if (true === $internal) {
                    return $msg;
                }
                bfEncrypt::reply('success', $msg);
            }

            if (function_exists('mysql_connect')) {
                if (!@mysql_select_db($db, $link)) {
                    $msg->msg    = trim(mysql_error().' Mysql User exists, but has no access to the database');
                    $msg->result = 'error';
                    if (true === $internal) {
                        return $msg;
                    }
                    bfEncrypt::reply('success', $msg);
                }
            } else {
                if (!@mysqli_select_db($link, $db)) {
                    $msg->msg    = trim(mysqli_error().' Mysql User exists, but has no access to the database');
                    $msg->result = 'error';
                    if (true === $internal) {
                        return $msg;
                    }
                    bfEncrypt::reply('success', $msg);
                }
            }

            $msg->result = 'success';
            if (true === $internal) {
                return $msg;
            }

            bfEncrypt::reply('success', $msg);
        } catch (Exception $e) {
            bfEncrypt::reply('error', 'exception: '.$e->getMessage());
        }
    }

    private function getUpdatesCount()
    {
        require 'bfUpdates.php';

        $bfUpdates = new bfUpdates();

        bfEncrypt::reply('success', array(
            'count' => $bfUpdates->getupdates(true),
        ));
    }

    private function getUpdatesDetail()
    {
        @ob_start();
        @set_time_limit(60);
        require 'bfUpdates.php';

        $bfUpdates = new bfUpdates();
        $updates   = $bfUpdates->getupdates();

        @ob_clean();

        bfEncrypt::reply('success', array(
            'current_joomla_version' => JVERSION,
            'availableUpdates'       => $updates['updates'],
            'updateSites'            => $updates['sites'],
        ));
    }

    /**
     * Fix Db Schema version in the db.
     *
     * @since 20130929
     */
    private function fixDbSchema()
    {
        require JPATH_ADMINISTRATOR.'/components/com_installer/models/database.php';
        $model = new InstallerModelDatabase();
        $model->fix();

        $changeSet = $model->getItems();
        bfEncrypt::reply('success', array(
            'latest'        => $changeSet->getSchema(),
            'current'       => $model->getSchemaVersion(),
            'schema_errors' => $model->getItems()->check(),
        ));
    }

    /**
     * Return the DB schema.
     *
     * @since 20130929
     */
    private function getDbSchemaVersion()
    {
        require JPATH_ADMINISTRATOR.'/components/com_installer/models/database.php';
        $model     = new InstallerModelDatabase();
        $changeSet = $model->getItems();
        bfEncrypt::reply('success', array(
            'latest'        => $changeSet->getSchema(),
            'current'       => $model->getSchemaVersion(),
            'schema_errors' => $model->getItems()
                ->check(),
        ));
    }

    private function checkGoogleFile()
    {
        $found = false;
        $files = scandir(JPATH_BASE);
        foreach ($files as $file) {
            if (preg_match('/google.*\.html/', $file)) {
                $found = true;
            }
        }
        bfEncrypt::reply('success', array(
            'found' => $found,
        ));
    }

    private function toggleOnline()
    {
        return $this->_setConfigParam('offline', $this->_dataObj->status, 'int');
    }

    /**
     * Generic function for updating the configuration.php file.
     *
     * @param $param string
     * @param $value string|int
     */
    private function _setConfigParam($param, $value, $type = 'int')
    {
        // Require more complex methods for dealing with files
        require 'bfFilesystem.php';

        if ('int' == $type && !is_int($value)) {
            if ('true' == $value) {
                $value = 1;
            } elseif ('false' == $value) {
                $value = 0;
            } else {
                $value = 0;
            }
        }

        $config = JFactory::getConfig();

        if (version_compare(JVERSION, '3.0', 'ge')) {
            $config->set($param, $value);
        } else {
            $config->setValue('config.'.$param, $value);
        }

        $newConfig = $config->toString('PHP', array(
            'class' => 'JConfig',
        ));

        /**
         * On some occasions, Joomla! 1.6+ ignores the configuration and
         * produces "class c". Let's fix this!
         */
        $newConfig = str_replace('class c {', 'class JConfig {', $newConfig);
        $newConfig = str_replace('namespace c;', '', $newConfig);

        // Set the correct location of the file
        $filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'configuration.php';

        // Try to write out the configuration.php
        $result = Bf_Filesystem::_write($filename, $newConfig);

        if (false !== $result) {
            bfEncrypt::reply('success', array(
                $param => $value,
            ));
        } else {
            bfEncrypt::reply(bfReply::ERROR, array(
                'msg' => 'Could Not Save Config value for '.$param,
            ));
        }
    }

    private function toggleCache()
    {
        return $this->_setConfigParam('caching', $this->_dataObj->status, 'int');
    }

    private function getOfflineStatus()
    {
        bfEncrypt::reply('success', array(
            'offline' => JFactory::getApplication()->getCfg('offline'),
        ));
    }

    private function getCacheStatus()
    {
        bfEncrypt::reply('success', array(
            'caching' => JFactory::getApplication()->getCfg('caching'),
        ));
    }

    /**
     * Install an extension from Url.
     */
    private function doExtensionInstallFromUrl()
    {
        ob_start();
        // Load up as much of Joomla as we need
        require 'bfExtensions.php';
        $ext = new bfExtensions($this->_dataObj);
        $ext->installExtensionFromUrl();
    }

    private function doExtensionUpgrade()
    {
        ob_start();

        // Load up as much of Joomla as we need
        require 'bfExtensions.php';

        $app = JFactory::getApplication('Myjoomla');

        // Support crappy extensions like OSMap that implement their own license manager via plugins
        JPluginHelper::importPlugin('system');

        // init reply to myJoomla.com
        $result             = array();
        $result['messages'] = array();

        // which row in the _updates table should we use
        $this->_db->setQuery('SELECT update_id from #__updates WHERE extension_id = "'.$this->_dataObj->eid.'"');
        $extension_row_id = $this->_db->loadResult();

        // Do the update
        $ext              = new bfExtensions();
        $result['result'] = $ext->doUpdate($extension_row_id);

        // Grab any error messages

        $result['messages'] = $app->getMessageQueue();

        // translate messages
        $lang = JFactory::getLanguage();
        $lang->load('com_installer', JPATH_ADMINISTRATOR, 'en-GB', true);
        $lang->load('lib_joomla', JPATH_ADMINISTRATOR, 'en-GB', true);

        if (count($result['messages'])) {
            foreach ($result['messages'] as &$msg) {
                $msg['message'] = JText::_($msg['message']);
            }
        }

        bfEncrypt::reply('success', array(
            'result' => $result,
        ));
    }

    private function checkAkeebaOutputDirectory()
    {
        try {
            // If using PHP 5.2 then ABORT as Akeeba stuff needs newer PHP version
            if (version_compare(PHP_VERSION, '5.3.0', '<')) {
                throw new Exception('PHP version below 5.3.0 so Akeeba Will Not Work!');
            } else {
                require 'bfPHPFiveThreePlusOnly.php';
            }

            // Check Akeeba Installed - Prerequisite
            if (!file_exists(JPATH_SITE.'/libraries/f0f/include.php')
                || !file_exists(JPATH_SITE.'/administrator/components/com_akeeba/engine/Factory.php')
                || !file_exists(JPATH_SITE.'/administrator/components/com_akeeba/engine/serverkey.php')
            ) {
                bfEncrypt::reply('success', array(
                    'paths' => array(),
                ));
            }

            $returnData = array();

            if (!defined('AKEEBAENGINE')) {
                define('AKEEBAENGINE', 1);
            }

            require_once JPATH_SITE.'/libraries/f0f/include.php';
            require_once JPATH_SITE.'/administrator/components/com_akeeba/engine/Factory.php';

            $serverKeyFile = JPATH_BASE.'/administrator/components/com_akeeba/engine/serverkey.php';
            if (!defined('AKEEBA_SERVERKEY') && file_exists($serverKeyFile)) {
                include $serverKeyFile;
            }

            // Get the list of profiles
            $profileList = F0FModel::getTmpInstance('Profiles', 'AkeebaModel')->getProfilesList();

            // for each profile
            foreach ($profileList as $config) {
                // if encrypted
                if ('###AES128###' == substr($config->configuration, 0, 12)) {
                    $php53 = new bfPHPFiveThreePlusOnly();

                    $config->configuration = $php53->getAkeebaConfig($config->configuration);
                }

                // Convert ini to useable array
                $data = parse_ini_string($config->configuration, true);

                // find the folder
                $dir = $data['akeeba']['basic.output_directory'];

                $returnData[] = array('path' => $dir,
                    'is_writable'            => is_writable($dir),
                    'file_exists'            => file_exists($dir), );
            }

            bfEncrypt::reply('success', array(
                'paths' => $returnData,
            ));
        } catch (Exception $e) {
            bfEncrypt::reply('error', array(
                'msg' => $e->getMessage(),
            ));
        }
    }

    /**
     * return a value from the config.
     */
    private function getDebugMode()
    {
        $config = JFactory::getConfig();

        $data = array(
            'debug' => $config->get('debug'),
        );

        bfEncrypt::reply('success', array(
            'debug' => $data,
        ));
    }

    /**
     * set a value to the config.
     */
    private function setDebugMode()
    {
        return $this->_setConfigParam('debug', 'false', 'int');
    }

    /**
     * return a value from the config.
     */
    private function getErrorReporting()
    {
        $config = JFactory::getConfig();

        $data = array(
            'error_reporting' => $config->get('error_reporting'),
        );

        bfEncrypt::reply('success', array(
            'error_reporting' => $data,
        ));
    }

    /**
     * set a value to the config.
     */
    private function setErrorReporting()
    {
        return $this->_setConfigParam('error_reporting', 'none', 'string');
    }

    /**
     * return the main configuration.php without sensitive information
     * like passwords.
     */
    private function getJoomlaLogTmpConfig()
    {
        $config = JFactory::getConfig();

        $data = array(
            'log_path' => $config->get('log_path'),
            'tmp_path' => $config->get('tmp_path'),
            'base'     => JPATH_BASE,
        );

        bfEncrypt::reply('success', array(
            'paths' => $data,
        ));
    }

    /**
     * Get the User Actions Log.
     *
     * Joomla 3.9.0 implemented a User Action Log which basically replicates what we used to do
     * So now we load data from their log and not ours :-) Saves duplicating our efforts!
     */
    private function getActivityLog()
    {
        if (!class_exists('bfActivitylog')) {
            require_once 'bfActivitylog.php';
        }

        $inst = bfActivitylog::getInstance();
        $inst->ensureTableCreated();

        $limitstart = (int) $this->_dataObj->ls;
        $limit      = (int) $this->_dataObj->limit;

        if (!$limitstart) {
            $limitstart = 0;
        }
        if (!$limit) {
            $limit = '100';
        }

        if (version_compare(JVERSION, '3.9.0', '>=')) {
            // Manipulate the base Uri in the Joomla Stack to provide compatibility with some 3pd extensions like ACL Manager!
            try {
                $uri = \Joomla\CMS\Uri\Uri::getInstance();

                $reflection   = new \ReflectionClass($uri);
                $baseProperty = $reflection->getProperty('base');
                $baseProperty->setAccessible(true);
                $base           = $baseProperty->getValue();
                $base['prefix'] = $uri->toString(array('scheme', 'host'));
                $base['path']   = '/';
                $baseProperty->setValue($base);
            } catch (ReflectionException $e) {
            }

            JLoader::register('ActionlogsModelActionlogs', JPATH_ADMINISTRATOR.'/components/com_actionlogs/models/actionlogs.php');
            JLoader::register('ActionlogsHelper', JPATH_ADMINISTRATOR.'/components/com_actionlogs/helpers/actionlogs.php');

            $model = JModelLegacy::getInstance('Actionlogs', 'ActionlogsModel', array('ignore_request' => true));

            // Set the Start and Limit
            $model->setState('list.start', $limitstart);
            $model->setState('list.limit', $limit);
            $model->setState('list.ordering', 'a.id');
            $model->setState('list.direction', 'DESC');

            $rows = $model->getItems();

            // Load all language files needed
            ActionlogsHelper::loadActionLogPluginsLanguage();
            $lang = JFactory::getLanguage();
            $lang->load('com_privacy', JPATH_ADMINISTRATOR, null, false, true);
            $lang->load('plg_system_actionlogs', JPATH_ADMINISTRATOR, null, false, true);
            $lang->load('plg_system_privacyconsent', JPATH_ADMINISTRATOR, null, false, true);

            // manipulate data to push to myJoomla.com
            foreach ($rows as $row) {
                $row->what   = ActionlogsHelper::getHumanReadableLogMessage($row);
                $row->ip     = $row->ip_address;
                $row->when   = $row->log_date;
                $row->who_id = $row->user_id;
                $row->source = 'core_user_action_log';
            }
        } else {
            // Before Joomla 3.9.0
            $this->_db->setQuery('SELECT * from bf_activitylog ORDER by id DESC LIMIT '.$limitstart.', '.$limit);
            $rows = $this->_db->loadObjectList();
        }

        bfEncrypt::reply('success', $rows ?: array());
    }

    /**
     * enable/disable and get status of our plugin.
     */
    private function getBFPluginStatus()
    {
        switch ($this->_dataObj->action) {
            case 'enable':

                if (version_compare(JVERSION, '3.9.0', '>=')) {
                    $this->_db->setQuery("UPDATE `#__extensions` set enabled = 1 WHERE `name` = 'PLG_ACTIONLOG_JOOMLA'");
                    $this->_db->query();
                    $this->_db->setQuery("UPDATE `#__extensions` set enabled = 1 WHERE `name` = 'PLG_SYSTEM_ACTIONLOGS'");
                    $this->_db->query();
                }

                $this->_db->setQuery('UPDATE `#__extensions` SET enabled = 1 WHERE element = "bfnetwork"');
                $this->_db->query();
                break;
            case 'disable':
                $this->_db->setQuery('UPDATE `#__extensions` SET enabled = 0 WHERE element = "bfnetwork"');
                $this->_db->query();
                break;
        }

        $this->_db->setQuery('SELECT enabled FROM #__extensions WHERE element = "bfnetwork"');
        $result = $this->_db->loadResult();
        bfEncrypt::reply('success', $result);
    }

    /**
     * get the list of users that have a 32 char password hash - e.g md5.
     */
    private function getMD5PasswordUsers()
    {
        $this->_db->setQuery('SELECT id, username, name, password FROM #__users WHERE CHAR_LENGTH(password) = 32');
        $result = $this->_db->loadObjectList();
        bfEncrypt::reply('success', $result);
    }

    /**
     * Check the session gc plugin in Joomla 3.
     */
    private function setSessionGCStatus()
    {
        $this->_db->setQuery("update #__extensions set enabled = 1 where name = 'plg_system_sessiongc'");
        $this->_db->query();

        bfEncrypt::reply('success', array(
            'status' => $this->getSessionGCStatus(),
        ));
    }

    /**
     * Check the session gc plugin in Joomla 3.
     */
    private function getSessionGCStatus()
    {
        $res = 2;

        // Session GC
        $this->_db->setQuery("select count(*) from #__extensions where name = 'plg_system_sessiongc'");
        $hasSessionGcPlugin = $this->_db->LoadResult();

        if ($hasSessionGcPlugin) {
            $this->_db->setQuery("select enabled from #__extensions where name = 'plg_system_sessiongc'");
            $res = $this->_db->LoadResult();
        }

        bfEncrypt::reply('success', array(
            'status' => $res,
        ));
    }

    /**
     * Get the 2FA plugins.
     */
    private function enable2FAPlugins()
    {
        $this->_db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE `folder` = 'twofactorauth'");
        $this->_db->LoadResult();

        $this->get2FAPlugins();
    }

    /**
     * Get the 2FA plugins.
     */
    private function get2FAPlugins()
    {
        $this->_db->setQuery("SELECT * FROM `#__extensions` WHERE `folder` = 'twofactorauth'");
        $res = $this->_db->loadObjectList();

        bfEncrypt::reply('success', $res);
    }

    /**
     * set params from com_config without using a helper.
     */
    private function setAdminFilterFixed()
    {
        $this->_db->setQuery("SELECT `params` from #__extensions WHERE `element` = 'com_config'");
        $params                            = json_decode($this->_db->LoadResult());
        $params->filters->{7}->filter_type = 'BL';
        $this->_db->setQuery(sprintf("UPDATE #__extensions set `params` = '%s' WHERE `element` = 'com_config'", json_encode($params)));
        $this->_db->query();

        return $this->getAdminFilterFixed();
    }

    /**
     * Load params from com_config without using a helper.
     */
    private function getAdminFilterFixed()
    {
        $this->_db->setQuery("SELECT `params` from #__extensions WHERE element = 'com_config'");
        $params = json_decode($this->_db->LoadResult());

        bfEncrypt::reply('success', $params->filters->{7});
    }

    /**
     * set params from com_config without using a helper.
     */
    private function setPlaintextpasswords()
    {
        $this->_db->setQuery("SELECT `params` from #__extensions WHERE `element` = 'com_users'");
        $params               = json_decode($this->_db->LoadResult());
        $params->sendpassword = '0';
        $this->_db->setQuery(sprintf("UPDATE #__extensions set `params` = '%s' WHERE `element` = 'com_users'", json_encode($params)));
        $this->_db->query();

        $this->getPlaintextpasswords();
    }

    /**
     * Load params from com_config without using a helper.
     */
    private function getPlaintextpasswords()
    {
        $this->_db->setQuery("SELECT `params` from #__extensions WHERE element = 'com_users'");
        $params = json_decode($this->_db->LoadResult());

        bfEncrypt::reply('success', array('sendpassword' => $params->sendpassword));
    }

    /**
     * set params from com_content without using a helper.
     */
    private function setMailtofrienddisabled()
    {
        $this->_db->setQuery("SELECT `params` from #__extensions WHERE `element` = 'com_content'");
        $params                  = json_decode($this->_db->LoadResult());
        $params->show_email_icon = '0';
        $this->_db->setQuery(sprintf("UPDATE #__extensions set `params` = '%s' WHERE `element` = 'com_content'", json_encode($params)));
        $this->_db->query();

        $this->getMailtofrienddisabled();
    }

    /**
     * Load params from com_content without using a helper.
     */
    private function getMailtofrienddisabled()
    {
        $this->_db->setQuery("SELECT `params` from #__extensions WHERE element = 'com_content'");
        $params = json_decode($this->_db->LoadResult());

        bfEncrypt::reply('success', array('show_email_icon' => $params->show_email_icon));
    }

    /**
     * set params from com_templates without using a helper.
     */
    private function setTemplatePositionDisplay()
    {
        $this->_db->setQuery("SELECT `params` from #__extensions WHERE `element` = 'com_templates'");
        $params                             = json_decode($this->_db->LoadResult());
        $params->template_positions_display = '0';
        $this->_db->setQuery(sprintf("UPDATE #__extensions set `params` = '%s' WHERE `element` = 'com_templates'", json_encode($params)));
        $this->_db->query();

        $this->getTemplatePositionDisplay();
    }

    /**
     * Load params from com_templates without using a helper.
     */
    private function getTemplatePositionDisplay()
    {
        $this->_db->setQuery("SELECT `params` from #__extensions WHERE element = 'com_templates'");
        $params = json_decode($this->_db->LoadResult());

        bfEncrypt::reply('success', array('template_positions_display' => $params->template_positions_display));
    }

    /**
     * Get the configuration of the google recaptcha plugin and global config.
     */
    private function getCaptchaConfig()
    {
        $config = JFactory::getApplication();

        $this->_db->setQuery("SELECT enabled FROM #__extensions WHERE name ='plg_captcha_recaptcha'");
        $enabled = $this->_db->loadResult();

        $this->_db->setQuery("SELECT params FROM #__extensions WHERE name ='plg_captcha_recaptcha'");
        $keyed = $this->_db->loadResult();

        bfEncrypt::reply('success', array(
            'enabled'    => $enabled,
            'configured' => $config->getCfg('captcha', ''),
            'keys'       => json_decode($keyed),
        ));
    }

    /**
     * Set the configuration of the google recaptcha plugin and global config.
     */
    private function setCaptchaConfig()
    {
        $this->_db->setQuery(sprintf("UPDATE #__extensions 
        SET 
        enabled = 1,
        params = '{\"version\":\"2.0\",\"public_key\":\"%s\",\"private_key\":\"%s\",\"theme\":\"clean\",\"theme2\":\"light\",\"size\":\"normal\"}' 
        WHERE name ='plg_captcha_recaptcha'",
            $this->_dataObj->site_key,
            $this->_dataObj->secret_key
        ));
        $this->_db->query();

        $this->_setConfigParam('captcha', 'recaptcha', 'string');
    }

    /**
     * get the list of ACL Groups.
     */
    private function getGroups()
    {
        $this->_db->setQuery('select id, title from #__usergroups');

        bfEncrypt::reply('success', array(
            'groups' => $this->_db->loadObjectList(),
        ));
    }

    /**
     * get the list of super admins.
     */
    private function getSuperAdmins()
    {
        $this->_db->setQuery('select id, name, username from #__users as u
                        left join #__user_usergroup_map as m on u.id = m.user_id
                        where m.group_id = '.(int) $this->_dataObj->groupid);

        bfEncrypt::reply('success', array(
            'users' => $this->_db->loadObjectList(),
        ));
    }

    /**
     * 110
     * Identify Files That Existed In Last Audit, And Modified Before This Audit.
     */
    private function getModifiedfilessincelastaudit()
    {
        $limitstart = (int) $this->_dataObj->ls;
        $sort       = $this->_dataObj->s;

        if (!$sort) {
            $sort = 'filewithpath';
        }

        if (!in_array($sort, array('filewithpath', 'filemtime'))) {
            die('Invalid Sort');
        }

        if ('filemtime' === $sort) {
            $sort = 'filemtime DESC';
        }

        $limit = (int) $this->_dataObj->limit;

        // Set the query
        $this->_db->setQuery('SELECT new.id, new.iscorefile, new.filewithpath, new.filemtime, new.fileperms, new.`size`, new.iscorefile from bf_files  as new
                              LEFT JOIN bf_files_last as old ON old.filewithpath = new.filewithpath
                              WHERE old.currenthash != new.currenthash
                              ORDER BY '.$sort.'
                              LIMIT '.$limitstart.', '.$limit);

        // Get an object list of files
        $files = $this->_db->loadObjectList();

        // see how many files there are in total without a limit
        $sql = 'select count(*) from `bf_files` as new
                  LEFT JOIN bf_files_last as old ON old.filewithpath = new.filewithpath
                  WHERE old.currenthash != new.currenthash';

        $this->_db->setQuery($sql);
        $count = $this->_db->loadResult();

        // Only show files that still exist on the hard drive
        $existingFiles = array();
        foreach ($files as $k => $file) {
            if (file_exists(JPATH_BASE.$file->filewithpath)) {
                $existingFiles[] = $file;
            } else {
                $this->_db->setQuery(sprintf('DELETE FROM bf_files WHERE filewithpath = "%s"',
                    $file->filewithpath));
                $this->_db->query();

                --$count;
            }
        }

        // return an encrypted reply
        bfEncrypt::reply('success', array(
            'files' => $existingFiles,
            'total' => $count,
        ));
    }
}

// init this class
$securityController = new bfTools($dataObj);

// Run the tool method
$securityController->run();
