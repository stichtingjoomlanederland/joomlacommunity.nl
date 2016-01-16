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

class DiscussConversationParticipant extends JTable
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
	 * The unique user id.
	 * @var int
	 */
	public $user_id			= null;

	/**
	 * Constructor.
	 *
	 * @since	3.0
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_conversations_participants' , 'id' , $db );
	}

}
