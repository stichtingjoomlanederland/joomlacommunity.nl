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

jimport('joomla.filesystem.file');

$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

if (!JFile::exists($path)) {
	return;
}

require_once($path);

$config = ED::config();
$acl = ED::acl();

$canViewStatistic = $acl->allowed('board_statistics') ? true : false;

// Skip this if the user didn't have permission to view it.
if (!$canViewStatistic) {
	return;
}

// load eadydiscuss styling.
ED::init();
JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

$lib = ED::modules($module);
$helper = $lib->getHelper(false);

$totalPosts = $helper->getTotalPosts();
$resolvedPosts = $helper->getTotalResolvedPosts();
$unresolvedPosts = $helper->getTotalUnresolvedPosts();
$totalUsers = $helper->getTotalUsers();
$latestMember = $helper->getLatestMember();
$totalGuests = $helper->getTotalGuests();
$onlineUsers = $helper->getOnlineUsers();

require($lib->getLayout());