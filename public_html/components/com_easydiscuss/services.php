<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$config = ED::config();

if (JRequest::getCmd('task', '', 'GET') == 'cron') {

	// $mailq = ED::getMailQueue();

	// if (JRequest::getCmd('job', '', 'GET') == 'subscription' && $config->get('main_sitesubscription')) {
	// 	//process the site subscription
	// 	//daily - index.php?option=com_easydiscuss&task=cron&job=subscription&interval=daily
	// 	//weekly - index.php?option=com_easydiscuss&task=cron&job=subscription&interval=weekly
	// 	//monthly - index.php?option=com_easydiscuss&task=cron&job=subscription&interval=monthly
	// 	//all - index.php?option=com_easydiscuss&task=cron&job=subscription&interval=all

	// 	$interval	= JRequest::getCmd('interval', 'daily', 'GET');

	// 	$subs = DiscussHelper::getSiteSubscriptionClass();

	// 	if($interval == 'all')
	// 	{
	// 		$processIntervals = array('daily', 'weekly', 'monthly');

	// 		foreach($processIntervals as $processInterval)
	// 		{
	// 			$subs->interval = $processInterval;
	// 			$subs->process();
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$subs->interval = $interval;
	// 		$subs->process();
	// 	}

	// 	echo ucfirst($interval).' subscription processed.';
	// } else {
	// 	$mailq->sendOnPageLoad();

	// 	echo 'Email batch process finished.';
	// }

	// // @rule: Process incoming email rules
	// if ($config->get('main_email_parser')) {
	// 	$mailq->parseEmails();
	// }

	// // Run any archiving or maintenance calls
	// if ($config->get('prune_notifications_cron')) {
	// 	ED::maintenance()->pruneNotifications();
	// }

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

// Prune notification items.
if ($config->get('prune_notifications_onload')) {
	ED::maintenance()->pruneNotifications();
}

if ($config->get('main_mailqueueonpageload')) {
	$mailq = ED::getMailQueue();
	$mailq->sendOnPageLoad();
}
