<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/message.php');

class EasyDiscussConversation extends EasyDiscuss
{
	// This is the DiscussConversation table
	public $table = null;

	public $message = null;

	public function __construct($item)
	{
		parent::__construct();

		$this->table = ED::table('Conversation');

		// For object that is being passed in
		if (is_object($item) && !($item instanceof DiscussConversation)) {
			$this->table->bind($item);

			if (isset($item->message)) {
				$this->message = $item->message;
			}
		}

		// If the object is DiscussConversation, just map the variable back.
		if ($item instanceof DiscussConversation) {
			$this->table = $item;
		}

		// If this is an integer
		if (is_int($item) || is_string($item)) {
			$this->table->load($item);
		}
	}

	/**
	 * Magic method to get properties which don't exist on this object but on the table
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function __get($key)
	{
		if (isset($this->table->$key)) {
			return $this->table->$key;
		}

		if (isset($this->$key)) {
			return $this->$key;
		}

		return $this->table->$key;
	}

	/**
	 * Archives a conversation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function archive($userId = null)
	{
		$userId = JFactory::getUser($userId)->id;

		// // Try to archive / unarchive the conversation.
		// $state = $task == 'archive' ?  : DISCUSS_CONVERSATION_PUBLISHED;
		// $model->archive($id, $this->my->id, $state);

		$model = ED::model('Conversation');

		$state = $model->archive($this->table->id, $userId, DISCUSS_CONVERSATION_ARCHIVED);

		return $state;
	}


	/**
	 * Determines if the current viewer can view this conversation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canAccess($userId = null)
	{
		$userId = JFactory::getUser($userId)->id;

		$model = ED::model('Conversation');
		$canAccess = $model->hasAccess($this->table->id, $userId);

		return $canAccess;
	}

	/**
	 * Gets a new message object
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMessage($obj, $truncate = false)
	{
		$message = new EasyDiscussConversationMessage($obj);

		return $message;
	}

	/**
	 * Retrieves a list of messages in a particular conversation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMessages()
	{
		$model = ED::model("Conversation");
		$rows = $model->getMessages($this->table->id, $this->my->id, true);
		$messages = array();

		foreach ($rows as $row) {
			$messages[] = $this->getMessage($row);
		}

		return $messages;
	}

	/**
	 * Method to retrieve messages that are created by this user only
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function getUserMessagesOnly($userId = false)
	{
		if (!$userId) {
			$userId = $this->table->created_by;
		}

		$model = ED::model('conversation');
		$results = $model->getMessages($this->table->id, $userId, true, false, true);
		$messages = array();

		foreach ($results as $row) {
			$messages[] = $this->getMessage($row);
		}

		return $messages;
	}

	/**
	 * Retrieves the last replier of the message
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLastReplier()
	{
		static $users = array();

		$key = $this->table->id;

		if (!isset($users[$key])) {
			$model = ED::model('Conversation');
			$users[$key] = $model->getLastReplier($this->table->id, $this->my->id);
		}

		return $users[$key];
	}

	/**
	 * Retrieves the latest message in this conversation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLastMessage($userId = null, $intro = true)
	{
		static $messages = array();

		if (is_null($userId)) {
			$userId = JFactory::getUser($userId)->id;
		}

		$key = $this->table->id . $userId . $intro;

		if (!isset($messages[$key])) {

			$model = ED::model('Conversation');
			$contents = $model->getLastMessage($this->table->id, $userId);

			// We need to parse the message
			$contents = ED::parser()->bbcode($contents);

			if ($intro) {
				$contents = JString::substr(strip_tags($contents), 0, 35) . JText::_('COM_EASYDISCUSS_ELLIPSES');
			}

			$messages[$key] = $contents;
		}

		return $messages[$key];
	}

	/**
	 * Get's the target recipient
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getParticipant()
	{
		$model = ED::model('Conversation');

		$participantId = $model->getParticipants($this->table->id, $this->my->id);

		$participant = ED::user($participantId);
		$participant = $participant[0];

		return $participant;
	}


	/**
	 * Determines if the current conversation has been read.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	int	$userId		The user's id.
	 * @return	boolean			True if it's new, false otherwise.
	 */
	public function isNew($userId = null)
	{
		if (is_null($userId)) {
			$userId = ED::user($userId)->id;
		}

		$model = ED::model('Conversation');
		$isNew = $model->isNew($this->table->id, $userId);

		return $isNew;
	}

