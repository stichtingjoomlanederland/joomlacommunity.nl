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

require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');
require_once(ED_ROOT . '/views/views.php');

require_once(JPATH_COMPONENT . '/controllers/controller.php');


ED::ajax()->process();
ED::checkEnvironment();

require_once(__DIR__ . '/services.php');

$app = JFactory::getApplication();
$input = $app->input;
$task = $input->get('task', 'display', 'cmd');

// We treat the view as the controller. Load other controller if there is any.
$controller = $input->get('controller', '', 'cmd');

if ($controller) {

	$controller = strtolower($controller);
	$path = JPATH_COMPONENT . '/controllers/' . $controller . '.php';

	if (!JFile::exists($path)) {
		throw ED::exception('Invalid Controller name "' . $controller . '".<br /> File does not exists in this context.', ED_MSG_ERROR);
	}

	require_once($path);
}

$class = 'EasyDiscussController' . ucfirst($controller);

if (!class_exists($class)) {
	throw ED::exception('Invalid Controller Object. Class definition does not exists in this context.', ED_MSG_ERROR);
}

$controller = new $class();
$controller->execute($task);
$controller->redirect();
