<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelCategories extends EasyDiscussAdminModel
{
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easydiscuss.categories.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		$total = $this->getTotal();
		if ($limitstart > $total - $limit) {
			$limitstart = max(0, (int) (ceil($total / $limit) - 1) * $limit);
		}

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
		// Lets load the content if it doesn't already exist
		if (empty($this->_total)) {
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
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery($options = array())
	{
		$db = $this->db;

		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere($options);
		$orderby = $this->_buildQueryOrderBy($options);

		$query = 'SELECT a.*, '
				. '( SELECT COUNT(id) FROM ' . $db->nameQuote('#__discuss_category') . ' '
				. 'WHERE lft < a.lft AND rgt > a.rgt AND a.lft != ' . $db->Quote(0) . ' ) AS depth '
				. 'FROM ' . $db->nameQuote('#__discuss_category') . ' AS a '
				. $where
				. $orderby;

		return $query;
	}

	public function _buildQueryWhere($options = array())
	{
		$db = $this->db;
		$filter_state = $this->app->getUserStateFromRequest('com_easydiscuss.categories.filter_state', 'filter_state', '', 'word');
		$search = $this->app->getUserStateFromRequest('com_easydiscuss.categories.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(JString::strtolower($search)));

		if (isset($options['published']) && $options['published'] == true) {
			$filter_state = 'P';
		}

		$where = array();

		// $where[]		= $db->nameQuote( 'lft' ) . '!=' . $db->Quote( 0 );

		if ($filter_state) {

			if ($filter_state == 'P') {
				$where[] = $db->nameQuote('published') . '=' . $db->Quote('1');
			} else if ($filter_state == 'U') {
				$where[] = $db->nameQuote( 'published' ) . '=' . $db->Quote('0');
			}
		}

		if ($search) {
			$where[] = ' LOWER( title ) LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	public function _buildQueryOrderBy($options = array())
	{
		$filter_order = $this->app->getUserStateFromRequest('com_easydiscuss.categories.filter_order', 'filter_order', 'lft', 'cmd');
		$filter_order_Dir = $this->app->getUserStateFromRequest('com_easydiscuss.categories.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir. ', ordering';

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData($usePagination = true, $options = array())
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery($options);

			if ($usePagination) {
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			} else {
				$this->_data = $this->_getList($query);
			}
		}

		return $this->_data;
	}

	/**
	 * Method to publish or unpublish categories
	 *
	 * @access public
	 * @return array
	 */
	public function publish(&$categories = array(), $publish = 1)
	{
		$db = $this->db;

		if (count($categories) > 0) {

			$ids = implode( ',', $categories);

			$query	= 'UPDATE ' . $db->nameQuote('#__discuss_category') . ' '
					. 'SET ' . $db->nameQuote('published') . '=' . $db->Quote($publish) . ' '
					. 'WHERE ' . $db->nameQuote('id') . ' IN (' . $ids . ')';
					if ($publish = 0) {
						$query .= 'AND ' . $db->nameQuote('default') . ' = ' . $db->Quote(0) . '';
					}
			$db->setQuery($query);

			if (!$db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Returns the number of blog entries created within this category.
	 *
	 * @return int	$result	The total count of entries.
	 * @param boolean	$published	Whether to filter by published.
	 */
	public function getUsedCount($categoryId, $published = false, $parentOnly = false)
	{
		$db = $this->db;


		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_posts') . ' '
				. 'WHERE ' . $db->nameQuote('category_id') . '=' . $db->Quote($categoryId);

		if ($published) {
			$query	.= ' AND ' . $db->nameQuote('published') . '=' . $db->Quote(1);
		}

		if ($parentOnly) {
			$query	.= ' AND ' . $db->nameQuote('parent_id') . '=' . $db->Quote(0);
		}

		$db->setQuery($query);

		$result	= $db->loadResult();

		return $result;
	}

	public function getCategoryTreePosts($ids, $options = array())
	{
		if (!$ids) {
			return;
		}

		$my = $this->my;
		$db = $this->db;
		$config = $this->config;
		$date = ED::date();

		$sort = isset($options['sort']) ? $options['sort'] : 'latest';
		$limit = isset($options['limit']) ? $options['limit'] : null;
		$includeFeatured = isset($options['includeFeatured']) ? $options['includeFeatured'] : true;
		$featuredSticky = isset($options['featuredSticky']) ? $options['featuredSticky'] : false;
		$includeCluster = isset($options['includeCluster']) ? $options['includeCluster'] : null;

		$exclude = isset($options['exclude']) ? $options['exclude'] : array();

		$includeChild = isset($options['includeChild']) ? $options['includeChild'] : true;

		if (! is_array($ids)) {
			$ids = array($ids);
		}

		$joins = array();

		foreach($ids as $catId) {
			$query = "select $catId as `cat_parent_id`, a.`post_id`, a.`has_polls` as `polls_cnt`, a.`num_fav` as `totalFavourites`, a.`num_replies`, a.`num_attachments` as attachments_cnt,";
			$query .= " a.`num_likes` as `likeCnt`, a.`sum_totalvote` as `VotedCnt`,";
			$query .=  " a.`replied` as `lastupdate`, a.vote as `total_vote_cnt`,";
			$query .= ' DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created`) as `noofdays`, ';
			$query .= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', a.`created`) as `timediff`,';

			if ($my->id) {
				$query .= " (SELECT COUNT(1) FROM " . $db->nameQuote('#__discuss_votes') . " WHERE `post_id` = a.`post_id` AND `user_id` = " . $db->Quote($my->id) . ") AS `isVoted`,";
			} else {
				$query .= " 0 as `isVoted`,";
			}

			// $query .= " a.`last_user_id`, a.`last_poster_name`, a.`last_poster_email`,";
			$query .= " a.`last_user_id`, a.`last_poster_name`, a.`last_poster_email`,";
			$query .= " (select cc.`anonymous` from `#__discuss_posts` as cc where cc.`parent_id` = a.`post_id` order by cc.`id` desc limit 1) as `last_user_anonymous`,";

			$query .= " a.`post_status`, a.`post_type`";

			$query .= " from " . $db->nameQuote('#__discuss_thread') . " as a";
			$query .= " where a.`published` = " . $db->Quote('1');

			if (!$includeFeatured) {
				$query .= " and a.featured = 0";
			}

			if (!$includeCluster) {
				$query .= " and a.`cluster_id` = " . $db->Quote(0);
			}

			// category ACL:
			$catOptions = array('include' => $catId,
								'includeChilds' => $includeChild);

			$catAccessSQL = ED::category()->genCategoryAccessSQL('a.category_id', $catOptions);
			$query .= " and " . $catAccessSQL;

			$orderby = " order by";

			if ($featuredSticky) {
				$orderby .= " a.featured desc, ";
			}

			switch($sort) {
				case 'latest':
				default:
					$orderby .= " a.replied desc";
					break;
			}

			$query .= $orderby;
			$query .= " LIMIT $limit";

			$joins[] = $query;
		}

		$joinQuery = "(" . implode(") UNION ALL (", $joins) . ")";

		$query = "select b.*, th.*,";
		$query .= " pt.`suffix` AS post_type_suffix, pt.`title` AS `post_type_title`, e.`title` AS `category`";
		$query .= " FROM (" . $joinQuery . ") as th";
		$query .= " 	INNER JOIN " . $db->nameQuote('#__discuss_posts') . " as b on th.`post_id` = b.`id`";

		// Join with post types table
		$query 	.= "	LEFT JOIN " . $db->nameQuote('#__discuss_post_types') . " AS pt ON b.`post_type`= pt.`alias`";

		// Join with category table.
		$query	.= "	LEFT JOIN " . $db->nameQuote('#__discuss_category') . " AS e ON b.`category_id` = e.`id`";

// echo $query;exit;
		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;

	}

	public function getCategoryTree($ids = array(), $options = array())
	{
		$db = $this->db;
		$orderConfig = isset($options['ordering']) ? $options['ordering'] : $this->config->get('layout_ordering_category','latest');
		$sortConfig = isset($options['sorting']) ? $options['sorting'] : $this->config->get('layout_sort_category','asc');

		$queryExclude = '';
		$excludeCats = array();

		$sortParentChild = isset($options['sortParentChild']) ? $options['sortParentChild'] : true;
		$categoryIds = isset($options['categoryIds']) ? $options['categoryIds'] : array();

		$showAllSubCats = isset($options['showSubCategories']) ? $options['showSubCategories'] : $this->config->get('layout_show_all_subcategories');
		$showPostCount = isset($options['showPostCount']) ? $options['showPostCount'] : false;

		$limit = isset($options['limit']) ? (int) $options['limit'] : 0;

		$orderByAlias = "a";
		$query = "";
		$childCatAccessSQL = "";

		if ($showAllSubCats) {
			$orderByAlias = "b";

			$query = "select b.*, FLOOR(((b.rgt - b.lft) - 1) / 2) as `descendants`,";
			$query .= " (SELECT COUNT(id) FROM `#__discuss_category` WHERE lft < b.lft AND rgt > b.rgt) as `depth`";
			if ($showPostCount || $orderConfig == 'popular') {
				$query .= ', (select count(t.id) from `#__discuss_thread` as t where b.`id` = t.`category_id` and t.`published` = 1) as `post_count`';
			}
			$query .= " from `#__discuss_category` as a";
			$query .= " 	inner join `#__discuss_category` as b on a.`lft` <= b.`lft` and a.`rgt` >= b.`rgt`";

			// If show all subcategories, we need to check the subcategory permission as well
			$childCatAccessSQL = " and " . ED::category()->genCategoryAccessSQL('b.id', array());

		} else {
			$query = "select a.*, FLOOR(((a.rgt - a.lft) - 1) / 2) as `descendants`,";
			$query .= " (SELECT COUNT(id) FROM `#__discuss_category` WHERE lft < a.lft AND rgt > a.rgt) as `depth`";
			if ($showPostCount || $orderConfig == 'popular') {
				$query .= ', (select count(t.id) from `#__discuss_thread` as t where a.`id` = t.`category_id` and t.`published` = 1) as `post_count`';

			}
			$query .= " from `#__discuss_category` as a";
		}

		$query .= " where a.`published` = " . $db->Quote(DISCUSS_ID_PUBLISHED);

		if (!$categoryIds) {
			$query .= " and a.`parent_id` = 0";
		} else {
			$query .= " and a.`id` IN (" . implode(',', $categoryIds) . ")";
		}

		$catAccessSQL = " and " . ED::category()->genCategoryAccessSQL('a.id', array());

		$query .= $catAccessSQL;
		$query .= $childCatAccessSQL;

		switch($orderConfig) {
			case 'popular' :
				// this option only used in categories module for now.
				$orderBy = " ORDER BY `post_count` ";
				break;

			case 'alphabet' :
				$orderBy = " ORDER BY $orderByAlias.`title` ";
				break;
			case 'ordering' :
				$orderBy = " ORDER BY $orderByAlias.`lft` ";
				break;
			case 'latest' :
				$orderBy = " ORDER BY $orderByAlias.`created` ";
				break;
			default	:
				$orderBy = " ORDER BY $orderByAlias.`lft` ";
				break;
		}

		$query  .= $orderBy.$sortConfig;

		if ($limit) {
			$query .= ' LIMIT ' . $limit;
		}

		// echo $query;

		$db->setQuery($query);

		$rows = $db->loadObjectList();

		$total = count($rows);
		$categories = array();

		for ($i = 0; $i < $total; $i++) {

			$category = ED::category($rows[$i]);
			$category->depth = $rows[$i]->depth;
			$category->descendants = $rows[$i]->descendants;
			$category->postCount = false;

			if (isset($rows[$i]->post_count)) {
				$category->postCount = $rows[$i]->post_count;
			}

			$categories[] = $category;
		}

		if ($sortParentChild && ($orderConfig == 'alphabet' || $orderConfig == 'latest')) {
			$cats = array();
			$groups = array();

			foreach ($categories as $row) {
				$cats[$row->parent_id][] = $row;
			}

			$this->sortAlpha($groups, $cats, 0);

			$categories = $groups;
		}

		return $categories;
	}

	private function sortAlpha(&$groups, $cats, $parent_id)
	{
		if (!empty($cats[$parent_id])) {

			foreach($cats[$parent_id] as $row) {

				$groups[] = $row;

				$this->sortAlpha($groups, $cats, $row->id);
			}
		}
	}

	private function getItems($parent_id, $cats)
	{
		if (isset($cats[$parent_id])) {

			foreach($cats[$parent_id] as $row) {

				//echo $row;
				$this->endResults[] = $row;

				//$this->getItems( $row->parent_id, $cats );
			}

			// $id+=1;
			// if( $id < 6 )
			// {
			// 	$this->getItems( $id, $cats, '' );
			// }
		}
	}

	/**
	 * Retrieves a list of categories from the site.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int 	If there's a parent id provided, it would load sub categories.
	 */
	public function getCategories($options = array())
	{
		$db = $this->db;
		// Legacy
		if (!is_array($options)) {
			$parent_id = $options;
			$options = array('parent_id' => $parent_id);
		}

		$default = array(
				'acl_type' => DISCUSS_CATEGORY_ACL_ACTION_VIEW,
				'bind_table' => true,
				'parent_id' => 0
			);

		$options += $default;

		$query	= 'SELECT * FROM ' . $db->nameQuote('#__discuss_category');
		$query	.= ' WHERE ' . $db->nameQuote('parent_id') . '=' . $db->Quote($options['parent_id']);
		$query	.= ' AND ' . $db->nameQuote('published') . '=' . $db->Quote(1);

		if ($this->my->id == 0) {
			$query	.= ' AND ' . $db->nameQuote('private') . '!=' . $db->Quote('1');
		}

		//check categories acl here.
		$catIds	= ED::getAclCategories($options['acl_type'], $this->my->id, $options['parent_id']);

		if (count($catIds) > 0) {

			$strIds = '';

			foreach($catIds as $cat) {
				$strIds = (empty($strIds)) ? $cat->id : $strIds . ', ' . $cat->id;
			}

			$query .= ' AND ' . $db->nameQuote('id') . ' NOT IN (' . $strIds . ')';
		}

		$query	.= ' ORDER BY ' . $db->nameQuote('lft');

		$db->setQuery($query);

		$rows = $db->loadObjectList();

		if ($options['bind_table']) {

			$total = count($rows);
			$categories	= array();

			for ($i = 0; $i < $total; $i++) {

				$ignore['alias'] = true;

				$category = ED::table('Category');
				$category->bind( $rows[$i], $ignore );

				$categories[] = $category;
			}

			return $categories;
		}

		return $rows;
	}

	public function getChildCount($categoryId, $published = false)
	{
		$db = $this->db;

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_category') . ' '
				. 'WHERE ' . $db->nameQuote('parent_id') . '=' . $db->Quote($categoryId);

		if ($published) {
			$query	.= ' AND ' . $db->nameQuote('published') . '=' . $db->Quote(1);
		}

		$db->setQuery($query);

		$result	= $db->loadResult();

		return $result;
	}

	public function getChildIds($parentId = 0)
	{
		$categories = ED::Category()->getChildIds($parentId);

		return $categories;
	}

	private function getNestedIds($parentId, & $result)
	{
		$db = $this->db;
		$query	= 'SELECT * FROM ' . $db->nameQuote('#__discuss_category') . ' '
				. 'WHERE ' . $db->nameQuote( 'parent_id' ) .'=' . $db->Quote($parentId);

		$db->setQuery($query);
		$categories	= $db->loadObjectList();

		if ($categories) {

			foreach($categories as $category) {
				$result[] = $category->id;
				$this->getNestedIds($category->id, $result);
			}
		}
	}


	public function getPrivateCategories()
	{
		$db = $this->db;
		$query = 'select a.`id`';
		$query .= ' from `#__discuss_category` as a';
		$query .= ' where a.`private` = ' . $db->Quote('1');

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getChildCategories($parentId , $isPublishedOnly = false, $includePrivate = true, $exclusion = array())
	{
		$categories = ED::Category()->getChildCategories($parentId , $isPublishedOnly, $includePrivate, $exclusion);

		return $categories;
	}

	public function getParentCategories($contentId, $type = 'all', $isPublishedOnly = false, $showPrivateCat = true, $exclusion = array(), $aclType = DISCUSS_CATEGORY_ACL_ACTION_VIEW)
	{
		$db = $this->db;
		$sortConfig	= $this->config->get('layout_ordering_category','latest');

		$query = 'select a.`id`, a.`title`, a.`alias`, a.`private`,a.`default`,a.`container`';
		$query .= ' from `#__discuss_category` as a';
		$query .= ' where a.parent_id = ' . $db->Quote('0');

		if ($type == 'poster') {
			$query	.=  ' and a.created_by = ' . $db->Quote($contentId);
		} else if($type == 'category') {
			$query	.=  ' and a.`id` = ' . $db->Quote($contentId);
		}

		if ($isPublishedOnly) {
			$query	.=  ' and a.`published` = ' . $db->Quote('1');
		}

		if (!$this->app->isAdmin()) {
			// // we do not need to see the privacy when user accessing category via backend because only admin can access it.
			// // in a way, we do not restrict for admin.

			$catOptions = array('includeChilds', false);
			$catAccessSQL = ED::category()->genCategoryAccessSQL('a.id', $catOptions, $aclType);
			$query .= " and " . $catAccessSQL;

			// We don't need to check for language in backend because only frontend has the language filter
			$filterLanguage = JFactory::getApplication()->getLanguageFilter();
			if ($filterLanguage) {
				$query .= ' AND ' . ED::getLanguageQuery('a.language');
			}
		}

		// Exclude category list.
		if (!empty($exclusion)) {

			$excludeQuery = 'AND a.`id` NOT IN (';

			for ($i = 0 ; $i < count($exclusion); $i++) {

				$id = $exclusion[$i];

				$excludeQuery .= $db->Quote($id);

				if (next($exclusion) !== false) {
					$excludeQuery .= ',';
				}
			}

			$excludeQuery .= ')';

			$query .= $excludeQuery;
		}

		switch($sortConfig) {
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`lft` ';
				break;
			case 'latest' :
				$orderBy = ' ORDER BY a.`created` ';
				break;
			default	:
				$orderBy = ' ORDER BY a.`lft` ';
				break;
		}

		$sort = $this->config->get('layout_sort_category', 'asc');

		$query .= $orderBy.$sort;


		// echo $query;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}


	/**
	 * Retrieves a list of parent categories from the site.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	int 	If there's a parent id provided, it would load sub categories.
	 */
	public function getParentCategoriesOnly($contentId = 0, $options = array())
	{
		$db = $this->db;

		$idOnly = isset($options['id_only']) ? $options['id_only'] : false;
		$pagination = isset($options['pagination']) ? $options['pagination'] : true;
		$limitstart = isset($options['limitstart']) ? $options['limitstart'] : $this->input->get('limitstart', 0);
		$limit = isset($options['limit']) ? $options['limit'] : null;

		$query = 'select a.`id`, a.`title`, a.`alias`, a.`private`,a.`default`,a.`container`';

		if ($idOnly) {
			$query = 'select SQL_CALC_FOUND_ROWS a.`id`';
		}

		$query .= ' from `#__discuss_category` as a';
		$query .= ' where a.parent_id = ' . $db->Quote($contentId);
		$query .=  ' and a.`published` = ' . $db->Quote('1');

		$filterLanguage = JFactory::getApplication()->getLanguageFilter();
		if ($filterLanguage) {
			$query .= ' AND ' . ED::getLanguageQuery('a.language');
		}

		if (!$this->app->isAdmin()) {
			// // we do not need to see the privacy when user accessing category via backend because only admin can access it.
			// // in a way, we do not restrict for admin.

			$catOptions = array('includeChilds', false);
			$catAccessSQL = ED::category()->genCategoryAccessSQL('a.id', $catOptions);
			$query .= " and " . $catAccessSQL;
		}

		$sortConfig	= $this->config->get('layout_ordering_category','latest');
		switch($sortConfig) {
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`lft` ';
				break;
			case 'latest' :
				$orderBy = ' ORDER BY a.`created` ';
				break;
			default	:
				$orderBy = ' ORDER BY a.`lft` ';
				break;
		}

		$sort = $this->config->get('layout_sort_category', 'asc');

		$query .= $orderBy . $sort;

		// Pagination
		if ($limit != DISCUSS_NO_LIMIT) {
			if ($pagination) {
				$query .= " LIMIT $limitstart, $limit";

			} else {
				$query .= " LIMIT $limit";
			}
		}

		$db->setQuery($query);

		if ($idOnly) {
			$result = $db->loadColumn();
		} else {
			$result = $db->loadObjectList();
		}

		if ($limit != DISCUSS_NO_LIMIT && $pagination) {
			// now lets get the row_count() for pagination.
			$cntQuery = "select FOUND_ROWS()";
			$db->setQuery($cntQuery);
			$this->_total = $db->loadResult();
			$this->_pagination = ED::pagination($this->_total, $limitstart, $limit);
		}


		return $result;
	}




	public function getAllCategories()
	{
		$db = $this->db;
		$query = 'SELECT `id`, `title` FROM `#__discuss_category` ORDER BY `title`';
		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	public function getCategorySubscribers($categoryId)
	{
		$db = $this->db;

		$query  = "SELECT *, 'categorysubscription' as `type` FROM `#__discuss_category_subscription`";
		$query  .= " WHERE `category_id` = " . $db->Quote($categoryId);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function updateDefault($id)
	{
		$db = $this->db;

		$query = 'UPDATE ' . $db->nameQuote('#__discuss_category') . ' ' . 'SET ' . $db->nameQuote('default') . '=' . $db->Quote(0);

		$db->setQuery($query);
		$db->Query();

		$category = JTable::getInstance('Category', 'Discuss');
		$category->load($id);

		$category->default = true;

		$category->store();

		return true;
	}

	public function getTable($name = '', $prefix = 'Table', $options = array())
	{
		return ED::table('Category');
	}

	/**
	 * Return all the category which set as container
	 *
	 * @since 	4.0
	 * @access 	public
	 * @param
	 * @return
	 **/
	public function getCatContainer()
	{
		$db = $this->db;
		$query = 'SELECT * FROM ' . $db->nameQuote('#__discuss_category') . ' WHERE ' . $db->nameQuote('container') . ' = ' . $db->Quote(1);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function preloadCategories($cats)
	{
		$db = $this->db;

		$cats = array_unique($cats);

		$query = "select * from " . $db->nameQuote('#__discuss_category');
		$query .= " where id IN (" . implode(',', $cats) . ")";

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}


	/**
	 * search categories based on title and used in search filter.
	 *
	 * @since 	4.0
	 * @access 	public
	 * @param
	 * @return
	 **/
	public function suggestCategories($text)
	{
		$db = $this->db;

		$query = "select a.`id`, a.`title`";
		$query .= " from `#__discuss_category` as a";
		$query .= " where a.`published` = " . $db->Quote('1');
		$query .= " and a.`title` LIKE " . $db->Quote('%' . $text . '%');

		$catAccessSQL = ED::category()->genCategoryAccessSQL('a.id', array());
		$query .= " and " . $catAccessSQL;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}
}
