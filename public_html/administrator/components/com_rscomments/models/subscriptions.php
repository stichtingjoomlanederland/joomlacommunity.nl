<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsModelSubscriptions extends JModelList
{
	protected $context = 'com_rscomments.subscriptions';
	
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'name', 'option', 'email', 'component'
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
		$query->from($db->qn('#__rscomments_subscriptions'));

		// Filter by search in title
		if ($search = $this->getState('filter.search')) {
			$search = $db->q('%'.$search.'%');
			$query->where('('.$db->qn('name').' LIKE '.$search.' OR '.$db->qn('email').' LIKE '.$search.')');
		}

		// Filter by components
		if ($component = $this->getState('filter.component')) {
			$query->where($db->qn('option').' = '.$db->q($component));
		}

		// Filter by component id
		$component_id = $this->getState('filter.component_id');
		if(is_numeric($component_id)) {
			$query->where($db->qn('id').' = '.$db->q($component_id));
		}

		// Add the list ordering clause
		$listOrdering  = $this->getState('list.ordering', 'IdSubscription');
		$listDirection = $this->getState('list.direction', 'DESC');
		
		$query->order($db->qn($listOrdering).' '.$db->escape($listDirection));

		return $query;
	}
}