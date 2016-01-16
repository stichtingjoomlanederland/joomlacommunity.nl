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

class EasyDiscussViewComment extends EasyDiscussView
{
	/**
	 * Responsible to process a comment for saving.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function save()
	{
		$id		= JRequest::getInt( 'id' , 0 );
		$my		= JFactory::getUser();
		$acl	= DiscussHelper::getHelper( 'ACL' );
		$ajax	= DiscussHelper::getHelper( 'Ajax' );
		$config	= DiscussHelper::getConfig();

		// Load the post item.
		$post	= DiscussHelper::getTable( 'Post' );
		$state	= $post->load( $id );

		// Test if a valid post id is provided.
		if( !$id || !$state )
		{
			$ajax->reject( JText::_('COM_EASYDISCUSS_COMMENTS_INVALID_POST_ID') );
			return $ajax->send();
		}

		$category = DiscussHelper::getTable( 'Category' );
		$category->load( $post->category_id );

		$access 	= $post->getAccess( $category );
		// Test if the user is allowed to add comment or not.
		if( ! $access->canComment() )
		{
			$ajax->reject( JText::_('COM_EASYDISCUSS_COMMENTS_NOT_ALLOWED') );
			return $ajax->send();
		}

		// Test if the comment message exists.
		$message 	= JRequest::getVar( 'comment' , '' );

		if( empty( $message ) )
		{
			$ajax->reject( JText::_( 'COM_EASYDISCUSS_COMMENT_IS_EMPTY' ) );
		}

		// Test if the user checked the terms and conditions box.
		if( $config->get( 'main_comment_tnc' ) )
		{
			$acceptedTerms	= JRequest::getInt( 'tnc' , 0 );

			if( !$acceptedTerms )
			{
				$ajax->reject( JText::_( 'COM_EASYDISCUSS_TERMS_PLEASE_ACCEPT' ) );
				return $ajax->send();
			}
		}

		// Load user profile's object.
		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->load( $my->id );

		// Build up comment object.
		$commentData			= new stdClass();
		$commentData->user_id	= $my->id;
		$commentData->name		= $profile->getName();
		$commentData->email		= $my->email;
		$commentData->comment	= $message;
		$commentData->post_id	= $post->id;

		// Run through akismet screening if necessary.
		if( $config->get( 'antispam_akismet' ) && ( $config->get('antispam_akismet_key') ) )
		{
			require_once DISCUSS_CLASSES . '/akismet.php';

			$data = array(
					'author'	=> $my->name,
					'email'		=> $my->email,
					'website'	=> DISCUSS_JURIROOT,
					'body'		=> $commentData->comment,
					'alias'		=> ''
				);

			$akismet = new Akismet( DISCUSS_JURIROOT , $config->get( 'antispam_akismet_key' ) , $data );

			if( $akismet->isSpam() )
			{
				$ajax->reject( JText::_('COM_EASYDISCUSS_AKISMET_SPAM_DETECTED') );
				return $ajax->send();
			}
		}

		$comment	= DiscussHelper::getTable( 'Comment' );
		$comment->bind( $commentData , true );

		if( !$comment->store() )
		{
			$ajax->reject( $comment->getError() );
			return $ajax->send();
		}

		// Get post duration.
		$durationObj			= new stdClass();
		$durationObj->daydiff	= 0;
		$durationObj->timediff	= '00:00:01';

		$comment->duration		= DiscussHelper::getDurationString( $durationObj );

		// Set the comment creator.
		$comment->creator		= $profile;


		// Try to detect if the comment is posted to the main question or a reply.
		$liveNotificationText   = '';
		if( $post->parent_id )
		{
			$question	= DiscussHelper::getTable( 'Post' );
			$question->load( $post->parent_id );
			$liveNotificationText   = 'COM_EASYDISCUSS_COMMENT_REPLY_NOTIFICATION_TITLE';
		}
		else
		{
			$question	= DiscussHelper::getTable( 'Post' );
			$question->load( $id );
			$liveNotificationText   = 'COM_EASYDISCUSS_COMMENT_QUESTION_NOTIFICATION_TITLE';
		}

		// Create notification item in EasySocial
		DiscussHelper::getHelper( 'EasySocial' )->notify( 'new.comment' , $post , $question , $comment );


		if( $comment->published && !$question->private )
		{
			// AUP integrations
			DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_NEW_COMMENT , $comment->user_id , '' );

			// jomsocial activity stream
			DiscussHelper::getHelper( 'jomsocial' )->addActivityComment( $post, $question );

			DiscussHelper::getHelper( 'easysocial' )->commentDiscussionStream( $comment , $post , $question );
		}

		// Add notification to the post owner.
		if( $post->user_id != $my->id && $comment->published && $config->get( 'main_notifications_comments' ) )
		{
			$notification 	= DiscussHelper::getTable( 'Notifications' );
			$notification->bind( array(
						'title'		=> JText::sprintf( $liveNotificationText , $question->title ),
						'cid'		=> $question->id,
						'type'		=> DISCUSS_NOTIFICATIONS_COMMENT,
						'target'	=> $post->user_id,
						'author'	=> $my->id,
						'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $question->id
			) );

			$notification->store();
		}

		// Try to assign badge and points to the current user.
		// Only assign points and badge when they are commenting a post that are not posted by them
	//	if( $my->id != $post->user_id )
	//	{
			// Add logging for user.
			DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.new.comment' , $my->id , JText::_( 'COM_EASYDISCUSS_BADGES_HISTORY_NEW_COMMENT'), $post->id );

			// Assign badge for EasySocial
			DiscussHelper::getHelper( 'EasySocial' )->assignBadge( 'create.comment' , $my->id , JText::_( 'COM_EASYDISCUSS_BADGES_HISTORY_NEW_COMMENT' ) );

			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.new.comment' , $my->id );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.new.comment' , $my->id, $comment);
	//	}

		// Apply badword filtering for the comment.
		$comment->comment 	= DiscussHelper::wordFilter( $comment->comment );

		$emailData = array();
		$emailData['commentContent']		= $comment->comment;
		$emailData['commentAuthor']			= $profile->getName();
		$emailData['commentAuthorAvatar']	= $profile->getAvatar();
		$emailData['postTitle']				= $question->title;
		$emailData['postLink']				= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $question->id , false , true );

		$emails		= array();

		// Send email to the post owner only if the commenter is not the post owner.
		if( $post->user_id != 0 && $post->id != $my->id )
		{
			$user 		= JFactory::getUser( $post->user_id );
			$emails[]	= $user->email;
		}

		// Retrieve the list of user emails from the list of comments made on the post.
		$existingComments 	= $post->getComments();

		if( $existingComments )
		{
			foreach( $existingComments as $existingComment )
			{
				// Only add the email when the user id is not the current logged in user who is posting the comment.
				// It should not send email to the post owner as well since the post owner will already get a notification.
				if( $existingComment->user_id != 0 && $existingComment->user_id != $my->id && $existingComment->user_id != $post->user_id )
				{
					$user 		= JFactory::getUser( $existingComment->user_id );
					$emails[]	= $user->email;
				}
			}
		}

		// Ensure the emails are all unique.
		$emails 	= array_unique( $emails );

		// Only send email when email is not empty.
		if( !empty( $emails ) )
		{
			$notify		= DiscussHelper::getNotification();
			$notify->addQueue( $emails, JText::sprintf( 'COM_EASYDISCUSS_EMAIL_TITLE_NEW_COMMENT' , JString::substr($question->content, 0, 15) ) . '...' , '', 'email.post.comment.new.php', $emailData);
		}

		//revert the comment form
		// $ajax->script('discuss.comment.cancel()');

		// Process comment triggers.
		if ( $config->get( 'main_content_trigger_comments' ) )
		{
			$comment->content	= $comment->comment;

			// process content plugins
			DiscussEventsHelper::importPlugin( 'content' );
			DiscussEventsHelper::onContentPrepare('comment', $comment);

			$comment->event = new stdClass();

			$results	= DiscussEventsHelper::onContentBeforeDisplay('comment', $comment);
			$comment->event->beforeDisplayContent	= trim(implode("\n", $results));

			$results	= DiscussEventsHelper::onContentAfterDisplay('comment', $comment);
			$comment->event->afterDisplayContent	= trim(implode("\n", $results));

			$comment->comment	= $comment->content;
		}

		// Get the parent post post id
		$postId = $post->parent_id ? $post->parent_id : $post->id;

		// Get the result of the posted comment.
		$theme	= new DiscussThemes();
		$theme->set( 'comment'	, $comment );


		$theme->set( 'postId'	, $postId );

		$output	= $theme->fetch( 'post.reply.comment.item.php' );

		$ajax->resolve( $output );

		return $ajax->send();
	}

	/**
	 * Displays a confirmation dialog to delete a comment.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique post id.
	 */
	public function confirmConvert( $id = null , $postId = null )
	{
		$ajax		= new Disjax();

		// Test if a valid post id is provided.
		if( !$id )
		{
			$ajax->reject( JText::_('COM_EASYDISCUSS_COMMENTS_INVALID_POST_ID') );
			return $ajax->send();
		}

		$theme		= new DiscussThemes();
		$theme->set( 'id'	, $id );
		$theme->set( 'postId' , $postId );
		$content	= $theme->fetch( 'ajax.comment.convert.php' , array('dialog'=> true ) );

		$options	= new stdClass();
		$options->content	= $content;

		$options->title		= JText::_( 'COM_EASYDISCUSS_CONVERT_COMMENT' );

		$buttons			= array();

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_NO' );
		$button->action		= 'disjax.closedlg();';
		$buttons[]			= $button;

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_YES' );
		$button->form		= '#frmConvert';
		$button->className	= 'btn-primary';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$ajax->dialog( $options );
		$ajax->send();
	}

