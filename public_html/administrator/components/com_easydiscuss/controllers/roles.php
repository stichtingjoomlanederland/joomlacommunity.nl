<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussControllerRoles extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.users');

		$this->registerTask('add', 'edit');
		$this->registerTask('save', 'save');
		$this->registerTask('apply', 'save');
	}

	public function save()
	{
		$task = $this->getTask();

		$post = $this->input->getArray('post');
		$roleId = $this->input->get('role_id', 0, 'int');
		$isNew = $roleId ? false : true;
		$url = 'index.php?option=com_easydiscuss&view=roles';
		$formUrl = '&layout=form';

		$role = ED::table('Role');
		$role->load($roleId);

		if (empty($post['title'])) {
			ED::setMessage('COM_ED_ROLE_EMPTY_TITLE_MESSAGE', ED_MSG_ERROR);
			$url .= $isNew ? $formUrl : $formUrl .'&id=' . $role->id;

			return ED::redirect($url);
		}

		$excludeId = null;
		$skip = false;
		$model = ED::model('Roles');

		// If user never change user group during editing and the user group does not have any other role as well
		// then we do not need to show the error
		if ($role->id && $post['usergroup_id'] == $role->usergroup_id) {

			$ids = $model->getSelectedUserGroupIds(array('id' => $role->id));

			if (!in_array($post['usergroup_id'], $ids)) {
				$excludeId = $role->usergroup_id;
				$skip = true;
			}
		}

		$ids = $model->getSelectedUserGroupIds(array('usergroup_id' => $excludeId));

		if (!$skip && in_array($post['usergroup_id'], $ids)) {
			ED::setMessage('COM_ED_ROLE_ONE_ROLE_MESSAGE', ED_MSG_ERROR);
			$url .= $isNew ? $formUrl : $formUrl .'&id=' . $role->id;

			return ED::redirect($url);
		}

		$post['description'] = '';
		$post['created_user_id'] = $this->my->id;
		$role->bind($post);

		$role->title = EDJString::trim($role->title);
		$role->created_time = ED::date()->toSql();

		if (!$role->store()) {
			ED::setMessage($role->getError(), ED_MSG_ERROR);
			$url .= $isNew ? $formUrl : $formUrl .'&id=' . $role->id;
			
			return ED::redirect($url);
		}

		ED::setMessage('COM_EASYDISCUSS_ROLE_SAVED', 'success');

		if ($task == 'apply') {
			$url .= $formUrl .'&id=' . $role->id;
		}

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlogMsg = $isNew ? 'COM_ED_ACTIONLOGS_AUTHOR_CREATED_NEW_ROLE' : 'COM_ED_ACTIONLOGS_AUTHOR_UPDATED_ROLE';

		$actionlog->log($actionlogMsg, 'user', array(
			'roleTitle' => $role->title,
			'link' => 'index.php?option=com_easydiscuss&view=roles&layout=form&id=' . $role->id
		));

		ED::redirect($url);
	}

	public function cancel()
	{
		return ED::redirect('index.php?option=com_easydiscuss&view=roles');
	}

	public function remove()
	{
		$roles = $this->input->get('cid', '', 'POST');
		$message = '';
		$type = 'success';
		$redirection = 'index.php?option=com_easydiscuss&view=roles';

		if (empty($roles)) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_ROLE_ID');
			$type = ED_MSG_ERROR;
		} else {
			$table = ED::table('Role');

			// log the current action into database.
			$actionlog = ED::actionlog();

			foreach ($roles as $role) {
				
				$table->load($role);

				$roleTitle = $table->title;

				$state = $table->delete();

				if (!$state) {
					$message = JText::_('COM_EASYDISCUSS_REMOVE_ROLE_ERROR');
					$type = ED_MSG_ERROR;

					ED::setMessage($message, $type);
					return ED::redirect($redirection);
				}

				$actionlog->log('COM_ED_ACTIONLOGS_AUTHOR_DELETED_USER_ROLE', 'user', array(
					'roleTitle' => $roleTitle
				));				
			}

			$message = JText::_('COM_EASYDISCUSS_ROLE_DELETED');
		}

		ED::setMessage($message, $type);
		ED::redirect($redirection);
	}

	public function publish()
	{
		$roles = $this->input->get('cid', array(0), 'POST');
		$message = '';
		$type = 'success';
		$redirection = 'index.php?option=com_easydiscuss&view=roles';

		if (count($roles) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_ROLE_ID');
			$type = ED_MSG_ERROR;

		} else {

			$model = ED::model('Roles');
			$state = $model->publish($roles, 1);

			if (!$state) {
				$message = JText::_('COM_EASYDISCUSS_ROLE_PUBLISH_ERROR');
				$type = ED_MSG_ERROR;

				ED::setMessage($message, $type);
				return ED::redirect($redirection);
			}

			$message = JText::_('COM_EASYDISCUSS_ROLE_PUBLISHED_MSG');
		}

		ED::setMessage($message, $type);
		ED::redirect($redirection);
	}

	public function unpublish()
	{
		$roles = $this->input->get('cid', array(0), 'POST');
		$message = '';
		$type = 'success';
		$redirection = 'index.php?option=com_easydiscuss&view=roles';

		if (count($roles) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_ROLE_ID');
			$type = ED_MSG_ERROR;

		} else {
			$model = ED::model('Roles');
			$state = $model->publish($roles, 0);

			if (!$state) {
				$message = JText::_('COM_EASYDISCUSS_ROLE_UNPUBLISH_ERROR');
				$type = ED_MSG_ERROR;

				ED::setMessage($message, $type);
				return ED::redirect($redirection);
			}

			$message = JText::_('COM_EASYDISCUSS_ROLE_UNPUBLISHED');
		}

		ED::setMessage($message, $type);
		ED::redirect($redirection);
	}

	public function orderdown()
	{
		// Check for request forgeries
		ED::checkToken();

		self::orderRole(1);
	}

	public function orderup()
	{
		// Check for request forgeries
		ED::checkToken();

		self::orderRole(-1);
	}

	public function orderRole($direction)
	{
		// Check for request forgeries
		ED::checkToken();

		// Initialize variables
		$db	= ED::db();
		$cid = $this->input->get('cid', array(), 'post', 'array');

		if (isset($cid[0])) {
			$row = ED::table('Role');
			$row->load( (int) $cid[0] );
			$row->move($direction);
		}

		return ED::redirect('index.php?option=com_easydiscuss&view=roles');
	}

	public function saveOrder()
	{
		// Check for request forgeries
		ED::checkToken();

		$row = ED::table('Role');
		$row->rebuildOrdering();

		$message = JText::_('COM_EASYDISCUSS_ROLES_ORDERING_SAVED');
		$type = 'message';
		ED::setMessage($message, $type);
		return ED::redirect('index.php?option=com_easydiscuss&view=roles');
	}
}
