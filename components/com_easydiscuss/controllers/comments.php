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

class EasydiscussControllerComments extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Converts a comment into a discussion reply
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function convert()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		// Get the Joomla app
		$app 		= JFactory::getApplication();

		// Get the comment id from the request.
		$id			= JRequest::getInt( 'id' );

		// Load the comment
		$comment 	= DiscussHelper::getTable( 'Comment' );
		$comment->load( $id );

		if( !$id || !$comment->id )
		{
			// Throw error here.
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_COMMENTS_INVALID_COMMENT_ID_PROVIDED' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss' , false ) );
			$app->close();
		}

		// Get the post id from the request.
		$postId 	= JRequest::getInt( 'postId' );
		$post 		= DiscussHelper::getTable( 'Post' );
		$post->load( $postId );

		if( !$postId || !$post->id )
		{
			// Throw error here.
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_COMMENTS_INVALID_POST_ID_PROVIDED' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss' , false ) );
			$app->close();
		}

		if( !$comment->canConvert() )
		{
			// Throw error here.
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_COMMENTS_NOT_ALLOWED_TO_CONVERT' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id , false ) );
			$app->close();
		}

		// Create a new reply.
		$reply 				= DiscussHelper::getTable( 'Post' );
		$reply->title 		= $post->title;
		$reply->content		= $comment->comment;
		$reply->published	= 1;
		$reply->created 	= $comment->created;
		$reply->parent_id 	= $post->id;
		$reply->user_id 	= $comment->user_id;
		$reply->user_type 	= 'member';
		$reply->category_id = $post->category_id;

		$state 	= $reply->store();

		if( !$state )
		{
			// Throw error here.
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_COMMENTS_ERROR_SAVING_REPLY' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id , false ) );
			$app->close();
		}

		// Once the reply is stored, delete the comment
		$comment->delete();

		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_COMMENTS_SUCCESS_CONVERTED_COMMENT_TO_REPLY' ) , DISCUSS_QUEUE_SUCCESS );
		$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id , false ) );
		$app->close();
	}
}