	/**
	 * Displays a confirmation dialog to delete a comment.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique post id.
	 */
	public function confirmDelete( $id = null )
	{
		$ajax		= new Disjax();

		// Test if a valid post id is provided.
		if( !$id )
		{
			$ajax->reject( JText::_('COM_EASYDISCUSS_COMMENTS_INVALID_POST_ID') );
			return $ajax->send();
		}

		$theme		= new DiscussThemes();
		$theme->set( 'id'	, $id );
		$content	= $theme->fetch( 'ajax.comment.delete.php' , array('dialog'=> true ) );

		$options	= new stdClass();
		$options->content	= $content;

		$options->title		= JText::_( 'COM_EASYDISCUSS_DELETE_COMMENT' );

		$buttons			= array();

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_NO' );
		$button->action		= 'disjax.closedlg();';
		$buttons[]			= $button;

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_YES' );
		$button->action		= 'disjax.load( "comment" , "delete" , "' . $id . '");';
		$button->className	= 'btn-primary';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$ajax->dialog( $options );
		$ajax->send();
	}

	/**
	 * Responsible to delete a comment.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function delete( $id = null )
	{
		$ajax		= new Disjax();
		$id			= (int) $id;

		$comment	= DiscussHelper::getTable( 'Comment' );
		$comment->load( $id );

		if( !$comment->canDelete() )
		{
			echo JText::_('COM_EASYDISCUSS_COMMENTS_NOT_ALLOWED');
			exit;
		}

		if( !$comment->delete() )
		{
			echo $comment->getError();
			exit;
		}

		// AUP Integrations
		DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_DELETE_COMMENT , $comment->user_id , '' );

		$content			= JText::_( 'COM_EASYDISCUSS_COMMENT_SUCESSFULLY_DELETED' );

		$options			= new stdClass();
		$options->content	= $content;

		$options->title		= JText::_( 'COM_EASYDISCUSS_DELETE_COMMENT' );

		$buttons			= array();

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_CLOSE' );
		$button->action		= 'disjax.closedlg();';
		$button->className	= 'btn-primary';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$ajax->dialog( $options );
		$ajax->script(' discuss.comment.removeEntry("' . $id . '");');

		return $ajax->send();
	}

	/**
	 * Shows the terms and condition dialog window.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function tnc()
	{
		$config		= DiscussHelper::getConfig();
		$disjax		= new Disjax();

		$themes		= new DiscussThemes();
		$content	= $themes->fetch( 'ajax.terms.php' , array('dialog'=> true ) );

		$options	= new stdClass();
		$options->title 	= JText::_( 'COM_EASYDISCUSS_TERMS_AND_CONDITIONS' );
		$options->content = $content;

		$buttons			= array();

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_OK' );
		$button->action		= 'disjax.closedlg();';
		$button->className	= 'btn-primary';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$disjax->dialog( $options );
		$disjax->send();
		return;
	}
}
