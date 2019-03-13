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

$config = ED::config();
$app = JFactory::getApplication();

$cron = $app->input->get('cron', false, 'bool');
$crondata = $app->input->get('crondata', false, 'bool');
$task = $app->input->get('task', '', 'cmd');

if ($task == 'cron' || $cron) {

	$mailq = ED::mailqueue();

	// Process pending emails.
	$mailq->sendOnPageLoad();

	if ($config->get('main_email_parser')) {
		$mailq->parseEmails();
	}

	// Process remote storage tasks
	ED::cron()->execute();

	// Maintainance bit
	ED::maintenance()->run();

	echo 'Cronjob Processed.';
	exit;
}

if ($crondata) {
	$msg = ED::cron()->executeDownload();
	echo $msg;
	exit;
}

// Prune notification items.
if ($config->get('prune_notifications_onload')) {
	ED::maintenance()->pruneNotifications();
}

if ($config->get('main_mailqueueonpageload')) {
	$mailq = ED::getMailQueue();
	$mailq->sendOnPageLoad();
}
