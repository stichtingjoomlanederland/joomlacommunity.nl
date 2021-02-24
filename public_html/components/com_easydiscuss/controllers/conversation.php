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

class EasydiscussControllerConversation extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('unarchive', 'archive');
	}

	/**
	 * Determines if conversations are enabled
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isFeatureAvailable()
	{
		if (!$this->config->get('main_conversations')) {
			return false;
		}

		return true;
	}
	
	/**
	 * Allows user to reply to a conversation
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function reply()
	{
		// Check for request forgeries
		ED::checkToken();

		// Ensure that the user is logged in.
		if ($this->my->id <= 0) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}

		// Obtain the message id from post.
		$id	= $this->input->get('id', 0, 'int');

		// Load current conversation
		$conversation = ED::conversation($id);

		// Test for valid message id.
		if (!$conversation->id) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_CONVERSATION_INVALID'));
		}

		// Test if the current user is involved in this conversation.
		if (!$conversation->isInvolved()) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_CONVERSATION_INVALID'));
		}

		// Get message meta here.
		$content = $this->input->get('message', '', 'default');

		if (empty($content)) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_CONVERSATION_EMPTY_CONTENT'));
		}

		$message = $conversation->reply($content);

		$theme = ED::themes();
		$theme->set('message', $message);
		$contents = $theme->output('site/conversations/message');

		$message = JText::_('COM_EASYDISCUSS_CONVERSATION_MESSAGE_SENT_SUCCESSFULLY');

		return $this->ajax->resolve($contents, $message);
	}

	/**
	 * Stores a new conversation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function save()
	{
		// Ensure that the user is logged in
		ED::requireLogin();

		// Check for request forgeries
		ED::checkToken();

		// Get the recipient id
		$recipientId = $this->input->get('recipient', 0, 'int');

		// Default redirection
		$redirect = EDR::_('view=conversation&layout=compose', false);

		// Test for valid recipients.
		if (!$recipientId) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_MESSAGING_INVALID_RECIPIENT'));
			return ED::redirect($redirect);
		}

		// Do not allow user to send a message to himself, it's crazy.
		if ($recipientId == $this->my->id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_CONVERSATION_CANNOT_TALK_TO_SELF'));
			return ED::redirect($redirect);
		}

		// Get message from query.
		$message = $this->input->get('message', '', 'default');

		if (empty($message)) {
			$message = JText::_('COM_EASYDISCUSS_CONVERSATION_EMPTY_MESSAGE');
			
			if ($this->isAjax) {
				return $this->ajax->reject($message);
			}

			ED::setMessage($message);
			return ED::redirect($redirect);
		}

		// Create a new conversation
		$conversation = ED::conversation();
		$conversation->create($this->my->id, $recipientId, $message);

		if ($this->isAjax) {
			return $this->ajax->resolve();
		}

		// Set message queue.
		ED::setMessage(JText::_('COM_EASYDISCUSS_MESSAGE_SENT'));

		$redirect = EDR::_('view=conversation&id=' . $conversation->id, false);
		return ED::redirect($redirect);
	}

	/**
	 * Toggling archives for conversations.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function toggleArchive()
	{
		// Check for request forgeries
		ED::checkToken();

		// Detect the message id.
		$id = $this->input->get('id', 0, 'int');
		$type = $this->input->get('type', 'archive', 'string');

		// Default redirection
		$redirect = EDR::_('view=conversation', false);

		// Load up the conversation
		$conversation = ED::conversation($id);

		if (!$id || !$conversation->id) {
			throw ED::exception('COM_EASYDISCUSS_SYSTEM_INVALID_ID', ED_MSG_ERROR);
		}

		// Test if user has access
		if (!$conversation->canAccess()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'), 'error');
			return ED::redirect($redirect);
		}

		// Archive the conversation now
		$conversation->$type();

		ED::setMessage('COM_EASYDISCUSS_CONVERSATION_IS_NOW_' . strtoupper($type), 'success');

		return ED::redirect($redirect);
	}

	/**
	 * Allows a user to delete a conversation
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function delete()
	{
		// Requires user to be logged in
		ED::requireLogin();

		// Check for request forgeries
		ED::checkToken();

		// Get the conversation object
		$id = $this->input->get('id', 0, 'int');
		$conversation = ED::conversation($id);

		$redirect = EDR::_('view=conversation', false);

		if (!$id || !$conversation->id) {
			throw ED::exception('COM_EASYDISCUSS_SYSTEM_INVALID_ID', ED_MSG_ERROR);
		}

		// Test if user has access
		if (!$conversation->canAccess()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'), 'error');
			return ED::redirect($redirect);
		}

		// Delete the conversation now
		$conversation->delete();

		ED::setMessage('COM_EASYDISCUSS_CONVERSATION_DELETED_SUCCESSFULLY', 'success');
		return ED::redirect($redirect);
	}
}
