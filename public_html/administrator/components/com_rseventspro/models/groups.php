<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproModelGroups extends JModelList
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
				'id', 'name'
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
		
		$limitstart = $jinput->getInt('lstart');
		
		// List state information.
		parent::populateState('name', 'asc');
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
		$query->select($db->qn('id').', '.$db->qn('name'));
		
		// Select from table
		$query->from($db->qn('#__rseventspro_groups'));
		
		// Filter by search in name or description
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->q('%'.$db->escape($search, true).'%');
			$query->where($db->qn('name').' LIKE '.$search.' ');
		}
		
		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'name');
		$listDirn = $db->escape($this->getState('list.direction', 'asc'));
		$query->order($db->qn($listOrdering).' '.$listDirn);
		
		return $query;
	}
	
	/**
	 * Method to set the side bar.
	 */
	public function getSidebar() {
		if (rseventsproHelper::isJ3()) {
			return JHtmlSidebar::render();
		}
		
		return;
	}
	
	/**
	 * Method to set the filter bar.
	 */
	public function getFilterBar() {
		$options = array();
		$options['search'] = array(
			'label' => JText::_('JSEARCH_FILTER'),
			'value' => $this->getState('filter.search')
		);
		$options['listDirn']  = $this->getState('list.direction', 'asc');
		$options['listOrder'] = $this->getState('list.ordering', 'name');
		$options['sortFields'] = array(
			JHtml::_('select.option', 'id', JText::_('COM_RSEVENTSPRO_GLOBAL_SORT_ID')),
			JHtml::_('select.option', 'name', JText::_('COM_RSEVENTSPRO_GROUPS_SORT_NAME'))
		);
		
		$bar = new RSFilterBar($options);
		return $bar;
	}
}