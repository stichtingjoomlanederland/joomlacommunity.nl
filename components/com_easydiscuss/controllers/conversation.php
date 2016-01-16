<?php
/**
 * @package		Easydiscuss
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Easydiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class EasydiscussControllerConversation extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask( 'unarchive' , 'archive' );
	}

	/**
	 * Stores a private message composed by the user.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	null
	 */
	public function save()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );
		
		$config 		= DiscussHelper::getConfig();
		$recipientId 	= JRequest::getInt( 'recipient' , 0 );
		$app 			= JFactory::getApplication();
		$my 			= JFactory::getUser();

		// Test for valid recipients.
		if( !$recipientId )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_MESSAGING_INVALID_RECIPIENT' ) );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation&layout=compose' , false ) );
			$app->close();
		}

		// Do not allow user to send a message to himself, it's crazy.
		if( $recipientId == $my->id )
		{
			DiscussHelper::setMessageQueue( JText::_( 'You should not start a conversation with your self.' ) );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation&layout=compose' , false ) );
			$app->close();
		}

		// Initialize conversation table.
		$conversation 	= DiscussHelper::getTable( 'Conversation' );

		// Check if this conversation already exist in the system.
		$state 			= $conversation->loadByRelation( $my->id , $recipientId );

		if( !$state )
		{
			$date 		= DiscussHelper::getDate()->toMySQL();
			$conversation->created 		= $date;
			$conversation->created_by	= $my->id;
			$conversation->lastreplied 	= $date;
			
			$conversation->store();
		}

		// Get message from query.
		$content 	= JRequest::getVar( 'message' );

		// Initialize message table.
		$message 					= DiscussHelper::getTable( 'ConversationMessage' );
		$message->message 			= $content;
		$message->conversation_id 	= $conversation->id;
		$message->created 			= DiscussHelper::getDate()->toMySQL();
		$message->created_by 		= $my->id;
		$message->store();

		// Add participant to this conversation.
		$model 		= DiscussHelper::getModel( 'Conversation' );
		$model->addParticipant( $conversation->id , $recipientId , $my->id );

		// Add message map so that recipient can view the message.
		$model->addMessageMap( $conversation->id , $message->id , $recipientId , $my->id );


		// @TODO: Add notification for recipient to let them know they received a message.

		// @TODO: Add points for user.

		// @TODO: Add badge for user.

		// Set message queue.
		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_MESSAGE_SENT' ) );
		$app->redirect( DiscussRouter::getMessageRoute( $conversation->id , false ) );

	}

	/**
	 * Archives a conversation
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function archive()
	{
		JRequest::checkToken( 'request' ) or jexit( 'Invalid Token' );
	
		// Determine the task
		$task 		= $this->getTask();

		// Detect the message id.
		$id 		= JRequest::getInt( 'id' , 0 );
		$app 		= JFactory::getApplication();
		$my 		= JFactory::getUser();

		// Retrieve model
		$model 		= DiscussHelper::getModel( 'Conversation' );

		// Test if user has access
		if( !$model->hasAccess( $id , $my->id ) )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' , false ) );
			$app->close();
		}

		// Try to archive / unarchive the conversation.
		$state 		= $task == 'archive' ? DISCUSS_CONVERSATION_ARCHIVED : DISCUSS_CONVERSATION_PUBLISHED;
		$model->archive( $id , $my->id , $state );

		$message 	= $task == 'archive' ? 'COM_EASYDISCUSS_CONVERSATION_IS_NOW_ARCHIVED' : 'COM_EASYDISCUSS_CONVERSATION_IS_NOW_UNARCHIVED';
		DiscussHelper::setMessageQueue( JText::_( $message ) , DISCUSS_QUEUE_SUCCESS );

		$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' , false ) );
		$app->close();
	}

	/**
	 * Delete's a message.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function delete()
	{
		JRequest::checkToken( 'request' ) or jexit( 'Invalid Token' );
		
		// Detect the message id.
		$id 		= JRequest::getInt( 'id' , 0 );
		$app 		= JFactory::getApplication();
		$my 		= JFactory::getUser();

		// Retrieve model
		$model 		= DiscussHelper::getModel( 'Conversation' );

		// Test if user has access
		if( !$model->hasAccess( $id , $my->id ) )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' , false ) );
			$app->close();
		}

		// Try to delete the message.
		$model->delete( $id , $my->id );

		DiscussHelper::setMessageQueue( JText::_( 'The conversation is deleted.' ) , DISCUSS_QUEUE_SUCCESS );
		$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' , false ) );
		$app->close();
	}

	/**
	 * Marks a message as unread
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function unread()
	{
		JRequest::checkToken( 'request' , 'get' ) or jexit( 'Invalid Token' );
		
		$id 	= JRequest::getInt( 'id' );
		$app 	= JFactory::getApplication();

		// Test for valid recipients.
		if( !$id )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_MESSAGING_INVALID_ID' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' , false ) );
			$app->close();
		}

		// Only registered users are allowed to use conversation.
		$my 		= JFactory::getUser();
		if( !$my->id )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' , false ) );
			$app->close();
		}

		// Retrieve model.
		$model 		= DiscussHelper::getModel( 'Conversation' );

		// Test if user has access
		if( !$model->hasAccess( $id , $my->id ) )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' , false ) );
			$app->close();
		}

		// Mark the conversation as unread.
		$model->markAsUnread( $id , $my->id );


		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_CONVERSATION_MARKED_AS_UNREAD' ) , DISCUSS_QUEUE_SUCCESS );
		$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' , false ) );
		$app->close();
	}
}
