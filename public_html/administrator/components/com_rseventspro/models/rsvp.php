<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproModelRsvp extends JModelList
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
				'r.id', 'r.date', 'id', 'date', 'rsvp'
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
		$id		= JFactory::getApplication()->input->getInt('id',0);
		
		// Select fields
		$query->select('r.*')->select($db->qn('u.name'));
		
		// Select from table
		$query->from($db->qn('#__rseventspro_rsvp_users', 'r'));
		
		// Join over the users table
		$query->join('LEFT', $db->qn('#__users','u').' ON '.$db->qn('r.uid').' = '.$db->qn('u.id'));
		
		// Filter by search in name
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->q('%'.$db->escape($search, true).'%');
			$query->where($db->qn('u.name').' LIKE '.$search);
		}
		
		// Filter by rsvp status
		if ($status = $this->getState('filter.rsvp')) {
			$query->where($db->qn('r.rsvp').' = '.$db->q($status));
		}
		
		$query->where($db->qn('r.ide').' = '.$db->q($id));
		
		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'r.date');
		$listDirn = $db->escape($this->getState('list.direction', 'asc'));
		$query->order($db->qn($listOrdering).' '.$listDirn);

		return $query;
	}
	
	public function status($pks, $task) {
		// Sanitize the ids.
		$pks = (array) $pks;
		$pks = array_map('intval',$pks);
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->update($db->qn('#__rseventspro_rsvp_users'))
			->set($db->qn('rsvp').' = '.$db->q($task))
			->where($db->qn('id').' IN ('.implode(',',$pks).')');
		
		$db->setQuery($query);
		$db->execute();
		
		return true;
	}
	
	public function delete($pks) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$pks	= array_map('intval', $pks);
		
		$query->clear()
			->delete($db->qn('#__rseventspro_rsvp_users'))
			->where($db->qn('id').' IN ('.implode(',',$pks).')');
		$db->setQuery($query);
		$db->execute();
		
		return true;
	}
	
	public function export() {
		$query = $this->getListQuery();
		rseventsproHelper::exportRSVPCSV($query);
	}
}