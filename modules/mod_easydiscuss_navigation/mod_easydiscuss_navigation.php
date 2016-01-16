<?php
/**
 * @package		mod_easydiscuss_navigation
 * @copyright	Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
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

// Load theme css here.
DiscussHelper::loadStylesheet("module", "mod_easydiscuss_navigation");
DiscussHelper::loadHeaders();

// Load component's language file.
JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );
JFactory::getLanguage()->load( 'mod_easydiscuss_navigation' , JPATH_ROOT );

$my 		= JFactory::getUser();
// We need to detect if the user is browsing a particular category
$active 	= '';
$view 		= JRequest::getVar( 'view' );
$layout 	= JRequest::getVar( 'layout' );
$option 	= JRequest::getVar( 'option' );
$id 		= JRequest::getInt( 'category_id' );

if( $option == 'com_easydiscuss' && $view == 'post')
{
	$postId = JRequest::getInt( 'id', 0 );
	// update user's post read flag
	if( !empty( $my->id ) && !empty( $postId ) )
	{
		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->load( $my->id );
		$profile->read( $postId );
	}
}

$model			= DiscussHelper::getModel( 'Categories' );
$categories		= $model->getCategoryTree();

$notificationModel		= DiscussHelper::getModel( 'Notification' );
$totalNotifications		= $notificationModel->getTotalNotifications( $my->id );

if( $option == 'com_easydiscuss' && $view == 'categories' && $layout == 'listings' && $id )
{
	$active		= $id;
}

require( JModuleHelper::getLayoutPath( 'mod_easydiscuss_navigation' ) );
