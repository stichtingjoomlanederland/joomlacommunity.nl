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

class EasyDiscussModelSearch extends EasyDiscussModel
{
	/**
	 * Post total
	 *
	 * @var integer
	 */
	public $_total		= null;

	/**
	* Pagination object
	*
	* @var object
	*/
	public $_pagination	= null;

	/**
	* Post data array
	*
	* @var array
	*/
	public $_data		= null;

	/**
	 * Parent ID
	 *
	 * @var integer
	 */
	private $_parent	= null;
	private $_isaccept	= null;

	public function __construct($config = array())
	{
		parent::__construct($config);

		$mainframe	= JFactory::getApplication();
		$limit		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.search.limit', 'limit', DiscussHelper::getListLimit(), 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal($sort, $filter, $category='', $featuredOnly = 'all')
	{
		// user must call the getdata before they can call this method or else the total will be empty

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the posts
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination( $parent_id = 0, $sort = 'latest', $filter='', $category='', $featuredOnly = 'all' )
	{
		$this->_parent	= $parent_id;

		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			$this->_pagination	= DiscussHelper::getPagination( $this->getTotal($sort, $filter, $category, $featuredOnly), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	private function _buildQuery($sort = 'latest', $filter = '' , $category = '', $isCountOnly = false)
	{
		$my		= JFactory::getUser();
		$config	= DiscussHelper::getConfig();
		$date	= DiscussHelper::getDate();
		$db		= DiscussHelper::getDBO();

		// Get the WHERE and ORDER BY clauses for the query

		if(empty($this->_parent))
		{
			$parent_id = JRequest::getInt('parent_id', 0);
			$this->_parent = $parent_id;
		}

		$filteractive	= (empty($filter)) ? JRequest::getString('filter', 'allposts') : $filter;
		$where			= '';
		$orderby		= '';
		$queryExclude	= '';

		$excludeCats	= array();
		$excludeCats	= DiscussHelper::getPrivateCategories();

		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		// // Posts
		$pquery	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created` ) as `noofdays`, ';
		$pquery	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) ) as `daydiff`, ';
		$pquery	.= ' TIMEDIFF(' . $db->Quote($date->toMySQL()). ', IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) ) as `timediff`,';
		$pquery	.= ' IF(a.`parent_id` = 0, ' . $db->Quote('posts') . ', ' .  $db->Quote('replies') . ') as `itemtype`,';
		$pquery .= ' a.`id`, a.`title`, a.`content`, a.`user_id`, a.`category_id`, a.`parent_id`, a.`user_type`, a.`created` AS `created`, a.`poster_name`,';
		$pquery	.= ' b.`title` AS `category`, a.password, a.`featured` AS `featured`, a.`islock` AS `islock`, a.`isresolve` AS `isresolve`,';
		$pquery	.= ' IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) as `lastupdate`';
		$pquery	.= ' ,a.`legacy`, pt.`suffix` AS post_type_suffix, pt.`title` AS post_type_title';
		$pquery	.= ' FROM `#__discuss_posts` AS a';
		$pquery .= '	LEFT JOIN ' . $db->nameQuote( '#__discuss_category' ) . ' AS b ON a.`category_id`=b.`id`';
		$pquery .= '	LEFT JOIN ' . $db->nameQuote( '#__discuss_post_types' ) . ' AS pt ON a.`post_type`= pt.`alias`';
		$pquery	.= $this->_buildQueryWhere('posts', 'a', $category);
		$pquery	.= $queryExclude;


		// // Replies
		// $rquery	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created` ) as `noofdays`, ';
		// $rquery	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) ) as `daydiff`, ';
		// $rquery	.= ' TIMEDIFF(' . $db->Quote($date->toMySQL()). ', IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) ) as `timediff`,';
		// $rquery	.= ' ' . $db->Quote('replies') . ' as `itemtype`,';
		// $rquery .= ' a.`id`, a.`title`, a.`content`, a.`user_id`, a.`category_id`, a.`parent_id`, a.`user_type`,a.`created` AS `created`, a.`poster_name`,';
		// $rquery	.= ' b.`title` AS `category`, a.password, a.`featured` AS `featured`, a.`islock` AS `islock`, a.`isresolve` AS `isresolve`,';
		// $rquery	.= ' IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) as `lastupdate`';
		// $rquery	.= ' ,a.`legacy`, ' . $db->Quote('') . ' AS `post_type_suffix`, ' . $db->Quote( '' ) . ' AS `post_type_title`';
		// $rquery	.= ' FROM `#__discuss_posts` AS a';
		// $rquery .= '	LEFT JOIN ' . $db->nameQuote( '#__discuss_category' ) . ' AS b ON a.`category_id`=b.`id`';
		// $rquery	.= $this->_buildQueryWhere('replies', 'a', $category);
		// $rquery	.= $queryExclude;

		// Categories
		$cquery	= 'SELECT 0 as `noofdays`, ';
		$cquery	.= ' 0 as `daydiff`, ';
		$cquery	.= ' ' . $db->Quote( '00:00:00' ) . ' as `timediff`,';
		$cquery	.= ' ' . $db->Quote('category') . ' as `itemtype`,';
		$cquery .= ' a.`id`, a.`title`, a.`description` as `content`, a.`created_by` as `user_id`, a.`id` as `category_id`, 0 as `parent_id`, 0 AS `user_type`, a.`created` AS `created`, 0 as `poster_name`,';
		$cquery	.= ' a.`title` AS `category`, 0 AS `password`,0 as `featured`, 0 as `islock` , 0 as `isresolve`,';
		$cquery	.= ' a.`created` as `lastupdate`,';
		$cquery	.= ' 1 as `legacy`, ' . $db->Quote('') . ' AS `post_type_suffix`, ' . $db->Quote( '' ) . ' AS `post_type_title`';
		$cquery	.= ' FROM `#__discuss_category` AS a';
		$cquery	.= $this->_buildQueryWhere('category', 'a', $category);
		// $cquery .= ' order by a.`created` desc';


		if(! empty($excludeCats))
		{
			$cquery .= ' AND a.`id` NOT IN (' . implode(',', $excludeCats) . ')';
		}


		// if ($isCountOnly) {
		// 	$query  = 'SELECT count(1) FROM (';
		// 	$query  .= '(' . $pquery . ') UNION (' . $rquery . ') UNION (' . $cquery . ')';
		// 	$query  .=  ') as x';

		// } else {
		// 	$query  = 'SELECT * FROM (';
		// 	$query  .= '(' . $pquery . ') UNION (' . $rquery . ') UNION (' . $cquery . ')';
		// 	$query  .=  ') as x';
		// 	$query .= ' ORDER BY x.`lastupdate` DESC';
		// }

		$query  = 'SELECT SQL_CALC_FOUND_ROWS * FROM (';
		$query  .= '(' . $pquery . ') UNION (' . $cquery . ')';
		$query  .=  ') as x';
		$query .= ' ORDER BY x.`lastupdate` DESC';

		return $query;
	}

	private function _buildQueryWhere( $type, $tbl, $categoryId )
	{
		$mainframe	= JFactory::getApplication();
		$db			= DiscussHelper::getDBO();

		$search		= JRequest::getString( 'query' , '' );

		$phrase		= 'all';
		$where		= array();
		$extra		= array();

		$where[] = $tbl.'.`published` = ' . $db->Quote('1');

		// if( $type == 'posts' )
		// {
		// 	$where[] = $tbl.'.`parent_id` = ' . $db->Quote( '0' );
		// }

		// if( $type == 'replies' )
		// {
		// 	$where[] = $tbl.'.`parent_id` != ' . $db->Quote( '0' );
		// }

		// Private discussions should not show up
		$where[]	= $tbl . '.`private`=' . $db->Quote(0);

		if( $type == 'posts' || $type == 'replies' )
		{
			if( !empty($categoryId) )
			{
				$where[] = $tbl.'.`category_id` = ' . $db->Quote( $categoryId );
			}

			$words = explode(' ', $search);
			$wheres = array();
			foreach ($words as $word) {

				$word		= $db->Quote('%'.$db->getEscaped($word, true).'%', false);
				$wheres2	= array();

				if( $type == 'posts' )
					$wheres2[]	= 'a.title LIKE '.$word;

				$wheres2[]	= 'a.content LIKE '.$word;
				$wheres[]	= implode(' OR ', $wheres2);
			}
			$whereString = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
			$where[]	= '(' . $whereString . ')';

		}
		else if( $type == 'category' )
		{
			if( !empty($categoryId) )
			{
				$where[] 	= 'a.`id` = ' . $db->Quote( $categoryId );
			}

			$extra[]	= 'a.`title` LIKE ' . $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$extra		= '(' . implode( ') OR (', $extra ) . ')';
			$where[]	= '(' . $extra . ')';
		}

		$where		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	private function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.search.filter_order', 		'filter_order', 	'created DESC'	, 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.search.filter_order_Dir',	'filter_order_Dir',	''				, 'word' );

		$orderby			= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to get posts item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData( $usePagination = true, $sort = 'latest' , $limitstart = null, $filter = '' , $category = '', $limit = null )
	{
		$db			= DiscussHelper::getDBO();

		if (empty($this->_data))
		{

			$query = $this->_buildQuery( $sort, $filter , $category );

			if($usePagination)
			{
				$limitstart		= is_null( $limitstart ) ? $this->getState( 'limitstart') : $limitstart;
				$limit			= is_null( $limit ) ? $this->getState( 'limit') : $limit;
				// $this->_data	= $this->_getList($query, $limitstart , $limit);
			} else {
				$limitstart     = 0;
				$limit			= is_null( $limit ) ? $this->getState( 'limit') : $limit;
			}

			$query .= ' LIMIT ' . $limitstart . ', ' . $limit;

			$db->setQuery($query);
			$this->_data	= $db->loadObjectList();

			// now execute found_row() to get the number of records found.
			$cntQuery = 'select FOUND_ROWS()';
			$db->setQuery( $cntQuery );
			$this->_total	= $db->loadResult();
		}

		return $this->_data;
	}

	public function clearData()
	{
		$this->_data = null;
	}

}
