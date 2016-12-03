<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

class EasyDiscussControllerRoles extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.roles');

		$this->registerTask('add', 'edit');
		$this->registerTask('savePublishNew', 'save');
	}

	public function save()
	{
		$message = '';
		$type = 'success';
		$task = $this->getTask();

		if (JRequest::getMethod() == 'POST') {
			$post = JRequest::get('post');

			if (empty($post['title'])) {
				$this->app->enqueueMessage(JText::_('COM_EASYDISCUSS_INVALID_ROLES'), 'error');

				$url = 'index.php?option=com_easydiscuss&view=roles';
				return $this->app->redirect(JRoute::_($url, false));
			}

			$post['created_user_id'] = $this->my->id;
			$roleId = $this->input->get('role_id', '', 'var');
			$role = ED::table('Role');

			$role->load($roleId);
			$role->bind($post);

			$role->title = JString::trim($role->title);

			if (!$role->store()) {
				JError::raiseError(500, $role->getError());
			} else {
				$message = JText::_('COM_EASYDISCUSS_ROLE_SAVED');
			}
		} else {
			$message = JText::_('COM_EASYDISCUSS_INVALID_FORM_METHOD');
			$type = 'error';
		}

		ED::setMessage($message, $type);

		if ($task == 'savePublishNew') {
			return $this->app->redirect('index.php?option=com_easydiscuss&view=roles&task=roles.edit');
		}

		$this->app->redirect('index.php?option=com_easydiscuss&view=roles');
	}

	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_easydiscuss&view=roles');
	}

	public function remove()
	{
		$roles = $this->input->get('cid', '', 'POST');
		$message = '';
		$type = 'success';

		if (empty($roles)) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_ROLE_ID');
			$type = 'error';
		} else {
			$table = ED::table('Role');

			foreach($roles as $role) {
				
				$table->load($role);

				if (!$table->delete()) {
					$message = JText::_('COM_EASYDISCUSS_REMOVE_ROLE_ERROR');
					$type = 'error';

					ED::setMessage($message, $type);

					return $this->app->redirect('index.php?option=com_easydiscuss&view=roles');
				}
			}

			$message = JText::_('COM_EASYDISCUSS_ROLE_DELETED');
		}

		ED::setMessage($message, $type);

		$this->app->redirect('index.php?option=com_easydiscuss&view=roles');
	}

	public function publish()
	{
		$roles = $this->input->get('cid', array(0), 'POST');
		$message = '';
		$type = 'success';

		if (count($roles) <= 0){
			$message = JText::_('COM_EASYDISCUSS_INVALID_ROLE_ID');
			$type = 'error';
		} else {
			$model = ED::model('Roles');

			if ($model->publish($roles, 1)) {
				$message = JText::_('COM_EASYDISCUSS_ROLE_PUBLISHED_MSG');
			} else {
				$message = JText::_('COM_EASYDISCUSS_ROLE_PUBLISH_ERROR');
				$type = 'error';
			}
		}

		ED::setMessage($message, $type);

		$this->app->redirect('index.php?option=com_easydiscuss&view=roles');
	}

	public function unpublish()
	{
		$roles = $this->input->get('cid', array(0), 'POST');
		$message = '';
		$type = 'success';

		if (count($roles) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_ROLE_ID');
			$type = 'error';
		} else {
			$model = ED::model('Roles');

			if ($model->publish($roles, 0)) {
				$message = JText::_('COM_EASYDISCUSS_ROLE_UNPUBLISHED');
			} else {
				$message = JText::_('COM_EASYDISCUSS_ROLE_UNPUBLISH_ERROR');
				$type = 'error';
			}
		}

		ED::setMessage($message, $type);

		$this->app->redirect('index.php?option=com_easydiscuss&view=roles');
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

		return $this->app->redirect('index.php?option=com_easydiscuss&view=roles');
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
		return $this->app->redirect('index.php?option=com_easydiscuss&view=roles');
	}
}
