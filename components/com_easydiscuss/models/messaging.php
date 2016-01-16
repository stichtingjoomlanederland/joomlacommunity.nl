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

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelMessaging extends EasyDiscussModel
{
	/**
	 * Category total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	var $_data = null;

	function __construct()
	{
		parent::__construct();


		$mainframe	= JFactory::getApplication();

		$limit		= ($mainframe->getCfg('list_limit') == 0) ? 5 : DiscussHelper::getListLimit();
		$limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Delete's a message from the system.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int 	The unique message id.
	 * @param	int		The unique user id.
	 */
	public function delete( $messageId , $userId )
	{
		$db 		= DiscussHelper::getDBO();
		$query 		= array();

		// Try to detect how many deletion occured.
		$query[]	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_messages_states' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'deleted' ) . '=' . $db->Quote( 1 );
		$query[]	= 'AND ' . $db->nameQuote( 'message_id' ) . '=' . $db->Quote( $messageId );

		$query		= implode( ' ' , $query );
		$db->setQuery( $query );
		$total 		= $db->loadResult();

		// If nothing has been deleted before, we need to update the deletion part.
		if( $total == 0 )
		{
			// Just mark the message as deleted.
			$query 		= array();
			$query[]	= 'UPDATE ' . $db->nameQuote( '#__discuss_messages_states' );
			$query[]	= 'SET ' . $db->nameQuote( 'deleted' ) . '=' . $db->Quote( 1 );
			$query[]	= ',' . $db->nameQuote( 'deleted_time' ) . '=' . $db->Quote( DiscussHelper::getDate()->toMySQL() );
			$query[]	= 'WHERE ' . $db->nameQuote( 'message_id' ) . '=' . $db->Quote( $messageId );
			$query[]	= 'AND ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );

			$query 		= implode( ' ' , $query );
			$db->setQuery( $query );
			$db->Query();

			return true;
		}

		// If there is already 1 record, it means we need to delete the entire message.
		// First, we need to delete the replies.
		$this->deleteMeta( $messageId );

		// Delete the states
		$this->deleteMessageStates( $messageId );

		// Delete the message
		$this->deleteMessage( $messageId );

		return true;
	}

	/**
	 * Reply existing message.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	DiscussMessage		The message object
	 * @param	string				The message content.
	 * @param	int 				The unique user id.
	 */
	public function reply( DiscussMessage $message , $content , $userId )
	{
		// Update all records in {#__discuss_messages_states} to ensure that `deleted` column is false.
		$db 			= DiscussHelper::getDBO();
		$query 			= array();
		$query[]		= 'UPDATE ' . $db->nameQuote( '#__discuss_messages_states' );
		$query[]		= 'SET ' . $db->nameQuote( 'deleted' ) . '=' . $db->Quote( 0 );
		$query[]		= 'WHERE ' . $db->nameQuote( 'message_id' ) . '=' . $db->Quote( $message->id );
		$query 			= implode( ' ' , $query );
		$db->setQuery( $query );
		$db->Query();


		// Store the message meta.
		$meta 		= DiscussHelper::getTable( 'MessageMeta' );
		$meta->message_id	= $message->id;
		$meta->message 		= $content;
		$meta->created		= DiscussHelper::getDate()->toMySQL();
		$meta->created_by	= $userId;
		$meta->isparent 	= false;
		$meta->store();

		return $meta;
	}

	/**
	 * Delete message.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique message id.
	 * @return	bool	True if success and false otherwise.
	 */
	public function deleteMessage( $messageId )
	{
		$db 		= DiscussHelper::getDBO();

		$query		= array();
		$query[]	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_messages' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'id' ) . ' = ' . $db->Quote( $messageId );

		$query 		= implode( ' ' , $query );
		$db->setQuery( $query );
		$db->Query();

		return true;
	}

	/**
	 * Delete all message states with the provided message id.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique message id.
	 * @return	bool	True if success and false otherwise.
	 */
	public function deleteMessageStates( $messageId )
	{
		$db 		= DiscussHelper::getDBO();

		$query		= array();
		$query[]	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_messages_states' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'message_id' ) . ' = ' . $db->Quote( $messageId );

		$query 		= implode( ' ' , $query );
		$db->setQuery( $query );
		$db->Query();

		return true;
	}

	/**
	 * Delete all message meta with the provided message id.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique message id.
	 * @return	bool	True if success and false otherwise.
	 */
	public function deleteMeta( $messageId )
	{
		$db 		= DiscussHelper::getDBO();

		$query		= array();
		$query[]	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_messages_meta' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'message_id' ) . ' = ' . $db->Quote( $messageId );

		$query 		= implode( ' ' , $query );
		$db->setQuery( $query );
		$db->Query();

		return true;
	}

	/**
	 * Determines if the provided user id is a participant of a conversation.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique user id.
	 * @param	int		The unique message id.
	 * @return	bool	True if user is a participant, false otherwise.
	 */
	public function isParticipant( $userId , $messageId )
	{
		$db 		= DiscussHelper::getDBO();

		$query 		= array();
		$query[]	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_messages_states' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );
		$query[]	= 'AND ' . $db->nameQuote( 'message_id' ) . '=' . $db->Quote( $messageId );

		$query 		= implode( ' ' , $query );
		$db->setQuery( $query );

		$exist 		= $db->loadResult();

		return $exist;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		return $this->_pagination;
	}

	/**
	 * Initializes the state records for a single message.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	DiscussMessage
	 */
	public function initStates( DiscussMessage $message )
	{
		if( !$message->created_by || !$message->recipient )
		{
			return false;
		}

		// Store creator's state
		$state 	= DiscussHelper::getTable( 'MessageState' );
		$state->message_id 	= $message->id;
		$state->user_id 	= $message->created_by;

		// Creator's read state is always marked as is read.
		$state->isread		= DISCUSS_MESSAGING_READ;
		$state->store();

		// Store recipient state
		$state 	= DiscussHelper::getTable( 'MessageState' );
		$state->message_id 	= $message->id;
		$state->user_id 	= $message->recipient;
		$state->store();

		return true;
	}

	/**
	 * Retrieves a list of messages.
	 *
	 * @since	3.0
	 * @param	array 	An array of options.
	 */
	public function getMessages( $options = array() )
	{
		$db			= DiscussHelper::getDBO();

		$query		= array();

		$query[]	= 'SELECT a.*, b.`title` AS `title` , b.`message` AS `message` FROM ' . $db->nameQuote( '#__discuss_messages' ) . ' AS a';
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__discuss_messages_meta' ) . ' AS b';
		$query[]	= 'ON b.' . $db->nameQuote( 'message_id' ) . ' = a.' . $db->nameQuote( 'id' );
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__discuss_messages_states' ) . ' AS c';
		$query[]	= 'ON c.' . $db->nameQuote( 'message_id' ) . ' = a.' . $db->nameQuote( 'id' );

		if( isset( $options[ 'user_id' ] ) )
		{
			$userId 	= $options[ 'user_id' ];
			$query[]	= 'AND c.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );
		}


		$query[]	= 'WHERE 1';

		// If user's id is provided, we need to only retrieve messages for a particular user.
		if( isset( $options[ 'user_id' ] ) )
		{
			$userId 	= $options[ 'user_id' ];
			$query[]	= 'AND(';
			$query[]	= 'a.' . $db->nameQuote( 'recipient' ) . '=' . $db->Quote( $userId );
			$query[]	= 'OR';
			$query[]	= 'b.' . $db->nameQuote( 'created_by' ) . '!=' . $db->Quote( $userId );
			$query[]	= ')';

			$query[]	= 'AND c.' . $db->nameQuote( 'deleted' ) . '=' . $db->Quote( 0 );
		}

		$query[]	= 'GROUP BY a.' . $db->nameQuote( 'id' );

		// Join back with space glue
		$query	= implode( ' ', $query );

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		return $result;
	}

	/**
	 * Gets the main message of a conversation.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique messaging id.
	 * @return	DiscussMessage
	 */
	public function getReplies( $messageId , DiscussMessageState $state )
	{
		// Load the first initial message tied to the message object.
		$db 	= DiscussHelper::getDBO();

		$query		= array();
		$query[] 	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_messages_meta' ) . ' AS a';
		$query[]	= 'WHERE a.' . $db->nameQuote( 'message_id' ) . '=' . $db->Quote( $messageId );
		$query[]	= 'AND a.' . $db->nameQuote( 'isparent' ) . '=' . $db->Quote( 0 );

		// If there's a `deleted_time` state, we need to only fetch records that are created after the deletion time.
		if( $state->deleted_time != '0000-00-00 00:00:00' )
		{
			$query[]	= 'AND a.' . $db->nameQuote( 'created' ) . '>' . $db->Quote( $state->deleted_time );
		}

		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );

		$result 	= $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		$replies 	= array();

		foreach( $result as $row )
		{
			$meta	= DiscussHelper::getTable( 'MessageMeta' );
			$meta->bind( $row );

			$replies[]	= $meta;
		}

		return $replies;
	}

	/**
	 * Gets the main message of a conversation.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique messaging id.
	 * @return	DiscussMessage
	 */
	public function getMessage( $messageId )
	{
		// Load the first initial message tied to the message object.
		$db 	= DiscussHelper::getDBO();

		$query		= array();

		$query[] 	= 'SELECT a.* FROM ' . $db->nameQuote( '#__discuss_messages_meta' ) . ' AS a';
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__discuss_messages_states' ) . ' AS b';
		$query[]	= 'ON a.' . $db->nameQuote( 'message_id' ) . ' = b.' . $db->nameQuote( 'message_id' );
		$query[]	= 'WHERE a.' . $db->nameQuote( 'message_id' ) . '=' . $db->Quote( $messageId );
		$query[]	= 'AND b.' . $db->nameQuote( 'deleted' ) . '=' . $db->Quote( 0 );
		$query[]	= 'AND a.' . $db->nameQuote( 'isparent' ) . '=' . $db->Quote( 1 );
		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );

		$result 	= $db->loadObject();

		$messageMeta 	= DiscussHelper::getTable( 'MessageMeta' );
		$messageMeta->bind( $result );

		return $messageMeta;
	}

	/**
	 * Retrieves the total number of new messages for a user.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The user's unique id.
	 * @return	int		The total number of new messages.
	 */
	public function getNewMessagesCount( $userId )
	{
		$db 		= DiscussHelper::getDBO();
		$query		= array();

		$query[]	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_messages' ) . ' AS a';
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__discuss_messages_states' ) . ' AS b';
		$query[]	= 'ON b.' . $db->nameQuote( 'message_id' ) . ' = a.' . $db->nameQuote( 'id' );
		$query[]	= 'WHERE b.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );
		$query[]	= 'AND b.' . $db->nameQuote( 'isread' ) . '=' . $db->Quote( DISCUSS_MESSAGING_UNREAD );
		$query[]	= 'AND b.' . $db->nameQuote( 'deleted' ) . '=' . $db->Quote( 0 );

		$query		= implode( ' ' , $query );
		$db->setQuery( $query );

		$count 		= $db->loadResult();

		return $count;
	}

	/**
	 * Marks a specified message as read.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The message id.
	 * @param	int		The unique user id.
	 */
	public function markUnRead( $messageId , $userId )
	{
		return $this->markRead( $messageId , $userId , DISCUSS_MESSAGING_UNREAD );
	}

	/**
	 * Marks a specified message as read.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The message id.
	 * @param	int		The unique user id.
	 */
	public function markRead( $messageId , $userId , $state = DISCUSS_MESSAGING_READ )
	{
		$db 		= DiscussHelper::getDBO();
		$query		= array();
		$query[]	= 'UPDATE ' . $db->nameQuote( '#__discuss_messages_states' );
		$query[]	= 'SET ' . $db->nameQuote( 'isread' ) . '=' . $db->Quote( $state );
		$query[]	= 'WHERE ' . $db->nameQuote( 'message_id' ) . '=' . $db->Quote( $messageId );
		$query[]	= 'AND ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );

		$query 		= implode( ' ' , $query );
		$db->setQuery( $query );
		$db->Query();
	}
}