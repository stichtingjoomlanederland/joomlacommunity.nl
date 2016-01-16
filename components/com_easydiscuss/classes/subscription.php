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


class DiscussSubscription
{
	var $interval	= 'daily';

	function __construct()
	{
		require_once DISCUSS_ROOT . '/views.php';
	}

	function process()
	{
		$view = new EasyDiscussView();

		$date	= DiscussHelper::getDate();
		$now	= $date->toMySQL();

		$modelSubscribe	= $view->getModel( 'Subscribe' );
		$subscribers	= $modelSubscribe->getSiteSubscribers($this->interval, $now);

		$total = count($subscribers);

		if(empty($total))
		{
			return false;
		}

		foreach($subscribers as $subscriber)
		{
			$notify	= DiscussHelper::getNotification();

			$data				= array();
			$rows 				= $modelSubscribe->getCreatedPostByInterval($subscriber->sent_out, $now);
			$posts 				= array();

			if( $rows )
			{
				foreach( $rows as $row )
				{
					$row['categorylink']	= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=categorie&layout=listings&category_id='.$row['category_id'], false, true);
					$row['link']			= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id='.$row['id'], false, true);
					$row['userlink'] 		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=profile&id=' . $row['user_id'] , false , true );

					$category 				= DiscussHelper::getTable( 'Category' );
					$creator 				= DiscussHelper::getTable( 'Profile' );

					$category->load( $row['category_id'] );
					$creator->load( $row['user_id'] );

					$row['category']	= $category->getTitle();
					$row['avatar'] 		= $creator->getAvatar();
					$row['name']	 	= $creator->getName();
					$row['date']		= DiscussDateHelper::toFormat( $row['created'] , '%b %e, %Y' );
					$row['message']		= DiscussHelper::parseContent( $row['content'] );

					$posts[]		= $row;
				}
			}
			$data['post']		= $posts;
			$data['total']		= count($data['post']);

			$data['unsubscribeLink']	= DiscussHelper::getUnsubscribeLink( $subscriber, true, true);

			$subject			= $date->toMySQL();

			switch( strtoupper($this->interval) )
			{
				case 'DAILY':
					$subject 			= $date->toFormat( '%F' );
					$data['interval']	= JText::_( 'today' );
				break;
				case 'WEEKLY':
					$subject			= $date->toFormat( '%V' );
					$data['interval']	= JText::_( 'this week' );
				break;
				case 'MONTHLY':
					$subject 	= $date->toFormat( '%B' );
					$data['interval']	= JText::_( 'this month' );
				break;
			}

			if(!empty($data['post']))
			{
				$notify->addQueue($subscriber->email, JText::sprintf('COM_EASYDISCUSS_YOUR_'.$this->interval.'_SUBSCRIPTION', $subject) , '', 'email.subscription.site.interval.php', $data);
			}

			$subscribe = DiscussHelper::getTable( 'Subscribe' );
			$subscribe->load($subscriber->id);
			$subscribe->sent_out = $now;
			$subscribe->store();
		}
	}
}
