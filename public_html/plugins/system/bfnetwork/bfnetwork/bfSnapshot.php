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
 */

// require all we need to access Joomla API
require 'bfInitJoomla.php';
require_once 'bfActivitylog.php';

final class bfSnapshot
{
    public $_data;
    private $db;
    private $version;
    private $config;

    public function __construct()
    {
        $this->cleanOurCrap();

        // Ask Joomla to report config through its API
        $this->config = JFactory::getApplication('site');

        // Connect to the database
        $this->initDb();

        $session_save_path = @ini_get('session_save_path') ? ini_get('session_save_path') : '/tmp';

        $this->_data = array(
            'version'                    => $this->getJoomlaVersion(),
            'connectorversion'           => file_get_contents('./VERSION'),
            'php_version'                => PHP_VERSION,
            'php_disabled_functions'     => ini_get('disable_functions'),
            'display_errors'             => ini_get('display_errors'),
            'register_globals'           => (int) ini_get('register_globals'),
            'safe_mode'                  => (int) ini_get('safe_mode'),
            'file_uploads'               => (int) ini_get('file_uploads'),
            'magic_quotes_gpc'           => (int) ini_get('magic_quotes_gpc'),
            'magic_quotes_runtime'       => (int) ini_get('magic_quotes_runtime'),
            'session_autostart'          => (int) ini_get('session_autostart'),
            'gc_probability'             => (int) ini_get('session.gc_probability'),
            'mysql_version'              => $this->initDb(),
            'session_save_path'          => $session_save_path,
            'is_windows_host'            => (int) ('WIN' == substr(PHP_OS, 0, 3)) ? 1 : 0,
            'session_save_path_writable' => (int) is_writable($session_save_path),
            'db_prefix'                  => $this->config->getCfg('dbprefix', ''),
            'dbs_visible'                => $this->getVisibleDbsCount(),
            'db_user_is_root'            => (int) ('root' == $this->config->getCfg('user', '') ? 1 : 0),
            'db_bak_tables'              => (int) $this->hasBakTables(),
            'memory_limit'               => ini_get('memory_limit'),
            'has_installation_folders'   => (int) $this->hasInstallationFolders(),
            'site_debug_enabled'         => (int) $this->config->getCfg('debug') ? 1 : 0,
            'has_ftp_configured'         => (int) $this->checkFTPLayer(),
            'numberofsuperadmins'        => $this->getNumberOfSuperAdmins(),
            'adminusernames'             => $this->getAdminUserNameCount(),
            'neverloggedinusers'         => $this->getNeverLoggedInUsersCount(),
            'hasjce'                     => $this->hasExtensionWithNameInstalled('com_jce'),
            'hasakeebabackup'            => $this->hasExtensionWithNameInstalled('com_akeeba'),
            'site_offline'               => $this->config->getCfg('offline', ''),
            'cache_enabled'              => $this->config->getCfg('caching', ''),
            'sef_enabled'                => $this->config->getCfg('sef', ''),
            'tmplogfolderswritable'      => (int) $this->hastmplogfolderswritable(),
            'extensionupdatesavailable'  => null, // Now called in separate job was $this->hasUpdatesAvailable(),
            'defaulttemplateused'        => (int) $this->hasUsedDefaultTemplate(),
            'tpequalsone'                => $this->hastpequalsone(),
            'configsymlinked'            => (is_link(JPATH_BASE.'/configuration.php') ? 1 : 0),
            'kickstartseen'              => (file_exists(JPATH_BASE.'/kickstart.php') ? 1 : 0),
            'fpaseen'                    => (int) $this->fpaexists(),
            'userregistrationenabled'    => (int) JComponentHelper::getParams('com_users')->get('allowUserRegistration'),
            'has_root_htaccess'          => (int) (file_exists(JPATH_BASE.'/.htaccess') ? 1 : 0),
            'adminhtaccess'              => (int) (file_exists(JPATH_BASE.'/administrator/.htaccess') ? 1 : 0),
            'gzipenabled'                => (int) $this->config->getCfg('gzip', ''),
            'gcerrorreportingnone'       => (int) $this->getErrorReportingLevel(),
            'livesitevarset'             => strlen($this->config->getCfg('live_site')) > 1 ? 1 : 0,
            'cookiedomainpath'           => ($this->config->getCfg('cookie_path') || $this->config->getCfg('cookie_domain')) ? 1 : 0,
            'sessionlifetime'            => (int) $this->config->getCfg('lifetime'),
            'akeebabackupscount'         => (int) $this->getNumberOfAkeebaBackups(),
            'md5passwords'               => (int) $this->hasmd5passwords(),
            'tmplogfoldersdefaultpaths'  => (int) $this->hastmplogfoldersdefaultpaths(),
            'max_allowed_packet'         => (int) $this->getMaxAllowedPacket(),
            'jceversion'                 => $this->checkJCEVersion(),
            'fluff'                      => (int) $this->checkfluff(),
            'db_schema'                  => $this->checkdbschema(),
            'robots_blocks_media'        => (int) $this->checkRobotsBlocksMedia(),
            'server_hostname'            => function_exists('gethostname') ? gethostname() : php_uname('n'),
            'akeeba_dir_problems'        => $this->getAkeebaOutputDirectoryProblems(),
            'diskspace'                  => $this->getDiskSpace(),
            'eol_issues'                 => $this->testEOLIssues(),
            'hacked'                     => $this->checkIf100percentHackedOrNot(),
            'new_usertype'               => $this->getNewUserType(),
            'non2faadmins'               => $this->getNon2FaAdmins(),
            'users_hacked'               => $this->checkJoomlaUserHelperHack2016(),
            'sessiongcpublished'         => $this->getSessionGCStatus(),
            'twofactorenabled'           => $this->getTwoFactorPluginsEnabled(),
            'adminfilterfixed'           => $this->getAdminFilterFixed(),
            'plaintextpasswordsfixed'    => $this->getPlaintextpasswordsFixed(),
            'uploadsettingsfixed'        => $this->getUploadsettingsfixed(),
            'mailtofrienddisabled'       => $this->getMailtofrienddisabled(),
            'captchaenabled'             => $this->getCaptchaDetails(),
            'useractionlogenabled'       => (int) $this->getUseractionlogenabled(),
            'plgprivacyconsentenabled'   => (int) $this->getPrivacyConsentPluginEnabled(),
            'useractionlogiplogenabled'  => (int) $this->getActionLogsIPLoggingEnabled(),
            'systemlogrotationenabled'   => (int) $this->getSystemLogRotationEnabled(),
            'hasprivacypolicy'           => (int) $this->hasprivacypolicy(),
            'privacypendingremove'       => (int) $this->getPrivacypendingremove(),
            'privacycompletedexport'     => (int) $this->getPrivacycompletedexport(),
            'privacypendingexport'       => (int) $this->getPrivacypendingexport(),
            'privacycompletedremove'     => (int) $this->getPrivacycompletedremove(),
            'privacyoverdue'             => (int) $this->getPrivacyoverdue(),
            'privacyconfirmedremove'     => (int) $this->getPrivacyconfirmedremove(),
            'privacyconfirmedexport'     => (int) $this->getPrivacyconfirmedexport(),
            'enablepurge30days'          => (int) $this->getPurge30Days(),
        );
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

        $this->db->setQuery("SELECT params FROM `#__extensions` WHERE `name` = 'PLG_SYSTEM_ACTIONLOGS'");

        $params = $this->db->LoadResult();

        if ('{}' == $params) {
            return null;
        }

        $params = json_decode($params);

        return $params->logDeletePeriod;
    }

