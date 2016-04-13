<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussControllerPosts extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.posts');

		$this->registerTask('unfeature', 'toggleFeatured');
		$this->registerTask('feature', 'toggleFeatured');
		$this->registerTask('savePublishNew', 'save');
		$this->registerTask('apply', 'save');
		$this->registerTask('save', 'save');
		$this->registerTask('unpublish', 'unpublish');
	}

	public function movePosts()
	{
		// Check for request forgeries
		ED::checkToken();

		$cid = $this->input->get('cid');
		$newCategoryId = $this->input->get('move_category');
		$newCategory = ED::Category($newCategoryId);


		if (!$newCategoryId || !$newCategory->id) {
			ED::setMessageQueue(JText::_('COM_EASYDISCUSS_PLEASE_SELECT_CATEGORY'), DISCUSS_QUEUE_ERROR);
			return $this->setRedirect('index.php?option=com_easydiscuss&view=posts');
		}

		if (!is_array($cid)) {
			$cid = array($cid);
		}

		foreach ($cid as $id) {
			$post = ED::post($id);

			$post->category_id = $newCategory->id;
			$post->save();

			// The category_id for the replies should change too
			$post->moveReplies($post->id, $post->category_id);
		}

		$message = JText::sprintf('COM_EASYDISCUSS_POSTS_MOVED_SUCCESSFULLY', $newCategory->title);

		ED::setMessageQueue($message, DISCUSS_QUEUE_SUCCESS);

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=posts' );
	}

	/**
	 * Process the toggle featured.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function toggleFeatured()
	{
		$app = $this->app;
		$records = $this->input->get('cid', '', 'array');
		$message = '';
		$task = $this->input->get('task');

		if ($records) {
			foreach ($records as $record) {
				$post = ED::Post($record);

				// Toggle the feature for this post.
				$task = $post->featured ? 'unfeature' : 'feature';

				// Run the task
				$post->$task();
			}

			$message = JText::_('COM_EASYDISCUSS_DISCUSSIONS_FEATURED');

			if (!$post->featured) {
				$message = JText::_('COM_EASYDISCUSS_DISCUSSIONS_UNFEATURED');
			}

			ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		} else {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);
		}

		$app->redirect('index.php?option=com_easydiscuss&view=posts');
		$app->close();
	}

	/**
	 * Process the toggle publish.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function publish()
	{
		$config = $this->config;
		$items = $this->input->get('cid', '', 'array');
		$pid = $this->input->get('pid', '');
		$message = '';

		if (count($items) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);
		} else {
			// @task: Tell the world that we're publishing post by sending notification.
			foreach ($items as $item) {
				$post = ED::table('Posts');
				$post->load($item);

				if ($post->published == DISCUSS_ID_PENDING) {
					$callback = EDR::getRoutedUrl('index.php?option=com_easydiscuss&view=post&id=' . $post->id, false, true);
					$sites = array('facebook', 'twitter');

					foreach ($sites as $site) {
						if ($config->get('main_autopost_' . $site)) {
							$oauth = ED::table('Oauth');
							$exists = $oauth->loadByType($site);

							$oauthPost = ED::table('OauthPosts');

							if ($exists && !empty($oauth->access_token) && !$oauthPost->exists($post->id, $$oauth->id)) {
								$consumer = ED::getHelper('Oauth')->getConsumer($site, $config->get('main_autopost_' . $site . '_id'), $config->get('main_autopost_' . $site . '_secret'), $callback);
								$consumer->setAccess($oauth->access_token);

								$consumer->share($post);

								// @rule: Store this as sent.
								$oauthPost->set('post_id', $post->id);
								$oauthPost->set('oauth_id', $oauth->id);

								$oauthPost->store();
							}
						}
					}

					// @rule: Send out notification when the pending moderation items are being published.
					ED::sendNotification($post, $post->parent_id, true, $post->user_id, $post->published);

					if ($config->get('integration_pingomatic') && empty($post->parent_id)) {
						$pingo = ED::getHelper('Pingomatic');
						$pingo->ping($post->title, EDR::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $post->id, true, true));
					}

					$user = JFactory::getUser($post->user_id);

					// @task: Add activity stream into the following extensions
					if ($post->parent_id) {
						ED::getHelper('jomsocial')->addActivityReply($post);
						ED::getHelper('easysocial')->replyDiscussionStream($post);
					} else {
						ED::getHelper('jomsocial')->addActivityQuestion($post);
						ED::getHelper('easysocial')->createDiscussionStream($post);
					}

					if ($user->id) {
						// Add logs for user
						ED::getHelper('History')->log('easydiscuss.new.discussion', $user->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_NEW_POST', $post->title), $post->id);

						// Add badges and points for user
						ED::getHelper('Badges')->assign('easydiscuss.new.discussion', $user->id);
						ED::getHelper('Points')->assign('easydiscuss.new.discussion', $user->id);

						// Assign badge for EasySocial
						ED::getHelper('easysocial')->assignBadge('create.question', $user->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_NEW_POST', $post->title));

						// Assign new ranks
						ED::getHelper('ranks')->assignRank($user->id, $config->get('main_ranking_calc_type'));

						// Assign AUP
						ED::getHelper('Aup')->assign(DISCUSS_POINTS_NEW_DISCUSSION, $user->id, $post->title);
					}
				}
			}

			$model = ED::model('Posts');
			$published = $model->publishPosts($items, 1);

			if ($published) {
				$message = JText::_('COM_EASYDISCUSS_POSTS_PUBLISHED');
				ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);
			} else {
				$message = JText::_('COM_EASYDISCUSS_ERROR_PUBLISHING');
				ED::setMessage($message, DISCUSS_QUEUE_ERROR);
			}
		}

		$pidLink = '';

		if (!empty($pid)) {
			$pidLink = '&pid=' . $pid;
		}

		$this->setRedirect('index.php?option=com_easydiscuss&view=posts' . $pidLink);
	}

	/**
	 * Process the toggle unpublish.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unpublish()
	{
		$posts = $this->input->get('cid', '', 'array');
		$pid = $this->input->get('pid', '');
		$message = '';

		if (count($posts) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);
		} else {
			$model = ED::model('Posts');
			$unpublish = $model->publishPosts($posts, 0);

			if ($unpublish) {
				$message = JText::_('COM_EASYDISCUSS_POSTS_UNPUBLISHED');
				ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);
			} else {
				$message = JText::_('COM_EASYDISCUSS_ERROR_UNPUBLISHING');
				ED::setMessage($message, DISCUSS_QUEUE_ERROR);
			}
		}

		$pidLink = '';

		if(! empty($pid)) {
			$pidLink = '&pid=' . $pid;
		}

		$this->setRedirect('index.php?option=com_easydiscuss&view=posts' . $pidLink);
	}

	function edit()
	{
		JRequest::setVar( 'view', 'post' );
		JRequest::setVar( 'id' , JRequest::getVar( 'id' , '' , 'REQUEST' ) );
		JRequest::setVar( 'pid' , JRequest::getVar( 'pid' , '' , 'REQUEST' ) );
		JRequest::setVar( 'source' , 'posts' );

		parent::display();
	}

	function addNew()
	{
		JRequest::setVar('view', 'post');
		parent::display();
	}

	/**
	 * Remove discussions from the site.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function remove()
	{
		$post = $this->input->get('cid' , array(), 'POST');
		$pid = $this->input->get('pid', '', 'default');

		$message = '';
		$type = 'success';

		if (count($post) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type = 'error';
		} else {

			$model = ED::model('Post');

			//check if any of the 'to be' remove entry was a answered reply.
			// If yes, revert the main post to unresolved.
			// if (! empty($pid)) {
			// 	// we know this is the replies.
			// 	$model->revertAnwered( $post );
			// }

			if ($post) {
				foreach ($post as $id) {
					// $discussion = DiscussHelper::getTable( 'Post' );
					// $discussion->load( $id );

					// // Delete all notification associated with this post
					// $notificationModel = ED::model('Notification');
					// $notificationModel->deleteNotifications( $id );

					// $discussion->delete();

					$post = ED::post($id);
					$post->delete();
				}

				$message = (empty($pid)) ? JText::_('COM_EASYDISCUSS_POSTS_DELETED') : JText::_('COM_EASYDISCUSS_REPLIES_DELETED');

				// @rule: Trigger AUP points
				if ( !empty($pid)) {
					ED::getHelper('Aup')->assign( DISCUSS_POINTS_DELETE_DISCUSSION , $post->user_id , $post->title);
				}

			} else {
				$message = (empty($pid)) ? JText::_('COM_EASYDISCUSS_ERROR_DELETING_POST') : JText::_('COM_EASYDISCUSS_ERROR_DELETING_REPLY');
				$type = 'error';
			}

		}

		$pidLink = '';
		if (! empty($pid)) {
			$pidLink = '&pid=' . $pid;
		}

		ED::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=posts' . $pidLink );
	}

	function add()
	{
		$this->app->redirect('index.php?option=com_easydiscuss&view=post');
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

	/**
	 * This occurs when the user tries to create a new discussion or edits an existing discussion
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function save()
	{
	    // Check for request forgeries
	    ED::checkToken();

	    // Get the id if available
	    $id = $this->input->get('id', 0, 'int');

	    // Get the date POST
	    $data = JRequest::get('post');;

	    // Load the post library
	    $post = ED::post($id);

	    $isNew = $post->isNew();

	    // Get the redirect URL
	    $redirectUrl = EDR::_('view=ask', false);

	    if (!$isNew) {
	        $redirectUrl = EDR::_('view=ask&id=' . $post->id, false);
	    }

	    // Check the permissions to post a new question
	    if (!$post->canPostNewDiscussion()) {
	        ED::setMessage($post->getError(), 'error');
	        return $this->app->redirect(EDR::_('', false));
	    }

	    // If this post is being edited, check for perssion if the user is able to edit or not.
	    if ($post->id && !$post->canEdit()) {
	        ED::setMessage($post->getError(), 'error');
	        return $this->app->redirect(EDR::_('view=post&id='.$id, false));
	    }

	    // For contents, we need to get the raw data.
	    $data['content'] = $this->input->get('dc_content', '', 'raw');

	    // Bind the posted data
	    $post->bind($data);

	    // Validate the posted data to ensure that we can really proceed
	    if (!$post->validate($data)) {

	        $files = $this->input->get('filedata', array(), 'FILES');
	        $data['attachments'] = $files;

	        ED::storeSession($data, 'NEW_POST_TOKEN');
	        ED::setMessage($post->getError(), 'error');

	        return $this->app->redirect(EDR::getAskRoute($redirectUrl, false));
	    }

	    // Save
	    // Need to check all the error and make sure it is standardized
	    if (!$post->save()) {
	        ED::setMessage($post->getError(), 'error');
	        return $this->app->redirect(EDR::getAskRoute($redirectUrl, false));
	    }

	    $message = ($isNew)? JText::_('COM_EASYDISCUSS_POST_STORED') : JText::_('COM_EASYDISCUSS_EDIT_SUCCESS');
	    $state = 'success';

	    // Let's set our custom message here.
	    if (!$post->isPending()){
	        ED::setMessageQueue($message, $state);
	    }

	    $redirect = $this->input->get('redirect', '');

	    if (!empty($redirect)) {
	        $redirect = base64_decode($redirect);
	        return $this->app->redirect($redirect);
	    }

	    $task = $this->getTask();

		switch($task) {
			case 'apply':
				$redirect = 'index.php?option=com_easydiscuss&view=post&layout=edit&id=' . $post->id;
				break;
			case 'save':
				$redirect = 'index.php?option=com_easydiscuss&view=posts';
				break;
			case 'savePublishNew':
			default:
				$redirect = 'index.php?option=com_easydiscuss&view=post';
				break;
		}

	    $this->app->redirect($redirect);
	}


	/**
	 * Reset the vote count to 0.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function resetVotes()
	{
		// Check for request forgeries
	    ED::checkToken();

		$cid = $this->input->get('cid');

		foreach ($cid as $id) {
			$post = ED::Post($id);

			if (!$post->id) {
				ED::setMessageQueue(JText::_('COM_EASYDISCUSS_POST_RESET_VOTES_ERROR'), DISCUSS_QUEUE_ERROR);
				return $this->setRedirect('index.php?option=com_easydiscuss&view=posts');
			}

			$post->resetVotes();
		}

		ED::setMessageQueue(JText::_('COM_EASYDISCUSS_POST_RESET_VOTES_SUCCESS'), DISCUSS_QUEUE_SUCCESS);

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=posts' );
	}
}
