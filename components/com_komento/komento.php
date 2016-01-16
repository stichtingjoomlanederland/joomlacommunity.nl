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

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'bootstrap.php' );

// process ajax calls here
Komento::getHelper( 'Ajax' )->process();

jimport('joomla.application.component.controller');

require_once( KOMENTO_ROOT . DIRECTORY_SEPARATOR . 'controller.php' );

$task = JRequest::getCmd('task', 'display', 'GET');
$controllerName	= JRequest::getCmd( 'controller' , '' );

$controller = KomentoController::getInstance( $controllerName );
$controller->execute( $task );
$controller->redirect();
