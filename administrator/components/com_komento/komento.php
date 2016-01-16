<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');


// Load constants and helpers
require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'bootstrap.php' );

// Require the base controller
require_once( KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'controller.php' );

// Include dependencie
jimport('joomla.application.component.controller');

// Check access
if( Komento::joomlaVersion() >= '1.6' )
{
	if( !JFactory::getUser()->authorise('core.manage', 'com_komento') )
	{
		JFactory::getApplication()->redirect( 'index.php' , JText::_('JERROR_ALERTNOAUTHOR' ), 'error');
		JFactory::getApplication()->close();
	}
}
// else
// {
// 	if ( !JFactory::getUser()->authorize('com_komento', 'manage') )
// 	{
// 		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
// 	}
// }

if (JRequest::getBool('compile'))
{
	$minify = JRequest::getBool('minify', false);

	$compiler = Komento::getClass('compiler', 'KomentoCompiler');
	$results = $compiler->compile($minify);

	header('Content-type: text/x-json; UTF-8');
	echo json_encode($results);

	exit;
}

Komento::getHelper( 'Ajax' )->process();

// Get the task
$task	= JRequest::getCmd( 'task' , 'display' );

// We treat the view as the controller. Load other controller if there is any.
$controller	= JRequest::getCmd( 'c' , JRequest::getCmd( 'controller', '' ) );

// If task is getLanguage, then set c = lang
if( $task == 'getLanguage' )
{
	$controller = 'lang';
}

$controller	= KomentoController::getInstance( $controller );

// Task's are methods of the controller. Perform the Request task
$controller->execute( $task );

// Redirect if set by the controller
$controller->redirect();
