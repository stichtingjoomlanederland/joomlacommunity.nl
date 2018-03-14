<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Load framework base classes
jimport('joomla.application.component.modellist');
jimport('joomla.access.rules');

/**
 * Aclmanager Model
 */
class AclmanagerModelUser extends JModelList
{
	/**
	 * Override getItems method.
	 */
	public function getItems()
	{
		// Initialise variable
		$user = $this->getState('filter.user_id'); // Get User ID

		if (($assets = parent::getItems()) && ($user)) {

			foreach ($assets as &$asset)
			{
				if(!$asset->rules) {$asset->rules = '{}';}
				$rules			= json_decode($asset->rules,true);
				$rules			= array_keys($rules);
				$asset->checks	= array();
				$asset->rule	= array();
				$asset->third	= 0;
				$assetRules		= new JAccessRules;
				$assetRules 	= new $assetRules($asset->rules);

				if($asset->level == 1) {
					$asset->component = $asset->name;
				} else {
					$asset->component = substr($asset->name, 0, stripos($asset->name, ".") );
				}

				foreach ($rules as $rule)
				{
					$asset->checks[$rule]	= JAccess::check($user, $rule, $asset->id);
					$asset->rule[$rule]		= null;

					// Check for additional rules
					$asset->third = AclmanagerHelper::thirdCheck($rule,$asset->component,$asset->third);
				}
			}
		}

		return $assets;
	}

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$userId	= JFactory::getApplication()->input->get('id',0);
		if (!$userId) {
			$userId = $app->getUserStateFromRequest($this->context.'.filter.user', 'filter_user_id', null, 'int');
		}
		$this->setState('filter.user_id', $userId);

		$componentID = $app->getUserStateFromRequest($this->context.'.filter.component', 'filter_component', null, 'STRING');
		$this->setState('filter.component', $componentID);

		$category = $app->getUserStateFromRequest($this->context.'.filter.category', 'filter_category', null, 'STRING');
		$this->setState('filter.category', $category);

		$item = $app->getUserStateFromRequest($this->context.'.filter.item', 'filter_item', null, 'STRING');
		$this->setState('filter.item', $item);

		// Load the parameters.
		$params		= JComponentHelper::getParams('com_aclmanager');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.name', 'asc');
	}


	/**
	 * Get a list of the user groups for filtering.
	 */
	static function getPermissions()
	{
		// Initialise variables.
		$app 			= JFactory::getApplication();
		$db 			= JFactory::getDbo();
		$permissions 	='';
		$userId	= JFactory::getApplication()->input->get('id',0);
		if (!$userId) {
			$userId = $app->getUserStateFromRequest('filter.user', 'filter_user_id', null, 'int'); // Get User ID
		}

		if($userId) {
			// Get group(s) information for user
			$db->setQuery(
				'SELECT a.id AS value, a.title AS title, COUNT(DISTINCT b.id) AS level' .
				' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents' .
				' FROM #__usergroups AS a' .
				' LEFT JOIN #__usergroups AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
				' LEFT JOIN #__user_usergroup_map AS c ON c.group_id = a.id' .
				' WHERE c.user_id = '.$userId .
				' GROUP BY a.id' .
				' ORDER BY a.lft ASC'
			);

			$permissions = $db->loadObjectList();

			foreach ($permissions as &$permission) {
				$permission->identities = ($permission->parents) ? explode(',', $permission->parents.','.$permission->value) : array($permission->value);
			}
		}

		return $permissions;
	}

	/**
	 * Build an SQL query to load the list data.
	 */
	protected function getListQuery()
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$params = JComponentHelper::getParams('com_aclmanager',1);

		// Override list limit for print view
		$layout	= JFactory::getApplication()->input->get('layout',null);
		if ($layout =='print') {
				$this->setState('list.limit', 0);
				$this->setState('list.start', 0);
		}

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id AS id, a.name AS name, a.title AS title, a.level AS level, a.parent_id AS parent, a.rules AS rules')
		);
		$query->from('#__assets AS a');
		$query->where('a.name not like \'com_admin\' AND a.name not like \'com_config\' AND a.name not like \'com_cpanel\' AND a.name not like \'com_login\' AND a.name not like \'com_mailto\' AND a.name not like \'com_massmail\' AND a.name not like \'com_wrapper\' AND a.name not like \'com_contenthistory\' AND a.name not like \'com_ajax\'');

		// Filter on the categories.
		if ($this->getState('filter.category') == '0') {
			$query->where('a.level = 0 OR a.level = 1');
			$query->where('a.name not like \'com_admin\' AND a.name not like \'com_config\' AND a.name not like \'com_cpanel\' AND a.name not like \'com_login\' AND a.name not like \'com_mailto\' AND a.name not like \'com_massmail\' AND a.name not like \'com_wrapper\' AND a.name not like \'com_contenthistory\' AND a.name not like \'com_ajax\'');
		}

		// Filter on the items.
		if (($this->getState('filter.item') == '0') || ($this->getState('filter.category') == '0')) {
			$query->where('a.name not like \'com_content.article%\'');
			$query->where('a.name not like \'com_modules.module%\'');
		}

		// Filter on the component.
		$component = $this->getState('filter.component');
		if ($this->getState('filter.component')) {
			$query->where('(a.name like \''.$component.'%\' or a.name like \'root%\')');
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('(a.title LIKE '.$search.')');
		}

		$query->order('a.lft ASC');
		return $query;
	}
}