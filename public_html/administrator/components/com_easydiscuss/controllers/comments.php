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

class EasyDiscussControllerComments extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.posts');

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
		$this->registerTask('apply', 'save');

	}

	/**
	 * Cancel the edit comment page
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function save()
	{
		ED::checkToken();

		// Get the comment id
		$id = $this->input->get('id', 0, 'int');
		$task = $this->getTask();

		$comment = ED::table('Comment');
		$comment->load($id);

		$redirect = 'index.php?option=com_easydiscuss&view=comments&layout=form&id=' . $comment->id;
		$post = $this->input->getArray('POST');

		$comment->bind($post);

		$state = $comment->store();

		if (!$state) {
			ED::setMessage($comment->getError(), ED_MSG_ERROR);
			return ED::redirect($redirect);
		}

		ED::setMessage('COM_ED_COMMENTS_EDIT_SAVED', 'success');

		if ($task == 'save') {
			$redirect = 'index.php?option=com_easydiscuss&view=comments';
		}

		// log the current action into database.
		$actionlog = ED::actionlog();

		$actionlog->log('COM_ED_ACTIONLOGS_UPDATED_COMMENT', 'comment', array(
			'link' => $redirect
		));

		return ED::redirect($redirect);
	}

	/**
	 * Cancel the edit comment page
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function cancel()
	{
		return ED::redirect('index.php?option=com_easydiscuss&view=comments');
	}

	/**
	 * Remove the comment from backend
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function remove()
	{
		ED::checkToken();

		$redirect = 'index.php?option=com_easydiscuss&view=comments';
		$ids = $this->input->get('cid', array(), 'default');

		if (!$ids) {
			ED::setMessage('COM_ED_INVALID_COMMENT_ID', ED_MSG_ERROR);
			return ED::redirect($redirect);
		}

		$table = ED::table('Comment');

		foreach ($ids as $id) {

			$comment = $table->load($id);

			$state = $table->delete();

			if (!$state) {
				ED::setMessage('COM_ED_COMMENT_REMOVE_ERROR', ED_MSG_ERROR);
				return ED::redirect($redirect);
			}

			// log the current action into database.
			$actionlog = ED::actionlog();

			$actionlog->log('COM_ED_ACTIONLOGS_DELETED_COMMENT', 'comment', array(
				'link' => 'index.php?option=com_easydiscuss&view=comments&layout=form&id=' . $id
			));
		}

		ED::setMessage('COM_ED_COMMENT_DELETED', 'success');
		return ED::redirect($redirect);
	}

	/**
	 * Publish/unpublish comment from backend
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function togglePublish()
	{
		ED::checkToken();

		// Get the current task
		$task = $this->getTask();

		$redirect = 'index.php?option=com_easydiscuss&view=comments';
		$ids = $this->input->get('cid', array(), 'default');

		if (!$ids) {
			ED::setMessage('COM_ED_INVALID_COMMENT_ID', ED_MSG_ERROR);
			return ED::redirect($redirect);
		}

		foreach ($ids as $id) {

			$comment = ED::table('Comment');
			$comment->load((int) $id);

			$comment->$task();
		}

		$message = 'COM_ED_COMMENT_PUBLISHED';

		if ($task == 'unpublish') {
			$message = 'COM_ED_COMMENT_UNPUBLISHED';
		}

		ED::setMessage($message, 'success');
		return ED::redirect($redirect);
	}
}
