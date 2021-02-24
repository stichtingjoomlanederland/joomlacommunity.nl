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
jimport('joomla.filesystem.folder');

// Get application
$app = JFactory::getApplication();
$input = $app->input;

$input->set('tmpl', 'component');

$reinstall = $input->get('reinstall', false, 'bool') || $input->get('install', false, 'bool');
$update = $input->get('update', false, 'bool');
$developer = $input->get('developer', false, 'bool');

############################################################
#### Constants
############################################################
$path = dirname(__FILE__);

define('SI_IDENTIFIER_SHORT', 'easydiscuss');
define('SI_IDENTIFIER', 'com_' . SI_IDENTIFIER_SHORT);
define('SI_LANG', strtoupper(SI_IDENTIFIER));
define('SI_ADMIN', JPATH_ROOT . '/administrator/components/' . SI_IDENTIFIER);
define('SI_ADMIN_MANIFEST', SI_ADMIN . '/' . SI_IDENTIFIER_SHORT . '.xml');
define('SI_SETUP', SI_ADMIN . '/setup');
define('SI_PACKAGES', $path . '/packages');
define('SI_CONFIG', $path . '/config');
define('SI_THEMES', $path . '/themes');
define('SI_CONTROLLERS', $path . '/controllers');
define('SI_CONTROLLER_PREFIX', 'EasyDiscussController');
define('SI_VERIFIER', 'https://stackideas.com/updater/verify');
define('SI_MANIFEST', 'https://stackideas.com/updater/manifests/' . SI_IDENTIFIER_SHORT);
define('SI_SETUP_URL', JURI::base() . 'components/' . SI_IDENTIFIER . '/setup');
define('SI_TMP', $path . '/tmp');
define('SI_BETA', false);
define('SI_KEY', 'be5367700e2f4d3d834bb8803ab67f56');
define('SI_INSTALLER', 'full');

// Only when SI_PACKAGE is running on full package, the SI_PACKAGE should contain the zip's filename
define('SI_PACKAGE', 'com_easydiscuss_5.0.1_component_pro.zip');

// If this is in developer mode, we need to set the session
if ($developer) {
	$session = JFactory::getSession();
	$session->set(SI_IDENTIFIER_SHORT . '.developer', true);
}

if (!function_exists('dump')) {

	function isDevelopment()
	{
		$session = JFactory::getSession();
		$developer = $session->get(SI_IDENTIFIER_SHORT . '.developer');

		return $developer;
	}

	function dump()
	{
		$args = func_get_args();

		echo '<pre>';
		
		foreach ($args as $arg) {
			var_dump($arg);
		}
		echo '</pre>';

		exit;
	}
}

function t($constant)
{
	return JText::_(SI_LANG . '_' . $constant);
}

############################################################
#### Process controller
############################################################
$controller = $input->get('controller', '', 'cmd');
$task = $input->get('task', '');

if (!empty($controller)) {

	$file = strtolower($controller) . '.' . strtolower($task) . '.php';
	$file = SI_CONTROLLERS . '/' . $file;

	require_once($file);

	$className = SI_CONTROLLER_PREFIX . ucfirst($controller) . ucfirst($task);
	$controller = new $className();
	return $controller->execute();
}

// Get the current version
$contents = file_get_contents(SI_ADMIN_MANIFEST);
$parser = simplexml_load_string($contents);

$version = $parser->xpath('version');
$version = (string) $version[0];

define('SI_HASH', md5($version));

############################################################
#### Initialization
############################################################
$contents = file_get_contents(SI_CONFIG . '/install.json');
$steps = json_decode($contents);

############################################################
#### Workflow
############################################################
$active = $input->get('active', 0, 'default');

if ($active === 'complete') {
	$activeStep = new stdClass();

	$activeStep->title = t('INSTALLER_INSTALLATION_COMPLETED');
	$activeStep->template = 'complete';

	// Assign class names to the step items.
	if ($steps) {
		foreach ($steps as $step) {
			$step->className = ' done';
		}
	}
} else {

	if ($active == 0) {
		$active = 1;
		$stepIndex = 0;
	} else {
		$active += 1;
		$stepIndex = $active - 1;
	}

	// Get the active step object.
	$activeStep = $steps[$stepIndex];

	// Assign class names to the step items.
	foreach ($steps as $step) {
		$step->className = $step->index == $active || $step->index < $active ? ' current' : '';
		$step->className .= $step->index < $active ? ' done' : '';
	}
}

require(SI_THEMES . '/default.php');