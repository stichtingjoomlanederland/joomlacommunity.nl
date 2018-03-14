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
jimport('joomla.application.component.model');

class SPUpgradeModelCom_Menus extends SPUpgradeModelCom {

    public function __construct($config = array()) {
        
        parent::__construct($config);
        
        switch ($this->task->name) {

            case 'menu_types':
                $this->id = 'id';
                $this->table_name = 'menu_types';

                break;

            case 'menu':
                $this->id = 'id';
                $this->table_name = 'menu';

                break;

            default:
                break;
        }

        $jinput = JFactory::$application->input;
        if ($jinput->get('task') == 'transfer_all') {
            $this->params->set('duplicate_alias', 1);
            $this->params->set('new_ids', 2);
        }
    }

    public function menu_types($ids = null) {
        //initialize
        $this->destination_table = $this->factory->getTable('MenuType', 'JTable');
        $this->table_name = 'menu_types';
        $this->task->category = null;
        $this->id = 'id';
        $this->task->query = 'SELECT ' . $this->id . ' 
            FROM #__' . $this->table_name . '
            WHERE ' . $this->id . ' > 0';

        $this->delete_empty();
        $this->items($ids);
    }

    private function delete_empty() {

        $this->destination_db->setQuery(
                'DELETE FROM #__menu_types' .
                " WHERE  `menutype` =  ''"
        );
        CYENDFactory::execute($this->destination_db);
    }

    public function menu($ids = null) {
        //initialize
        $this->destination_table = $this->factory->getTable('Menu', 'JTable');
        $this->table_name = 'menu';
        $this->task->category = null;
        $this->id = 'id';
        $this->task->query = $this->menu_query();
        if (!$this->task->query)
            return false;
        $this->task->state = 2; //state for success

        $this->move();

        $this->items($ids);
        
        //status pending
        $this->status = 'pending';

        $this->task->query = 'SELECT * 
            FROM #__menu
            WHERE id > 0';

        $this->fix($ids);

        $this->fix_root_menu();

    }

    private function menu_query() {
        //Filter per menu_types already transferred
        $query = $this->destination_db->getQuery(true);
        $query->select('a.menutype');
        $query->from('#__menu_types AS a');
        $query->join('LEFT', '`#__spupgrade_log` AS b ON b.destination_id = a.id');
        $query->where('b.tables_id = 15 AND b.state >= 2');
        $query->order('b.id ASC');
        $this->destination_db->setQuery($query);
        $result = CYENDFactory::execute($this->destination_db);
        if (!$result) {
            jexit($this->destination_db->getErrorMsg());
        }
        $temp2 = $this->destination_db->loadColumn();

        if (is_null($temp2[0])) {
            return false;
        }

        foreach ($temp2 as $i => $temp3) {
            if (strpos($temp3, '-sp-')) {
                $temp4 = explode('-sp-', $temp3);
                $temp3 = $temp4[0];
            }
            $temp2[$i] = '"' . $temp3 . '"';
        }

        $query = 'SELECT id 
            FROM #__menu
            WHERE id > 1';
        $query .= ' AND menutype IN (' . implode(',', $temp2) . ')';

        return $query;
    }

