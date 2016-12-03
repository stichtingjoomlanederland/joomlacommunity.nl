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

class EasyDiscussModelReplies extends EasyDiscussAdminModel
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
		$limit		= $mainframe->getUserStateFromRequest('com_easydiscuss.replies.limit', 'limit', $mainframe->getCfg('list_limit') , 'int');
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

		$query	= 'SELECT a.*, 0 as `cnt`, 0 as `pendingcnt` FROM `#__discuss_posts` AS a';

		$query	.= $where . ' ' . $orderby;

		return $query;
	}

	function _buildQueryWhere()
	{
		$mainframe		= JFactory::getApplication();
		$db				= DiscussHelper::getDBO();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.replies.filter_state', 'filter_state', '', 'word' );

		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.replies.search', 'search', '', 'string' );
		$search			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

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
		}

		$where[]	= $db->nameQuote( 'a.parent_id' ) . '=' . $db->quote( '0' );

		$where		= count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' ;

		if ($search)
		{
			//$where	.= ' AND LOWER( a.title ) LIKE \'%' . $search . '%\' OR LOWER( a.content ) LIKE ';
			$where	.= ' AND LOWER( a.title ) LIKE '.$db->quote('%'.$search.'%').' OR LOWER( a.content ) LIKE '.$db->quote('%'.$search.'%');
		}

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.replies.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.replies.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );

		$orderby			= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', ordering';

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
	function &getPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	function publish( $blogs = array(), $publish = 1 )
	{
		if( count( $blogs ) > 0 )
		{
			$db		= DiscussHelper::getDBO();

			$blogs	= implode( ',' , $blogs );

			$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_posts' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $blogs . ')';

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


	function getTotalPosts()
	{
		$db		= DiscussHelper::getDBO();

		$query = 'SELECT COUNT(id) AS `total` FROM ' . $db->nameQuote('#__discuss_posts');
		$db->setQuery( $query );

		return $this->loadResult();
	}

	function getPostTags( $postId )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT a.* FROM `#__discuss_tags` AS a';
		$query	.= ' LEFT JOIN `#__discuss_posts_tags` AS b';
		$query	.= ' ON a.`id` = b.`tag_id`';
		$query	.= ' WHERE b.`post_id` = ' . $db->Quote($postId);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	function getPostRepliesCount( $postId )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM `#__discuss_posts`';
		$query	.= ' WHERE `parent_id` = ' . $db->Quote($postId);

		$db->setQuery($query);

		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;

	}

	function delete( $blogs = array())
	{
		if( count( $blogs ) > 0 )
		{
			$db		= DiscussHelper::getDBO();

			$blogs	= implode( ',' , $blogs );

			$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $blogs . ')';
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

	function revertAnwered( $blogs = array() )
	{
		if( count( $blogs ) > 0)
		{
			$db		= DiscussHelper::getDBO();
			$blogs	= implode( ',' , $blogs );

			$query	= 'SELECT `parent_id` FROM `#__discuss_posts`';
			$query	.= ' WHERE `id` IN (' . $blogs . ')';
			$query	.= ' AND `answered` = ' . $db->Quote('1');
			$query	.= ' AND `parent_id` != ' . $db->Quote('0');

			$db->setQuery( $query );
			$parent   = $db->loadResult();

			if( !empty( $parent ) )
			{
				$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_posts' ) . ' '
						. 'SET ' . $db->nameQuote( 'isresolve' ) . '=' . $db->Quote( 0 ) . ' '
						. 'WHERE `id` = ' . $db->Quote( $parent );
				$db->setQuery( $query );
				$db->query();
			}
		}
	}

}
