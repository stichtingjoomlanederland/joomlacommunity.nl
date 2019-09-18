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
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewConversation extends EasyDiscussView
{
	/**
	 * Retrieves the list of conversations
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getConversations()
	{
		// Require user to be logged in
		ED::requireLogin();

		// Get the conversation type
		$type = $this->input->get('type', '', 'word');

		$options = array();

		if ($type == 'archives') {
			$options['archives'] = true;
		}

		// Retrieve a list of conversations
		$model = ED::model('Conversation');
		$lists = $model->getConversations($this->my->id, $options);

		$activeConversation = null;

		// Get the first list as active conversation
		if ($lists && count($lists) > 0) {
			$activeConversation = $lists[0];
		}

		// Mark the discussion as read since it is already opened
		if ($activeConversation) {
			$activeConversation->setRead($this->my->id);
		}

		$theme = ED::themes();
		$theme->set('lists', $lists);
		$theme->set('activeConversation', $activeConversation);

		$lists = $theme->output('site/conversations/default.lists');


		return $this->ajax->resolve($lists);
	}

	/**
	 * Retrieves the contents of a conversation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getConversation()
	{
		// Require user to be logged in
		ED::requireLogin();

		// Get the conversation
		$id = $this->input->get('id', 0, 'int');
		$conversation = ED::conversation($id);

		$messages = $conversation->getMessages();
		$contents = '';

		foreach ($messages as $message) {
			// Get the contents
			$theme = ED::themes();
			$theme->set('message', $message);
			$contents .= $theme->output('site/conversations/message');
		}

		$title = $conversation->getParticipant()->getName();

		return $this->ajax->resolve($title, $contents);
	}

	/**
	 * Renders the popbox for the toolbar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function popbox()
	{
		// Require user to be logged in
		ED::requireLogin();

		// Check if this feature has been enabled.
		if (!$this->config->get('main_conversations_notification') || !$this->config->get('main_conversations')) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}

		// Show only x amount of items
		// Get a list of conversations to be displayed in the drop down.
		$model = ED::model('Conversation');
		$conversations = $model->getConversations($this->my->id, array('limit' => $this->config->get('main_conversations_notification_items')));

		$theme = ED::themes();
		$theme->set('conversations', $conversations);

		$output = $theme->output('site/conversations/popbox');

		return $this->ajax->resolve($output);
	}

	/**
	 * Returns the number of new messages for a particular user.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function count()
	{
		if ($this->my->guest) {
			return $this->ajax->fail('COM_EASYDISCUSS_NOT_ALLOWED');
		}

		$model = ED::model('Conversation');
		$count = $model->getCount($this->my->id, array('filter' => 'unread'));

		return $this->ajax->resolve($count);
	}

	/**
	 * Displays a confirmation dialog before deleting a conversation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function confirmDelete()
	{
		// Check if the user is
		if ($this->my->id <= 0) {
			return $this->ajax->fail('COM_EASYDISCUSS_NOT_ALLOWED');
		}

		// Get the post id from the REQUEST data
		$id = $this->input->get('id', 0, 'int');

		$theme = ED::themes();

		$theme->set('id', $id);
		$contents = $theme->output('site/conversations/dialogs/delete');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Confirm deletion of a message.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function confirmArchive()
	{
		// Ensure that the user is logged in.
		ED::requireLogin();

		// Get the conversation id from the REQUEST data
		$id = $this->input->get('id', 0, 'int');

		$theme = ED::themes();
		$theme->set('id', $id);
		$contents = $theme->output('site/conversations/dialogs/archive');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Displays the conversation composer
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function compose()
	{
		// Ensure that the user is logged in
		ED::requireLogin();

		if (!$this->config->get('main_conversations')) {
			return JError::raiseError(500);
		}

		// Get the recipient id
		$userId = $this->input->get('id', 0, 'int');
		$previousContents = $this->input->get('contents', '', 'raw');

		// Do not allow guests to access here.
		if (!$this->my->id || !$userId) {
			return $this->ajax->fail('COM_EASYDISCUSS_NOT_ALLOWED');
		}

		$theme = ED::themes();
		$recipient = ED::profile($userId);

		$theme->set('recipient', $recipient);
		$theme->set('previousContents', $previousContents);
		$contents = $theme->output('site/conversations/dialogs/compose');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Displays the message sent confirmation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function sent()
	{
		// Ensure that the user is logged in
		ED::requireLogin();

		if (!$this->config->get('main_conversations')) {
			return JError::raiseError(500);
		}

		$userId = $this->input->get('id', 0, 'int');

		$theme = ED::themes();
		$recipient = ED::profile($userId);

		$theme->set('recipient', $recipient);
		$contents = $theme->output('site/conversations/dialogs/sent');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Marks conversation as unread
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function unread()
	{
		ED::requireLogin();
		ED::checkToken();


		// Get the conversation object
		$id = $this->input->get('id', 0, 'int');
		$conversation = ED::conversation($id);

		if (!$id || !$conversation->id) {
			return JError::raiseError(500, JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'));
		}

		// Test if user has access
		if (!$conversation->canAccess()) {
			return JError::raiseError(500, JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}

		// Mark it as unread
		$conversation->unread();

		return $this->ajax->resolve();
	}

	/**
	 * Marks conversation as unread
	 *
	 * @since	4.1.8
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function read()
	{
		ED::requireLogin();
		ED::checkToken();

		// Get the conversation item
		$id = $this->input->get('id', 0, 'int');
		
		$conversation = ED::conversation($id);

		if (!$id || !$conversation->id) {
			return JError::raiseError(500, JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'));
		}

		// Test if user has the access for it
		if (!$conversation->canAccess()) {
			return JError::raiseError(500, JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}

		// Mark it as read
		$conversation->setRead($this->my->id);

		return $this->ajax->resolve();
	}
}
