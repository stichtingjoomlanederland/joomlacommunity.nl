<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproModelSubscriptions extends JModelList
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
				'u.name', 'e.name', 'u.id',
				'u.gateway', 'u.state', 'u.confirmed'
			);
		}
		
		parent::__construct($config);
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null) {
		$jinput = JFactory::getApplication()->input;
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search'));
		$this->setState('filter.state', $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state'));
		$this->setState('filter.event', $this->getUserStateFromRequest($this->context.'.filter.event', 'filter_event'));
		$this->setState('filter.ticket', $this->getUserStateFromRequest($this->context.'.filter.ticket', 'filter_ticket'));
		
		$limitstart = $jinput->getInt('lstart');
		
		// List state information.
		parent::populateState('u.date', 'desc');
		$this->setState('list.start', $limitstart);
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
		
		return $query;
	}
	
	/**
	 *	Method to set the side bar
	 */
	public function getSidebar() {
		if (rseventsproHelper::isJ3()) {
			$layout = JFactory::getApplication()->input->get('layout','');
			
			if ($layout != 'scan') {
				JHtmlSidebar::addFilter(
					JText::_('COM_RSEVENTSPRO_SELECT_STATE'),
					'filter_state',
					JHtml::_('select.options', rseventsproHelper::getStatuses(), 'value', 'text', $this->getState('filter.state'), false)
				);
				JHtmlSidebar::addFilter(
					JText::_('COM_RSEVENTSPRO_SELECT_EVENT'),
					'filter_event',
					JHtml::_('select.options', rseventsproHelper::getFilterEvents(true,true), 'value', 'text', $this->getState('filter.event'), false)
				);
				
				if ($this->getState('filter.event')) {
					JHtmlSidebar::addFilter(
						JText::_('COM_RSEVENTSPRO_SELECT_TICKET'),
						'filter_ticket',
						JHtml::_('select.options', $this->getFilterTickets(), 'value', 'text', $this->getState('filter.ticket'), false)
					);
				}
			}
			
			return JHtmlSidebar::render();
		}
		
		return;
	}
	
	/**
	 *	Method to set the filter bar
	 */
	public function getFilterBar() {
		$options = array();
		$options['search'] = array(
			'label' => JText::_('JSEARCH_FILTER'),
			'value' => $this->getState('filter.search')
		);
		$options['listDirn']  = $this->getState('list.direction', 'desc');
		$options['listOrder'] = $this->getState('list.ordering', 'u.date');
		$options['sortFields'] = array(
			JHtml::_('select.option', 'u.id', JText::_('COM_RSEVENTSPRO_GLOBAL_SORT_ID')),
			JHtml::_('select.option', 'u.date', JText::_('COM_RSEVENTSPRO_SUBSCRIPTIONS_SORT_DATE')),
			JHtml::_('select.option', 'u.name', JText::_('COM_RSEVENTSPRO_SUBSCRIPTIONS_SORT_NAME')),
			JHtml::_('select.option', 'e.name', JText::_('COM_RSEVENTSPRO_SUBSCRIPTIONS_SORT_EVENT_NAME')),
			JHtml::_('select.option', 'u.gateway', JText::_('COM_RSEVENTSPRO_SUBSCRIPTIONS_SORT_GATEWAY')),
			JHtml::_('select.option', 'u.confirmed', JText::_('COM_RSEVENTSPRO_SUBSCRIBERS_HEAD_CONFIRMED')),
			JHtml::_('select.option', 'u.state', JText::_('JSTATUS'))
		);
		$options['rightItems'] = array(
			array(
				'input' => '<select id="filter_state" name="filter_state" class="inputbox" onchange="this.form.submit()">'."\n"
						   .'<option value="">'.JText::_('COM_RSEVENTSPRO_SELECT_STATE').'</option>'."\n"
						   .JHtml::_('select.options', rseventsproHelper::getStatuses(), 'value', 'text', $this->getState('filter.state'))."\n"
						   .'</select>'
				),
			
			array(
				'input' => '<select id="filter_event" name="filter_event" class="inputbox" onchange="this.form.submit()">'."\n"
						   .'<option value="">'.JText::_('COM_RSEVENTSPRO_SELECT_EVENT').'</option>'."\n"
						   .JHtml::_('select.options', rseventsproHelper::getFilterEvents(true,true,'DESC'), 'value', 'text', $this->getState('filter.event'))."\n"
						   .'</select>'
				)
		);
		
		if ($this->getState('filter.event')) {
			$options['rightItems'][] = array(
				'input' => '<select id="filter_ticket" name="filter_ticket" class="inputbox" onchange="this.form.submit()">'."\n"
						   .'<option value="">'.JText::_('COM_RSEVENTSPRO_SELECT_TICKET').'</option>'."\n"
						   .JHtml::_('select.options', $this->getFilterTickets(), 'value', 'text', $this->getState('filter.ticket'))."\n"
						   .'</select>'
				);
		}
		
		$bar = new RSFilterBar($options);
		return $bar;
	}
	
	/**
	 *	Method to get tickets
	 */
	protected function getFilterTickets() {
		$db = JFactory::getDbo();
		$id = $this->getState('filter.event');
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('id','value'))->select($db->qn('name','text'))
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('ide').' = '.(int) $id)
			->order($db->qn('order').' ASC');
			
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 *	Method to export subscribers
	 */
	public function export() {
		$query = $this->getListQuery();
		rseventsproHelper::exportSubscribersCSV($query);
	}
	
	/**
	 *	Method to get event details
	 */
	public function getEvent() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$input	= JFactory::getApplication()->input;
		$ticket	= $input->getString('ticket','');
		$ids	= str_replace(rseventsproHelper::getConfig('barcode_prefix', 'string', 'RST-'),'',$ticket);
		
		$query->select($db->qn('e.name'))
			->select($db->qn('e.start'))->select($db->qn('e.end'))
			->from($db->qn('#__rseventspro_events','e'))
			->join('LEFT',$db->qn('#__rseventspro_users','u').' ON '.$db->qn('e.id').' = '.$db->qn('u.ide'))
			->where($db->qn('u.id').' = '.(int) $ids);
		$db->setQuery($query);
		return $db->loadObject();
	}
}