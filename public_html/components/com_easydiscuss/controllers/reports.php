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

class EasyDiscussControllerReports extends EasyDiscussController
{
	/**
	 * Allows caller to save a report on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function save()
	{
		$id = $this->input->get('id', 0, 'int');
		$message = $this->input->get('reporttext', '', 'string');

        // Load the new post object
        $post = ED::post($id);

		if (!$post->id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_POST_ID'), 'error');
			return $this->app->redirect(EDR::_('index.php?option=com_easydiscuss', false));
		}

		// Get the URL to the discussion.
		$url = EDR::getPostRoute($post->id, false);

		if ($post->isReply()) {
			$url = EDR::getPostRoute($post->parent_id, false);
		}

		if (empty($message)) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_REPORT_EMPTY_TEXT'), 'error');
			return $this->app->redirect($url);
		}

		if (!$post->canReport()) {
            ED::setMessage(JText::_('COM_EASYDISCUSS_REPORT_POST_FAILED'), 'error');
            return $this->app->redirect($url);
		}

		$date = ED::date();
		$report = ED::table('Report');
		$report->created_by	= $this->my->id;
		$report->post_id = $post->id;
		$report->reason = $message;
		$report->created = $date->toMySQL();

		if (!$report->store()) {
			ED::setMessage($report->getError(), 'error');
			return $this->app->redirect($url);
		}

		// Mark post as reported.
		$report->markPostReport();

		$threshold = $this->config->get('main_reportthreshold', 15);
		$totalReports = $report->getReportCount();
		$redirectMessage = JText::_('COM_EASYDISCUSS_REPORT_SUBMITTED');

		// Check if the number of reports for this post exceeded the threshold.
		if ($totalReports > $threshold) {
			$owner = $post->getOwner();
			$date = ED::date($post->created);

			$emailData = array();
			$emailData['postTitle'] = $post->title;
			$emailData['postContent'] = $post->content;
			$emailData['postAuthor'] = $owner->name;
			$emailData['postAuthorAvatar'] = $owner->avatar;
			$emailData['postDate'] = $date->toFormat();
			$emailData['postLink'] = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $post->id, false, true);
			$emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_REPORT_REQUIRED_YOUR_ATTENTION', JString::substr($postTbl->content, 0, 15) ) . '...';
			$emailData['emailTemplate'] = 'email.post.attention.php';

			if ($post->isReply()) {
				$emailData['postLink'] = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $post->parent_id, false, true);
			}

			ED::mailer()->notifyAdministrators($emailData, array(), $this->config->get('notify_admin'), $this->config->get('notify_moderator'));

			$redirectMessage = JText::_('COM_EASYDISCUSS_REPORT_SUBMITTED_BUT_POST_MARKED_AS_REPORT');
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_REPORT_SUBMITTED'), 'success');
		return $this->app->redirect($url);
	}

}
