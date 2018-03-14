<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

// ACL Check
$user = JFactory::getUser();
if (!$user->authorise('core.manage', 'com_rsform')) {
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
}

// Require the base controller
require_once JPATH_COMPONENT.'/controller.php';
require_once JPATH_COMPONENT.'/helpers/rsform.php';

$mainframe = JFactory::getApplication();

RSFormProHelper::readConfig();

// See if this is a request for a specific controller
$controller 		= $mainframe->input->getWord('controller');
$controller_exists  = false;
$task				= $mainframe->input->getCmd('task');

if (!$controller && strpos($task, '.')) {
	list($controller, $controller_task) = explode('.', $task, 2);
}

if (!empty($controller) && file_exists(JPATH_COMPONENT.'/controllers/'.$controller.'.php'))
{
	require_once JPATH_COMPONENT.'/controllers/'.$controller.'.php';
	
	$controller 	   = 'RsformController'.$controller;
	$RsformController  = new $controller();
	$controller_exists = true;
}
else
	$RsformController = new RsformController();

// Trigger onInit
$mainframe->triggerEvent('rsfp_bk_onInit');

// Execute task
if ($controller_exists && !empty($controller_task))
{	
	$controller_task = preg_replace('/[^A-Z_]/i', '', $controller_task);
	$RsformController->execute($controller_task);
}
else
	$RsformController->execute($mainframe->input->getWord('task'));

// Redirect if set
$RsformController->redirect();