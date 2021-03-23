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

jimport('joomla.application.component.controller');

class EasyDiscussControllerPost_types extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.posttypes');
		$this->registerTask('add', 'edit');
		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
		$this->registerTask('apply', 'save');
		$this->registerTask('savepublishnew', 'save');
		$this->registerTask('save2new', 'save');
	}

	public function edit()
	{
		ED::redirect('index.php?option=com_easydiscuss&view=types&layout=form');
	}

	public function cancel()
	{
		return ED::redirect('index.php?option=com_easydiscuss&view=types');
	}

	public function togglePublish()
	{
		ED::checkToken();

		// Get the current task
		$task = $this->getTask();
		$ids = $this->input->get('cid', '', 'array');

		$postTypes = ED::table('Post_types');

		foreach ($ids as $id) {
			$postTypes->load((int) $id);
			$postTypes->$task();
		}

		$message = $task == 'publish' ? JText::_('COM_EASYDISCUSS_POST_TYPES_PUBLISHED') : JText::_('COM_EASYDISCUSS_POST_TYPES_UNPUBLISHED');

		ED::setMessage($message, 'success');
		ED::redirect('index.php?option=com_easydiscuss&view=types');
	}

	public function apply()
	{
		$this->save();
	}

	/**
	 * Saves a post type
	 *
	 * @since	4.0.14
	 * @access	public
	 */
	public function save()
	{
		ED::checkToken();

		$post = $this->input->getArray('post');

		$id = $this->input->get('id', 0, 'int');
		$isNew = $id ? false : true;

		$postTypes = ED::table('Post_types');
		$postTypes->load($id);

		$oldTitle = $postTypes->title;

		// Binds the new data.
		$postTypes->bind($post);

		if (!$postTypes->created) {
			$postTypes->created = ED::date()->toSql();
		}

		if ($postTypes->title != $oldTitle || $oldTitle == '') {
			$postTypes->alias = ED::getAlias($postTypes->title, 'posttypes');
		}

		$postTypes->published = 1;

		// Get the association
		$postTypes->type = $this->input->get('type', 'global', 'word');

		//since we using the alias to join with discuss_posts.post_type, we need to update the value there as well.
		if ($postTypes->store()) {
			$postTypes->updateTopicPostType($oldTitle);
		}

		$categories = $this->input->get('categories', '', 'array');

		// if the association type set to global then we need to reset the categories
		if ($postTypes->type == 'global') {
			$categories = '';
		}

		$model = ED::model('PostTypes');

		// always delete the existing associated category in the post types
		// Create the necessary associations
		$model->createAssociation($postTypes, $categories);

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlogMsg = $isNew ? 'COM_ED_ACTIONLOGS_CREATED_POSTTYPES' : 'COM_ED_ACTIONLOGS_UPDATED_POSTTYPES';

		$actionlog->log($actionlogMsg, 'postTypes', array(
			'link' => 'index.php?option=com_easydiscuss&view=types&layout=form&id=' . $postTypes->id,
			'postTypeTitle' => $postTypes->title
		));

		// Get the current task
		$task = $this->getTask();

		if ($task == 'save2new') {
			$redirect = 'index.php?option=com_easydiscuss&view=types&layout=form';
		} else if ($task == 'apply') {
			$redirect = 'index.php?option=com_easydiscuss&view=types&layout=form&id=' . $postTypes->id;
		} else {
			$redirect = 'index.php?option=com_easydiscuss&view=types';
		}

		$message = !empty($postTypes->id) ? JText::_('COM_EASYDISCUSS_POST_TYPES_UPDATED') : JText::_('COM_EASYDISCUSS_POST_TYPES_CREATED');

		ED::setMessage($message, 'success');
		ED::redirect($redirect);
		$this->app->close();
	}

	/**
	 * Allows deletion of post types
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the categories
		$ids = $this->input->get('cid', '', 'array');

		$redirect = 'index.php?option=com_easydiscuss&view=types';

		$types = ED::table('post_types');

		foreach ($ids as $id) {

			$types->load((int) $id);
			$state = $types->delete();

			if (!$state) {
				ED::setMessage($types->getError(), ED_MSG_ERROR);
				return ED::redirect($redirect);
			}
		}

		ED::setMessage('COM_EASYDISCUSS_CATEGORIES_DELETE_SUCCESS', 'success');

		return ED::redirect($redirect);
	}

	public function saveOrder()
	{
		// Check for request forgeries
		ED::checkToken();

		$model = ED::model('postTypes');
		$model->rebuildOrdering();

		$message = JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_ORDERING_SAVED');

		ED::setMessage($message, 'success');
		return ED::redirect('index.php?option=com_easydiscuss&view=types');
	}

	public function orderdown()
	{
		// Check for request forgeries
		ED::checkToken();

		self::orderType(DISCUSS_ORDER_DOWN);
	}

	public function orderup()
	{
		// Check for request forgeries
		ED::checkToken();

		self::orderType(DISCUSS_ORDER_UP);
	}

	public function orderType($direction)
	{
		// Check for request forgeries
		ED::checkToken();

		$db = ED::db();
		$cid = $this->input->get('cid', array(), 'post', 'array');

		if (isset($cid[0])) {
			$id = (int) $cid[0];
			$types = ED::model('postTypes');
			$types->moveOrder($id, $direction);
		}

		ED::redirect('index.php?option=com_easydiscuss&view=types');
		exit;
	}
}
