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

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelNotification extends EasyDiscussModel
{
	/**
	 * Post total
	 *
	 * @var integer
	 */
	var $_total 	= null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Post data array
	 *
	 * @var array
	 */
	var $_data 		= null;

	/**
	 * Parent ID
	 *
	 * @var integer
	 */
	var $_parent	= null;
	var $_isaccept	= null;

	function __construct()
	{
		parent::__construct();


		$mainframe	= JFactory::getApplication();

		$limit			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.limit', 'limit', DiscussHelper::getListLimit(), 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to test if there are any notifications for any particular user.
	 *
	 * @access public
	 *
	 * @param	int $userId		The user id to test on.
	 * @return	boolean
	 */
	public function getTotalNotifications( $userId )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT COUNT(1) FROM ( '
				. 'SELECT ' . $db->nameQuote( 'id') . ' FROM ' . $db->nameQuote( '#__discuss_notifications' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'target' ) . '=' . $db->Quote( $userId ) . ' '
				. 'AND ' . $db->nameQuote( 'state' ) . '=' . $db->Quote( DISCUSS_NOTIFICATION_NEW ) . ' '
				. 'GROUP BY ' . $db->nameQuote( 'cid' ) . ',' . $db->nameQuote( 'type' ) . ' '
				. ') as a';

		$db->setQuery( $query );

		$notifications	= $db->loadResult();

		return $notifications;
	}

	/**
	 * Returns a list of notifications for a specific user
	 *
	 * @access	public
	 * @param	int	$userId		The target
	 * @param	int	$limit		The limit of notifications to fetch
	 * @return	array
	 **/
	public function getNotifications( $userid , $showNewOnly = false , $limit = 10 )
	{
		$db		= DiscussHelper::getDBO();

		$limit	= (int) $limit;

		$query	= 'SELECT x.items , a.`id` , a.`cid` , a.`type` , a.`title`, a.`target`, a.`author`, a.`permalink`, a.`created`, a.`component`,';
		$query	.= ' a.`state`, a.`favicon`, DATE_FORMAT( a.`created`, "%Y%m%d" ) as `day`';
		$query	.= ' FROM `#__discuss_notifications` a';
		$query	.= ' inner join (';
		$query	.= '    select count(b.`cid`) as items, max( b.id ) as `id`, ';
		$query	.= '		   DATE_FORMAT( b.`created`, "%Y%m%d" ) as `day` ';
		$query	.= '	from `#__discuss_notifications` b';
		$query	.= '	WHERE b.`target`=' . $db->Quote( $userid );
		if( $showNewOnly )
		{
			$query	.= '	AND b.`state`=' . $db->Quote( 1 ) ;
		}
		$query	.= ' GROUP BY b.`cid`,b.`type`,`day`';
		$query	.= '  ) as x ON x.id = a.id';

		$query	.= ' WHERE a.`target`=' . $db->Quote( $userid );
		if( $showNewOnly )
		{
			$query	.= ' AND a.`state`=' . $db->Quote( 1 );
		}

		$query  .= ' ORDER BY a.`id` DESC';
		$query  .= ' LIMIT 0,' . $limit;

		$db->setQuery( $query );

		return $db->loadObjectList();
	}

	/**
	 * Updates notifications since the browser / viewer / user has already read the topic
	 *
	 * @access	public
	 * @param	int	$userId		The current user that is viewing
	 * @param	int $cid		The unique id of notification to clear
	 * @param	Array $types	The type of notification to clear
	 **/
	public function markRead( $userId , $cid = false , $types )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'UPDATE #__discuss_notifications '
				. 'SET ' . $db->nameQuote( 'state' ) . '=' . $db->Quote( DISCUSS_NOTIFICATION_READ ) . ' '
				. 'WHERE ' . $db->nameQuote( 'target' ) . '=' . $db->Quote( $userId );


		// If cid is not provided, caller might just want to clear all notifications for a specific user when they view certain actions.
		if( $cid )
		{
			$query	.= ' AND ' . $db->nameQuote( 'cid' ) . '=' . $db->Quote( $cid );
		}


		if( !is_array( $types ) )
		{
			$types	= array( $types );
		}

		$query	.= ' AND ' . $db->nameQuote( 'type' ) . ' IN(';
		for( $i = 0; $i < count( $types ); $i++ )
		{
			$query	.= $db->Quote( $types[ $i ] );

			if( next( $types ) !== false )
			{
				$query	.= ',';
			}
		}
		$query	.= ')';
		$db->setQuery( $query );

		$db->Query();
	}

	public function deleteNotifications( $postId = null )
	{
		if( !empty($postId) )
		{
			$db = DiscussHelper::getDBO();

			$query = 'DELETE FROM ' . $db->nameQuote( '#__discuss_notifications' )
					. ' WHERE ' . $db->nameQuote( 'cid' ) . '=' . $db->Quote( $postId );

			$db->setQuery( $query );
			$db->Query();
		}
	}
}
