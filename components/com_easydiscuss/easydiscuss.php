<?php
/**
 * @package		EasyDiscuss
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

jimport( 'joomla.filesystem.file' );

// Load constants
require_once( JPATH_ROOT . '/components/com_easydiscuss/constants.php' );
require_once( JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php' );

// Require the base controller
require_once( JPATH_COMPONENT . '/controllers/controller.php' );

// Call ajax library
require_once( DISCUSS_CLASSES . '/disjax.php' );
require_once( DISCUSS_CLASSES . '/themes.php' );

DiscussHelper::getSAUsersIds();

// Process AJAX calls here
DiscussHelper::getHelper( 'Ajax' )->process();
DiscussHelper::getUserGids();

// Load up config object.
$config = DiscussHelper::getConfig();

require_once( dirname( __FILE__ ) . '/services.php' );

// Compile javascripts
DiscussHelper::compileJS();

// Get the task
$task	= JRequest::getCmd( 'task' , 'display' );

// We treat the view as the controller. Load other controller if there is any.
$controller	= JRequest::getWord( 'controller' , '' );

if( !empty( $controller ) )
{
	$controller	= JString::strtolower( $controller );
	$path		= JPATH_COMPONENT . '/controllers/' . $controller . '.php';

	// Test if the controller really exists
	if( JFile::exists( $path ) )
	{
		require_once( $path );
	}
	else
	{
		JError::raiseError( 500 , JText::_( 'Invalid Controller name "' . $controller . '".<br /> File does not exists in this context.' ) );
	}
}

$class	= 'EasyDiscussController' . JString::ucfirst( $controller );

// Test if the object really exists in the current context
if( class_exists( $class ) )
{
	$controller	= new $class();
}
else
{
	JError::raiseError( 500 , 'Invalid Controller Object. Class definition does not exists in this context.' );
}

// Task's are methods of the controller. Perform the Request task
$controller->execute( $task );

// Redirect if set by the controller
$controller->redirect();
