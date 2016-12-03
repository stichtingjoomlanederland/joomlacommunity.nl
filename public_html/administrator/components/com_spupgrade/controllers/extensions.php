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

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * SPUpgrades Controller
 */
class SPUpgradeControllerExtensions extends JControllerAdmin {

    function transfer() {
        // Check for request forgeries
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $factory = new CYENDFactory();
        $source = new CYENDSource();

        //Get task ids
        $statuses = JRequest::getVar('status', array(), '', 'array');
        $ids = JRequest::getVar('cid', array(), '', 'array');
        $task_ids = JRequest::getVar('task_ids', array(), '', 'array');
        //Validate Input IDs        
        $input_ids = JRequest::getVar('input_ids', array(), '', 'array');
        $input_ids = $this->validateInputIDs(JRequest::getVar('input_ids', array(), '', 'array'), $task_ids);
        if (!$input_ids) {
            jexit(JText::_('COM_SPUPGRADE_MSG_ERROR_INVALID_IDS_BATCH'));
        }

        //Initial tasks
        //Disable warnings
        error_reporting(E_ERROR | E_PARSE);
        @set_time_limit(0);

        // Connect to source db
        if (!$source->testConnection()) {
            jexit(JText::_("COM_SPUPGRADE_MSG_ERROR_CONNECTION"));
        }

        // Get the model.
        $model = $factory->getModel('Extensions', 'SPUpgradeModel');

        //Main Loop
        //Loop on ids
        $id = $ids[0];
        if (!($item = $model->getItem($id))) {
            jexit($model->getError());
        }

        $status = $this->getStatus($statuses);
        $modelContent = parent::getModel($item->extension_name, 'SPUpgradeModel', array('status' => $status));
        $tables = $modelContent->tables($item->extension_name);
        if (!$tables) {
            jexit(JText::_($modelContent->getError()));
        }
        
        foreach ($tables as $table) {
            $modelContent->init($item->extension_name, $table);   
            if ($table != 'media') {
                $modelContent->setTable($table);                
                $modelContent->transfer($input_ids[$id], $table);
            } else {
                $modelContent->media();
            }
            if ($modelContent->batch < 0) {
                $result = $modelContent->getResult();
                jexit(json_encode($result));
            }
        }
        
        $modelContent->status = 'completed';
            
        //end loop on ids
        // Finish
        //enable warnings
        error_reporting(E_ALL);
        @set_time_limit(30);

        $result = $modelContent->getResult();
        jexit(json_encode($result));
    }

    function validateInputIDs($input_ids, $task_ids) {
        $return = Array();
        foreach ($input_ids as $i => $ids) {
            if ($ids != "") {
                $task_id = $task_ids[$i];
                $ranges = explode(",", $ids);
                foreach ($ranges as $range) {
                    if (preg_match("/^[0-9]*$/", $range)) {
                        $return[$task_id][] = $range;
                    } else {
                        if (preg_match("/^[0-9]*-[0-9]*$/", $range)) {
                            $nums = explode("-", $range);
                            if ($nums[0] >= $nums[1])
                                return false;
                            for ($k = $nums[0]; $k <= $nums[1]; $k++) {
                                $return[$task_id][] = $k;
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
