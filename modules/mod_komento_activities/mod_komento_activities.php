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

$helper = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php';

if (!JFile::exists($helper)) {
    return;
}

// load all dependencies
require_once($helper);
// KomentoDocumentHelper::loadHeaders();
KomentoDocumentHelper::load('module', 'css', 'assets');
JFactory::getLanguage()->load('com_komento' , JPATH_ROOT);

// initialise all data
$profile = Komento::getProfile();
$config = Komento::getConfig();
$konfig = Komento::getKonfig();

/* $params
limit
component
includelikes
includecomments
includereplies
showavatar
showcomment
showtitle
maxcommenttext
maxtitletext
*/

$type = array();
if ($params->get('includelikes')) {
    $type[] = 'like';
}

if ($params->get('includecomments')) {
    $type[] = 'comment';
}
if ($params->get('includereplies')) {
    $type[] = 'reply';
}

$options = array(
    'type'      => $type,
    'limit'     => $params->get('limit'),
    'component' => $params->get('component')
);

$model = Komento::getModel('activity');
$activities = $model->getUserActivities('all', $options);

require(JModuleHelper::getLayoutPath('mod_komento_activities'));
