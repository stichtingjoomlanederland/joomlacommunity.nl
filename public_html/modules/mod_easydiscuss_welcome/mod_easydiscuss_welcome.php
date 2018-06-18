<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport( 'joomla.filesystem.file' );

$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

if (!JFile::exists($path)) {
	return;
}

require_once ($path);

// Ensure that it renders ED stylesheets
ED::init();

$my	= ED::user();
$isLoggedIn	= ED::modules()->getLoginStatus();

$return = ED::modules()->getReturnURL($params, $isLoggedIn);

$config = ED::config();

$badges	= $my->getBadges();

// Retrieve user's ranking
$ranking = '';

if ($my->id) {
	$ranking = ED::ranks()->getRank($my->id);
}

$usersConfig = JComponentHelper::getParams('com_users');
$allowRegister = $usersConfig->get('allowUserRegistration');

require(JModuleHelper::getLayoutPath('mod_easydiscuss_welcome'));
