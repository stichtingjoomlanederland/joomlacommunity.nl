<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2020 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

/**
 * PWT ACL Wizard Model
 *
 * @since   3.0
 */
class PwtAclModelWizard extends ListModel
{
	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 * @since   3.0
	 */
	public function getForm($loadData = true)
	{
		$form = $this->loadForm(
			'com_pwtacl.wizard',
			'wizard',
			[
				'control'   => 'jform',
				'load_data' => $loadData
			]
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   3.0
	 * @throws  Exception
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		return Factory::getApplication()->getUserState('com_pwtacl.wizard', []);
	}

	/**
	 * Setup the permissions for a group
	 *
	 * @param   array  $data  Wizard data
	 *
	 * @return  integer $groupId
	 * @since   3.0
	 * @throws  Exception
	 */
	public function groupSetup($data)
	{
		// Group ID
		$groupId = $data['groupid'];

		// Create new group
		if ((int) $data['new'] === 1)
		{
			$groupId = $this->createGroup($data['grouptitle']);
		}

		// Get the parent of all groups
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pwtacl/models/', 'PwtAclModel');

		/** @var PwtAclModelAssets $assetsModel */
		$assetsModel = BaseDatabaseModel::getInstance('Assets', 'PwtAclModel');

		// Clear Permissions first
		$assetsModel->clear($groupId);

		// Reset Access after clearing group to prevent cached results
		Access::clearStatics();

		// Set login site action
		if ((int) $data['core.login.site'] === 1)
		{
			$assetsModel->saveAction('root.1', 'core.login.site', $groupId, 1);
		}

		// Set login admin action
		if ((int) $data['core.login.admin'] === 1)
		{
			$assetsModel->saveAction('root.1', 'core.login.admin', $groupId, 1);
		}

		// Set login offline action
		if ((int) $data['core.login.offline'] === 1)
		{
			$assetsModel->saveAction('root.1', 'core.login.offline', $groupId, 1);
		}

		// Set component manage actions
		foreach ($data['core.manage'] as $component)
		{
			$assetsModel->saveAction($component, 'core.manage', $groupId, 1);
		}

		return $groupId;
	}

	/**
	 * Method to create a new User Group
	 *
	 * @param   string  $title  Title of group
	 *
	 * @return  integer new Group ID
	 * @since   3.0
	 */
	protected function createGroup($title)
	{
		// Get the parent of all groups
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pwtacl/models/', 'PwtAclModel');

		/** @var PwtAclModelAssets $assetsModel */
		$assetsModel = BaseDatabaseModel::getInstance('Assets', 'PwtAclModel');

		// Get parent of all groups
		$rootGroup = $assetsModel->getGroupsParent();

		// Get the Groups model
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models/', 'UsersModel');

		/** @var UsersModelGroup $groupModel */
		$groupModel = BaseDatabaseModel::getInstance('Group', 'UsersModel');

		// The new group data
		$newGroup = [
			'title'     => $title,
			'parent_id' => $rootGroup,
			'id'        => 0
		];

		// Save group
		$groupModel->save($newGroup);

		// Get the group ID of the new group
		return (int) $groupModel->getState('group.id');
	}

	/**
	 * Method to get an array of assets.
	 *
	 * @return  stdClass Components with assets
	 * @since   3.0
	 * @throws  Exception
	 */
	public function getItems()
	{
		// Get Group ID
		$group = Factory::getApplication()->input->getInt('group');

		// Return if we don't have a group
		if (!$group)
		{
			return new stdClass;
		}

		// Get components set in wizard
		$components = Factory::getSession()->get('components', null, 'pwtacl');

		// Prepare where query
		$where = null;

		// We need the component and category assets for the components
		foreach ($components as $component)
		{
			$where .= $this->_db->quoteName('name') . ' = ' . $this->_db->quote($component) . ' OR ';
			$where .= $this->_db->quoteName('name') . ' LIKE ' . $this->_db->quote($component . '.category.%') . ' OR ';
		}

		// Remove last OR
		$where = rtrim($where, ' OR');

		// Get Assets Model
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pwtacl/models/', 'PwtAclModel');

		/** @var PwtAclModelAssets $assetsModel */
		$assetsModel = BaseDatabaseModel::getInstance('Assets', 'PwtAclModel');

		// Get the assets
		$assets = $assetsModel->prepareAssets($assetsModel->getAssets($where, 'name'), 'group', $group, null);

		// Grouped by components
		$components = new stdClass;

		// Prepare assets for wizard view
		foreach ($assets as $asset)
		{
			// Check the actions for the asset
			$actions = json_decode($asset->rules);

			// Remove actions not displayed
			unset(
				$actions->{'core.admin'},
				$actions->{'core.options'},
				$actions->{'core.manage'},
				$asset->actions->core->{'core.admin'},
				$asset->actions->core->{'core.options'},
				$asset->actions->core->{'core.manage'}
			);

			// Correct level for this view
			--$asset->level;

			// Prepare components array
			$components->{$asset->component}->{'assets'}[] = $asset;
			$components->{$asset->component}->{'title'}    = $assets[$asset->component]->title;
		}

		return $components;
	}
}
