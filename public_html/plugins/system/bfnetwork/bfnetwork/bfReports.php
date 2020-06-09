<?php

/*
 * @package   bfNetwork
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020 Blue Flame Digital Solutions Ltd. All rights reserved.
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

require 'bfEncrypt.php';
require 'bfInitJoomla.php';
require 'bfActivitylog.php';

/**
 * If we have got here then we have already passed through decrypting
 * the encrypted header and so we are sure we are now secure and no one
 * else cannot run the code below.
 */
class bfReports
{
    /**
     * @var stdClass|null
     */
    private $_dataObj;

    /**
     * @var JDatabase
     */
    private $db;

    /**
     * PHP 5 Constructor,
     * I inject the request to the object.
     *
     * @param stdClass $dataObj
     */
    public function __construct($dataObj = null)
    {
        // Set the request vars
        $this->_dataObj = $dataObj;
        $this->db       = JFactory::getDBO();
    }

    private function remotelog()
    {
        bfActivitylog::getInstance()->log(
            $this->_dataObj->who,
            $this->_dataObj->who_id,
            $this->_dataObj->what,
            $this->_dataObj->where,
            $this->_dataObj->where_id,
            $this->_dataObj->ip,
            $this->_dataObj->useragent,
            json_encode($this->_dataObj->meta),
            $this->_dataObj->action,
            $this->_dataObj->alertname,
            $this->_dataObj->constkey,
            $this->_dataObj->when
        );
        bfEncrypt::reply('success', $this->_dataObj);
    }

    /**
     * Retrieve Log Rows for reporting purposes.
     */
    private function getdata()
    {
        $fromDateTime = $this->_dataObj->f;
        $toDateTime   = $this->_dataObj->t;

        $sql = "select * from bf_activitylog WHERE
                `when` >= '%s' 
                AND 
                `when` <= '%s'
                ";

        $this->db->setQuery(sprintf($sql, $fromDateTime, $toDateTime));

        $data = $this->db->loadObjectList();

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

            JLoader::register('ActionlogsHelper', JPATH_ADMINISTRATOR.'/components/com_actionlogs/helpers/actionlogs.php');

            $sql = "select * from #__action_logs WHERE
                `log_date` >= '%s' 
                AND 
                `log_date` <= '%s'
                ";

            $this->db->setQuery(sprintf($sql, $fromDateTime, $toDateTime));
            $rows = $this->db->loadObjectList();

            // Load all language files needed
            ActionlogsHelper::loadActionLogPluginsLanguage();
            $lang = JFactory::getLanguage();
            $lang->load('com_privacy', JPATH_ADMINISTRATOR, null, false, true);
            $lang->load('plg_system_actionlogs', JPATH_ADMINISTRATOR, null, false, true);
            $lang->load('plg_system_privacyconsent', JPATH_ADMINISTRATOR, null, false, true);

            // manipulate data to push to mysites.guru
            foreach ($rows as $row) {
                $row->what   = ActionlogsHelper::getHumanReadableLogMessage($row);
                $row->ip     = $row->ip_address;
                $row->when   = $row->log_date;
                $row->who_id = $row->user_id;
                $row->source = 'core_user_action_log';
            }

            $data = array_merge($data, $rows);
        }

        bfEncrypt::reply('success', $data);
    }

    /**
     * I'm the controller - I run methods based on the requested action.
     */
    public function run()
    {
        $action = $this->_dataObj->remoteaction;

        return $this->$action();
    }
}

$preferences = new bfReports($dataObj);
$preferences->run();
