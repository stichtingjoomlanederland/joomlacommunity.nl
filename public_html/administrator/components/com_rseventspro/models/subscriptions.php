<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproModelSubscriptions extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'u.name', 'e.name', 'u.id', 'u.date',
				'u.gateway', 'u.state', 'u.confirmed', 
				'state', 'event', 'ticket'
			);
		}
		
		parent::__construct($config);
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = 'u.date', $direction = 'desc') {
		$event = JFactory::getApplication()->input->getInt('filter_event', 0);
		
		$this->setState('filter.event', $this->getUserStateFromRequest($this->context . '.filter.event', 'filter_event', '', 'cmd'));
		
		// List state information.
		parent::populateState($ordering, $direction);
		
		if ($event) {
			$this->setState('filter.event', $event);
		}
		
	}
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery() {
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		
		// Select fields
		$query->select($db->qn('e.name','event'))->select($db->qn('e.start'))->select($db->qn('e.end'))->select($db->qn('u.id'))->select($db->qn('u.ide'));
		$query->select($db->qn('u.idu'))->select($db->qn('u.name'))->select($db->qn('u.email'))->select($db->qn('u.date'))->select($db->qn('u.state'));
		$query->select($db->qn('u.ip'))->select($db->qn('u.gateway'))->select($db->qn('u.SubmissionId'))->select($db->qn('u.discount'))->select($db->qn('u.early_fee'));
		$query->select($db->qn('u.late_fee'))->select($db->qn('u.tax'))->select($db->qn('u.confirmed'))->select($db->qn('e.allday'));
		
		// Select from table
		$query->from($db->qn('#__rseventspro_users','u'));
		
		// Join over the users table
		$query->join('left',$db->qn('#__rseventspro_events','e').' ON '.$db->qn('e.id').' = '.$db->qn('u.ide'));
		
		// Filter by search in name or description
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->q('%'.$db->escape($search, true).'%');
			$query->where('('.$db->qn('e.name').' LIKE '.$search.' OR '.$db->qn('u.name').' LIKE '.$search.' OR '.$db->qn('u.email').' LIKE '.$search.')');
		}
		
		// Filter by state
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where($db->qn('u.state').' = '. (int) $state);
		}
		elseif ($state === '') {
			$query->where($db->qn('u.state').' IN (0,1,2)');
		}
		
		// Filter by event
		$event = $this->getState('filter.event');
		if (is_numeric($event)) {
			$query->where($db->qn('e.id').' = '. (int) $event);
		}
		
		// Filter by ticket
		$ticket = $this->getState('filter.ticket');
		if (is_numeric($ticket)) {
			$query->join('left', $db->qn('#__rseventspro_user_tickets','ut').' ON '.$db->qn('ut.ids').' = '.$db->qn('u.id'));
			$query->where($db->qn('ut.idt').' = '. (int) $ticket);
		}
		
		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'u.date');
		$listDirn = $db->escape($this->getState('list.direction', 'desc'));
		$query->order($db->qn($listOrdering).' '.$listDirn);
		
		JFactory::getApplication()->triggerEvent('rsepro_subscriptionsQuery', array(array('query' => &$query)));
		
		return $query;
	}
	
	/**
	 *	Method to export subscribers
	 */
	public function export() {
		$query = $this->getListQuery();
		rseventsproHelper::exportSubscribersCSV($query);
	}
}