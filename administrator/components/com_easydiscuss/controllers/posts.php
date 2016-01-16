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

require_once DISCUSS_HELPERS . '/date.php';
require_once DISCUSS_HELPERS . '/input.php';
require_once DISCUSS_HELPERS . '/filter.php';

class EasyDiscussControllerPosts extends EasyDiscussController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'unfeature'		, 'toggleFeatured' );
		$this->registerTask( 'feature'			, 'toggleFeatured' );

		$this->registerTask( 'savePublishNew'	, 'submit' );
		$this->registerTask( 'apply'			, 'submit' );
		$this->registerTask( 'save'				, 'submit' );

		$this->registerTask( 'unpublish'		, 'unpublish' );
	}

	public function movePosts()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid 			= JRequest::getVar( 'cid' );
		$newCategoryId	= JRequest::getInt( 'move_category' );
		$newCategory 	= DiscussHelper::getTable( 'Category' );
		$newCategory->load( $newCategoryId );


		if( !$newCategoryId || !$newCategory->id )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_PLEASE_SELECT_CATEGORY') , DISCUSS_QUEUE_ERROR );
			return $this->setRedirect( 'index.php?option=com_easydiscuss&view=posts' );
		}

		if( !is_array( $cid ) )
		{
			$cid 	= array( $cid );
		}

		foreach( $cid as $id )
		{
			$post 	= DiscussHelper::getTable( 'Post' );
			$post->load( $id );

			$post->category_id 	= $newCategory->id;
			$post->store();

			// The category_id for the replies should change too
			$post->moveChilds( $post->id, $post->category_id );
		}

		$message 	= JText::sprintf( 'COM_EASYDISCUSS_POSTS_MOVED_SUCCESSFULLY' , $newCategory->title );

		DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_SUCCESS );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=posts' );
	}

	function toggleFeatured()
	{

		$mainframe	= JFactory::getApplication();
		$records	= JRequest::getVar( 'cid' , '' );
		$message	= '';
		$task		= JRequest::getVar( 'task' );

		foreach( $records as $record )
		{
			$post = JTable::getInstance( 'Posts' , 'Discuss' );
			$post->load( $record );

			$post->featured	= $task == 'feature';

			$post->store();
		}

		$message = JText::_( 'COM_EASYDISCUSS_DISCUSSIONS_FEATURED' );

		if( $task == 'unfeature' )
		{
			$message = JText::_( 'COM_EASYDISCUSS_DISCUSSIONS_UNFEATURED' );
		}

		DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_SUCCESS );

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=posts' );
		$mainframe->close();
	}

	function publish()
	{
		$config	= DiscussHelper::getConfig();
		$post	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		$pid	= JRequest::getString( 'pid' , '' , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $post ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type		= 'error';
		}
		else
		{
			//send notification:
			//so we are publising posts.
			foreach( $post as $postId)
			{
				$item	= JTable::getInstance( 'posts', 'Discuss' );
				$item->load( $postId );

				if( $item->published == DISCUSS_ID_PENDING)
				{

					$callback	= DiscussRouter::getRoutedUrl( 'index.php?option=com_easydiscuss&view=post&id=' . $item->id , false , true );

					$sites		= array( 'facebook' , 'twitter' );

					foreach( $sites as $site )
					{
						if( $config->get( 'main_autopost_' . $site ) )
						{
							$oauth	= DiscussHelper::getTable( 'Oauth' );
							$exists	= $oauth->loadByType( $site );

							$oauthPost	= DiscussHelper::getTable( 'OauthPosts' );

							if( $exists && !empty( $oauth->access_token ) && !$oauthPost->exists( $item->id , $oauth->id ) )
							{
								$consumer	= DiscussHelper::getHelper( 'OAuth' )->getConsumer( $site , $config->get( 'main_autopost_' . $site . '_id') , $config->get( 'main_autopost_' . $site . '_secret') , $callback );
								$consumer->setAccess( $oauth->access_token );

								$consumer->share( $item );

								// @rule: Store this as sent
								$oauthPost->set( 'post_id' , $item->id );
								$oauthPost->set( 'oauth_id', $oauth->id );

								$oauthPost->store();
							}
						}
					}

					// @rule: Send out notifications when the pending moderation items are being published.
					DiscussHelper::sendNotification( $item , $item->parent_id, true, $item->user_id, $item->published);

					// only if the post is a discussion
					if( $config->get( 'integration_pingomatic' ) && empty( $item->parent_id ) )
					{
						$pingo = DiscussHelper::getHelper( 'Pingomatic' );
						$pingo->ping( $item->title, DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $item->id, true, true ) );
					}

					$user 		= JFactory::getUser( $item->user_id );

					if( $item->parent_id )
					{
						DiscussHelper::getHelper( 'jomsocial' )->addActivityReply( $item );
						DiscussHelper::getHelper( 'easysocial')->replyDiscussionStream( $item );
					}
					else
					{
						DiscussHelper::getHelper( 'jomsocial' )->addActivityQuestion( $item );
						DiscussHelper::getHelper( 'easysocial')->createDiscussionStream( $item );
					}


					if( $user->id )
					{
						// Add logging for user.
						DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.new.discussion' , $user->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_NEW_POST' , $item->title ), $item->id );

						DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.new.discussion' , $user->id );
						DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.new.discussion' , $user->id );

						// Assign badge for EasySocial
						DiscussHelper::getHelper( 'EasySocial' )->assignBadge( 'create.question' , $user->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_NEW_POST' , $item->title ) );
						
						// assign new ranks.
						DiscussHelper::getHelper( 'ranks' )->assignRank( $user->id, $config->get( 'main_ranking_calc_type' ) );

						// aup
						DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_NEW_DISCUSSION , $user->id , $item->title );
					}
				}
			}

			//$model		= $this->getModel( 'Posts' );
			$model = DiscussHelper::getModel( 'Posts', true );

			if( $model->publish( $post , 1 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_POSTS_PUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_ERROR_PUBLISHING');
				$type		= 'error';
			}

		}

		$pidLink = '';
		if(! empty($pid))
			$pidLink = '&pid=' . $pid;

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=posts' . $pidLink );
	}

	function unpublish()
	{
		$post	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		$pid	= JRequest::getString( 'pid' , '' , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $post ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Posts' );

			if( $model->publish( $post , 0 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_POSTS_UNPUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_ERROR_UNPUBLISHING');
				$type		= 'error';
			}

		}

		$pidLink = '';
		if(! empty($pid))
			$pidLink = '&pid=' . $pid;

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=posts' . $pidLink );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'post' );
		JRequest::setVar( 'id' , JRequest::getVar( 'id' , '' , 'REQUEST' ) );
		JRequest::setVar( 'pid' , JRequest::getVar( 'pid' , '' , 'REQUEST' ) );
		JRequest::setVar( 'source' , 'posts' );

		parent::display();
	}

	/**
	 * Remove discussions from the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function remove()
	{
		$post	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		$pid	= JRequest::getString( 'pid' , '' , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $post ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Posts' );

			//check if any of the 'to be' remove entry was a answered reply.
			// If yes, revert the main post to unresolved.
			if( ! empty( $pid ) )
			{
				// we know this is the replies.
				$model->revertAnwered( $post );
			}

			if( $post )
			{
				foreach( $post as $id )
				{
					$discussion = DiscussHelper::getTable( 'Post' );
					$discussion->load( $id );

					// Delete all notification associated with this post
					$notificationModel = DiscussHelper::getModel( 'Notification' );
					$notificationModel->deleteNotifications( $id );

					$discussion->delete();
				}

				$message	= ( empty( $pid ) ) ? JText::_('COM_EASYDISCUSS_POSTS_DELETED') : JText::_('COM_EASYDISCUSS_REPLIES_DELETED');

				// @rule: Trigger AUP points
				if( !empty( $pid ) )
				{
					DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_DELETE_DISCUSSION , $post->user_id , $post->title );
				}
			}
			else
			{
				$message	= ( empty( $pid ) ) ? JText::_('COM_EASYDISCUSS_ERROR_DELETING_POST') : JText::_('COM_EASYDISCUSS_ERROR_DELETING_REPLY');
				$type		= 'error';
			}

		}

		$pidLink = '';
		if(! empty($pid))
			$pidLink = '&pid=' . $pid;

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=posts' . $pidLink );
	}

	function add()
	{
		$mainframe	= JFactory::getApplication();

		$mainframe->redirect( 'index.php?option=com_easydiscuss&controller=posts&task=edit' );
	}

	function cancelSubmit()
	{
		$source	= JRequest::getVar('source', 'posts');
		$pid	= JRequest::getString( 'parent_id' , '' , 'POST' );

		$pidLink = '';
		if(! empty($pid))
			$pidLink = '&pid=' . $pid;

		$this->setRedirect( JRoute::_('index.php?option=com_easydiscuss&view=' . $source . $pidLink, false) );
	}

	function save()
	{
		$this->submit();
	}


	/**
	 * Reset the vote count to 0.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function resetVotes()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid' );

		foreach( $cid as $id )
		{
			$post = DiscussHelper::getTable( 'Post' );
			$post->load( $id );

			if( !$post->id )
			{
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_POST_RESET_VOTES_ERROR' ) , DISCUSS_QUEUE_ERROR );
				return $this->setRedirect( 'index.php?option=com_easydiscuss&view=posts' );
			}

			$post->resetVotes();
		}

		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_POST_RESET_VOTES_SUCCESS' ) , DISCUSS_QUEUE_SUCCESS );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=posts' );
	}

	/**
	 * update posts
	 */
	public function submit()
	{
		if( JRequest::getMethod() == 'POST' )
		{
			JRequest::checkToken('request') or jexit( 'Invalid Token' );

			$user = JFactory::getUser();

			// get all forms value
			$post	= JRequest::get( 'post' );

			// get id if available
			$id		= JRequest::getInt('id', 0);

			// get post parent id
			$parent	= JRequest::getInt('parent_id', 0);

			// the source where page come from
			$source	= JRequest::getVar('source', 'posts');

			// Get raw content from request as we may need to respect the html codes.
			$content	= JRequest::getVar( 'dc_reply_content' , '' , 'post' , 'none' , JREQUEST_ALLOWRAW );

			// Ensure that the posted content is respecting the correct values.
			$post[ 'dc_reply_content' ]	= $content;

			// get config
			$config	= DiscussHelper::getConfig();

			$post['alias']	= (empty($post['alias']))? DiscussHelper::getAlias( $post['title'], 'post', $id) : DiscussHelper::getAlias( $post['alias'], 'post', $id );

			//clear tags if editing a post.
			$previousTags = array();
			if(!empty($id))
			{
				$postsTagsModel = $this->getModel('PostsTags');

				$tmppreviousTags = $postsTagsModel->getPostTags( $id );
				if(!empty($tmppreviousTags))
				{
					foreach($tmppreviousTags as $previoustag)
					{
						$previousTags[] = $previoustag->id;
					}
				}

				$postsTagsModel->deletePostTag( $id );
			}

			// bind the table
			$postTable		= JTable::getInstance( 'posts', 'Discuss' );
			$postTable->load( $id );

			//get previous post status before binding.
			$prevPostStatus = $postTable->published;

			$postTable->bind( $post , true );

			// hold last inserted ID in DB
			$lastId = null;

			// @rule: Bind parameters
			$postTable->bindParams( $post );


			if ($config->get('main_private_post') && isset($post['private'])) {

				$postTable->private 	= $post['private'];
			}

			// @trigger: onBeforeSave
			$isNew	= (bool) $postTable->id;
			DiscussEventsHelper::importPlugin( 'content' );
			DiscussEventsHelper::onContentBeforeSave('post', $post, $isNew);

			if ( !$postTable->store() )
			{
				JError::raiseError(500, $postTable->getError() );
			}

			//Clear off previous records before storing
			$ruleModel = DiscussHelper::getModel( 'CustomFields' );
			$ruleModel->deleteCustomFieldsValue( $postTable->id, 'update' );

			// Process custom fields.
			$fieldIds = JRequest::getVar( 'customFields' );
			if( !empty($fieldIds) )
			{
				foreach( $fieldIds as $fieldId )
				{
					$fields	= JRequest::getVar( 'customFieldValue_'.$fieldId );

					if( !empty($fields) )
					{
						// Cater for custom fields select list
						// To detect if there is no value selected for the select list custom fields
						if( in_array( 'defaultList', $fields ) )
						{
							$tempKey = array_search( 'defaultList', $fields );
							$fields[ $tempKey ] = '';
						}
					}

					$postTable->bindCustomFields( $fields, $fieldId );
				}
			}

			// @trigger: onAfterSave
			DiscussEventsHelper::onContentAfterSave('post', $post, $isNew);


			// The category_id for the replies should change too
			$postTable->moveChilds( $postTable->id, $postTable->category_id );

			$lastId		= $postTable->id;

			// Bind file attachments
			$postTable->bindAttachments();

			$message	= JText::_( 'COM_EASYDISCUSS_POST_SAVED' );

			$date = DiscussHelper::getDate();

			//@task: Save tags
			$tags			= JRequest::getVar( 'tags' , '' , 'POST' );

			if( !empty( $tags ) )
			{
				$tagModel	= $this->getModel( 'Tags' );

				foreach ( $tags as $tag )
				{
					if ( !empty( $tag ) )
					{
						$tagTable	= JTable::getInstance( 'Tags' , 'Discuss' );

						//@task: Only add tags if it doesn't exist.
						if( !$tagTable->exists( $tag ) )
						{
							$tagInfo['title']		= JString::trim( $tag );
							$tagInfo['alias']		= DiscussHelper::getAlias( $tag, 'tag' );
							$tagInfo['created']		= $date->toMySQL();
							$tagInfo['published']	= 1;
							$tagInfo['user_id']		= $user->id;

							$tagTable->bind($tagInfo);
							$tagTable->store();

						}
						else
						{
							$tagTable->load( $tag , true );
						}

						$postTagInfo = array();

						//@task: Store in the post tag
						$postTagTable	= JTable::getInstance( 'PostsTags' , 'Discuss' );
						$postTagInfo['post_id']	= $postTable->id;
						$postTagInfo['tag_id']	= $tagTable->id;

						$postTagTable->bind( $postTagInfo );
						$postTagTable->store();
					}
				}
			}

			$isNew  = ( empty($id) ) ? true : false;
			if( ( $isNew || $prevPostStatus == DISCUSS_ID_PENDING ) && $postTable->published == DISCUSS_ID_PUBLISHED )
			{
				$owner = ( $isNew ) ? $user->id : $postTable->user_id;
				DiscussHelper::sendNotification( $postTable , $parent, $isNew, $owner, $prevPostStatus);

				// auto subscription
				if( $config->get('main_autopostsubscription') && $config->get('main_postsubscription') && $postTable->user_type != 'twitter' && !empty($postTable->parent_id))
				{
					// process only if this is a reply
					//automatically subscribe this user into this reply
					$replier = JFactory::getUser($postTable->user_id);

					$subscription_info = array();
					$subscription_info['type']		= 'post';
					$subscription_info['userid']	= ( !empty($postTable->user_id) ) ? $postTable->user_id : '0';
					$subscription_info['email']		= ( !empty($postTable->user_id) ) ? $replier->email : $postTable->poster_email;;
					$subscription_info['cid']		= $postTable->parent_id;
					$subscription_info['member']	= ( !empty($postTable->user_id) ) ? '1':'0';
					$subscription_info['name']		= ( !empty($postTable->user_id) ) ? $replier->name : $postTable->poster_name;
					$subscription_info['interval']	= 'instant';

					//get frontend subscribe table
					$susbcribeModel	= DiscussHelper::getModel( 'Subscribe' );
					$sid = '';
					if( $subscription_info['userid'] == 0)
					{
						$sid = $susbcribeModel->isPostSubscribedEmail($subscription_info);
						if( empty( $sid ) )
						{
							$susbcribeModel->addSubscription($subscription_info);
						}
					}
					else
					{
						$sid = $susbcribeModel->isPostSubscribedUser($subscription_info);
						if( empty( $sid['id'] ))
						{
							//add new subscription.
							$susbcribeModel->addSubscription($subscription_info);
						}
					}
				}

				// only if the post is a discussion
				if( $config->get( 'integration_pingomatic' ) && empty( $postTable->parent_id ) )
				{
					$pingo	= DiscussHelper::getHelper( 'Pingomatic' );
					$urls	= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $postTable->id, true, true );
					$pingo->ping( $postTable->title,  $urls);
				}
			}

			$pid = '';
			if(! empty($parent))
				$pid = '&pid=' . $parent;

			$task = $this->getTask();

			switch( $task )
			{
				case 'apply':
					$redirect 	= 'index.php?option=com_easydiscuss&view=post&id=' . $postTable->id;
					break;
				case 'save':
					$redirect 	= 'index.php?option=com_easydiscuss&view=posts';
					break;
				case 'savePublishNew':
				default:
					$redirect 	= 'index.php?option=com_easydiscuss&view=post';
					break;
			}

			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_DISCUSSION_SAVED' ) , DISCUSS_QUEUE_SUCCESS );

			$this->setRedirect( $redirect );
		}
	}
}
