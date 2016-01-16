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

class DiscussConversation extends JTable
{
	/**
	 * The unique id for the conversation.
	 * @var int
	 */
	public $id			= null;

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
		parent::__construct( '#__discuss_conversations' , 'id' , $db );
	}

	/**
	 * Loads a conversation record based on the existing conversations.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		$creator	The node id of the creator.
	 * @param	int		$recipient	The node id of the recipient.
	 */
	public function loadByRelation( $creator , $recipient )
	{
		$db 		= DiscussHelper::getDBO();
		$query 		= array();
		$query[]	= 'SELECT COUNT(1) AS `related` , a.*';
		$query[]	= 'FROM ' . $db->nameQuote( '#__discuss_conversations' ) . ' AS a';
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_participants' ) . ' AS b';
		$query[]	= 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'conversation_id' );
		$query[]	= 'WHERE';
		$query[]	= '( b.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $recipient ) . ' OR b.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $creator ) . ' )';
		$query[]	= 'GROUP BY b.' . $db->nameQuote( 'conversation_id' );
		$query[]	= 'HAVING COUNT( b.' . $db->nameQuote( 'conversation_id' ) . ') > 1';
		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );

		$data	= $db->loadObject();

		if( !isset( $data->related ) )
		{
			return false;
		}

		if( $data->related >= 2 )
		{
			return parent::bind( $data );
		}
		return false;
	}

	/**
	 * Retrieves a list of participants in this conversation.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array (Optional)	$exclusions		An array of user's node id that should be excluded.
	 */
	public function getParticipants( $exclusions = false )
	{
		$model 	= DiscussHelper::getModel( 'Conversation' );
		$result	= $model->getParticipants( $this->id , $exclusions );

		if( !is_array( $result ) )
		{
			$result 	= array( $result );
		}

		return $result;
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
	public function isInvolved( $id )
	{
		$model 	= DiscussHelper::getModel( 'Conversation' );
		$result	= $model->getParticipants( $this->id );

		return in_array( $id , $result );
	}

	/**
	 * Alias method to mark a conversation as read.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The user's id.
	 */
	public function markAsRead( $userId )
	{
		$model 	= DiscussHelper::getModel( 'Conversation' );
		return $model->markAsRead( $this->id , $userId );
	}

	/**
	 * Determines if the current conversation has been read.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int	$userId		The user's id.
	 * @return	boolean			True if it's new, false otherwise.
	 */
	public function isNew( $userId )
	{
		$model 	= DiscussHelper::getModel( 'Conversation' );
		return $model->isNew( $this->id , $userId );
	}

	/**
	 * Notify the user when a new conversation is started or replied.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	DiscussConversationMessage	The message object that is formatted
	 *
	 */
	public function notify( DiscussConversationMessage $message )
	{
		$author 		= DiscussHelper::getTable( 'Profile' );
		$author->load( $message->created_by );

		$model 	= DiscussHelper::getModel( 'Conversation' );
		$result	= $model->getParticipants( $this->id , $message->created_by );

		$recipient 	= DiscussHelper::getTable( 'Profile' );
		$recipient->load( $result[ 0 ] );

		$emailData						= array();
		$emailData['conversationLink']	= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=conversation&layout=read&id=' . $this->id, false, true);
		$emailData['authorName']		= $author->getName();
		$emailData['authorAvatar']		= $author->getAvatar();
		$emailData['content']			= $message->message;

		$subject 						= JText::sprintf( 'COM_EASYDISCUSS_CONVERSATION_EMAIL_SUBJECT' , $author->getName() );

		$notification 	= DiscussHelper::getNotification();
		$notification->addQueue( $recipient->user->email , $subject , '', 'email.conversation.reply.php' , $emailData );
	}

	/**
	 * Retrieves the last message for this specific conversation.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The current user id.
	 * @return	string	The last message for this conversation
	 */
	public function getLastMessage( $userId )
	{
		static $messages	= array();

		if( !isset( $messages[ $this->id . $userId ] ) )
		{
			$db 	= DiscussHelper::getDBO();
			$query	= 'SELECT b.`message` FROM ' . $db->nameQuote( '#__discuss_conversations' ) . ' AS a '
					. 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message' ) . ' AS b '
					. 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'conversation_id' ) . ' '
					. 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' AS c '
					. 'ON c.' . $db->nameQuote( 'message_id' ) . ' = b.' . $db->nameQuote( 'id' ) . ' '
					. 'WHERE a.' . $db->nameQuote( 'id' ) . ' = ' . $db->Quote( $this->id ) . ' '
					. 'AND c.' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId ) . ' '
					. 'ORDER BY b.' . $db->nameQuote( 'created' ) . ' DESC '
					. 'LIMIT 1';
			$db->setQuery( $query );

			$messages[ $this->id . $userId ]	= $db->loadResult();
		}
		return $messages[ $this->id . $userId ];
	}
}
