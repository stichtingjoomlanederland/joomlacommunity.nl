<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class KomentoActivityHelper
{
	public function process( $action, $comment )
	{
		// process all activities and 3rd party integration here

		// Due to the possible conflict data of $comment->created containing lapsed time instead of raw date, we use the table object directly instead

		if( !( $comment instanceof KomentoTableComments ) )
		{
			$id = 0;

			if( is_int( $comment ) || is_string( $comment ) )
			{
				$id = $comment;
			}

			if( $comment instanceof KomentoComment )
			{
				$id = $comment->id;
			}

			$comment = Komento::getTable( 'comments' );
			$comment->load( $id );
		}

		if( $action != 'remove' && $comment->published != 1 )
		{
			return false;
		}

		$config = Komento::getConfig();
		$profile = Komento::getProfile();
		$application = Komento::loadApplication( $comment->component )->load( $comment->cid );

		if( $application === false)
		{
			$application = Komento::getErrorApplication( $comment->component, $comment->cid );
		}

		$pagelink = $application->getContentPermalink();
		$permalink = $pagelink . '#' . $comment->id;
		$author = $application->getAuthorId();
		$title = $application->getContentTitle();

		if( $profile->id == 0 )
		{
			return false;
		}

		// native activity
		if( ( $action == 'comment' && $config->get( 'activities_comment' ) ) || ( $action == 'reply' && $config->get( 'activities_reply' ) ) || ( $action == 'like' && $config->get( 'activities_like' ) ) )
		{
			$this->addActivity( $action, $comment->id, $comment->created_by );
		}

		// Add jomsocial activity
		if( $action == 'comment' && $config->get( 'jomsocial_enable_comment' ) )
		{
			$this->addJomSocialActivityComment( $comment );
		}
		if( $action == 'reply' && $config->get( 'jomsocial_enable_reply' ) )
		{
			$this->addJomSocialActivityReply( $comment );
		}
		if( $action == 'like' && $config->get( 'jomsocial_enable_like' ) )
		{
			$this->addJomSocialActivityLike( $comment, $profile->id );
		}

		// Add jomsocial userpoints
		if( $config->get( 'jomsocial_enable_userpoints' ) )
		{
			switch( $action )
			{
				case 'comment':
					Komento::addJomSocialPoint( 'com_komento.comment.add' );
					Komento::addJomSocialPoint( 'com_komento.comment.add.author', $author );
					break;

				case 'reply':
					Komento::addJomSocialPoint( 'com_komento.comment.reply' );
					break;

				case 'like':
					Komento::addJomSocialPoint( 'com_komento.comment.like' );
					Komento::addJomSocialPoint( 'com_komento.comment.liked', $comment->created_by );
					break;

				case 'unlike':
					Komento::addJomSocialPoint( 'com_komento.comment.unlike' );
					Komento::addJomSocialPoint( 'com_komento.comment.unliked', $comment->created_by );
					break;

				case 'report':
					Komento::addJomSocialPoint( 'com_komento.comment.report' );
					Komento::addJomSocialPoint( 'com_komento.comment.reported', $comment->created_by );
					break;

				case 'unreported':
					Komento::addJomSocialPoint( 'com_komento.comment.unreport' );
					Komento::addJomSocialPoint( 'com_komento.comment.unreported', $comment->created_by );
					break;

				case 'stick':
					Komento::addJomSocialPoint( 'com_komento.comment.sticked' );
					break;

				case 'unstick':
					Komento::addJomSocialPoint( 'com_komento.comment.unsticked' );
					break;

				case 'remove':
					Komento::addJomSocialPoint( 'com_komento.comment.removed' );
					Komento::addJomSocialPoint( 'com_komento.comment.removed.author', $author );
					break;
			}
		}

		// Add aup
		if( $config->get( 'enable_aup' ) )
		{
			switch( $action )
			{
				case 'comment':
					Komento::addAUP( 'plgaup_komento_post_comment', $comment->created_by, 'komento_post_comment_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_POST_COMMENT', $permalink, $title ) );
					Komento::addAUP( 'plgaup_komento_add_comment_author', $author, 'komento_post_comment_on_' . $comment->component . '_' . $comment->cid, JText::sprintf( 'COM_KOMENTO_AUP_POST_COMMENT', $permalink, $title ) );
					break;

				case 'reply':
					Komento::addAUP( 'plgaup_komento_reply_comment', $comment->created_by, 'komento_reply_comment_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_REPLY_COMMENT', $permalink, $title ) );
					break;

				case 'like':
					Komento::addAUP( 'plgaup_komento_like_comment', $profile->id, 'komento_like_comment_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_LIKE_COMMENT', $permalink, $title ) );
					Komento::addAUP( 'plgaup_komento_comment_liked', $comment->created_by, 'komento_comment_liked_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_LIKED', $permalink, $title ) );
					break;

				case 'unlike':
					Komento::addAUP( 'plgaup_komento_unlike_comment', $profile->id, 'komento_unlike_comment_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_UNLIKE_COMMENT', $permalink, $title ) );
					Komento::addAUP( 'plgaup_komento_comment_unliked', $comment->created_by, 'komento_comment_unliked_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_UNLIKED', $permalink, $title ) );
					break;
				case 'report':
					Komento::addAUP( 'plgaup_komento_report_comment', $profile->id, 'komento_report_comment_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_REPORT_COMMENT', $permalink, $title ) );
					Komento::addAUP( 'plgaup_komento_comment_reported', $comment->created_by, 'komento_comment_reported_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_REPORTED', $permalink, $title ) );
					break;
				case 'unreport':
					Komento::addAUP( 'plgaup_komento_unreport_comment', $profile->id, 'komento_unreport_comment_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_UNREPORT_COMMENT', $permalink, $title ) );
					Komento::addAUP( 'plgaup_komento_comment_unreported', $comment->created_by, 'komento_comment_unreported_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_UNREPORTED', $permalink, $title ) );
					break;
				case 'stick':
					Komento::addAUP( 'plgaup_komento_comment_sticked', $comment->created_by, 'komento_comment_sticked_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_STICKED', $permalink, $title ) );
					break;
				case 'unstick':
					Komento::addAUP( 'plgaup_komento_comment_unsticked', $comment->created_by, 'komento_comment_unsticked_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_UNSTICKED', $permalink, $title ) );
					break;
				case 'remove':
					Komento::addAUP( 'plgaup_komento_comment_removed', $comment->created_by, 'komento_comment_removed_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_REMOVED', $pagelink, $title ) );
					Komento::addAUP( 'plgaup_komento_remove_comment_author', $author, 'komento_remove_comment_on_' . $comment->component . '_' . $comment->cid, JText::sprintf( 'COM_KOMENTO_AUP_REMOVED_COMMENT_AUTHOR', $pagelink, $title ) );
					break;
			}
		}

		// Add Discuss points
		if( $config->get( 'enable_discuss_points' ) )
		{
			switch( $action )
			{
				case 'comment':
					Komento::addDiscussPoint( 'komento.add.comment', $comment->created_by, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_ADD_COMMENT', $title ) );
					Komento::addDiscussPoint( 'komento.add.comment.article.author', $author, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_ADD_COMMENT_ARTICLE_AUTHOR', $title ) );
					break;

				case 'reply':
					Komento::addDiscussPoint( 'komento.reply.comment', $comment->created_by, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_REPLY_COMMENT', $title ) );
					break;

				case 'like':
					Komento::addDiscussPoint( 'komento.like.comment', $profile->id, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_LIKE_COMMENT', $title ) );
					Komento::addDiscussPoint( 'komento.comment.liked', $comment->created_by, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_COMMENT_LIKED', $title ) );
					break;

				case 'unlike':
					Komento::addDiscussPoint( 'komento.unlike.comment', $profile->id, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_UNLIKE_COMMENT', $title ) );
					Komento::addDiscussPoint( 'komento.comment.unliked', $comment->created_by, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_COMMENT_UNLIKED', $title ) );
					break;
				case 'report':
					Komento::addDiscussPoint( 'komento.report.comment', $profile->id, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_REPORT_COMMENT', $title ) );
					Komento::addDiscussPoint( 'komento.comment.reported', $comment->created_by, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_COMMENT_REPORTED', $title ) );
					break;
				case 'unreport':
					Komento::addDiscussPoint( 'komento.unreport.comment', $profile->id, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_UNREPORT_COMMENT', $title ) );
					Komento::addDiscussPoint( 'komento.comment.unreported', $comment->created_by, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_COMMENT_UNREPORTED', $title ) );
					break;
				case 'stick':
					Komento::addDiscussPoint( 'komento.comment.sticked', $comment->created_by, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_COMMENT_STICKED', $title ) );
					break;
				case 'unstick':
					Komento::addDiscussPoint( 'komento.comment.unsticked', $comment->created_by, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_COMMENT_UNSTICKED', $title ) );
					break;
				case 'remove':
					Komento::addDiscussPoint( 'komento.comment.removed', $comment->created_by, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_COMMENT_REMOVED', $title ) );
					Komento::addDiscussPoint( 'komento.remove.comment.article.author', $author, JText::sprintf( 'COM_KOMENTO_DISCUSS_HISTORY_REMOVE_COMMENT_ARTICLE_AUTHOR', $title ) );
					break;
			}
		}

		if( $config->get( 'enable_easysocial_points' ) )
		{
			$es	= Komento::getHelper( 'EasySocial' );

			switch( $action )
			{
				case 'comment':
					$es->assignPoints( 'komento.add.comment' , $comment->created_by, $comment); //Add points to user when a user post a comment.
					$es->assignPoints( 'komento.add.comment.article.author' , $author, $comment); //Add points for article author when a user posts a new comment on that article.
					break;

				case 'reply':
					$es->assignPoints( 'komento.reply.comment', $comment->created_by, $comment); //Add points to user when a user reply to a comment.
					break;

				case 'like':
					$es->assignPoints( 'komento.like.comment' , $profile->id ); //Add points to user when a user likes a comment.
					$es->assignPoints( 'komento.comment.liked' , $comment->created_by ); //Add points to comment author when a user likes the comment. *?
					break;

				case 'unlike':
					$es->assignPoints( 'komento.unlike.comment' , $profile->id ); //Add points to user when a user unlikes a comment.
					$es->assignPoints( 'komento.comment.unliked' , $comment->created_by ); //Deduct points to comment author when a user unlike the comment.
					break;
				case 'report':
					$es->assignPoints( 'komento.report.comment' , $profile->id ); // Add points to user when a user reports a comment.
					$es->assignPoints( 'komento.comment.reported' , $comment->created_by ); //Deduct points to comment author when a comment is reported.
					break;
				case 'unreport':
					$es->assignPoints( 'komento.unreport.comment' , $profile->id ); // Deduct points to user when a user unreports a comment.
					$es->assignPoints( 'komento.comment.unreported' , $comment->created_by ); //Add points to comment author when a comment is unreported.
					break;
				case 'stick':

					$es->assignPoints( 'komento.comment.sticked' , $comment->created_by ); //Add points to comment author when a comment is sticked.
					break;
				case 'unstick':
					$es->assignPoints( 'komento.comment.unsticked' , $comment->created_by ); //Deduct points to comment author when a comment is unsticked.
					break;
				case 'remove':
					$es->assignPoints( 'komento.comment.removed' , $comment->created_by ); //Deduct points to comment author when a comment is removed.
					$es->assignPoints( 'komento.remove.comment.article.author' , $author ); //Deduct points for article author when a comment is removed from that article.
					break;
				case 'upload':
					$es->assignPoints( 'komento.upload.attachments' , $comment->created_by ); //Deduct points to comment author when a comment is removed.
					break;
			}
		}

		if( $config->get( 'enable_easysocial_badges' ) )
		{
			$es 	= Komento::getHelper( 'EasySocial' );

			switch( $action )
			{
				case 'comment':
					$es->assignBadge( 'komento.add.comment', JText::_( 'COM_KOMENTO_EASYSOCIAL_BADGES_ADD_COMMENTS_MESSAGE' ) );
					break;

				case 'reply':
					$es->assignBadge( 'komento.reply.comment', JText::_( 'COM_KOMENTO_EASYSOCIAL_BADGES_REPLY_COMMENTS_MESSAGE' ) );
					break;

				case 'like':
					$es->assignBadge( 'komento.like.comment', JText::_( 'COM_KOMENTO_EASYSOCIAL_BADGES_LIKE_COMMENTS_MESSAGE' ) );
					break;

				case 'report':
					$es->assignBadge( 'komento.report.comment', JText::_( 'COM_KOMENTO_EASYSOCIAL_BADGES_REPORT_COMMENTS_MESSAGE' ) );
					break;

				case 'upload':
					$es->assignBadge( 'komento.upload.attachments', JText::_( 'COM_KOMENTO_EASYSOCIAL_BADGES_UPLOAD_COMMENTS_MESSAGE' ) );
					break;
			}
		}

		if( $config->get( 'notification_es_enable' ) && ( $config->get( 'notification_es_event_new_' . $action ) ) )
		{
			$es 	= Komento::getHelper( 'EasySocial' );

			$es->notify( $action, $comment );
		}

		// If action is reply and sync comment is on, then we don't create a stream
		// If comment params->source is from com_easysocial, then we don't create a stream
		if( ( ( in_array( $action, array( 'comment', 'like' ) ) && $config->get( 'enable_easysocial_stream_' . $action ) ) || $action === 'reply' && !$config->get( 'enable_easysocial_sync_comment' ) ) && ( empty( $comment->params->source ) || $comment->params->source !== 'com_easysocial' ) )
		{
			$es 	= Komento::getHelper( 'EasySocial' );
			$es->createStream( $action, $comment );
		}

		if( $action === 'reply' && $config->get( 'enable_easysocial_sync_comment' ) && empty( $comment->params->social->source ) )
		{
			$es = Komento::getHelper( 'EasySocial' );
			$es->injectComment( $comment );
		}

		if( $action === 'remove' && $config->get( 'enable_easysocial_sync_comment' ) )
		{
			$es = Komento::getHelper( 'EasySocial' );
			$es->removeComment( $comment );
		}

		if( $action === 'like' && $config->get( 'enable_easysocial_sync_like' ) )
		{
			$es = Komento::getHelper( 'EasySocial' );
			$es->injectLike( $comment );
		}

		if( $action === 'unlike' && $config->get( 'enable_easysocial_sync_like' ) )
		{
			$es = Komento::getHelper( 'EasySocial' );
			$es->removeLike( $comment );
		}
	}

	public function addActivity( $type, $comment_id, $uid )
	{
		$model	= Komento::getModel( 'Activity' );
		return $model->add( $type, $comment_id, $uid );
	}

	public static function addJomSocialActivity( $options = array() )
	{
		$defaultOptions = array(
			'comment'		=> '',
			'title'			=> '',
			'content'		=> '',
			'cmd'			=> '',
			'actor'			=> '',
			'target'		=> 0,
			'app'			=> '',
			'cid'			=> '',
			'comment_id'	=> '',
			'comment_type'	=> '',
			'like_id'		=> '',
			'like_type'		=> ''

		);

		$options = Komento::mergeOptions( $defaultOptions, $options );

		$jsCoreFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
		$config		= Komento::getConfig();

		if( !JFile::exists( $jsCoreFile ) )
		{
			return false;
		}
		require_once( $jsCoreFile );

		$obj				= (object) $options;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add( $obj );
	}

	public function addJomSocialActivityComment( $comment )
	{
		if( !is_object( $comment ) )
		{
			$comment = Komento::getComment( $comment );
		}

		$comment = Komento::getHelper( 'comment' )->process( $comment );
		$comment->comment = JString::substr( strip_tags( $comment->comment ), 0 , Komento::getConfig()->get( 'jomsocial_comment_length' ) );

		$options = array(
			'title'			=> JText::sprintf('COM_KOMENTO_JOMSOCIAL_ACTIVITY_COMMENT_ADDED', $comment->pagelink, $comment->extension->getContentTitle() ),
			'content'		=> $comment->comment,
			'cmd'			=> 'komento.comment.add',
			// 'app'			=> $comment->component,
			// due to js 2.8
			'app'			=> 'komento',
			'cid'			=> $comment->cid,
			'actor'			=> $comment->created_by,
			'comment_id'	=> $comment->id,
			'comment_type'	=> 'com_komento.comments',
			'like_id'		=> $comment->id,
			'like_type'		=> 'com_komento.likes'
		);
		self::addJomSocialActivity( $options );
	}

	public function addJomSocialActivityReply( $comment )
	{
		if( !is_object( $comment ) )
		{
			$comment = Komento::getComment( $comment );
		}

		$comment = Komento::getHelper( 'comment' )->process( $comment );
		$comment->comment = JString::substr( strip_tags( $comment->comment ), 0 , Komento::getConfig()->get( 'jomsocial_comment_length' ) );

		$parent = Komento::getHelper( 'comment' )->process( Komento::getComment( $comment->parent_id ) );

		$options = array(
			'title'			=> JText::sprintf('COM_KOMENTO_JOMSOCIAL_ACTIVITY_REPLY_ADDED', $parent->permalink, $comment->pagelink, $comment->extension->getContentTitle() ),
			'content'		=> $comment->comment,
			'cmd'			=> 'komento.comment.reply',
			// 'app'			=> $comment->component,
			// due to js 2.8
			'app'			=> 'komento',
			'cid'			=> $comment->cid,
			'actor'			=> $comment->created_by,
			'comment_id'	=> $comment->id,
			'comment_type'	=> 'com_komento.comments',
			'like_id'		=> $comment->id,
			'like_type'		=> 'com_komento.likes'
		);
		self::addJomSocialActivity( $options );
	}

	public function addJomSocialActivityLike( $comment, $uid )
	{
		if( !is_object( $comment ) )
		{
			$comment = Komento::getComment( $comment );
		}

		$comment = Komento::getHelper( 'comment' )->process( $comment );
		$comment->comment = JString::substr( strip_tags( $comment->comment ), 0 , Komento::getConfig()->get( 'jomsocial_comment_length' ) );

		$options = array(
			'title'			=> JText::sprintf('COM_KOMENTO_JOMSOCIAL_ACTIVITY_LIKED_COMMENT', $comment->permalink, $comment->pagelink, $comment->extension->getContentTitle() ),
			'content'		=> $comment->comment,
			'cmd'			=> 'komento.comment.like',
			// 'app'			=> $comment->component,
			// due to js 2.8
			'app'			=> 'komento',
			'cid'			=> $comment->cid,
			'actor'			=> $uid,
			'comment_id'	=> $comment->id,
			'comment_type'	=> 'com_komento.comments',
			'like_id'		=> $comment->id,
			'like_type'		=> 'com_komento.likes'
		);
		self::addJomSocialActivity( $options );
	}
}