    private function fix($pks = null) {
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
        $this->task->state = 4; //state for success
        $id = $this->id;
        $table_name = $this->table_name;

        $message = ('<h2>' . JText::_($task->extension_name) . ' - ' . JText::_($task->extension_name . '_' . $task->name) . ' - ' . JText::_('COM_SPUPGRADE_FIX') . '</h2>');
        //$factory->writeLog($message);
        // Load items
        $query = 'SELECT destination_id
            FROM #__spupgrade_log
            WHERE tables_id = ' . (int) $task->id . ' AND ( state = 2 OR state = 3 )';
        $query .= ' ORDER BY id ASC';
        $destination_db->setQuery($query);
        if (!CYENDFactory::execute($destination_db)) {
            jexit($destination_db->getErrorMsg());
        }
        $excludes = $destination_db->loadColumn();

        //Find ids
        if (is_null($pks[0])) {
            $existing_id = true;
            $query = $this->task->query;
            $query .= ' ORDER BY ' . $id . ' ASC';
            $destination_db->setQuery($query);
            if (!$factory->execute($destination_db)) {
                jexit($destination_db->getErrorMsg());
            }
            $pks = $destination_db->loadColumn();
        } else {
            $existing_id = false;
        }

        // Loop to save items
        foreach ($pks as $pk) {

            //Load data from source
            $exclude = array_search($pk, $excludes);
            if ($exclude === false)
                continue;
            else
                unset($excludes[$exclude]);

            $query = 'SELECT * 
            FROM #__menu' .
                    ' WHERE id = ' . $pk;
            $destination_db->setQuery($query);
            if (!$factory->execute($destination_db)) {
                if ($existing_id) {
                    jexit($destination_db->getErrorMsg());
                } else {
                    continue;
                }
            }
            $item = $destination_db->loadAssoc();
            
            if (empty($item)) {
                if ($existing_id) {
                    jexit($destination_db->getErrorMsg());
                } else
                    continue;
            }
            
            //status pending
            $this->batch -= 1;
            if ($this->batch < 0) 
                return;

            //sections
            if (strpos($item['link'], 'view=section') > 0) {
                $link = explode('&', $item['link']);
                foreach ($link as $value) {
                    $pos = strpos($value, 'id=');
                    if ($pos === 0) {
                        $id = substr($value, 3);
                        $strId = $value;
                    }
                }

                $tableLog->reset();
                $tableLog->id = null;
                $tableLog->load(array("tables_id" => 3, "source_id" => $id));
                $id = $tableLog->destination_id;

                $item['link'] = str_replace('view=section', 'view=category', $item['link']);
                $item['link'] = str_replace($strId, 'id=' . $id, $item['link']);
            }

            //menu link
            if ($item['type'] == 'alias' || $item['type'] == 'menulink') {
                $params = json_decode($item['params']);
                $id = $params->aliasoptions;
                $tableLog->reset();
                $tableLog->id = null;
                $tableLog->load(array("tables_id" => $task->id, "source_id" => $id));
                $params->aliasoptions = $tableLog->destination_id;
                $item['params'] = json_encode($params);
            }

            //Itemid
            if ((strpos($item['link'], 'Itemid')) > 0) {
                $link = explode('?', $item['link']);
                foreach ($link as $value) {
                    $pos = strpos($value, 'Itemid=');
                    if ($pos === 0) {
                        $id = substr($value, 7);
                        $strId = $value;
                    }
                }

                $tableLog->reset();
                $tableLog->id = null;
                $tableLog->load(array("tables_id" => $task->id, "source_id" => $id));
                $id = $tableLog->destination_id;
                $item['link'] = str_replace($strId, 'Itemid=' . $id, $item['link']);
            }

            //parent_id
            if ($item['parent_id'] > 1) {
                $tableLog->reset();
                $tableLog->id = null;
                $tableLog->load(array("tables_id" => $task->id, "source_id" => $item['parent_id']));
                if ($tableLog->source_id == $tableLog->destination_id) {
                    $tableLog->load(array("tables_id" => $task->id, "destination_id" => $item['id']));
                    $tableLog->state = 4;
                    $tableLog->store();
                    continue;
                }
                $item['parent_id'] = $tableLog->destination_id;
            } /* else {
              $tableLog->reset();
              $tableLog->id = null;
              $tableLog->load(array("tables_id" => $task->id, "destination_id" => $item['id']));
              $tableLog->state = 4;
              $tableLog->store();
              continue;
              }
             * 
             */

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
                $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_BIND', $item['id'], $destination_table->getError()) . '</p>';
                //$factory->writeLog($message);
                $tableLog->note = $message;
                $tableLog->store();
                continue;
            }

            //reset path
            $destination_table->path = null;

            // Store
            if (!$destination_table->store()) {
                if ($params->get("duplicate_alias", 0)) {
                    if ($task->extension_name . '_' . $task->name == 'com_menus_menu_types') {
                        $destination_table->menutype .= '-sp-' . rand(100, 999);
                        $alias = $destination_table->menutype;
                    } else {
                        $destination_table->alias .= '-sp-' . rand(100, 999);
                        $alias = $destination_table->alias;
                    }
                    if (!$destination_table->store()) {
                        // delete record
                        $destination_db->setQuery(
                                "DELETE FROM #__" . $table_name .
                                " WHERE id = " . $destination_db->quote($item['id'])
                        );
                        if (!CYENDFactory::execute($destination_db)) {
                            $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                            //$factory->writeLog($message);
                        }
                        $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_STORE', $item['id'], $destination_table->getError()) . '</p>';
                        //$factory->writeLog($message);
                        $tableLog->note = $message;
                        $tableLog->store();
                        continue;
                    }
                    $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_DUPLICATE_ALIAS', $item['id'], $alias) . '</p>';
                    //$factory->writeLog($message);
                    $tableLog->note = $message;
                } else {
                    // delete record
                    $destination_db->setQuery(
                            "DELETE FROM #__" . $table_name .
                            " WHERE id = " . $destination_db->quote($item['id'])
                    );
                    if (!CYENDFactory::execute($destination_db)) {
                        $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                        //$factory->writeLog($message);
                    }
                    $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_STORE', $item['id'], $destination_table->getError()) . '</p>';
                    //$factory->writeLog($message);
                    $tableLog->note = $message;
                    $tableLog->store();
                    continue;
                }
            }

            // Rebuild the tree path.
            if (!$destination_table->rebuildPath($destination_table->id)) {
                jexit($destination_table->getError());
            }

            //Log
            $tableLog->state = 4;
            $tableLog->store();
        } //Main loop end  
        
