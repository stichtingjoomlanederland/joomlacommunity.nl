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

jimport('joomla.filesystem.file');

$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

if (!JFile::exists($path)) {
	return;
}

require_once ($path);

ED::init();

$my = ED::user();
$config = ED::config();
$acl = ED::acl();

$notificationsCount = 0;
$conversationsCount = 0;

if (!empty($my->id)) {
	$notificationModel = ED::model('Notification');
	$conversationModel = ED::model('Conversation');

	if (!empty($notificationModel)) {
		$notificationsCount = $notificationModel->getTotalNotifications($my->id);
	}

	if (!empty($conversationModel)) {
		$conversationsCount = $conversationModel->getCount($my->id, array('filter' => 'unread'));
	}
}

$usernameField = 'MOD_NOTIFICATIONS_USERNAME';

if (ED::easysocial()->exists() && $config->get('main_login_provider') == 'easysocial') {
	$usernameField = ED::easysocial()->getUsernameField();
}

// since we are loading frontend lib, we will need to load EasyDiscuss frontend language.
JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

require(JModuleHelper::getLayoutPath('mod_easydiscuss_notifications'));