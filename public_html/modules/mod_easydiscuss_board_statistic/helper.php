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

class EasyDiscussModBoard_statisticHelper extends EasyDiscuss
{
	public function __construct(&$params)
	{
		$this->params = $params;
		$this->model = ED::model('Posts');
		$this->userModel = ED::model('Users');
	}

	public function getTotalPosts()
	{
		$totalPosts = 0;

		if ($this->params->get('show_total_posts', true)) {
			$totalPosts = $this->model->getTotalThread();
		}

		return $totalPosts;
	}

	public function getTotalResolvedPosts()
	{
		$resolvedPosts = 0;

		if ($this->params->get('show_total_resolved', true)) {
			$resolvedPosts = $this->model->getTotalResolved();
		}

		return $resolvedPosts;
	}

	public function getTotalUnresolvedPosts()
	{
		$unresolvedPosts = 0;

		if ($this->params->get('show_total_unresolved', true)) {
			$unresolvedPosts = $this->model->getUnresolvedCount();
		}

		return $unresolvedPosts;
	}

	public function getTotalUsers()
	{
		$totalUsers = 0;

		if ($this->params->get('show_total_users', false)) {
			$totalUsers	= $this->userModel->getTotalUsers();
		}

		return $totalUsers;
	}

	public function getLatestMember()
	{
		$latestMember = null;

		if ($this->params->get('show_latest_member', true)) {
			$latestUserId = $this->userModel->getLatestUser();
			$latestMember = ED::user($latestUserId);
		}

		return $latestMember;
	}

	public function getTotalGuests()
	{
		// Total guests
		$totalGuests = 0;

		if ($this->params->get('show_total_guests', false)) {
			$totalGuests = $this->userModel->getTotalGuests();
		}

		return $totalGuests;
	}

	public function getOnlineUsers()
	{
		// Online users
		$onlineUsers = array();
		
		if ($this->params->get('show_online_users', true)) {
			$onlineUsers = $this->userModel->getOnlineUsers();
		}

		return $onlineUsers;

	}
}
