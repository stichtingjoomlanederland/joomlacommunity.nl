<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
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

class EasyDiscussModelSearch extends EasyDiscussAdminModel
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

		$limit		= $this->app->getUserStateFromRequest( 'com_easydiscuss.search.limit', 'limit', DiscussHelper::getListLimit(), 'int');
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
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
	public function getPagination($parent_id = 0)
	{
		$this->_parent	= $parent_id;

		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
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
	private function _buildQuery($sort = 'latest', $filter = '' , $category = '', $isCountOnly = false, $tags = array(), $postType = '')
	{
		$my = $this->my;
		$config = $this->config;
		$db = $this->db;
		$date = ED::date();

		// Get the WHERE and ORDER BY clauses for the query
		if (empty($this->_parent)) {
			$parent_id = $this->input->get('parent_id', 0, 'int');
			$this->_parent = $parent_id;
		}

		$filteractive	= (empty($filter)) ? $this->input->get('filter', 'allposts', 'string') : $filter;
		$where = '';
		$orderby = '';

		// // Posts
		$pquery	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created` ) as `noofdays`, ';
		$pquery	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) ) as `daydiff`, ';
		$pquery	.= ' TIMEDIFF(' . $db->Quote($date->toMySQL()). ', IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) ) as `timediff`,';
		$pquery	.= ' IF(a.`parent_id` = 0, ' . $db->Quote('posts') . ', ' .  $db->Quote('replies') . ') as `itemtype`,';
		$pquery .= ' a.`id`, a.`title`, a.`content`, a.`preview`, a.`user_id`, a.`category_id`, a.`parent_id`, a.`user_type`, a.`created` AS `created`, a.`poster_name`,';
		$pquery	.= ' b.`title` AS `category`, a.password, a.`featured` AS `featured`, a.`islock` AS `islock`, a.`isresolve` AS `isresolve`,';
		$pquery	.= ' IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) as `lastupdate`';
		$pquery	.= ' ,a.`legacy`, pt.`suffix` AS post_type_suffix, pt.`title` AS post_type_title, a.`thread_id`';
		$pquery	.= ' FROM `#__discuss_posts` AS a';
		$pquery .= '	LEFT JOIN ' . $db->nameQuote( '#__discuss_category' ) . ' AS b ON a.`category_id`=b.`id`';
		$pquery .= '	LEFT JOIN ' . $db->nameQuote( '#__discuss_post_types' ) . ' AS pt ON a.`post_type`= pt.`alias`';
		if ($tags) {
			$pquery .= '	INNER JOIN ' . $db->nameQuote( '#__discuss_posts_tags' ) . ' AS ptg ON a.`id`= ptg.`post_id`';
		}

		// exclude blocked user posts.
		$pquery .= " left join `#__users` as uu on a.`user_id` = uu.`id`";

		$pquery	.= $this->_buildQueryWhere('posts', 'a', $category, $postType);
		if ($tags) {
			$pquery .= ' AND ptg.tag_id IN (' . $db->implode($tags) . ')';
		}

		$pquery .= " and (uu.`block` = 0 OR uu.`id` IS NULL)";

		// Categories
		$cquery	= 'SELECT 0 as `noofdays`, ';
		$cquery	.= ' 0 as `daydiff`, ';
		$cquery	.= ' ' . $db->Quote( '00:00:00' ) . ' as `timediff`,';
		$cquery	.= ' ' . $db->Quote('category') . ' as `itemtype`,';
		$cquery .= ' a.`id`, a.`title`, a.`description` as `content`, a.`description` as `preview`, a.`created_by` as `user_id`, a.`id` as `category_id`, 0 as `parent_id`, 0 AS `user_type`, a.`created` AS `created`, 0 as `poster_name`,';
		$cquery	.= ' a.`title` AS `category`, 0 AS `password`,0 as `featured`, 0 as `islock` , 0 as `isresolve`,';
		$cquery	.= ' a.`created` as `lastupdate`,';
		$cquery	.= ' 1 as `legacy`, ' . $db->Quote('') . ' AS `post_type_suffix`, ' . $db->Quote( '' ) . ' AS `post_type_title`, ' . $db->Quote('') . ' AS `thread_id`';
		$cquery	.= ' FROM `#__discuss_category` AS a';
		$cquery	.= $this->_buildQueryWhere('category', 'a', $category);

		$query  = 'SELECT SQL_CALC_FOUND_ROWS * FROM (';
		$query  .= '(' . $pquery . ') UNION (' . $cquery . ')';
		$query  .=  ') as x';
		$query .= ' ORDER BY x.`lastupdate` DESC';

		return $query;
	}

	private function _buildQueryWhere($type, $tbl, $categoryId = '', $postType = '')
	{
		$mainframe = $this->app;
		$db = $this->db;

		$search = $this->input->get('query', '', 'string');

		$phrase = 'all';
		$where = array();
		$extra = array();

		$includeCatChilds = false;

		$where[] = $tbl.'.`published` = ' . $db->Quote('1');

		if ($type == 'posts' || $type == 'replies') {

			$includeCatChilds = true;

			if (!ED::isSiteAdmin() && !ED::isModerator()) {
				$where[] = $tbl . '.`private`=' . $db->Quote(0);
			}

			if ($categoryId) {
				if (count($categoryId) == 1) {
					$where[] = $tbl.'.`category_id` = ' . $db->Quote( $categoryId[0] );
				} else {
					$where[] = $tbl.'.`category_id` IN (' . $db->implode($categoryId) . ')';
				}
			}

			if ($postType) {
				$where[] = $tbl . '.`post_type` = ' . $db->Quote($postType);
			}

			$words = explode(' ', $search);
			$wheres = array();
			foreach ($words as $word) {

				$word = $db->Quote('%'.$db->getEscaped($word, true).'%', false);
				$wheres2 = array();

				if ($type == 'posts') {
					$wheres2[]	= 'a.title LIKE '.$word;
				}

				$wheres2[] = 'a.content LIKE '.$word;
				$wheres[] = implode(' OR ', $wheres2);
			}

			$whereString = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';

			$where[] = '(' . $whereString . ')';
			// $where[] = $whereString;


		} else if ($type == 'category') {

			if ($categoryId) {
				if (count($categoryId) == 1) {
					$where[] = 'a.`id` = ' . $db->Quote( $categoryId[0] );
				} else {
					$where[] = 'a.`id` IN (' . $db->implode($categoryId) . ')';
				}
			}

			$extra[] = 'a.`title` LIKE ' . $db->Quote('%'.$db->getEscaped( $search, true ).'%', false);
			$extra = '(' . implode( ') OR (', $extra ) . ')';
			$where[] = '(' . $extra . ')';
		}

		$catOptions = array('includeChilds' => $includeCatChilds);
		if ($type == 'category') {
			$catAccessSQL = ED::category()->genCategoryAccessSQL('a.id', $catOptions);
		} else {
			$catAccessSQL = ED::category()->genCategoryAccessSQL('a.category_id', $catOptions);
		}

		$where[] = $catAccessSQL;

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

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
	public function getData($options = array())
	{
		$db = $this->db;

		// settings
		$usePagination = isset($options['usePagination']) ? $options['usePagination'] : true;
		$sort = isset($options['sort']) ? $options['sort'] : 'latest';
		$limitstart = isset($options['limitstart']) ? $options['limitstart'] : null;
		$filter = isset($options['filter']) ? $options['filter'] : '';
		$category = isset($options['category']) ? $options['category'] : '';
		$limit = isset($options['limit']) ? $options['limit'] : null;
		$tags = isset($options['tags']) ? $options['tags'] : array();
		$postType = isset($options['post_type']) ? $options['post_type'] : '';

		// Check if this site support multilanguage
		$multilang = JLanguageMultilang::isEnabled();

		// If the category is empty, we add a check whether this search was made from the page with language code
		if (empty($category) && $multilang) {
			// Get current page language
			$lang = JFactory::getLanguage();

			// Now we need to retrieve categories that belongs to this language.
			// So that we can only display result based on the language
			$categoriesModel = ED::model('Categories');
			$categories = $categoriesModel->getCategories(array('language' => $lang->getTag(), 'bind_table' => false));

			$category = array();

			foreach ($categories as $categoryObj) {
				$category[] = $categoryObj->id;
			}
		}

		if (empty($this->_data)) {

			$query = $this->_buildQuery($sort, $filter, $category, false, $tags, $postType);

			// echo $query;

			if ($usePagination) {
				$limitstart = is_null($limitstart) ? $this->getState('limitstart') : $limitstart;
				$limit = is_null($limit) ? $this->getState('limit') : $limit;
			} else {
				$limitstart = 0;
				$limit = is_null($limit) ? $this->getState('limit') : $limit;
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
