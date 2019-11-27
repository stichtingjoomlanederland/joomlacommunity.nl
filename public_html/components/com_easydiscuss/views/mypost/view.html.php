<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewMypost extends EasyDiscussView
{
	public function display($tpl = null)
	{
		// Ensure that the user is logged in
		ED::requireLogin();

		ED::setPageTitle(JText::_('COM_EASYDISCUSS_PAGETITLE_MYPOST'));

		if (! EDR::isCurrentActiveMenu('mypost')) {
			$this->setPathway( JText::_('COM_EASYDISCUSS_BREADCRUMB_MYPOST'));
		}

		// Load the user's profile
		$profile = ED::profile($this->my->id);

		// If profile is invalid, throw an error.
		if (!$profile->id || !$this->my->id) {
			return JError::raiseError(404, JText::_('COM_EASYDISCUSS_USER_ACCOUNT_NOT_FOUND'));
		}

		$postsModel = ED::model('Posts');

		$options = array('filter' => 'questions', 'userId' => $profile->id, 'includeCluster' => true, 'private' => true);
		$posts = $postsModel->getDiscussions($options);

		// format resultset.
		$posts = ED::formatPost($posts);
		// now we need to attach the post status and types into the resultset.
		$posts = ED::getPostStatusAndTypes($posts);

		$pagination	= $postsModel->getPagination();
		$pagination	= $pagination->getPagesLinks('mypost');

		// Get user badges
		$badges = $profile->getBadges();

		$userModel = ED::model('Users');

		// Get posts graph
		$postsHistory = $userModel->getPostsGraph($profile->id);

		// Format the ticks for the posts
		$postsTicks = array();

		foreach ($postsHistory->dates as $dateString) {

			// Normalize the date string first
			$dateString = str_ireplace('/', '-', $dateString);
			$date = ED::date($dateString);

			$postsTicks[] = $date->display(JText::_('COM_EASYDISCUSS_DATE_DM'));
		}

		$postsCreated = json_encode($postsHistory->count);
		$postsTicks = json_encode($postsTicks);

		$this->set('posts', $posts);
		$this->set('profile', $profile);
		$this->set('pagination', $pagination);
		$this->set('badges', $badges);
		$this->set('postsCreated', $postsCreated);
		$this->set('postsTicks', $postsTicks);

		parent::display('mypost/default');
	}
}
