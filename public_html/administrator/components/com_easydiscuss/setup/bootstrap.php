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

define('ED_IDENTIFIER', 'com_easydiscuss');
define('ED_PACKAGES', $path . '/packages');
define('ED_CONFIG', $path . '/config');
define('ED_THEMES', $path . '/themes');
define('ED_CONTROLLERS', $path . '/controllers');
define('ED_SERVER', 'https://stackideas.com');
define('ED_VERIFIER', 'https://stackideas.com/updater/verify');
define('ED_MANIFEST', 'https://stackideas.com/updater/manifests/easydiscuss');
define('ED_SETUP_URL', JURI::base() . 'components/com_easydiscuss/setup');
define('ED_TMP', $path . '/tmp');
define('ED_BETA', false);
define('ED_KEY', 'be5367700e2f4d3d834bb8803ab67f56');
define('ED_INSTALLER', 'full');

// Only when ED_PACKAGE is running on full package, the ED_PACKAGE should contain the zip's filename
define('ED_PACKAGE', 'com_easydiscuss_4.1.12_component_pro.zip');

// If this is in developer mode, we need to set the session
if ($developer) {
	$session = JFactory::getSession();
	$session->set('easydiscuss.developer', true);
}

if (!function_exists('dump')) {

	function isDevelopment()
	{
		$session = JFactory::getSession();
		$developer = $session->get('easydiscuss.developer');

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


############################################################
#### Process controller
############################################################
$controller = $input->get('controller', '', 'cmd');
$task = $input->get('task', '');

if (!empty($controller)) {

	$file = strtolower($controller) . '.' . strtolower($task) . '.php';
	$file = ED_CONTROLLERS . '/' . $file;

	require_once($file);

	$className = 'EasyDiscussController' . ucfirst($controller) . ucfirst($task);
	$controller = new $className();
	return $controller->execute();
}

// Get the current version
$contents = JFile::read(JPATH_ROOT. '/administrator/components/com_easydiscuss/easydiscuss.xml');
$parser = simplexml_load_string($contents);

$version = $parser->xpath('version');
$version = (string) $version[0];

define('ED_HASH', md5($version));

############################################################
#### Initialization
############################################################
$contents = JFile::read(ED_CONFIG . '/install.json');
$steps = json_decode($contents);

############################################################
#### Workflow
############################################################
$active = $input->get('active', 0, 'default');

if ($active === 'complete') {
	$activeStep = new stdClass();

	$activeStep->title = JText::_('COM_EASYDISCUSS_INSTALLER_INSTALLATION_COMPLETED');
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

require(ED_THEMES . '/default.php');