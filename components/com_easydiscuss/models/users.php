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

class EasyDiscussModelUsers extends EasyDiscussModel
{
	/**
	 * Tag total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Tag data array
	 *
	 * @var array
	 */
	var $_data = null;

	function __construct()
	{
		parent::__construct();


		$mainframe	= JFactory::getApplication();

		$limit			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.limit', 'limit', DiscussHelper::getListLimit(), 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Get the latest user that registered on the site.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getLatestUser()
	{
		$db		= DiscussHelper::getDBO();

		$query		= array();
		$query[]	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__users' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'block' ) . '=' . $db->Quote( 0 );
		$query[]	= 'ORDER BY ' . $db->nameQuote( 'id' ) . ' DESC';
		$query[]	= 'LIMIT 1';

		$query		= implode( ' ' , $query );
		$db->setQuery( $query );

		$id			= $db->loadResult();

		return $id;
	}

	/**
	 * Get logged in users from the site.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getOnlineUsers()
	{
		$jConfig	= DiscussHelper::getJConfig();
		$lifespan	= $jConfig->getValue('lifetime');
		$online		= time() - ($lifespan * 60);

		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT a.* FROM ' . $db->nameQuote( '#__discuss_views' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b '
				. 'ON a.`user_id`=b.`id`'
				. 'INNER JOIN ' . $db->nameQuote( '#__session' ) . ' AS c '
				. 'ON c.`userid`=b.`id` '
				. 'WHERE a.`user_id` !=' . $db->Quote( 0 )
				. 'AND c.`time` >= ' . $db->Quote( $online ) . ' '
				. 'AND c.`client_id` = ' . $db->Quote('0') . ' '
				. 'GROUP BY a.`user_id`';

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		$users	= array();

		foreach( $result as $res )
		{
			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load( $res->user_id );
			$users[]	= $profile;
		}

		return $users;
	}

	/**
	 * Get logged in users from the site.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getPageViewers( $hash )
	{
		$jConfig	= DiscussHelper::getJConfig();
		$lifespan	= $jConfig->getValue('lifetime');
		$online		= time() - ($lifespan * 60);

		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT a.* FROM ' . $db->nameQuote( '#__discuss_views' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b '
				. 'ON a.`user_id`=b.`id`'
				. 'INNER JOIN ' . $db->nameQuote( '#__session' ) . ' AS c '
				. 'ON c.`userid`=b.`id` '
				. 'WHERE ' . $db->nameQuote( 'hash' ) . '=' . $db->Quote( $hash ) . ' '
				. 'AND a.`user_id` !=' . $db->Quote( 0 )
				. 'AND c.`time` >= ' . $db->Quote( $online ) . ' '
				. 'AND c.`client_id` = ' . $db->Quote('0') . ' '
				. 'GROUP BY a.`user_id`';

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		$users	= array();

		foreach( $result as $res )
		{
			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load( $res->user_id );
			$users[]	= $profile;
		}

		return $users;
	}

	/**
	 * Get total number of guests that is viewing the site.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getTotalGuests()
	{
		$db			= DiscussHelper::getDBO();
		$jConfig	= DiscussHelper::getJConfig();
		$lifespan	= $jConfig->getValue('lifetime');
		$online		= time() - ($lifespan * 60);

		$query		= array();
		$query[]	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__session' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'guest' ) . '=' . $db->Quote( 1 );
		$query[]	= 'AND ' . $db->nameQuote( 'time' ) . '>=' . $db->Quote( $online );
		$query		= implode( ' ' , $query );
		$db->setQuery( $query );

		$total		= $db->loadResult();

		return $total;
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		$db = DiscussHelper::getDBO();


		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery( true );
			$db->setQuery( $query );

			//$this->_total = $this->_getListCount($query);
			$this->_total = $db->loadResult();
		}

		return $this->_total;
	}

	function getTotalUsers()
	{

		$db			= DiscussHelper::getDBO();
		$query 		= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__users' ) . ' AS u';
		$query 		.= ' WHERE u.' . $db->nameQuote( 'block' ) . '=' . $db->Quote( 0 );

		$db->setQuery( $query );

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			$this->_pagination	= DiscussHelper::getPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	function _buildQuery( $isTotalCnt = false, $name = '' )
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere( $name );
		$orderby	= $this->_buildQueryOrderBy();
		$groupby	= $this->_buildQueryGroupBy();
		$db			= DiscussHelper::getDBO();


		if( $isTotalCnt )
		{
			$query  = 'select count(id) from `#__users` as u';
			$query .= $where;
		}
		else
		{
			$query		= 'SELECT u.`id`, u.`name`, u.`username`, u.`email`, u.`registerDate`, u.`lastvisitDate`, u.`params`, u.`block` '
						. ', d.`nickname`, d.`avatar`, d.`description`, d.`url`, d.`alias` '
						. 'FROM ' . $db->nameQuote( '#__users' ) . ' AS u '
						. 'LEFT JOIN ' . $db->nameQuote( '#__discuss_users' ) . ' AS d ON d.`id` = u.`id` '
						. $where
						. $orderby;
		}

		return $query;
	}

	function _buildQueryWhere( $name = '' )
	{
		$mainframe		= JFactory::getApplication();
		$db				= DiscussHelper::getDBO();
		$config 		= DiscussHelper::getConfig();
		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.filter_state', 'filter_state', '', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.search', 'search', '', 'string' );
		$search			= $db->getEscaped( trim(JString::strtolower( $search ) ) );
		

		// Sanity checks!!
		$name 			= $db->getEscaped( $name );

		$where			= array();

		$where[]		= 'u.`block`=' . $db->Quote( 0 );

		if ($search)
		{
			$where[] = ' LOWER( name ) LIKE \'%' . $search . '%\' ';
		}
		elseif( !empty($name) )
		{

			$displayname	= $config->get('layout_nameformat');

			switch($displayname)
			{
				case "name" :
					$where[] = ' LOWER( name ) LIKE \'%' . $name . '%\' ';
					break;
				case "username" :
					$where[] = ' LOWER( username ) LIKE \'%' . $name . '%\' ';
					break;
				case "nickname" :
				default :
					// nickname and name is the same, just different table
					$where[] = ' LOWER( d.nickname ) LIKE \'%' . $name . '%\' ';
					break;
			}

			// $where[] = ' LOWER( name ) LIKE \'%' . $name . '%\' ';
		}

		$where[]		= 'u.`id` != ' . $db->Quote( 0 );

		$where		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.filter_order', 		'sort', 	'name ASC'	, 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.filter_order_Dir',	'dir',		''			, 'word' );

// 		if ( $filter_order == 'name' )
// 		{
// 			$filter_order		= '`name`';
// 			$filter_order_Dir	= 'ASC';
// 		}
// 		elseif ( $filter_order == 'lastvisit' )
// 		{
// 			$filter_order		= '`lastvisitDate`';
// 			$filter_order_Dir	= 'DESC';
// 		}
// 		elseif ( $filter_order == 'latest' )
// 		{
// 			$filter_order		= '`registerDate`';
// 			$filter_order_Dir	= 'DESC';
// 		}
// 		else
// 		{
// 			$filter_order		= '`name`';
// 			$filter_order_Dir	= 'ASC';
// 		}

		$filter_order		= '`name`';
		$filter_order_Dir	= 'ASC';

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;


		return $orderby;
	}

	function _buildQueryGroupBy()
	{
		return ' GROUP BY u.`id`';
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getData( $name = '' )
	{
		$db = DiscussHelper::getDBO();

		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery( false, $name );

			$result = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

// 			if( count( $result ) > 0 )
// 			{
// 				$tmpIds = array();
// 				foreach( $result as $item)
// 				{
// 					$tmpIds[] = $item->id;
// 				}
//
// 				$numberOfRec    = 3;
// 				$idx            = 0;
//
// 				while( count( $tmpIds ) >= $idx )
// 				{
// 					$ids    = array_slice( $tmpIds, $idx, $numberOfRec );
// 					$idx    += $numberOfRec;
//
// 					$ids = implode( ',', $ids );
//
// 					$query	= 'select user_id, max(created) as `lastPostCreated`, count(id) as `postCount`';
// 					$query .= ' from #__discuss_posts';
// 					$query .= ' where `user_id` IN (' . $ids . ')';
// 					$query .= ' group by `user_id`';
//
// 					$db->setQuery($query);
//
// 					$postResult = $db->loadObjectList();
//
// 					if( count( $postResult ) > 0 )
// 					{
// 						foreach( $postResult as $prItem )
// 						{
// 							//now we attach the counts into main resultset.
// 							for($i = 0; $i < count($result); $i++ )
// 							{
// 								$row =& $result[$i];
// 								if( $row->id == $prItem->user_id)
// 								{
// 									$row->lastPostCreated 	= $prItem->lastPostCreated;
// 									$row->postCount 		= $prItem->postCount;
// 								}
// 							}
// 						}
// 					}
// 				}
// 			}

			$this->_data = $result;
		}

		return $this->_data;
	}

	public function getAllEmails( $exclusion = array(), $force = false )
	{
		$db 	= DiscussHelper::getDBO();
		$query	= 'SELECT `email` FROM ' . $db->nameQuote( '#__users' );

		if( !$force )
		{
			$query .= ' WHERE `block` = 0 ';
		}

		if( !is_array( $exclusion ) )
		{
			$exclusion	= array( $exclusion );
		}

		if( !empty( $exclusion ) )
		{
			$query	.= ' AND ' . $db->nameQuote( 'email' ) . ' NOT IN (';
			for( $i = 0; $i < count( $exclusion ); $i++ )
			{
				$query	.= $db->Quote( $exclusion[ $i ] );

				if( next( $exclusion ) !== false )
				{
					$query	.= ',';
				}
			}
			$query	.= ')';
		}

		$db->setQuery( $query );

		$emails = $db->loadResultArray();

		return $emails;
	}
}
