<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsModelReports extends JModelList
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
				'r.date', 'r.ip', 'u.name'
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
		$app = JFactory::getApplication();

		$this->setState($this->context.'.filter.id',		$app->getUserStateFromRequest($this->context.'.filter.id', 'id'));
		$this->setState($this->context.'.filter.search',	$app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search'));

		// List state information.
		parent::populateState('r.date', 'desc');
	}
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery() {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);

		// Select fields
		$query->select($db->qn('r').'.*');
		$query->select($db->qn('u.name'));
		
		// Select from table
		$query->from($db->qn('#__rscomments_reports','r'));
		
		// Join on users table
		$query->join('LEFT', $db->qn('#__users','u').' ON '.$db->qn('u.id').' = '.$db->qn('r.uid'));
		
		// Filter by comment id
		if ($id = $this->getState($this->context.'.filter.id')) {
			$query->where($db->qn('r.IdComment').' = '.(int) $id);
		}
		
		// Filter by search in title
		$search = $this->getState($this->context.'.filter.search');
		if (!empty($search)) {
			$search = $db->q('%'.$search.'%');
			$query->where('('.$db->qn('r.report').' LIKE '.$search.' OR '.$db->qn('r.ip').' LIKE '.$search.' OR '.$db->qn('u.name').' LIKE '.$search.' )');
		}
		
		// Add the list ordering clause
		$listOrdering  = $this->getState('list.ordering', 'r.date');
		$listDirection = $db->escape($this->getState('list.direction', 'desc'));
		$query->order($db->qn($listOrdering).' '.$listDirection);
		
		return $query;
	}
	
	// get filters
	public function getFilterBar() {
		$options = array();

		// search filter
		$options['search'] = array(
			'label' => JText::_('JSEARCH_FILTER'),
			'value' => $this->getState($this->context.'.filter.search')
		);
		// number of items per page
		$options['limitBox']   = $this->getPagination()->getLimitBox();
		// order by filter
		$options['listDirn']   = $this->getState('list.direction', 'desc');
		$options['listOrder']  = $this->getState('list.ordering', 'r.date');
		
		$options['sortFields'] = array(
			JHtml::_('select.option', 'r.date', JText::_('COM_RSCOMMENTS_REPORTS_DATE')),
			JHtml::_('select.option', 'r.ip', JText::_('COM_RSCOMMENTS_REPORTS_IP')),
			JHtml::_('select.option', 'u.name', JText::_('COM_RSCOMMENTS_REPORTS_NAME'))
		);
		
		$bar = new RSFilterBar($options);
		return $bar;
	}
	
	public function getSideBar() {
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		return RSCommentsToolbarHelper::render();
	}
}