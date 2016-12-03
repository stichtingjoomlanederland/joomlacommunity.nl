<?php

/**
 * @package		SP Upgrade
 * @subpackage	Components
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		http://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access to this file
defined('_JEXEC') or die;

// import the Joomla modellist library
jimport('joomla.application.component.modellist');
include_once 'ftp.php';

class SPUpgradeModelExtensions extends JModelList {

    public function getTable($type = 'Tables', $prefix = 'SPUpgradeTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getItem($pk) {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $this->getQuery($pk);

        $db->setQuery($query);
        $db->query();
        $item = $db->loadObject();

        return $item;
    }

    protected function getQuery($pk) {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.id, a.extension_name, a.name'
                )
        );
        $query->from('#__spupgrade_tables AS a');        

        // Filter by id
        $query->where('a.id = ' . (int) $pk);

        return $query;
    }

    public function getItems($pk = null) {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $this->getListQuery($pk);

        $db->setQuery($query);
        $db->query();
        $items = $db->loadObjectList();

        return $items;
    }

    protected function getListQuery($pk = null) {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.id, a.extension_name, a.name'
                )
        );
        $query->from('#__spupgrade_tables AS a');

        // Filter by extension_name
        // Join over the extension name
        if (!is_null($pk)) {
            $query->join('LEFT', '`#__extensions` AS l ON l.extension_name = a.extension_name GROUP BY a.extension_name');
            $query->where('l.extension_id = ' . (int) $pk);
        }

        //Limit up to id < 1000
        $query->where('`extension_name` LIKE '.$db->quote('com_acymailing'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_k2'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_kunena'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_virtuemart'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_breezingforms'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_sh404sef'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_jcomments'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_jevents'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_comprofiler'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_sef'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_phocadownload'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_phocagallery'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_phocaguestbook'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_phocamaps'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_jnews'), 'OR');
        $query->where('`extension_name` LIKE '.$db->quote('com_rsform'), 'OR');
        
        $query->group('`extension_name`');

        // Ordering
        $query->order('a.id ASC');

        return $query;
    }

    public function getTestConnection() {
        //Check connection
        $source = new CYENDSource();
        return $source->testConnection();
    }
    
    public function getPathConnection() {
        //Check connection
        $source = new CYENDSource();
        return $source->testPathConnection();
    }
    
    public function getFtpConnection() {
        // Check if FTP extension is loaded?  If not return false
        if (!extension_loaded('ftp')) {
            JError::raiseWarning('31', JText::_('COM_SPUPGRADE_MSG_ERROR_FTP_EXTENSION_NOT_LOADED'));
            return false;
        }
        $ftp = new SPUpgradeModelFTP();
        return $ftp->checkConnection();
    }

}
