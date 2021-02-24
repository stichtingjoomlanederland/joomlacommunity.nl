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

class EasyDiscussControllerPriorities extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.priorities');
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
	}

	/**
	 * remove priorities
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		ED::checkToken();

		$ids = $this->input->get('cid', '', 'array');
		$redirect = 'index.php?option=com_easydiscuss&view=priorities';

		if (empty($ids)) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID'), ED_MSG_ERROR);
			return ED::redirect($redirect);
		}

		foreach ($ids as $id) {

			$table = ED::table('Priority');
			$table->load($id);

			$state = $table->delete();

			if (!$state) {
				$message = JText::sprintf('COM_EASYDISCUSS_PRIORITIES_DELETE_FAILED', $table->title);
				ED::setMessage($message, ED_MSG_ERROR);
				
				return ED::redirect($redirect);
			}
		}

		// Display message
		ED::setMessage('COM_EASYDISCUSS_PRIORITY_DELETE_SUCCESSFULLY', 'success');
		return ED::redirect($redirect);
	}

	/**
	 * Saves a new priority
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		ED::checkToken();

		$id = $this->input->get('id', 0, 'int');
		$isNew = $id ? false : true;		

		$priority = ED::table('Priority');
		$priority->load($id);

		$redirect = 'index.php?option=com_easydiscuss&view=priorities';

		// Get the task
		$task = $this->getTask();

		// Bind the data from post
		$post = $this->input->post->getArray();

		// validation
		if (!isset($post['title']) || trim($post['title']) == "") {

			$message = JText::_('COM_EASYDISCUSS_PRIORITY_TITLE_CANNOT_BE_EMPTY');
			ED::setMessage($message, ED_MSG_ERROR);

			$redirect = 'index.php?option=com_easydiscuss&view=priorities&layout=form';
			
			if ($priority->id) {
				$redirect .= '&id=' . $priority->id;
			}

			return ED::redirect($redirect);
		}

		$priority->bind($post);

		if (!$id) {
			$priority->created = ED::date()->toSql();
		}

		// Save the priority
		$priority->store();

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlogMsg = $isNew ? 'COM_ED_ACTIONLOGS_CREATED_PRIORITIES' : 'COM_ED_ACTIONLOGS_UPDATED_PRIORITIES';

		$actionlog->log($actionlogMsg, 'priority', array(
			'link' => 'index.php?option=com_easydiscuss&view=priorities&layout=form&id=' . $priority->id,
			'priorityTitle' => $priority->title
		));

		if ($task == 'save2new') {
			$redirect = 'index.php?option=com_easydiscuss&view=priorities&layout=form';
		}

		if ($task == 'apply') {
			$redirect = 'index.php?option=com_easydiscuss&view=priorities&layout=form&id=' . $priority->id;
		}

		// Display message
		ED::setMessage('COM_EASYDISCUSS_PRIORITY_SAVED_SUCCESSFULLY', 'success');

		return ED::redirect($redirect);
	}
}
