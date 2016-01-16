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

class EasyDiscussModelConversation extends EasyDiscussModel
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
	 * Method to get a pagination object for the posts
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		return $this->_pagination;
	}

	/**
	 * Adds a list of recipients that can see a particular message
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique conversation id.
	 * @param	int		The unique message id.
	 * @param	int 	The unique recipient id.
	 * @param	int		The unique creator id.
	 */
	public function addMessageMap( $conversationId , $messageId , $recipientId , $creator )
	{
		$db			= DiscussHelper::getDBO();

		// Add record for recipient
		$map 					= DiscussHelper::getTable( 'ConversationMap' );
		$map->user_id			= $recipientId;
		$map->conversation_id 	= $conversationId;
		$map->message_id		= $messageId;
		$map->isread 			= DISCUSS_CONVERSATION_UNREAD;
		$map->state 			= DISCUSS_CONVERSATION_PUBLISHED;
		$map->store();

		// Add a record for the creator.
		$map 					= DiscussHelper::getTable( 'ConversationMap' );
		$map->user_id			= $creator;
		$map->conversation_id 	= $conversationId;
		$map->message_id		= $messageId;
		$map->isread 			= DISCUSS_CONVERSATION_READ;
		$map->state 			= DISCUSS_CONVERSATION_PUBLISHED;
		$map->store();

		return true;
	}

	/**
	 * Adds a participant into a conversation
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The conversation id.
	 * @param	int 	The unique id of the user
	 */
	public function addParticipant( $conversationId , $participantId , $creatorId )
	{
		$db		= DiscussHelper::getDBO();;
		$ids 	= array();

		// Add recipient.
		$participant 	= DiscussHelper::getTable( 'ConversationParticipant' );
		$participant->conversation_id 	= $conversationId;
		$participant->user_id 			= $participantId;
		$participant->store();

		// Add creator.
		$participant 	= DiscussHelper::getTable( 'ConversationParticipant' );
		$participant->conversation_id 	= $conversationId;
		$participant->user_id 			= $creatorId;
		$participant->store();

		return true;
	}

	/**
	 * Determines if the conversation is new for the particular node.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique conversation id.
	 * @param	int 	The unique user id.
	 * @return	boolean
	 */
	public function isNew( $conversationId , $userId )
	{
		$db		= DiscussHelper::getDBO();;

		$query 	= 'SELECT COUNT(1) '
				. 'FROM ' . $db->nameQuote( '#__discuss_conversations' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message' ) . ' AS b '
				. 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'conversation_id' ) . ' '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' AS c '
				. 'ON c.' . $db->nameQuote( 'message_id' ) . ' = b.' . $db->nameQuote( 'id' ) . ' '
				. 'AND c.' . $db->nameQuote( 'isread' ) . '=' . $db->Quote( 0 ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'id' ) . ' = ' . $db->Quote( $conversationId ) . ' '
				. 'AND c.' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId ) . ' '
				. 'GROUP BY a.' . $db->nameQuote( 'id' );
		$db->setQuery( $query );

		$isNew 	= $db->loadResult() > 0;

		return $isNew;
	}

	/**
	 * Toggle a conversation read state.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique conversation id.
	 * @param	int		The unique user id.
	 * @param	int 	The read state
	 */
	public function toggleRead( $conversationId , $userId , $state )
	{
		$db			= DiscussHelper::getDBO();;
		$query		= 'UPDATE ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' '
					. 'SET ' . $db->nameQuote( 'isread' ) . ' = ' . $db->Quote( $state ) . ' '
					. 'WHERE ' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId ) . ' '
					. 'AND ' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );
		$db->setQuery( $query );
		$db->Query();

		return true;
	}
	/**
	 * Mark a conversation to old.
	 *
	 * @since	3.0
	 * @access	public
	 * @return	boolean
	 * @param	int $conversationId
	 * @param	int $userId
	 */
	public function markAsRead( $conversationId , $userId )
	{
		return $this->toggleRead( $conversationId , $userId , DISCUSS_CONVERSATION_READ );
	}

	/**
	 * Mark a conversation to new.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int $conversationId
	 * @param	int $userId
	 *
	 * @return	boolean
	 */
	public function markAsUnread( $conversationId , $userId )
	{
		return $this->toggleRead( $conversationId , $userId , DISCUSS_CONVERSATION_UNREAD );
	}

	/**
	 * Archiving a conversation simply means modifying the state :)
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int $conversationId
	 * @param	int $nodeId
	 * @return	boolean
	 */
	public function archive( $conversationId , $userId , $state = DISCUSS_CONVERSATION_ARCHIVED )
	{
		$db 	= DiscussHelper::getDBO();

		$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' '
				. 'SET ' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( $state ) . ' '
				. 'WHERE ' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId ) . ' '
				. 'AND ' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );
		$db->setQuery( $query );
		$db->Query();

		return true;
	}
	/**
	 * Remove the child message mapping for the particular node.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique conversation id.
	 * @param	int 	The unique user id which owns the message mapping.
	 * @return	boolean
	 */
	public function delete( $conversationId , $userId )
	{
		$db			= DiscussHelper::getDBO();
		$query		= array();

		// Delete the conversation items
		$query[]	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_conversations_message_maps' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId );
		$query[]	= 'AND ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );
		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );
		$db->Query();


		// @rule: Check if this is the last child item. If it is the last, we should delete everything else.
		$query	= 'SELECT COUNT(DISTINCT( c.' . $db->nameQuote( 'user_id' ) . ')) '
				. 'FROM ' . $db->nameQuote( '#__discuss_conversations' ) . ' AS a '
				. 'INNER JOIN '. $db->nameQuote( '#__discuss_conversations_message' ) . ' AS b '
				. 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'conversation_id' ) . ' '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' AS c '
				. 'ON b.' . $db->nameQuote( 'id' ) . ' = c.' . $db->nameQuote( 'message_id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'id' ) . ' = ' . $db->Quote( $conversationId ) . ' '
				. 'AND c.' . $db->nameQuote( 'user_id' ) . ' != ' . $db->Quote( $userId ) . ' '
				. 'GROUP BY a.' . $db->nameQuote( 'id' );
		$db->setQuery( $query );
		$total	= $db->loadResult();

		if( $total <= 0 )
		{
			return $this->cleanup();
		}
	}

	/**
	 * Completely removes the conversation from the site.
	 *
	 * @return	boolean
	 * @param	int $conversationId
	 */
	private function cleanup( $conversationId )
	{
		$db		= DiscussHelper::getDBO();

		// @rule: Delete conversation first
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_conversations' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . ' = ' . $db->Quote( $conversationId );
		$db->setQuery( $query );
		$db->Query();

		// @rule: Delete messages for the conversation.
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_conversations_message' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId );
		$db->setQuery( $query );
		$db->Query();

		// @rule: Delete messages mapping for the conversation.
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId );
		$db->setQuery( $query );
		$db->Query();

		// @rule: Delete participants for the conversation.
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_conversations_participants' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId );
		$db->setQuery( $query );
		$db->Query();

		return true;
	}

	/**
	 * Checks whether or not the node id has any access to the conversation.
	 *
	 * @return	boolean
	 * @param	int $conversationId
	 * @param	int $userId
	 */
	public function hasAccess( $conversationId , $userId )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_conversations_participants' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId ) . ' '
				. 'AND ' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );
		$db->setQuery( $query );

		return ( $db->loadResult() > 0 );
	}

	/**
	 * Retrieves a list of users who are participating in a conversation.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		$conversationId		The unique id of that conversation
	 * @param	array	$excludeUsers		Exlude a list of nodes
	 *
	 * @return	array	An array that contains SocialUser objects.
	 */
	public function getParticipants( $conversationId , $currentUserId = null )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT DISTINCT( a.' . $db->nameQuote( 'user_id' ) . ') FROM ' . $db->nameQuote( '#__discuss_conversations_participants' ) . ' AS a '
				. 'WHERE a.' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId );

		if( !is_null( $currentUserId ) )
		{
			$query 	.= ' AND a.' . $db->nameQuote( 'user_id' ) . '!=' . $db->Quote( $currentUserId );
		}

		$db->setQuery( $query );
		$participants	= $db->loadResultArray();

		return $participants;
	}

	/**
	 * Retrieves a list of messages in a particular conversation
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique id of that conversation
	 * @param	int		The current user id of the viewer.
	 *
	 */
	public function getMessages( $conversationId , $userId, $viewAll = false, $count = false )
	{
		$db		= DiscussHelper::getDBO();
		$config = DiscussHelper::getConfig();

		$operation  = '( UNIX_TIMESTAMP( \'' . DiscussHelper::getDate()->toMySQL() . '\' ) - UNIX_TIMESTAMP( a.`created`) )';

		$query	= 'SELECT a.* ';
		$query  .= ', FLOOR( ' . $operation. ' / 60 / 60 / 24) AS daydiff '
				. 'FROM ' . $db->nameQuote( '#__discuss_conversations_message' ) . ' AS a '
				. 'LEFT JOIN ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' AS b '
				. 'ON b.' . $db->nameQuote( 'message_id' ) . ' = a.' . $db->nameQuote( 'id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId ) . ' '
				. 'AND b.' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );

		// @rule: Messages ordering.
		// @TODO: respect ordering settings.
		$query	.= 'ORDER BY a.' . $db->nameQuote( 'created' ) . ' ASC';


		// By default show the latest messages limit by the numbers specified in backend
		if( !$viewAll )
		{
			$query 	.= ' LIMIT ' . $config->get('main_messages_limit', 5);
		}

		// If view == 'all', do nothing because we wanted to show all messages.

		if( $viewAll == 'previous' )
		{
			$count = $config->get('main_messages_limit', 5) + $count;
			// View another 5 more previous messages
			$query 	.= ' LIMIT ' . $count;
		}

		$db->setQuery( $query );

		$rows			= $db->loadObjectList();
		$messages		= array();

		foreach( $rows as $row )
		{
			$message 	= DiscussHelper::getTable( 'ConversationMessage' );
			$message->bind( $row );
			$message->daydiff   = $row->daydiff;
			$messages[]	= $message;
		}
		return $messages;
	}

	/**
	 * Retrieves a total number of conversations for a particular user
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The current user id of the viewer
	 */
	public function getCount( $userId , $options = array() )
	{
		$db			= DiscussHelper::getDBO();

		$query		= array();
		$query[]	= 'SELECT COUNT(1) FROM(';
		$query[]	= 'SELECT a.' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__discuss_conversations' ) . ' AS a';
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message' ) . ' AS b';
		$query[]	= 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'conversation_id' );
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' AS c';
		$query[]	= 'ON c.' . $db->nameQuote( 'message_id' ) . ' = b.' . $db->nameQuote( 'id' );
		$query[]	= 'WHERE c.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );

		// @rule: Respect filter options
		if( isset( $options['filter'] ) )
		{
			switch( $options[ 'filter' ] )
			{
				case 'unread':
					$query[]	= 'AND c.' . $db->nameQuote( 'isread' ) . '=' . $db->Quote( DISCUSS_CONVERSATION_UNREAD );
				break;
			}
		}

		// @rule: Process any additional filters here.
		if( isset( $options[ 'archives' ] ) && $options[ 'archives' ] )
		{
			$query[]	= ' AND c.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( DISCUSS_CONVERSATION_ARCHIVED );
		}
		else
		{
			$query[]	= ' AND c.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( DISCUSS_CONVERSATION_PUBLISHED );
		}

		$query[]	= 'GROUP BY a.' . $db->nameQuote( 'id' );

		$query[]	= ') AS x';

		// Join back query with a proper glue.
		$query		= implode( ' ' , $query );

		$db->setQuery( $query );
		$total 		= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves a list of conversations for a particular node
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The current user id of the viewer
	 */
	public function getConversations( $userId , $options = array() )
	{
		$db		= DiscussHelper::getDBO();;
		$query	= 'SELECT a.*,b.' . $db->nameQuote( 'message' ) . ',c.' . $db->nameQuote( 'isread' )
				. 'FROM ' . $db->nameQuote( '#__discuss_conversations' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message' ) . ' AS b '
				. 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'conversation_id' ) . ' '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' AS c '
				. 'ON c.' . $db->nameQuote( 'message_id' ) . ' = b.' . $db->nameQuote( 'id' ) . ' '
				. 'WHERE c.' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );

		// @rule: Process any additional filters here.
		if( isset( $options[ 'archives' ] ) && $options[ 'archives' ] )
		{
			$query	.= ' AND c.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( DISCUSS_CONVERSATION_ARCHIVED );
		}
		else
		{
			$query	.= ' AND c.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( DISCUSS_CONVERSATION_PUBLISHED );
		}

		// @rule: Respect filter options
		if( isset( $options['filter'] ) )
		{
			switch( $options[ 'filter' ] )
			{
				case 'unread':
					$query	.= ' AND c.' . $db->nameQuote( 'isread' ) . '=' . $db->Quote( DISCUSS_CONVERSATION_UNREAD );
				break;
			}
		}

		$query		.= ' GROUP BY b.' . $db->nameQuote( 'conversation_id' );

		$sorting 	= isset( $options[ 'sorting' ] ) ? $options[ 'sorting' ] : 'latest';

		switch( $sorting )
		{
			case 'latest':
			default:
				$query	.= ' ORDER BY a.' . $db->nameQuote( 'lastreplied' ) . ' DESC';
			break;
		}


		// If limit is provided, only show certain number of items.
		if( isset( $options[ 'limit' ] ) )
		{
			$limit 	= $options[ 'limit' ];
			$query 	.= ' LIMIT 0,' . $limit;
		}
		else
		{
			$limitstart 	= $this->getState( 'limitstart' );
			$limit 			= $this->getState( 'limit' );
			$paginationQuery	= str_ireplace( 'SELECT a.*,b.' . $db->nameQuote( 'message' ) . ',c.' . $db->nameQuote( 'isread' ) , 'SELECT COUNT(1) AS count FROM ( SELECT a.* ' , $query );
			$paginationQuery 	.= ') AS x';

			$db->setQuery( $paginationQuery );
			$total 				= $db->loadResult();

			$this->_pagination	= DiscussHelper::getPagination( $total , $limitstart , $limit );

			$query 			.= ' LIMIT ' . $limitstart . ' , ' . $limit;

		}

		$db->setQuery( $query );
		$rows	= $db->loadObjectList();

		if( !$rows )
		{
			return $rows;
		}

		foreach( $rows as $row )
		{
			$conversation 	= DiscussHelper::getTable( 'Conversation' );
			$conversation->bind( $row );

			$conversations[]	= $conversation;
		}
		return $conversations;
	}

	/**
	 * Inserts a new reply into an existing conversation.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		$conversationId		The conversation id.
	 * @param	string	$message			The content of the reply.
	 * @param 	int 	$creatorId			The user's id.
	 *
	 * @return	SocialTableConversationMessage	The message object
	 */
	public function insertReply( $conversationId , $content , $creatorId )
	{
		$conversation 	= DiscussHelper::getTable( 'Conversation' );
		$conversation->load( $conversationId );

		// Store the new message first.
		$message 		= DiscussHelper::getTable( 'ConversationMessage' );
		$message->conversation_id 	= $conversationId;
		$message->message 			= $content;
		$message->created_by	 	= $creatorId;
		$message->created 			= DiscussHelper::getDate()->toMySQL();
		$message->store();

		// Since a new message is added, add the visibility of this new message to the participants.
		$users 	= $this->getParticipants( $conversation->id );

		foreach( $users as $userId )
		{
			$map 	= DiscussHelper::getTable( 'ConversationMap' );
			$map->user_id 			= $userId;
			$map->conversation_id	= $conversation->id;
			$map->state 			= DISCUSS_CONVERSATION_PUBLISHED;
			$map->isread 			= $userId == $creatorId ? DISCUSS_CONVERSATION_READ : DISCUSS_CONVERSATION_UNREAD;
			$map->message_id 		= $message->id;
			$map->store();
		}

		// Update the last replied date.
		$conversation->lastreplied 	= DiscussHelper::getDate()->toMySQL();
		$conversation->store();

		return $message;
	}
}
