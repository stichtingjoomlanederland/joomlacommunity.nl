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

class EasyDiscussModelThreaded extends EasyDiscussAdminModel
{
	/**
	 * Blogs data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Configuration data
	 *
	 * @var int	Total number of rows
	 **/
	var $_total;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		$mainframe 	= JFactory::getApplication();

		//get the number of events from database
		$limit		= $mainframe->getUserStateFromRequest('com_easydiscuss.posts.limit', 'limit', $mainframe->getCfg('list_limit') , 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getPosts( $userId = null )
	{
		if(empty($this->_data) )
		{
			$query = $this->_buildQuery( $userId );

			$this->_data	= $this->_getList( $this->_buildQuery() , $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}

	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		$db			= DiscussHelper::getDBO();

		$parentId	= JRequest::getString('pid', '');


// 		$query = 'SELECT IFNULL(a.id, b.id) AS pid, b.* FROM #__discuss_posts AS a ' .
// 				 '	RIGHT JOIN #__discuss_posts AS b ' .
// 				 '	ON a.id = b.parent_id';

		if(! empty($parentId))
		{
			$query	= 'select a.*, 0 as `cnt`, 0 as `pendingcnt`';
			$query	.= ' from `#__discuss_posts` AS a';
		}
		else
		{
// 			$query	= 'select a.*, count(b.`id`) as `cnt`';
// 			$query	.= ' from `#__discuss_posts` AS a LEFT JOIN `#__discuss_posts` AS b ON a.`id` = b.`parent_id`';

			$query	= 'select a.*, count(b.`id`) as `cnt`, count(c.`id`) as `pendingcnt`';
			$query	.= ' from `#__discuss_posts` AS a LEFT JOIN `#__discuss_posts` AS b ON a.`id` = b.`parent_id`';
			$query	.= ' LEFT JOIN `#__discuss_posts` AS c ON b.`id` = c.`id` and c.`published` = ' . $db->Quote('4');

		}

		$query	.= $where;

		if(empty($parentId))
		{
			$query	.= ' GROUP BY a.`id`';
		}

		$query	.= ' ' . $orderby;

		return $query;
	}

	function _buildQueryWhere()
	{
		$mainframe		= JFactory::getApplication();
		$db				= DiscussHelper::getDBO();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_state', 'filter_state', '', 'word' );
		$filter_category	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_state', 'category_id', '', 'int' );

		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.search', 'search', '', 'string' );
		$search			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$parentId		= JRequest::getString('pid', '');

		$where = array();


		if(! empty($parentId))
		{
			$where[]	= 'a.`parent_id` = ' . $db->Quote($parentId);
		}
		else
		{
			$where[]	= 'a.`parent_id` = ' . $db->Quote('0');
		}

		if( $filter_category )
		{
			$where[]	= 'a.' . $db->nameQuote( 'category_id' ) . '=' . $db->Quote( $filter_category );
		}

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = $db->nameQuote( 'a.published' ) . '=' . $db->Quote( '1' );
			}
			else if ($filter_state == 'U' )
			{
				$where[] = $db->nameQuote( 'a.published' ) . '=' . $db->Quote( '0' );
			}
			else if ($filter_state == 'A' )
			{
				$where[] = $db->nameQuote( 'a.published' ) . '=' . $db->Quote( '4' );
			}
		}

		if ($search)
		{
			$where[] = ' LOWER( a.`title` ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' ;

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', ordering';

		//$orderby	= ' ORDER BY pid, a.created, b.created';
		//$orderby	= ' ORDER BY a.created';

		return $orderby;
	}

	/**
	 * Method to return the total number of rows
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Load total number of rows
		if( empty($this->_total) )
		{
			$this->_total	= $this->_getListCount( $this->_buildQuery() );
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	function publish( &$posts = array(), $publish = 1 )
	{
		if( count( $posts ) > 0 )
		{
			$db		= DiscussHelper::getDBO();

			$ids	= implode( ',' , $posts );

			$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_posts' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $ids . ')';
			$db->setQuery( $query );

			if( !$db->query() )
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
		return false;
	}


	function getAllPosts()
	{
		$db = DiscussHelper::getDBO();

		$query = 'SELECT IFNULL(a.id, b.id) AS pid, b.* FROM #__discuss_posts AS a ' .
				 '	RIGHT JOIN #__discuss_posts AS b ' .
				 '	ON a.id = b.parent_id' .
				 ' ORDER BY a.created, b.created';

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		return $result;
	}
}
