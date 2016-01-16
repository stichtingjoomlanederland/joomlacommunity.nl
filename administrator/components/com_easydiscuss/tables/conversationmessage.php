<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

class DiscussConversationMessage extends JTable
{
	/**
	 * The unique id for the current record
	 * @var int
	 */
	public $id			= null;

	/**
	 * The unique id for the conversation.
	 * @var int
	 */
	public $conversation_id			= null;

	/**
	 * The content for the message.
	 * @var int
	 */
	public $message			= null;

	/**
	 * The creation date of the message.
	 * @var datetime
	 */
	public $created		= null;

	/**
	 * Creator's user id.
	 * @var int
	 */
	public $created_by	= null;

	/**
	 * The last replied date for this message.
	 * @var datetime
	 */
	public $lastreplied	= null;

	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_conversations_message' , 'id' , $db );
	}
}
