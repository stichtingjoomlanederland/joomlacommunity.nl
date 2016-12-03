<?php

/**
 * @version		$Id: tracks.php 22355 2011-11-07 05:11:58Z github_bot $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of tracks.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class SPUpgradeModelLog extends JModelList {

    /**
     * Constructor.
     *
     * @param	array	An optional associative array of configuration settings.
     * @see		JControllerLegacy
     * @since	1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'extension_name', 'b.extension_name',
                'name', 'b.name',
                'state', 'a.state',
                'tables_id', 'a.tables_id',
                'created', 'a.created',
            );
        }

        parent::__construct($config);
    }

    protected $basename;

    protected function populateState($ordering = null, $direction = null) {

        // Load the filter state.
        $tablesId = $this->getUserStateFromRequest($this->context . '.filter.tables_id', 'filter_tables_id');
        $this->setState('filter.tables_id', $tablesId);

        $state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state');
        $this->setState('filter.state', $state);

        $begin = $this->getUserStateFromRequest($this->context . '.filter.begin', 'filter_begin', '', 'string');
        $this->setState('filter.begin', $begin);

        $end = $this->getUserStateFromRequest($this->context . '.filter.end', 'filter_end', '', 'string');
        $this->setState('filter.end', $end);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_spupgrade');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('extension_name', 'asc');
    }

    protected function getListQuery() {

        //require_once JPATH_COMPONENT.'/helpers/banners.php';
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                'a.id as id,' .
                'a.tables_id as tables_id,' .
                'a.source_id as source_id,' .
                'a.destination_id as destination_id,' .
                'a.state as state,' .
                'a.created as created,' .
                'a.`note` as `note`'
        );
        $query->from('`#__spupgrade_log` AS a');

        // Join with the tables
        $query->join('LEFT', '`#__spupgrade_tables` as b ON b.id=a.tables_id');
        $query->select('b.extension_name as extension_name, b.name as name');

        // Filter by tables_id
        $tablesId = $this->getState('filter.tables_id');
        if (is_numeric($tablesId)) {
            $query->where('a.tables_id = ' . (int) $tablesId);
        }

        // Filter by state
        $state = $this->getState('filter.state');
        if (is_numeric($state)) {
            $query->where('a.state = ' . (int) $state);
        }

        // Filter by begin date
        $begin = $this->getState('filter.begin');
        if (!empty($begin)) {
            $query->where('a.created >= ' . $db->Quote($begin));
        }

        // Filter by end date
        $end = $this->getState('filter.end');
        if (!empty($end)) {
            $query->where('a.created <= ' . $db->Quote($end));
        }

        // Add the list ordering clause.
        $orderCol = $this->getState('list.ordering', 'id');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

        return $query;
    }

    public function delete() {
        // Initialise variables
        $user = JFactory::getUser();

        // Access checks.
        $allow = $user->authorise('core.delete', 'com_spupgrade');

        if ($allow) {
            // Delete tracks from this banner
            $db = $this->getDbo();
            $query = $db->getQuery(true);
            $query->delete();
            $query->from('`#__spupgrade_log`');

            // Filter by tables_id
            $tablesId = $this->getState('filter.tables_id');
            if (!empty($tablesId)) {
                $query->where('tables_id = ' . (int) $tablesId);
            }

            // Filter by state
            $state = $this->getState('filter.state');
            if (!empty($state)) {
                $query->where('state = ' . (int) $state);
            }

            // Filter by begin date
            $begin = $this->getState('filter.begin');
            if (!empty($begin)) {
                $query->where('created >= ' . $db->Quote($begin));
            }

            // Filter by end date
            $end = $this->getState('filter.end');
            if (!empty($end)) {
                $query->where('created <= ' . $db->Quote($end));
            }

            $db->setQuery((string) $query);
            $this->setError((string) $query);
            CYENDFactory::execute($db);

            // Check for a database error.
            if ($db->getErrorNum()) {
                $this->setError($db->getErrorMsg());
                return false;
            }
        } else {
            JError::raiseWarning(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
        }

        return true;
    }
    
    public function delete_ind(&$pks) {
        $pks = (array) $pks;
        $table = CYENDFactory::getTable('Log', 'SPUpgradeTable');

        // Iterate the items to delete each one.
        foreach ($pks as $pk) {

            if ($table->load($pk)) {

                if (!$table->delete($pk)) {
                    $this->setError($table->getError());
                    return false;
                }
            } else {
                
                $this->setError($table->getError());
                return false;
            }
        }

        return true;
    }
    
    public function get_last_id() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
                ->from('#__spupgrade_log AS a');
        $query->select('ta.extension_name AS extension_name, ta.name AS name')
			->join('LEFT', '#__spupgrade_tables AS ta ON ta.id = a.tables_id');
        $query->order('a.id DESC');        
        $db->setQuery($query);
        $result = $db->loadAssoc();                
        
        $message = '<p><b>Last Processed Item</b><p>' .
                '<p>' .
                '<b>Type: </b>' . $result['extension_name'] . ' -> ' . $result['name'] .
                '<br/>' . 
                '<b>ID: </b>' . $result['source_id'] .
                '<br/>' . 
                '<b>Update time: </b>' . $result['created'] .
                '</p>';
        
        return $message;
    }

}
