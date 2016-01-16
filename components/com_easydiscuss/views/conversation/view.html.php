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

require_once( DISCUSS_ROOT . '/views.php' );

class EasyDiscussViewConversation extends EasyDiscussView
{
	/**
	 * Displays a list of messages for the current logged in user.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function display( $tpl = null )
	{
		$my 	= JFactory::getUser();
		$app 	= JFactory::getApplication();

		// Do not allow non logged in users to view anything in conversation.
		if( !$my->id )
		{
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false ) );
			$app->close();
		}

		// Retrieve a list of conversations
		$model 			= DiscussHelper::getModel( 'Conversation' );
		$conversations	= $model->getConversations( $my->id );
		$pagination		= $model->getPagination();

		$countInbox 		= $model->getCount( $my->id );
		$countArchives		= $model->getCount( $my->id , array( 'archives' => true ) );


		DiscussHelper::setPageTitle( JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_TITLE' ) );

		// Format messages
		DiscussHelper::formatConversations( $conversations );

		$theme 	= new DiscussThemes();
		$theme->set( 'heading'			, JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_HEADING' ) );
		$theme->set( 'active'			, 'inbox' );
		$theme->set( 'conversations' , $conversations );
		$theme->set( 'pagination'	, $pagination );
		$theme->set( 'countInbox'	, $countInbox );
		$theme->set( 'countArchives'	, $countArchives );

		echo $theme->fetch( 'conversation.list.php' );
	}

	/**
	 * Displays a list of messages for the current logged in user.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function archives()
	{
		$my 	= JFactory::getUser();
		$app 	= JFactory::getApplication();

		// Do not allow non logged in users to view anything in conversation.
		if( !$my->id )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false ) );
			$app->close();
		}

		DiscussHelper::setPageTitle( JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_ARCHIVE_TITLE' ) );

		// Retrieve a list of conversations
		$model 			= DiscussHelper::getModel( 'Conversation' );
		$conversations	= $model->getConversations( $my->id , array( 'archives' => true ) );
		$pagination		= $model->getPagination();

		$countInbox 		= $model->getCount( $my->id );
		$countArchives	= $model->getCount( $my->id , array( 'archives' => true ) );

		// Format messages
		DiscussHelper::formatConversations( $conversations );

		$theme 	= new DiscussThemes();

		$theme->set( 'heading'			, JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_ARCHIVES' ) );
		$theme->set( 'active'			, 'archives' );
		$theme->set( 'conversations' 	, $conversations );
		$theme->set( 'pagination'		, $pagination );
		$theme->set( 'countInbox'		, $countInbox );
		$theme->set( 'countArchives'	, $countArchives );

		echo $theme->fetch( 'conversation.list.php' );
	}

	/**
	 * Displays the conversation.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function read()
	{
		$id 	= JRequest::getInt( 'id' );
		$app 	= JFactory::getApplication();
		$my 	= JFactory::getUser();

		// Do not allow non logged in users to view anything in conversation.
		if( !$my->id )
		{
			$returnURL = base64_encode( JRequest::getURI() );
			//DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' ) , DISCUSS_QUEUE_ERROR );
			//$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false ) );
			$app->redirect( DiscussHelper::getLoginLink( $returnURL ) );
			$app->close();
		}

		// Try to load the conversation
		$conversation 	= DiscussHelper::getTable( 'Conversation' );
		$state 			= $conversation->load( $id );

		// The conversation id needs to be valid.
		if( !$state )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_CONVERSATION_INVALID' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false ) );
			$app->close();
		}

		// Check if the current logged in user has access to this conversation.
		$model 		= DiscussHelper::getModel( 'Conversation' );

		if( !$model->hasAccess( $conversation->id , $my->id ) )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false ) );
			$app->close();
		}

		$doc 	= JFactory::getDocument();
		$result	= $conversation->getParticipants( $my->id );
		$user 	= DiscussHelper::getTable( 'Profile' );
		$user->load( $result[0 ] );

		DiscussHelper::setPageTitle( JText::sprintf( 'COM_EASYDISCUSS_VIEW_CONVERSATION_TITLE' , $this->escape( $user->getName() ) ) );

		// Mark this message as read for the current logged in user.
		$conversation->markAsRead( $my->id );



		// Check if it is view all messages
		$viewAll = JRequest::getVar( 'show' );
		$count = JRequest::getInt( 'count' );


		if( $viewAll == 'all' )
		{
			// For future use
			$count = '';
		}

		if( $viewAll == 'previous' )
		{
			$count = JRequest::getInt( 'count' );
			// Check if the value is integer, we do no want any weird values
			if( isset($count) && is_int($count) )
			{
				// Convert to absolute number
				$count = abs($count);
			}
		}

		// Get replies in the conversation
		$replies 	= $model->getMessages( $conversation->id , $my->id, $viewAll, $count );


		// Format conversation replies.
		DiscussHelper::formatConversationReplies( $replies );

		// Format the conversation object.
		$data 		= array( $conversation );
		DiscussHelper::formatConversations( $data );

		$theme 	= new DiscussThemes();
		$theme->set( 'replies'		, $replies );
		$theme->set( 'conversation'	, $data[0] );

		echo $theme->fetch( 'conversation.read.php' );
	}

	/**
	 * Responsible to display the conversation form.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function compose()
	{
		// Get recipient id from request.
		$id 	= JRequest::getInt( 'id' );
		$app 	= JFactory::getApplication();
		$my 	= JFactory::getUser();

		// Do not allow non logged in users to view anything in conversation.
		if( !$my->id )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false ) );
			$app->close();
		}

		if( !$id )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_CONVERSATION_INVALID' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false ) );
			$app->close();
		}

		$recipient 	= DiscussHelper::getTable( 'Profile' );
		$recipient->load( $id );

		// Initialize conversation table.
		$conversation 	= DiscussHelper::getTable( 'Conversation' );

		// Check if this conversation already exist in the system.
		$state 			= $conversation->loadByRelation( $my->id , $recipient->id );

		// If conversation already exists between both parties, just redirect to the reply in an existing conversation.
		if( $state )
		{
			$app->redirect( DiscussRouter::getMessageRoute( $conversation->id , false ) . '#reply' );
			$app->close();
		}

		$theme 	= new DiscussThemes();

		$theme->set( 'recipient'	, $recipient );
		echo $theme->fetch( 'conversation.compose.php' );
	}
}
