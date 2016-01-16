<?php

/**
 * @version		$Id: tracks.php 22338 2011-11-04 17:24:53Z github_bot $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Tracks list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class SpUpgradeControllerLog extends JControllerLegacy {

    /**
     * @var		string	The context for persistent state.
     * @since	1.6
     */
    protected $context = 'com_spupgrade.log';

    public function delete() {
        $factory = new CYENDFactory();

        // Check for request forgeries.
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get the model.
        $model = $factory->getModel('Log', 'SPUpgradeModel');

        // Load the filter state.
        $app = JFactory::getApplication();

        $tablesId = $app->getUserState($this->context . '.filter.tables_id');
        $model->setState('filter.tables_id', $tablesId);

        $state = $app->getUserState($this->context . '.filter.state');
        $model->setState('filter.state', $state);

        $begin = $app->getUserState($this->context . '.filter.begin');
        $model->setState('filter.begin', $begin);

        $end = $app->getUserState($this->context . '.filter.end');
        $model->setState('filter.end', $end);

        $model->setState('list.limit', 0);
        $model->setState('list.start', 0);

        $count = $model->getTotal();
        // Remove the items.
        if (!$model->delete()) {
            JError::raiseWarning(500, $model->getError());
        } else {
            $this->setMessage(JText::plural('COM_SPUPGRADE_TRACKS_N_ITEMS_DELETED', $count));
        }

        $this->setRedirect('index.php?option=com_spupgrade&view=log');
    }

    public function delete_ind() {
        $factory = new CYENDFactory();

        // Check for request forgeries.
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $ids = JRequest::getVar('cid', array(), '', 'array');
        $model = $factory->getModel('Log', 'SPUpgradeModel');
        if (!$model->delete_ind($ids)) {
            JError::raiseWarning(500, $model->getError());
        } else {
            $this->setMessage(JText::plural('COM_SPUPGRADE_TRACKS_N_ITEMS_DELETED', count($ids)));
        }

        $this->setRedirect('index.php?option=com_spupgrade&view=log');
    }
    
    public function get_last_id() {       
        
        $model = CYENDFactory::getModel('Log', 'SPUpgradeModel');
        $result = $model->get_last_id();
        jexit($result);
        
    }

}
