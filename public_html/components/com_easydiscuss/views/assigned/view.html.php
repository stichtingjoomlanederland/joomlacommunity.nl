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

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewAssigned extends EasyDiscussView
{
	public function display($tpl = null)
	{
		// Ensure that the user is logged in
		ED::requireLogin();

		ED::setPageTitle(JText::_('COM_EASYDISCUSS_PAGETITLE_ASSIGNED'));

		if (! EDR::isCurrentActiveMenu('assigned')) {
			$this->setPathway( JText::_('COM_EASYDISCUSS_BREADCRUMB_ASSIGNED'));
		}

		if (!ED::isModerator()) {
			return JError::raiseError(404, JText::_('COM_EASYDISCUSS_YOU_ARE_NOT_ALLOWED_HERE'));
		}

		// Load the user's profile
		$profile = ED::profile($this->my->id);

		// If profile is invalid, throw an error.
		if (!$profile->id || !$this->my->id) {
			return JError::raiseError(404, JText::_('COM_EASYDISCUSS_USER_ACCOUNT_NOT_FOUND'));
		}

		// [Model:Assigned]
		$model = ED::model('Assigned');

		// retrieve the assiged post
		$posts = $model->getPosts($this->my->id);
		$pagination = $model->getPagination();

		// format the post
		$posts = ED::formatPost($posts);

		// Get the post type/status
		$posts = ED::getPostStatusAndTypes($posts);

		// Get user badges
		$badges = $profile->getBadges();

		// Get the pass 7 days assigned post data for current view user
		$assignedPostGraph = $model->getAssignPostGraph($this->my->id);

		// Format the ticks for the assign posts data
		$assignPostData = array();

		foreach ($assignedPostGraph->dates as $dateString) {

			// Normalize the date string first
			$dateString = str_ireplace('/', '-', $dateString);
			$date = ED::date($dateString);

			$assignPostData[] = $date->display(JText::_('COM_EASYDISCUSS_DATE_DM'));
		}

		// Format the 7 days to json data
		$assignedPostDate = json_encode($assignPostData);

		// Format the total assign post per day to json data
		$assignedPostHistory = json_encode($assignedPostGraph->count);

		// Get total number of posts assigned to the current user.
		$totalAssigned = $model->getTotalAssigned($this->my->id);

		// Get total number of posts that is assigned to this user and resolved.
		$totalResolved = $model->getTotalSolved($this->my->id);

		// Calculate percentage and default value
		$completedPercentage = 0;
		$unresolvedPercentage = 0;
		$fullPercentage = 100;
		$emptyAssignedPostPercentage = '100';

		if ($posts) {
			$completedPercentage = round(($totalResolved / $totalAssigned) * 100, 2);
			$unresolvedPercentage = $fullPercentage - $completedPercentage;
		}

		// Format the total assigned post to json data
		$totalAssignedData = json_encode($totalAssigned);

		// Format the total resolved post to json data
		$totalResolvedData = json_encode($totalResolved);

		// Format the total resolved post to json data
		$completedPercentage = json_encode($completedPercentage);

		// Format the total resolved post to json data
		$unresolvedPercentage = json_encode($unresolvedPercentage);

		$this->set('assignedPostDate', $assignedPostDate);
		$this->set('assignedPostHistory', $assignedPostHistory);
		$this->set('totalAssignedData', $totalAssignedData);
		$this->set('totalResolvedData', $totalResolvedData);
		$this->set('completedPercentage', $completedPercentage);
		$this->set('unresolvedPercentage', $unresolvedPercentage);
		$this->set('emptyAssignedPostPercentage', $emptyAssignedPostPercentage);
		$this->set('posts', $posts);
		$this->set('profile', $profile);
		$this->set('badges', $badges);
		$this->set('pagination', $pagination);

		parent::display('assign/default');
	}
}
