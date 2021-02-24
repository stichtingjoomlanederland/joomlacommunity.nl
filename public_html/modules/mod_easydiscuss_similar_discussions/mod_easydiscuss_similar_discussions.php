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

// Load ED Engine
$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';
if (!JFile::exists($path)) {
    return;
}

require_once ($path);

ED::init();
$lib = ED::modules($module);
$helper = $lib->getHelper(false);

$app = JFactory::getApplication();

// Check the current view. This module only appear on post.
$view = $app->input->get('view', '');
$postId = $app->input->get('id', '');

if ($view != 'post' || !$postId) {
    return;
}

// Load site language
JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

$itemid = EDR::getItemId('post');
$posts = $helper->getSimilarPosts($postId, $params);

require(JModuleHelper::getLayoutPath('mod_easydiscuss_similar_discussions'));
