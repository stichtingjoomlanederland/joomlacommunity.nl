<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;

// No direct access.
defined('_JEXEC') or die;

/**
 * PWT ACL Assets Model
 *
 * @since   3.0
 */
class PwtaclModelAssets extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   3.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'component',
				'level_start',
				'level_end',
				'item',
				'language'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to save action
	 *
	 * @param   integer $assetId Asset ID
	 * @param   string  $action  Action name
	 * @param   integer $group   Group ID
	 * @param   integer $setting Action setting
	 *
	 * @return  integer
	 * @since   3.0
	 * @throws  Exception
	 */
	public function saveAction($assetId, $action, $group, $setting)
	{
		// Get rules of asset
		$assetrules = Access::getAssetRules($assetId, false, false);
		$assetrules = json_decode($assetrules, true);

		// Change the action setting
		if ($setting == 9)
		{
			unset($assetrules[$action][$group]);
		}

		if ($setting == 1 || $setting == 0)
		{
			$assetrules[$action][$group] = $setting;
		}

		// Save new asset rules
		$this->saveAssetRules($assetId, $assetrules);

		// Get new action permission
		$newsetting = Access::checkGroup($group, $action, $assetId);

		// Clear Statics to prevent cached results
		Access::clearStatics();

		return (int) $newsetting;
	}

	/**
	 * Method to clear permissions for group
	 *
	 * @param   integer $group Group ID
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception
	 */
	public function clear($group)
	{
		// Get assets to clear
		$assets = $this->getAssets($this->_db->quoteName('rules') . ' LIKE ' . $this->_db->quote('%"' . $group . '"%'), 'name');

		// Remove group settings for each asset
		foreach ($assets as $asset)
		{
			// Get current rules
			$assetrules = json_decode($asset->rules, true);

			// Remove action setting
			foreach ($assetrules as $action => $values)
			{
				unset($assetrules[$action][$group]);
			}

			// Save new asset rules
			$this->saveAssetRules($asset->id, $assetrules);
		}

		return;
	}

	/**
	 * Method to reset permissions for group
	 *
	 * @param   integer $group Group ID
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception
	 */
	public function reset($group)
	{
		// Clear group first
		$this->clear($group);

		// Reset Access after clearing group to prevent cached results
		Access::clearStatics();

		// Load default group settings
		$defaults = $this->defaultSettings($group);

		// Combine asset rules with new settings and save it
		foreach ($defaults as $assetname => $actions)
		{
			// Load current rules
			$assetrules = Access::getAssetRules($assetname);
			$assetrules = json_decode($assetrules, true);

			// Add new action settings
			foreach ($actions as $action => $setting)
			{
				$assetrules[$action][$group] = $setting;
			}

			// Save new asset rules
			$this->saveAssetRules($assetname, $assetrules);
		}

		return;
	}

	/**
	 * Method to copy permissions for group
	 *
	 * @param   integer $group  Group ID
	 * @param   integer $copyTo New Group ID
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception
	 */
	public function copy($group, $copyTo)
	{
		// Clear group first
		$this->clear($copyTo);

		// Reset Access after clearing group to prevent cached results
		Access::clearStatics();

		// Load default group settings
		$assets = $this->getAssets($this->_db->quoteName('rules') . ' LIKE ' . $this->_db->quote('%"' . $group . '"%'), 'name');

		// Remove group settings for each asset
		foreach ($assets as $asset)
		{
			// Get current rules
			$assetrules = json_decode($asset->rules, true);

			// Remove action setting
			foreach ($assetrules as $action => $values)
			{
				if (isset($values[$group]))
				{
					$assetrules[$action][$copyTo] = $values[$group];
				}
			}

			// Save new asset rules
			$this->saveAssetRules($asset->id, $assetrules);
		}

		return;
	}

	/**
	 * Method to export permissions for group
	 *
	 * @param   integer $group Group ID
	 *
	 * @return  array
	 * @since   3.0
	 * @throws  Exception
	 */
	public function export($group)
	{
		// Load default group settings
		$assets = $this->getAssets($this->_db->quoteName('rules') . ' LIKE ' . $this->_db->quote('%"' . $group . '"%'), 'name');

		$export = array(
			'generator'   => 'PWT ACL',
			'permissions' => array()
		);

		// Remove group settings for each asset
		foreach ($assets as $asset)
		{
			// Get current rules
			$assetrules = json_decode($asset->rules, true);

			// Remove action setting
			foreach ($assetrules as $action => $values)
			{
				if (isset($values[$group]))
				{
					$export['permissions'][$asset->name][$action] = $values[$group];
				}
			}

			// Save new asset rules
			$this->saveAssetRules($asset->id, $assetrules);
		}

		return $export;
	}

	/**
	 * Method to reset permissions for group
	 *
	 * @param   integer $group       Group ID
	 * @param   array   $permissions Array with permissions for actions
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception
	 */
	public function import($group, $permissions)
	{
		// Clear group first
		$this->clear($group);

		// Reset Access after clearing group to prevent cached results
		Access::clearStatics();

		// Combine asset rules with new settings and save it
		foreach ($permissions as $assetname => $actions)
		{
			// Load current rules
			$assetrules = Access::getAssetRules($assetname);
			$assetrules = json_decode($assetrules, true);

			// Add new action settings
			foreach ($actions as $action => $setting)
			{
				$assetrules[$action][$group] = $setting;
			}

			// Save new asset rules
			$this->saveAssetRules($assetname, $assetrules);
		}

		return;
	}

	/**
	 * Method to save rules for asset
	 *
	 * @param   string $asset Asset ID or Asset Name
	 * @param   array  $rules Rules for asset
	 *
	 * @return  mixed
	 * @since   3.0
	 * @throws  Exception
	 */
	public function saveAssetRules($asset, $rules)
	{
		// Convert rules to AccessRules
		$rules = new JAccessRules($rules);

		// Save asset in database
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query
			->update('#__assets')
			->set('rules = ' . $db->quote($rules));

		// Is this an asset id or name
		if (is_numeric($asset))
		{
			$query->where('id = ' . (int) $asset);
		}
		else
		{
			$query->where('name = ' . $db->quote($asset));
		}

		try
		{
			return $db->setQuery($query)->execute();
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}
	}

	/**
	 * Gets records from the #__assets table.
	 *
	 * @param   string $where Where query.
	 * @param   string $key   LoadObjectList key.
	 *
	 * @return  array
	 * @since   3.0
	 * @throws  Exception
	 */
	public function getAssets($where, $key = 'id')
	{
		if (!$where)
		{
			return array();
		}

		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query
			->select(
				array('*',
					'SUBSTRING_INDEX(name, ".", 1) AS component',
					'SUBSTRING_INDEX(name, ".", -1) AS objectid',
					'"" AS additional',
					'IF(name LIKE "com_%.%", SUBSTRING_INDEX(SUBSTRING_INDEX(name, ".", 2), ".", -1), 
						IF(name = "root.1", "config", "component"
						))  AS type')
			)
			->from($db->quoteName('#__assets'))
			->where($where)
			->order('lft ASC');

		try
		{
			return $db->setQuery($query)->loadObjectList($key);
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string $ordering  An optional ordering field.
	 * @param   string $direction An optional direction (asc|desc).
	 *
	 * @return  void
	 * @since   3.0
	 */
	protected function populateState($ordering = 'a.name', $direction = 'asc')
	{
		// Load the filter state.
		$type = $this->getUserStateFromRequest($this->context . '.type', 'type', 'group');
		$this->setState('type', $type);
		$this->setState('group', $this->getUserStateFromRequest($this->context . '.group', 'group'));
		$this->setState('user', $this->getUserStateFromRequest($this->context . '.user', 'user'));
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search'));
		$this->setState('filter.component', $this->getUserStateFromRequest($this->context . '.filter.component', 'filter_component', '', 'string'));
		$this->setState('filter.level_start', $this->getUserStateFromRequest($this->context . '.filter.level_start', 'filter_level_start'));
		$this->setState('filter.level_end', $this->getUserStateFromRequest($this->context . '.filter.level_end', 'filter_level_end'));
		$this->setState('filter.item', $this->getUserStateFromRequest($this->context . '.filter.item', 'filter_item'));
		$this->setState('filter.language', $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language'));


		if ($type == 'group')
		{
			$this->setState('user', null);
		}

		if ($type == 'user')
		{
			$this->setState('group', null);
		}

		// Load the parameters.
		$this->setState('params', ComponentHelper::getParams('com_pwtacl'));

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @param   string $id A prefix for the store id.
	 *
	 * @since   3.0
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('type');
		$id .= ':' . $this->getState('group');
		$id .= ':' . $this->getState('user');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.component');
		$id .= ':' . $this->getState('filter.level_start');
		$id .= ':' . $this->getState('filter.level_end');
		$id .= ':' . $this->getState('filter.item');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Method to create a query for a list of assets.
	 *
	 * @return  string
	 * @since   3.0
	 */
	protected function getListQuery()
	{
		// Initialise variables.
		$db = $this->getDbo();

		// Prevent server limitation issues
		$db->setQuery('SET SQL_BIG_SELECTS=1')->execute();

		// Compile query
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select(
				array(
					'a.id AS id',
					'a.name AS name',
					'a.title AS title',
					'a.level AS level',
					'a.parent_id AS parent_id',
					'a.rules AS rules',
					'SUBSTRING_INDEX(a.name, ".", 1) AS component',
					'IF(a.name LIKE "com_%.%", SUBSTRING_INDEX(SUBSTRING_INDEX(a.name, ".", 2), ".", -1), 
						IF(a.name = "root.1", "config", "component"
						))  AS type',
					'SUBSTRING_INDEX(a.name, ".", -1) AS objectid',
					'"" AS icon',
					'CONCAT("index.php?option=", SUBSTRING_INDEX(a.name, ".", 1)) AS link',
					'"" AS additional',
					'"" AS actions',
					'"" AS pwtacl',
					'IF(content.state IS NOT NULL, content.state, 
						IF(categories.published IS NOT NULL, categories.published, 
						IF(modules.published IS NOT NULL, modules.published, 
						IF(fields.state IS NOT NULL, fields.state, 
						IF(fieldsgroups.state IS NOT NULL, fieldsgroups.state, 
						IF(extensions.enabled IS NOT NULL, extensions.enabled, 1
						)))))) AS state',
					'IF(content.language IS NOT NULL, content.language, 
						IF(categories.language IS NOT NULL, categories.language, 
						IF(modules.language IS NOT NULL, modules.language, 
						IF(fields.language IS NOT NULL, fields.language, 
						IF(fieldsgroups.language IS NOT NULL, fieldsgroups.language, "*"
						))))) AS language'
				)
			)
			->from($db->quoteName('#__assets') . ' AS a')
			->leftJoin($db->quoteName('#__content', 'content') . ' ON content.asset_id = a.id')
			->leftJoin($db->quoteName('#__categories', 'categories') . ' ON categories.asset_id = a.id')
			->leftJoin($db->quoteName('#__modules', 'modules') . ' ON modules.asset_id = a.id')
			->leftJoin($db->quoteName('#__fields', 'fields') . ' ON fields.asset_id = a.id')
			->leftJoin($db->quoteName('#__fields_groups', 'fieldsgroups') . ' ON fieldsgroups.asset_id = a.id')
			->leftJoin($db->quoteName('#__extensions', 'extensions') . ' ON extensions.element = a.name');

		// Filter out UCM content assets
		$query
			->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('#__ucm_content.%'));

		// Filter out language assets (as long as they don't have actions)
		$query
			->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_languages.language.%'));

		// Filter out core components assets without ACL
		$query
			->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_admin'))
			->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_config'))
			->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_cpanel'))
			->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_login'))
			->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_mailto'))
			->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_wrapper'))
			->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_contenthistory'))
			->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_ajax'))
			->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_fields'))
			->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_actionlogs'))
			->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_privacy'));

		// Filter on the start and end levels.
		$levelStart = (int) $this->getState('filter.level_start');
		$levelEnd   = (int) $this->getState('filter.level_end');

		if ($levelEnd > 0 && $levelEnd < $levelStart)
		{
			$levelEnd = $levelStart;
		}

		if ($levelStart > 0)
		{
			$query
				->where($db->quoteName('a.level') . ' >= ' . ($levelStart - 1));
		}

		if ($levelEnd > 0)
		{
			$query
				->where($db->quoteName('a.level') . ' <= ' . ($levelEnd - 1));
		}

		// Filter on the items.
		$item = $this->getState('filter.item');

		if (is_numeric($item) && $item == 0)
		{
			$query
				->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_content.article.%'))
				->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_modules.module.%'))
				->where($db->quoteName('a.name') . ' NOT LIKE ' . $db->quote('com_users.field.%'));
		}

		// Filter on the component.
		$component = $this->getState('filter.component');

		if (!empty($component))
		{
			$query
				->where(
					$db->quoteName('a.name') . ' LIKE ' . $db->quote($db->escape(trim($component) . '%')) . ' OR ' .
					$db->quoteName('a.name') . ' LIKE ' . $db->quote('root%')
				);
		}

		// Filter on the language.
		$language = $this->getState('filter.language');

		if (!empty($language))
		{
			$query
				->where(
					$db->quoteName('content.language') . ' = ' . $db->quote($language) . ' OR ' .
					$db->quoteName('categories.language') . ' = ' . $db->quote($language) . ' OR ' .
					$db->quoteName('modules.language') . ' = ' . $db->quote($language) . ' OR ' .
					$db->quoteName('fields.language') . ' = ' . $db->quote($language) . ' OR ' .
					$db->quoteName('fieldsgroups.language') . ' = ' . $db->quote($language)
				);
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$query
				->where($db->quoteName('a.title') . ' LIKE ' . $db->quote('%' . $db->escape(trim($search), true) . '%'));
		}

		// Ordering
		$query->order('a.lft ASC');

		return $query;
	}

	/**
	 * Method to get an array of assets.
	 *
	 * @return  array  An array of assets.
	 * @since   3.0
	 */
	public function getItems()
	{
		// Get assets
		$assets = parent::getItems();

		$type  = $this->getState('type');
		$group = $this->getState('group');
		$user  = $this->getState('user');

		return $this->prepareAssets($assets, $type, $group, $user);
	}

	/**
	 * Method to prepare the assets for rendering
	 *
	 * @param   array   $assets Array of the assets
	 * @param   string  $type   group or user
	 * @param   integer $group  Group ID
	 * @param   integer $user   User ID
	 *
	 * @return  array
	 *
	 * @since   3.0
	 */
	public function prepareAssets($assets, $type, $group, $user)
	{
		// Initialise variables
		$coreactions    = $this->getActions('root.1', true);
		$groupsparent   = $this->getGroupsParent();
		$params         = ComponentHelper::getParams('com_pwtacl');
		$superUser      = Factory::getUser($user)->authorise('core.admin', 'root.1');
		$canEdit        = Factory::getUser()->authorise('core.edit', 'com_pwtacl');
		$superUserGroup = Access::checkGroup($group, 'core.admin', 'root.1');
		$languages      = LanguageHelper::getLanguages('lang_code');

		// Prepare asset rows
		foreach ($assets as $key => $asset)
		{
			// Unset com_categories asset if not in use
			if (!$params->get('acl_categorymanager', 0) && $asset->name == 'com_categories')
			{
				unset($assets[$key]);
			}

			// Variables
			$actions    = $this->getActions($asset->name, false);
			$assetRules = new JAccessRules($asset->rules);

			// ACL by PWT ACL
			$asset->pwtacl = $actions->pwtacl;

			// Actions and settings
			$asset->actions             = new stdClass;
			$asset->actions->core       = new stdClass;
			$asset->actions->additional = new stdClass;

			foreach ($coreactions->items as $name)
			{
				$asset->actions->core->{$name}                     = new stdClass;
				$asset->actions->core->{$name}->name               = $name;
				$asset->actions->core->{$name}->class              = 'no-action';
				$asset->actions->core->{$name}->setting            = null;
				$asset->actions->core->{$name}->setting_calculated = null;
				$asset->actions->core->{$name}->setting_parent     = null;
				$asset->actions->core->{$name}->icon               = null;
			}

			if ($asset->name !== 'root.1')
			{
				unset($asset->actions->core->{'core.login.site'});
				unset($asset->actions->core->{'core.login.admin'});
				unset($asset->actions->core->{'core.login.offline'});
			}

			foreach ($actions->items as $action)
			{
				$actiontype = (in_array($action->name, $coreactions->items)) ? 'core' : 'additional';

				// Add title and name
				$asset->actions->{$actiontype}->{$action->name}        = new stdClass;
				$asset->actions->{$actiontype}->{$action->name}->title = $action->title;
				$asset->actions->{$actiontype}->{$action->name}->name  = $action->name;

				// Group actions
				if ($type == 'group')
				{
					$setting           = $assetRules->allow($action->name, $group);
					$settingCalculated = Access::checkGroup($group, $action->name, $asset->id);
					$settingParent     = Access::checkGroup($group, $action->name, $asset->parent_id);
				}

				// User actions
				if ($type == 'user' && $superUser)
				{
					$settingCalculated = 1;
					$settingParent     = 1;
				}
				elseif ($type == 'user')
				{
					$settingCalculated = Access::check($user, $action->name, $asset->id);
					$settingParent     = Access::check($user, $action->name, $asset->parent_id);
				}

				// Get action settings
				$setting           = isset($setting) ? $setting : 9;
				$settingCalculated = isset($settingCalculated) ? $settingCalculated : 9;
				$settingParent     = isset($settingParent) ? $settingParent : 9;

				// Not set - inherited
				if ($settingCalculated == 9)
				{
					$asset->actions->{$actiontype}->{$action->name}->class = 'action';
					$asset->actions->{$actiontype}->{$action->name}->icon  = 'icon-not-ok';
				}

				// Allowed - inherited
				if ($settingCalculated == 1)
				{
					// Action Not set
					if ($setting == 9)
					{
						$asset->actions->{$actiontype}->{$action->name}->class = 'action';
						$asset->actions->{$actiontype}->{$action->name}->icon  = 'icon-ok';
					}

					// Action Allowed
					if ($setting == 1)
					{
						$asset->actions->{$actiontype}->{$action->name}->class = 'action allowed';
						$asset->actions->{$actiontype}->{$action->name}->icon  = 'icon-ok';
					}
				}

				// Denied - inherited
				if ($settingCalculated == 0)
				{
					// Action Not set
					if ($setting == 9)
					{
						$asset->actions->{$actiontype}->{$action->name}->class = 'action';
						$asset->actions->{$actiontype}->{$action->name}->icon  = 'icon-lock';

						// Set correct icon for user view
						if ($type == 'user')
						{
							$asset->actions->{$actiontype}->{$action->name}->icon = 'icon-not-ok';
						}
					}

					// Action Allowed (so we have a conflict here)
					if ($setting == 1)
					{
						$asset->actions->{$actiontype}->{$action->name}->class = 'action conflict';
						$asset->actions->{$actiontype}->{$action->name}->icon  = 'icon-warning';
					}

					// Action Denied
					if ($setting == 0)
					{
						$asset->actions->{$actiontype}->{$action->name}->class = 'action denied';
						$asset->actions->{$actiontype}->{$action->name}->icon  = 'icon-not-ok';
					}
				}

				// Add class for user view
				if ($type == 'group' && $canEdit)
				{
					if ($superUserGroup && $action->name == 'core.admin' && $asset->name == 'root.1')
					{
						$asset->actions->{$actiontype}->{$action->name}->class = $asset->actions->{$actiontype}->{$action->name}->class . ' edit';
					}
					elseif (!$superUserGroup)
					{
						$asset->actions->{$actiontype}->{$action->name}->class = $asset->actions->{$actiontype}->{$action->name}->class . ' edit';
					}
					elseif ($superUserGroup)
					{
						$settingCalculated                                     = 1;
						$asset->actions->{$actiontype}->{$action->name}->class = 'action';
						$asset->actions->{$actiontype}->{$action->name}->icon  = 'icon-ok';
					}
				}

				// Make sure the root.1 assets have correct parent settings
				if ($asset->name == 'root.1' && $groupsparent == $group)
				{
					$settingParent = 9;
				}
				elseif ($asset->name == 'root.1')
				{
					$settingParent = $settingCalculated;
				}

				$asset->actions->{$actiontype}->{$action->name}->setting            = (int) $setting;
				$asset->actions->{$actiontype}->{$action->name}->setting_calculated = (int) $settingCalculated;
				$asset->actions->{$actiontype}->{$action->name}->setting_parent     = (int) $settingParent;
			}

			// Global configuration
			switch ($asset->type)
			{
				// Root
				case 'config':
					$asset->title    = Text::_('COM_CONFIG_GLOBAL_CONFIGURATION');
					$asset->link     = 'index.php?option=com_config';
					$asset->icon     = 'equalizer';
					$asset->objectid = null;
					break;

				// Components
				case 'component':
					$asset->title    = Text::_($asset->component);
					$asset->icon     = 'puzzle';
					$asset->objectid = null;
					break;

				// Categories
				case 'category':
					$asset->link = 'index.php?option=com_categories&task=category.edit&id=' . $asset->objectid . '&extension=' . $asset->component;
					$asset->icon = 'folder';
					break;

				// Articles
				case 'article':
					$asset->link = 'index.php?option=' . $asset->component . '&task=article.edit&id=' . $asset->objectid;
					$asset->icon = 'stack';
					break;

				//  Modules
				case 'module':
					$asset->link = 'index.php?option=' . $asset->component . '&task=module.edit&id=' . $asset->objectid;
					$asset->icon = 'cube';
					break;

				//  Menus
				case 'menu':
					$asset->link = 'index.php?option=com_menus&task=menu.edit&id=' . $asset->objectid;
					$asset->icon = 'list';
					break;

				//  Fieldgroups
				case 'fieldgroup':
					$asset->link = 'index.php?option=com_fields&task=group.edit&id=' . $asset->objectid;
					$asset->icon = 'puzzle';
					break;

				//  Fields
				case 'field':
					$asset->link = 'index.php?option=com_fields&task=field.edit&id=' . $asset->objectid;
					$asset->icon = 'puzzle';
					break;
			}

			// Additional actions
			if (!empty((array) $asset->actions->additional))
			{
				$asset->additional = true;
			}

			// Set language image
			if (isset($asset->language))
			{
				$asset->languageimage = ($asset->language == '*') ? '' : $languages[$asset->language]->image;
				$asset->languagetitle = ($asset->language == '*') ? '' : $languages[$asset->language]->title;
			}
		}

		return $assets;
	}

	/**
	 * Method to get actions for asset.
	 *
	 * @param   string  $assetname   Name of asset.
	 * @param   boolean $actionsonly Return only array of action names.
	 *
	 * @return  stdClass
	 * @since   3.0
	 */
	public function getActions($assetname, $actionsonly = false)
	{
		// Get parts of the asset name
		$asset = explode('.', $assetname);

		// Set section to component if not set
		$section = isset($asset[1]) ? $asset[1] : 'component';

		// Array with the actions
		$actions         = new stdClass;
		$actions->pwtacl = false;
		$actions->items  = array();

		// Load actions for root asset
		if ($assetname == 'root.1')
		{
			$actions->items = Access::getActionsFromFile(
				JPATH_ADMINISTRATOR . '/components/com_config/model/form/application.xml',
				"/form/fieldset[@name='permissions']/field/"
			);
		}

		// Load actions from component for section
		if ($assetname != 'root.1')
		{
			// Get actions from access.xml
			$actions->items = Access::getActionsFromFile(
				JPATH_ADMINISTRATOR . '/components/' . $asset[0] . '/access.xml',
				"/access/section[@name='" . $section . "']/"
			);

			// Get actions from config.xml as fallback
			if (empty($actions->items))
			{
				$actions->items = Access::getActionsFromFile(
					JPATH_ADMINISTRATOR . '/components/' . $asset[0] . '/config.xml',
					"/config/fieldset/field[@section='" . $section . "']/"
				);
			}

			// Add PWT ACL actions as fallback
			if (empty($actions->items))
			{
				$actions->pwtacl = true;

				// Get actions from access.xml
				$actions->items = Access::getActionsFromFile(
					JPATH_ADMINISTRATOR . '/components/com_pwtacl/models/forms/fallback-access.xml',
					"/section[@name='" . $section . "']/"
				);
			}
		}

		// Make simple actions array if needed
		if ($actionsonly)
		{
			$simpleactions = array();

			if ($actions->items)
			{
				foreach ($actions->items as $action)
				{
					$simpleactions[] = $action->name;
				}
			}

			$actions->items = $simpleactions;
		}

		return $actions;
	}

	/**
	 * Method to get default settings for group.
	 *
	 * @param   integer $group ID of group.
	 *
	 * @return  array
	 * @since   3.0
	 */
	protected function defaultSettings($group)
	{
		$defaults = array();

		switch ($group)
		{
			case 2:
				$defaults = array(
					'root.1' => array('core.login.site' => 1)
				);
				break;

			case 3:
				$defaults = array(
					'root.1'      => array('core.create' => 1, 'core.edit.own' => 1),
					'com_content' => array('core.create' => 1),
					'com_media'   => array('core.create' => 1)
				);
				break;

			case 4:
				$defaults = array(
					'root.1'      => array('core.edit' => 1),
					'com_content' => array('core.edit' => 1)
				);
				break;

			case 5:
				$defaults = array(
					'root.1'      => array('core.edit.state' => 1),
					'com_content' => array('core.edit.state' => 1),
					'com_media'   => array('core.delete' => 1)
				);
				break;

			case 6:
				$defaults = array(
					'root.1'        => array(
						'core.login.site' => 1, 'core.login.admin' => 1, 'core.login.offline' => 1, 'core.create' => 1,
						'core.delete'     => 1, 'core.edit' => 1, 'core.edit.state' => 1, 'core.edit.own' => 1
					),
					'com_banners'   => array('core.manage' => 1),
					'com_contact'   => array('core.manage' => 1),
					'com_content'   => array('core.manage' => 1),
					'com_media'     => array('core.manage' => 1),
					'com_newsfeeds' => array('core.manage' => 1),
					'com_search'    => array('core.manage' => 1),
					'com_finder'    => array('core.manage' => 1)
				);
				break;

			case 7:
				$defaults = array(
					'root.1'        => array('core.manage' => 1),
					'com_banners'   => array('core.admin' => 1),
					'com_cache'     => array('core.admin' => 1, 'core.manage' => 1),
					'com_checkin'   => array('core.admin' => 1, 'core.manage' => 1),
					'com_contact'   => array('core.admin' => 1),
					'com_content'   => array('core.admin' => 1),
					'com_installer' => array('core.manage' => 0, 'core.delete' => 0, 'core.edit.state' => 0),
					'com_languages' => array('core.admin' => 1),
					'com_media'     => array('core.admin' => 1),
					'com_menus'     => array('core.admin' => 1),
					'com_messages'  => array('core.admin' => 1, 'core.manage' => 1),
					'com_modules'   => array('core.admin' => 1),
					'com_newsfeeds' => array('core.admin' => 1),
					'com_plugins'   => array('core.admin' => 1),
					'com_redirect'  => array('core.admin' => 1),
					'com_search'    => array('core.admin' => 1),
					'com_templates' => array('core.admin' => 1),
					'com_users'     => array('core.admin' => 1),
					'com_finder'    => array('core.admin' => 1)
				);
				break;

			case 8:
				$defaults = array(
					'root.1' => array('core.admin' => 1)
				);
				break;
		}

		return $defaults;
	}

	/**
	 * Get the parent of all groups
	 *
	 * @return  integer
	 * @since   3.0
	 */
	public function getGroupsParent()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query
			->select('id')
			->from($db->quoteName('#__usergroups'))
			->where('parent_id = 0');

		try
		{
			return $db->setQuery($query)->loadResult();
		}
		catch (Exception $e)
		{
			// Sorry, just ignore this. Assume Public = 1 as fallback
			return 1;
		}
	}
}
