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
	 * Determines if conversations are enabled
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function isFeatureAvailable()
	{
		if (!$this->config->get('main_conversations')) {
			return false;
		}

		return true;
	}

	/**
	 * Renders the conversation layout
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function display($tpl = null)
	{
		// Do not allow non logged in users to view anything in conversation.
		ED::requireLogin();

		// Check if the user has permission to use private message
		if (!$this->acl->allowed('allow_privatemessage')) {
			ED::setMessage('COM_EASYDISCUSS_NOT_ALLOWED_POST_PRIVATE_MESSAGE', 'error');
			return $this->app->redirect(EDR::_('index.php?option=com_easydiscuss&view=index', false));
		}

		// If configured to use EasySocial conversations, redirect them to the correct page.
		$easysocial = ED::easysocial();

        if ($easysocial->exists() && $this->config->get('integration_easysocial_messaging')) {
        	$this->app->redirect($easysocial->getConversationsRoute(false));
        	return;
        }

		// Set page attributes
		ED::setPageTitle('COM_EASYDISCUSS_CONVERSATIONS_TITLE');

		// Get the conversation type
		$type = $this->input->get('type', '', 'word');

		$options = array();

		if ($type == 'archives') {
			$options['archives'] = true;
		}

		// Retrieve a list of conversations
		$model = ED::model('Conversation');
		$lists = $model->getConversations($this->my->id, $options);

		// Get the active conversation
		$id = $this->input->get('id', 0, 'int');
		$activeConversation = null;

		// If there was an id, we know the user wants to view an active conversation
		if ($id) {
			$conversation = ED::conversation($id);

			if ($conversation->id) {
				$activeConversation = $conversation;
			}
		}

		// If there is no id provided, we load up the first item
		if (!$activeConversation && $lists && count($lists) > 0) {
			$activeConversation = $lists[0];
		}

		// Mark the discussion as read since it is already opened
		if ($activeConversation) {
			$activeConversation->setRead($this->my->id);
		}

		$pagination = $model->getPagination();

		$countInbox = $model->getCount($this->my->id);
		$countArchives = $model->getCount($this->my->id, array('archives' => true));

		$this->set('type', $type);
		$this->set('activeConversation', $activeConversation);
		$this->set('lists', $lists);
		$this->set('pagination', $pagination);
		$this->set('countInbox', $countInbox);
		$this->set('countArchives', $countArchives);

		parent::display('conversations/default');
	}

	/**
	 * Displays the single conversation page.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function read()
	{
		$id = $this->input->get('id', 0, 'int');
		
		// Do not allow not logged in users to view anything in conversation.
		if (!$this->my->id) {
			$returnURL = base64_encode(JRequest::getURI());
			$this->app->redirect(ED::getLoginLink($returnURL));
			return $this->app->close();
		}

		// Try to load the conversation
		$conversation = ED::table('Conversation');
		$state = $conversation->load($id);

		// The conversation id needs to be valid.
		if (!$state) {
			ED::setMessageQueue(JText::_('COM_EASYDISCUSS_CONVERSATION_INVALID'), 'error');
			return $this->app->redirect(EDR::_('index.php?option=com_easydiscuss&view=index', false));
		}
		
		// Check if the current logged in user has access to this conversation.
		$model = ED::model('Conversation');

		if (!$model->hasAccess($conversation->id, $this->my->id)) {
			ED::setMessageQueue(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'), 'error');
			return $this->app->redirect(EDR::_('index.php?option=com_easydiscuss&view=index', false));
		}
		
		$result	= $model->getParticipants($conversation->id, $this->my->id);
		$user = ED::profile();
		$user->load($result[0]);

		ED::setPageTitle(JText::sprintf( 'COM_EASYDISCUSS_VIEW_CONVERSATION_TITLE', $this->escape($user->getName())));



		// Check if it is view all messages
		$show = $this->input->get('show');
		$count = $this->input->get('count', 0, 'int');


		if ($show == 'all') {
			// For future use
			$count = '';
		}

		if ($show == 'previous') {
			// Check if the value is integer, we do no want any weird values
			if (isset($count) && is_int($count)) {
				// Convert to absolute number
				$count = abs($count);
			}
		}

		// Get replies in the conversation
		$replies = $model->getMessages($conversation->id, $this->my->id, $show, $count);

		// Format conversation replies.
		$model->formatConversationReplies($replies);

		// Format the conversation object.
		$data = array($conversation);
		$model->formatConversations($data);

		// To retrieve previous messages
		$count += $this->config->get('main_messages_limit', 5);

		$this->set('replies', $replies);
		$this->set('conversation', $data[0]);
		$this->set('count', $count);
		$this->set('show', $show);

		parent::display('conversations/conversation.read');
	}

	/**
	 * Responsible to display the conversation form.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function compose()
	{
		// Do not allow non logged in users to view anything in conversation.
		if (!$this->my->id) {
			ED::setMessageQueue(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'), 'error');
			return $this->app->redirect(EDR::_( 'index.php?option=com_easydiscuss&view=index', false));
			return $this->app->close();
		}


		parent::display('conversations/compose');
	}
}
