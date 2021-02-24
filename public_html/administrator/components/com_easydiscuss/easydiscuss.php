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

require_once(__DIR__ . '/setup.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');

jimport('joomla.filesystem.file');

// Require the base classes needed by controllers and views
require_once(DISCUSS_ADMIN_ROOT . '/controllers/controller.php');
require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

ED::ajax()->process();

$app = JFactory::getApplication();
$input = $app->input;
$task = $input->get('task', 'display', 'cmd');

ED::checkEnvironment();

// We treat the view as the controller. Load other controller if there is any.
$controller = $input->get('controller', '', 'cmd');

if ($controller) {

	$controller = strtolower($controller);
	$file = DISCUSS_ADMIN_ROOT . '/controllers/' . $controller . '.php';
	
	// Test if the controller really exists
	if (!JFile::exists($file)) {
		throw ED::exception('Invalid Controller name "' . $controller . '".<br /> File "' . $path . '" does not exists in this context.', ED_MSG_ERROR);
	}

	require_once($file);
}

$class = 'EasyDiscussController' . ucfirst($controller);

// Test if the object really exists in the current context
if (!class_exists($class)) {
	throw ED::exception('Invalid Controller Object. Class definition does not exists in this context.', ED_MSG_ERROR);
}

$controller	= new $class();
$controller->execute($task);
$controller->redirect();