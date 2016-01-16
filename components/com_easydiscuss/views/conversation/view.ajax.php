<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
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
	 * Loads a list of new messages via ajax.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	null
	 */
	public function load()
	{
		$my		= JFactory::getUser();
		$ajax	= DiscussHelper::getHelper( 'Ajax' );

		$config	= DiscussHelper::getConfig();

		if( $my->id <= 0 || !$config->get( 'main_conversations_notification') || !$config->get( 'main_conversations' ) )
		{
			$ajax->fail( JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' ) );
			return;
		}

		// @TODO: Show only x amount of items
		// Get a list of conversations to be displayed in the drop down.
		$model			= DiscussHelper::getModel( 'Conversation' );
		$conversations	= $model->getConversations( $my->id , array( 'limit' => $config->get( 'main_conversations_notification_items' ) ) );

		// Format messages
		DiscussHelper::formatConversations( $conversations );

		$theme = new DiscussThemes();
		$theme->set( 'conversations'	, $conversations );

		$output	= $theme->fetch( 'toolbar.conversation.item.php' );

		$ajax->success( $output );
	}

	/**
	 * Returns the number of new messages for a particular user.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function count()
	{
		$my	    = JFactory::getUser();
		$ajax	= DiscussHelper::getHelper( 'Ajax' );

		if( $my->id <= 0 )
		{
			$ajax->fail( 'COM_EASYDISCUSS_NOT_ALLOWED' );
			return;
		}

		$model	= DiscussHelper::getModel( 'Conversation' );
		$count	= $model->getCount( $my->id , array( 'filter' => 'unread' ) );

		$ajax->success( $count );
	}

	/**
	 * Confirm deletion of a message.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmDelete( $id )
	{
		$my = JFactory::getUser();

		if( $my->id <= 0 )
		{
			$ajax->fail( 'COM_EASYDISCUSS_NOT_ALLOWED' );
			return;
		}

		$ajax		= new Disjax();
		$theme		= new DiscussThemes();

		$theme->set( 'id'	, $id );
		$content	= $theme->fetch( 'delete.conversation.php' , array('dialog'=> true ) );

		$options			= new stdClass();
		$options->content	= $content;

		$options->title		= JText::_( 'COM_EASYDISCUSS_DELETE_CONVERSATION' );

		$buttons			= array();

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_NO' );
		$button->action		= 'disjax.closedlg();';
		$buttons[]			= $button;

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_YES' );
		$button->form		= '#deletePostForm';
		$button->className	= 'btn-primary';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$ajax->dialog( $options );
		$ajax->send();
	}

	/**
	 * Method responsible to save a reply.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	null
	 */
	public function reply()
	{
		$ajax	= DiscussHelper::getHelper( 'Ajax' );
		$config	= DiscussHelper::getConfig();
		$my		= JFactory::getUser();

		// Ensure that the user is logged in.
		if( $my->id <= 0 )
		{
			$ajax->fail( 'COM_EASYDISCUSS_NOT_ALLOWED' );
			return;
		}

		// Obtain the message id from post.
		$id				= JRequest::getInt( 'id' , 0 );

		// Load current conversation
		$conversation	= DiscussHelper::getTable( 'Conversation' );
		$state			= $conversation->load( $id );

		// Test for valid message id.
		if( !$state )
		{
			// @TODO: Throw some error here;
			exit;
		}

		// Test if the current user is involved in this conversation.
		if( !$conversation->isInvolved( $my->id ) )
		{
			exit;
		}

		// Get message meta here.
		$content	= JRequest::getVar( 'message' );

		if( empty($content) )
		{
			$ajax->reject( 'COM_EASYDISCUSS_CONVERSATION_EMPTY_CONTENT' );
			return;
		}


		$model		= DiscussHelper::getModel( 'Conversation' );
		$reply		= $model->insertReply( $conversation->id , $content , $my->id );
		$data		= array( $reply );

		// Format conversation replies.
		DiscussHelper::formatConversationReplies( $data );

		$replyObj	= $data[ 0 ];
		$reply 		= new stdClass();
		// Send notification to the recipient.
		$conversation->notify( $replyObj );

		// Use for ejs theme file.
		$reply->id 				= $replyObj->id;
		$reply->conversation_id = $replyObj->conversation_id;
		$reply->message 		= $replyObj->message;
		$reply->created 		= $replyObj->created;
		$reply->created_by 		= $replyObj->created_by;
		$reply->lastreplied 	= $replyObj->lastreplied;
		$reply->authorName		= $replyObj->creator->getName();
		$reply->authorLink		= $replyObj->creator->getLink();
		$reply->authorAvatar	= $replyObj->creator->getAvatar();
		$reply->className		= $replyObj->creator->id == $my->id ? 'by-me' : 'by-user';

		// Since the ajax called could be within the same milisecond, the lapsed should always be 1 second ago.
		$reply->lapsed			= JText::sprintf( 'COM_EASYDISCUSS_X_SECOND_AGO' , 1 );

		$ajax->resolve( $reply );
		$ajax->send();
	}


	/**
	 * Displays the conversation dialog
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function write( $userId )
	{
		$my 		= JFactory::getUser();

		// Do not allow guests to access here.
		if( !$my->id || !$userId )
		{
			exit;
		}

		$ajax		= new Disjax();
		$theme		= new DiscussThemes();
		$recipient	= DiscussHelper::getTable( 'Profile' );
		$recipient->load( $userId );

		$theme->set( 'recipient'	, $recipient );

		$content	= $theme->fetch( 'ajax.conversation.write.php' , array('dialog'=> true ) );

		$options			= new stdClass();
		$options->content	= $content;
		$options->title		= JText::_( 'COM_EASYDISCUSS_DIALOG_TITLE_NEW_CONVERSATION' );

		$buttons			= array();

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_CANCEL' );
		$button->action		= 'disjax.closedlg();';
		$buttons[]			= $button;

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_SEND' );
		$button->action		= 'discuss.conversation.send();';
		$button->className	= 'btn-primary';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$options->width 	= 650;

		$ajax->dialog( $options );

		return $ajax->send();
	}

	/**
	 * Save a conversation
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function save()
	{
		$config 	= DiscussHelper::getConfig();
		$app 		= JFactory::getApplication();
		$my 		= JFactory::getUser();
		$ajax		= new Disjax();

		$content 		= JRequest::getVar( 'contents' );
		$recipientId	= JRequest::getInt( 'recipient' );

		// Test for valid recipients.
		if( !$recipientId )
		{
			return $ajax->send();
		}

		// Test for empty contents here.
		if( empty( $content ) )
		{
			exit;
		}

		// Do not allow user to send a message to himself, it's crazy.
		if( $recipientId == $my->id )
		{
			echo JText::_( 'You should not start a conversation with your self.' );
			return $ajax->send();
		}

		// Initialize conversation table.
		$conversation 	= DiscussHelper::getTable( 'Conversation' );

		// Check if this conversation already exist in the system.
		$state 			= $conversation->loadByRelation( $my->id , $recipientId );

		$model 		= DiscussHelper::getModel( 'Conversation' );

		// If the conversation does not exist, we need to create the conversation and add a recipient.
		if( !$state )
		{
			$date 		= DiscussHelper::getDate()->toMySQL();
			$conversation->created 		= $date;
			$conversation->created_by	= $my->id;
			$conversation->lastreplied 	= $date;

			$conversation->store();

			// Add participant to this conversation.

			$model->addParticipant( $conversation->id , $recipientId , $my->id );
		}
		else
		{
			// Set last replied date if this is a reply.
			$conversation->lastreplied 	= DiscussHelper::getDate()->toMySQL();
			$conversation->store();
		}

		// Initialize message table.
		$message 					= DiscussHelper::getTable( 'ConversationMessage' );
		$message->message 			= $content;
		$message->conversation_id 	= $conversation->id;
		$message->created 			= DiscussHelper::getDate()->toMySQL();
		$message->created_by 		= $my->id;
		$message->store();

		// Add message map so that recipient can view the message.
		$model->addMessageMap( $conversation->id , $message->id , $recipientId , $my->id );

		// Format conversation replies.
		$data		= array( $message );
		DiscussHelper::formatConversationReplies( $data );
		$reply 		= $data[ 0 ];

		// Send notification to the recipient.
		$conversation->notify( $reply );

		$options			= new stdClass();
		$options->content	= JText::_( 'COM_EASYDISCUSS_CONVERSATION_MESSAGE_SUCCESSFULLY_SENT' );
		$options->title		= JText::_( 'COM_EASYDISCUSS_DIALOG_TITLE_NEW_CONVERSATION' );

		$buttons			= array();

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_CLOSE' );
		$button->action		= 'disjax.closedlg();';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$options->width 	= 500;

		$ajax 		= DiscussHelper::getHelper( 'Ajax' );

		$ajax->resolve( $options );
		return $ajax->send();
	}
}
