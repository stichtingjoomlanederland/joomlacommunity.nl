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

defined('_JEXEC') or die('Restricted access');

class EasyDiscussControllerReports extends EasyDiscussController
{
    public function __construct()
    {
        parent::__construct();

        $this->checkAccess('discuss.manage.reports');
    }

	public function publish()
	{
		$posts = $this->input->get('cid', array(), 'array');
		$message = '';
		$type = 'success';

		if (count($posts) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type = 'error';
		} else {
			$model = ED::model('Reports');

			if ($model->publishPost($posts , 1)) {
				$message = JText::_('COM_EASYDISCUSS_POST_PUBLISHED');
			} else {
				$message = JText::_('COM_EASYDISCUSS_ERROR_PUBLISHING_POST');
				$type = 'error';
			}
		}

		ED::setMessage($message, $type);
		$this->app->redirect('index.php?option=com_easydiscuss&view=reports');
	}

	public function unpublish()
	{
		$posts = $this->input->get('cid', array(), 'array');
		$message = '';
		$type = 'success';

		if (count($posts) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type = 'error';
		} else {
			$model = ED::model('Reports');

			if ($model->publishPost($posts , 0)) {
				$message = JText::_('COM_EASYDISCUSS_POST_UNPUBLISHED');
			} else {
				$message = JText::_('COM_EASYDISCUSS_ERROR_UNPUBLISHING_POST');
				$type = 'error';
			}
		}

		ED::setMessage($message, $type);
		$this->app->redirect('index.php?option=com_easydiscuss&view=reports');
	}

	/**
	 * Publish/unpublish post.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function togglePublish()
	{
		$postId = $this->input->get('post_id', array(), 'array');
		$postVal = $this->input->get('post_val', 0, 'int');

		$model = ED::model('Reports');
		$message = '';
		$type = 'success';

		if (empty($postId)) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type = 'error';
		}

		if ($postVal && !empty($postId)) {
			if ($model->publishPost($postId , 1)) {
				$message = JText::_('COM_EASYDISCUSS_POST_PUBLISHED');
			} else {
				$message = JText::_('COM_EASYDISCUSS_ERROR_PUBLISHING_POST');
				$type = 'error';
			}
		} else {
			if ($model->publishPost($postId, 0)) {
				$message = JText::_('COM_EASYDISCUSS_POST_UNPUBLISHED');
			} else {
				$message = JText::_('COM_EASYDISCUSS_ERROR_UNPUBLISHING_POST');
				$type = 'error';
			}
		}

		ED::setMessage($message, $type);
		$this->app->redirect('index.php?option=com_easydiscuss&view=reports');
	}

	/**
	 * Remove reports of a discussion.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeReports()
	{
		$postId = $this->input->get('post_id', 0, 'int');

		$model = ED::model('Reports');
		$message = '';
		$type = 'success';

		if (empty($postId)) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type = 'error';
		} else {
			$model->removeReports($postId);
			$message = JText::_('COM_EASYDISCUSS_REPORT_ABUSE_REMOVED');
		}

		ED::setMessage($message, $type);
		$this->app->redirect('index.php?option=com_easydiscuss&view=reports');
	}

	public function edit()
	{
		$id = $this->input->getInt('id', 0);

		$this->input->set('view', 'post');
		$this->input->set('id', $id);
		$this->input->set('source', 'reports');

		parent::display();
	}

	public function remove()
	{
		$post = $this->input->get('cid', array(0), 'POST');
		$post = $this->get('cid', array(), 'ARRAY');

		$message = '';
		$type = 'success';

		if (count($post) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type = 'error';
		} else {
			$model = ED::model('Reports');

			for ($i = 0; $i < count($post); $i++) {
				$pid = $post[$i];
				$model->removePostReports($pid);
			}

			$message = JText::_('COM_EASYDISCUSS_POST_DELETED');
		}

		ED::setMessage($message, $type);
		$this->app->redirect('index.php?option=com_easydiscuss&view=reports');
	}

	public function deletePost()
	{
		$id = $this->input->get('post_id', 0, 'int');

		if (!$id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_POST_ID'), 'error');
			return $this->app->redirect('index.php?option=com_easydiscuss&view=reports');
		}
		
		$model = ED::model('Reports');
		$status = $model->removePostReports($id);

		// Let the post library handle the delete post.
		if ($status) {
			$post = ED::post($id);
			$status = $post->delete();
		}

		$message = JText::_('COM_EASYDISCUSS_POST_DELETED');
		ED::setMessage(JText::_('COM_EASYDISCUSS_POST_DELETED'), 'success');

		$this->app->redirect('index.php?option=com_easydiscuss&view=reports');
	}
}
