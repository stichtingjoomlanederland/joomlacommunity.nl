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

// Load ED engine
$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';
if (!JFile::exists($path)) {
	return;
}

require_once($path);

ED::init();

// Load language
JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);
// ED::loadStylesheet("module", "mod_easydiscuss_notifications");

$app = JFactory::getApplication();
$user = ED::user();
$acl = ED::acl();
$themes = ED::themes();

// lets check if the current page is EasyDiscuss Ask page or not. If yes, dont show the module. Dont make sense
// to show quick question form in ask page. #234
$component = $app->input->get('option', '', 'cmd');
$view = $app->input->get('view', '', 'cmd');

if ($component == 'com_easydiscuss' && $view == 'ask') {
	return;
}

// Retrieve the default category.
$defaultCategory = ED::model('Category')->getDefaultCategory();
$category = $app->input->get('category_id', '');
$category = (!$category && $defaultCategory !== false) ? $defaultCategory->id : $category;

// Only show private categories for login user.
$showPrivateCat = ED::isLoggedIn();

// Retrieve all categories
$nestedCategories = ED::populateCategories('', '', 'select', 'category_id', $category, true, true, $showPrivateCat, true, 'form-control', '',  DISCUSS_CATEGORY_ACL_ACTION_SELECT);

// Captcha Integration
$captcha = ED::captcha();

require(JModuleHelper::getLayoutPath('mod_easydiscuss_quickquestion'));