    public function getPrivacypendingremove()
    {
        if (version_compare(JVERSION, '3.9.0', '<')) {
            return false;
        }
        $this->db->setQuery("select count(*) from #__privacy_requests where status = 0 and request_type = 'remove'");

        return $this->db->LoadResult();
    }

    public function getPrivacyconfirmedremove()
    {
        if (version_compare(JVERSION, '3.9.0', '<')) {
            return false;
        }
        $this->db->setQuery("select count(*) from #__privacy_requests where status = 1 and request_type = 'remove'");

        return $this->db->LoadResult();
    }

    public function getPrivacyconfirmedexport()
    {
        if (version_compare(JVERSION, '3.9.0', '<')) {
            return false;
        }
        $this->db->setQuery("select count(*) from #__privacy_requests where status = 1 and request_type = 'export'");

        return $this->db->LoadResult();
    }

    public function getPrivacycompletedexport()
    {
        if (version_compare(JVERSION, '3.9.0', '<')) {
            return false;
        }
        $this->db->setQuery("select count(*) from #__privacy_requests where status = 2 and request_type = 'export'");

        return $this->db->LoadResult();
    }

    public function getPrivacypendingexport()
    {
        if (version_compare(JVERSION, '3.9.0', '<')) {
            return false;
        }
        $this->db->setQuery("select count(*) from #__privacy_requests where status = 0 and request_type = 'export'");

        return $this->db->LoadResult();
    }

