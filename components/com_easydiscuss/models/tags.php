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

class EasyDiscussModelTags extends EasyDiscussModel
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

		$limit			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.limit', 'limit', DiscussHelper::getListLimit(), 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
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
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		$db			= DiscussHelper::getDBO();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_tags' )
				. $where . ' '
				. $orderby;

		return $query;
	}

	function _buildQueryWhere()
	{
		$mainframe		= JFactory::getApplication();
		$db				= DiscussHelper::getDBO();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.filter_state', 'filter_state', '', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.search', 'search', '', 'string' );
		$search			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' );
			}
			else if ($filter_state == 'U' )
			{
				$where[] = $db->nameQuote( 'published' ) . '=' . $db->Quote( '0' );
			}
		}

		if ($search)
		{
			$where[] = ' LOWER( title ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.filter_order', 		'filter_order', 	'title ASC'	, 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.filter_order_Dir',	'filter_order_Dir',		''			, 'word' );

		$orderby			= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Method to publish or unpublish tags
	 *
	 * @access public
	 * @return array
	 */
	function publish( $tags = array(), $publish = 1 )
	{
		if( count( $tags ) > 0 )
		{
			$db		= DiscussHelper::getDBO();

			$tags	= implode( ',' , $tags );

			$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_tags' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $tags . ')';
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

	function searchTag($title)
	{
		$db	= DiscussHelper::getDBO();

		$query	= 'SELECT ' . $db->nameQuote('id') . ' '
				. 'FROM ' 	. $db->nameQuote('#__discuss_tags') . ' '
				. 'WHERE ' 	. $db->nameQuote('title') . ' = ' . $db->quote($title) . ' '
				. 'LIMIT 1';
		$db->setQuery($query);

		$result	= $db->loadObject();

		return $result;
	}


	function getTagName($id)
	{
		$db	= DiscussHelper::getDBO();

		$query	= 'SELECT ' . $db->nameQuote('title') . ' '
				. 'FROM ' 	. $db->nameQuote('#__discuss_tags') . ' '
				. 'WHERE ' 	. $db->nameQuote('id') . ' = ' . $db->quote($id) . ' '
				. 'LIMIT 1';
		$db->setQuery($query);

		$result	= $db->loadResult();

		return $result;
	}

	function getTagNames($ids)
	{
		$names = array();
		foreach ($ids as $id)
		{
			$names[] = $this->getTagName($id);
		}

		$names = implode(' + ', $names);

		return $names;
	}


	/**
	 * Method to get total tags created so far iregardless the status.
	 *
	 * @access public
	 * @return integer
	 */
	function getTotalTags( $userId = 0)
	{
		$db		= DiscussHelper::getDBO();
		$where	= array();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_tags' );

		if(! empty($userId))
			$where[]  = '`user_id` = ' . $db->Quote($userId);

		$extra	= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$query	= $query . $extra;


		$db->setQuery( $query );

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	function isExist( $tagName, $excludeTagIds = '0' )
	{
		$db = DiscussHelper::getDBO();

		$query  = 'SELECT COUNT(1) FROM #__discuss_tags';
		$query  .= ' WHERE `title` = ' . $db->Quote($tagName);
		if($excludeTagIds != '0')
			$query  .= ' AND `id` != ' . $db->Quote($excludeTagIds);

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	function getTagCloud($limit='', $order='title', $sort='asc' , $userId = '' )
	{
		$db = DiscussHelper::getDBO();

		$query	=   'select a.`id`, a.`title`, a.`alias`, a.`created`, count(c.`id`) as `post_count`';
		$query	.=  ' from #__discuss_tags as a';
		$query	.=  '    left join #__discuss_posts_tags as b';
		$query	.=  '    on a.`id` = b.`tag_id`';
		$query	.=  '    left join #__discuss_posts as c';
		$query	.=  '    on b.post_id = c.id';
		$query	.=  '    and c.`private`=' . $db->Quote(0);
		$query	.=  '    and c.`published` = ' . $db->Quote('1');

		$exclude = DiscussHelper::getPrivateCategories();

		if (!empty($exclude)) {
			$query .= ' AND c.`category_id` NOT IN(' . implode(',', $exclude) . ')';
		}
		

		$query	.= 	' where a.`published` = ' . $db->Quote('1');

		if( !empty( $userId ) )
		{
			$query	.= ' AND a.`user_id`=' . $db->Quote( $userId );
		}


		$query	.=  ' group by (a.`id`)';

		//order
		switch($order)
		{
			case 'postcount':
				$query	.=  ' ORDER BY (post_count)';
				break;
			case 'title':
			default:
				$query	.=  ' ORDER BY (a.`title`)';
		}

		//sort
		switch($sort)
		{
			case 'asc':
				$query	.=  ' asc ';
				break;
			case 'desc':
			default:
				$query	.=  ' desc ';
		}

		//limit
		if(!empty($limit))
		{
			$query	.=  ' LIMIT ' . (INT)$limit;
		}

		$db->setQuery($query);

		$result = $db->loadObjectList();
		return $result;
	}

	function getTags($count="")
	{
		$db		= DiscussHelper::getDBO();

		$query	=   ' SELECT `id`, `title`, `alias` ';
		$query	.=  ' FROM #__discuss_tags ';
		$query	.=  ' WHERE `published` = 1 ';
		$query	.=  ' ORDER BY `title`';

		if(!empty($count))
		{
			$query	.=  ' LIMIT ' . $count;
		}


		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
}
