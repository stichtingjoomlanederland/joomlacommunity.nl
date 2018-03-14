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

class SPUpgradeModelCom_PhocaMaps extends SPUpgradeModelExtension {
    
    public function setTable($name) {
        
        return true;
    }

    public function media($folders = null) {
        return true;
        
        //define folders to copy
        $folders = Array();
        $folders[] = 'phocamapskml';
        
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

            //language
            $item['language'] = '*';
            
            //access difference
            if (!is_null($item['access'])) {
                if ($item['access'] > 2) {
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
                $tableLog->note = $destination_db->getErrorMsg();
                $tableLog->store();
                continue;
            }

            //Log
            $tableLog->state = $this->task->state;
            $tableLog->store();
        } //Main loop end                
    }
}