    public function getPrivacycompletedremove()
    {
        if (version_compare(JVERSION, '3.9.0', '<')) {
            return false;
        }
        $this->db->setQuery("select count(*) from #__privacy_requests where status = 2 and request_type = 'remove'");

        return $this->db->LoadResult();
    }

    /**
     * Get the overdue requests.
     *
     * @return bool
     */
    public function getPrivacyoverdue()
    {
        if (version_compare(JVERSION, '3.9.0', '<')) {
            return false;
        }
        // Load the parameters.
        $params = \Joomla\CMS\Component\ComponentHelper::getComponent('com_privacy')->getParams();
        $notify = (int) $params->get('notify', 14);
        $now    = JFactory::getDate()->toSql();
        $period = '-'.$notify;

        $query = $this->db->getQuery(true)
            ->select('COUNT(*)');
        $query->from($this->db->quoteName('#__privacy_requests'));
        $query->where($this->db->quoteName('status').' = 1 ');
        $query->where($query->dateAdd($this->db->quote($now), $period, 'DAY').' > '.$this->db->quoteName('requested_at'));
        $this->db->setQuery($query);

        return $this->db->LoadResult();
    }

    /**
     * Joomla 3.9.0+ Check for system log rotation plugin.
     *
     * @return mixed
     */
    public function getSystemLogRotationEnabled()
    {
        if (version_compare(JVERSION, '3.9.0', '<')) {
            return false;
        }

        $this->db->setQuery("SELECT count(*) FROM `#__extensions` WHERE `name` = 'plg_system_logrotation' and enabled = 1");

        return $this->db->LoadResult();
    }

    /**
     * Joomla 3.9.0+ Check for action log ip logging enabled.
     *
     * @return mixed
     */
    public function hasprivacypolicy()
    {
        if (version_compare(JVERSION, '3.9.0', '<')) {
            return false;
        }

        $this->db->setQuery("SELECT params FROM `#__extensions` WHERE `name` = 'plg_system_privacyconsent'");

        $params = json_decode($this->db->LoadResult());

        return $params->privacy_article > 0 ? 1 : 0;
    }

    /**
     * Joomla 3.9.0+ Check for action log ip logging enabled.
     *
     * @return mixed
     */
    public function getActionLogsIPLoggingEnabled()
    {
        if (version_compare(JVERSION, '3.9.0', '<')) {
            return false;
        }

        $this->db->setQuery("SELECT params FROM `#__extensions` WHERE `name` = 'com_actionlogs'");

        $params = json_decode($this->db->LoadResult());

        return $params->ip_logging;
    }

    /**
     * Joomla 3.9.0+ Check for action log ip logging enabled.
     *
     * @return mixed
     */
    public function getUseractionlogenabled()
    {
        if (version_compare(JVERSION, '3.9.0', '<')) {
            return false;
        }

        $this->db->setQuery("SELECT count(*) FROM `#__extensions` WHERE (`name` = 'PLG_ACTIONLOG_JOOMLA' or `name` = 'PLG_SYSTEM_ACTIONLOGS') and enabled = 1");

        return 2 == $this->db->LoadResult() ? 1 : 0;
    }

