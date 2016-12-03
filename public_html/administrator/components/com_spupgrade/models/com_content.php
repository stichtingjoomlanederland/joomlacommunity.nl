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

class SPUpgradeModelCom_Content extends SPUpgradeModelCom {
    
    public function __construct($config = array()) {
        
        parent::__construct($config);
        
        switch ($this->task->name) {
            case 'sections':
                $this->id = 'id';
                $this->table_name = 'sections';

                break;

            case 'categories':
                $this->id = 'id';
                $this->table_name = 'categories';

                break;

            case 'content':
                $this->id = 'id';
                $this->table_name = 'content';

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

    public function sections($pks = null) {
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

        $message = ('<h2>' . JText::_(COM_CONTENT) . ' - ' . JText::_(COM_CONTENT_SECTIONS) . '</h2>');
        //$factory->writeLog($message);
        // Load items
        $query = 'SELECT source_id
            FROM #__spupgrade_log
            WHERE tables_id = ' . (int) $task->id . ' AND state >= 2
            ORDER BY id ASC';
        $destination_db->setQuery($query);
        if (!CYENDFactory::execute($destination_db)) {
            jexit($destination_db->getErrorMsg());
        }
        $excludes = $destination_db->loadColumn();

        //Find ids
        if (is_null($pks[0])) {
            $existing_id = true;
            $query = 'SELECT id
            FROM #__sections
            WHERE id > 0';
            $source_db->setQuery($query);
            if (!$factory->execute($source_db)) {
                jexit($source_db->getErrorMsg());
            }
            $pks = $source_db->loadColumn();
        } else {
            $existing_id = false;
        }

        //Find total number of categories
        $query = 'SELECT id' .
                ' FROM #__categories' .
                ' ORDER BY id DESC';
        $source_db->setQuery($query);
        if (!CYENDFactory::execute($source_db)) {
            jexit($source_db->getErrorMsg());
        }
        $catnum = $source_db->loadResult();

        // Loop to save items
        foreach ($pks as $i => $pk) {

            //Load data from source
            $exclude = array_search($pk, $excludes);
            if ($exclude !== false) {
                unset($excludes[$exclude]);
                continue;
            }
            
            $query = 'SELECT * FROM #__sections' .
                    ' WHERE id = ' . $pk;
            $source_db->setQuery($query);
            if (!$factory->execute($source_db)) {
                if ($existing_id) {
                    jexit($source_db->getErrorMsg());
                } else {
                    continue;
                }
            }
            $item = $source_db->loadAssoc();

            if (empty($item)) {
                if ($existing_id) {
                    jexit($source_db->getErrorMsg());
                } else
                    continue;
            }

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
            $item['id'] = $catnum + $i + 1; //find destination id
            $tableLog->destination_id = $item['id'];
            $tableLog->state = 1;
            $tableLog->tables_id = $task->id;

            //access difference
            if ($item['access'] > 2) {
                $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_ACCESSLEVEL', $item['id']) . '</p>';
                //$factory->writeLog($message);
                continue;
            }
            if ($item['access'] == 2)
                $item['access'] = 3;
            if ($item['access'] == 1)
                $item['access'] = 2;
            if ($item['access'] == 0)
                $item['access'] = 1;

            // Create record
            $destination_db->setQuery(
                    "INSERT INTO #__categories" .
                    " (id)" .
                    " VALUES (" . $destination_db->quote($item['id']) . ")"
            );
            if (!CYENDFactory::execute($destination_db)) {
                if ($params->get("new_ids", 0) == 1) {
                    $destination_db->setQuery(
                            "INSERT INTO #__categories" .
                            " (title)" .
                            " VALUES (" . $destination_db->quote('sp_transfer') . ")"
                    );
                    if (!CYENDFactory::execute($destination_db)) {
                        $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                        //$factory->writeLog($message);
                        $tableLog->note = $message;
                        $tableLog->store();
                        continue;
                    }
                    $destination_db->setQuery(
                            "SELECT id FROM #__categories" .
                            " WHERE title LIKE " . $destination_db->quote('sp_transfer')
                    );
                    CYENDFactory::execute($destination_db);
                    $tableLog->destination_id = $destination_db->loadResult();
                    $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_NEW_IDS', $item['id'], $tableLog->destination_id) . '</p>';
                    $item['id'] = $tableLog->destination_id;
                    //$factory->writeLog($message);
                    $tableLog->note = $message;
                } elseif ($params->get("new_ids", 0) == 0) {
                    $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                    //$factory->writeLog($message);
                    $tableLog->note = $message;
                    $tableLog->store();
                    continue;
                }
            }

            // Reset
            $destination_table->reset();

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
                if (!CYENDFactory::execute($destination_db)) {
                    $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                    //$factory->writeLog($message);
                }
                $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_BIND', $item['id'], $destination_table->getError()) . '</p>';
                //$factory->writeLog($message);
                $tableLog->note = $message;
                $tableLog->store();
                continue;
            }

            //no parent
            $destination_table->asset_id = null;
            $destination_table->parent_id = 1;
            $destination_table->lft = null;
            $destination_table->rgt = null;
            $destination_table->level = null;
            $destination_table->path = null;
            $destination_table->extension = "com_content";
            $destination_table->language = '*';

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
                    $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_DUPLICATE_ALIAS', $item['id'], $destination_table->alias) . '</p>';
                    //$factory->writeLog($message);
                    $tableLog->note = $message;
                } else {
                    // delete record
                    $destination_db->setQuery(
                            "DELETE FROM #__categories" .
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

            //Log
            $tableLog->state = 4;
            $tableLog->store();
        } //Main loop end
        
        // Rebuild the hierarchy.
        if (!$destination_table->rebuild()) {
            jexit($destination_db->getErrorMsg());
        }

        // Clear the component's cache
        $cache = JFactory::getCache('com_categories');
        $cache->clean();
        
        //status completed
        $this->status = 'completed';
        
    }

    public function categories($ids = null) {
        //initialize
        $this->destination_table = $this->factory->getTable('Category', 'JTable');
        $this->task->section = ' WHERE section NOT LIKE "com_%"';
        $this->task->state = 2; //state for success

        parent::categories($ids);
        
        //status pending
        $this->status = 'pending';
        
        //Fix categories
        $this->categories_fix($ids);
    }

    public function content($ids = null) {
        //initialize
        $this->destination_table = $this->factory->getTable('Content', 'JTable');
        $this->table_name = 'content';
        $this->task->category = 4;
        $this->id = 'id';
        $this->task->query = 'SELECT * 
            FROM #__' . $this->table_name . '
            WHERE ' . $this->id . ' > 0';

        $this->items($ids);
    }

    private function categories_fix($pks = null) {
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

        $message = ('<h2>' . JText::_('COM_CONTENT') . ' - ' . JText::_('COM_CONTENT_CATEGORIES') . ' - ' . JText::_('COM_SPUPGRADE_FIX') . '</h2>');
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
            $query = 'SELECT * 
            FROM #__categories
            WHERE extension LIKE "com_content"
            AND parent_id > 0';
            $query .= ' ORDER BY id ASC';
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
            FROM #__categories' .
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

            //parent_id
            if ($item['parent_id'] > 0) {
                $tableLog->reset();
                $tableLog->id = null;
                $tableLog->load(array("tables_id" => 3, "source_id" => $item['parent_id']));
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
                $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_BIND', $item['id'], $destination_table->getError()) . '</p>';
                //$factory->writeLog($message);
                $tableLog->note = $message;
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
                            $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                            //$factory->writeLog($message);
                        }
                        $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_STORE', $item['id'], $destination_table->getError()) . '</p>';
                        //$factory->writeLog($message);
                        $tableLog->note = $message;
                        $tableLog->store();
                        continue;
                    }
                    $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_DUPLICATE_ALIAS', $item['id'], $destination_table->alias) . '</p>';
                    //$factory->writeLog($message);
                    $tableLog->note = $message;
                } else {
                    // delete record
                    $destination_db->setQuery(
                            "DELETE FROM #__categories" .
                            " WHERE id = " . $destination_db->quote($item['id'])
                    );
                    if (!CYENDFactory::execute($destination_db)) {
                        $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                        //$factory->writeLog($message);
                    }
                    $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_STORE', $item['id'], $destination_table->getError()) . '</p>';
                    //$factory->writeLog($message);
                    $tableLog->state = 1;
                    $tableLog->note = $message;
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
        
        //status completed
        $this->status = 'completed';
    }

}
