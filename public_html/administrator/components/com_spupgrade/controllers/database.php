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

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * SPUpgrades Controller
 */
class SPUpgradeControllerDatabase extends JControllerAdmin {

    function transfer() {

        // Check for request forgeries
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $factory = new CYENDFactory();
        $source = new CYENDSource();

        //Validate Input IDs        
        $statuses = JRequest::getVar('status', array(), '', 'array');
        $input_ids = JRequest::getVar('input_ids', array(), '', 'array');
        $input_ids = $this->validateInputIDs($input_ids);
        if (!$input_ids) {
            jexit(JText::_('COM_SPUPGRADE_MSG_ERROR_INVALID_IDS'));
        }

        //Disable warnings
        error_reporting(E_ERROR | E_PARSE);
        @set_time_limit(0);

        // Connect to source db
        if (!$source->testConnection()) {
            jexit(JText::_("COM_SPUPGRADE_MSG_ERROR_CONNECTION"));
        }

        // Main Loop within extensions
        //Get ids
        $ids = JRequest::getVar('cid', array(), '', 'array');
        $input_prefixes = JRequest::getVar('input_prefixes', array(), '', 'array');
        $input_names = JRequest::getVar('input_names', array(), '', 'array');

        // Get the model.
        $model = parent::getModel('Database', 'SPUpgradeModel');

        //Main Loop
        //Loop on ids
        $id = $ids[0];
       
        $table_name = $input_prefixes[$id - 1] . '_' . $input_names[$id - 1];
        $item = $model->getItem($table_name);
        if (is_null($item)) {
            //Insert new item in tables
            $item = $model->newItem($table_name);
        }
        if (is_null($item)) {
            jexit(JText::plural('COM_SPUPGRADE_DATABASE_FAILED', $table_name));
        }

        $status = $this->getStatus($statuses);
        $modelContent = parent::getModel('com_database', 'SPUpgradeModel', array('task' => $item, 'status' => $status));
        $modelContent->setTable($input_prefixes[$id - 1], $input_names[$id - 1]);
        
        $modelContent->content($input_ids[$id - 1], $input_prefixes[$id - 1], $input_names[$id - 1]);
        
        //end loop on ids
        
        // Finish
        error_reporting(E_ALL);
        @set_time_limit(30);

        $result = $modelContent->getResult();      
        jexit(json_encode($result));
    }

    function validateInputIDs($input_ids) {
        $return = Array();
        foreach ($input_ids as $i => $ids) {
            if ($ids != "") {
                $ranges = explode(",", $ids);
                foreach ($ranges as $j => $range) {
                    if (preg_match("/^[0-9]*$/", $range)) {
                        $return[$i][] = $range;
                    } else {
                        if (preg_match("/^[0-9]*-[0-9]*$/", $range)) {
                            $nums = explode("-", $range);
                            if ($nums[0] >= $nums[1])
                                return false;
                            for ($k = $nums[0]; $k <= $nums[1]; $k++) {
                                $return[$i][] = $k;
                            }
                        } else {
                            return false;
                        }
                    }
                }
            }
        }
        if (count($return) == 0) {
            return true;
        } else {
            return $return;
        }
    }
    
    function getStatus($statuses) {

        foreach ($statuses as $value) {
            if ($value != 'completed') {
                return $value;
            }
        }

        return 'completed';
    }

}
