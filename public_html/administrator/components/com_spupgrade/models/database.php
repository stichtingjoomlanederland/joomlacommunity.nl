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
defined('_JEXEC') or die('Restricted access');

// import the Joomla modellist library
jimport('joomla.application.component.modellist');

class SPUpgradeModelDatabase extends JModelList {

    public function getTable($type = 'Tables', $prefix = 'SPUpgradeTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getItem($table_name) {
        $db = $this->getDbo();
        //$query = "SELECT * FROM #__spupgrade_tables WHERE `extension_name` LIKE 'com_database' AND `name` LIKE '".$table_name."'";

        $query = $db->getQuery(true);
        $query->select(
                $this->getState(
                        'list.select', 'a.id, a.extension_name, a.name'
                )
        );
        $query->from('#__spupgrade_tables AS a');
        $query->where("a.extension_name LIKE 'com_database'");
        $query->where("a.name LIKE '" . $table_name . "'");

        $db->setQuery($query);
        $db->query();
        return $db->loadObject();
    }

    public function newItem($table_name) {
        $db = $this->getDbo();
        $query = "
            INSERT INTO  `#__spupgrade_tables` (
                `id` ,
                `extension_name` ,
                `name`
                )
                VALUES (
                NULL ,  'com_database',  '" . $table_name . "'
                );
            ";
        $db->setQuery($query);
        $db->query();
        return $this->getItem($table_name);
    }

    public function getItems($pk = null) {
        $source = new CYENDSource();
        //$items = parent::getItems(); 
        // Create a new query object.
        $db = $source->source_db;
        $query = $this->getListQuery($pk);

        //apply pagination
        $start = $this->getStart();
        $limit = $this->getState('list.limit');

        $db->setQuery($query, $start, $limit);
        $db->query();
        $items = $db->loadColumn();
        if (empty($items)) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_SPUPGRADE_MSG_ERROR_OPTIONS'));
            JFactory::getApplication()->enqueueMessage(JText::plural('COM_SPUPGRADE_MSG_DB', $db->getErrorMsg()), 'error');
            return Array();
        }


        //Rename fields and keep only tables with same prefix
        $params = JComponentHelper::getParams('com_spupgrade');
        $prefix = $source->modPrefix($params->get("source_db_prefix", ''));
        $items2 = Array();
        $i = 0;
        foreach ($items as $item => $value) {
            $value2 = explode('_', $value);
            if ($value2[0] . "_" == $prefix) {
                $i += 1;
                @$items2[$item]->id = $i;
                $items2[$item]->prefix = $value2[0];
                unset($value2[0]);
                $items2[$item]->name = implode('_', $value2);
            }
        }

        return $items2;
    }

    protected function getListQuery($pk = null) {

        $params = JComponentHelper::getParams('com_spupgrade');
        $database = $params->get("source_database_name", '');
        $prefix = CYENDSource::modPrefix($params->get("source_db_prefix", ''));

        $query = 'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = \'' . $database . '\' 
            AND TABLE_NAME LIKE "' . $prefix . '%"';

        return $query;
    }

    public function getTestConnection() {
        //Check connection
        $source = new CYENDSource();
        return $source->testConnection();
    }

    /**
     * Method to get the total number of items for the data set.
     *
     * @return  integer  The total number of items available in the data set.
     *
     */
    public function getTotal() {
        parent::getTotal();
        // Get a storage key.
        $store = $this->getStoreId('getTotal');
        // Try to load the data from internal storage.
        if (isset($this->cache[$store])) {
            return $this->cache[$store];
        }

        // Load the total.
        $query = $this->_getListQuery();
        try {
            $total = (int) $this->_getListCount($query);
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }

        // Add the total to the internal cache.
        $this->cache[$store] = $total;

        return $this->cache[$store];
    }

    /**
     * Returns a record count for the query.
     *
     * @param   JDatabaseQuery|string  $query  The query.
     *
     * @return  integer  Number of rows for query.
     *
     * @since   12.2
     */
    protected function _getListCount($query) {
        $source = new CYENDSource();
        //$items = parent::getItems(); 
        // Create a new query object.
        $db = $source->source_db;

        // Use fast COUNT(*) on JDatabaseQuery objects if there no GROUP BY or HAVING clause:
        if ($query instanceof JDatabaseQuery && $query->type == 'select' && $query->group === null && $query->having === null) {
            $query = clone $query;
            $query->clear('select')->clear('order')->select('COUNT(*)');

            $db->setQuery($query);
            return (int) $db->loadResult();
        }

        // Otherwise fall back to inefficient way of counting all results.
        $db->setQuery($query);
        $db->execute();

        return (int) $db->getNumRows();
    }

}
