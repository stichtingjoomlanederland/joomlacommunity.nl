<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewRoles extends EasyDiscussAdminView
{
	/**
	 * Renders a list of user roles
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.roles');

		// Page attributes
		$this->title('COM_EASYDISCUSS_ROLES');

		JToolbarHelper::addNew();
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

		// Filtering
		$filter = $this->getUserState('roles.filter_state', 'filter_state', '*', 'word');

		// Search
		$search = $this->getUserState('roles.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		// Ordering
		$order = $this->getUserState('roles.filter_order', 'filter_order', 'a.ordering', 'cmd');
		$orderDirection = $this->getUserState('roles.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		// Retrieve the roles
		$model = ED::model('Roles', true);
		$roles = $model->getData();
		$pagination = $model->getPagination();

		$this->set('roles', $roles);
		$this->set('pagination', $pagination);
		$this->set('filter', $filter);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('roles/default');
	}

	/**
	 * Displays the form for roles
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function form($tpl = null)
	{
		$this->checkAccess('discuss.manage.roles');

		$id = $this->input->get('id', 0, 'int');

		$role = ED::table('Role');
		$role->load($id);

		$this->title('COM_EASYDISCUSS_EDITING_ROLE');

		// Set default values for new entries.
		if (!$role->id) {
			$this->title('COM_EASYDISCUSS_ADD_NEW_ROLE');

			$date = ED::date();

			$role->created_time	= $date->toSql();
			$role->published = true;
		} else {
			$role->created_time = ED::date()->toSql($role->created_time);
		}

		JToolbarHelper::apply('save');
		JToolbarHelper::save();
		JToolBarHelper::divider();
		JToolBarHelper::cancel();

		// Get a list of user groups
		$userGroups = ED::getJoomlaUserGroups();

		// Use a simple hashmap for groups
		$groups = array();

		foreach ($userGroups as $group) {
			$groups[$group->id] = $group->name;
		}

		// Create the available colors
		$colors = array(
					'success' => 'Green', // Green
					'warning' => 'Orange', // Orange
					'danger' => 'Red', // Red
					'info' => 'Blue', // Blue
					'default' => 'Black' // black
				);

		$this->set('groups', $groups);
		$this->set('role', $role);
		$this->set('colors', $colors);

		parent::display('roles/form');
	}
}