    /**
     * Joomla 3.9.0+ Check for plg_privacy_actionlogs enabled.
     *
     * @return mixed
     */
    public function getPrivacyConsentPluginEnabled()
    {
        if (version_compare(JVERSION, '3.9.0', '<')) {
            return false;
        }

        $this->db->setQuery("SELECT count(*) FROM `#__extensions` WHERE `name` = 'plg_system_privacyconsent' and enabled = 1");

        return $this->db->LoadResult();
    }

    /**
     * Checks if the FTP Layer is in anyway configured.
     *
     * @return bool
     */
    private function checkFTPLayer()
    {
        $ftp_pass   = $this->config->getCfg('ftp_pass', '');
        $ftp_user   = $this->config->getCfg('ftp_user', '');
        $ftp_enable = $this->config->getCfg('ftp_enable', '');
        $ftp_host   = $this->config->getCfg('ftp_host', '');
        $ftp_root   = $this->config->getCfg('ftp_root', '');

        if ($ftp_pass || $ftp_user || '1' == $ftp_enable || $ftp_host || $ftp_root) {
            return true;
        }

        return false;
    }

    /**
     * Clean up old myJoomla.com files and features.
     */
    private function cleanOurCrap()
    {
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

        $fileContent = file_get_contents('../bfnetwork.php');
        if (!preg_match('/bfPlugin/', $fileContent)) {
            $fileContent = str_replace(array(
                "\n\n",
                '// For more details please contact Phil Taylor <phil@phil-taylor.com>',
                '// This is NOT a Joomla Extension or Plugin and is NOT designed for consumption within Joomla - yet :)', ), '', $fileContent);
            $fileContent = $fileContent."
/**
 * All our code is in the sub folder, as that is what is auto-upgraded
 * and fully maintained by the automated processes at myJoomla.com
 */
require 'bfnetwork/bfPlugin.php';";

            file_put_contents('../bfnetwork.php', $fileContent);
        }

        bfActivitylog::getInstance();

        // Soon we will enable this...
        //        $this->db = JFactory::getDBO();
        //        $this->db->setQuery('UPDATE #__extensions SET enabled = 0 where element = "bfnetwork"');
        //        $this->db->query();
    }

    /**
     * Init the Joomla db connection.
     */
    private function initDb()
    {
        $this->db = JFactory::getDBO();

        $dbVerString = '';

        if ('JDatabaseDriverMysqli' == get_class($this->db)) {
            $dbVerString = @mysqli_get_server_info($this->db->getConnection())->server_info;
        }

        if (!$dbVerString && 'JDatabaseDriverMysql' == get_class($this->db) && function_exists('mysql_get_server_info')) {
            $dbVerString = @mysql_get_server_info($this->db->getConnection());
        }

        if (!$dbVerString && method_exists($this->db, 'getConnection') && $this->db->getConnection()) {
            $dbVerString = $this->db->getConnection()->server_info;
        }

        if (!$dbVerString && function_exists('mysql_get_server_info')) {
            // crappy Joomla 1.5.x versions - I hat the @ supressor yeah yeah - but its CRAP!
            $dbVerString = @mysql_get_server_info($this->db->_resource);
        }

        return $dbVerString;
    }

    private function getJoomlaVersion()
    {
        $VERSION = new JVersion();

        // Store in our object for switching configs
        $this->version = $VERSION->getShortVersion();

        return $VERSION->getShortVersion();
    }

