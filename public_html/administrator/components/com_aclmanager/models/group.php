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
class AclmanagerModelGroup extends JModelList
{

	/**
	 * Save group permissions.
	 */
	public function save($data)
	{
		// Save the rules
		if (isset($data['rules']))
		{
			// Initialise variables.
			$rules 	= $data['rules'];
			$db 	= JFactory::getDBO();
			$group 	= $this->getState('filter.group_id');

			// Loop through asset rows
			foreach($rules as $id=>$rule){
				foreach($rule as $s=>$action){
					$rule[$s]= (array_filter($action,'strlen'));
				}

				$currentrules = JAccess::getAssetRules($id);
				$newrules = json_decode($currentrules,true);

				foreach($newrules as $i=>$newrule) {
					unset($newrule[$group]);
					if(array_key_exists($i, $rule)){
						$newrules[$i] = $newrule + $rule[$i];
					} else {
						$newrules[$i] = $newrule;
					}
					ksort($newrules[$i]);
				}

				$newrules = new JAccessRules($newrules);

				// Save new permissions if different
				if($currentrules != $newrules) {
					$query = $this->_db->getQuery(true);
					$query->update('#__assets');
					$query->set('rules = ' . $db->quote($newrules));
					$query->where('id = ' . (int) $id);
					$this->_db->setQuery($query);
					if (!$this->_db->query())
					{
						$this->setError(JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_FAILED'));
						return false;
					}

				}

			}
			// Unset form data
			unset($data['rules']);
		}

	}

	/**
	 * Reset group permissions.
	 */
	public function reset($data,$task)
	{
		// Save the rules
		if (isset($data['rules']))
		{
			// Default Joomla permissions
			$default = array();
			$default[1]['core.login.site'][6] = 1;
			$default[1]['core.login.site'][2] = 1;
			$default[1]['core.login.admin'][6] = 1;
			$default[1]['core.login.offline'][6] = 1;
			$default[1]['core.admin'][8] = 1;
			$default[1]['core.manage'][7] = 1;
			$default[1]['core.create'][6] = 1;
			$default[1]['core.create'][3] = 1;
			$default[1]['core.delete'][6] = 1;
			$default[1]['core.edit'][6] = 1;
			$default[1]['core.edit'][4] = 1;
			$default[1]['core.edit.state'][6] = 1;
			$default[1]['core.edit.state'][5] = 1;
			$default[1]['core.edit.own'][6] = 1;
			$default[1]['core.edit.own'][3] = 1;
			$default[3]['core.admin'][7] = 1;
			$default[3]['core.manage'][6] = 1;
			$default[4]['core.admin'][7] = 1;
			$default[4]['core.manage'][7] = 1;
			$default[5]['core.admin'][7] = 1;
			$default[5]['core.manage'][7] = 1;
			$default[7]['core.admin'][7] = 1;
			$default[7]['core.manage'][6] = 1;
			$default[8]['core.admin'][7] = 1;
			$default[8]['core.manage'][6] = 1;
			$default[8]['core.create'][3] = 1;
			$default[8]['core.edit'][4] = 1;
			$default[8]['core.edit.state'][5] = 1;
			$default[10]['core.manage'][7] = 0;
			$default[10]['core.delete'][7] = 0;
			$default[10]['core.edit.state'][7] = 0;
			$default[11]['core.admin'][7] = 1;
			$default[15]['core.admin'][7] = 1;
			$default[15]['core.manage'][6] = 1;
			$default[15]['core.create'][3] = 1;
			$default[15]['core.delete'][5] = 1;
			$default[16]['core.admin'][7] = 1;
			$default[17]['core.admin'][7] = 1;
			$default[17]['core.manage'][7] = 1;
			$default[18]['core.admin'][7] = 1;
			$default[19]['core.admin'][7] = 1;
			$default[19]['core.manage'][6] = 1;
			$default[20]['core.admin'][7] = 1;
			$default[21]['core.admin'][7] = 1;
			$default[22]['core.admin'][7] = 1;
			$default[22]['core.manage'][6] = 1;
			$default[23]['core.admin'][7] = 1;
			$default[24]['core.admin'][7] = 1;
			$default[25]['core.admin'][7] = 1;
			$default[25]['core.manage'][6] = 1;
			$default[25]['core.create'][3] = 1;
			$default[25]['core.edit'][4] = 1;
			$default[25]['core.edit.state'][5] = 1;
			$default[33]['core.admin'][7] = 1;
			$default[33]['core.manage'][6] = 1;

			// Initialise variables.
			$rules 	= $data['rules'];
			$db 	= JFactory::getDBO();
			$group 	= $this->getState('filter.group_id');

			// Loop through asset rows
			foreach($rules as $id=>$rule){
				foreach($rule as $s=>$action){
					$rule[$s]= (array_filter($action,'strlen'));
				}

				$currentrules = JAccess::getAssetRules($id);
				$newrules = json_decode($currentrules,true);

				foreach($newrules as $i=>$newrule) {
					unset($newrule[$group]);
					if(($task == 'reset') && (array_key_exists($i, $default[$id]))) {
						$newrules[$i] = $newrule + $default[$id][$i];
					} else {
						$newrules[$i] = $newrule;
					}
					ksort($newrules[$i]);
				}

				$newrules = new JAccessRules($newrules);

				// Save new permissions if different
				if($currentrules != $newrules) {
					$query = $this->_db->getQuery(true);
					$query->update('#__assets');
					$query->set('rules = ' . $db->quote($newrules));
					$query->where('id = ' . (int) $id);
					$this->_db->setQuery($query);
					if (!$this->_db->query())
					{
						$this->setError(JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_FAILED'));
						return false;
					}

				}

			}
			// Unset form data
			unset($data['rules']);
		}

	}


	/**
	 * Override getItems method.
	 */
	public function getItems()
	{
		// Initialise variable
		$groupId = $this->getState('filter.group_id'); // Get Group ID
		$assets = parent::getItems();
		$db = JFactory::getDbo();

		// Get global configuration row
		$query = $db->getQuery(true);
		$query->select('a.id AS id, a.name AS name, a.title AS title, a.level AS level, a.parent_id AS parent, a.rules AS rules');
		$query->from('#__assets AS a');
		$query->where('parent_id = 0');
		$db->setQuery($query);
		$configuration = $db->loadObjectList();
		$assets = array_merge($configuration,$assets);

		// Get disabled components
		$query = $db->getQuery(true);
		$query->select('name')
			->from('#__extensions')
			->where('enabled = 0')
			->where('type = \'component\'');

		$disabled = $db->setQuery($query)->loadColumn();

		if ($assets && $groupId) {

			foreach ($assets as $key=>$asset)
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

				// Unset asset if component is disabled
				if(in_array($asset->component, $disabled)) {
					unset($assets[$key]);
				}

				foreach ($rules as $rule)
				{
					$asset->checks[$rule]	= JAccess::checkGroup($groupId, $rule, $asset->id);
					$asset->rule[$rule]		= $assetRules->allow($rule, $groupId);

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

		$groupId = JFactory::getApplication()->input->get('id',0);
		if(!$groupId) {
			$groupId = $app->getUserStateFromRequest($this->context.'.filter.group', 'filter_group_id', null, 'STRING');
		}
		$this->setState('filter.group_id', $groupId);

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
		$groupId = $app->getUserStateFromRequest('filter.group', 'filter_group_id', null, 'STRING');

		if($groupId) {
			// Get group information
			$db->setQuery(
				'SELECT a.id AS value, a.title AS title, COUNT(DISTINCT b.id) AS level' .
				' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents' .
				' FROM #__usergroups AS a' .
				' LEFT JOIN #__usergroups AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
				' WHERE a.id = '.$groupId .
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
		if (($layout =='print') || ($layout =='reset')) {
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