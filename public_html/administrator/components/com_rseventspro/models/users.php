<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproModelUsers extends JModelList
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
				'u.id', 'u.name'
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
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		
		// Select fields
		$query->select($db->qn('u.id'))->select($db->qn('u.name'))->select($db->qn('u.username'));
		$query->select($db->qn('u.email'))->select($db->qn('a.name','author'));
		
		// Select from table
		$query->from($db->qn('#__users','u'));
		
		// Join over the rsblog users table
		$query->join('LEFT', $db->qn('#__rseventspro_user_info','a').' ON '.$db->qn('u.id').' = '.$db->qn('a.id'));
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where($db->qn('u.name').' LIKE '.$search.' OR '.$db->qn('u.email').' LIKE '.$search.' OR '.$db->qn('a.name').' LIKE '.$search);
		}
		
		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'u.name');
		$listDirn = $db->escape($this->getState('list.direction', 'asc'));
		$query->order($db->escape($listOrdering).' '.$listDirn);
		
		return $query;
	}
}