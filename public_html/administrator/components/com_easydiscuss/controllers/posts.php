<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
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
		$this->registerTask('unpublish', 'unpublish');
		$this->registerTask('updateAuthor', 'updateAuthor');
		$this->registerTask('copy', 'duplicate');
	}

	/**
	 * Update the author of the posts
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function updateAuthor()
	{
		$cid = $this->input->get('cid', array(), 'array');
		$authorId = $this->input->get('authorId', 0, 'int');
		$redirect = 'index.php?option=com_easydiscuss&view=posts';
		$isAjax = $this->doc->getType() == 'ajax' ? true : false;

		if (empty($cid) || !$authorId) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			if ($isAjax) {
				return $this->ajax->resolve($redirect);
			}

			return ED::redirect($redirect);
		}

		$actionlog = ED::actionlog();
		$author = ED::user($authorId);

		foreach ($cid as $id) {

			$post = ED::post($id);
			$post->updateAuthor($author->id);

			$actionlogMsg = $actionlog->normalizeActionLogConstants($post->isReply(), 'COM_ED_ACTIONLOGS_UPDATE_AUTHOR_POST');
       		$actionlogPostTitle = $actionlog->normalizeActionLogPostTitle($post);
       		$actionlogPostPermalink = $actionlog->normalizeActionLogPostPermalink($post);

			$actionlog->log($actionlogMsg, 'post', array(
				'link' => $actionlogPostPermalink,
				'postTitle' => $actionlogPostTitle,
				'authorName' => $author->getName(),
				'authorLink' => 'index.php?option=com_easydiscuss&view=users&layout=form&id=' . $author->id
			));
		}

		$message = JText::_('COM_ED_UPDATE_POST_AUTHOR_SUCCESS_MESSAGE');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		if ($isAjax) {
			return $this->ajax->resolve($redirect);
		}

		return ED::redirect($redirect);
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
			ED::setMessage(JText::_('COM_EASYDISCUSS_PLEASE_SELECT_CATEGORY'), DISCUSS_QUEUE_ERROR);
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

		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

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
	 * Process the duplication of the posts
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function duplicate()
	{
		$ids = $this->input->get('cid', [], 'array');
		$redirect = 'index.php?option=com_easydiscuss&view=posts';

		if (!$ids) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			return ED::redirect($redirect);
		}

		foreach ($ids as $id) {
			if (!is_numeric($id)) {
				continue;
			}

			$post = ED::post($id);
			$post->duplicate();
		}

		$message = JText::_('COM_ED_DUPLICATE_POST_SUCCESS_MESSAGE');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		return ED::redirect($redirect);
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

		$posts = $this->input->get('cid', array(), 'array');

		if (!$posts) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			return ED::redirect($redirect);			
		}

		$hashkey = ED::table('HashKeys');

		// log the current action into database.
		$actionlog = ED::actionlog();

		// Try to unpublish these selected posts
		foreach ($posts as $item) {
			$post = ED::post($item);

			if ($post->isPending()) {
				$state = $hashkey->load(array('uid' => $post->id));

				if ($state) {
					$hashkey->delete();
				}
			}

			$post->publish(1);

			$actionlogMsg = $actionlog->normalizeActionLogConstants($post->isReply(), 'COM_ED_ACTIONLOGS_PUBLISHED_POST');
       		$actionlogPostPermalink = $actionlog->normalizeActionLogPostPermalink($post);

			$actionlog->log($actionlogMsg, 'post', array(
				'link' => $actionlogPostPermalink
			));
		}

		$message = JText::_('COM_EASYDISCUSS_POSTS_PUBLISHED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		return ED::redirect($redirect);
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
		$posts = $this->input->get('cid', array(), 'array');

		if (!$posts) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			return ED::redirect($redirect);
		}

		// log the current action into database.
		$actionlog = ED::actionlog();

		// Try to unpublish each posts
		foreach ($posts as $item) {
			$post = ED::post($item);
			$post->unpublish();

			$actionlogMsg = $actionlog->normalizeActionLogConstants($post->isReply(), 'COM_ED_ACTIONLOGS_UNPUBLISHED_POST');
       		$actionlogPostPermalink = $actionlog->normalizeActionLogPostPermalink($post);

			$actionlog->log($actionlogMsg, 'post', array(
				'link' => $actionlogPostPermalink
			));			
		}

		$message = JText::_('COM_EASYDISCUSS_POSTS_UNPUBLISHED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		ED::redirect($redirect);
	}

	public function edit()
	{
		$this->input->set( 'view', 'post' );
		$this->input->set( 'id' , $this->input->get('id', 0,'int' ) );
		$this->input->set( 'pid' , $this->input->get('pid', 0, 'int' ) );
		$this->input->set( 'source' , 'posts' );

		parent::display();
	}

	public function addNew()
	{
		$this->input->set('view', 'post');
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

		$posts = $this->input->get('cid' , array(), 'array');

		if (!$posts) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			return ED::redirect($redirect);
		}

		$model = ED::model('Post');

		foreach ($posts as $item) {
			$post = ED::post($item);
			$post->delete();
		}

		$message = (empty($pid)) ? JText::_('COM_EASYDISCUSS_POSTS_DELETED') : JText::_('COM_EASYDISCUSS_REPLIES_DELETED');

		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		return ED::redirect($redirect);
	}

	public function add()
	{
		ED::redirect('index.php?option=com_easydiscuss&view=post');
	}

	public function cancelSubmit()
	{
		$source	= $this->input->get('source', '', 'string');
		$pid = $this->input->get('parent_id' ,0 , 'int');

		$pidLink = '';
		if(! empty($pid))
			$pidLink = '&pid=' . $pid;

		$this->setRedirect( JRoute::_('index.php?option=com_easydiscuss&view=' . $source . $pidLink, false) );
	}

	public function approve()
	{
		// Check for request forgeries
		ED::checkToken();

		// log the current action into database.
		$actionlog = ED::actionlog();

		$ids = $this->input->get('ids', '', 'array');

		// Get the id if available
		$id = $this->input->get('id', 0, 'int');

		if ($id) {
			$ids = array($id);
		}

		// Construct the default redirection link
		$redirect = 'index.php?option=com_easydiscuss&view=posts';

		// Determine where should we redirect the user back to
		$from = $this->input->get('from', '', 'word');

		if ($from) {
			$redirect .= '&layout=' . $from;
		}

		foreach ($ids as $postId) {
			// Load the post library
			$post = ED::post($postId);

			// Validate the posted data to ensure that we can really proceed
			if (!$from) {
				// Get the date POST
				$data = $this->input->post->getArray();

				// For contents, we need to get the raw data.
				$data['content'] = $this->input->get('dc_content', '', 'raw');

				// Bind post data
				$post->bind($data);

				if (!$post->validate($data)) {
					ED::setMessage($post->getError(), ED_MSG_ERROR);
					return ED::redirect($redirect);
				}
			}

			// Reset previous publish state to moderated before publishing. #168
			$post->post->published = DISCUSS_ID_PENDING;

			// Toggle publish state
			$post->publish(1);

			$actionlogMsg = $actionlog->normalizeActionLogConstants($post->isReply(), 'COM_ED_ACTIONLOGS_APPROVED_POST');
       		$actionlogPostPermalink = $actionlog->normalizeActionLogPostPermalink($post);

			$actionlog->log($actionlogMsg, 'post', array(
				'link' => $actionlogPostPermalink
			));
		}
		
		$message = JText::_('COM_EASYDISCUSS_POSTS_PUBLISHED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		ED::redirect($redirect);
	}

	public function reject()
	{
		// Check for request forgeries
		ED::checkToken();

		// log the current action into database.
		$actionlog = ED::actionlog();

		// Get the list of posts to unpublish
		$ids = $this->input->get('ids', '', 'array');

		// // Get the id if available
		// $id = $this->input->get('id', 0, 'int');

		// if ($id) {
		// 	$ids = array($id);
		// }

		// Construct the default redirection link
		$redirect = 'index.php?option=com_easydiscuss&view=posts';

		// Determine where should we redirect the user back to
		$from = $this->input->get('from', '', 'word');

		if ($from) {
			$redirect .= '&layout=' . $from;
		}

		if (!$ids && !$id) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			return ED::redirect($redirect);
		}

		$hashkey = ED::table('HashKeys');

		// Multiple rejection
		if ($ids) {
			// Try to reject each posts
			foreach ($ids as $id) {
				$post = ED::post($id);

				if ($post->isPending()) {
					$state = $hashkey->load(array('uid' => $post->id));

					if ($state) {
						$hashkey->delete();
					}
				}

				$post->publish(0, true);

				$actionlogMsg = $actionlog->normalizeActionLogConstants($post->isReply(), 'COM_ED_ACTIONLOGS_REJECTED_POST');
	       		$actionlogPostPermalink = $actionlog->normalizeActionLogPostPermalink($post);

				$actionlog->log($actionlogMsg, 'post', array(
					'link' => $actionlogPostPermalink
				));
			}
		}

		$message = JText::_('COM_ED_POSTS_REJECTED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		ED::redirect($redirect);
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
		$posts = $this->input->get('cid', array(), 'array');

		if (!$posts) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			return ED::redirect($redirect);
		}

		// Try to unpublish each posts
		foreach ($posts as $item) {
			$post = ED::post($item);
			$post->lock();
		}


		$message = JText::_('COM_EASYDISCUSS_POSTS_LOCKED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		ED::redirect($redirect);	
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
		$posts = $this->input->get('cid', array(), 'array');

		if (!$posts) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			ED::setMessage($message, DISCUSS_QUEUE_ERROR);

			return ED::redirect($redirect);
		}

		// Try to unpublish each posts
		foreach ($posts as $item) {
			$post = ED::post($item);
			$post->unlock();
		}

		$message = JText::_('COM_EASYDISCUSS_POSTS_UNLOCKED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		ED::redirect($redirect);	
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
		$data = $this->input->post->getArray();

		// Load the post library
		$post = ED::post($id);

		// Toggle publish state
		$post->delete();
		
		$message = JText::_('COM_EASYDISCUSS_POSTS_DELETED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		$redirect = 'index.php?option=com_easydiscuss&view=posts';

		ED::redirect($redirect);
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

		$cid = $this->input->get('cid', array(), 'array');

		foreach ($cid as $id) {
			$post = ED::Post($id);

			if (!$post->id) {
				ED::setMessage(JText::_('COM_EASYDISCUSS_POST_RESET_VOTES_ERROR'), DISCUSS_QUEUE_ERROR);
				return ED::redirect($redirect);
			}

			$post->resetVotes();
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_POST_RESET_VOTES_SUCCESS'), DISCUSS_QUEUE_SUCCESS);

		return ED::redirect($redirect);
	}

	/**
	 * Deletes a list of provided points
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function removeHoneypotLog()
	{
		ED::checkToken();

		$ids = $this->input->get('cid', array(), 'int');

		if (!$ids) {
			return $this->view->exception('Invalid Id provided');
		}

		foreach ($ids as $id) {
			$table = ED::table('Honeypot');
			$table->load((int) $id);

			$table->delete();
		}

		ED::setMessage('COM_ED_HONEYPOT_LOG_DELETED_SUCCESSFULLY', 'success');
		return ED::redirect('index.php?option=com_easydiscuss&view=posts&layout=honeypot');
	}

	/**
	 * Publishes a point
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function purge()
	{
		ED::checkToken();

		$model = ED::model('Honeypot');
		$model->purge();

		ED::setMessage('COM_ED_HONEYPOT_LOGS_PURGED', 'success');
		return ED::redirect('index.php?option=com_easydiscuss&view=posts&layout=honeypot');
	}
}
