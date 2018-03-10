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

jimport('joomla.filesystem.file');

$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

if ( !JFile::exists($path)) {
	return;
}

require_once( $path );

// load eadydiscuss styling.
ED::init();

$postModel 	= ED::model( 'Posts' );
$totalPosts	= $postModel->getTotal();

$resolvedPosts = $postModel->getTotalResolved();
$unresolvedPosts = $postModel->getUnresolvedCount();

$userModel 	= ED::model( 'Users' );
$totalUsers	= $userModel->getTotalUsers();

$latestUserId = $userModel->getLatestUser();
$latestMember = ED::user($latestUserId);

// Total guests
$totalGuests 	= $userModel->getTotalGuests();

// Online users
$onlineUsers 	= $userModel->getOnlineUsers();

$config = ED::getConfig();
$gids = $config->get( 'main_exclude_frontend_statistics' );

$canViewStatistic = true;

if (!empty($gids)) {
	//Remove whitespace
	$gids = str_replace(' ', '', $gids);
	$excludeGroup = explode(',', $gids);

	$my = JFactory::getUser();
	$myGroup = ED::getUserGroupId($my);

	$result = array_intersect($myGroup, $excludeGroup);
	$canViewStatistic = empty($result) ? true : false;
}

require( JModuleHelper::getLayoutPath( 'mod_easydiscuss_board_statistic' ) );



