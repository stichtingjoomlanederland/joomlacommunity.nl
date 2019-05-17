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

require_once 'bfPreferences.php';

class bfActivitylog
{
    /**
     * @var
     */
    protected static $instance;

    /**
     * @var
     */
    private $db;

    /**
     * @var string
     */
    private $table_create = 'CREATE TABLE IF NOT EXISTS `bf_activitylog` (
                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `who` varchar(255) DEFAULT NULL,
                              `who_id` int(11) DEFAULT NULL,
                              `what` varchar(255) DEFAULT NULL,
                              `when` datetime DEFAULT NULL,
                              `where` varchar(255) DEFAULT NULL,
                              `where_id` int(11) DEFAULT NULL,
                              `ip` varchar(20) DEFAULT NULL,
                              `useragent` varchar(255) DEFAULT NULL,
                              `meta` text,
                              `action` varchar(255) DEFAULT NULL,
                              PRIMARY KEY (`id`),
                              KEY `who` (`who`),
                              KEY `who_id` (`who_id`),
                              KEY `when` (`when`)
                            ) DEFAULT CHARSET=utf8';

    private $table_insert = 'INSERT INTO `bf_activitylog`
                              (`id`, `who`, `who_id`, `what`, `when`, `where`, `where_id`, `ip`, `useragent`, `meta`,`action`) 
                              VALUES 
                             (NULL, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)';

    /**
     * @var mixed|stdClass
     */
    private $prefs;

    /**
     * bfActivitylog constructor.
     */
    public function __construct()
    {
        $preferences = new bfPreferences();
        $this->prefs = $preferences->getPreferences();
        $this->db    = JFactory::getDBO();
        $this->ensureTableCreated();
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
     *
     * @since version
     */
    public function log($who = 'not me!', $who_id = 0, $what = 'dunno', $where = 'er?', $where_id = 0, $ip = null, $userAgent = null, $meta = '{}', $action = '', $alertName = '')
    {
        $when = JFactory::getDate()->format('Y-m-d H:i:s', true);

        if (null == $ip) {
            $ip = str_replace('::ffff:', '', (@getenv('HTTP_X_FORWARDED_FOR') ? @getenv('HTTP_X_FORWARDED_FOR') : @$_SERVER['REMOTE_ADDR']));
        }
        if ('system' == $ip) {
            $ip = '';
        }

        $sql = sprintf($this->table_insert,
            $this->db->quote($who),
            $this->db->quote($who_id),
            $this->db->quote($what),
            $this->db->quote($when),
            $this->db->quote($where),
            $this->db->quote($where_id),
            $this->db->quote($ip),
            $this->db->quote(null),
            $this->db->quote($meta),
            $this->db->quote($action)
        );

        $this->db->setQuery($sql);
        if (method_exists($this->db, 'execute')) {
            $this->db->execute();
        } else {
            $this->db->query();
        }

        if (property_exists($this->prefs, $alertName) && $this->prefs->$alertName == 1) {
            $this->sendLogAlert($who, $who_id, $what, $when, $where, $where_id, $ip, $userAgent, $meta, $action, $alertName);
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
    public function sendLogAlert($who = 'not me!', $who_id = 0, $what = 'dunno', $when, $where = 'er?', $where_id = 0, $ip = null, $userAgent = null, $meta = '{}', $action = '', $alertName = '')
    {
        $host_id = $this->getHostID();

        if (!$host_id) {
            return;
        }

        $postdata = http_build_query(
            array(
                'HOST_ID'    => $host_id,
                'who'        => $who,
                'who_id'     => $who_id,
                'what'       => $what,
                'what'       => $what,
                'when'       => $when,
                'where'      => $where,
                'where_id'   => $where_id,
                'ip'         => $ip,
                'userAgent'  => $userAgent,
                'meta'       => $meta,
                'action'     => $action,
                'alert_name' => $alertName,
            )
        );

        $opts = array('http' => array(
                              'content'       => $postdata,
                              'method'        => 'POST',
                              'user_agent'    => JURI::base(),
                              'max_redirects' => 1,
                              'header'        => 'Content-type: application/x-www-form-urlencoded',
                              'proxy'         => ('local' == getenv('APPLICATION_ENV') ? 'tcp://127.0.0.1:8888' : ''),
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

            return @file_get_contents('https://local-maintain.myjoomla.com/api/log', false, stream_context_create($opts));
        } else {
            // Using @ so we don't destroy live sites if the service is offline
            return @file_get_contents('https://manage.myjoomla.com/api/log', false, stream_context_create($opts));
        }
    }

    /**
     * @return string
     */
    public function getHostID()
    {
        $files = array(
            str_replace('/administrator', '', JPATH_BASE.'/plugins/system/bfnetwork/HOST_ID'),         //Joomla 1.5 gulp
            str_replace('/administrator', '', JPATH_BASE.'/plugins/system/bfnetwork/bfnetwork/HOST_ID'), //Joomla 2+
        );

        foreach ($files as $file) {
            if (file_exists($file)) {
                return file_get_contents($file);
            }
        }
    }
}
