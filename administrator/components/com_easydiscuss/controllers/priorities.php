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
	 * Saves a new priority
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save()
	{
		// Check for request forgeries
		ED::checkToken();

		$id = $this->input->get('id', 0, 'int');

		$priority = ED::table('Priority');
		$priority->load($id);

		$redirect = 'index.php?option=com_easydiscuss&view=priorities';

		// Get the task
		$task = $this->getTask();

		// Bind the data from post
		$post = JRequest::get('post');
		$post = $this->input->getArray('post');

		// var_dump($post);exit;

		// validation
		if (! isset($post['title']) || trim($post['title']) == "") {

			$message = JText::_('COM_EASYDISCUSS_PRIORITY_TITLE_CANNOT_BE_EMPTY');
			ED::setMessage($message, 'error');

			$redirect = 'index.php?option=com_easydiscuss&view=priorities&layout=form';
			if ($priority->id) {
				$redirect .= '&id=' . $priority->id;
			}

			return $this->app->redirect($redirect);
		}

		$priority->bind($post);

		if (!$id) {
			$priority->created = ED::date()->toSql();
		}

		// Save the priority
		$priority->store();

		if ($task == 'save2new') {
			$redirect = 'index.php?option=com_easydiscuss&view=priorities&layout=form';
		}


		if ($task == 'apply') {
			$redirect = 'index.php?option=com_easydiscuss&view=priorities&layout=form&id=' . $priority->id;
		}


		// Display message
		ED::setMessage('COM_EASYDISCUSS_PRIORITY_SAVED_SUCCESSFULLY', 'success');

		return $this->app->redirect($redirect);
	}
}
