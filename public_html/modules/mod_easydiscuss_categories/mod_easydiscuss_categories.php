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

// Load ED engine.
$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

if (!JFile::exists($path)) {
	return;
}

require_once($path);

ED::init();
$lib = ED::modules($module);
$lib->addScript('filter.js');

// Load language.
JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

$helper = $lib->getHelper(false);
$categories = $helper->getData($params);

$tpl = 'default';

if ($params->get('layouttype') == 'tree') {
    $tpl = 'tree';
}

$app = JFactory::getApplication();
$input = $app->input;

$activeCategory = $input->get('category_id', 0);

if ($input->get('view', '') == 'ask') {
	$activeCategory = $input->get('category', 0);
}

$activeCategory = ED::category($activeCategory);

require(JModuleHelper::getLayoutPath('mod_easydiscuss_categories', $tpl));
