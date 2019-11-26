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

$lang = JFactory::getLanguage();
$lang->load('mod_easydiscuss_most_voted', JPATH_ROOT);

$config = ED::config();

ED::init();

$count = (INT)trim($params->get('count', 0));

$model = ED::model('posts');

$posts = $model->getMostVoted($count);

// format the post
if ($posts) {
	$posts = ED::modules()->format($posts);
}

require(JModuleHelper::getLayoutPath('mod_easydiscuss_most_voted'));
