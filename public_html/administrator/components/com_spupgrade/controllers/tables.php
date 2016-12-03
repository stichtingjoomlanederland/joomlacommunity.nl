<?php

/**
 * @package		SP Upgrade
 * @subpackage	Components53
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
class SPUpgradeControllerTables extends JControllerAdmin {

    public function __construct($config = array()) {
        parent::__construct($config);

        $this->registerTask('transfer_all', 'transfer');
        $this->registerTask('transfer_template', 'transfer');
        $this->registerTask('transfer_images', 'transfer');
    }

    function transfer() {
        // Check for request forgeries
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $source = new CYENDSource();

        //Get task ids
        $statuses = JRequest::getVar('status', array(), '', 'array');
        $ids = JRequest::getVar('cid', array(), '', 'array');
        $task_ids = JRequest::getVar('task_ids', array(), '', 'array');

        //Validate Input IDs        
        $input_ids = $this->validateInputIDs(JRequest::getVar('input_ids', array(), '', 'array'), $task_ids);
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

        // Get the model.
        $model = parent::getModel('Tables', 'SPUpgradeModel');

        //Loop on ids
        $id = $ids[0];

        if (!($task = $model->getItem($id))) {
            jexit($model->getError());
        }

        $status = $this->getStatus($statuses, $ids, $task_ids);
        $modelContent = parent::getModel($task->extension_name, 'SPUpgradeModel', array('task' => $task, 'status' => $status));

        echo $modelContent->{$task->name}($input_ids[$id]);

        //end loop on ids
        
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

    function getStatus($statuses, $ids, $task_ids) {

        foreach ($task_ids as $key => $value) {
            if ($value == $ids[0]) {
                return $statuses[$key];
            }
        }

        jexit('error with values.');
    }

}
