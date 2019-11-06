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

class EasyDiscussControllerCategory extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.categories');
		$this->registerTask('add', 'edit');
		$this->registerTask('publish', 'publish');
		$this->registerTask('unpublish', 'unpublish');
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
	}

	public function orderdown()
	{
		// Check for request forgeries
		ED::checkToken();

		EasyDiscussControllerCategory::orderCategory(1);
	}

	public function orderup()
	{
		// Check for request forgeries
		ED::checkToken();

		EasyDiscussControllerCategory::orderCategory(-1);
	}

	public function orderCategory($direction)
	{
		// Check for request forgeries
		ED::checkToken();

		// Initialize variables
		$db = ED::db();
		$cid = $this->input->get('cid', array(), 'post', 'array');

		if (isset($cid[0])) {
			$id = (int) $cid[0];
			$category = ED::category($id);
			$category->move($direction);
		}

		$this->app->redirect('index.php?option=com_easydiscuss&view=categories');
		exit;
	}

	public function saveOrder()
	{
		// Check for request forgeries
		ED::checkToken();

		$model = ED::model('Category');
		$model->rebuildOrdering();

		$message = JText::_('COM_EASYDISCUSS_CATEGORIES_ORDERING_SAVED');

		ED::setMessage($message, 'success');
		return $this->app->redirect('index.php?option=com_easydiscuss&view=categories');
	}

	public function removeAvatar()
	{
		// Check for request forgeries
		ED::checkToken('get');

		$id = $this->input->get('id', 0, 'int');

		$category = ED::category($id);
		$state = $category->deleteAvatar(true);

		ED::setMessage(JText::_('COM_EASYDISCUSS_CATEGORY_AVATAR_REMOVED'), 'success');

		return $this->app->redirect('index.php?option=com_easydiscuss&view=categories&layout=form&id=' . $category->id);
	}

	/**
	 * Triggered when we need to store a new category or save an existing category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		ED::checkToken();

		$message = JText::_('COM_EASYDISCUSS_CATEGORIES_SAVED_SUCCESS');
		$type = 'success';

		// Default redirection URL
		$url = 'index.php?option=com_easydiscuss&view=categories';
		$urlCatForm = 'index.php?option=com_easydiscuss&view=categories&layout=form';

		// Get posted data
		$post = $this->input->getArray('post');

		// If this is an edited category
		$id = $this->input->get('id', 0, 'int');
		$isNew = $id ? false : true;

		// Load the category
		$category = ED::category($id);

		// Ensure that the data posted is valid
		$user = ED::user();
		$post['created_by'] = $user->id;

		// We need to allow description to contain raw codes
		$post['description'] = $this->input->post->get('description', '', 'raw');

		// This determines if we should update the ordering during the save
		$updateOrdering = true;

		if (!$isNew && $category->parent_id == $post['parent_id']) {
			$updateOrdering = false;
		}

		// If this is default category, strict it to publish
		if (!$isNew && $category->default == 1 && $post['published'] == 0) {
			$post['published'] = '1';
			$message = JText::_('COM_EASYDISCUSS_CATEGORY_SAVED_PUBLISH_DEFAULT_CATEGORY');
			$type = 'info';
		}

		// Bind the posted data
		$category->bind($post);

		// Set the category params
		$params = new JRegistry();
		$params->set('show_description', $post['show_description']);
		$params->set('maxlength', $post['maxlength']);
		$params->set('maxlength_size', $post['maxlength_size']);
		$params->set('cat_notify_custom', $post['cat_notify_custom']);
		$params->set('cat_email_parser', $post['cat_email_parser']);
		$params->set('cat_email_parser_password', $post['cat_email_parser_password']);
		$params->set('cat_email_parser_switch', $post['cat_email_parser_switch']);
		$params->set('cat_default_private', $post['cat_default_private']);
		$params->set('cat_enforce_private', $post['cat_enforce_private']);

		// Set the params to category
		$category->set('params', $params->toString());

		// Determine the redirection if the validate is fail
		$validateRedirection = $isNew ? $urlCatForm : $urlCatForm . '&id=' . $category->id;

		// The user might change this to a container category and when that happens, the default category is a container.
		if ($category->default && $category->container) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_UNABLE_TO_SAVE_CATEGORY_AS_CONTAINER'), 'error');
			return $this->app->redirect($validateRedirection);			
		}

		if (!JMailHelper::isEmailAddress($post['cat_email_parser']) && !empty($post['cat_email_parser'])) {
			ED::setMessage(JText::_('COM_ED_UNABLE_TO_SAVE_EMAIL_PARSER'), 'error');
			return $this->app->redirect($validateRedirection);	
		}

		if (!empty($post['cat_notify_custom'])) {

			// We need to check each of the category moderator emails if it is valid
			$customMods = array();
			$customMods = explode(',', $post['cat_notify_custom']);

			foreach ($customMods as $moderator) {

				if (!JMailHelper::isEmailAddress($moderator)) {
					ED::setMessage(JText::_('COM_ED_UNABLE_TO_SAVE_CUSTOM_EMAIL'), 'error');
					return $this->app->redirect($validateRedirection);	
				}
			}
		}
		
		// We need to ensure that the category is valid
		if (!$category->validate()) {
			ED::setMessage($category->getError(), 'error');
			return $this->app->redirect($validateRedirection);
		}

		// Try to save the category
		$state = $category->save($updateOrdering);

		if (!$state) {
			return JError::raiseError(500, $category->getError());
		}

		// Process the avatar
		$file = $this->input->files->get('Filedata', array(), 'array');

		if (!empty($file['name'])) {
			$newAvatar = ED::uploadCategoryAvatar($category, true);
			$category->set('avatar', $newAvatar);
			$category->save();
		}

		// Set the message
		ED::setMessage($message, $type);

		// Build the redirection options based on the task.
		$task = $this->getTask();

		$active = $this->input->get('active', '');

		if ($active) {
			$urlCatForm .= '&active=' . $active;
		}
		
		if ($task == 'save2new') {
			return $this->app->redirect($urlCatForm);
		}

		if ($task == 'apply') {
			return $this->app->redirect($urlCatForm . '&id=' . $category->id);
		}

		return $this->app->redirect($url);
	}

	/**
	 * Allows deletion of category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the categories
		$ids = $this->input->get('cid', '', 'default');

		$redirect = 'index.php?option=com_easydiscuss&view=categories';

		foreach ($ids as $id) {
			$id = (int) $id;

			$category = ED::category($id);

			// Try to delete the category
			$state = $category->delete();

			if (!$state) {
				ED::setMessage($category->getError(), 'error');
				return $this->app->redirect($redirect);
			}
		}

		ED::setMessage('COM_EASYDISCUSS_CATEGORIES_DELETE_SUCCESS', 'success');

		return $this->app->redirect($redirect);
	}

	public function publish()
	{
		// Check for request forgeries
		ED::checkToken();

		$categories = $this->input->get('cid', array(0), 'POST');
		$message = '';
		$type = 'success';

		if (count($categories) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_CATEGORIES_INVALID_CATEGORY');
			$type = 'error';
		} else {

			$model = ED::model('Categories');

			if ($model->publish($categories, 1)) {
				$message = JText::_('COM_EASYDISCUSS_CATEGORIES_PUBLISHED_SUCCESS');
			} else {
				$message = JText::_('COM_EASYDISCUSS_CATEGORIES_PUBLISHED_ERROR');
				$type = 'error';
			}
		}

		ED::setMessage($message, $type);
		return $this->app->redirect('index.php?option=com_easydiscuss&view=categories');
	}

	public function unpublish()
	{
		// Check for request forgeries
		ED::checkToken();

		$categories = $this->input->get('cid', array(0), 'POST');
		$message = '';
		$type = 'success';

		if (count($categories) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_CATEGORIES_INVALID_CATEGORY');
			$type = 'error';
		} else {
			$model = ED::model('Categories');

			if ($model->publish($categories, 0)) {
				$message = JText::_('COM_EASYDISCUSS_CATEGORIES_UNPUBLISHED_SUCCESS');

				// Check if there is default category amongs selected categories
				foreach ($categories as $cat) {
					$category = ED::category($cat);

					if ($category->default) {
						$message = JText::_('COM_EASYDISCUSS_CATEGORY_FAIL_TO_SET_AS_UNPUBLISH_DEFAULT_CATEGORY');
						$type = 'info';
					}
				}

			} else {
				$message = JText::_('COM_EASYDISCUSS_CATEGORIES_UNPUBLISHED_ERROR');
				$type = 'error';
			}
		}

		ED::setMessage($message, $type);
		return $this->app->redirect('index.php?option=com_easydiscuss&view=categories');
	}

	/*
	 * Logic to make a category as default.
	 */
	public function makeDefault()
	{
		$cid = $this->input->get('cid', '', 'var');

		if (is_array($cid)) {
			$cid = (int) $cid[0];
		}

		// Load the category
		$category = ED::category($cid);

		$model = ED::model('Categories');
		$catContainerIds = $model->getCatContainer();

		foreach ($catContainerIds as $catContainerId) {

			// Check whether that category id is it already set as container
			if ($catContainerId->id == $category->id) {
				ED::setMessage(JText::_('COM_EASYDISCUSS_CATEGORY_FAIL_TO_SET_AS_DEFAULT_CATEGORY'), 'error');
				return $this->app->redirect('index.php?option=com_easydiscuss&view=categories');
			}
		}

		// If the category is not published, don't set it as default.
		if (!$category->published) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_CATEGORY_FAIL_TO_SET_AS_DEFAULT_CATEGORY_UNPUBLISHED'), 'error');
			return $this->app->redirect('index.php?option=com_easydiscuss&view=categories');
		}

		$model->updateDefault($cid);

		ED::setMessage(JText::_('COM_EASYDISCUSS_CATEGORY_SET_DEFAULT'), 'success');
		$this->app->redirect('index.php?option=com_easydiscuss&view=categories');
	}
}
