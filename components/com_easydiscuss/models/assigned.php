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

class EasyDiscussModelAssigned extends EasyDiscussModel
{
	/**
	 * Category total
	 *
	 * @var integer
	 */
	private $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	private $_pagination = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	private $_data = null;

	public function __construct()
	{
		parent::__construct();

		$app		= JFactory::getApplication();

		$limit		= ($app->getCfg('list_limit') == 0) ? 5 : DiscussHelper::getListLimit();
		$limitstart	= JRequest::getVar('limitstart', 0, 'REQUEST');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return object
	 */
	public function getPagination()
	{
		return $this->_pagination;
	}

	/**
	 * Method to get an array of post assigned to
	 *
	 * @access public
	 * @return array
	 */
	public function _buildQuery( $userid = null )
	{
		$db		= DiscussHelper::getDBO();
		$date	= DiscussHelper::getDate();

		if( is_null($userid) )
		{
			$userid = JFactory::getUser()->id;
		}

		$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created` ) as `noofdays`, '
				. ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) ) as `daydiff`, '
				. ' TIMEDIFF(' . $db->Quote($date->toMySQL()). ', IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) ) as `timediff`,'
				. ' a.*, count(c.id) as `num_replies`, e.`title` AS `category`,'
				. ' pt.`suffix` AS post_type_suffix, pt.`title` AS post_type_title,'
				. ' IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) as `lastupdate`'
				. ' FROM `#__discuss_posts` AS a'
				. ' LEFT JOIN `#__discuss_posts` AS c ON c.`parent_id` = a.`id`'
				. ' 	AND c.`published` = ' . $db->Quote('1')
				. ' LEFT JOIN ' . $db->nameQuote( '#__discuss_category' ) . ' AS e ON e.`id` = a.`category_id`'
				. ' LEFT JOIN ' . $db->nameQuote( '#__discuss_assignment_map' ) . ' AS am ON am.`post_id` = a.id'
				. '	LEFT JOIN ' . $db->nameQuote('#__discuss_post_types') . ' AS pt ON a.`post_type` = pt.`alias`'
				. ' WHERE am.`created` = ( SELECT MAX(`created`) FROM `#__discuss_assignment_map` WHERE `post_id` = a.`id` )'
				. ' AND am.`assignee_id` = ' . $db->Quote( $userid )
				. ' AND a.`parent_id` = 0'
				. ' GROUP BY am.`created`'
				;

		return $query;
	}

	public function getTotalAssigned( $userId = null )
	{
		$db		= DiscussHelper::getDBO();
		$date	= DiscussHelper::getDate();

		if( is_null($userId) )
		{
			$userId = JFactory::getUser()->id;
		}

		$query		= array();
		$query[]	= 'SELECT COUNT(*)';
		$query[]	= 'FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' AS a';
		$query[]	= 'LEFT JOIN ' . $db->nameQuote( '#__discuss_assignment_map' ) . ' AS b';
		$query[]	= 'ON b.' . $db->nameQuote( 'post_id' ) . ' = a.' . $db->nameQuote( 'id' );
		$query[]	= 'WHERE';
		$query[]	= 'b.' . $db->nameQuote( 'assignee_id' ) . '=' . $db->Quote( $userId );
		$query[]	= 'AND a.' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( 0 );

		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );
		$total 		= $db->loadResult();

		if( !$total )
		{
			return 0;
		}

		return (int) $total;
	}

	public function getTotalSolved( $userId = null )
	{
		$db		= DiscussHelper::getDBO();
		$date	= DiscussHelper::getDate();

		if( is_null($userId) )
		{
			$userId = JFactory::getUser()->id;
		}

		$query		= array();
		$query[]	= 'SELECT COUNT(*)';
		$query[]	= 'FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' AS a';
		$query[]	= 'LEFT JOIN ' . $db->nameQuote( '#__discuss_assignment_map' ) . ' AS b';
		$query[]	= 'ON b.' . $db->nameQuote( 'post_id' ) . ' = a.' . $db->nameQuote( 'id' );
		$query[]	= 'WHERE';
		$query[]	= 'b.' . $db->nameQuote( 'assignee_id' ) . '=' . $db->Quote( $userId );
		$query[]	= 'AND a.' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( 0 );
		$query[]	= 'AND a.' . $db->nameQuote( 'isresolve' ) . '=' . $db->Quote( 1 );

		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );

		$total 		= $db->loadResult();

		if( !$total )
		{
			return 0;
		}

		return (int) $total;
	}

	public function getTotalUnresolved( $userId = null )
	{
		$db		= DiscussHelper::getDBO();
		$userId	= JFactory::getUser($userId)->id;

		$query		= array();
		$query[]	= 'SELECT COUNT(*)';
		$query[]	= 'FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' AS a';
		$query[]	= 'LEFT JOIN ' . $db->nameQuote( '#__discuss_assignment_map' ) . ' AS b';
		$query[]	= 'ON b.' . $db->nameQuote( 'post_id' ) . ' = a.' . $db->nameQuote( 'id' );
		$query[]	= 'WHERE';
		$query[]	= 'b.' . $db->nameQuote( 'assignee_id' ) . '=' . $db->Quote( $userId );
		$query[]	= 'AND a.' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( 0 );
		$query[]	= 'AND a.' . $db->nameQuote( 'isresolve' ) . '=' . $db->Quote(0);

		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );

		$total 		= $db->loadResult();

		if( !$total )
		{
			return 0;
		}

		return (int) $total;
	}



	public function getPosts()
	{
		if (empty($this->_data))
		{
			$query			= $this->_buildQuery();

			$limitstart		= $this->getState( 'limitstart');
			$limit			= $this->getState( 'limit');
			$this->_data	= $this->_getList($query, $limitstart , $limit);
		}

		return $this->_data;
	}

	/**
	 * Method to get the number of post assigned to
	 *
	 * @access public
	 * @return integer
	 */
	public function getPostCount()
	{

	}
}
