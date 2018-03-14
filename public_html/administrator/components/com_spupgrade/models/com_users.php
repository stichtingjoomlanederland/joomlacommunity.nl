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

class SPUpgradeModelCom_Users extends JModelLegacy {

    protected $jAp;
    protected $tableLog;
    protected $destination_db;
    protected $destination_query;
    protected $source_db;
    protected $source_query;
    protected $user;
    protected $params;
    public $task;
    protected $factory;
    protected $source;
    protected $batch;
    protected $status;

    function __construct($config = array()) {
        
        parent::__construct($config);
        
        $this->factory = new CYENDFactory();
        $this->source = new CYENDSource();
        $this->jAp = & JFactory::getApplication();
        $this->tableLog = $this->factory->getTable('Log', 'SPUpgradeTable');
        $this->destination_db = $this->getDbo();
        $this->destination_query = $this->destination_db->getQuery(true);
        $this->source_db = $this->source->source_db;
        $this->source_query = $this->source_db->getQuery(true);
        $this->user = JFactory::getUser(0);
        $this->params = JComponentHelper::getParams('com_spupgrade');
        $this->batch = $this->params->get('batch', 100);
        $this->task = $config['task'];
        $this->status = $config['status'];
                
        $jinput = JFactory::$application->input;
        if ($jinput->get('task') == 'transfer_all') {

            switch ($this->status) {
                case 'pending':
                    $this->params->set('duplicate_alias', 1);
                    $this->params->set('new_ids', 0);

                    break;

                case 'run_2':
                    $this->params->set('duplicate_alias', 1);
                    $this->params->set('new_ids', 1);

                    break;

                default:
                    break;
            }
        }
        
    }


