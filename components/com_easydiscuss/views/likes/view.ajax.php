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

class EasyDiscussViewLikes extends EasyDiscussView
{
	/**
	 * Processes ajax request when a user likes an item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function like()
	{
		$my			= JFactory::getUser();
		$ajax		= DiscussHelper::getHelper( 'ajax' );
		$config		= DiscussHelper::getConfig();

		// Get the post.
		$postId	 	= JRequest::getInt( 'postid' );
		$post		= DiscussHelper::getTable( 'Post' );
		$post->load( $postId );

		// Determine if the likes are enabled or not.
		if( $post->isReply() && !$config->get( 'main_likes_replies' ) || ($post->isQuestion() && !$config->get( 'main_likes_discussions' ) ) )
		{
			return $ajax->reject();
		}

		// Do not allow non logged in users to like an item.
		if( !$my->id )
		{
			return $ajax->reject();
		}

		// Determine if the current user request is to like the post item or unlike.
		$isLike		= !$post->isLikedBy( $my->id );

		if( $isLike )
		{
			DiscussHelper::getHelper( 'Likes' )->addLikes( $post->id , 'post' , $my->id );
		}
		else
		{
			// If this is an unlike request, we need to remove it.
			DiscussHelper::getHelper( 'Likes' )->removeLikes( $post->id , $my->id );
		}

		// Get the main question if this is a reply.
		if( $post->isReply() )
		{
			$question	= DiscussHelper::getTable( 'Post' );
			$question->load( $post->parent_id );
		}
		else
		{
			$question 	= $post;
		}

		// Add JomSocial activity item if the post for the main discussion item if it has been liked.
		if( $post->published && $isLike )
		{
			// EasySocial instegrations
			if( $post->user_id != $my->id ) {
				DiscussHelper::getHelper( 'EasySocial' )->notify( 'new.likes' , $post , $question );
			}

			DiscussHelper::getHelper( 'jomsocial' )->addActivityLikes( $post , $question );
			DiscussHelper::getHelper( 'easysocial' )->likesStream( $post , $question );
		}

		// Add a badge record for the user when they like a discussion
		// The record should only be added when the user liked another user's post.
		if( $post->isQuestion() && $isLike && $my->id != $post->user_id )
		{
			// Add logging for user.
			DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.like.discussion' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_LIKE_DISCUSSION' , $question->title ), $post->id );

			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.like.discussion' , $my->id );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.like.discussion' , $my->id );

			// Assign badge for EasySocial
			DiscussHelper::getHelper( 'EasySocial' )->assignBadge( 'like.question' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_LIKE_DISCUSSION' , $question->title ) );
		}

		// Add a badge record for the user when they like a discussion
		// The record should only be added when the user liked another user's post.
		if( $post->isReply() && $isLike && $my->id != $post->user_id )
		{
			// Add logging for user.
			DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.like.reply' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_LIKE_REPLY' , $post->title ), $post->id );

			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.like.reply' , $my->id );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.like.reply' , $my->id );
		}

		// Remove history when a user unlikes a discussion.
		if( $post->isQuestion() && !$isLike && $my->id != $post->user_id )
		{
			// Remove unlike
			DiscussHelper::getHelper( 'History' )->removeLog( 'easydiscuss.like.discussion' , $my->id , $post->id );

			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.unlike.discussion' , $my->id );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.unlike.discussion' , $my->id );
		}

		// Remove history when a user unlikes a reply.
		if( $post->isReply() && !$isLike && $my->id != $post->user_id )
		{
			// Remove unlike
			DiscussHelper::getHelper( 'History' )->removeLog( 'easydiscuss.like.reply' , $my->id , $post->id );

			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.unlike.reply' , $my->id );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.unlike.reply' , $my->id );
		}

		// Add notifications to the post owner.
		if( $post->user_id != $my->id )
		{
			$notification	= DiscussHelper::getTable( 'Notifications' );

			$text 			= $post->isQuestion() ? 'COM_EASYDISCUSS_LIKE_DISCUSSION_NOTIFICATION_TITLE' : 'COM_EASYDISCUSS_LIKE_REPLY_NOTIFICATION_TITLE';
			$title			= $question->title;
			$likeType 		= $post->isQuestion() ? DISCUSS_NOTIFICATIONS_LIKES_DISCUSSION : DISCUSS_NOTIFICATIONS_LIKES_REPLIES;

			$notification->bind( array(
					'title'		=> JText::sprintf( $text , $title ),
					'cid'		=> $question->id,
					'type'		=> $likeType,
					'target'	=> $post->user_id,
					'author'	=> $my->id,
					'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $question->id
				) );
			$notification->store();
		}

		// Only send notification email if the post owner is a registered user.
		// And, it would not send email if the user that is liking on his own item.
		if(($post->user_id && $my->id != $post->user_id) && $isLike && $config->get('notify_owner_like'))
		{
			// Send email to post / reply author that someone liked their post.
			$notify	= DiscussHelper::getNotification();

			$profile 	= DiscussHelper::getTable( 'Profile' );
			$profile->load( $my->id );

			$emailSubject 	= JText::sprintf( 'COM_EASYDISCUSS_USER_LIKED_YOUR_POST' , $profile->getName() );
			$emailTemplate	= 'email.like.post.php';

			$emailData						= array();
			$emailData[ 'authorName']		= $profile->getName();
			$emailData[ 'authorAvatar' ]	= $profile->getAvatar();
			$emailData[ 'replyContent' ]	= $post->content;
			$emailData[ 'postLink' ]		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $question->id, false, true);

			$recipient 		= JFactory::getUser( $post->user_id );

			$notify->addQueue( $recipient->email, $emailSubject, '', $emailTemplate, $emailData);
		}

		// Get the like's text.
		$likeText		= DiscussHelper::getHelper( 'Likes' )->getLikesHTML( $post->id , $my->id , 'post' );

		if( !$likeText )
		{
			$likeText 	= JText::_( 'COM_EASYDISCUSS_BE_THE_FIRST_TO_LIKE' );
		}



		$count = DiscussHelper::getModel('Likes')->getTotalLikes( $postId );

		$ajax->resolve( $likeText, $count );
		return $ajax->send();
	}

}