        //fix ordering
        $this->fix_ordering();

        // Rebuild the hierarchy.
        if (!$destination_table->rebuild()) {
            jexit($destination_table->getError());
        }

        // Clear the component's cache
        $cache = JFactory::getCache('com_categories');
        $cache->clean();
        
        //status completed
        $this->status = 'completed';
    }

    private function fix_root_menu() {
        //fix root menu
        $this->destination_db->setQuery(
                "UPDATE `#__menu` SET `menutype` = '' WHERE `id` = 1;"
        );
        CYENDFactory::execute($this->destination_db);

        //fix auto increment
        $this->destination_db->setQuery(
                "ALTER TABLE `#__menu` AUTO_INCREMENT = 1;"
        );
        CYENDFactory::execute($this->destination_db);
    }

    private function move() {

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
        $id = $this->id;
        $table_name = $this->table_name;

        //exit if ran again
        $query = 'SELECT destination_id
            FROM #__spupgrade_log
            WHERE tables_id = ' . (int) $task->id;
        $destination_db->setQuery($query);
        if (!CYENDFactory::execute($destination_db)) {
            jexit($destination_db->getErrorMsg());
        }
        $result = $destination_db->loadColumn();
        if (!empty($result)) {
            return true;
        }

        $source_db = $this->source_db;
        $source_query = $this->source_query;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;

        //check if already moved
        $destination_query->clear();
        $destination_query->select('id')->from('#__menu')->where('id = 2');
        $destination_db->setQuery($destination_query);
        $check_id = $destination_db->loadResult();
        if (empty($check_id))
            return;

        //find last id
        $source_query->clear();
        $source_query->select('id')->from('#__menu')->where('id > 1')->order('id desc');
        $source_db->setQuery($source_query);
        $source_last_id = $source_db->loadResult(); //destination last id

        $destination_query->clear();
        $destination_query->select('id, parent_id')->from('#__menu')->where('id > 1')->order('id desc');
        $destination_db->setQuery($destination_query);
        $ids = $destination_db->loadAssocList();

        //update ids        
        foreach ($ids as $id) {
            $destination_query->clear();
            $destination_query->update('#__menu');
            $destination_query->set('id = ' . (int) ($id['id'] + $source_last_id));
            if ($id['parent_id'] > 1)
                $destination_query->set('parent_id = ' . (int) ($id['parent_id'] + $source_last_id));
            $destination_query->where('id = ' . (int) $id['id']);

            $destination_db->setQuery($destination_query);
            CYENDFactory::execute($this->destination_db);
        }
    }

    private function fix_ordering() {
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
        $this->task->state = 4; //state for success
        $id = $this->id;
        $table_name = $this->table_name;

        $message = ('<p>Fixing ordering...</p>');
        //$factory->writeLog($message);
        // Load ids
        $query = 'SELECT destination_id
            FROM #__spupgrade_log
            WHERE tables_id = ' . (int) $task->id;
        $query .= ' ORDER BY id ASC';
        $destination_db->setQuery($query);
        if (!CYENDFactory::execute($destination_db)) {
            jexit($destination_db->getErrorMsg());
        }
        $idArray = $destination_db->loadColumn();

        // Load ordering
        $query = 'SELECT data
            FROM #__spupgrade_log
            WHERE tables_id = ' . (int) $task->id;
        $query .= ' ORDER BY id ASC';
        $destination_db->setQuery($query);
        if (!CYENDFactory::execute($destination_db)) {
            jexit($destination_db->getErrorMsg());
        }
        $lft_array_encoded = $destination_db->loadColumn();
        $lft_array = array();
        foreach ($lft_array_encoded as $key => $value) {
            $lft_array[] = json_decode($value);
        }

        if (!$destination_table->saveorder($idArray, $lft_array)) {
            jexit($destination_table->getError());
        }

        return true;
    }

}
