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

class DiscussBadgesHelper
{
	var $exists	= null;

	public function assign( $command , $userId )
	{
		// We don't have to give any badge to guests.
		if( !$userId || $userId == 0 )
		{
			return;
		}

		// @task: Load necessary language files
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );
		
		$config = DiscussHelper::getConfig();

		// If badges is disabled, do not proceed.
		if( !$config->get( 'main_badges' ) )
		{
			return;
		}

		// @task: Compute the count of the history already matches any badge for this user.
		$total	= $this->getTotal( $command , $userId );

		// @task: Get the badges that is relevant to this command
		$badges	= $this->getBadges( $command , $userId );

		if( !$badges )
		{
			return false;
		}

		foreach( $badges as $badge )
		{
			if( $total >= $badge->rule_limit )
			{
				$table	= DiscussHelper::getTable( 'BadgesUsers' );
				$table->set( 'badge_id' , $badge->id );
				$table->set( 'user_id'	, $userId );
				$table->set( 'created'	, DiscussHelper::getDate()->toMySQL() );
				$table->set( 'published', 1 );

				$table->store();

				// @task: Add a new notification when they earned a new badge.
				$notification	= DiscussHelper::getTable( 'Notifications' );

				$notification->bind( array(
						'title'	=> JText::sprintf( 'COM_EASYDISCUSS_NEW_BADGE_NOTIFICATION_TITLE' , $badge->title ),
						'cid'	=> $badge->id,
						'type'	=> DISCUSS_NOTIFICATIONS_BADGE,
						'target'	=> $userId,
						'author'	=> $userId,
						'permalink'	=> 'index.php?option=com_easydiscuss&view=profile&id=' . $userId
					) );
				$notification->store();
				
				
				//insert into JS stream.
				if( $config->get( 'integration_jomsocial_activity_badges', 0 ) )
				{
                    $badgeTable	= DiscussHelper::getTable( 'Badges' );
                    $badgeTable->load($badge->id );
				    $badgeTable->uniqueId    = $table->id;
				    DiscussHelper::getHelper( 'jomsocial' )->addActivityBadges( $badgeTable );
				}
				
			}
		}

		return true;
	}

	/**
	 * Retrieve a list of badges for the specific command
	 *
	 * @access	private
	 * @param	string	$command	The action string.
	 * @param	int		$userId		The actor's id.
	 *
	 * @return	Array	An array of BadgesHistory object.
	 **/
	private function getBadges( $command , $userId )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT a.* FROM ' . $db->nameQuote( '#__discuss_badges' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_rules' ) . ' AS b '
				. 'ON b.' . $db->nameQuote( 'id' ) . '= a.' . $db->nameQuote( 'rule_id' ) . ' '
				. 'WHERE b.' . $db->nameQuote( 'command' ) . '=' . $db->Quote( $command ) . ' '
				. 'AND a.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 ) . ' '
				. 'AND a.id NOT IN( '
				. ' SELECT ' . $db->nameQuote( 'badge_id' ) . ' FROM ' . $db->nameQuote( '#__discuss_badges_users' ) . ' AS x '
				. ' WHERE x.' . $db->nameQuote( 'user_id') . '=' . $db->Quote( $userId ) . ' '
				. ')';
					
		$db->setQuery( $query );

		$badges	= $db->loadObjectList(); 

		return $badges;
	}

	/**
	 * Retrieve total history for a user based on a specific command
	 *
	 * @access	private
	 * @param	string	$command	The action string.
	 * @param	int		$userId		The actor's id.
	 * @return	int		The total number of items.
	 **/
	private function getTotal( $command , $userId )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_users_history' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId ) . ' '
				. 'AND ' . $db->nameQuote( 'command' ) . '=' . $db->Quote( $command );

		$db->setQuery( $query );

		return (int) $db->loadResult();
	}

}