<?php
/*
 * @package		mod_easydiscuss_welcome
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

$path	= JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';

jimport( 'joomla.filesystem.file' );

if( !JFile::exists( $path ) )
{
	return;
}

require_once( $path );
require_once (dirname(__FILE__) . '/helper.php');

$my			= JFactory::getUser();
$isLoggedIn	= modEasyDiscussWelcomeHelper::getLoginStatus();
$return		= modEasyDiscussWelcomeHelper::getReturnURL($params, $isLoggedIn);
$menu		= $params->get( 'menuid' , '' );
$menuURL	= '';
$config		= DiscussHelper::getConfig();

$document	= JFactory::getDocument();
// $document->addStyleSheet( rtrim( JURI::root() , '/' ) . '/modules/mod_easydiscuss_welcome/assets/css/styles.css' );
DiscussHelper::loadStylesheet("module", "mod_easydiscuss_welcome");

// Load current logged in user.
$profile	= DiscussHelper::getTable( 'Profile' );
$profile->load( $my->id );
$badges		= $profile->getBadges();

// Retrieve user's ranking
$ranking	= '';

if( $profile->id )
{
	$ranking	= DiscussHelper::getHelper( 'Ranks')->getRank( $profile->id );
}

if( !empty( $menu ) )
{
	$menuURL	= '&Itemid=' . $menu;
}

$userComponent = new stdClass();
$userComponent->option		= 'com_user';
$userComponent->login		= 'login';
$userComponent->logout		= 'logout';
$userComponent->register	= 'register';
$userComponent->password	= 'passwd';

$usersConfig					= JComponentHelper::getParams( 'com_users' );
$userComponent->allowRegister	= $usersConfig->get( 'allowUserRegistration' );

if( DiscussHelper::getJoomlaVersion() >= '1.6' )
{
	$userComponent->option		= 'com_users';
	$userComponent->login		= 'user.login';
	$userComponent->logout		= 'user.logout';
	$userComponent->register	= 'registration';
	$userComponent->password	= 'password';
}

require(JModuleHelper::getLayoutPath('mod_easydiscuss_welcome'));
