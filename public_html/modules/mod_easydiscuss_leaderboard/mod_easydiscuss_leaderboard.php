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

$file = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';
$exists = JFile::exists($file);

if (!JFile::exists($file)) {
    return;
}

require_once($file);

ED::init();
$lib = ED::modules($module);

JFactory::getLanguage()->load('mod_easydiscuss_leaderboard', JPATH_ROOT);

$order = (string) $params->get('rank_type', 'points');
$count = (int) trim($params->get('count', 20));

$options = array('order' => $order, 'count' => $count);

$excludeUser = $params->get('exclusion', 0);
$excludeUser = explode(',', $excludeUser);

// Exclude users
if (!empty($excludeUser)) {
	$excludeUser = array_filter($excludeUser, 'is_numeric');
	$options['exclude'] = $excludeUser;
}

$model = ED::model('Users');
$users = $model->getTopUsers($options);

if (!$users) {
	return;
}

$my = ED::user();

require(JModuleHelper::getLayoutPath('mod_easydiscuss_leaderboard'));
