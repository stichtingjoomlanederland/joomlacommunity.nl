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
$input = JFactory::getApplication()->input;
$guest = $my->id ? false : true;

$postId = $app->input->get('id', 0, 'int');
$post = ED::post($postId);

$popboxPosition = JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';
$showConversation = $config->get('layout_toolbar_conversation');
$showNotification = ($config->get('layout_toolbar_notification') && $config->get('main_notifications'));
$showSettings = $config->get('layout_toolbarprofile');

if ($showNotification) {
	// Get total notifications for the current viewer
	$model = ED::model('Notification');
	$notificationsCount = $model->getTotalNotifications($my->id);
}

if ($showConversation) {
	// Get new message count.
	$conversationModel = ED::model('Conversation');
	$conversationsCount = $conversationModel->getCount($my->id, array('filter' => 'unread'));
}

$useExternalConversations = false;

if (ED::easysocial()->exists() && $config->get('integration_easysocial_messaging')) {
    $useExternalConversations = true;
}

if (ED::jomsocial()->exists() && $config->get('integration_jomsocial_messaging')) {
    $useExternalConversations = true;
}

require(JModuleHelper::getLayoutPath('mod_easydiscuss_navigation_button'));