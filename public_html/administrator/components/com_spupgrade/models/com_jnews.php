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
jimport('joomla.application.component.model');

class SPUpgradeModelCom_JNews extends SPUpgradeModelExtension {
    
    public function setTable($name) {
        $factory = $this->factory;

        $prefix = $this->params->get('source_db_prefix');

        //Exit if empty table
        $source_table_name = $prefix . $name;

        // Init
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;

        //Define destination table name
        $destination_table_name = $destination_db->getPrefix() . $name;

        // Get tables descriptions
        $query = 'SHOW CREATE TABLE ' . $source_table_name;

        $source_db->setQuery($query);
        if (!CYENDFactory::execute($source_db)) {
            jexit($source_db->getErrorMsg());
        }
        $source_table_desc = $source_db->loadObject();
        $source_table_desc_new = $source_table_desc;

        $query = 'describe ' . $destination_table_name;
        $destination_db->setQuery($query);
        if (!CYENDFactory::execute($destination_db)) {
            //Create table
            $query = $source_table_desc->{'Create Table'};
            $query = str_replace('CREATE TABLE `' . $source_table_name, 'CREATE TABLE `' . $destination_table_name, $query);            
            $destination_db->setQuery($query);
            if (!CYENDFactory::execute($destination_db)) {
                jexit($destination_db->getErrorMsg());
            }
            $query = 'describe ' . $destination_table_name;
            $destination_db->setQuery($query);
            if (!CYENDFactory::execute($destination_db)) {
                jexit($destination_db->getErrorMsg());
            }
        }
        
        $destination_table_desc = $destination_db->loadAssocList();

        //Compare tables
        $query = 'describe ' . $source_table_name;
        $source_db->setQuery($query);
        $result = CYENDFactory::execute($source_db);
        $source_table_desc = $source_db->loadAssocList();

        //$compare_desc = array_diff($destination_table_desc, $source_table_desc);
        //if (!empty($compare_desc)) {                
        if ($destination_table_desc != $source_table_desc) {

            //delete and create table
            $destination_db->dropTable($destination_table_name, true);
            $query = $source_table_desc_new->{'Create Table'};
            $query = str_replace('CREATE TABLE `' . $source_table_name, 'CREATE TABLE `' . $destination_table_name, $query);            
            $destination_db->setQuery($query);
            if (!CYENDFactory::execute($destination_db)) {
                jexit($destination_db->getErrorMsg());
            }
            $query = 'describe ' . $destination_table_name;
            $destination_db->setQuery($query);
            if (!CYENDFactory::execute($destination_db)) {
                jexit($destination_db->getErrorMsg());
            }
        }
        return true;
    }

    public function media($folders = null) {
        //define folders to copy
        $folders = Array();
        $folders[] = 'media/com_jnews';
        
        parent::media($folders);
                
    }
    
    public function transfer($ids = null, $name) {
        $this->task->state = 2; //state for success        
        parent::transfer($ids, $name);

        $this->task->state = 4; //state for success
        $this->fix($ids, $name);
    }
    
    private function fix($pks = null, $name) {
        
        // Initialize
        $jAp = $this->jAp;
        $factory = $this->factory;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        $task = $this->task;
        $user = $this->user;
        $params = $this->params;
        $destination_table = $this->destination_table;
        $table_name = $this->table_name;
        $id = $this->id;

        $source_table_name = '#__' . $name;
        $destination_table_name = '#__' . $name;
        $items = Array();
        
        // Load items
        $query = 'SELECT destination_id
            FROM #__spupgrade_log
            WHERE tables_id = ' . (int) $task->id . ' AND ( state = 2 OR state = 3 )';
        $query .= ' ORDER BY id ASC';
        $destination_db->setQuery($query);
        if (!$factory->execute($destination_db)) {
            jexit($destination_db->getErrorMsg());
        }
        $excludes = $destination_db->loadColumn();

        //Find ids
        if (is_null($pks[0])) {
            $query = 'SELECT COUNT(*)' .
                    ' FROM #__' . $name;
            $source_db->setQuery($query);
            if (!$factory->execute($source_db)) {
                jexit($source_db->getErrorMsg());
            }
            $total_items = $source_db->loadResult();
            for ($index = 0; $index < $total_items; $index++) {
                $pks[$index] = $index;
            }
        }

        // Loop to save items
        foreach ($pks as $pk) {
                     
            //Load data from source
            $exclude = array_search($pk, $excludes);
            if ($exclude === false)
                continue;
            else
                unset($excludes[$exclude]);

            $query = 'SELECT * FROM #__' . $name .
                    ' LIMIT ' . $pk . ', 1';
            $source_db->setQuery($query);
            if (!$factory->execute($source_db)) {
                jexit($source_db->getErrorMsg());
            }
            $item = $source_db->loadAssoc();

            if (empty($item))
                continue;

            //status pending
            $this->batch -= 1;
            if ($this->batch < 0) 
                return;
            
            //log            
            $tableLog->reset();
            $tableLog->id = null;
            $tableLog->load(array("tables_id" => $task->id, "source_id" => $pk));
            $tableLog->created = null;
            $tableLog->note = "";
            $tableLog->source_id = $pk;
            $tableLog->destination_id = $pk;
            $tableLog->state = 1;
            $tableLog->tables_id = $task->id;
            
            ///////////////////////////////////////////////////////////////////////////////////////

            //params
            $params = $item['params'];
            if (!empty($params)) {
                $item_params = explode("\n", $params);
                foreach ($item_params as $param) {
                    $attribs = explode("=", $param);
                    if (count($attribs) > 1) {
                        if ($attribs[0] == 'timezone') {
                            $new_params[$attribs[0]] = '';
                        } else {
                            $new_params[$attribs[0]] = $attribs[1];
                        }
                    }
                }
                $item['params'] = json_encode($new_params);
            }

            //user_id
            $user_id = $item['user_id'];
            if (!empty($user_id)) {
                $tableLog->reset();
                $tableLog->id = null;
                $tableLog->load(array("tables_id" => 1, "source_id" => $user_id));
                $item['user_id'] = $tableLog->destination_id;
            }

            //access difference
            if (!is_null($item['access'])) {
                if ($item['access'] > 2) {
                    //$message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_ACCESSLEVEL', $item['id']) . '</p>';
                    continue;
                }
                if ($item['access'] == 2)
                    $item['access'] = 3;
                if ($item['access'] == 1)
                    $item['access'] = 2;
                if ($item['access'] == 0)
                    $item['access'] = 1;
            }
            
            ///////////////////////////////////////////////////////////////////////////////////////

            //reload table log
            $tableLog->reset();
            $tableLog->id = null;
            $tableLog->load(array("tables_id" => $task->id, "source_id" => $pk));
            
            //Build query
            $query = "REPLACE INTO #__" . $name . " (";
            $columnNames = Array();
            $values = Array();
            foreach ($item as $column => $value) {
                if ($column != 'sp_id') {
                    $columnNames[] = $destination_db->quoteName($column);
                    $temp1 = implode(',', $columnNames);
                    $values[] = $destination_db->quote($value);
                    $temp2 = implode(',', $values);
                }
            }
            $query .= $temp1 . ") VALUES (" . $temp2 . ")";
            
            // Create record
            $destination_db->setQuery($query);
            if (!$destination_db->query()) {
                $tableLog->note = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CREATE', print_r($item, true), $destination_db->getErrorMsg()) . '</p>';
                $tableLog->store();
                continue;
            }

            //Log
            $tableLog->state = $this->task->state;
            $tableLog->store();
        } //Main loop end        
    }
}