	/**
	 * Determines if the user is involved in this conversation
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int 	The user's id to check against.
	 * @return	bool	True if user is involved in the conversation.
	 *
	 */
	public function isInvolved($userId = null)
	{
		if (is_null($userId)) {
			$userId = JFactory::getUser($userId)->id;
		}

		$model = ED::model('Conversation');
		$result = $model->getParticipants($this->table->id);

		return in_array($userId, $result);
	}

	/**
	 * Allows caller to remove a conversation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete($userId = null)
	{
		$userId = JFactory::getUser($userId)->id;

		// Retrieve model
		$model = ED::model('Conversation');

		// Delete the user mapping for the messages in a conversation
		$state = $model->deleteMapping($this->table->id, $userId);

		return $state;
	}

	/**
	 * Sets a conversation as unread
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unread($userId = null)
	{
		$userId = JFactory::getUser($userId)->id;

		// Mark the conversation as unread.
		$model = ED::model('Conversation');
		$model->markAsUnread($this->table->id, $userId);

		return true;
	}

	/**
	 * Saves a new conversation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function create($authorId, $recipientId, $message)
	{
		// Check if this conversation already exist in the system.
		$exists = $this->table->loadByRelation($authorId, $recipientId);

		$currentDate = ED::date()->toSql();

		// If the conversation between author and recipient doesn't exist yet, we need to create a new conversation
		if (!$exists) {
			$this->table->created = $currentDate;
			$this->table->created_by = $authorId;
		}

		$this->table->lastreplied = $currentDate;
		$this->table->store();

		// Create a new message now
		$message = $this->createMessage($authorId, $message);

		// Add participant to this conversation.
		$model = ED::model('Conversation');

		if (! $exists) {
			$model->addParticipant($this->table->id, $recipientId, $authorId);
		}

		// Add message map so that recipient can view the message.
		$model->addMessageMap($this->table->id ,$message->id, $recipientId, $authorId);

		// Add notification for recipient to let them know they received a message.
		ED::conversation()->notify($message);

		// @TODO: Add points for user.

		// @TODO: Add badge for user.


	}

	/**
	 * Create a new messsage record
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createMessage($authorId, $message)
	{
		// Create a new message
		$table = ED::table('ConversationMessage');
		$table->message = $message;
		$table->conversation_id = $this->table->id;
		$table->created = ED::date()->toSql();
		$table->created_by = $authorId;
		$table->store();

		return $table;
	}

	/**
	 * Allows caller to insert a reply to this conversation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	EasyDiscussConversationMessage
	 */
	public function reply($contents)
	{
		$model = ED::model('Conversation');
		$result = $model->insertReply($this->table->id, $contents, $this->my->id);

		// Send notification to the recipient.
		$this->notify($result);

		$reply = $this->getMessage($result);

		return $reply;
	}

	/**
	 * Sets this conversation as read
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setRead($userId)
	{
		$model = ED::model('Conversation');
		return $model->markAsRead($this->table->id, $userId);
	}

	/**
	 * Get the elapsed time of a conversation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getElapsedTime()
	{
		$elapsed = ED::date()->toLapsed($this->table->lastreplied);

		return $elapsed;
	}

	/**
	 * Retrieves the permalink of the conversation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPermalink($xhtml = true)
	{
		$url = 'view=conversation&id=' . $this->table->id;
		$url = EDR::_($url, $xhtml);

		return $url;
	}

	/**
	 * Notify the user when a new conversation is started or replied.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function notify(DiscussConversationMessage $message)
	{
		$author = ED::user($message->created_by);

		$model = ED::model('Conversation');
		$result	= $model->getParticipants($message->conversation_id, $message->created_by);

		$recipient = ED::user($result[0]);

		$emailData = array();
		$emailData['conversationLink'] = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=conversation&id=' . $message->id, false, true);
		$emailData['authorName'] = $author->getName();
		$emailData['authorAvatar'] = $author->getAvatar();

		$content = ED::parser()->replaceMentions($message->message);
		$emailData['content'] = $content;

		$subject = JText::sprintf( 'COM_EASYDISCUSS_CONVERSATION_EMAIL_SUBJECT', $author->getName());

		$notification = ED::getNotification();
		$notification->addQueue($recipient->user->email, $subject, '', 'email.conversation.reply', $emailData);
	}
}

