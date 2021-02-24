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

class EasyDiscussControllerReports extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.reports');

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
	}

	/**
	 * Toggles the publishing state of the posts
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function togglePublish()
	{
		$cid = $this->input->get('cid', array(), 'int');
		$task = $this->getTask();

		if (!$cid) {
			die('Invalid id provided');
		}

		$message = JText::_('COM_EASYDISCUSS_POST_PUBLISHED');

		if ($task == 'unpublish') {
			$message = JText::_('COM_EASYDISCUSS_POST_UNPUBLISHED');
		}

		$model = ED::model('Reports');

		foreach ($cid as $id) {
			$id = (int) $id;
			$action = $task == 'publish' ? 'publish' : 'unpublish';

			$post = ED::post($id);
			$state = $post->$action();

			if ($state) {
				// log the current action into database.
				$actionlog = ED::actionlog();
				$action = strtoupper($action);

				$actionlogMsg = $actionlog->normalizeActionLogConstants($post->isReply(), 'COM_ED_ACTIONLOGS_REPORT_POST_' . $action);
				$actionlogPostTitle = $actionlog->normalizeActionLogPostTitle($post);
				$actionlogPostPermalink = $actionlog->normalizeActionLogPostPermalink($post);

				$actionlog->log($actionlogMsg, 'report', array(
					'link' => $actionlogPostPermalink,
					'postTitle' => $actionlogPostTitle
				));				
			}
		}

		ED::setMessage($message, 'success');
		ED::redirect('index.php?option=com_easydiscuss&view=reports');
	}

	/**
	 * Remove just the reports from the site
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function remove()
	{
		$cid = $this->input->get('cid', array(), 'int');

		if (!$cid) {
			die('Invalid id provided');
		}

		$model = ED::model('Reports');

		foreach ($cid as $id) {
			$id = (int) $id;

			// Remove all reports associated with the post
			$state = $model->removePostReports($id);

			if ($state) {
				// log the current action into database.
				$actionlog = ED::actionlog();
				$actionlog->log('COM_ED_ACTIONLOGS_REPORT_RECORD_DELETED', 'report');
			}		
		}

		ED::setMessage('COM_ED_SELECTED_REPORTS_DELETED', 'success');
		ED::redirect('index.php?option=com_easydiscuss&view=reports');
	}

	/**
	 * Delete posts associated with the reports
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function deletePosts()
	{
		$ids = $this->input->get('cid', 0, 'int');

		if (!$ids) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_POST_ID'), ED_MSG_ERROR);
			return ED::redirect('index.php?option=com_easydiscuss&view=reports');
		}
			
		$model = ED::model('Reports');	

		foreach ($ids as $id) {
			$id = (int) $id;
			$post = ED::post($id);

			// log the current action into database.
			$actionlog = ED::actionlog();

			$actionlogMsg = $actionlog->normalizeActionLogConstants($post->isReply(), 'COM_ED_ACTIONLOGS_REPORT_POST_DELETED');
			$actionlogPostTitle = $actionlog->normalizeActionLogPostTitle($post);
			$actionlogPostPermalink = $actionlog->normalizeActionLogPostPermalink($post);

			// Delete the post
			$state = $post->delete();

			if ($state) {
				$actionlog->log($actionlogMsg, 'report', array(
					'link' => $actionlogPostPermalink,
					'postTitle' => $actionlogPostTitle
				));
			}

			// Remove all reports related to the post first
			$status = $model->removePostReports($id);

			if ($status) {
				$actionlog->log('COM_ED_ACTIONLOGS_REPORT_RECORD_DELETED', 'report');
			}
		}
		
		ED::setMessage('COM_ED_SELECTED_POSTS_AND_REPORTS_DELETED_SUCCESSFULLY', 'success');
		ED::redirect('index.php?option=com_easydiscuss&view=reports');
	}
}
