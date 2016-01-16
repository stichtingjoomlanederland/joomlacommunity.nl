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

class EasyDiscussModelPosts extends EasyDiscussAdminModel
{
	/**
	 * Blogs data array
	 *
	 * @var array
	 */
	protected $_data = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	protected $_pagination = null;

	/**
	 * Configuration data
	 *
	 * @var int	Total number of rows
	 **/
	protected $_total;

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

		$filter_tag			= JRequest::getInt( 'tagid' , '' );


		$query	= 'SELECT DISTINCT a.* FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' AS a ';


// 		if( !empty( $filter_tag ) )
// 		{
// 			$query	.= ' INNER JOIN #__discuss_posts_tag AS b '
// 						. 'ON a.`id`=b.`post_id` AND b.`tag_id`=' . $db->Quote( $filter_tag );
// 		}
		$query	.= $where . ' ' . $orderby;

		return $query;
	}

	function _buildQueryWhere()
	{
		$mainframe			= JFactory::getApplication();
		$db					= DiscussHelper::getDBO();

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_state', 'filter_state', '', 'word' );

		$search 			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

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
// 			else if ($filter_state == 'S' )
// 			{
// 				$where[] = $db->nameQuote( 'a.published' ) . '=' . $db->Quote( '2' );
// 			}
		}

		if ($search)
		{
			$where[] = ' LOWER( a.title ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' ;

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );

		$orderby	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', ordering';

		$orderby	= ' ORDER BY';

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

			// We need to update the parent post last replied date
			foreach( $posts as $postId )
			{
				// Load the reply
				$reply = DiscussHelper::getTable( 'Posts' );
				$reply->load( $postId );

				// We only need replies
				if( !empty( $reply->parent_id ) )
				{
					$parent = DiscussHelper::getTable( 'Post' );
					$parent->load( $reply->parent_id );

					// Check if current reply date is more than the last replied date of the parent to determine if this reply is new or is an old pending moderate reply.
					if( $reply->created > $parent->replied )
					{
						$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_posts' ) . ' '
								. 'SET ' . $db->nameQuote( 'replied' ) . '=' . $db->Quote( $reply->created ) . ' '
								. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $parent->id );

						$db->setQuery( $query );

						if( !$db->query() )
						{
							$this->setError($this->_db->getErrorMsg());
							return false;
						}
					}
				}
			}



			return true;
		}
		return false;
	}


	function getTotalPosts()
	{
		$db		= DiscussHelper::getDBO();

		$query = 'SELECT COUNT(id) AS total FROM #__discuss_posts';
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

	function delete( &$posts = array())
	{
		if( count( $posts ) > 0 )
		{
			$db		= DiscussHelper::getDBO();

			$ids	= implode( ',' , $posts );

			$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
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
			$parent	= $db->loadResult();

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


	/**
	 * Removes all finder indexed items for replies
	 *
	 * @since	3.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function deleteRepliesInFinder($postId)
	{
		$db = JFactory::getDBO();

		$query = array();
		$query[] = 'DELETE FROM ' . $db->quoteName('#__finder_links');
		$query[] = 'WHERE ' . $db->quoteName('url') . ' LIKE (' . $db->Quote('%index.php?option=com_easydiscuss&view=post&id=' . $postId . '#reply-%') . ')';

		$query = implode(' ', $query);

		$db->setQuery($query);
		
		return $db->Query();
	}

}
