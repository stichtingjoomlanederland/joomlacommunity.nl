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

class EasydiscussControllerConversation extends JController
{
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

		// Test for valid recipients.
		if( !$recipientId )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_MESSAGING_INVALID_RECIPIENT' ) );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=messaging&layout=compose' , false ) );
			$app->close();
		}

		// Get message meta here.
		$title 		= JRequest::getVar( 'title' );
		$content 	= JRequest::getVar( 'message' );

		// Store the new message.
		$message 	= DiscussHelper::getTable( 'Message' );
		$message->created_by 	= JFactory::getUser()->id;
		$message->recipient		= $recipientId;
		$message->created 		= DiscussHelper::getDate()->toMySQL();
		$message->lastreplied	= DiscussHelper::getDate()->toMySQL();
		$message->store();

		// Store the message meta.
		$meta 		= DiscussHelper::getTable( 'MessageMeta' );
		$meta->message_id	= $message->id;
		$meta->title 		= $title;
		$meta->message 		= $content;
		$meta->created		= DiscussHelper::getDate()->toMySQL();
		$meta->created_by	= JFactory::getUser()->id;
		$meta->isparent 	= true;
		$meta->store();


		$app 	= JFactory::getApplication();

		// @TODO: Add notification for recipient to let them know they received a message.

		// @TODO: Add points for user.

		// @TODO: Add badge for user.

		// Set message queue.
		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_MESSAGE_SENT' ) );
		$app->redirect( DiscussRouter::getMessageRoute( $message->id , false ) );

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
		$id 	= JRequest::getInt( 'id' , 0 );

		$app 		= JFactory::getApplication();
		$message	= DiscussHelper::getTable( 'Message' );
		$state 		= $message->load( $id );
		$my 		= JFactory::getUser();

		// Test for valid message id.
		if( !$state )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_MESSAGING_INVALID_ID' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=messaging' , false ) );
			$app->close();
		}

		// Test if the message is owned by the current user.
		if( !$message->isParticipant( $my->id ) )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=messaging' , false ) );
			$app->close();
		}

		// Try to delete the message.
		if( !$message->delete( $my->id ) )
		{
			DiscussHelper::setMessageQueue( $message->getError() , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=messaging' , false ) );
			$app->close();
		}

		DiscussHelper::setMessageQueue( JText::_( 'The message is deleted.' ) , DISCUSS_QUEUE_SUCCESS );
		$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=messaging' , false ) );
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
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=messaging' , false ) );
			$app->close();
		}

		$message	= DiscussHelper::getTable( 'Message' );
		$message->load( $id );

		$my 		= JFactory::getUser();

		if( !$my->id )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=messaging' , false ) );
			$app->close();
		}

		$model 	= DiscussHelper::getModel( 'Messaging' );
		$model->markUnread( $message->id , $my->id );

		DiscussHelper::setMessageQueue( JText::_( 'The message is now marked as unread.' ) , DISCUSS_QUEUE_SUCCESS );
		$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=messaging' , false ) );
		$app->close();
	}
}
