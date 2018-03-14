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

class SPUpgradeModelCom_JEvents extends SPUpgradeModelExtension {
    
    public function setTable($name) {
        if ($name == 'categories')
            return true;
        
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
        return true;
        
        //define folders to copy
        $folders = Array();
        $folders[] = 'media';
        
        parent::media($folders);
                
    }
    
    public function transfer($ids = null, $name) {
        
        if ($name == 'categories') {
            //initialize
            $this->destination_table = $this->factory->getTable('Category', 'JTable');
            $this->task->section = ' WHERE section LIKE "com_jevents"';
            $this->task->state = 2; //state for success

            $this->categories($ids);

            //Fix categories
            $this->task->state = 4; //state for success
            $this->categories_fix($ids);
        } else {
            $this->task->state = 2; //state for success        
            parent::transfer($ids, $name);

            $this->task->state = 4; //state for success
            $this->fix($ids, $name);
        }
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

            //created_by
            $created_by = $item['created_by'];
            if (!empty($created_by)) {
                $tableLog->reset();
                $tableLog->id = null;
                $tableLog->load(array("tables_id" => 1, "source_id" => $created_by));
                $item['created_by'] = $tableLog->destination_id;
            }

            //modified_by
            $modified_by = $item['modified_by'];
            if (!empty($created_by)) {
                $tableLog->reset();
                $tableLog->id = null;
                $tableLog->load(array("tables_id" => 1, "source_id" => $modified_by));
                $item['modified_by'] = $tableLog->destination_id;
            }
            
            //user_id
            $user_id = $item['user_id'];
            if (!empty($user_id)) {
                $tableLog->reset();
                $tableLog->id = null;
                $tableLog->load(array("tables_id" => 1, "source_id" => $user_id));
                $item['user_id'] = $tableLog->destination_id;
            }
            
            //catid
            $catid = $item['catid'];
            if (!empty($catid)) {
                $tableLog->reset();
                $tableLog->id = null;
                $tableLog->load(array("tables_id" => 164, "source_id" => $catid));
                $item['catid'] = $tableLog->destination_id;
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
    
    public function categories($ids = null) {
        // Initialize
        $jAp = $this->jAp;
        $factory = $this->factory;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        $destination_table = $this->destination_table;
        $user = $this->user;
        $params = $this->params;
        $task = $this->task;

        // Load items
        $query = 'SELECT source_id
            FROM #__spupgrade_log
            WHERE tables_id = ' . (int) $task->id . ' AND state >= 2
            ORDER BY id ASC';
        $destination_db->setQuery($query);
        if (!$factory->execute($destination_db)) {
            jexit($destination_db->getErrorMsg());
        }
        $temp = $destination_db->loadColumn();

        $query = 'SELECT * 
            FROM #__categories ' .
                $task->section;
        if (!is_null($temp[0]))
            $query .= ' AND id NOT IN (' . implode(',', $temp) . ')';
        if (!empty($ids))
            $query .= ' AND id IN (' . implode(',', $ids) . ')';
        $query .= ' ORDER BY id ASC';
        $source_db->setQuery($query);
        if (!$factory->execute($source_db)) {
            jexit($source_db->getErrorMsg());
        }
        $items = $source_db->loadAssocList();

        //Find total number of categories
        $query = 'SELECT id' .
                ' FROM #__categories' .
                ' ORDER BY id DESC';
        $source_db->setQuery($query);
        if (!$factory->execute($source_db)) {
            jexit($source_db->getErrorMsg());
        }
        $catnum = $source_db->loadResult();

        // Loop to save items
        foreach ($items as $i => $item) {

            //status pending
            $this->batch -= 1;
            if ($this->batch < 0) 
                return;

            //log            
            $tableLog->reset();
            $tableLog->id = null;
            $tableLog->load(array("tables_id" => $task->id, "source_id" => $item['id']));
            $tableLog->created = null;
            $tableLog->note = "";
            $tableLog->source_id = $item['id'];
            $tableLog->destination_id = $item['id'];
            $tableLog->state = 1;
            $tableLog->tables_id = $task->id;

            //Fix id=1
            if ($item['id'] < 2) {
                $item['id'] = $catnum * 2;
                $tableLog->destination_id = $item['id'];
            }

            // Create record
            $destination_db->setQuery(
                    "INSERT INTO #__categories" .
                    " (id)" .
                    " VALUES (" . $destination_db->quote($item['id']) . ")"
            );
            if (!$factory->execute($destination_db)) {
                if ($params->get("new_ids", 0) == 1) {
                    $destination_db->setQuery(
                            "INSERT INTO #__categories" .
                            " (title)" .
                            " VALUES (" . $destination_db->quote('sp_transfer') . ")"
                    );
                    if (!$factory->execute($destination_db)) {
                        $tableLog->note = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                        $tableLog->store();
                        continue;
                    }
                    $destination_db->setQuery(
                            "SELECT id FROM #__categories" .
                            " WHERE title LIKE " . $destination_db->quote('sp_transfer')
                    );
                    $factory->execute($destination_db);
                    $tableLog->destination_id = $destination_db->loadResult();
                    $item['id'] = $tableLog->destination_id;
                    $tableLog->note = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_NEW_IDS', $item['id'], $tableLog->destination_id) . '</p>';
                } elseif ($params->get("new_ids", 0) == 0) {
                    $tableLog->note = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                    $tableLog->store();
                    continue;
                }
            }

            // Reset
            $destination_table->reset();

            //access difference
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
            
            //parent_id
            if ($item['parent_id'] == 0)
                $item['parent_id'] = 1;

            //Replace existing item
            if ($params->get("new_ids", 0) == 2)
                $destination_table->load($item['id']);

            // Bind
            if (!$destination_table->bind($item)) {
                // delete record
                $destination_db->setQuery(
                        "DELETE FROM #__categories" .
                        " WHERE id = " . $destination_db->quote($item['id'])
                );
                if (!$factory->execute($destination_db)) {
                    jexit($destination_db->getErrorMsg());
                }
                $tableLog->note = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_BIND', $item['id'], $destination_table->getError()) . '</p>';
                $tableLog->store();
                continue;
            }

            //no parent
            $destination_table->asset_id = null;
            //$destination_table->parent_id = 1;
            $destination_table->lft = null;
            $destination_table->rgt = null;
            $destination_table->level = null;
            $destination_table->path = null;
            $destination_table->extension = $task->extension_name;
            $destination_table->language = '*';
            $destination_table->created_user_id = $user->id;
            if ($item['image'] != "") {
                $destination_table->params = '{"category_layout":"","image":"images\/stories\/' .
                        $item['image'] .
                        '"}';
            }

            // Store
            if (!$destination_table->store()) {
                if ($params->get("duplicate_alias", 0)) {
                    $destination_table->alias .= '-sp-' . rand(100, 999);
                    if (!$destination_table->store()) {
                        // delete record
                        $destination_db->setQuery(
                                "DELETE FROM #__categories" .
                                " WHERE id = " . $destination_db->quote($item['id'])
                        );
                        if (!$factory->execute($destination_db)) {
                            jexit($destination_db->getErrorMsg());
                        }
                        $tableLog->note = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_STORE', $item['id'], $destination_table->getError()) . '</p>';
                        $tableLog->store();
                        continue;
                    }
                    $tableLog->note = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_DUPLICATE_ALIAS', $item['id'], $destination_table->alias) . '</p>';
                } else {
                    // delete record
                    $destination_db->setQuery(
                            "DELETE FROM #__categories" .
                            " WHERE id = " . $destination_db->quote($item['id'])
                    );
                    if (!$factory->execute($destination_db)) {
                        jexit($destination_db->getErrorMsg());
                    }
                    $tableLog->note = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_STORE', $item['id'], $destination_table->getError()) . '</p>';
                    $tableLog->store();
                    continue;
                }
            }

            //Log
            $tableLog->state = $task->state;
            $tableLog->store();
        } //Main loop end
        // Rebuild the hierarchy.
        if (!$destination_table->rebuild()) {
            jexit('<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_REBUILD', $destination_table->getError()) . '</p>');
        }

        // Clear the component's cache
        $cache = JFactory::getCache('com_categories');
        $cache->clean();
    }
    
    private function categories_fix($ids = null) {
        // Initialize
        $jAp = $this->jAp;
        $factory = $this->factory;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        $destination_table = $factory->getTable('Category', 'JTable');
        $user = $this->user;
        $params = $this->params;
        $task = $this->task;

        // Load items
        $query = 'SELECT destination_id
            FROM #__spupgrade_log
            WHERE tables_id = ' . (int) $task->id . ' AND ( state = 2 OR state = 3 )';
        if (!empty($ids))
            $query .= ' AND source_id IN (' . implode(',', $ids) . ')';
        $query .= ' ORDER BY id ASC';
        $destination_db->setQuery($query);
        if (!CYENDFactory::execute($destination_db)) {
            jexit($destination_db->getErrorMsg());
        }
        $temp = $destination_db->loadColumn();

        // Return if empty
        if (is_null($temp[0])) {
            return;
        }

        $query = 'SELECT * 
            FROM #__categories
            WHERE extension LIKE "com_jevents"
            AND parent_id > 0';
        $query .= ' AND id IN (' . implode(',', $temp) . ')';
        $query .= ' ORDER BY id ASC';
        $destination_db->setQuery($query);
        $result = CYENDFactory::execute($destination_db);
        if (!$result) {
            jexit($destination_db->getErrorMsg());
        }
        $items = $destination_db->loadAssocList();

        //percentage
        $percTotal = count($items);
        if ($percTotal < 100)
            $percKlasma = 0.1;
        if ($percTotal > 100 && $percTotal < 2000)
            $percKlasma = 0.05;
        if ($percTotal > 2000)
            $percKlasma = 0.01;
        $percTen = $percKlasma * $percTotal;
        $percCounter = 0;

        // Loop to save items
        foreach ($items as $i => $item) {           
            
            //status pending
            $this->batch -= 1;
            if ($this->batch < 0) 
                return;

            //parent_id
            if ($item['parent_id'] > 0) {
                $tableLog->reset();
                $tableLog->id = null;
                $tableLog->load(array("tables_id" => 164, "source_id" => $item['parent_id']));
                if ($tableLog->source_id == $tableLog->destination_id) {
                    $tableLog->load(array("tables_id" => $task->id, "destination_id" => $item['id']));
                    $tableLog->state = 4;
                    $tableLog->store();
                    continue;
                }
                $item['parent_id'] = $tableLog->destination_id;
            } else {
                $tableLog->load(array("tables_id" => $task->id, "destination_id" => $item['id']));
                $tableLog->state = 4;
                $tableLog->store();
                continue;
            }

            //log            
            $tableLog->reset();
            $tableLog->id = null;
            $tableLog->load(array("tables_id" => $task->id, "destination_id" => $item['id']));
            $tableLog->created = null;
            $tableLog->state = 3;
            $tableLog->tables_id = $task->id;

            // Reset
            $destination_table->reset();

            // Bind
            if (!$destination_table->bind($item)) {
                $tableLog->note = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_BIND', $item['id'], $destination_table->getError()) . '</p>';
                $tableLog->store();
                continue;
            }

            // Store
            if (!$destination_table->store()) {
                if ($params->get("duplicate_alias", 0)) {
                    $destination_table->alias .= '-sp-' . rand(100, 999);
                    if (!$destination_table->store()) {
                        // delete record
                        $destination_db->setQuery(
                                "DELETE FROM #__categories" .
                                " WHERE id = " . $destination_db->quote($item['id'])
                        );
                        if (!CYENDFactory::execute($destination_db)) {
                            jexit($destination_db->getErrorMsg());
                        }
                        $tableLog->note = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_STORE', $item['id'], $destination_table->getError()) . '</p>';
                        $tableLog->store();
                        continue;
                    }
                    $tableLog->note = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_DUPLICATE_ALIAS', $item['id'], $destination_table->alias) . '</p>';
                } else {
                    // delete record
                    $destination_db->setQuery(
                            "DELETE FROM #__categories" .
                            " WHERE id = " . $destination_db->quote($item['id'])
                    );
                    if (!CYENDFactory::execute($destination_db)) {
                        jexit($destination_db->getErrorMsg());
                    }
                    $tableLog->state = 1;
                    $tableLog->note = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_STORE', $item['id'], $destination_table->getError()) . '</p>';
                    $tableLog->store();
                    continue;
                }
            }

            //Log
            $tableLog->state = 4;
            $tableLog->store();
        } //Main loop end   
        // Rebuild the hierarchy.
        if (!$destination_table->rebuild()) {
            jexit($destination_table->getError());
        }

        // Clear the component's cache
        $cache = JFactory::getCache('com_categories');
        $cache->clean();
    }
}
