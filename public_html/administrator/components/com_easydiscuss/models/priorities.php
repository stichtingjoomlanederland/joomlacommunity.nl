<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__) . '/model.php';

class EasyDiscussModelPriorities extends EasyDiscussAdminModel
{
	public $_data = null;
	public $_pagination = null;
	public $_total;

	public function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();

		//get the number of events from database
		$limit = $mainframe->getUserStateFromRequest('com_easydiscuss.post_priorities.limit', 'limit', $mainframe->getCfg('list_limit') , 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	protected function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere();
		$orderby = $this->_buildQueryOrderBy();
		$db = ED::db();

		$query = 'SELECT a.* FROM `#__discuss_priorities` AS a '
				. $where . ' '
				. $orderby;

		return $query;
	}

	protected function _buildQueryWhere()
	{
		$mainframe = JFactory::getApplication();
		$db = ED::db();

		$filter_state = $mainframe->getUserStateFromRequest( 'com_easydiscuss.post_priorities.filter_state', 'filter_state', '', 'word' );
		$search = $mainframe->getUserStateFromRequest( 'com_easydiscuss.post_priorities.search', 'search', '', 'string' );
		$search = $db->getEscaped( trim(JString::strtolower( $search ) ) );


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

		if ($search)
		{
			$where[]	= 'LOWER(' . $db->nameQuote( 'title' ) . ') LIKE ' . $db->Quote( '%' . $search . '%' );
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	protected function _buildQueryOrderBy()
	{
		$mainframe = JFactory::getApplication();

		$filter_order = $mainframe->getUserStateFromRequest('com_easydiscuss.filter_order.filter_order', 'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_easydiscuss.filter_order.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;

		return $orderby;
	}

	/**
	 * Get all priorities
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getAllPriorities()
	{
		$db = $this->db;
		$query = 'SELECT * FROM ' . $db->qn("#__discuss_priorities");
		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves a list of priorities on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPriorities()
	{
		$db = $this->db;

		$query = $this->_buildQuery();

		// You need this in order limit to work.
		$this->_data = $this->_getList( $query , $this->getState('limitstart'), $this->getState('limit') );

		return $this->_data;
	}

	public function getPagination()
	{
		if (!$this->_pagination) {
			$this->_pagination = ED::getPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	public function getTotal()
	{
		// Load total number of rows
		if( empty($this->_total) )
		{
			$this->_total	= $this->_getListCount( $this->_buildQuery() );
		}

		return $this->_total;
	}

	public function getSuffix($alias = null)
	{
		$db = ED::db();
		$query	= 'SELECT `suffix` FROM ' . $db->nameQuote('#__discuss_post_types')
				. ' WHERE ' . $db->nameQuote('alias') . '=' . $db->quote($alias)
				. ' AND ' . $db->nameQuote('published') . '=' . $db->quote(1);

		$db->setQuery($query);
		$result	= $db->loadResult();

		return $result;
	}

	public function getTitle($alias = null)
	{
		$db = ED::db();
		$query	= 'SELECT `title` FROM ' . $db->nameQuote('#__discuss_post_types')
				. ' WHERE ' . $db->nameQuote('alias') . '=' . $db->quote($alias)
				. ' AND ' . $db->nameQuote('published') . '=' . $db->quote(1);

		$db->setQuery($query);
		$result	= $db->loadResult();

		return $result;
	}	

	public function setPostTagsBatch( $ids )
	{
		$db = ED::db();

		if( count( $ids ) > 0 )
		{

			$query	= 'SELECT a.`id`, a.`title`, a.`alias`, b.`post_id`';
			$query .= ' FROM `#__discuss_tags` AS a';
			$query .= ' LEFT JOIN `#__discuss_posts_tags` AS b';
			$query .= ' ON a.`id` = b.`tag_id`';
			if( count( $ids ) == 1 )
			{
				$query .= ' WHERE b.`post_id` = '.$db->Quote( $ids[0] );
			}
			else
			{
				$query .= ' WHERE b.`post_id` IN (' . implode(',', $ids) . ')';
			}

			$db->setQuery( $query );
			$result = $db->loadObjectList();

			if( count( $result ) > 0 )
			{
				foreach( $result as $item )
				{
					self::$_postTags[ $item->post_id ][] = $item;
				}
			}

			foreach( $ids as $id )
			{
				if(! isset( self::$_postTags[ $id ] ) )
				{
					self::$_postTags[ $id ] = array();
				}
			}


		}
	}

	/*
	 * method to get post tags.
	 *
	 * param postId - int
	 * return object list
	 */
	public function getPostTags($postId)
	{

		if( isset( self::$_postTags[ $postId ] ) )
		{
			return self::$_postTags[ $postId ];
		}


		$db = ED::db();

		$query	= 'SELECT a.`id`, a.`title`, a.`alias`';
		$query .= ' FROM `#__discuss_tags` AS a';
		$query .= ' LEFT JOIN `#__discuss_posts_tags` AS b';
		$query .= ' ON a.`id` = b.`tag_id`';
		$query .= ' WHERE b.`post_id` = '.$db->Quote($postId);
		$query .= ' AND a.`published`=' . $db->Quote( 1 );

		$db->setQuery($query);

		if($db->getErrorNum() > 0)
		{
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		$result	= $db->loadObjectList();

		self::$_postTags[ $postId ] = $result;
		return $result;

	}

	public function add( $tagId , $postId , $creationDate )
	{
		$db				= ED::db();

		$obj			= new stdClass();
		$obj->tag_id	= $tagId;
		$obj->post_id	= $postId;
		$obj->created	= $creationDate;

		return $db->insertObject( '#__discuss_posts_tags' , $obj );
	}

	public function deletePostTag($postId)
	{
		$db	= ED::db();

		$query	= ' DELETE FROM ' . $db->nameQuote('#__discuss_posts_tags')
				. ' WHERE ' . $db->nameQuote('post_id') . ' =  ' . $db->quote($postId);

		$db->setQuery($query);
		$result	= $db->Query();

		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}

		return $result;
	}


}
