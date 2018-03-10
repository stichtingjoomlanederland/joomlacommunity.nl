<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsModelComments extends JModelList
{	
	protected $context = 'com_rscomments.comments';
	
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
				'comment', 'name', 'option', 'date', 'published', 'component'
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
		$query->select('*');

		// Select from table
		$query->from($db->qn('#__rscomments_comments'));
		
		// Filter by search
		if ($search = $this->getState('filter.search')) {
			$search = $db->q('%'.$search.'%');
			$query->where('('.$db->qn('name').' LIKE '.$search.' OR '.$db->qn('email').' LIKE '.$search.' OR '.$db->qn('comment').' LIKE '.$search.' OR '.$db->qn('subject').' LIKE '.$search.' )');
		}
		
		// Filter by components
		if ($component = $this->getState('filter.component')) {
			$query->where($db->qn('option').' = '.$db->q($component));
		}

		// Filter by component id
		$component_id = $this->getState('filter.component_id');
		if (is_numeric($component_id)) {
			$query->where($db->qn('id').' = '.$db->q($component_id));
		}

		// Add the list ordering clause
		$listOrdering  = $this->getState('list.ordering', 'date');
		$listDirection = $db->escape($this->getState('list.direction', 'ASC'));
		$query->order($db->qn($listOrdering).' '.$listDirection);
		
		return $query;
	}
	
	/**
	 * Method to get the items list.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 * @since	1.6.1
	 */
	public function getItems() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$items	= parent::getItems();
		
		foreach ($items as $i => $item) {
			$query->clear()
				->select('COUNT('.$db->qn('id').')')
				->from($db->qn('#__rscomments_reports'))
				->where($db->qn('IdComment').' = '.$db->q($item->IdComment));
			$db->setQuery($query);
			$items[$i]->reports = (int) $db->loadResult();
		}
		
		return $items;
	}
}