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

class EasyDiscussModelBadges extends EasyDiscussModel
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

		$limit			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.badges.limit', 'limit', DiscussHelper::getListLimit(), 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieve a list of badges from the site
	 *
	 * @access public
	 *
	 * @param	null
	 * @return	Array	An array of DiscussBadges object
	 */
	public function getBadges( $filter = array() )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT a.* FROM ' . $db->nameQuote( '#__discuss_badges' ) . ' AS a';

		if( isset( $filter[ 'user' ] ) )
		{
			$query	.= ' INNER JOIN ' . $db->nameQuote( '#__discuss_badges_users' ) . ' AS b '
					 . 'ON b.' . $db->nameQuote( 'badge_id' ) . '=a.' . $db->nameQuote( 'id' ) . ' '
					 . 'AND b.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		}

		$query	.= ' WHERE a.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );

		if( isset( $filter['user'] ) )
		{
			$query	.= ' AND b.' . $db->nameQuote( 'user_id') . '=' . $db->Quote( $filter['user' ] );
		}
		$db->setQuery( $query );

		$result	= $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		$badges	= array();

		foreach( $result as $res )
		{
			$badge	= DiscussHelper::getTable( 'Badges' );
			$badge->bind( $res );

			$badges[]	= $badge;
		}

		return $badges;
	}

	/**
	 * Delete badges based on user id
	 *
	 * @access public
	 *
	 * @param
	 * @return state
	 */
	public function removeBadges( $userId = null )
	{
		$db = DiscussHelper::getDBO();

		$query = 'DELETE FROM ' . $db->nameQuote( '#__discuss_badges_users' )
				. ' WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );

		$db->setQuery( $query );

		if( !$db->query() )
		{
			return false;
		}
		return true;
	}
}
