<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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

		$cid = $this->input->get('cid', '', 'array');
		$newCategoryId = $this->input->get('move_category');

		if (! $cid) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			return $this->setRedirect('index.php?option=com_easydiscuss&view=posts');
		}


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
			$post->move($newCategory->id);
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
	 * @since	4.0.10
	 * @access	public
	 */
	public function publish()
	{
		// Construct the default redirection link
		$redirect = 'index.php?option=com_easydiscuss&view=posts';

		// Determine where should we redirect the user back to
		$from = $this->input->get('from', '', 'word');

		if ($from) {
			$redirect .= '&layout=' . $from;
		}

		$posts = $this->input->get('cid', '', 'array');

		if (!$posts) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			return $this->app->redirect($redirect);			
		}

		// Try to unpublish these selected posts
		foreach ($posts as $item) {
			$post = ED::post($item);
			$post->publish(1);
		}

		$message = JText::_('COM_EASYDISCUSS_POSTS_PUBLISHED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		return $this->app->redirect($redirect);
	}

	/**
	 * Process the toggle unpublish.
	 *
	 * @since	4.0.10
	 * @access	public
	 */
	public function unpublish()
	{
		// Construct the default redirection link
		$redirect = 'index.php?option=com_easydiscuss&view=posts';

		// Determine where should we redirect the user back to
		$from = $this->input->get('from', '', 'word');

		if ($from) {
			$redirect .= '&layout=' . $from;
		}

		// Get the list of posts to unpublish
		$posts = $this->input->get('cid', '', 'array');

		if (!$posts) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			return $this->app->redirect($redirect);
		}

		// Try to unpublish each posts
		foreach ($posts as $item) {
			$post = ED::post($item);
			$post->publish('0');
		}


		$message = JText::_('COM_EASYDISCUSS_POSTS_UNPUBLISHED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		$this->app->redirect($redirect);
	}

	public function edit()
	{
		JRequest::setVar( 'view', 'post' );
		JRequest::setVar( 'id' , JRequest::getVar( 'id' , '' , 'REQUEST' ) );
		JRequest::setVar( 'pid' , JRequest::getVar( 'pid' , '' , 'REQUEST' ) );
		JRequest::setVar( 'source' , 'posts' );

		parent::display();
	}

	public function addNew()
	{
		JRequest::setVar('view', 'post');
		parent::display();
	}

	/**
	 * Remove discussions from the site.
	 *
	 * @since	4.0.10
	 * @access	public
	 */
	public function remove()
	{
		// Construct the default redirection link
		$redirect = 'index.php?option=com_easydiscuss&view=posts';

		// Determine where should we redirect the user back to
		$from = $this->input->get('from', '', 'word');

		if ($from) {
			$redirect .= '&layout=' . $from;
		}

		$posts = $this->input->get('cid' , array(), 'POST');

		if (!$posts) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			return $this->app->redirect($redirect);
		}

		$model = ED::model('Post');

		foreach ($posts as $item) {
			$post = ED::post($item);
			$post->delete();
		}

		$message = (empty($pid)) ? JText::_('COM_EASYDISCUSS_POSTS_DELETED') : JText::_('COM_EASYDISCUSS_REPLIES_DELETED');

		ED::setMessageQueue($message, DISCUSS_QUEUE_SUCCESS);

		return $this->app->redirect($redirect);
	}

	public function add()
	{
		$this->app->redirect('index.php?option=com_easydiscuss&view=post');
	}

	public function cancelSubmit()
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
	 */
	public function save()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the id if available
		$id = $this->input->get('id', 0, 'int');

		// Get the date POST
		$data = JRequest::get('post');

		// Load the post library
		$post = ED::post($id);

		$isNew = $post->isNew();

		// Get the redirect URL
		$redirectUrl = 'index.php?option=com_easydiscuss&view=post';

		if (!$isNew) {
			$redirectUrl = 'index.php?option=com_easydiscuss&view=post&layout=edit&id=' . $post->id;
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

		// Bind the posted data for saving
		$post->bind($data, false, false, true);

		// Validate the posted data to ensure that we can really proceed
		if (!$post->validate($data)) {

			$files = $this->input->get('filedata', array(), 'FILES');
			$data['attachments'] = $files;

			ED::storeSession($data, 'NEW_POST_TOKEN');
			ED::setMessage($post->getError(), 'error');

			return $this->app->redirect($redirectUrl);
		}

		// Save
		// Need to check all the error and make sure it is standardized
		if (!$post->save()) {
			ED::setMessage($post->getError(), 'error');
			return $this->app->redirect($redirectUrl);
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

				if ($post->isReply()) {
					$redirect .= '&layout=replies';
				}

				break;
			case 'savePublishNew':
			default:
				$redirect = 'index.php?option=com_easydiscuss&view=post';
				break;
		}

		$this->app->redirect($redirect);
	}

	public function approve()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the id if available
		$id = $this->input->get('id', 0, 'int');

		// Get the date POST
		$data = JRequest::get('post');

		// For contents, we need to get the raw data.
		$data['content'] = $this->input->get('dc_content', '', 'raw');	    

		// Load the post library
		$post = ED::post($id);

		// Bind post data
		$post->bind($data);

		// Validate the posted data to ensure that we can really proceed
		if (!$post->validate($data)) {

			$redirectUrl = 'index.php?option=com_easydiscuss&view=post&layout=pending&id=' . $post->id;

			ED::setMessage($post->getError(), 'error');

			return $this->app->redirect($redirectUrl);
		}

		// Reset previous publish state to moderated before publishing. #168
		$post->post->published = DISCUSS_ID_PENDING;

		// Toggle publish state
		$post->publish(1);

		$message = JText::_('COM_EASYDISCUSS_POSTS_PUBLISHED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		$redirect = 'index.php?option=com_easydiscuss&view=posts';

		$this->app->redirect($redirect);
	}

	public function reject()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the list of posts to unpublish
		$ids = $this->input->get('cid', '', 'array');

		// Get the id if available
		$id = $this->input->get('id', 0, 'int');		

		if (!$ids && !$id) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			return $this->app->redirect($redirect);
		}

		// Multiple rejection
		if ($ids) {
			// Try to reject each posts
			foreach ($ids as $id) {
				$post = ED::post($id);
				$post->publish(0, true);
			}			
		} else {
			$post = ED::post($id);
			$post->publish(0, true);
		}

		// Construct the default redirection link
		$redirect = 'index.php?option=com_easydiscuss&view=posts';

		// Determine where should we redirect the user back to
		$from = $this->input->get('from', '', 'word');

		if ($from) {
			$redirect .= '&layout=' . $from;
		}	    

		$message = JText::_('COM_EASYDISCUSS_POSTS_UNPUBLISHED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		$this->app->redirect($redirect);		
	}

	/**
	 * Locks a post
	 *
	 * @since	4.0.17
	 * @access	public
	 */
	public function lock()
	{
		ED::checkToken();

		// Construct the default redirection link
		$redirect = 'index.php?option=com_easydiscuss&view=posts';

		// Get the list of posts to unpublish
		$posts = $this->input->get('cid', '', 'array');

		if (!$posts) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			return $this->app->redirect($redirect);
		}

		// Try to unpublish each posts
		foreach ($posts as $item) {
			$post = ED::post($item);
			$post->lock();
		}


		$message = JText::_('COM_EASYDISCUSS_POSTS_LOCKED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		$this->app->redirect($redirect);	
	}

	/**
	 * Unlocks a post
	 *
	 * @since	4.0.17
	 * @access	public
	 */
	public function unlock()
	{
		ED::checkToken();

		// Construct the default redirection link
		$redirect = 'index.php?option=com_easydiscuss&view=posts';

		// Get the list of posts to unpublish
		$posts = $this->input->get('cid', '', 'array');

		if (!$posts) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			return $this->app->redirect($redirect);
		}

		// Try to unpublish each posts
		foreach ($posts as $item) {
			$post = ED::post($item);
			$post->unlock();
		}


		$message = JText::_('COM_EASYDISCUSS_POSTS_UNLOCKED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		$this->app->redirect($redirect);	
	}

	/**
	 * Deletes posts from the back end
	 *
	 * @since	4.1.10
	 * @access	public
	 */
	public function delete()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the id if available
		$id = $this->input->get('id', 0, 'int');

		// Get the date POST
		$data = JRequest::get('post');;

		// Load the post library
		$post = ED::post($id);

		// Toggle publish state
		$post->delete();
		
		$message = JText::_('COM_EASYDISCUSS_POSTS_DELETED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		$redirect = 'index.php?option=com_easydiscuss&view=posts';

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

		// Construct the default redirection link
		$redirect = 'index.php?option=com_easydiscuss&view=posts';

		// Determine where should we redirect the user back to
		$from = $this->input->get('from', '', 'word');

		if ($from) {
			$redirect .= '&layout=' . $from;
		}

		$cid = $this->input->get('cid');

		foreach ($cid as $id) {
			$post = ED::Post($id);

			if (!$post->id) {
				ED::setMessageQueue(JText::_('COM_EASYDISCUSS_POST_RESET_VOTES_ERROR'), DISCUSS_QUEUE_ERROR);
				return $this->app->redirect($redirect);
			}

			$post->resetVotes();
		}

		ED::setMessageQueue(JText::_('COM_EASYDISCUSS_POST_RESET_VOTES_SUCCESS'), DISCUSS_QUEUE_SUCCESS);

		return $this->app->redirect($redirect);
	}
}