    /**
     * How many databases can I see?
     *
     * We need to reconnect again to the db so we are ot going through the Joomla
     * DB Layer because it just crashes too far up the stack for us to catch the
     * exception
     *
     * @return int
     */
    private function getVisibleDbsCount()
    {
        $count = 0;

        try {
            // Create correct commands based on how old and crap the server is!
            switch ($this->config->getCfg('dbtype')) {
                default:
                case 'mysqli':

                    $link = mysqli_connect($this->config->getCfg('host'), $this->config->getCfg('user'), $this->config->getCfg('password'));
                    if (!$link) {
                        return null;
                    }

                    $res = mysqli_query($link, 'SHOW DATABASES where `Database` NOT IN ("test","performance_schema", "information_schema", "mysql")');
                    if (!$res) {
                        return null;
                    }

                    $count = $res->num_rows;

                    // tidy up
                    mysqli_close($link);
                    break;

                // Yes we have to cope with the old guys too!!!
                case 'mysql':
                    /*
                     * If you are trying to open multiple, separate MySQL connections with the same MySQL user,
                     * password, and hostname, you must set $new_link = TRUE to prevent mysql_connect from using an existing connection.
                     *
                     * @see http://uk1.php.net/manual/en/function.mysql-connect.php#comments
                     * @see http://uk1.php.net/manual/en/function.mysql-close.php#47865
                     */

                    // PHP upgraded to PHP 7+ on a site with mysql abstraction type
                    if (!function_exists('mysql_connect')) {
                        throw new Exception('Your site is incorrectly configured for PHP 7. Your Joomla Global Config states to use the "mysql" database abstraction layer but your server doesnt have the mysql* functions available as you are running PHP 7+ - to fix this you should select mysqli from the database type in Joomla Global Config and save your Joomla global configuration again (note it looks strange and already selected as Joomla on PHP7 will remove the mysql option in the dropdown - but be assured once you save the configuration in Joomla this will fix the issues.');
                    }

                    $link = mysql_connect($this->config->getCfg('host'), $this->config->getCfg('user'), $this->config->getCfg('password'), true);
                    if (!$link) {
                        return null;
                    }

                    // get the list of databases - if we can, if we have no access then returns null
                    $res = mysql_query('SHOW DATABASES  where `Database` NOT IN ("test", "information_schema","performance_schema", "mysql")');

                    if (!$res) {
                        return null;
                    }

                    // get the list of dbs
                    while ($row = mysql_fetch_row($res)) {
                        ++$count;
                    }

                    // tidy up
                    mysql_close($link);
                    break;
            }

            // return number seen
            return $count;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Do we have any backup tables.
     *
     * @return string
     */
    private function hasBakTables()
    {
        $this->db->setQuery("SHOW TABLES WHERE `Tables_in_{$this->config->getCfg('db', '')}` like 'bak_%'");

        return $this->db->loadResult() ? true : false;
    }

    /**
     * See if we have any installation folders.
     *
     * @return string "TRUE|FALSE" if we do
     */
    private function hasInstallationFolders()
    {
        $folders = $this->getFolders(JPATH_BASE);
        foreach ($folders as $folder) {
            if (preg_match('/installation|installation.old|docs\/installation|install|installation.bak|installation.old|installation.backup|installation.delete/i', $folder)) {
                return true;
            }
        }

        return false;
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
     * The the number of super admins.
     *
     * @todo remove hard coded 8 and look for the correct group_id if people have messed with ACL
     *
     * @return int The number of super admins
     */
    private function getNumberOfSuperAdmins()
    {
        if (preg_match('/^1\.5/', $this->version)) {
            $this->db->setQuery('SELECT count(*) FROM #__users WHERE gid = 25');
        } else {
            $this->db->setQuery('SELECT count(*) FROM #__user_usergroup_map WHERE group_id = 8');
        }

        return (int) $this->db->LoadResult();
    }

    /**
     * Report if any users have a username of 'admin'.
     *
     * @return int
     */
    private function getAdminUserNameCount()
    {
        $this->db->setQuery('SELECT COUNT(*) FROM #__users WHERE username = "admin"');

        return (int) $this->db->LoadResult();
    }

    private function getNeverLoggedInUsersCount()
    {
        $this->db->setQuery('SELECT COUNT(*) FROM #__users WHERE lastvisitDate IS NULL');

        return (int) $this->db->LoadResult();
    }

    /**
     * See if we have extension installed.
     *
     * @return string "TRUE|FALSE" if we do
     */
    private function hasExtensionWithNameInstalled($name)
    {
        $count   = 0;
        $folders = $this->getFolders(JPATH_BASE.'/administrator/components/');
        foreach ($folders as $folder) {
            if (preg_match('/'.$name.'/i', $folder, $matches)) {
                ++$count;
            }
        }

        return $count;
    }

    private function hastmplogfolderswritable()
    {
        return is_writeable($this->config->getCfg('tmp_path')) && $this->config->getCfg('log_path');
    }

    /**
     * @return bool
     */
    private function hasUsedDefaultTemplate()
    {
        $core_templates = array(
            'atomic',
            'beez_20',
            'beez_5',
            'beez3',
            'ja_purity',
            'protostar',
            'rhuk_milkyway',
            'rhuk_milkyway_2',
        );

        if (preg_match('/^1\.5/', $this->version)) {
            $this->db->setQuery('SELECT template FROM #__templates_menu WHERE client_id = 0 limit 1');
        } else {
            $this->db->setQuery('SELECT template FROM #__template_styles WHERE client_id=0 AND home=1');
        }

        return (bool) in_array($this->db->loadResult(), $core_templates);
    }

    private function hastpequalsone()
    {
        if (strpos($this->version, '1.5.') || 1 == JComponentHelper::getParams('com_templates')
                ->get('template_positions_display')
        ) {
            // allowed - which is bad
            $tpequalsone = 1;
        } else {
            // not allowed - which is good
            $tpequalsone = 0;
        }

        return $tpequalsone;
    }

    private function fpaexists()
    {
        $files = scandir(JPATH_BASE);
        foreach ($files as $file) {
            if (preg_match('/fpa.*\.php/i', $file)) {
                return true;
            }
        }

        return false;
    }

    private function getErrorReportingLevel()
    {
        $er = $this->config->getCfg('error_reporting');
        if (!is_int($er)) {
            switch ($er) {
                case 'none':
                    $er = 0;
                    break;
                case 'simple':
                    $er = 7;
                    break;
                case 'maximum':
                    $er = 2047;
                    break;
                case 'development':
                    $er = -1;
                    break;
                default:
                    $er = $er; // yeah yeah I know!
                    break;
            }
        }

        return $er;
    }

    private function getNumberOfAkeebaBackups()
    {
        $count  = 0;
        $folder = JPATH_BASE.'/administrator/components/com_akeeba/backup';
        if (file_exists($folder)) {
            $folderContents = scandir($folder);

            foreach ($folderContents as $file) {
                if (preg_match('/\.jpa$/i', $file)) {
                    ++$count;
                }
            }
        }

        return $count;
    }

    private function hasmd5passwords()
    {
        $this->db->setQuery('SELECT count(*) FROM #__users WHERE CHAR_LENGTH(password) = 32');

        return (int) $this->db->LoadResult();
    }

    private function hastmplogfoldersdefaultpaths()
    {
        $logPath          = $this->config->getCfg('log_path');
        $tmpPath          = $this->config->getCfg('tmp_path');
        $expectedLogPath1 = JPATH_BASE.'/logs';
        $expectedLogPath2 = JPATH_BASE.'/administrator/logs'; // Introduced in Joomla 3.6.0
        $expectedTmpPath  = JPATH_BASE.'/tmp';

        return (int) (($expectedLogPath1 == $logPath || $expectedLogPath2 == $logPath) && $expectedTmpPath == $tmpPath);
    }

    private function getMaxAllowedPacket()
    {
        $this->db->setQuery('SHOW VARIABLES LIKE "max_allowed_packet"');
        $res = $this->db->loadObjectList();

        return $res[0]->Value;
    }

    /**
     * @return string
     */
    private function checkJCEVersion()
    {
        $versionFile = JPATH_BASE.'/administrator/components/com_jce/jce.xml';
        if (file_exists($versionFile)) {
            $xml = file_get_contents($versionFile);
            preg_match('/\<version\>(.*)\<\/version\>/', $xml, $matches);
            if (count($matches)) {
                return $matches[1];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function checkfluff()
    {
        $fluffFiles = array(
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

        $fluffCount = 0;
        foreach ($fluffFiles as $file) {
            $fileWithPath = JPATH_BASE.$file;
            if (file_exists($fileWithPath)) {
                ++$fluffCount;
            }
        }

        return (int) $fluffCount;
    }

    private function checkdbschema()
    {
        $schemaData = new stdClass();
        // Handle crap versions
        if (preg_match('/^1\.7/', $this->version) || preg_match('/^1\.6/', $this->version)) {
            $schemaData->latest  = '1.6';
            $schemaData->current = '1.6';

        // Handle Anything Recent
        } elseif (!preg_match('/^1\.5/', $this->version) && file_exists(JPATH_ADMINISTRATOR.'/components/com_installer/models/database.php')) {
            require JPATH_ADMINISTRATOR.'/components/com_installer/models/database.php';

            $InstallerModelDatabase = new InstallerModelDatabase();
            $changeSet              = $InstallerModelDatabase->getItems();

            $schemaData->latest  = $changeSet->getSchema();
            $schemaData->current = $InstallerModelDatabase->getSchemaVersion();
        } else { // Handle Joomla 1.5
            $schemaData->latest  = '1.5';
            $schemaData->current = '1.5';
        }

        return json_encode($schemaData);
    }

    private function checkRobotsBlocksMedia()
    {
        $robots_blocks_media = 0;

        if (file_exists(JPATH_BASE.'/robots.txt')) {
            $robotsTxTContent = file_get_contents(JPATH_BASE.'/robots.txt');

            if (preg_match('/Disallow:\s\/(templates|media)\//', $robotsTxTContent)) {
                $robots_blocks_media = 1;
            }
        }

        return $robots_blocks_media;
    }

    private function getAkeebaOutputDirectoryProblems()
    {
        $problems = 0;

        try {
            // If using PHP 5.2 then ABORT as Akeeba stuff needs newer PHP version
            if (version_compare(PHP_VERSION, '5.3.0', '<')) {
                throw new Exception('PHP version below 5.3.0');
            } else {
                require 'bfPHPFiveThreePlusOnly.php';
            }

            // Check Akeeba Installed - Prerequisite
            if (!file_exists(JPATH_SITE.'/libraries/f0f/include.php')
                || !file_exists(JPATH_SITE.'/administrator/components/com_akeeba/engine/Factory.php')
                || !file_exists(JPATH_SITE.'/administrator/components/com_akeeba/engine/serverkey.php')
            ) {
                throw new Exception('Cannot load Akeeba, maybe not installed');
            }

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

                if ('[DEFAULT_OUTPUT]' != $dir && (!is_writable($dir) || !file_exists($dir))) {
                    ++$problems;
                }
            }

            return $problems;
        } catch (Exception $e) {
            // No need to pass back issues when looking for Akeeba or PHP versions - we will just ignore it
            // After all if the site is running in PHP 5.2 they have bigger issues!!!

            return $problems;
        }
    }

    private function getDiskSpace()
    {
        $data = array(
            'free'  => disk_free_space(JPATH_BASE),
            'total' => disk_total_space(JPATH_BASE),
        );

        $data['used'] = $data['total'] - $data['free'];

        $data['percentUsed'] = sprintf('%.2f', ($data['used'] / $data['total']) * 100);

        $data['free']  = $this->formatSize($data['free']);
        $data['total'] = $this->formatSize($data['total']);
        $data['used']  = $this->formatSize($data['used']);

        return json_encode($data);
    }

    private function formatSize($bytes)
    {
        $types = array('B', 'KB', 'MB', 'GB', 'TB');
        for ($i = 0; $bytes >= 1024 && $i < (count($types) - 1); $bytes /= 1024, $i++);

        return round($bytes, 2).' '.$types[$i];
    }

    public function testEOLIssues()
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

        return json_encode($data);
    }

    /**
     * Run some very specific checks to see if this site is hacked or not.
     */
    private function checkIf100percentHackedOrNot()
    {
        // oh, not dont this yet :) doing it service site instead :)
    }

    private function getNewUserType()
    {
        $this->db->setQuery("SELECT params FROM #__extensions WHERE name ='com_users'");
        $paramsJsonString = $this->db->loadResult();
        preg_match('/new_usertype\":\"([0-9]*)\"/', $paramsJsonString, $matches);

        return count($matches) ? $matches[1] : null;
    }

    private function getNon2FaAdmins()
    {
        try {
            $this->db->setQuery("select count(*) from #__users as u
                              left join #__user_usergroup_map as ugm on ugm.user_id = u.id
                              where (otpKey = \"\"  or otpKey IS NULL)
                             and (ugm.group_id IN (select id from #__usergroups where title= 'Super Users'))");

            return $this->db->loadResult();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Check for joomla.user.helper.XXXXX usernames - hack seen in Q4 2016.
     *
     * @return mixed
     */
    private function checkJoomlaUserHelperHack2016()
    {
        $this->db->setQuery("select count(*) from #__users where username LIKE 'joomla.user.helper.%'");

        return $this->db->loadResult();
    }

    /**
     * Check the session gc plugin in Joomla 3.
     *
     * @return int
     */
    public function getSessionGCStatus()
    {
        $res = 2;

        // Session GC
        $this->db->setQuery("select count(*) from #__extensions where name = 'plg_system_sessiongc'");
        $hasSessionGcPlugin = $this->db->LoadResult();

        if ($hasSessionGcPlugin) {
            $this->db->setQuery("select enabled from #__extensions where name = 'plg_system_sessiongc'");
            $res = $this->db->LoadResult();
        }

        return $res;
    }

    /**
     * Check how many Two Factor Plugins are enabled.
     */
    public function getTwoFactorPluginsEnabled()
    {
        // Session GC
        $this->db->setQuery("SELECT count(*) FROM `#__extensions` WHERE `folder` = 'twofactorauth' and enabled = 1");

        return $this->db->LoadResult();
    }

    /**
     * Load filters from com_config without using a helper.
     */
    public function getAdminFilterFixed()
    {
        $this->db->setQuery("select params from #__extensions where element = 'com_config'");
        $params = json_decode($this->db->LoadResult());

        if ('NONE' == $params->filters->{7}->filter_type) {
            return 0;
        } elseif ('BL' == $params->filters->{7}->filter_type) {
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * Load sendpassword from params from com_users without using a helper.
     */
    public function getPlaintextpasswordsFixed()
    {
        $this->db->setQuery("select params from #__extensions where element = 'com_users'");
        $params = json_decode($this->db->LoadResult());

        return 1 - $params->sendpassword;
    }

    /**
     * Load Flash Upload Settings from params from com_media without using a helper.
     */
    public function getUploadsettingsfixed()
    {
        $this->db->setQuery("select params from #__extensions where element = 'com_media'");
        $params = json_decode($this->db->LoadResult());
        if (
            !preg_match('/swf/ism', $params->upload_extensions)
            &&
            !preg_match('/application\/x-shockwave-flash/ism', $params->upload_mime)
        ) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Load params from com_content without using a helper.
     */
    public function getMailtofrienddisabled()
    {
        $this->db->setQuery("select params from #__extensions where element = 'com_content'");
        $params = json_decode($this->db->LoadResult());

        if (
            0 == $params->show_email_icon
        ) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Get the configuration of the google recaptcha plugin and global config.
     */
    private function getCaptchaDetails()
    {
        $this->db->setQuery("SELECT count(*) FROM #__extensions WHERE (name ='plg_captcha_recaptcha' or name = 'plg_captcha_recaptcha_invisible') and enabled = 1");

        return 0 != $this->db->loadResult() ? 1 : 0;
    }

    public function getData()
    {
        return $this->_data;
    }

    private function hasUpdatesAvailable()
    {
        set_time_limit(60);
        ob_start();
        require 'bfUpdates.php';
        $upCheck                   = new bfUpdates();
        $extensionupdatesavailable = $upCheck->getUpdates(true);
        ob_clean();

        return $extensionupdatesavailable;
    }
}

$data = new bfSnapshot();
bfEncrypt::reply(bfReply::SUCCESS, $data->getData());
