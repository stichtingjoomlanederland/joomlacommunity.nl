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

class EasyDiscussControllerCustomFields extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.customfields');

		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');

		$this->registerTask('required', 'toggleRequired');
		$this->registerTask('optional', 'toggleRequired');

		$this->registerTask('save', 'save');
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
	}

	/**
	 * Saves a custom field
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the posted data
		$post = $this->input->post->getArray();

		// This could be an edited field
		$id = $this->input->get('id', 0, 'int');

		// Determines the active tab
		$active = $this->input->get('active', 'general', 'word');

		// Default redirection url
		$redirect = 'index.php?option=com_easydiscuss&view=customfields&active=' . $active;

		// Bind the posted data with the library
		$field = ED::field($id);
		$field->bind($post);

		$isNew = $id ? false : true;

		// Bind the field options
		$options = $this->input->get('options', '', 'default');
		$field->bindOptions($options);

		// Validation
		if (!$field->validate()) {
			// Set the error
			ED::setMessage($field->getError(), ED_MSG_ERROR);

			return ED::redirect($redirect . '&layout=form');
		}

		// Try to save the field now
		if (!$field->save()) {
			ED::setMessage($field->getError(), ED_MSG_ERROR);
			return ED::redirect($redirect);
		}

		// Build the redirection options based on the task.
		$task = $this->getTask();

		if ($task == 'apply') {
			$redirect .= '&layout=form&id=' . $field->id;
		}

		if ($task == 'save2new') {
			$redirect .= '&layout=form';
		}

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionString = $isNew ? 'COM_ED_ACTIONLOGS_CREATED_CUSTOMFIELD' : 'COM_ED_ACTIONLOGS_UPDATED_CUSTOMFIELD';

		$actionlog->log($actionString, 'field', array(
			'link' => 'index.php?option=com_easydiscuss&view=customfields&layout=form&id=' . $field->id,
			'fieldTitle' => $field->title
		));

		// Set the message
		ED::setMessage('COM_EASYDISCUSS_CUSTOMFIELDS_SAVED', 'success');

		return ED::redirect($redirect);
	}

	/**
	 * Deletes a custom field
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the list of fields to be deleted
		$ids = $this->input->get('cid', '', 'array');

		$redirect = 'index.php?option=com_easydiscuss&view=customfields';
		
		foreach ($ids as $id) {
			$id = (int) $id;
			
			$field = ED::field($id);
			$state = $field->delete();

			if (!$state) {
				ED::setMessage($field->getError(), ED_MSG_ERROR);

				return ED::redirect($redirect);
			}
		}

		// Set the message
		ED::setMessage('COM_EASYDISCUSS_CUSTOMFIELDS_DELETED', 'success');

		ED::redirect($redirect);
	}

	/**
	 * Toggles a field publish state
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function togglePublish()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the current task
		$task = $this->getTask();

		$redirect = 'index.php?option=com_easydiscuss&view=customfields';

		// Get the ids
		$ids = $this->input->get('cid', array(), 'array');	

		if (!$ids) {
			ED::setMessage('COM_EASYDISCUSS_INVALID_CUSTOMFIELDS_ID');
			return ED::redirect($redirect);
		}

		foreach ($ids as $id) {
			$field = ED::field($id);

			$field->$task();
		}

		$message = 'COM_EASYDISCUSS_CUSTOMFIELDS_PUBLISHED_SUCCESS';

		if ($task == 'unpublish') {
			$message = 'COM_EASYDISCUSS_CUSTOMFIELDS_UNPUBLISHED';
		}

		ED::setMessage($message, 'success');
		
		return ED::redirect($redirect);
	}

	/**
	 * Toggles a field's required state
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function toggleRequired()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the current task
		$task = $this->getTask();

		$redirect = 'index.php?option=com_easydiscuss&view=customfields';

		// Get the ids
		$ids = $this->input->get('cid', array(), 'array');	

		if (!$ids) {
			ED::setMessage('COM_EASYDISCUSS_INVALID_CUSTOMFIELDS_ID');
			return ED::redirect($redirect);
		}

		foreach ($ids as $id) {
			$field = ED::field($id);

			$field->$task();
		}

		$message = 'COM_EASYDISCUSS_CUSTOMFIELDS_PUBLISHED_SUCCESS';

		if ($task == 'optional') {
			$message = 'COM_EASYDISCUSS_CUSTOMFIELDS_OPTIONAL_SUCCESS';
		}

		ED::setMessage($message, 'success');
		
		return ED::redirect($redirect);
	}

	/**
	 * Allows re-ordering of custom field
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function reorder()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the direction
		$task = $this->getTask();

		// Get the direction
		$direction = $task == 'orderup' ? 'up' : 'down';

		// Initialize variables
		$cid = $this->input->get('cid', array(), 'array');

		// Get the field id
		$id = (int) $cid[0];

		$field = ED::field($cid[0]);
		$field->move($direction);

		return ED::redirect('index.php?option=com_easydiscuss&view=customfields');
	}

	public function saveOrder()
	{
		// Check for request forgeries
		ED::checkToken();
		$row = ED::table('CustomFields');
		$row->rebuildOrdering();

		$message = JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_ORDERING_SAVED');
		$type = 'message';

		ED::setMessage($message, $type);

		return ED::redirect('index.php?option=com_easydiscuss&view=customfields');
	}
}
