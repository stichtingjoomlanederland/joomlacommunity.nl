<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsModelReports extends JModelList
{
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'r.date', 'r.ip', 'u.name'
			);
		}
		parent::__construct($config);
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
		if ($id = JFactory::getApplication()->input->getInt('id',0)) {
			$query->where($db->qn('r.IdComment').' = '.(int) $id);
		}
		
		// Filter by search in title
		if ($search = $this->getState('filter.search')) {
			$search = $db->q('%'.$search.'%');
			$query->where('('.$db->qn('r.report').' LIKE '.$search.' OR '.$db->qn('r.ip').' LIKE '.$search.' OR '.$db->qn('u.name').' LIKE '.$search.' )');
		}
		
		// Add the list ordering clause
		$listOrdering  = $this->getState('list.ordering', 'r.date');
		$listDirection = $db->escape($this->getState('list.direction', 'desc'));
		$query->order($db->qn($listOrdering).' '.$listDirection);
		
		return $query;
	}
}