    public function users($pks = null) {
        // Initialize
        $jAp = $this->jAp;
        $factory = $this->factory;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        $params = $this->params;
        $task = $this->task;
        $destination_table = $factory->getTable('User', 'JTable');
        $user = $this->user;

        $message = ('<h2>' . JText::_(COM_USERS) . ' - ' . JText::_(COM_USERS_USERS) . '</h2>');
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
            FROM #__users
            WHERE id > 0';
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
            
            $query = 'SELECT * FROM #__users' .
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

            //Remove unesseary fields
            unset($item['gid']);

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

            // Special treatment for admin
            if ($item['id'] == 62) {
                $item['username'] = $item['username'] . 'v15';
                $item['email'] = $item['email'] . 'v15';
                $message = '<p>' . JText::_('COM_SPUPGRADE_MSG_OLD_ADMIN') . '<br/>';
                //$factory->writeLog($message);
                $message = 'username: ' . $item['username'] . '<br/>';
                //$factory->writeLog($message);
                $message = 'email: ' . $item['email'] . '</p>';
                //$factory->writeLog($message);
            }

            //handle params
            $item_params = explode("\n", $item['params']);
            foreach ($item_params as $key => $param) {
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

            //Handle user group
            if ($item['usertype'] == "Super Administrator")
                $item['usertype'] = "Super Users";
            $destination_db->setQuery(
                    'SELECT id' .
                    ' FROM #__usergroups' .
                    ' WHERE title LIKE ' . $destination_db->quote($item['usertype'])
            );
            CYENDFactory::execute($destination_db);
            $group_id = $destination_db->loadResult();
            unset($item['usertype']); //Valid from Joomla 3.0.2
            // Create record
            if ($params->get("new_ids", 0) == 2) {
                $query = "REPLACE INTO #__users";
            } else {
                $query = "INSERT INTO #__users";
            }
            $query .= " (";
            foreach ($item as $key => $value) {
                $query .= $destination_db->quoteName($key) . ",";
            }
            $query = chop($query, ",");
            $query .=")";
            $query .= " VALUES (";
            foreach ($item as $key => $value) {
                $query .= $destination_db->quote($value) . ",";
            }
            $query = chop($query, ",");
            $query .=")";

            $destination_db->setQuery($query);
            if (!CYENDFactory::execute($destination_db)) {
                if ($params->get("new_ids", 0) == 1) {
                    $destination_db->setQuery(
                            "INSERT INTO #__users" .
                            " (email)" .
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
                            "SELECT id FROM #__users" .
                            " WHERE email LIKE " . $destination_db->quote('sp_transfer')
                    );
                    CYENDFactory::execute($destination_db);
                    $tableLog->destination_id = $destination_db->loadResult();
                    $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_NEW_IDS', $item['id'], $tableLog->destination_id) . '</p>';
                    $item['id'] = $tableLog->destination_id;
                    //$factory->writeLog($message);
                    $tableLog->note = $message;
                    $query = "UPDATE #__users";
                    $query .= " SET ";
                    foreach ($item as $key => $value) {
                        $query .= $destination_db->quoteName($key) . "=" . $destination_db->quote($value) . ",";
                    }
                    $query = chop($query, ",");
                    $query .= " WHERE `id` =" . (int) $item['id'];
                    $destination_db->setQuery($query);
                    if (!CYENDFactory::execute($destination_db)) {
                        $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                        //$factory->writeLog($message);
                        $tableLog->note = $message;
                        $tableLog->store();
                        continue;
                    }
                } elseif ($params->get("new_ids", 0) == 0) {
                    $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                    //$factory->writeLog($message);
                    $tableLog->note = $message;
                    $tableLog->store();
                    continue;
                }
            }

            // check for existing username
            $query = 'SELECT id'
                    . ' FROM #__users '
                    . ' WHERE username = ' . $destination_db->Quote($item['username'])
                    . ' AND id != ' . (int) $item['id'];
            $destination_db->setQuery($query);
            $xid = intval($destination_db->loadResult());
            if ($xid && $xid != intval($item['id'])) {
                $item['username'] .= '-sp-' . rand();
                $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_DUPLICATE_USERNAME', $item['id'], $item['username']) . '</p>';
                //$factory->writeLog($message);
                $tableLog->note = $message;
                $query = "UPDATE #__users";
                $query .= " SET ";
                foreach ($item as $key => $value) {
                    $query .= $destination_db->quoteName($key) . "=" . $destination_db->quote($value) . ",";
                }
                $query = chop($query, ",");
                $query .= " WHERE `id` =" . (int) $item['id'];
                $destination_db->setQuery($query);
                if (!CYENDFactory::execute($destination_db)) {
                    $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                    //$factory->writeLog($message);
                    $tableLog->note = $message;
                    // delete record
                    $destination_db->setQuery(
                            "DELETE FROM #__users" .
                            " WHERE id = " . $destination_db->quote($item['id'])
                    );
                    if (!CYENDFactory::execute($destination_db)) {
                        $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                        //$factory->writeLog($message);
                        continue;
                    }
                }
            }

            // check for existing email
            $query = 'SELECT id'
                    . ' FROM #__users '
                    . ' WHERE email = ' . $destination_db->Quote($item['email'])
                    . ' AND id != ' . (int) $item['id']
            ;
            $destination_db->setQuery($query);
            $xid = intval($destination_db->loadResult());
            if ($xid && $xid != intval($this->id)) {
                $item['email'] .= '-sp-' . rand();
                $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_DUPLICATE_EMAIL', $item['id'], $item['email']) . '</p>';
                //$factory->writeLog($message);
                $tableLog->note = $message;
                $query = "UPDATE #__users";
                $query .= " SET ";
                foreach ($item as $key => $value) {
                    $query .= $destination_db->quoteName($key) . "=" . $destination_db->quote($value) . ",";
                }
                $query = chop($query, ",");
                $query .= " WHERE `id` =" . (int) $item['id'];
                $destination_db->setQuery($query);
                if (!CYENDFactory::execute($destination_db)) {
                    $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                    //$factory->writeLog($message);
                    $tableLog->note = $message;
                    // delete record
                    $destination_db->setQuery(
                            "DELETE FROM #__users" .
                            " WHERE id = " . $destination_db->quote($item['id'])
                    );
                    if (!CYENDFactory::execute($destination_db)) {
                        $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                        //$factory->writeLog($message);
                        continue;
                    }
                }
            }

            // User Usergroup Map
            if ($params->get("new_ids", 0) == 2) {
                $query = "DELETE FROM #__user_usergroup_map WHERE user_id = " . $destination_db->quote($item['id']);
                $destination_db->setQuery($query);
                if (!CYENDFactory::execute($destination_db)) {
                    $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                    //$factory->writeLog($message);
                    $tableLog->note = $message;
                    $tableLog->store();
                    continue;
                }
            }
            $query = "INSERT INTO #__user_usergroup_map";
            $query .= " (user_id,group_id)" .
                    " VALUES (" . $destination_db->quote($item['id']) . ',' . $destination_db->quote($group_id) . ")";
            $destination_db->setQuery($query);
            if (!CYENDFactory::execute($destination_db)) {
                $message = '<p>' . JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg()) . '</p>';
                //$factory->writeLog($message);
                $tableLog->note = $message;
                $tableLog->store();
                continue;
            }

            //Log
            $tableLog->state = 4;
            $tableLog->store();
        } //Main loop end
        
        //status completed
        if ($this->status == 'run_2')
            $this->status = 'completed';
        if ($this->status == 'pending')
            $this->status = 'run_2';
                
        return;
    }

    public function getResult() {
        
        $result = Array();
        $result['status'] = $this->status;
        $result['message'] = $this->task->extension_name . ' - ' . $this->task->name;
        
        return $result;
    }
    
}
