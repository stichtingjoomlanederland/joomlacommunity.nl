<?php

/*
 * @package   bfNetwork
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021 Blue Flame Digital Solutions Ltd. All rights reserved.
 * @license   GNU General Public License version 3 or later
 *
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

require_once 'bfPreferences.php';

class bfActivitylog
{
    protected static $instance;
    private $db;
    private $table_create = 'CREATE TABLE IF NOT EXISTS `bf_activitylog` (
                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `who` varchar(255) DEFAULT NULL,
                              `who_id` int(11) DEFAULT NULL,
                              `what` varchar(255) DEFAULT NULL,
                              `when` datetime DEFAULT NULL,
                              `where` varchar(255) DEFAULT NULL,
                              `where_id` int(11) DEFAULT NULL,
                              `ip` varchar(255) DEFAULT NULL,
                              `useragent` varchar(255) DEFAULT NULL,
                              `constkey` varchar(255) DEFAULT NULL,
                              `meta` text,
                              `action` varchar(255) DEFAULT NULL,
                              PRIMARY KEY (`id`),
                              KEY `who` (`who`),
                              KEY `who_id` (`who_id`),
                              KEY `when` (`when`)
                            ) DEFAULT CHARSET=utf8';

    private $table_migrate = array(
        'ALTER TABLE `bf_activitylog` CHANGE `ip` `ip` VARCHAR(255) NULL DEFAULT NULL'
    );

    private $table_insert = 'INSERT INTO `bf_activitylog`
                              (`id`, `who`, `who_id`, `what`, `when`, `where`, `where_id`, `ip`, `useragent`, `meta`,`action`,`constkey`) 
                              VALUES 
                             (NULL, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)';

    private $prefs;

    public function __construct()
    {
        try {
            $preferences = new bfPreferences();
            $this->prefs = $preferences->getPreferences();
            $this->db    = JFactory::getDBO();
            $this->ensureTableCreated();
            $this->ensureTableMigrated();
        } catch (Exception $exception) {
            //ignore failure as not to output anything to the public website
        }
    }

    public function ensureTableMigrated()
    {
        try {
            // Doh... cant think how else to do this in a nice way but need a quick fix.
            $sql = 'SHOW COLUMNS FROM bf_activitylog where Field = \'constkey\'';
            $this->db->setQuery($sql);
            $res = $this->db->loadObject();
            if (null === $res) {
                $sql = 'ALTER TABLE `bf_activitylog` ADD `constkey` VARCHAR(255) NULL DEFAULT NULL AFTER `action`';
                $this->db->setQuery($sql);
                if (method_exists($this->db, 'query')) {
                    $this->db->query();
                } else {
                    $this->db->execute();
                }
            }

            // process any
            foreach ($this->table_migrate as $sql) {
                $this->db->setQuery($sql);
                if (method_exists($this->db, 'query')) {
                    $this->db->query();
                } else {
                    $this->db->execute();
                }
            }
        } catch (Exception $exception) {
            //ignore failure as not to output anything to the public website
        }
    }

    public function ensureTableCreated()
    {
        $this->db->setQuery($this->table_create);
        if (method_exists($this->db, 'query')) {
            $this->db->query();
        } else {
            $this->db->execute();
        }
    }

    /**
     * @return bfActivitylog
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new bfActivitylog();
        }

        return self::$instance;
    }

    /**
     * If we get here we are "inside" the Joomla Application API and so all Joomla functions available.
     *
     * @param string $who
     * @param int    $who_id
     * @param string $what
     * @param string $where
     * @param int    $where_id
     * @param null   $ip
     * @param null   $userAgent
     */
    public function log($who = 'not me!', $who_id = 0, $what = 'dunno', $where = 'er?', $where_id = 0, $ip = null, $userAgent = null, $meta = '{}', $action = '', $alertName = '', $constKey = 'legacy', $when = null)
    {
        try {
            if (null === $when) {
                $when = date('Y-m-d H:i:s');
            }

            if (null == $ip) {
                $ip = str_replace('::ffff:', '', (@getenv('HTTP_X_FORWARDED_FOR') ? @getenv('HTTP_X_FORWARDED_FOR') : @$_SERVER['REMOTE_ADDR']));
            }

            if ('system' == $ip) {
                $ip = '';
            }

            if (!$userAgent && is_array($_SERVER) && array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
                $agent = $_SERVER['HTTP_USER_AGENT'];
                if (!$agent) {
                    $agent = 'Unknown';
                }
            } else {
                $agent = $userAgent;
            }

            $sql = sprintf($this->table_insert,
                $this->db->quote($who),
                $this->db->quote($who_id),
                $this->db->quote($what),
                $this->db->quote($when),
                $this->db->quote($where),
                $this->db->quote($where_id),
                $this->db->quote($ip),
                $this->db->quote($agent),
                $this->db->quote($meta),
                $this->db->quote($action),
                $this->db->quote($constKey)
            );

            $this->db->setQuery($sql);
            if (method_exists($this->db, 'execute')) {
                $this->db->execute();
            } else {
                $this->db->query();
            }

            $host_id = $this->getHostID();

            if (!$host_id) {
                return;
            }

            $data = array(
                'HOST_ID'    => $host_id,
                'who'        => $who,
                'who_id'     => $who_id,
                'what'       => $what,
                'when'       => $when,
                'where'      => $where,
                'where_id'   => $where_id,
                'ip'         => $ip,
                'userAgent'  => $userAgent,
                'meta'       => $meta,
                'action'     => $action,
                'alert_name' => $alertName,
            );

            // Always attempt
            $this->sendToSpy($data);

            if (property_exists($this->prefs, $alertName) && $this->prefs->$alertName == 1) {
                $this->sendLogAlert($data);
            }
        } catch (Exception $exception) {
            //ignore failure as not to output anything to the public website
        }
    }

    /**
     * @param string $who
     * @param int    $who_id
     * @param string $what
     * @param        $when
     * @param string $where
     * @param int    $where_id
     * @param null   $ip
     * @param null   $userAgent
     * @param string $meta
     * @param string $action
     * @param string $alertName
     *
     * @return string|void
     */
    public function sendLogAlert($data)
    {
        try {
            $opts = array('http' => array(
                'content'       => http_build_query($data),
                'method'        => 'POST',
                'user_agent'    => JURI::base(),
                'max_redirects' => 1,
                'header'        => 'Content-type: application/x-www-form-urlencoded',
                'proxy'         => ('local' == getenv('APPLICATION_ENV') ? 'tcp://host.docker.internal:8888' : ''),
                'timeout'       => 5, //so we don't destroy live sites if the service is offline
            ),
            );

            if ('local' == getenv('APPLICATION_ENV')) {
                $opts = array_merge($opts, array(
                        'ssl' => array(
                            'verify_peer'      => false,
                            'verify_peer_name' => false,
                        ), )
                );

                // Using @ so we don't destroy live sites if the service is offline
                return @file_get_contents('https://dev.mysites.guru/api/log', false, stream_context_create($opts));
            } else {
                // Using @ so we don't destroy live sites if the service is offline
                return @file_get_contents('https://manage.mysites.guru/api/log', false, stream_context_create($opts));
            }
        } catch (Exception $exception) {
            //ignore failure as not to output anything to the public website
        }
    }

    /**
     * @return string
     */
    public function getHostID()
    {
        $files = array(
            str_replace(array('/administrator', '\administrator'), '', JPATH_BASE.'/plugins/system/bfnetwork/HOST_ID'),         //Joomla 1.5 gulp
            str_replace(array('/administrator', '\administrator'), '', JPATH_BASE.'/plugins/system/bfnetwork/bfnetwork/HOST_ID'), //Joomla 2+
        );

        foreach ($files as $file) {
            if (file_exists($file)) {
                return file_get_contents($file);
            }
        }
    }

    /**
     * Realtime Log Viewer Integration.
     */
    private function sendToSpy($data)
    {
        if (!file_exists(dirname(__FILE__).'/tmp/realtime.php')) {
            return;
        }

        // decode configuration
        $realTimeConfig = json_decode(file_get_contents(dirname(__FILE__).'/tmp/realtime.php'));

        // check if realtime is still active
        if (time() < $realTimeConfig->until) {
            $opts = array('http' => array(
                'content'       => json_encode($data),
                'method'        => 'POST',
                'user_agent'    => JURI::base(),
                'max_redirects' => 1,
                'header'        => 'Content-type: application/x-www-form-urlencoded',
                'proxy'         => ('local' == getenv('APPLICATION_ENV') ? 'tcp://host.docker.internal:8888' : ''),
                'timeout'       => 5, //so we don't destroy live sites if the service is offline
            ),
            );

            // dev mode ignore SSL unsigned
            if (strpos($realTimeConfig->endpoint, 'dev')) {
                $opts = array_merge($opts, array(
                        'ssl' => array(
                            'verify_peer'      => false,
                            'verify_peer_name' => false,
                        ), )
                );
            }

            /*
             * Push data to tmp endpoint for this site
             * Using @ so we don't destroy live sites if the service is offline
             */
            @file_get_contents($realTimeConfig->endpoint, false, stream_context_create($opts));
        } else {
            @unlink(dirname(__FILE__).'/tmp/realtime.php');
        }
    }
}
