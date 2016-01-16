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
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
include_once 'ftp.php';

class SPUpgradeModelExtension extends JModelLegacy {

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
    protected $task;
    protected $factory;
    protected $source;
    protected $id;
    public $batch;
    public $status;

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
        $this->status = $config['status'];
    }

    public function init($extension_name, $name) {
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $jAp = $this->jAp;
        $factory = $this->factory;
        
        // Load items
        $destination_query->clear();
        $destination_query->select('*');
        $destination_query->from('#__spupgrade_tables');
        $destination_query->where('extension_name LIKE ' .$destination_db->quote($extension_name));
        $destination_query->where('name LIKE ' .$destination_db->quote($name));
        $destination_db->setQuery($destination_query);
        $result = CYENDFactory::execute($destination_db);
        
        if (!$result) {
            jexit($destination_db->getErrorMsg());
        }
        
        $this->task = $destination_db->loadObject();             

    }

    public function setTable($name) {

        //Exit if empty table
        $source_table_name = '#__' . $name;

        // Init
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;

        //Define destination table name
        $destination_table_name = '#__' . $name;

        // Get tables descriptions
        $query = 'SHOW CREATE TABLE ' . $source_table_name;

        $source_db->setQuery($query);
        if (!CYENDFactory::execute($source_db))
            jexit ($source_db->getErrorMsg());
        $source_table_desc = $source_db->loadObject();

        $query = 'describe ' . $destination_table_name;
        $destination_db->setQuery($query);
        if (!CYENDFactory::execute($destination_db)) {
            //Create table
            $this->setError(JText::plural('COM_SPUPGRADE_MSG_DESTINATION_TABLE_MISSING', $name));
            jexit('<b><font color="red">' . JText::sprintf('COM_SPUPGRADE_MSG_DESTINATION_TABLE_MISSING', $name) . '</font></b>');
        }                
    }
    
    public function transfer($pks = null, $name) {
        
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
            if ($exclude !== false) {
                unset($excludes[$exclude]);
                continue;
            }

            $query = 'SELECT * FROM #__' . $name .
                    ' LIMIT ' . $pk . ', 1';
            $source_db->setQuery($query);
            if (!$source_db->query()) {
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

            //Build query
            //$query = "INSERT INTO #__" . $name . " (";
            //if ($params->get("new_ids", 0) == 2)
            $query = "REPLACE INTO #__" . $name . " (";
            $columnNames = Array();
            $values = Array();
            foreach ($item as $column => $value) {
                if ( ($column != 'sp_id') && (!is_null($value)) ) {
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

    public function tables($extension) {        
        // Create a new query object.
        $db = $this->destination_db;
        $query = $this->destination_query;
        $query->clear();

        // Select the required fields from the table.
        $query->select('a.name');
        $query->from('#__spupgrade_tables AS a');
        $query->where('`extension_name` LIKE '.$db->quote($extension));        
        $query->order('a.id ASC');
        $db->setQuery($query);
        if(!CYENDFactory::execute($db)) {
            jexit($db->getErrorMsg());
        }      
        $tables = $db->loadColumn();

        return $tables;
    }
    
    public function media($folders = null) {
        
        $factory = $this->factory;
        $ftp = new SPUpgradeModelFTP();
        
        foreach ($folders as $folder) {
            JFolder::move($folder, $folder.'_bkp' . JFactory::getDate()->format('_Y_m_d_G_i_s'), JPATH_SITE);
            
            //JFolder::copy($source->source_path . $folder, JPATH_SITE . '/' . $folder);
            $ftp->transfer($folder, $folder);
        }                        
    }
    
    public function getResult() {
        
        $result = Array();
        $result['status'] = $this->status;
        $result['message'] = $this->task->extension_name . ' - ' . $this->task->name;
        
        return $result;
    }

}
