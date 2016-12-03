<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.folder');

class rsfilesModelUsers extends JModelList 
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'username', 'a.username',
				'email', 'a.email'
			);
		}

		parent::__construct($config);
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication();

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$groupId = $this->getUserStateFromRequest($this->context.'.filter.group', 'filter_group_id', null, 'int');
		$this->setState('filter.group_id', $groupId);

		// List state information.
		parent::populateState('a.name', 'asc');
	}
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);

		$query->from($db->quoteName('#__users').' AS a');

		// Filter the items over the group id if set.
		$groupId = $this->getState('filter.group_id');

		if ($groupId) {
			$query->join('LEFT', '#__user_usergroup_map AS map2 ON map2.user_id = a.id');
			$query->group($db->quoteName(array('a.id', 'a.name', 'a.username', 'a.password', 'a.block', 'a.sendEmail', 'a.registerDate', 'a.lastvisitDate', 'a.activation', 'a.params', 'a.email')));

			if ($groupId) {
				$query->where('map2.group_id = '.(int) $groupId);
			}
		}

		// Filter the items over the search string if set.
		if ($this->getState('filter.search') !== '')
		{
			// Escape the search token.
			$token	= $db->Quote('%'.$db->escape($this->getState('filter.search')).'%');

			// Compile the different search clauses.
			$searches	= array();
			$searches[]	= 'a.name LIKE '.$token;
			$searches[]	= 'a.username LIKE '.$token;
			$searches[]	= 'a.email LIKE '.$token;

			// Add the clauses to the query.
			$query->where('('.implode(' OR ', $searches).')');
		}
		
		// Filter by excluded users
		$excluded = $this->_getExcluded();
		if (!empty($excluded)) {
			JArrayHelper::toInteger($excluded);
			$query->where('id NOT IN ('.implode(',', $excluded).')');
		}
		
		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.name')).' '.$db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}
	
	/**
	 * Gets the list of users and adds expensive joins to the result set.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItems() {
		$items = parent::getItems();
		
		if (empty($items))
			return array();

		// First pass: get list of the user id's and reset the counts.
		$userIds = array();
		foreach ($items as $item) {
			$userIds[] = (int) $item->id;
			$item->group_names = '';
		}

		// Get the counts from the database only for the users in the list.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Join over the group mapping table.
		$query->select('map.user_id, COUNT(map.group_id) AS group_count')
			->from('#__user_usergroup_map AS map')
			->where('map.user_id IN ('.implode(',', $userIds).')')
			->group('map.user_id')
			// Join over the user groups table.
			->join('LEFT', '#__usergroups AS g2 ON g2.id = map.group_id');

		$db->setQuery($query);

		// Load the counts into an array indexed on the user id field.
		try {
			$userGroups = $db->loadObjectList('user_id');
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
			return false;
		}

		// Second pass: collect the group counts into the master items array.
		foreach ($items as &$item) {
			if (isset($userGroups[$item->id])) {
				//Group_concat in other databases is not supported
				$item->group_names = $this->_getUserDisplayedGroups($item->id);
			}
		}

		return $items;
	}
	
	protected function _getUserDisplayedGroups($user_id) {
		$db = JFactory::getDbo();
		$sql = "SELECT title FROM ".$db->quoteName('#__usergroups')." ug left join ".
				$db->quoteName('#__user_usergroup_map')." map on (ug.id = map.group_id)".
				" WHERE map.user_id=".$user_id;

		$db->setQuery($sql);
		$result = $db->loadColumn();
		return implode("\n", $result);
	}
	
	protected function _getExcluded() {
		$root = rsfilesHelper::getConfig('briefcase_folder');
		return JFolder::folders($root, '.', false, false, array('.htaccess'));
	}
}