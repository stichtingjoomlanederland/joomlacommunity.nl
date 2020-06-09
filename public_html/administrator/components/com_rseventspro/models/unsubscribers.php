<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproModelUnsubscribers extends JModelList
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
				'id', 'date'
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
		$query->select('*');
		
		// Select from table
		$query->from($db->qn('#__rseventspro_unsubscribers'));
		
		// Filter by search in name
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->q('%'.$db->escape($search, true).'%');
			$query->where($db->qn('name').' LIKE '.$search);
		}
		
		$query->where($db->qn('ide').' = '.$db->q($id));
		
		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'date');
		$listDirn = $db->escape($this->getState('list.direction', 'desc'));
		$query->order($db->qn($listOrdering).' '.$listDirn);

		return $query;
	}
	
	public function delete($pks) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$pks	= array_map('intval', $pks);
		
		$query->clear()
			->delete($db->qn('#__rseventspro_unsubscribers'))
			->where($db->qn('id').' IN ('.implode(',',$pks).')');
		$db->setQuery($query);
		$db->execute();
		
		return true;
	}
	
	public function export() {
		$query = $this->getListQuery();
		rseventsproHelper::exportUnsubscribersCSV($query);
	}
}