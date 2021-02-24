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

$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

jimport( 'joomla.filesystem.file' );

if (!JFile::exists($path)) {
	return;
}

// check if this is single post page or not.
$app = JFactory::getApplication();
$option = $app->input->get('option', '', 'cmd');
$view = $app->input->get('view', '', 'cmd');
$layout = $app->input->get('layout', '', 'cmd');
$id = $app->input->get('id', '', 'int');

// make sure this module only appear in single post page.
if ($option != 'com_easydiscuss' || $view != 'post' || ($view == 'post' && $layout == 'edit') || !$id) {
	return;
}

require_once($path);

ED::init();
$lib = ED::modules($module);

$config	= ED::config();

// hash on current post page.
$post = ED::post($id);

$hash = md5($post->getNonSEFLink());

$model = ED::model('Users');
$users = $model->getPageViewers($hash);

if (!$users) {
	return;
}

require(JModuleHelper::getLayoutPath('mod_easydiscuss_whos_viewing'));
