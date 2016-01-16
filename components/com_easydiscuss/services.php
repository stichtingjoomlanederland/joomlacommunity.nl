<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

$config = DiscussHelper::getConfig();

if (JRequest::getCmd('task', '', 'GET') == 'cron')
{
	$mailq	= DiscussHelper::getMailQueue();

	if(JRequest::getCmd('job', '', 'GET') == 'subscription' && $config->get('main_sitesubscription'))
	{
		//process the site subscription
		//daily - index.php?option=com_easydiscuss&task=cron&job=subscription&interval=daily
		//weekly - index.php?option=com_easydiscuss&task=cron&job=subscription&interval=weekly
		//monthly - index.php?option=com_easydiscuss&task=cron&job=subscription&interval=monthly
		//all - index.php?option=com_easydiscuss&task=cron&job=subscription&interval=all

		$interval	= JRequest::getCmd('interval', 'daily', 'GET');

		$subs = DiscussHelper::getSiteSubscriptionClass();

		if($interval == 'all')
		{
			$processIntervals = array('daily', 'weekly', 'monthly');

			foreach($processIntervals as $processInterval)
			{
				$subs->interval = $processInterval;
				$subs->process();
			}
		}
		else
		{
			$subs->interval = $interval;
			$subs->process();
		}

		echo ucfirst($interval).' subscription processed.';
	}
	else
	{
		$mailq->sendOnPageLoad();

		echo 'Email batch process finished.';
	}

	if( $config->get('main_email_parser') )
	{
		// @rule: Process incoming email rules
		$mailq->parseEmails();
	}

	// Run any archiving or maintenance calls
	if( $config->get( 'prune_notifications_cron' ) )
	{
		DiscussHelper::getHelper( 'Maintain' )->pruneNotifications();
	}

	// Maintainance bit
	DiscussHelper::getHelper( 'Maintain' )->run();

	exit;
}

// Prune notification items.
if( $config->get( 'prune_notifications_onload' ) )
{
	DiscussHelper::getHelper( 'Maintain' )->pruneNotifications();
}

/*
 * Processing email batch sending.
 */
if ($config->get('main_mailqueueonpageload'))
{
	$mailq	= DiscussHelper::getMailQueue();
	$mailq->sendOnPageLoad();
}