<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

if (!JFile::exists($path)) {
	return;
}

require_once($path);

$config = ED::getConfig();
$acl = ED::acl();


$canViewStatistic = ($config->get('layout_board_stats') && $acl->allowed('board_statistics')) ? true : false;

// Skip this if the user didn't have permission to view it.
if (!$canViewStatistic) {
	return;
}

// load eadydiscuss styling.
ED::init();

$postModel = ED::model('Posts');
$totalPosts = $postModel->getTotalThread();

$resolvedPosts = $postModel->getTotalResolved();
$unresolvedPosts = $postModel->getUnresolvedCount();

$userModel = ED::model('Users');
$totalUsers	= $userModel->getTotalUsers();

$latestUserId = $userModel->getLatestUser();
$latestMember = ED::user($latestUserId);

// Total guests
$totalGuests = $userModel->getTotalGuests();

// Online users
$onlineUsers = $userModel->getOnlineUsers();

require(JModuleHelper::getLayoutPath('mod_easydiscuss_board_statistic'));



