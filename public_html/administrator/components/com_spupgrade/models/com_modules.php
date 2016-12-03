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

class SPUpgradeModelCom_Modules extends SPUpgradeModelCom {

    public function __construct($config = array()) {
        
        parent::__construct($config);
        
        switch ($this->task->name) {

            case 'modules':
                $this->id = 'id';
                $this->table_name = 'modules';

                break;

            default:
                break;
        }
        
        $jinput = JFactory::$application->input;
        if ($jinput->get('task') == 'transfer_all') {
            $this->params->set('duplicate_alias', 1);
            $this->params->set('new_ids', 1);
        }
    }

    public function modules($ids = null) {
        //initialize
        $this->destination_table = $this->factory->getTable('Module', 'JTable');
        $this->table_name = 'modules';
        $this->task->category = null;
        $this->id = 'id';
        $this->task->query = 'SELECT ' . $this->id . ' 
            FROM #__' . $this->table_name . '
            WHERE client_id = 0
            ';

        $this->items($ids);
    }

}
