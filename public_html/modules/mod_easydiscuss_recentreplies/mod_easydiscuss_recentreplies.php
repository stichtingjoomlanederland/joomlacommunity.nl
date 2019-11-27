<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

jimport('joomla.filesystem.file');

if (!JFile::exists($path)) {
	return;
}

require_once($path);
require_once dirname( __FILE__ ) . '/helper.php';

ED::init();

JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

$posts = modRecentRepliesHelper::getData($params);

if (!$posts) {
	return;
}

require(JModuleHelper::getLayoutPath('mod_easydiscuss_recentreplies'));
