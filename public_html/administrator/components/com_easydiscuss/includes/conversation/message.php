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

class EasyDiscussConversationMessage extends EasyDiscuss
{
	// This is the DiscussConversation table
	public $table = null;

	public function __construct($item)
	{
		parent::__construct();

		// For object that is being passed in
		if (is_object($item) && !($item instanceof DiscussConversationMessage)) {
			$this->table = ED::table('Conversation');
			$this->table->bind($item);
		}

		// If the object is DiscussConversation, just map the variable back.
		if ($item instanceof DiscussConversationMessage) {
			$this->table = $item;
		}

		// If this is an integer
		if (is_int($item) || is_string($item)) {
			$this->table = ED::table('DiscussConversationMessage');
			$this->table->load($item);
		}
	}

	/**
	 * Retrieves the creator of this message
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getCreator()
	{
		$user = ED::user($this->table->created_by);

		return $user;
	}

	/**
	 * Retrieves the content of the message
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getContent()
	{
		$message = $this->table->message;

		// Parse with bbcode
		$parser = ED::parser();
		$message = $parser->bbcode($message);

		return nl2br($message);
	}

	/**
	 * Determines if this message has attachments
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function hasAttachments()
	{
		return false;
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
		$elapsed = ED::date()->toLapsed($this->table->created);

		if (is_null($elapsed)) {
			return JText::_('COM_ED_CONVERSATION_LAST_TIMELAPSE_JUST_NOW');
		}
		return $elapsed;
	}
}

