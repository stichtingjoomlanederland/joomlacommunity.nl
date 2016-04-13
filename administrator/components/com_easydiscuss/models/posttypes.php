<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelPostTypes extends EasyDiscussAdminModel
{
	public $_data = null;
	public $_pagination = null;
	public $_total;

	public function __construct()
	{
		parent::__construct();

		$mainframe = $this->app;

		// Get the number of events from database
		$limit = $mainframe->getUserStateFromRequest('com_easydiscuss.post_types.limit', 'limit', $mainframe->getCfg('list_limit') , 'int');
		$limitstart	= $this->input->get('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Builds query.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	protected function _buildQuery($frontend = false)
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere($frontend);
		$orderby= $this->_buildQueryOrderBy();

		$query = 'SELECT a.* FROM `#__discuss_post_types` AS a '
				. $where . ' ' . $orderby;

		return $query;
	}

	/**
	 * Builds query where.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	protected function _buildQueryWhere($frontend = false)
	{
		$mainframe = $this->app;
		$db = $this->db;

		$filter_state = $mainframe->getUserStateFromRequest('com_easydiscuss.post_types.filter_state', 'filter_state', '', 'word');
		$search = $mainframe->getUserStateFromRequest('com_easydiscuss.post_types.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(JString::strtolower($search)));

		$where = array();

		// This is for frontend
		if ($frontend) {
			$where[] = $db->nameQuote('a.published') . '=' . $db->Quote('1');
		}

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = $db->nameQuote('a.published') . '=' . $db->Quote('1');
			}
			else if ($filter_state == 'U') {
				$where[] = $db->nameQuote('a.published') . '=' . $db->Quote('0');
			}
		}

		if ($search) {
			$where[] = 'LOWER(' . $db->nameQuote('title') . ') LIKE ' . $db->Quote('%' . $search . '%')
					. 'OR LOWER(' . $db->nameQuote('alias') . ') LIKE ' . $db->Quote('%' . $search . '%');
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	/**
	 * Builds query order by.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	protected function _buildQueryOrderBy()
	{
		$mainframe = JFactory::getApplication();

		$filter_order = $mainframe->getUserStateFromRequest('com_easydiscuss.customs.filter_order','filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_easydiscuss.customs.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;

		return $orderby;
	}

	/**
	 * Retrieves the list of post types available.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTypes($frontend = false)
	{
		$db = $this->db;

		$query = $this->_buildQuery($frontend);

		// You need this in order limit to work.
		$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

		return $this->_data;
	}

	/**
	 * Retrieves the pagination.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');

			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Retrieves the total number of post types available.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotal()
	{
		// Load total number of rows
		if (empty($this->_total)) {
			$this->_total = $this->_getListCount($this->_buildQuery());
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
		$db = DiscussHelper::getDBO();

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


		$db = DiscussHelper::getDBO();

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
		$db				= DiscussHelper::getDBO();

		$obj			= new stdClass();
		$obj->tag_id	= $tagId;
		$obj->post_id	= $postId;
		$obj->created	= $creationDate;

		return $db->insertObject( '#__discuss_posts_tags' , $obj );
	}

	public function deletePostTag($postId)
	{
		$db	= DiscussHelper::getDBO();

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
