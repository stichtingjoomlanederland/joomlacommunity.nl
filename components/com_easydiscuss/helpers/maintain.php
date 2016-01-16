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

// Let's add a snooze to the lock
// ALTER TABLE  `#__discuss_posts` ADD `lockdate` DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00' AFTER  `islock`

class DiscussMaintainHelper
{
	public $nullDate	= '';
	public $nowDate		= '';
	public $hasRan		= false;

	public function __construct()
	{
		// Initiate some expensive functions and store them in class variable

		$db				= DiscussHelper::getDBO();

		$this->nullDate	= method_exists($db, 'getNullDate') ? $db->getNullDate() : '0000-00-00 00:00:00';
		$this->nullDate	= $db->quote( $this->nullDate );

		$this->nowDate	= $db->quote( DiscussHelper::getDate()->toMySQL());
	}

	/**
	 * Performs some maintenance here.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function pruneNotifications()
	{
		$db 	= DiscussHelper::getDBO();
		$date 	= DiscussHelper::getDate();

		$config	= DiscussHelper::getConfig();
		$days 	= $config->get( 'notifications_history' , 30 );

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_notifications' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'created' ) . ' <= DATE_SUB( ' . $db->Quote( $date->toMySQL() ) . ' , INTERVAL ' . $days . ' DAY )';

		$db->setQuery($query);
		$db->query();

		return true;
	}

	public function run()
	{
		if( $this->hasRan )
		{
			return;
		}

		// 1. Lock new post (with proper lockdate)
		// 2. Lock older post (without lockdate)
		//     2.1. First fill empty lock date for posts with replies
		//         2.1.1. Find empty lock date
		//         2.1.2. Update emtpy lock date
		//         2.1.3. Lock expired posts
		//     2.2. Lastly fill empty lock date for posts without replies
		//         2.2.1. repeat sub-steps in 2.1
		//     2.3. Lock all expired posts.

		$config		= DiscussHelper::getConfig();

		$userLastRepliedInterval = (int) $config->get( 'main_daystolock_afterlastreplied' );
		$userPostCreatedInterval = (int) $config->get( 'main_daystolock_aftercreated' );

		if( empty($userLastRepliedInterval) && empty($userPostCreatedInterval) )
		{
			// both is zero. this also means the auto lock feature is not required.
			return;
		}

		if( $userLastRepliedInterval || $userPostCreatedInterval )
		{
			$this->lock();
			$this->hasRan = true;
		}


		if( $config->get( 'main_lock_newpost_only' ) )
		{
			return;
		}

		$db			= DiscussHelper::getDBO();

		$query	= ' UPDATE `#__discuss_posts` SET lockdate = CASE'
				. ' WHEN replied = ' . $this->nullDate . ' THEN DATE_ADD(created, INTERVAL ' . $userPostCreatedInterval . ' DAY)'
				. ' ELSE DATE_ADD(replied, INTERVAL ' . $userPostCreatedInterval . ' DAY) END'
				. ' WHERE parent_id = 0 AND islock = 0 AND published = 1'
				. ' AND lockdate = ' . $this->nullDate;
		$db->setQuery( $query );
		$db->query();

		// alternative
		/*
		if( $userLastRepliedInterval > 0 )
		{
			$query	= ' SELECT a.id, a.created, MAX(b.created) AS lastreplied FROM `#__discuss_posts` AS a'
					. ' LEFT JOIN `#__discuss_posts` AS b ON b.parent_id = a.id'
					. ' WHERE b.parent_id > 0 AND a.parent_id = 0 AND a.islock = 0 AND a.published = 1'
					. ' AND a.lockdate = ' . $this->nullDate
					. ' GROUP BY a.id';
			$db->setQuery( $query );
			$result = $db->loadObjectList();

			if( count($result) > 0 )
			{
				foreach ($result as $item)
				{
					$query	= ' UPDATE `#__discuss_posts`'
							. ' SET `lockdate` = DATE_ADD(' . $item->lastreplied . ', INTERVAL ' . $userPostCreatedInterval . ' DAY)'
							. ' WHERE `id` = ' . $db->quote( $item->id );
					$db->setQuery( $query );
					$db->query();
				}
			}
		}

		if( $userPostCreatedInterval > 0 )
		{
			$query	= ' SELECT `id` FROM `#__discuss_posts`'
					. ' WHERE `parent_id` = 0 AND `islock` = 0 AND `published` = 1'
					. ' AND `lockdate` = ' . $this->nullDate;
			$db->setQuery( $query );

			if( $db->loadResult() > 0 )
			{
				$query	= ' UPDATE `#__discuss_posts` SET `lockdate` = DATE_ADD(`created`, INTERVAL ' . $userPostCreatedInterval . ' DAY)'
						. ' WHERE `parent_id` = 0 AND `islock` = 0 AND `published` = 1'
						. ' AND `lockdate` = ' . $this->nullDate;
				$db->setQuery( $query );
				$db->query();
			}
		}
		*/
		// alternative end

		$this->lock();
	}

	public function lock()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'UPDATE `#__discuss_posts` SET `islock` = 1'
				. ' WHERE `islock` = 0'
				. ' AND `parent_id` = 0'
				. ' AND `published` = 1'
				. ' AND `lockdate` != ' . $this->nullDate
				. ' AND `lockdate` <= ' . $this->nowDate;
		$db->setQuery( $query );
		$db->query();
	}
}
