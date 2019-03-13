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

class EasyDiscussThemesHelperForums
{
	/**
	 * Generates board statistics on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function stats()
	{
		$config = ED::config();
		$acl = ED::acl();
		$allowed = false;

		if ($config->get('layout_board_stats') && $acl->allowed('board_statistics')){
			$allowed = true;
		}
		
		if (!$allowed) {
			return;
		}

		$postModel = ED::model('Posts');
		$totalPosts	= $postModel->getTotalThread();

		$resolvedPosts = $postModel->getTotalResolved();
		$unresolvedPosts = $postModel->getUnresolvedCount();

		$userModel = ED::model('Users');
		$totalUsers	= $userModel->getTotalUsers();


		$ids = $userModel->getLatestUser();
		$latestMember = ED::user($ids);

		// Total guests
		$totalGuests = $userModel->getTotalGuests();

		// Online users
		$onlineUsers = $userModel->getOnlineUsers();

		$theme = ED::themes();

		$theme->set('latestMember', $latestMember);
		$theme->set('unresolvedPosts', $unresolvedPosts);
		$theme->set('resolvedPosts', $resolvedPosts);
		$theme->set('totalUsers', $totalUsers);
		$theme->set('totalPosts', $totalPosts);
		$theme->set('onlineUsers', $onlineUsers);
		$theme->set('totalGuests', $totalGuests);

		$output = $theme->output('site/html/forums.stats');

		return $output;
	}
}
