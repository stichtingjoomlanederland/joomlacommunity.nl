<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

$helper	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php';

if (!JFile::exists($helper)) {
	return;
}

// load all dependencies
require_once($helper);
// KomentoDocumentHelper::loadHeaders();
KomentoDocumentHelper::load('module', 'css', 'assets');
JFactory::getLanguage()->load('com_komento', JPATH_ROOT);

// initialise all data
$profile = Komento::getProfile();
$config = Komento::getConfig();
$konfig = Komento::getKonfig();

/* $params
limit
component
sort = latest/likes
filtersticked
showtitle
showcomponent
showavatar
showauthor
lapsedtime
maxcommenttext
maxtitletext */

// todo: filter by category

$model = Komento::getModel('comments');

$comments = '';
$options = array(
	'threaded'	=> 0,
	'sort'		=> $params->get('sort'),
	'limit'		=> $params->get('limit'),
	'sticked'	=> $params->get('filtersticked') ? 1 : 'all',
	'random'	=> $params->get('random')
);

$component = $params->get('component');
$cid = array();
$filter = $params->get('filter');
$category = $params->get('category');
$articleId = $params->get('articleId');
$userId = $params->get('userId');

if ($component != 'all' && $filter == 'article') {
	
	$cid = explode(',', $articleId);

	if (count($cid) == 1) {
		$cid = $cid[0];
	}

} else if ($component != 'all' && $filter == 'category') {
	$application = Komento::loadApplication($component);
	$cid = $application->getContentIds($category);

	if (count($cid) == 1) {
		$cid = $cid[0];
	}
} else {
	$cid = 'all';

	if ($filter == 'user') {

		$userId = explode(',', $userId);

		if (count($userId) == 1) {
			$userId = $userId[0];
		}
		$options['userid'] = $userId;
	}
}

if ($params->get('sort') == 'latest') {
	$comments = $model->getComments($component, $cid, $options);
} else {
	$comments = $model->getPopularComments($component, $cid, $options);
}

require(JModuleHelper::getLayoutPath('mod_komento_comments'));
