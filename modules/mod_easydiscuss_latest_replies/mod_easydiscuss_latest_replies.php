<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

jimport('joomla.filesystem.file');

if (!JFile::exists($path)) {
	return;
}

require_once($path);
require_once dirname(__FILE__) . '/helper.php';

$lang = JFactory::getLanguage();
$lang->load('mod_easydiscuss_latest_replies', JPATH_ROOT);

// Load component language as well.
$lang->load('com_easydiscuss', JPATH_ROOT);

ED::init();

$replies = modEasydiscussLatestRepliesHelper::getData($params);

if (!$replies) {
	return;
}

require(JModuleHelper::getLayoutPath('mod_easydiscuss_latest_replies'));
