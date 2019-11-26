<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
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

require_once($path);

ED::init();

$lang = JFactory::getLanguage();

$lang->load('com_easydiscuss', JPATH_ROOT);
$count = (INT)trim($params->get('count', 0));
$exclude = $params->get('exclusion', 0);
$exclude = explode(',', $exclude);

// Top members will always order by posts
$options = array(
				'count' => $count,
				'order' => 'posts'
			);

// Duration
$duration = $params->get('duration', 0);

if ($duration) {
	$options['duration'] = (int) $duration;
}

// Exclude users
if (!empty($exclude)) {
	$exclude = array_filter($exclude, 'is_numeric');
	$options['exclude'] = $exclude;
}

// Retrieve the users
$model = ED::model('Users');
$users = $model->getTopUsers($options);

// If there is no users, don't show the module
if (!$users) {
	return;
}

require(JModuleHelper::getLayoutPath('mod_easydiscuss_top_members'));
