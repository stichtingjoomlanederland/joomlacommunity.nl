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

class SPUpgradeModelCom extends JModelLegacy {

    protected $jAp;
    protected $tableLog;
    protected $destination_db;
    protected $destination_query;
    protected $destination_table;
    protected $table_name;
    protected $source_db;
    protected $source_query;
    protected $user;
    protected $params;
    public $task;
    protected $factory;
    protected $source;
    protected $id;
    protected $batch;
    protected $status;

    function __construct($config = array()) {
        
        parent::__construct($config);
        
        $this->factory = new CYENDFactory();
        $this->source = new CYENDSource();
        $this->jAp = & JFactory::getApplication();
        $this->tableLog = $this->factory->tableLog;
        $this->tableLog = $this->factory->getTable('Log', 'SPUpgradeTable');
        $this->destination_db = $this->getDbo();
        $this->destination_query = $this->destination_db->getQuery(true);
        $this->source_db = $this->source->source_db;
        $this->source_query = $this->source_db->getQuery(true);
        $this->user = JFactory::getUser();
        $this->params = JComponentHelper::getParams('com_spupgrade');
        $this->batch = $this->params->get('batch', 100);
        $this->task = $config['task'];
        $this->status = $config['status'];
    }

    public function categories($pks = null) {
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

        $message = ('<h2>' . JText::_($task->extension_name) . ' - ' . JText::_($task->extension_name . '_CATEGORIES') . '</h2>');
        //$factory->writeLog($message);

        // Load items
        $query = 'SELECT source_id
            FROM #__spupgrade_log
            WHERE tables_id = ' . (int) $task->id . ' AND state >= 2
            ORDER BY id ASC';
        $destination_db->setQuery($query);
        if (!$factory->execute($destination_db)) {
            jexit($destination_db->getErrorMsg());
        }
        $excludes = $destination_db->loadColumn();

        //Find ids
        if (is_null($pks[0])) {
            $existing_id = true;
            $query = 'SELECT id 
            FROM #__categories ' .
                    $task->section;
            $query .= ' ORDER BY id ASC';
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
        if (!$factory->execute($source_db)) {
            jexit($source_db->getErrorMsg());
        }
        $catnum = $source_db->loadResult();

        // Loop to save items
        foreach ($pks as $pk) {

            //Load data from source
            $exclude = array_search($pk, $excludes);
            if ($exclude !== false) {
                unset($excludes[$exclude]);
                continue;
            }
            
            $query = 'SELECT * FROM #__categories' .
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
                    $factory->execute($destination_db);
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
            //parent_id
            if ((strpos($item['section'], 'com_')) === false) {
                $item['parent_id'] = $item['section'];
            } else {
                $item['parent_id'] = 1;
            }

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
                    if (!$factory->execute($destination_db)) {
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
            $tableLog->state = $task->state;
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

    public function items($pks = null) {
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

        $max = getrandmax();

        $message = ('<h2>' . JText::_($task->extension_name) . ' - ' . JText::_($task->extension_name . '_' . $task->name) . '</h2>');
        //$factory->writeLog($message);

        // Load items
        $query = 'SELECT source_id
            FROM #__spupgrade_log
            WHERE tables_id = ' . (int) $task->id . ' AND state >= 2
            ORDER BY id ASC';
        $destination_db->setQuery($query);
        if (!$factory->execute($destination_db)) {
            jexit($destination_db->getErrorMsg());
        }
        $excludes = $destination_db->loadColumn();

        //Find ids
        if (is_null($pks[0])) {
            $existing_id = true;
            $query = $this->task->query;
            $query .= ' ORDER BY ' . $id . ' ASC';
            $source_db->setQuery($query);
            if (!$factory->execute($source_db)) {
                jexit($source_db->getErrorMsg());
            }
            $pks = $source_db->loadColumn();
        } else {
            $existing_id = false;
        }

        // Loop to save items
        foreach ($pks as $pk) {

            //Load data from source
            $exclude = array_search($pk, $excludes);
            if ($exclude !== false) {
                unset($excludes[$exclude]);
                continue;
            }
            
            $query = 'SELECT * FROM #__' . $this->table_name .
                    ' WHERE ' . $id . ' = ' . $pk;
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

            //banner special
            if ($task->extension_name . '_' . $task->name == 'com_banners_banner_clients') {
                $item['id'] = $item['cid'];
                $item['state'] = 1;
                $table_name = 'banner_clients';
            }
            if ($task->extension_name . '_' . $task->name == 'com_banners_banners') {
                $item['id'] = $item['bid'];
                $item['state'] = $item['showBanner'];
                $table_name = 'banners';
            }
            //menu special
            if ($task->extension_name . '_' . $task->name == 'com_menus_menu') {
                if ($item[id] == 1)
                    continue;
            }

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
            if (array_key_exists('ordering', $item)) {
                $tableLog->data = json_encode($item['ordering']);
            }
            $tableLog->store();

            //Fix item
            //fix item for all            
            $item = $this->com_item($item);

            //add extra coding
            switch ($task->extension_name . '_' . $task->name) {
                case 'com_menus_menu':
                    $item = $this->com_menus_menu_item($item);
                    break;
                case 'com_modules_modules':
                    $item = $this->com_modules_modules_item($item);
                    break;
                case 'com_banners_banners':
                    $item = $this->com_banners_banners_item($item);
                    break;
            }
            if ($item === false) {
                $tableLog->delete();
                continue;
            }

            // Create record
            $destination_db->setQuery(
                    "INSERT INTO #__" . $table_name .
                    " (id)" .
                    " VALUES (" . $destination_db->quote($item['id']) . ")"
            );
            if (!$factory->execute($destination_db)) {
                if ($params->get("new_ids", 0) == 1) {
                    $destination_db->setQuery(
                            "INSERT INTO #__" . $table_name .
                            " (id)" .
                            " VALUES (" . $destination_db->quote(0) . ")"
                    );
                    if (!$factory->execute($destination_db)) {
                        $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                        //$factory->writeLog($message);
                        $tableLog->note = $message;
                        $tableLog->store();
                        continue;
                    }
                    $destination_db->setQuery(
                            "SELECT id FROM #__" . $table_name .
                            " ORDER BY id DESC "
                    );
                    $factory->execute($destination_db);
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
                        "DELETE FROM #__" . $table_name .
                        " WHERE id = " . $destination_db->quote($item['id'])
                );
                if (!$factory->execute($destination_db)) {
                    $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                    //$factory->writeLog($message);
                }
                $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_BIND', $item['id'], $destination_table->getError()) . '</p>';
                //$factory->writeLog($message);
                $tableLog->note = $message;
                $tableLog->store();
                continue;
            }

            // Store
            if (!$destination_table->store()) {
                if ($params->get("duplicate_alias", 0)) {
                    if ($task->extension_name . '_' . $task->name == 'com_menus_menu_types') {
                        $destination_table->menutype .= '-sp-' . rand(100, $max);
                        $alias = $destination_table->menutype;
                    } else {
                        $destination_table->alias .= '-sp-' . rand(100, $max);
                        $alias = $destination_table->alias;
                    }
                    if (!$destination_table->store()) {
                        // delete record
                        $destination_db->setQuery(
                                "DELETE FROM #__" . $table_name .
                                " WHERE id = " . $destination_db->quote($item['id'])
                        );
                        if (!$factory->execute($destination_db)) {
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
                    if (!$factory->execute($destination_db)) {
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

            $this->com_modified($destination_table, $item); //fix modified date
            //add extra coding
            switch ($task->extension_name . '_' . $task->name) {
                case 'com_content_content':
                    $this->com_content_content($this->ordering);
                    break;
                case 'com_banners_banners':
                    $this->com_banners_banners();
                    $this->com_banners_banners_tracks($item);
                    break;
                case 'com_modules_modules':
                    $this->com_modules_modules_menu($item);
                    break;
            }

            //Log
            if (is_null($task->state))
                $task->state = 4;
            $tableLog->state = $task->state;
            $tableLog->store();
        } //Main loop end
        
        //status completed
        $this->status = 'completed';
    }

    private function com_item($item) {
        //handle params
        $item_params = explode("\n", $item['params']);
        foreach ($item_params as $key => $param) {
            $attribs = explode("=", $param);
            if (count($attribs) > 1) {
                $attribs[0] = trim($attribs[0]);
                if ($attribs[0] == '')
                    continue;
                if ($attribs[0] == 'timezone') {
                    $new_params[$attribs[0]] = '';
                } else {
                    $new_params[$attribs[0]] = $attribs[1];
                }
            }
        }
        $item['params'] = $new_params;

        //published
        if (!is_null($item['published']))
            $item['state'] = $item['published'];
        //problem with archived
        if ($item['state'] == -1)
            $item['state'] = 2;
        //catid
        $item['catid'] = SPUpgradeGeneral::getOldId($item['catid'], $this->task->category);
        if (!$item['catid'])
            $item['catid'] = 1;
        //sectionid
        $item['sectionid'] = SPUpgradeGeneral::getOldId($item['sectionid'], 3);
        if (!$item['sectionid'])
            $item['sectionid'] = 1;
//        $item['created_by'] = SPUpgradeGeneral::getOldId($item['created_by'], 1);
//        if (!$item['created_by'])
//            $item['created_by'] = $this->user->id;

        //access difference
        if ($item['access'] > 2) {
            $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_ACCESSLEVEL', $item['id']) . '</p>';
            //$this->factory->writeLog($message);
            return;
        }
        if ($item['access'] == 2)
            $item['access'] = 3;
        if ($item['access'] == 1)
            $item['access'] = 2;
        if ($item['access'] == 0)
            $item['access'] = 1;

        //find linked user 
        if ($item['user_id'] == "")
            $item['user_id'] = 0;

        //attribs
        $item_attribs = explode("\n", $item['attribs']);
        foreach ($item_attribs as $key => $param) {
            $attribs = explode("=", $param);
            if (count($attribs) > 1) {
                $attribs[0] = trim($attribs[0]);
                if ($attribs[0] == '')
                    continue;
                if ($attribs[0] == 'readmore') {
                    $attribs[0] = 'alternative_readmore';
                } 
                if ($attribs[0] == 'timezone') {
                    $new_attribs[$attribs[0]] = '';
                } else {
                    $new_attribs[$attribs[0]] = $attribs[1];
                }
            }
        }
        $item['attribs'] = $new_attribs;

        //metadata
        $item_metadata = explode("\n", $item['metadata']);
        foreach ($item_metadata as $key => $param) {
            $metadata = explode("=", $param);
            if (count($metadata) > 1) {
                $metadata[0] = trim($metadata[0]);
                if ($metadata[0] == '')
                    continue;
                if ($metadata[0] == 'timezone') {
                    $new_metadata[$metadata[0]] = '';
                } else {
                    $new_metadata[$metadata[0]] = $metadata[1];
                }
            }
        }
        $item['metadata'] = $new_metadata;

        //frontpage to featured
        $query = 'SELECT * FROM #__content_frontpage WHERE content_id = ' . $item['id'];
        $this->source_db->setQuery($query);
        $item2 = $this->source_db->loadAssoc();
        if ($item2['content_id'] == $item['id']) {
            $item['featured'] = 1;
            $this->ordering = $item2['ordering'];
        }

        //asset id
        $item['asset_id'] = null;
        //language
        $item['language'] = '*';

        return $item;
    }

    private function com_banners_banners() {
        //banner tracks
        $query = 'SELECT *
                FROM #__bannertrack
                WHERE banner_id = ' . (int) $this->tableLog->source_id;
        $this->source_db->setQuery($query);
        CYENDFactory::execute($this->source_db);
        $result = $this->source_db->loadAssoc();
        if (!is_null($result)) {
            $query = "INSERT INTO #__banner_tracks
                        (track_date, track_type, banner_id, count)
                        VALUES (" . $this->destination_db->quote($result['track_date']) . " , " .
                    $this->destination_db->quote($result['track_type']) . " , " .
                    $this->destination_db->quote($this->tableLog->destination_id) . " , " .
                    $this->destination_db->quote($result['count']) .
                    ")";
            $this->destination_db->setQuery($query);
            if (!CYENDFactory::execute($this->destination_db)) {
                $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CREATE', $item['id'], $this->destination_db->getErrorMsg()) . '</p>';
                //$this->factory->writeLog($message);
            }
        }
    }

    private function com_content_content($ordering) {
        //featured
        $query = 'SELECT *
                FROM #__content_frontpage
                WHERE content_id = ' . (int) $this->tableLog->source_id;
        $this->source_db->setQuery($query);
        CYENDFactory::execute($this->source_db);
        $result = $this->source_db->loadAssoc();
        if (!is_null($result)) {
            $this->destination_db->setQuery(
                    "INSERT INTO #__content_frontpage
                        (content_id, ordering)
                        VALUES (" . $this->tableLog->destination_id . " , " . $ordering . ")"
            );
            CYENDFactory::execute($this->destination_db);
        } else {
            $this->destination_db->setQuery(
                    "DELETE FROM #__content_frontpage
                        WHERE content_id = " . $this->destination_db->quote($this->tableLog->destination_id)
            );
            CYENDFactory::execute($this->destination_db);
        }

        //rating
        $query = 'SELECT *
                FROM #__content_rating
                WHERE content_id = ' . (int) $this->tableLog->source_id;
        $this->source_db->setQuery($query);
        CYENDFactory::execute($this->source_db);
        $result = $this->source_db->loadAssoc();
        if (!is_null($result)) {
            $this->destination_db->setQuery(
                    "INSERT INTO #__content_rating
                        (content_id, rating_sum, rating_count, lastip)
                        VALUES (" . $this->tableLog->destination_id . " , " . $result['rating_sum'] . " , " . $result['rating_count'] . " , '" . $result['lastip'] . "')"
            );
            CYENDFactory::execute($this->destination_db);
        }
    }

    private function com_modified($destinationTable, $item) { //fix modified date
        if (empty($item['modified']))
            return true;

        $tableName = $destinationTable->getTableName();
        $id = $destinationTable->id;
        $dbo = $this->destination_db;
        $query = $this->destination_query;
        $query->clear();
        $query->update($tableName);
        $query->set('modified = ' . $dbo->quote($item['modified']));
        $query->where('id = ' . $id);
        $dbo->setQuery($query);
        $dbo->execute();
        return true;
    }

    private function com_menus_menu_item($item) {
        /*
          $sql = 'SELECT id FROM #__menu ORDER BY id DESC';
          $this->destination_db->setQuery($sql);
          $this->factory->execute($this->destination_db);
          $item['id'] = $this->destination_db->loadResult() + 1;
         * 
         */

        $this->tableLog->destination_id = $item['id'];

        if ($item['parent'] == 0) {
            $item['parent_id'] = 1;
        } else {
            $item['parent_id'] = $item['parent'];
        }

        $item['title'] = $item['name'];
        $item['template_style_id '] = 0;

        //$item['params'] = str_replace("mseparator", "\n", $item['params']);
        if ($item['params']['image'] == -1) {
            $item['params']['image'] = null;
        }
        if (!is_null($item['params']['image'])) {
            $item['params']['image'] = 'images/stories/' . $item['params']['image'];
        }
        if ($item['params']['menu_image'] == -1) {
            $item['params']['menu_image'] = null;
        }
        if (!is_null($item['params']['menu_image'])) {
            $item['params']['menu_image'] = 'images/stories/' . $item['params']['menu_image'];
        }
        if (!is_null($item['params']['show_page_title'])) {
            $item['params']['show_page_heading'] = $item['params']['show_page_title'];
        } else {
            $item['params']['show_page_heading'] = '0';
        }

        //find extenion id
        $sql = 'SELECT `option` FROM #__components WHERE id = ' . $item['componentid'];
        $this->source_db->setQuery($sql);
        $this->factory->execute($this->source_db);
        $item2 = $this->source_db->loadAssoc();

        if ($item2['option'] == 'com_user')
            $item2['option'] = 'com_users';

        $this->destination_db->setQuery(
                'SELECT extension_id' .
                ' FROM #__extensions' .
                ' WHERE name LIKE ' . $this->destination_db->quote($item2['option'])
        );
        $this->factory->execute($this->destination_db);
        $extension = $item2['option'];

        $item['component_id'] = $this->destination_db->loadResult();

        //level
        $item['level'] = $item['sublevel'] + 1;

        //Handle various components
        if ($item['type'] == 'url')
            $extension = 'url';
        if ($item['type'] == 'separator')
            $extension = 'separator';
        if ($item['type'] == 'menulink')
            $extension = 'alias';
        //if ( $item['type'] == 'wrapper') $extension = 'com_wrapper'; //for old versions

        switch ($extension) {

            case 'com_content':
                //sections
                /*
                  if (strpos($item['link'], 'view=section') > 0) {
                  $link = explode('&', $item['link']);
                  foreach ($link as $key => $value) {
                  $pos = strpos($value, 'id=');
                  if ($pos === 0) {
                  $id = substr($value, $tmp + 3);
                  $strId = $value;
                  }
                  }

                  //find parent
                  $sql = 'SELECT alias FROM #__sections WHERE id = ' . $id;
                  $this->source_db->setQuery($sql);
                  $this->factory->execute($this->source_db);
                  $item2 = $this->source_db->loadAssoc();

                  $this->destination_query->setQuery(
                  'SELECT id' .
                  ' FROM #__categories' .
                  ' WHERE alias LIKE ' . $this->destination_query->quote($item2['alias'])
                  );
                  $this->factory->execute($this->destination_db);
                  $id = $this->destination_query->loadResult();

                  $item['link'] = str_replace('view=section', 'view=category', $item['link']);
                  $item['link'] = str_replace($Itemid, '', $item['link']);
                  $item['link'] = str_replace($strId, 'id=' . $id, $item['link']);
                  }
                 * 
                 */

                //new article
                if (strpos($item['link'], 'layout=form') > 0) {
                    $item['link'] = str_replace('view=article', 'view=form', $item['link']);
                    $item['link'] = str_replace('layout=form', 'layout=edit', $item['link']);
                }

                //new featured
                if (strpos($item['link'], 'view=frontpage') > 0) {
                    $item['link'] = str_replace('view=frontpage', 'view=featured', $item['link']);
                }

                break;

            case 'com_contact':
                $item['link'] = str_replace('&catid=', '&id=', $item['link']);

                break;

            case 'com_newsfeeds':
                $item['link'] = str_replace('&catid=', '&id=', $item['link']);

                break;

            case 'com_search':

                break;

            case 'com_users':
                $item['link'] = str_replace('com_user', 'com_users', $item['link']);
                $item['link'] = str_replace('view=register', 'view=registration', $item['link']);
                $item['link'] = str_replace('view=user&layout=form', 'view=profile&layout=edit', $item['link']);
                $item['link'] = str_replace('view=user&task=edit', 'view=profile&layout=edit', $item['link']);
                if (strpos($item['link'], 'view=user') > 0)
                    return false;
                break;

            case 'com_weblinks':
                $item['link'] = str_replace('&catid=', '&id=', $item['link']);
                $item['link'] = str_replace('view=weblink&layout=form', 'view=form&layout=edit', $item['link']);

                break;

            case 'com_wrapper':
                $item['component_id'] = 2;
                $item['type'] = 'component';

                break;

            case 'url':

                break;

            case 'separator':

                break;

            case 'alias':
                $item['type'] = 'alias';
                $item['params']['aliasoptions'] = $item['params']['menu_item'];
                unset($item['params']['menu_item']);

                break;

            case 'com_spupgrade':

                break;

            default:
                $all_menus = JRequest::getInt('all_menus', 0);
                if ($all_menus == 1)
                    return false;
                break;
        }

        return $item;
    }

    private function com_modules_modules_item($item) {
        //find extenion
        $module = "";
        if ($item['module'] == 'mod_archive')
            $module = 'mod_articles_archive';
        if ($item['module'] == 'mod_banners')
            $module = 'mod_banners';
        if ($item['module'] == 'mod_custom')
            $module = 'mod_custom';
        if ($item['module'] == 'mod_feed')
            $module = 'mod_feed';
        if ($item['module'] == 'mod_footer')
            $module = 'mod_footer';
        if ($item['module'] == 'mod_latestnews')
            $module = 'mod_articles_latest';
        if ($item['module'] == 'mod_login')
            $module = 'mod_login';
        if (strpos($item['params'], 'menutype') === 0)
            $module = 'mod_menu';
        if ($item['module'] == 'mod_mostread')
            $module = 'mod_articles_popular';
        if ($item['module'] == 'mod_newsflash')
            $module = 'mod_articles_news';
        if ($item['module'] == 'mod_random_image')
            $module = 'mod_random_image';
        if ($item['module'] == 'mod_related_items')
            $module = 'mod_related_items';
        if ($item['module'] == 'mod_search')
            $module = 'mod_search';
        if ($item['module'] == 'mod_stats')
            $module = 'mod_stats';
        if ($item['module'] == 'mod_status')
            $module = 'mod_status';
        if ($item['module'] == 'mod_syndicate')
            $module = 'mod_syndicate';
        if ($item['module'] == 'mod_whosonline')
            $module = 'mod_whosonline';
        if ($item['module'] == 'mod_wrapper')
            $module = 'mod_wrapper';
        if ($item['module'] == 'mod_mainmenu')
            $module = 'mod_menu';
        if ($item['module'] == 'mod_breadcrumbs')
            $module = 'mod_breadcrumbs';

        if ($module == "") {
            $all_modules = JRequest::getInt('all_modules', 0);
            if ($all_modules == 0) {
                $module = $item['module']; //transfer all modules
            } else {
                return false;
            }
        }
        $item['module'] = $module;

        $secid = '';
        foreach ($item['params'] as $key => $value) {
            if ($key == 'secid') {
                $this->tableLog->load(array("tables_id" => 3, "source_id" => $value));
                $secid = $this->tableLog->destination_id;
                $this->tableLog->load(array("tables_id" => $this->task->id, "source_id" => $item['id']));

                if ($secid == '')
                    $secid = '';

                $params['catid'] = $secid;
            }
        }

        //level
        $item['level'] = $item['sublevel'] + 1;

        return $item;
    }

    private function com_modules_modules_menu($item) {
        $this->tableLog->load(array("tables_id" => $this->task->id, "source_id" => $item['id']));
        // Modules_Menu
        //First delete
        $this->destination_db->setQuery(
                "DELETE FROM #__modules_menu
                    WHERE moduleid = " . $this->destination_db->quote($item['id'])
        );
        $this->factory->execute($this->destination_db);
        //Then insert
        $query = 'SELECT *'
                . ' FROM #__modules_menu '
                . ' WHERE moduleid = ' . (int) $this->tableLog->source_id
        ;
        $this->source_db->setQuery($query);
        $this->factory->execute($this->source_db);
        $modules_menus = $this->source_db->loadAssocList();

        foreach ($modules_menus as $k => $modules_menu) {
            if ($modules_menu['menuid'] != 0) {
                $this->tableLog->load(array("tables_id" => 16, "source_id" => $modules_menu['menuid']));
                $modules_menu['menuid'] = $this->tableLog->destination_id;
            }
            $query = "REPLACE INTO #__modules_menu" .
                    " (moduleid,menuid)" .
                    " VALUES (" . $this->destination_db->quote($item['id']) . ',' . $this->destination_db->quote($modules_menu['menuid']) . ")";
            $this->destination_db->setQuery($query);
            if (!$this->factory->execute($this->destination_db)) {
                $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CREATE', $item['id'], $this->destination_db->getErrorMsg()) . '</p>';
                //$this->factory->writeLog($message);
                $this->tableLog->load(array("tables_id" => $this->task->id, "source_id" => $item['id']));
                $this->tableLog->note = $message;
                $this->tableLog->store();
                continue;
            }
        }
        $this->tableLog->load(array("tables_id" => $this->task->id, "source_id" => $item['id']));
    }

    private function com_banners_banners_item($item) {

        if ($item['imageurl'] != '')
            $item['params']['imageurl'] = 'images/banners/' . $item['imageurl'];

        $item['metakey'] = $item['tags'];
        return $item;
    }

    private function com_banners_banners_tracks($item) {
        $item['track_date'] = JDate::getInstance($item['track_date'])->format('Y-m-d H');

        //insert
        $query = 'SELECT *'
                . ' FROM #__bannertrack '
                . ' WHERE banner_id = ' . (int) $this->tableLog->source_id
        ;
        $this->source_db->setQuery($query);
        $this->factory->execute($this->source_db);
        $banner_tracks = $this->source_db->loadAssocList();
        foreach ($banner_tracks as $k => $banner_track) {
            $banner_track['banner_id'] = $this->tableLog->destination_id;
            $query = "REPLACE INTO #__banner_tracks" .
                    " (track_date,track_type,banner_id)" .
                    " VALUES (" . $this->destination_db->quote($item['track_date']) . ',' . $this->destination_db->quote($banner_track['track_type']) . ',' . $this->destination_db->quote($banner_track['banner_id']) . ")";
            $this->destination_db->setQuery($query);
            if (!$this->factory->execute($this->destination_db)) {
                $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CREATE', $item['id'], $this->destination_db->getErrorMsg()) . '</p>';
                //$this->factory->writeLog($message);
                $this->tableLog->note = $message;
                $this->tableLog->store();
                continue;
            }
        }
    }

    public function getResult() {
        
        $result = Array();
        $result['status'] = $this->status;
        $result['message'] = $this->task->extension_name . ' - ' . $this->task->name;
        
        return $result;
    }

}
