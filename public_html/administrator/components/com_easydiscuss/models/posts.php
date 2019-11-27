<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/model.php');

class EasyDiscussModelPosts extends EasyDiscussAdminModel
{
	public $isModule = false;
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;
	protected $_parent = null;
	protected $_isaccept = null;
	protected $_favs = true;

	static $_lastReply = array();

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easydiscuss.posts.limit', 'limit', ED::getListLimit(), 'int');
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
	public function getTotal($sort = 'latest', $filter = '', $category = '', $featuredOnly = 'all', $clusterId = '')
	{
		$sid = serialize($sort) . serialize($filter) . serialize($category) . serialize($featuredOnly);

		static $_cache = array();

		if (isset($_cache[$sid])) {
			$this->_total = $_cache[$sid];
		} else {
			$query = $this->_buildQueryTotal($sort, $filter, $category, $featuredOnly, false, '', $clusterId);

			$db = $this->db;
			$db->setQuery($query);

			$this->_total = $db->loadResult();
			$_cache[$sid] = $this->_total;
		}

		return $this->_total;
	}


	/**
	 * Method to get the total number of posts group by month
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotalPostsByMonth()
	{
		$db = ED::db();

		$query[] = "SELECT DATE_FORMAT(" . $db->quoteName('created').", '%Y') as 'year', DATE_FORMAT(" . $db->quoteName('created') . ", '%m') as 'month', COUNT(id) as 'total'";
		$query[] = "FROM " . $db->quoteName('#__discuss_thread');
		$query[] = "GROUP BY DATE_FORMAT(" . $db->quoteName('created') .", '%Y%m')";

		$query = implode(' ', $query);

		$db->setQuery($query);

		return $db->loadObjectList();

	}

	/**
	 * Removes all finder indexed items for replies
	 *
	 * @since	3.0
	 * @access	public
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


	/**
	 * Retrieve post title (parent post)
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	public function getPostTitle($postId)
	{
		$db = ED::db();

		$query = "select `title`";
		$query .= " FROM `#__discuss_posts`";
		$query .= " where `id` = " . $db->Quote($postId);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}


	/**
	 * Method to get a pagination object for the posts
	 *
	 * @sicne	4.0.16
	 * @access 	public
	 */
	public function getPagination($parent_id = 0, $sort = 'latest', $filter='', $category='', $featuredOnly = 'all', $userId = '')
	{
		$this->_parent = $parent_id;

		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {

			if (!$this->_total) {
				$this->_total = $this->getTotal($sort, $filter, $category, $featuredOnly, $userId);
			}

			$this->_pagination = ED::getPagination($this->_total, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Retrieve the total number of posts which are resolved.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getTotalResolved()
	{
		$db = ED::db();

		$query	= array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_thread') . ' as a';

		// exclude blocked users #788
		$query[] = " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";

		$query[] = 'WHERE a.' . $db->nameQuote('published') . '=' . $db->Quote(1);
		$query[] = 'AND a.' . $db->nameQuote('isresolve') . '=' . $db->Quote(DISCUSS_ID_PUBLISHED);
		$query[] = 'AND a.' . $db->nameQuote('private') . '=' . $db->Quote(0);

		// exclude blocked users #788
		$query[] = 'AND (uu.block = 0 OR uu.id IS NULL)';

		$query = implode(' ', $query);

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @since	4.0
	 * @access	private
	 */
	private function _buildQueryTotal($sort = 'latest', $filter = '', $category = '', $featuredOnly = 'all', $reply = false, $userId = '', $clusterId = '')
	{
		$my = $this->my;
		$db = $this->db;

		// Get the WHERE and ORDER BY clauses for the query
		if (empty($this->_parent)) {
			$parent_id = $this->input->get('parent_id', 0, 'int');
			$this->_parent = $parent_id;
		}

		$filteractive = (empty($filter)) ? $this->input->get('filter', 'allposts', 'string') : $filter;
		$where = $this->_buildQueryWhere($filter, $category, $featuredOnly, array(), $userId, $clusterId);

		$orderby = '';
		$queryExclude = '';
		$excludeCats = array();

		// We do not need to check for private categories for replies since replies are posted in that particular discussion.
		if (!$reply) {
			$excludeCats = ED::getPrivateCategories();
		}

		if (!empty($excludeCats)) {
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query = 'SELECT COUNT(a.`id`)';
		$query .= ' FROM `#__discuss_posts` AS a';

		$query .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";

		if ($filteractive == 'myreplies') {
			$query .= ' AND a.`parent_id` != 0 AND a.`published`=' . $db->Quote(1);
		}

		if ($filter == 'favourites') {
			$query .= '	LEFT JOIN `#__discuss_favourites` AS f ON f.`post_id` = a.`id`';
		}

		$query .= $where;

		if (!empty($this->_isaccept)) {
			$query .= ' AND a.`answered` = ' . $db->Quote('1');
		}

		if ($filteractive == 'unanswered') {
			$query .= ' AND a.`isresolve`=' . $db->Quote(0);
		}

		if ($filteractive == 'mine') {
			$query .= ' AND a.`user_id`=' . $db->Quote($my->id);
		}

		$query .= $queryExclude;

		$query .= ' and (uu.`block` = 0 OR uu.`id` is null)';

		return $query;
	}


	public function loadBatchPosts($ids)
	{
		$db = ED::db();
		$my = $this->my;
		$config = $this->config;
		$date = ED::date();

		$query = 'select b.*, a.`has_polls` as `polls_cnt`, a.`num_fav` as `totalFavourites`, a.`num_replies`, a.`num_attachments` as attachments_cnt,';
		$query .= ' a.`num_likes` as `likeCnt`, a.`sum_totalvote` as `VotedCnt`,';
		$query .=  " a.`replied` as `lastupdate`, a.vote as `total_vote_cnt`,";

		$query .= " a.`last_user_id`, a.`last_poster_name`, a.`last_poster_email`, ";

		if ($config->get('main_anonymous_posting')){
			$query .= " (select cc.anonymous from `#__discuss_posts` as cc where cc.`thread_id` = a.`id` and cc.created = a.replied limit 1) as `last_user_anonymous`,";
		} else {
			$query .= " 0 as `last_user_anonymous`,";
		}

		$query	.= ' DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created`) as `noofdays`, ';
		$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', a.`created`) as `timediff`,';

		if ($my->id) {
			$query .= " (SELECT COUNT(1) FROM " . $db->nameQuote('#__discuss_votes') . " WHERE `post_id` = a.`post_id` AND `user_id` = " . $db->Quote($my->id) . ") AS `isVoted`,";
		} else {
			$query .= " 0 as `isVoted`,";
		}

		$query	.= " a.`post_status`, a.`post_type`,";

		// $query .= " pt.`suffix` AS post_type_suffix, pt.`title` AS `post_type_title`,";

		$query	.= " e.`title` AS `category`";


		$query .= " from " . $db->nameQuote('#__discuss_thread') . " as a";
		$query .= " inner join " . $db->nameQuote('#__discuss_posts') . " as b on a.post_id = b.id";


		// Join with post types table
		// $query 	.= "	LEFT JOIN " . $db->nameQuote('#__discuss_post_types') . " AS pt ON a.`post_type`= pt.`alias`";

		// Join with category table.
		$query	.= "	LEFT JOIN " . $db->nameQuote('#__discuss_category') . " AS e ON a.`category_id` = e.`id`";


		// conditions start here.
		$where = array();
		$where[] = "a.`published` = " . $db->Quote('1');
		$where[] = "a.`post_id` IN (" . implode(',', $ids) . ")";

		$where = (count($where) ? " WHERE " . implode(' AND ', $where) : "");
		$query .= $where;

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if ($result) {

			$typequery = "SELECT * FROM ". $db->nameQuote('#__discuss_post_types');
			$db->setQuery($typequery);
			$posttypes = $db->loadObjectList("alias");

			$count = count($result);

			for ($i = 0; $i < $count; $i++) {
				$row =& $result[$i];

				if (isset($posttypes[$row->post_type])) {
					$row->post_type_suffix = $posttypes[$row->post_type]->suffix;
					$row->post_type_title = $posttypes[$row->post_type]->title;
				} else {
					$row->post_type_suffix = "";
					$row->post_type_title = "";
				}
			}
		}

		return $result;
	}


	public function getSimilarQuestion($text, $options = array())
	{
		$my = $this->my;
		$db = $this->db;
		$config = $this->config;
		$date = ED::date();

		// remove punctuation from the string.
		$text = preg_replace("/(?![.=$'â?])\p{P}/u", "", $text);

		if (empty($text)) {
			return false;
		}

		// $text   = 'how to configure facebook integration?';
		$itemLimit  = isset($options['limit']) ? $options['limit'] : $config->get('main_similartopic_limit', '5');
		$includePrivatePost = isset($options['includePrivatePost']) ? $options['includePrivatePost'] : false;

		// category ACL:
		$catOptions = array();
		$catAccessSQL = ED::category()->genCategoryAccessSQL('a.category_id', array());

		//language filter:
		$filterLanguage = JFactory::getApplication()->getLanguageFilter();

		// lets check if db has more than 2 records or not.
		$query = 'SELECT COUNT(1) FROM `#__discuss_thread` as a';
		$query .= ' WHERE a.`published` = ' . $db->Quote('1');

		$db->setQuery($query);
		$rCount = $db->loadResult();

		if ($rCount <= 2) {
			// full index search will fail if record has only two. So we do a normal like search.
			$phrase = 'or';
			$words = explode(' ', $text);

			$wheres = array();
			foreach ($words as $word) {

				$word = $db->Quote('%'.$db->getEscaped($word, true).'%', false);
				$wheres2 = array();
				$wheres2[] = 'a.title LIKE '.$word;
				$wheres2[] = 'a.content LIKE '.$word;

				$wheres[] = implode(' OR ', $wheres2);
			}

			$whereString = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';

			$query = "select b.*, a.`has_polls` as `polls_cnt`, a.`num_fav` as `totalFavourites`, a.`num_replies`, a.`num_attachments` as attachments_cnt,";
			$query .= " a.`num_likes` as `likeCnt`, a.`sum_totalvote` as `VotedCnt`,";
			$query .=  " a.`replied` as `lastupdate`, a.vote as `total_vote_cnt`,";
			$query .= ' DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created`) as `noofdays`, ';
			$query .= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', a.`created`) as `timediff`,';
			$query .= " 0 as `isVoted`";
			$query .= " from " . $db->nameQuote('#__discuss_thread') . " as a";
			$query .= " inner join " . $db->nameQuote('#__discuss_posts') . " as b on a.post_id = b.id";

			// exclude blocked user posts
			$query .= " left join `#__users` as uu on a.`user_id` = uu.`id`";

			// where criteria
			$where = array();
			$where[] = "a.`published` = " . $db->Quote('1');
			$where[] = $whereString;

			// category ACL:
			$where[] = $catAccessSQL;

			$where[] = "(uu.`block` = 0 OR uu.`id` IS NULL)";

			$where = (count($where) ? " WHERE " . implode(' AND ', $where) : "");

			$query .= $where;
			$query .= ' LIMIT ' . $itemLimit;

			$db->setQuery($query);
			$result = $db->loadObjectList();
			return $result;
		}

		// we know table has more than 3 records.
		// lets do a full index search.

		// lets get the tags match the keywords
		$tagkeywords = explode(' ', $text);
		for ($i = 0; $i < count($tagkeywords); $i++) {
			if (JString::strlen($tagkeywords[$i]) > 3) {
				$tagkeywords[$i] = $tagkeywords[$i] . '*';
			} else {
				$tagkeywords[$i] = $tagkeywords[$i];
			}
		}

		$tagkeywords = implode(' ', $tagkeywords);

		$query = 'select `id` FROM `#__discuss_tags`';
		$query .= ' WHERE MATCH(`title`) AGAINST (' . $db->Quote($tagkeywords) . ' IN BOOLEAN MODE)';

		$db->setQuery($query);

		$tagResults = $db->loadResultArray();


		// determine which fulltext mode to use.
		$fulltextType = ' WITH QUERY EXPANSION';
		if ($config->get('system_query') == 'count') {
			$fulltextType = ' IN BOOLEAN MODE';
		}

		// now try to get the main topic
		$query = "select b.*, a.`has_polls` as `polls_cnt`, a.`num_fav` as `totalFavourites`, a.`num_replies`, a.`num_attachments` as attachments_cnt,";
		$query	.= " a.`num_likes` as `likeCnt`, a.`sum_totalvote` as `VotedCnt`,";
		$query	.=  " a.`replied` as `lastupdate`, a.vote as `total_vote_cnt`,";
		$query	.= ' DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created`) as `noofdays`, ';
		$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', a.`created`) as `timediff`,';
		$query .= " 0 as `isVoted`";
		$query .= " from " . $db->nameQuote('#__discuss_thread') . " as a";
		$query .= " inner join " . $db->nameQuote('#__discuss_posts') . " as b on a.post_id = b.id";

		//exclude blocked users posts.
		$query .= " left join `#__users` as uu on a.`user_id` = uu.`id`";


		// where criteria
		$where = array();
		$where[] = 'MATCH(a.`title`,a.`content`) AGAINST (' . $db->Quote($text) . $fulltextType . ')';
		$where[] = "a.`published` = " . $db->Quote('1');

		// category ACL:
		$where[] = $catAccessSQL;

		$where[] = "(uu.`block` = 0 OR uu.`id` IS NULL)";


		$where = (count($where) ? " WHERE " . implode(' AND ', $where) : "");

		$query .= $where;
		$query .= ' LIMIT ' . $itemLimit;

		$tagQuery = '';
		if (count($tagResults) > 0) {

			$tagQuery = "select b.*, a.`has_polls` as `polls_cnt`, a.`num_fav` as `totalFavourites`, a.`num_replies`, a.`num_attachments` as attachments_cnt,";
			$tagQuery	.= " a.`num_likes` as `likeCnt`, a.`sum_totalvote` as `VotedCnt`,";
			$tagQuery	.=  " a.`replied` as `lastupdate`, a.vote as `total_vote_cnt`,";
			$tagQuery	.= ' DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created`) as `noofdays`, ';
			$tagQuery	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', a.`created`) as `timediff`,';
			$tagQuery .= " 0 as `isVoted`";
			$tagQuery .= " from " . $db->nameQuote('#__discuss_thread') . " as a";
			$tagQuery .= " inner join " . $db->nameQuote('#__discuss_posts') . " as b on a.`post_id` = b.`id`";
			$tagQuery .= " INNER JOIN `#__discuss_posts_tags` as pt ON a.`post_id` = pt.`post_id`";

			//exclude blocked users posts.
			$tagQuery .= " left join `#__users` as uu on a.`user_id` = uu.`id`";

			// where criteria
			$where = array();
			$where[] = 'pt.`tag_id` IN (' . implode(',', $tagResults) . ')';
			$where[] = "a.`published` = " . $db->Quote('1');

			// category ACL:
			$where[] = $catAccessSQL;

			$where[] = "(uu.`block` = 0 OR uu.`id` IS NULL)";

			$where = (count($where) ? " WHERE " . implode(' AND ', $where) : "");

			$tagQuery .= $where;
			$tagQuery .= ' LIMIT ' . $itemLimit;

			$query = 'SELECT * FROM (' . $query . ' UNION ' . $tagQuery . ') AS x LIMIT ' . $itemLimit;
		}

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	public function getPostNavigation($post, $navigateType)
	{
		$db = $this->db;
		$my = $this->my;
		$config = $this->config;

		// get thread obj
		$thread = ED::table('thread');
		$thread->load($post->thread_id);

		$keys = array('prev','next');
		$nav = new stdClass();

		$sort = 'latest';

		$mainQuery = "select a.`post_id` as `id`, a.`alias`, a.`title`";
		$mainQuery .= " from `#__discuss_thread` as a";
		$mainQuery .= " inner join `#__discuss_category` as e on a.`category_id` = e.`id`";

		// exclude blocked user posts
		$mainQuery .= " left join `#__users` as uu on a.`user_id` = uu.`id`";

		$mainQuery .= " where a.`published` = " . $db->Quote('1');

		// category ACL:
		$category = '';

		if ($navigateType == 'category') {
			$category = $post->category_id;
		}

		$catOptions = array();
		$catOptions['idOnly'] = true;

		if ($category) {
			// do not include the posts from sub cats as we only want the post from this specific cat.
			$catOptions['includeChilds'] = false;
		} else {
			$catOptions['includeChilds'] = true;
		}

		// $catAccessSQL = ED::category()->genCategoryAccessSQL('a.category_id', $catOptions);
		// $where[] = $catAccessSQL;
		$catModel = ED::model('Categories');
		$catIds = $catModel->getCategoriesTree($category, $catOptions);

		// if there is no categories return, means this user has no permission to view all the categories.
		// if that is the case, just return empty array.
		if (! $catIds) {
			return array();
		}

		$mainQuery .= " and a.`category_id` IN (" . implode(',', $catIds) . ")";

		// We only get unfeatured post.
		// Because listing in index view is not including featured post
		$mainQuery .= " and a.`featured` = " . $db->Quote(0);

		$filterLanguage = JFactory::getApplication()->getLanguageFilter();
		if ($filterLanguage) {
			$mainQuery .= " and " . ED::getLanguageQuery('e.language');
		}

		// exclude blocked users
		$mainQuery .= " and (uu.`block` = 0 OR uu.`id` IS NULL)";

		foreach ($keys as $key) {
			$query = '';

			if ($key == 'prev') {
				$query .= " AND a.`replied` < " . $db->Quote($thread->replied);
				$query .= " ORDER BY a.`replied` DESC";
			}

			if ($key == 'next') {
				$query .= " AND a.`replied` > " . $db->Quote($thread->replied);
				$query .= " ORDER BY a.`replied` ASC";
			}

			$sql = $mainQuery . $query;
			$sql .= " limit 1";

			$db->setQuery($sql);

			$result = $db->loadObject();
			$nav->$key = $result;
		}

		return $nav;
	}

	/**
	 * Retrieve a list of discussions
	 *
	 * @since	4.0.16
	 * @access	public
	 */
	public function getDiscussions($options = array())
	{
		$my = $this->my;
		$db = $this->db;
		$config = $this->config;
		$date = ED::date();

		$sort = isset($options['sort']) ? $options['sort'] : 'latest';
		$postStatus = isset($options['postStatus']) ? $options['postStatus'] : 0;
		$pagination = isset($options['pagination']) ? $options['pagination'] : true;
		$limitstart = isset($options['limitstart']) ? $options['limitstart'] : null;
		$filter = isset($options['filter']) ? $options['filter'] : '';
		$category = isset($options['category']) ? $options['category' ] : '';
		$limit = isset($options['limit']) ? $options['limit' ] : null;
		$featured = isset($options['featured']) ? $options['featured'] : 'all';
		$exclude = isset($options['exclude']) ? $options['exclude'] : array();
		$reference = isset($options['reference']) ? $options['reference'] : null;
		$referenceId = isset($options['reference_id']) ? $options['reference_id'] : null;
		$userId = isset($options['userId']) ? $options['userId'] : null;
		$private = isset($options['private']) ? $options['private'] : null;
		$includeChilds = isset($options['includeChilds']) ? $options['includeChilds'] : true;
		$clusterId = isset($options['cluster_id']) ? $options['cluster_id'] : null;
		$includeCluster = isset($options['includeCluster']) ? $options['includeCluster'] : null;
		$includeAnonymous = isset($options['includeAnonymous']) ? $options['includeAnonymous'] : true;
		$respectSearch = isset($options['respectSearch']) ? $options['respectSearch'] : true;

		$search = $respectSearch ? $db->getEscaped($this->input->get('query', '', 'string')) : '';
		$filteractive = (empty($filter)) ? $this->input->get('filter', 'allpost', 'string') : $filter;

		// If this is from Module, we should ignore the filteractive
		$isModule = isset($options['module'])? $options['module'] : false;

		if ($isModule && empty($filter)) {
			$filteractive = '';
		}

		// unsure what is this. need to find out what and where this come from.
		$user_id = $this->input->get('user_id', 0, 'int');

		$useFoundRows = $config->get('system_query', 'default') == 'default' ? true : false;
		$countSql = array();


		$query = "select SQL_CALC_FOUND_ROWS";
		if (! $useFoundRows) {
			$query = "select";
		}

		$query .= " b.*, a.`has_polls` as `polls_cnt`, a.`num_fav` as `totalFavourites`, a.`num_replies`, a.`num_attachments` as attachments_cnt,";
		$query .= " a.`num_likes` as `likeCnt`, a.`sum_totalvote` as `VotedCnt`,";
		$query .=  " a.`replied` as `lastupdate`, a.vote as `total_vote_cnt`,";

		$query .= " a.`last_user_id`, a.`last_poster_name`, a.`last_poster_email`,";

		if ($config->get('main_anonymous_posting')){
			$query .= " (select cc.anonymous from `#__discuss_posts` as cc where cc.`thread_id` = a.`id` and cc.created = a.replied limit 1) as `last_user_anonymous`,";
		} else {
			$query .= " 0 as `last_user_anonymous`,";
		}

		$query	.= ' DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created`) as `noofdays`, ';
		$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', a.`created`) as `timediff`,';

		if ($my->id) {
			$query .= " (SELECT COUNT(1) FROM " . $db->nameQuote('#__discuss_votes') . " WHERE `post_id` = a.`post_id` AND `user_id` = " . $db->Quote($my->id) . ") AS `isVoted`,";
		} else {
			$query .= " 0 as `isVoted`,";
		}

		$query	.= " a.`post_status`, a.`post_type`,";

		// The post type retrieving will be done in separate query to speed up the performance.
		// $query .= " pt.`suffix` AS post_type_suffix, pt.`title` AS `post_type_title`,";
		//
		$query	.= " e.`title` AS `category`";

		$query .= " from " . $db->nameQuote('#__discuss_thread') . " as a";
		$query .= " inner join " . $db->nameQuote('#__discuss_posts') . " as b on a.post_id = b.id";

		// exlude block users. #788
		$query .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";


		// Join with post types table
		// The post type retrieving will be done in separate query to speed up the performance.
		// $query 	.= "	LEFT JOIN " . $db->nameQuote('#__discuss_post_types') . " AS pt ON a.`post_type`= pt.`alias`";

		// Join with category table.
		$query	.= "	LEFT JOIN " . $db->nameQuote('#__discuss_category') . " AS e ON a.`category_id` = e.`id`";

		if ($filter == 'favourites' || $filteractive == 'favourites') {
			$query	.= "	LEFT JOIN " . $db->nameQuote('#__discuss_favourites') . " AS f ON f.`post_id` = a.`post_id`";
		}

		if ($filter == 'assigned') {
			$query	.= " INNER JOIN " . $db->nameQuote('#__discuss_assignment_map') . " AS am ON am.`post_id` = a.post_id";
			$query 	.= " AND am.`assignee_id` = " . $db->Quote($my->id);
		}

		// 3rd party integrations
		if (!is_null($reference) && !is_null($referenceId)) {
			$query 	.= " INNER JOIN " . $db->nameQuote('#__discuss_posts_references') . " AS ref";
			$query	.= " 	ON a." . $db->nameQuote('post_id') . " = ref." . $db->nameQuote('post_id');
			$query	.= " 	AND ref." . $db->nameQuote('extension') . " = " . $db->Quote($reference);
			$query	.= " 	AND ref." . $db->nameQuote('reference_id') . " = " . $db->Quote($referenceId);
		}

		// ------------------------------
		// Build sql for pagination count
		$countSQL = "select count(1)";
		$countSQL .= " from " . $db->nameQuote('#__discuss_thread') . " as a";

		// exlude block users.
		$countSQL .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";

		if (!$includeAnonymous) {
			// to optimize the page load speed when using 'select count' query behavior
			$countSQL .= " inner join " . $db->nameQuote('#__discuss_posts') . " as b on a.post_id = b.id";
		}
		$countSQL .= "	LEFT JOIN " . $db->nameQuote('#__discuss_category') . " AS e ON a.`category_id` = e.`id`";

		if ($filter == 'favourites' || $filteractive == 'favourites') {
			$countSQL	.= "	LEFT JOIN " . $db->nameQuote('#__discuss_favourites') . " AS f ON f.`post_id` = a.`post_id`";
		}

		if ($filter == 'assigned') {
			$countSQL .= " INNER JOIN " . $db->nameQuote('#__discuss_assignment_map') . " AS am ON am.`post_id` = a.post_id";
			$countSQL .= " AND am.`assignee_id` = " . $db->Quote($my->id);
		}

		if (!is_null($reference) && !is_null($referenceId)) {
			$countSQL 	.= " INNER JOIN " . $db->nameQuote('#__discuss_posts_references') . " AS ref";
			$countSQL	.= " 	ON a." . $db->nameQuote('post_id') . " = ref." . $db->nameQuote('post_id');
			$countSQL	.= " 	AND ref." . $db->nameQuote('extension') . " = " . $db->Quote($reference);
			$countSQL	.= " 	AND ref." . $db->nameQuote('reference_id') . " = " . $db->Quote($referenceId);
		}


		// conditions start here.
		$where = array();
		$where[] = "a.`published` = " . $db->Quote('1');

		if (!ED::isSiteAdmin() && !ED::isModerator() && !$private && $filter != 'mine') {
			$privateQuery = "a.`private` = " . $db->Quote(0);

			// Include current user private post.
			if (ED::user()->id) {
				$privateQuery = "(a.`private` = " . $db->Quote(0) . " OR (a.`private` = " . $db->Quote(1) . " AND a.`user_id` = " . $db->Quote(ED::user()->id) . "))";
			}

			$where[] = $privateQuery;
		}

		if ($clusterId) {
			$where[] = "a.`cluster_id` = " . $clusterId;
			$includeCluster = true;
		}

		if (!$includeCluster) {
			$where[] = "a.`cluster_id` = " . $db->Quote(0);
		}

		if ($user_id) {
			$where[] = "a.`user_id` = " . $db->Quote((int) $user_id);
		}

		if (!empty($exclude)) {
			$excludePost = "a.`post_id` NOT IN (" . implode(',', $exclude) . ")";
			$where[] = $excludePost;
		}

		if ($filteractive == 'unread') {
			$viewer = ED::user();
			$readPosts = $viewer->posts_read;

			if ($readPosts) {
				$readPosts = unserialize($readPosts);

				if (count($readPosts) > 1) {
					$extraSQL = implode(',', $readPosts);

					$where[] = " a.`post_id` NOT IN (" . $extraSQL . ")";
				} else {
					$where[] = " a.`post_id` != " . $db->Quote($readPosts[0]);
				}
			}

			$where[] = "a.`legacy` = 0";
		}

		if ($filteractive == 'unanswered') {
			// Should not fetch posts which are resolved
			$where[] = "a.`isresolve` = " . $db->Quote(0);
			$where[] = "(a.`last_user_id` = 0 and a.`last_poster_email` = '')";
		}

		if ($filteractive == 'favourites') {
			if (empty($userId)) {
				$id = $my->id;
			} else {
				$id = $userId;
			}

			$where[] = "f.`created_by` = " . $db->quote($id);
		}

		if ($filteractive == 'unresolved') {
			$where[] = "a.`isresolve`= " . $db->Quote('0');
		}

		// @since 3.1 resolved filter
		if ($filteractive == 'resolved') {
			$where[] = "a.`isresolve`= " . $db->Quote('1');
		}

		if ($filter == 'answer') {
			$where[] = $db->nameQuote('a.answered') . ' = ' . $db->Quote(1);
		}

		if ($filter == 'mine') {
			$where[] = "a.`user_id` = " . $db->Quote($my->id);
		}

		if ($filteractive == 'unanswered') {
			$where[] = "a.`answered` = " . $db->Quote('0');
		}

		if ($filteractive == 'locked') {
			$where[] = "a.`islock` = " . $db->Quote('1');
		}

		if ($search) {
			$where[] = "LOWER(a.`title`) LIKE " . $db->Quote('%' . $search . '%');
		}

		$statuses = array('onhold' => DISCUSS_POST_STATUS_ON_HOLD, 
						'accepted' => DISCUSS_POST_STATUS_ACCEPTED, 
						'workingon' => DISCUSS_POST_STATUS_WORKING_ON, 
						'rejected' => DISCUSS_POST_STATUS_REJECT);

		if ($postStatus) {
			$where[] = "a.`post_status` = " . $db->Quote($statuses[$postStatus]);
		}

		// category ACL:
		$catOptions = array();
		$catOptions['idOnly'] = true;

		if ($category) {
			// $catOptions['include'] = $category;
			$catOptions['includeChilds'] = $includeChilds;
		} else {
			$catOptions['includeChilds'] = true;
		}

		// $catAccessSQL = ED::category()->genCategoryAccessSQL('a.category_id', $catOptions);
		// $where[] = $catAccessSQL;
		$catModel = ED::model('Categories');
		$catIds = $catModel->getCategoriesTree($category, $catOptions);

		// if there is no categories return, means this user has no permission to view all the categories.
		// if that is the case, just return empty array.
		if (! $catIds) {
			return array();
		}

		$where[] = "a.category_id IN (" . implode(',', $catIds) . ")";

		if ($filteractive == 'featured' || $featured === true) {
			$where[] = "a.`featured` = " . $db->Quote('1');
		}
		else if ($featured === false && $filter != 'resolved') {
			$where[] = "a.`featured` = " . $db->Quote('0');
		}

		if ($filteractive == 'myposts') {
			$where[] = "a.`user_id`= " . $db->Quote($my->id);
		}

		if ($filteractive == 'userposts' && !empty($userId)) {
			$where[] = "a.`user_id`= " . $db->Quote($userId);
		}

		if ($filteractive == 'questions' && !empty($userId)) {
			$where[] = "a.`user_id`= " . $db->Quote($userId);
		}

		$filterLanguage = JFactory::getApplication()->getLanguageFilter();
		if ($filterLanguage) {
			$where[] = ED::getLanguageQuery('e.language');
		}

		// exlude block users. #788
		$where[] = "(uu.`block` = 0 or uu.`id` is null)";

		$orderby = "";
		$featuredOrdering = '';

		$featuredSticky = isset($options['featuredSticky']) ? $options['featuredSticky'] : false;

		if ($featuredSticky) {
			$featuredOrdering = "a.featured DESC, ";
		}

		if ($featured && $config->get('layout_featuredpost_style') != '0') {
			switch ($config->get('layout_featuredpost_sort', 'date_latest')) {
				case 'date_oldest':
					$orderby = " ORDER BY " . $featuredOrdering . "a.`replied` ASC"; //used in getdata only
					break;
				case 'order_asc':
					$orderby = " ORDER BY " . $featuredOrdering . "a.`ordering` ASC"; //used in getreplies only
					break;
				case 'order_desc':
					$orderby = " ORDER BY " . $featuredOrdering . "a.`ordering` DESC"; //used in getdate and getreplies
					break;
				case 'date_latest':
				default:
					$orderby = " ORDER BY " . $featuredOrdering . "a.`replied` DESC"; //used in getsticky and get created date
					break;
			}
		} else {
			switch ($sort) {
				case 'title':
					$orderby = " ORDER BY " . $featuredOrdering . "a.`title` ASC"; //used in getdata only
					break;
				case 'popular':
					$orderby = " ORDER BY " . $featuredOrdering . "`num_replies` DESC, a.`created` DESC"; //used in getdata only
					break;
				case 'hits':
					$orderby = " ORDER BY " . $featuredOrdering . " a.`hits` DESC"; //used in getdata only
					break;
				case 'voted':
					$orderby = " ORDER BY " . $featuredOrdering . "a.`sum_totalvote` DESC"; //used in getreplies only
					break;
				case 'likes':
					$orderby = " ORDER BY " . $featuredOrdering . "a.`num_likes` DESC"; //used in getdate and getreplies
					break;
				case 'activepost':
					$orderby = " ORDER BY " . $featuredOrdering . "a.`replied` DESC"; //used in getsticky and getlastreply
					break;
				case 'featured':
					$orderby = " ORDER BY a.`featured` DESC, a.`created` DESC"; //used in getsticky and getlastreply
					break;
				case 'oldest':
					$orderby = " ORDER BY " . $featuredOrdering . "a.`created` ASC"; //used in discussion replies
					break;
				case 'replylatest':
					$orderby = " ORDER BY " . $featuredOrdering . "a.`created` DESC"; //used in discussion replies
					break;
				case 'latest':
				default:
					$orderby = " ORDER BY " . $featuredOrdering . "a.`replied` DESC"; //used in getsticky and get created date
					break;
			}
		}

		if (!$includeAnonymous) {
			$where[] = "b.`anonymous` != " . $db->Quote(1);
		}


		$where = (count($where) ? " WHERE " . implode(' AND ', $where) : "");
		$query .= $where;
		$query .= $orderby;

		$limitstart = is_null($limitstart) ? $this->getState('limitstart') : $limitstart;
		$limitstart = (int) $limitstart;

		$limit = is_null($limit) ? $this->getState('limit') : $limit;

		if ($limit != DISCUSS_NO_LIMIT) {
			if ($pagination) {
				$query .= " LIMIT $limitstart, $limit";

			} else {
				$query .= " LIMIT $limit";
			}
		}

		// echo $query;
		// echo '<br><br>';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if ($limit != DISCUSS_NO_LIMIT && $pagination) {
			// now lets get the row_count() for pagination.
			$cntQuery = "select FOUND_ROWS()";

			if (! $useFoundRows) {
				$cntQuery = $countSQL . $where;
			}

			$db->setQuery($cntQuery);
			$this->_total = $db->loadResult();
			$this->_pagination = ED::pagination($this->_total, $limitstart, $limit);
		}

		if ($result) {

			$typequery = "SELECT * FROM ". $db->nameQuote('#__discuss_post_types');
			$db->setQuery($typequery);
			$posttypes = $db->loadObjectList("alias");

			$count = count($result);

			for ($i = 0; $i < $count; $i++) {
				$row =& $result[$i];

				if (isset($posttypes[$row->post_type])) {
					$row->post_type_suffix = $posttypes[$row->post_type]->suffix;
					$row->post_type_title = $posttypes[$row->post_type]->title;
				} else {
					$row->post_type_suffix = "";
					$row->post_type_title = "";
				}
			}
		}

		$this->_getDateDiffs($result);

		return $result;
	}


	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	private function _buildQuery($sort = 'latest', $filter = '', $category = '', $featuredOnly = 'all', $reply = false, $exclude = array(), $reference = null, $referenceId = null, $userId = null, $private = null)
	{
		$my = JFactory::getUser();
		$config = DiscussHelper::getConfig();

		// Get the WHERE and ORDER BY clauses for the query
		if (empty($this->_parent)) {
			$parent_id = JRequest::getInt('parent_id', 0);
			$this->_parent = $parent_id;
		}

		if (isset($this->isModule) && $this->isModule == true) {
			$this->_parent = 0;
		}

		$filteractive = (empty($filter)) ? JRequest::getString('filter', 'allposts') : $filter;
		$where = $this->_buildQueryWhere($filter , $category, $featuredOnly , $exclude, $userId);

		$db = ED::db();

		$orderby		= '';
		$queryExclude	= '';
		$excludeCats	= array();

		// We do not want to include anything from cluster here.
		$includeCluster = false;

		$useFoundRows = $config->get('system_query', 'default') == 'default' ? true : false;

		$date = ED::date();

		// We do not need to check for private categories for replies since replies are posted in that particular discussion.
		// if(!$reply)
		// {
		// 	$excludeCats = DiscussHelper::getPrivateCategories();
		// }

		if (! empty($excludeCats)) {
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query = 'SELECT SQL_CALC_FOUND_ROWS';
		if (!$useFoundRows) {
			$query = 'SELECT';
		}

		// Include polls
		$query .= ' (SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_polls') . ' WHERE ' . $db->nameQuote('post_id') . ' = a.' . $db->nameQuote('id') . ') AS `polls_cnt`,';

		// Include favourites
		$query .= ' (SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_favourites') . ' WHERE ' . $db->nameQuote('post_id') . ' = a.' . $db->nameQuote('id') . ') AS `totalFavourites`,';



		// Calculate number replies
		$query .= '(SELECT COUNT(1) FROM `#__discuss_posts` WHERE `parent_id` = a.`id` AND `published`="1") AS `num_replies`,';

		// Include attachments
		if (!$reply) {
			$query .= ' (SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_attachments') . ' WHERE ' . $db->nameQuote('uid') . ' = a.' . $db->nameQuote('id')
					. ' AND ' . $db->nameQuote('type') . '=' . $db->Quote(DISCUSS_QUESTION_TYPE)
					. ' AND ' . $db->nameQuote('published') . '=' . $db->Quote(1) . ') AS `attachments_cnt`,';
		}

		//sorting criteria
		if ($sort == 'likes') {
			$query .= ' a.`num_likes` as `likeCnt`,';
		}

		if ($sort == 'voted') {
			$query .= ' a.`sum_totalvote` as `VotedCnt`,';
		}

		if ($my->id != 0) {
			$query .= ' (SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_votes') . ' WHERE ' . $db->nameQuote('post_id') . ' = a.' . $db->nameQuote('id') . ' AND `user_id` = ' . $db->Quote($my->id) . ') AS `isVoted`,';
		} else {
			$query .= ' ' . $db->Quote('0') . ' as `isVoted`,';
		}

		$query .= ' pt.`suffix` AS post_type_suffix, pt.`title` AS post_type_title , a.*, ';


		$query .= ' e.`title` AS `category`, ';
		$query .= ' IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) as `lastupdate`';

		$query .= ', (select count(1) from `#__discuss_votes` where post_id = a.id) as `total_vote_cnt`';

		$query .= ' FROM `#__discuss_posts` AS a';

		// Join with post types table
		$query .= '	LEFT JOIN ' . $db->nameQuote('#__discuss_post_types') . ' AS pt ON a.`post_type`= pt.`alias`';

		// Join with category table.
		$query .= '	LEFT JOIN ' . $db->nameQuote('#__discuss_category') . ' AS e ON a.`category_id`=e.`id`';

		if ($filter == 'favourites') {
			$query .= '	LEFT JOIN `#__discuss_favourites` AS f ON f.`post_id` = a.`id`';
		}

		if ($filter == 'assigned') {
			$query .= ' INNER JOIN ' . $db->nameQuote('#__discuss_assignment_map') . ' AS am ON am.`post_id` = a.id';
			$query .= ' AND am.`assignee_id`=' . $db->Quote(JFactory::getUser()->id);
		}

		// 3rd party integrations
		if (!is_null($reference) && !is_null($referenceId)) {
			$query .= ' INNER JOIN ' . $db->nameQuote('#__discuss_posts_references') . ' AS ref';
			$query .= ' ON a.' . $db->nameQuote('id') . '= ref.' . $db->nameQuote('post_id');
			$query .= ' AND ref.' . $db->nameQuote('extension') . '=' . $db->Quote($reference);
			$query .= ' AND ref.' . $db->nameQuote('reference_id') . '=' . $db->Quote($referenceId);
		}

		if ($filter == 'answer') {
			$where .= ' AND a.' . $db->nameQuote('answered') . '=' . $db->Quote(1);
		}

		if ($filter == 'mine') {
			$where .= ' AND a.`user_id`=' . $db->Quote((int)JFactory::getUser()->id);
		}

		if ($filteractive == 'unanswered')
		{
			$where .= ' AND a.`answered`=' . $db->Quote(0);
		}

		if (!ED::isSiteAdmin() && !ED::isModerator() && !$private && $filter != 'mine') {
			$where .= ' AND a.`private`=' . $db->Quote(0);
		}

		if (!$includeCluster) {
			$where .= ' AND a.`cluster_id` = ' . $db->Quote(0);
		}

		// category ACL:
		$catOptions = array();
		$catOptions['idOnly'] = true;
		$catOptions['includeChilds'] = true;

		$catModel = ED::model('Categories');
		$catIds = $catModel->getCategoriesTree(0, $catOptions);

		// if there is no categories return, means this user has no permission to view all the categories.
		// if that is the case, just return empty array.
		if (! $catIds) {
			$where .= " and `category_id` = 0";
		} else {
			$where .= " and `category_id` IN (" . implode(',', $catIds) . ")";
		}


		$query .= $where;
		$query .= $queryExclude;

		if ($featuredOnly && $config->get('layout_featuredpost_style') != '0' && empty($this->_parent)) {
			switch($config->get('layout_featuredpost_sort', 'date_latest')) {
				case 'date_oldest':
					$orderby = ' ORDER BY a.`replied` ASC'; //used in getdata only
					break;
				case 'order_asc':
					$orderby = ' ORDER BY a.`ordering` ASC'; //used in getreplies only
					break;
				case 'order_desc':
					$orderby = ' ORDER BY a.`ordering` DESC'; //used in getdate and getreplies
					break;
				case 'date_latest':
				default:
					$orderby = ' ORDER BY a.`replied` DESC'; //used in getsticky and get created date
					break;
			}
		} else {

			switch ($sort) {
				case 'popular':
					$orderby = ' ORDER BY `num_replies` DESC, a.`created` DESC'; //used in getdata only
					break;
				case 'hits':
					$orderby = ' ORDER BY a.`hits` DESC'; //used in getdata only
					break;
				case 'voted':
					$orderby = ' ORDER BY a.`sum_totalvote` DESC'; //used in getreplies only
					break;
				case 'likes':
					$orderby = ' ORDER BY a.`num_likes` DESC'; //used in getdate and getreplies
					break;
				case 'activepost':
					$orderby = ' ORDER BY a.`replied` DESC'; //used in getsticky and getlastreply
					break;
				case 'featured':
					$orderby = ' ORDER BY a.`featured` DESC, a.`created` DESC'; //used in getsticky and getlastreply
					break;
				case 'oldest':
					$orderby = " ORDER BY a.`created` ASC"; //used in discussion replies
					break;
				case 'replylatest':
					$orderby = " ORDER BY a.`created` DESC"; //used in discussion replies
					break;
				case 'latest':
				default:
					$orderby = ' ORDER BY a.`replied` DESC'; //used in getsticky and get created date
					break;
			}
		}

		$query .= $orderby;

		return $query;
	}

	private function _getDateDiffs(&$results)
	{
		$now = ED::date();
		$today = explode(' ', $now->toMySQL());
		$today = $today[0];

		if (! empty($results)) {
			for ($i = 0 ; $i < count($results); $i++) {
				$item =& $results[$i];

				//creation date
				$creation = $item->created;
				$creation = explode(' ', $creation);
				$creation = $creation[0];

				//daydiff
				$datetotest = ($item->replied == '0000-00-00 00:00:00') ? $item->created : $item->replied;
				$datesegment = explode(' ', $datetotest);
				$datesegment = $datesegment[0];

				$noofdays = floor((abs(strtotime($today) - strtotime($creation)) / (60*60*24)));
				$daydiff = floor((abs(strtotime($today) - strtotime($datesegment)) / (60*60*24)));
				$timediff = $this->calcTimeDiff(strtotime($now->toMySQL()), strtotime($datetotest));

				$item->noofdays = $noofdays;
				$item->daydiff = $daydiff;
				$item->timediff = $timediff;
			}
		}
	}

	private function calcTimeDiff($date1, $date2)
	{
		$diff = abs($date1-$date2);
		$seconds = 0;
		$hours  = 0;
		$minutes = 0;

		if ($diff % 86400 > 0) {

			$rest = ($diff % 86400);
			$days = ($diff - $rest) / 86400;

			if ($rest % 3600 > 0) {
				$rest1 = ($rest % 3600);
				$hours = ($rest - $rest1) / 3600;

				if($rest1 % 60 > 0) {
					$rest2 = ($rest1 % 60);
					$minutes = ($rest1 - $rest2) / 60;
					$seconds = $rest2;
				} else {
					$minutes = $rest1 / 60;
				}
			} else {
				$hours = $rest / 3600;
			}
		} else {
			$days = $diff / 86400;
		}

		$hours = ($days * 24) + $hours;
		$time = $hours . ':' . $minutes . ':' . $seconds;
		return $time;
	}

	private function _buildQueryWhere($filter = '', $category = '', $featuredOnly = 'all' , $exclude = array(), $userId = '', $clusterId = '')
	{
		$my = $this->my;
		$db = $this->db;

		$acl = ED::acl();

		$user_id = $this->input->get('user_id', 0, 'int');
		$includeCluster = false;

		$search = $db->getEscaped($this->input->get('query', '', 'string'));
		$filteractive = (empty($filter)) ? $this->input->get('filter', 'allposts', 'string') : $filter;
		$where = array();

		$publishState = ' a.`published` = ' . $db->Quote(DISCUSS_ID_PUBLISHED);

		// Site admin should be able to view moderated replies
		if ((ED::isSiteAdmin() || $acl->allowed('manage_pending')) && $this->_parent) {
			$publishState = ' a.`published` in (' . $db->Quote(DISCUSS_ID_PUBLISHED) . ',' . $db->Quote(DISCUSS_ID_PENDING) .')';
		}

		$where[] = $publishState;

		// get all posts where parent_id = 0
		if (empty($this->_parent)) {
			$this->_parent = '0';
		}

		if ($user_id) {
			$where[] = ' a.`user_id` = ' . $db->Quote((int) $user_id);
		}

		if ($clusterId) {
			$where[] = ' a.`cluster_id` = '. $clusterId;
			$includeCluster = true;
		}

		if (!$includeCluster) {
			$where[] = ' a.`cluster_id` = ' . $db->Quote(0);
		}

		if ($filteractive == 'featured' || $featuredOnly === true) {
			$where[] = ' a.`featured` = ' . $db->Quote('1');
		} else if ($featuredOnly === false && $filter != 'resolved') {
			$where[] = ' a.`featured` = ' . $db->Quote('0');
		}

		if ($filteractive == 'myposts') {
			$where[] = ' a.`user_id`= ' . $db->Quote($my->id);
		}

		if ($filteractive == 'userposts' && !empty($userId)) {
			$where[] = ' a.`user_id`= ' . $db->Quote($userId);
		}

		if ($filteractive == 'myreplies') {
			$where[] = ' a.`parent_id` != ' . $db->Quote(0) . ' AND a.`user_id`=' . $db->Quote($my->id);
		}

		if (!empty($exclude)) {
			$excludePost = 'a.`id` NOT IN(';

			for ($i = 0; $i < count($exclude); $i++) {
				$excludePost .= $db->Quote($exclude[$i]);

				if (next($exclude) !== false) {
					$excludePost .= ',';
				}
			}

			$excludePost .= ')';
			$where[] = $excludePost;
		}

		// @since 3.0
		if ($filteractive == 'unread') {
			$profile = ED::user($this->my->id);
			$readPosts = $profile->posts_read;

			if ($readPosts) {
				$readPosts = unserialize($readPosts);

				if (count($readPosts) > 1) {
					$extraSQL = implode(',', $readPosts);
					$where[] = ' a.`id` NOT IN (' . $extraSQL . ')';
				} else {
					$where[] = ' a.`id` != ' . $db->Quote($readPosts[0]);
				}
			}

			$where[] = ' a.`legacy` = 0';
		}

		if ($filteractive == 'unanswered') {
			$where[] = ' a.`isresolve`=' . $db->Quote(0);
			$where[] = ' a.`created` = a.`replied`';
		}

		if ($filteractive == 'favourites') {

			if (empty($userId)) {
				$id = $my->id;
			} else {
				$id = $userId;
			}

			$where[] = ' f.`created_by` = ' . $db->quote($id);
		}

		if ($filteractive == 'unresolved') {
			$where[] = ' a.`isresolve`= ' .$db->Quote('0');
		}

		// @since 3.1 resolved filter
		if ($filteractive == 'resolved') {
			$where[] = ' a.`isresolve`=' . $db->Quote(1);
		}

		if ($this->_parent=='allreplies') {
			$where[] = ' a.`parent_id` != ' . $db->Quote('0');

			$excludedCategories = ED::getPrivateCategories();

			if (!empty($excludedCategories)) {
				$where[] = ' a.`category_id` NOT IN (' . implode(',', $excludedCategories) . ')';
			}
		} else {
			$where[] = ' a.`parent_id` = ' . $db->Quote($this->_parent);

			if ($this->_isaccept) {
				$where[] = ' a.`answered` = ' . $db->Quote('1');
			} else {
				$where[] = ' a.`answered` = ' . $db->Quote('0');
			}
		}

		if ($search) {
			$where[] = ' LOWER(a.`title`) LIKE \'%' . $search . '%\' ';
		}

		// Filter by category
		if (!empty($category)) {
			require_once dirname(__FILE__) . '/categories.php';

			if (!is_array($category)) {
				$category = array($category);
			}

			$tmpCategoryArr = array();

			for ($i = 0 ; $i < count($category); $i++) {
				$categoryId = $category[$i];

				// Fetch all subcategories from within this category
				$model = $this->getInstance('Categories', 'EasyDiscussModel');
				$childs	= $model->getChildIds($categoryId);

				if ($childs) {
					$childs[] = $categoryId;

					foreach ($childs as &$child) {
						$child = $db->Quote($child);
						$tmpCategoryArr[] = $child;
					}
				} else {
					$tmpCategoryArr[] = $db->Quote($category[$i]);
				}
			}

			if (count($tmpCategoryArr) > 0) {
				$where[] = ' a.`category_id` IN (' . implode(',', $tmpCategoryArr) . ')';
			}
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	private function _buildQueryOrderBy()
	{
		$mainframe = JFactory::getApplication();

		$filter_order = $mainframe->getUserStateFromRequest('com_easydiscuss.posts.filter_order',		'filter_order',		'created DESC'	, 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_easydiscuss.posts.filter_order_Dir',	'filter_order_Dir',	''				, 'word');

		$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to get posts item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData($usePagination = true, $sort = 'latest', $limitstart = null, $filter = '', $category = '', $limit = null, $featuredOnly = 'all', $userId = null, $isModule = false)
	{
		$query = $this->_buildQuery($sort, $filter , $category, $featuredOnly, false, array(), null, null, $userId);

		// echo $query;exit;

		if ($usePagination) {
			$limitstart = is_null($limitstart) ? $this->getState('limitstart') : $limitstart;
			$limit = is_null($limit) ? $this->getState('limit') : $limit;

			$this->_data = $this->_getList($query, $limitstart , $limit);
			$this->_getDateDiffs($this->_data);
		} else {
			$limit = is_null($limit) ? $this->getState('limit') : $limit;
			$this->_data = $this->_getList($query, 0 , $limit);
			$this->_getDateDiffs($this->_data);

		}

		if ($this->_favs == true) {
			return $this->_data;
		}
	}

	/**
	 * Clears any replies for a discussion as answer.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function clearAcceptedReplies(EasyDiscussPost $post)
	{
		$db = $this->db;

		$query = 'UPDATE `#__discuss_posts` set `answered` = ' . $db->Quote('0');
		$query .= ' WHERE `parent_id` = ' . $db->Quote($post->id);

		$db->setQuery($query);
		$state = $db->query();

		if (!$state) {
			return false;
		}

		// Update the thread once we mark replies as unanswered
		$post->updateThread(array('answered' => '0'));
	}


	public function clearData()
	{
		$this->_data = null;
	}


	/**
	 * Retrieves replies of a specific discussion
	 *
	 * @since	3.0
	 * @since	4.0
	 * @access	public
	 */
	public function getReplies($id, $sort = 'replylatest', $limitstart = null, $limit = null, $pagination = true)
	{
		$acl = ED::acl();
		$my = ED::user();

		$db = $this->db;

		$this->_parent = $id;
		$this->_isaccept = false;

		$useFoundRows = ED::config()->get('system_query', 'default') == 'default' ? true : false;

		$query = 'SELECT SQL_CALC_FOUND_ROWS';
		if (!$useFoundRows) {
			$query = 'SELECT';
		}

		// Include polls
		$query .= ' (SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_polls') . ' WHERE ' . $db->nameQuote('post_id') . ' = a.' . $db->nameQuote('id') . ') AS `polls_cnt`,';

		// Include favourites
		$query .= ' (SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_favourites') . ' WHERE ' . $db->nameQuote('post_id') . ' = a.' . $db->nameQuote('id') . ') AS `totalFavourites`,';

		//sorting criteria
		if ($sort == 'likes') {
			$query .= ' a.`num_likes` as `likeCnt`,';
		}

		if ($sort == 'voted') {
			$query .= ' a.`sum_totalvote` as `VotedCnt`,';
		}

		if ($my->id != 0 && $acl->allowed('vote_discussion')) {
			$query .= ' (SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_votes') . ' WHERE ' . $db->nameQuote('post_id') . ' = a.' . $db->nameQuote('id') . ' AND `user_id` = ' . $db->Quote($my->id) . ') AS `isVoted`,';
		} else {
			$query .= ' ' . $db->Quote('0') . ' as `isVoted`,';
		}

		$query .= ' a.*, ';
		$query .= ' e.`title` AS `category`, ';
		$query .= ' a.`created` as `lastupdate`';

		$query .= ', (select count(1) from `#__discuss_votes` where post_id = a.id) as `total_vote_cnt`';

		$query .= ' FROM `#__discuss_posts` AS a';

		// exclude blocked users #788
		$query .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";

		// Join with category table.
		$query .= '	LEFT JOIN ' . $db->nameQuote('#__discuss_category') . ' AS e ON a.`category_id`=e.`id`';

		// building where conditions

		// query used in replies count
		$cntQuery = "select count(1) from `#__discuss_posts` AS a";

		$where = array();

		$publishState = ' a.`published` = ' . $db->Quote(DISCUSS_ID_PUBLISHED);

		$isModerator = ED::moderator()->isModerator($this->getCategoryId($id), $my->id);

		// Site admin and moderator for certain category should be able to view moderated replies
		if ((ED::isSiteAdmin() || $acl->allowed('manage_pending') || $isModerator)) {
			$publishState = ' a.`published` in (' . $db->Quote(DISCUSS_ID_PUBLISHED) . ',' . $db->Quote(DISCUSS_ID_PENDING) .')';
		}

		$where[] = $publishState;
		$where[] = ' a.`parent_id` = ' . $db->Quote($this->_parent);

		// we do not want to show the reply marked as answer as the answered reply 
		// will be display on top before the reply section in post page.
		$where[] = ' a.`answered` = ' . $db->Quote('0');

		// exclude blocked users #788
		$where[] = ' (uu.`block` = 0 or uu.`id` is null)';

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$query .= $where;
		$cntQuery .= $where;

		// building sort

		switch ($sort) {
			case 'voted':
				$orderby = ' ORDER BY a.`sum_totalvote` DESC'; //used in getreplies only
				break;
			case 'likes':
				$orderby = ' ORDER BY a.`num_likes` DESC'; //used in getdate and getreplies
				break;
			case 'oldest':
				$orderby = " ORDER BY a.`created` ASC"; //used in discussion replies
				break;
			case 'replylatest':
				$orderby = " ORDER BY a.`created` DESC"; //used in discussion replies
				break;
			case 'latest':
			default:
				$orderby = ' ORDER BY a.`replied` DESC'; //used in getsticky and get created date
				break;
		}

		$query .= $orderby;


		// echo $query;
		// echo '<br><br>';

		$result = '';

		if ($limit != DISCUSS_NO_LIMIT) {
			if ($pagination) {
				$limit = $limit ? $limit : $this->getState('limit');

				$result = $this->_getList($query, $limitstart, $limit);
			} else {
				$result	= $this->_getList($query);
			}
		}

		if ($limit != DISCUSS_NO_LIMIT && $pagination) {
			// now lets get the row_count() for pagination.
			if ($useFoundRows) {
				$cntQuery = "select FOUND_ROWS()";
			}

			$db->setQuery($cntQuery);
			$this->_total = $db->loadResult();
			$this->_pagination = ED::pagination($this->_total, $limitstart, $limit);
		}

		$this->_getDateDiffs($result);

		return $result;
	}


	/**
	 * Retrieves site wide latest replies
	 *
	 * @since	4.0
	 * @access	public
	 */

	public function getRecentReplies($count = 5)
	{
		$db = ED::db();

		$count = (int) $count;

		if (! $count) {
			$count = 5;
		}

		$query = "select a.* from `#__discuss_posts` as a";
		$query .= " inner join `#__discuss_thread` as b on a.thread_id = b.id";

		//exclude blocked user's replies
		$query .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";

		$query .= ' where a.`published` = 1';
		$query .= " and a.`parent_id` > 0";

		if (!ED::isSiteAdmin() && !ED::isModerator()) {
			$query	.= ' AND a.`private`=' . $db->Quote(0);
		}

		$catOptions = array();
		$catOptions['idOnly'] = true;
		$catOptions['includeChilds'] = true;

		$catAccessSQL = ED::category()->genCategoryAccessSQL('a.category_id', $catOptions);
		$query .= " and " . $catAccessSQL;

		//exclude blocked users replies
		$query .= " AND (uu.`block` = 0 OR uu.`id` IS NULL)";

		$query .= " order by a.`id` desc";
		$query .= " limit $count";

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}


	/**
	 * Method to publish or unpublish posts
	 *
	 * @access public
	 * @return array
	 */
	public function publishPosts($ids = array(), $publish = 1)
	{
		if (!$ids) {
			return false;
		}

		$db = ED::db();
		$postIds = implode(',', $ids);

		$query = 'UPDATE ' . $db->nameQuote('#__discuss_posts') . ' '
				. 'SET ' . $db->nameQuote('published') . '=' . $db->Quote($publish) . ' '
				. 'WHERE ' . $db->nameQuote('id') . ' IN (' . $postIds . ')';

		$db->setQuery($query);

		if (!$db->query()) {
			$this->setError($db->getErrorMsg());

			return false;
		}

		// Update thread table
		$thread = ED::model('threaded');
		$thread->publishThread($ids, $publish);

		// We need to update the parent post last replied date
		foreach ($ids as $id) {

			$post = ED::table('Posts');
			$post->load($id);

			// We only need replies
			if (!$post->parent_id) {
				continue;
			}

			// Load the parent
			$parent = ED::table('Post');
			$parent->load($post->parent_id);

			// Check if current reply date is more than the last replied date of the parent to determine if this reply is new or is an old pending moderate reply.
			if ($reply->created > $parent->replied) {
				$query	= 'UPDATE ' . $db->nameQuote('#__discuss_posts') . ' '
						. 'SET ' . $db->nameQuote('replied') . '=' . $db->Quote($reply->created) . ' '
						. 'WHERE ' . $db->nameQuote('id') . '=' . $db->Quote($parent->id);

				$db->setQuery($query);

				if (!$db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		return true;
	}

	public function getPostsBy($type, $typeId = 0, $sort = 'latest', $limitstart = null , $published = DISCUSS_FILTER_PUBLISHED , $search = '' , $limit = null)
	{
		$db = ED::db();

		$queryPagination = false;
		$queryWhere = '';
		$queryOrder = '';
		$queryLimit = '';
		$queryWhere = '';

		switch ($published) {
			case DISCUSS_FILTER_PUBLISHED:
			default:
				$queryWhere	= ' WHERE a.`published` = ' . $db->Quote('1');
				break;
		}

		$contentId = '';
		$isIdArray = false;
		if (is_array($typeId)) {
			if (count($typeId) > 1) {
				$contentId = implode(',', $typeId);
				$isIdArray = true;
			} else {
				$contentId = $typeId[0];
			}
		} else {
			$contentId = $typeId;
		}

		switch ($type) {
			case 'category':
				$queryWhere .= ($isIdArray) ? ' AND a.`category_id` IN ('. $contentId .')' : ' AND a.`category_id` = ' . $db->Quote($contentId);
				break;
			case 'user':
				$queryWhere .= ' AND a.`user_id`=' . $db->Quote($contentId);
				break;
			default:
				break;
		}

		if (!empty($search)) {
			$queryWhere .= ' AND a.`title` LIKE ' . $db->Quote('%' . $search . '%');
		}


		//getting only main posts.
		$queryWhere .= ' AND a.`parent_id` = 0';

		switch ($sort) {
			case 'latest':
				$queryOrder = ' ORDER BY a.`created` DESC';
				break;
			case 'popular':
				$queryOrder = ' ORDER BY a.`hits` DESC';
				break;
			case 'alphabet':
				$queryOrder = ' ORDER BY a.`title` ASC';
			case 'likes':
				$queryOrder = ' ORDER BY a.`num_likes` DESC';
				break;
			default :
				$queryOrder = ' ORDER BY a.`' . $sort . '` DESC';
				break;
		}

		$limitstart = is_null($limitstart) ? (int) $this->getState('limitstart') : $limitstart;
		$limit = is_null($limit) ? (int) $this->getState('limit') : $limit;

		$limitstart = abs($limitstart);

		// Ensure that nobody can temper with the limit
		if ($limit < 0) {
			$limit = 10;
		}

		$queryLimit = ' LIMIT ' . $limitstart . ',' . $limit;

		$query	= 'SELECT COUNT(1) FROM `#__discuss_posts` AS a';
		$query	.= $queryWhere;

		$db->setQuery($query);
		$this->_total	= $db->loadResult();

		jimport('joomla.html.pagination');
		// $this->_pagination	= new JPagination($this->_total , $limitstart , $limit);
		$this->_pagination	= DiscussHelper::getPagination($this->_total, $limitstart, $limit);


		$date = ED::date();

		$query = 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created`) as `noofdays`, ';
		$query .= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', a.`created`) as `timediff`,';
		$query .= ' a.`id`, a.`title`, a.`alias`, a.`created`, a.`modified`, a.`replied`, a.`legacy`,';
		$query .= ' a.`content`, a.`category_id`, a.`published`, a.`ordering`, a.`vote`, a.`hits`, a.`islock`,';
		$query .= ' a.`featured`, a.`isresolve`, a.`isreport`, a.`user_id`, a.`parent_id`,';
		$query .= ' a.`user_type`, a.`poster_name`, a.`poster_email`, a.`num_likes`,';
		$query .= ' a.`num_negvote`, a.`sum_totalvote`,a.`answered`,';
		$query .= ' a.`post_status`, a.`post_type`, pt.`title` AS `post_type_title`,pt.`suffix` AS `post_type_suffix`,';
		$query .= ' count(b.id) as `num_replies`,';
		$query .= ' c.`title` AS `category`, a.`password`';
		$query .= ' FROM ' . $db->nameQuote('#__discuss_posts') . ' AS a';
		$query .= '	 LEFT JOIN `#__discuss_posts` AS b ON a.`id` = b.`parent_id`';
		$query .= '	 AND b.`published` = 1';
		$query .= '	 LEFT JOIN `#__discuss_category` AS c ON a.`category_id` = c.`id`';
		$query .= '	LEFT JOIN `#__discuss_post_types` AS pt ON a.`post_type` = pt.`alias`';

		$query .= $queryWhere;

		$query .= ' GROUP BY (a.id)';

		$query .= $queryOrder;
		$query .= $queryLimit;

		$db->setQuery($query);
		if ($db->getErrorNum() > 0) {
			JError::raiseError($db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		$result	= $db->loadObjectList();

		return $result;
	}

	public function setLastReplyBatch($ids)
	{
		$authorIds  = array();

		if (count($ids) > 0) {
			$db	= ED::db();

			$query = 'SELECT * FROM `#__discuss_posts` as a';
			if (count($ids) == 1) {
				$query .= ' WHERE a.`parent_id` = ' . $db->Quote($ids[0]);
			} else {
				$query .= ' WHERE a.`parent_id` IN (' . implode(',', $ids) . ')';
			}
			$query .= ' and a.`id` = (select max(b.`id`) from `#__discuss_posts` as b where a.`parent_id` = b.`parent_id`)';

			$db->setQuery($query);
			$result = $db->loadObjectList();

			if (count($result) > 0) {
				foreach ($result as $item) {
					self::$_lastReply[$item->parent_id] = $item;
					$authorIds[] = $item->user_id;
				}
			}

			foreach ($ids as $id) {
				if (!isset(self::$_lastReply[$id])) {
					self::$_lastReply[$id] = '';
				}
			}
		}

		return $authorIds;
	}


	public function getLastReply($id)
	{
		if (isset(self::$_lastReply[$id])) {
			return self::$_lastReply[$id];
		}

		$db	= ED::db();
		$query = 'SELECT * FROM `#__discuss_posts` as a';

		// exclude blocked users #788
		$query .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";

		$query .= ' WHERE a.' . $db->nameQuote('parent_id') . ' = ' . $db->Quote($id);
		$query .= ' AND a.`published` = ' . $db->Quote('1');

		// exclude blocked users #788
		$query .= " AND (uu.`block` = 0 OR uu.`id` is null)";

		$query .= ' ORDER BY a.' . $db->nameQuote('created') . ' DESC LIMIT 1';
		$db->setQuery($query);
		$result = $db->loadObject();

		self::$_lastReply[$id] = $result;
		return $result;
	}

	public function getTotalReplies($id)
	{
		$db	= ED::db();
		$query = 'SELECT COUNT(1) AS `replies`';
		$query .= ' FROM `#__discuss_posts` as a';
		$query .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";

		$query .= ' WHERE a.`parent_id` = ' . $db->Quote($id);
		$query .= ' AND a.`published` = ' . $db->Quote('1');
		$query .= " AND (uu.`block` = 0 OR uu.`id` is null)";

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieves the total number of thread posts created on the site.
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getTotalThread()
	{
		$db	= ED::db();

		$query = "select count(1) from `#__discuss_thread` as a";

		// exclude blocked users #788
		$query .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";

		$query .= " where a.`published` = " . $db->Quote(1);
		$query .= " and a.`cluster_id` = " . $db->Quote(0);

		$query .= " and (uu.`block` = 0 OR uu.`id` is null)";

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}


	/**
	 * Retrieves the total number of comments for this particular discussion.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		$id		The post id
	 * @param	string	$type	Type of comments to calculate (post to calculate individual post comment count, thread to calculate full thread comment count)
	 * @return	int
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public static function getTotalComments($postid, $type = 'post')
	{

		static $loaded = array();

		$sig = $postid . $type;

		if (isset($loaded[$sig])) {
			return $loaded[$sig];
		}

		$db	= ED::db();

		$ids = array();

		$count = 0;

		if ($type == 'thread') {
			$query = 'SELECT `id` FROM `#__discuss_posts` WHERE `parent_id` = ' . $db->quote($postid);
			$db->setQuery($query);
			$ids = $db->loadResultArray();
			array_unshift($ids, $postid);
		} else {
			$ids = array($postid);
		}

		foreach ($ids as $id) {
			$query	= 'SELECT COUNT(1) FROM `#__discuss_comments` WHERE `post_id` = ' . $db->quote($id);
			$db->setQuery($query);

			$result = $db->loadResult();

			$tmpSig = $result . 'post';
			$loaded[$tmpSig] = $result;

			$count += (int) $result;
		}

		$loaded[$sig] = $count;

		return $loaded[$sig];
	}

	/**
	 * Method to retrieve blog posts based on the given tag id.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTaggedPost($tagId = 0, $sort = 'latest', $filter = '', $limitStart = '', $limit = null, $pagination = true)
	{
		if ($tagId == 0) {
			return false;
		}

		if (is_array($tagId) && empty($tagId)) {
			return false;
		}

		$db = ED::db();
		$limit = is_null($limit) ? (int) $this->getState('limit') : $limit;

		$limitstart = (empty($limitStart)) ? $this->getState('limitstart') : $limitStart;
		$limitstart = (int) $limitstart;
		
		$limitstart = abs($limitstart);

		if ($limit < 0) {
			$limit = 0;
		}

		$filteractive = (empty($filter)) ? JRequest::getString('filter', 'allposts') : $filter;

		$date = ED::date();

		$queryWhere = '';

		if (is_array($tagId)) {
			$queryWhere = ' WHERE a.tag_id IN (' . implode(',', $tagId) . ')';

		} else {
			$queryWhere = ' WHERE a.tag_id = ' . $db->Quote($tagId);
		}

		$query = ' SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', t.`created`) as `noofdays`, ';
		$query .= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', t.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', t.`created`) as `timediff`,';
		$query .= ' b.*,';
		$query .= ' t.`has_polls` as `polls_cnt`, t.`num_fav` as `totalFavourites`, t.`num_replies`, t.`num_attachments` as attachments_cnt,';
		$query .= ' t.`num_likes` as `likeCnt`, t.`sum_totalvote` as `VotedCnt`,';
		$query .= ' t.`replied` as `lastupdate`, t.`vote` as `total_vote_cnt`,';

		if ($this->my->id) {
			$query .= " (SELECT COUNT(1) FROM " . $db->nameQuote('#__discuss_votes') . " WHERE `post_id` = t.`post_id` AND `user_id` = " . $db->Quote($this->my->id) . ") AS `isVoted`";
		} else {
			$query .= " 0 as `isVoted`";
		}

		if (is_array($tagId)) {
			$query .= ' FROM `#__discuss_thread` AS t';
			$query .= ' INNER JOIN `#__discuss_posts` AS b on t.post_id = b.id';
			$query .= ' INNER JOIN `#__discuss_posts_tags` AS a ON a.post_id = b.id';
			$query .= ' INNER JOIN `#__discuss_tags` AS tg ON tg.id = a.tag_id';
			$query .= ' INNER JOIN `#__discuss_category` AS e ON e.id = t.category_id';
		} else {
			$query .= ' FROM ' . $db->nameQuote('#__discuss_posts_tags') . ' AS a ';
			$query .= ' INNER JOIN `#__discuss_thread` AS t on a.`post_id` = t.`post_id`';
			$query .= ' INNER JOIN `#__discuss_posts` AS b on t.`post_id` = b.`id`';
			$query .= ' INNER JOIN ' . $db->nameQuote('#__discuss_category') . ' AS e ';
			$query .= ' ON e.`id` = t.`category_id` ';
		}

		// exclude blocked users posts
		$query .= " left join " . $db->nameQuote('#__users') . " as uu on t.`user_id` = uu.`id`";

		$query .= $queryWhere;
		$query .= ' AND t.`published` = ' . $db->Quote('1');

		$excludeCats = ED::getPrivateCategories();

		if (!empty($excludeCats)) {
			$query .= ' AND t.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		if ($filteractive == 'featured') {
			$query .= ' AND t.`featured` = ' . $db->Quote('1');
		}

		// Check for private post
		if (!ED::isSiteAdmin() && !ED::isModerator()) {
			$query .= " AND (t.`private` = " . $db->Quote(0) . " OR (t.`private` = " . $db->Quote(1) . " AND t.`user_id` = " . $db->Quote(ED::user()->id) . "))";
		}

		// Do not include cluster item here.
		$query .= ' AND t.`cluster_id` = ' . $db->Quote(0);

		// exclude blocked users posts
		$query .= ' AND (uu.`block` = 0 OR uu.`id` IS NULL)';

		$orderby = '';

		switch ($sort) {
			case 'popular':
				$orderby = ' ORDER BY `t.num_replies` DESC, b.created DESC'; //used in getdata only
				break;
			case 'hits':
				$orderby = ' ORDER BY t.hits DESC'; //used in getdata only
				break;
			case 'voted':
				$orderby = ' ORDER BY t.`sum_totalvote` DESC, t.created DESC'; //used in getreplies only
				break;
			case 'likes':
				$orderby = ' ORDER BY t.`num_likes` DESC, t.created DESC'; //used in getdate and getreplies
				break;
			case 'activepost':
				$orderby = ' ORDER BY t.featured DESC, t.replied DESC'; //used in getsticky and getlastreply
				break;
			case 'featured':
			case 'latest':
			default:
				$orderby = ' ORDER BY t.featured DESC, t.created DESC'; //used in getsticky and get created date
				break;
		}

		if (is_array($tagId)) {
			$orderby = $orderby . ', count(t.post_id) DESC';
		}

		if ($filteractive == 'unanswered') {
			$groupby = ' GROUP BY t.`post_id` HAVING(COUNT(c.id) = 0)';
		} else {
			$groupby = ' GROUP BY t.`post_id`';
		}

		$query .= $groupby . $orderby;

		//total tag's post sql
		$totalQuery = 'SELECT COUNT(1) FROM (';
		$totalQuery .= $query;
		$totalQuery .= ') as x';

		if ($pagination) {
			$this->_total = $this->_getListCount($query);
			$this->_pagination = ED::pagination($this->_total, $limitstart, $limit);
		}

		$query .= ' LIMIT ' . $limitstart . ',' . $limit;

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if ($rows) {
			$typequery = "SELECT * FROM " . $db->nameQuote('#__discuss_post_types');
			$db->setQuery($typequery);
			$posttypes = $db->loadObjectList("alias");

			$count = count($rows);

			for ($i = 0; $i < $count; $i++) {
				$row =& $rows[$i];

				if (isset($posttypes[$row->post_type])) {
					$row->post_type_suffix = $posttypes[$row->post_type]->suffix;
					$row->post_type_title = $posttypes[$row->post_type]->title;
				} else {
					$row->post_type_suffix = "";
					$row->post_type_title = "";
				}
			}
		}


		return $rows;
	}

	public function getNegativeVote($postId)
	{
		$db = ED::db();

		$query = 'SELECT COUNT(1) FROM `#__discuss_votes`';
		$query .= ' WHERE `post_id` = ' . $db->Quote($postId);
		$query .= ' AND `value` = ' . $db->Quote('-1');

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	public function getComments($postId, $limit = null, $limitstart = null)
	{
		$db = ED::db();
		$date = ED::date();
		$offset = ED::getTimeZoneOffset();
		$config = $this->config;

		$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL(true)) . ', DATE_ADD(a.`created`, INTERVAL '.$offset.' HOUR)) as `noofdays`, '
				. ' DATEDIFF(' . $db->Quote($date->toMySQL(true)) . ', DATE_ADD(a.`created`, INTERVAL '.$offset.' HOUR)) as `daydiff`, '
				. ' TIMEDIFF(' . $db->Quote($date->toMySQL(true)). ', DATE_ADD(a.`created`, INTERVAL '.$offset.' HOUR)) as `timediff`, '
				. ' a.* ';
		$query	.= ' FROM `#__discuss_comments` AS a';

		// exclude comments from blocked users #788
		$query .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";


		// $runSort = false;

		$orderBy = '';

		if (is_array($postId)) {
			if (count($postId) == 1) {
				$query .= ' WHERE a.`post_id` = ' . $db->Quote($postId);
				$orderBy = ' ORDER BY a.`created` ASC';
			} else {
				$query .= ' WHERE a.`post_id` IN (' . implode(',', $postId) . ')';
				$orderBy = ' ORDER BY a.post_id, a.`created` ASC';
			}
		} else {
			$query .= ' WHERE a.`post_id` = ' . $db->Quote($postId);
		}

		if (!is_array($postId)) {
			if ($config->get('main_comment_ordering') == 'asc'){
				$orderBy = ' ORDER BY a.`created` ASC';
			} elseif ($config->get('main_comment_ordering') == 'desc'){
				$orderBy = ' ORDER BY a.`created` DESC';
			}
		}

		// exclude comments from blocked users #788
		$query .= " and (uu.`block` = 0 OR uu.`id` IS NULL)";

		$query .= $orderBy;

		if ($limit !== null) {
			if ($limitstart !== null) {
				$query .= ' LIMIT ' . $limitstart . ',' . $limit;
			} else {
				$query .= ' LIMIT ' . $limit;
			}
		}

		$db->setQuery($query) ;
		$result = $db->loadObjectList('created');

		return $result;
	}

	/**
	 * Method to get replies
	 *
	 * @access public
	 * @return array
	 */
	public function getAcceptedReply($id)
	{
		$db = ED::db();
		$this->_parent = $id;
		$this->_isaccept = true;

		$my = JFactory::getUser();
		// $query = $this->_buildQuery('latest' , 'answer' , '', 'all', true);


		$query = "SELECT (SELECT COUNT(1) FROM `#__discuss_polls` WHERE `post_id` = a.`id`) AS `polls_cnt`,";
		$query .= " (SELECT COUNT(1) FROM `#__discuss_favourites` WHERE `post_id` = a.`id`) AS `totalFavourites`,";
		$query .= " 0 AS `num_replies`,";

		if ($my->id != 0) {
			$query .= " (SELECT COUNT(1) FROM `#__discuss_votes` WHERE `post_id` = a.`id` AND `user_id` = " . $db->Quote($my->id) . ") AS `isVoted`,"; 
		} else {
			$query .= " 0 as `isVoted`,";
		}

		$query .= " pt.`suffix` AS post_type_suffix, pt.`title` AS post_type_title , a.*, e.`title` AS `category`,";
		$query .= " a.`created` as `lastupdate`, ";
		$query .= " (select count(1) from `#__discuss_votes` where post_id = a.id) as `total_vote_cnt` ";
		$query .= " FROM `#__discuss_posts` AS a";
		$query .= " LEFT JOIN `#__discuss_post_types` AS pt ON a.`post_type`= pt.`alias`";
		$query .= " LEFT JOIN `#__discuss_category` AS e ON a.`category_id`= e.`id`";
		$query .= " WHERE a.`published` = " . $db->Quote('1');
		$query .= " AND a.`parent_id` = " . $db->Quote($id);
		$query .= " AND a.`answered` = " . $db->Quote('1');

		$db->setQuery($query);
		$result = $db->loadObjectList();
		$this->_getDateDiffs($result);

		return $result;
	}

	public function getUnresolvedCount($filter = '', $category = '', $tagId = '', $featuredOnly = 'all', $queryOnly = false, $clusterId = '', $userId = '')
	{
		$db	= ED::db();
		$my	= JFactory::getUser();

		$queryExclude = '';
		$excludeCats = array();
		$includeCluster = false;

		// get all private categories id
		$excludeCats = ED::getPrivateCategories();

		if (!empty($excludeCats)) {
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_thread') . ' AS a';

		// exclude blocked users #788
		$query .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";

		if (!empty($tagId)) {
			$query .= ' INNER JOIN `#__discuss_posts_tags` as c';
			$query .= '	ON a.`id` = c.`post_id`';
			$query .= '	AND c.`tag_id` = ' . $db->Quote($tagId);
		}

		$query .= ' WHERE a.`published` = ' . $db->Quote(1);

		if ($clusterId) {
			$query .= ' AND a.`cluster_id` = ' . $clusterId;
			$includeCluster = true;
		}

		if (!$includeCluster && $userId) {
			$query .= ' AND a.`cluster_id` = '. $db->Quote(0);
			$query .= ' AND a.`user_id` = '. $db->Quote($userId);
		}

		// @rule: Should not calculate resolved posts
		$query .= ' AND a.`isresolve`=' . $db->Quote(0);

		if ($featuredOnly === true) {
			$query .= ' AND a.`featured`=' . $db->Quote(1);

		} else if($featuredOnly === false) {
			$query	.= ' AND a.`featured`=' . $db->Quote(0);
		}

		if (!ED::isSiteAdmin() && !ED::isModerator()) {
			$query	.= ' AND a.`private`=' . $db->Quote(0);
		}

		if ($category) {

			if (!is_array($category)) {
				$category = array($category);
			}

			$model = ED::model('Categories');

			foreach ($category as $categoryId) {

				$data = $model->getChildIds($categoryId);

				if ($data) {

					foreach ($data as $childCategory) {
						$childs[] = $childCategory;
					}
				}

				$childs[] = $categoryId;
			}

			$query .= ' AND a.`category_id` IN (' . implode(',' , $childs) . ')';
		}

		$query .= $queryExclude;

		// exclude blocked users #788
		$query .= ' AND (uu.`block` = 0 or uu.`id` is null)';

		$db->setQuery($query);

		return $db->loadResult();
	}

	public function getLockedCount($filter = '', $category = '', $tagId = '', $featuredOnly = 'all', $queryOnly = false, $clusterId = '')
	{
		$db = $this->db;
		$my	= $this->my;

		$queryExclude = '';
		$excludeCats = array();
		$includeCluster = false;

		// get all private categories id
		$excludeCats = ED::getPrivateCategories();

		if (!empty($excludeCats)) {
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query	= 'SELECT COUNT(a.`id`) FROM ' . $db->nameQuote('#__discuss_posts') . ' AS a';

		if (!empty($tagId)) {
			$query .= ' INNER JOIN `#__discuss_posts_tags` as c';
			$query .= '	ON a.`id` = c.`post_id`';
			$query .= '	AND c.`tag_id` = ' . $db->Quote($tagId);
		}

		$query .= ' WHERE a.`parent_id` = ' . $db->Quote(0);
		$query .= ' AND a.`published`=' . $db->Quote(1);

		if ($clusterId) {
			$query .= ' AND a.`cluster_id` = ' . $clusterId;
			$includeCluster = true;
		}

		if (!$includeCluster) {
			$query .= ' AND a.`cluster_id` = ' . $db->Quote(0);
		}

		// @rule: Should not calculate resolved posts
		$query .= ' AND a.`islock`=' . $db->Quote(1);

		if ($featuredOnly === true) {
			$query .= ' AND a.`featured`=' . $db->Quote(1);
		} else if ($featuredOnly === false) {
			$query .= ' AND a.`featured`=' . $db->Quote(0);
		}

		if ($category) {
			if (!is_array($category)) {
				$category = array($category);
			}

			$model = ED::model('Categories');

			foreach ($category as $categoryId) {
				$data = $model->getChildIds($categoryId);

				if ($data) {
					foreach ($data as $childCategory) {
						$childs[] = $childCategory;
					}
				}

				$childs[] = $categoryId;
			}

			$query .= ' AND a.`category_id` IN (' . implode(',' , $childs) . ')';
		}

		$query .= $queryExclude;

		$db->setQuery($query);

		return $db->loadResult();
	}

	public function getResolvedCount($filter = '', $category = '', $tagId = '', $featuredOnly = 'all', $queryOnly = false, $clusterId = '', $userId = '')
	{
		$db = $this->db;
		$my	= $this->my;

		$queryExclude = '';
		$excludeCats = array();
		$includeCluster = false;

		// get all private categories id
		$excludeCats = ED::getPrivateCategories();

		if (!empty($excludeCats)) {
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query	= 'SELECT COUNT(a.`id`) FROM ' . $db->nameQuote('#__discuss_posts') . ' AS a';

		// exclude blocked users #788
		$query .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";


		if (!empty($tagId)) {
			$query .= ' INNER JOIN `#__discuss_posts_tags` as c';
			$query .= '	ON a.`id` = c.`post_id`';
			$query .= '	AND c.`tag_id` = ' . $db->Quote($tagId);
		}

		$query .= ' WHERE a.`parent_id` = ' . $db->Quote(0);
		$query .= ' AND a.`published`=' . $db->Quote(1);

		if ($clusterId) {
			$query .= ' AND a.`cluster_id` = ' . $clusterId;
			$includeCluster = true;
		}

		if (!$includeCluster && $userId) {
			$query .= ' AND a.`cluster_id` = ' . $db->Quote(0);
			$query .= ' AND a.`user_id` = ' . $db->Quote($userId);
		}

		// @rule: Should not calculate resolved posts
		$query .= ' AND a.`isresolve`=' . $db->Quote(1);

		if ($featuredOnly === true) {
			$query .= ' AND a.`featured`=' . $db->Quote(1);
		} else if ($featuredOnly === false) {
			$query .= ' AND a.`featured`=' . $db->Quote(0);
		}

		if ($category) {
			if (!is_array($category)) {
				$category = array($category);
			}

			$model = ED::model('Categories');

			foreach ($category as $categoryId) {
				$data = $model->getChildIds($categoryId);

				if ($data) {
					foreach ($data as $childCategory) {
						$childs[] = $childCategory;
					}
				}

				$childs[] = $categoryId;
			}

			$query .= ' AND a.`category_id` IN (' . implode(',' , $childs) . ')';
		}

		$query .= $queryExclude;

		// exclude blocked users #788
		$query .= ' AND (uu.`block` = 0 or uu.`id` is null)';

		$db->setQuery($query);

		return $db->loadResult();
	}

	public function getUnansweredCount($filter = '' , $category = '' , $tagId = '', $featuredOnly = 'all', $clusterId = '' , $userId = '')
	{
		$db	= ED::db();
		$my	= JFactory::getUser();

		$queryExclude = '';
		$excludeCats = array();
		$includeCluster = false;

		// get all private categories id
		$excludeCats = DiscussHelper::getPrivateCategories();
		if (! empty($excludeCats)) {
			$queryExclude .= " AND a.`category_id` NOT IN (" . implode(',', $excludeCats) . ')';
		}

		$query = "select count(1)";
		$query .= " from `#__discuss_thread` as a";

		// exclude blocked users #788
		$query .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";

		if (! empty($tagId)) {
			$query .= " INNER JOIN `#__discuss_posts_tags` as c";
			$query .= "	ON a.`post_id` = c.`post_id`";
			$query .= "	AND c.`tag_id` = " . $db->Quote($tagId);
		}

		$query .= " WHERE a.`published` = " . $db->Quote('1');

		// @rule: Should not calculate resolved posts
		$query .= " AND a.`isresolve`= " . $db->Quote(0);

		// unanswered thread always have 0 replies
		$query .= " AND a.`num_replies` = 0";

		if ($clusterId) {
			$query .= " AND a.`cluster_id` = " . $clusterId;
			$includeCluster = true;
		}

		if (!$includeCluster && $userId) {
			$query .= " AND a.`cluster_id` = " . $db->Quote(0);
			$query .= " AND a.`user_id` = " . $db->Quote($userId);
		}

		if ($featuredOnly === true) {
			$query	.= " AND a.`featured` = " . $db->Quote('1');
		} else if ($featuredOnly === false) {
			$query	.= " AND a.`featured` = " . $db->Quote('0');
		}

		if (!ED::isSiteAdmin() && !ED::isModerator()) {
			$query	.= " AND a.`private` = " . $db->Quote(0);
		}

		if ($category) {
			$model = ED::model('Categories');
			$childs	= $model->getChildIds($category);
			$childs[] = $category;
			$query .= " AND a.`category_id` IN (" . implode(',' , $childs) . ")";
		}

		$query .= $queryExclude;

		// exclude blocked users #788
		$query .= ' AND (uu.`block` = 0 or uu.`id` is null)';

		$db->setQuery($query);

		return $db->loadResult();
	}

	public function getUnreadCount($category = 0 , $excludeFeatured = false)
	{
		$db = ED::db();
		$my = JFactory::getUser();

		$excludeCats = DiscussHelper::getPrivateCategories();

		if (!is_array($category)) {
			$category = array($category);
		}

		$catModel = ED::model('Categories');

		$childs = array();

		foreach ($category as $categoryId) {
			$data = $catModel->getChildIds($categoryId);

			if ($data) {
				foreach ($data as $childCategory) {
					$childs[] = $childCategory;
				}
			}

			$childs[] = $categoryId;
		}

		if (empty($category)) {
			$categoryIds = false;
		} else {
			$categoryIds = array_diff($childs, $excludeCats);

			if (empty($categoryIds)) {
				return '0';
			}
		}

		$profile = ED::user($this->my->id);

		$readPosts = $profile->posts_read;
		$extraSQL = '';


		if ($readPosts) {
			$readPosts = unserialize($readPosts);
			if (count($readPosts) > 1) {
				$extraSQL = implode(',', $readPosts);
				$extraSQL = ' AND `id` NOT IN (' . $extraSQL . ')';
			} else {
				$extraSQL = ' AND `id` != ' . $db->Quote($readPosts[0]);
			}
		}

		$query = 'SELECT COUNT(1) FROM `#__discuss_posts`';

		// exclude blocked users #788
		$query .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";

		$query .= ' WHERE `published` = ' . $db->Quote('1');
		$query .= ' AND `parent_id` = ' . $db->Quote('0');

		if ($categoryIds && !(count($categoryIds) == 1 && empty($categoryIds[0]))) {

			if (count($categoryIds) == 1) {
				$query .= ' AND `category_id` = ' . $db->Quote($categoryIds[0]);
			} else {
				$query .= ' AND `category_id` IN (' . implode(',', $categoryIds) .')';
			}
		}

		$query .= ' AND `answered` = ' . $db->Quote('0');

		if ($excludeFeatured) {
			$query .= ' AND `featured` = ' . $db->Quote('0');
		}

		if (!ED::isSiteAdmin() && !ED::isModerator()) {
			$query .= ' AND `private`=' . $db->Quote(0);
		}

		$query .= ' AND `legacy` = ' . $db->Quote('0');

		$query .= $extraSQL;

		// exclude blocked users #788
		$query .= ' AND (uu.`block` = 0 or uu.`id` is null)';

		$db->setQuery($query);
		$result = $db->loadResult();

		return empty($result) ? '0' : $result;
	}

	public function getFeaturedCount($filter = '' , $category = '' , $tagId = '')
	{
		$db = ED::db();
		$my = JFactory::getUser();

		$queryExclude = '';
		$excludeCats = array();

		// get all private categories id
		$excludeCats = DiscussHelper::getPrivateCategories();

		if (! empty($excludeCats)) {
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query = 'SELECT COUNT(1) as `CNT` FROM `#__discuss_posts` AS a';

		if (! empty($tagId)) {
			$query .= ' INNER JOIN `#__discuss_posts_tags` AS b ON a.`id` = b.`post_id`';
			$query .= ' AND b.`tag_id` = ' . $db->Quote($tagId);
		}

		$query .= ' WHERE a.`featured` = ' . $db->Quote('1');
		$query .= ' AND a.`parent_id` = ' . $db->Quote('0');
		$query .= ' AND a.`published` = ' . $db->Quote('1');

		if ($category) {
			$query .= ' AND a.`category_id`=' . $db->Quote($category);
		}
		$query .= $queryExclude;

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	public function getFeaturedPosts($category = '')
	{
		$db = ED::db();
		$my = JFactory::getUser();

		$queryExclude = '';
		$excludeCats = array();

		// get all private categories id
		$excludeCats = DiscussHelper::getPrivateCategories();

		if (! empty($excludeCats)) {
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}


		$query = 'SELECT a.* FROM `#__discuss_posts` AS a';

		if (! empty($tagId)) {
			$query .= ' INNER JOIN `#__discuss_posts_tags` AS b ON a.`id` = b.`post_id`';
			$query .= ' AND b.`tag_id` = ' . $db->Quote($tagId);
		}

		$query .= ' WHERE a.`featured` = ' . $db->Quote('1');
		$query .= ' AND a.`parent_id` = ' . $db->Quote('0');
		$query .= ' AND a.`published` = ' . $db->Quote('1');

		if ($category) {
			$query .= ' AND a.`category_id`=' . $db->Quote($category);
		}

		$query .= $queryExclude;
		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Get unresolved posts for particular user
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getUnresolvedFromUser($userId, $resolve = 0)
	{
		$db = ED::db();
		$date = ED::date();

		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');

		$query = 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', b.`created`) as `noofdays`, ';
		$query .= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', b.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', b.`created`) as `timediff`,';
		$query .= ' b.`id`, b.`title`, b.`alias`, b.`created`, b.`modified`, b.`replied`, b.`legacy`, b.`priority`,';
		$query .= ' b.`content`, b.`category_id`, b.`published`, b.`ordering`, b.`vote`, b.`hits`, b.`islock`,';
		$query .= ' b.`featured`, b.`isresolve`, b.`isreport`, b.`user_id`, b.`parent_id`,';
		$query .= ' b.`user_type`, b.`poster_name`, b.`poster_email`, b.`num_likes`,';
		$query .= ' b.`num_negvote`, b.`sum_totalvote`,b.`answered`, b.`thread_id`,';
		$query .= ' b.`post_status`, b.`post_type`, pt.`title` AS `post_type_title`,pt.`suffix` AS `post_type_suffix`,';
		$query .= ' count(d.id) as `num_replies`,';
		$query .= ' c.`title` as `category`, b.`password`';
		$query .= ' FROM ' . $db->nameQuote('#__discuss_posts') . ' AS b ';
		$query .= ' LEFT JOIN ' . $db->nameQuote('#__discuss_posts') . ' AS d ';
		$query .= ' ON d.' . $db->nameQuote('parent_id') . '=b.' . $db->nameQuote('id');
		$query .= ' LEFT JOIN ' . $db->nameQuote('#__discuss_category') . ' AS c';
		$query .= ' ON c.' . $db->nameQuote('id') . ' = b.' . $db->nameQuote('category_id');
		$query .= ' LEFT JOIN ' . $db->nameQuote('#__discuss_post_types') . ' AS pt';
		$query .= ' ON b.`post_type` = pt.' . $db->nameQuote('alias');
		$query .= ' WHERE b.' . $db->nameQuote('user_id') . ' = ' . $db->Quote($userId);
		$query .= ' AND b.' . $db->nameQuote('isresolve') . '=' . $db->Quote($resolve);
		$query .= ' AND b.`parent_id` = ' . $db->Quote('0');
		$query .= ' AND b.' . $db->nameQuote('published') . ' = ' . $db->Quote(1);

		// We do not want to include anything from cluster here.
		$query .= ' AND b.' . $db->nameQuote('cluster_id') . ' = ' . $db->Quote(0);

		// If the post is anonymous we shouldn't show to public.
		if ($this->my->id != $userId) {

			$query	.= ' AND b.' . $db->nameQuote('anonymous') . ' = ' . $db->Quote(0);

			$query .= ' AND b.' . $db->nameQuote('private') . ' = ' . $db->Quote(0);

			// category ACL:
			$catOptions = array();
			$catOptions['idOnly'] = true;
			$catOptions['includeChilds'] = true;

			$catModel = ED::model('Categories');
			$catIds = $catModel->getCategoriesTree(0, $catOptions);

			// if there is no categories return, means this user has no permission to view all the categories.
			// if that is the case, just return empty array.
			if (! $catIds) {
				return array();
			}

			$query .= " and b.`category_id` IN (" . implode(',', $catIds) . ")";
		}

		// Check for category language
		$filterLanguage = JFactory::getApplication()->getLanguageFilter();
		if ($filterLanguage) {
			$query .= ' AND '.ED::getLanguageQuery('c.language');
		}

		$query .= ' GROUP BY b.' . $db->nameQuote('id');
		$query .= ' ORDER BY b.' . $db->nameQuote('replied') . ' DESC';

		$this->_total = $this->_getListCount($query);

		$this->_pagination = ED::getPagination($this->_total, $limitstart, $limit);

		$this->_data = $this->_getList($query, $limitstart , $limit);

		return $this->_data;
	}

	/**
	 * Retrieve replies from a specific user
	 *
	 * @since	4.1.6
	 * @access	public
	 */
	public function getRepliesFromUser($userId, $ordering = '')
	{
		$db = $this->db;
		$date = ED::date();

		$respectAnonymous = ($this->my->id && $this->my->id == $userId) ? false : true;
		$respectPrivacy = ($this->my->id == $userId) ? false : true;
		$includeCluster = false;

		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');

		$query = 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', b.`created`) as `noofdays`, ';
		$query .= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', b.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', b.`created`) as `timediff`,';
		$query .= ' a.`id`, b.`title`, b.`alias`, b.`created`, b.`modified`, b.`replied`,b.`legacy`,';
		$query .= ' b.`content`, b.`category_id`, b.`published`, b.`ordering`, b.`vote`, a.`hits`, b.`islock`,';
		$query .= ' b.`featured`, b.`isresolve`, b.`isreport`, b.`user_id`, a.`parent_id`,';
		$query .= ' b.`user_type`, b.`poster_name`, b.`poster_email`, b.`num_likes`,';
		$query .= ' b.`num_negvote`, b.`sum_totalvote`, b.`answered`, a.`thread_id`,';
		$query .= ' b.`post_status`, b.`post_type`, pt.`title` AS `post_type_title`,pt.`suffix` AS `post_type_suffix`,';
		$query .= ' count(a.id) as `num_replies`,';
		$query .= ' c.`title` as `category`, b.`password`';
		$query .= ' FROM ' . $db->nameQuote('#__discuss_posts') . ' AS a ';
		$query .= ' INNER JOIN ' . $db->nameQuote('#__discuss_thread') . ' AS b ';
		$query .= ' ON a.' . $db->nameQuote('parent_id') . ' = b.' . $db->nameQuote('post_id');
		$query .= ' LEFT JOIN ' . $db->nameQuote('#__discuss_category') . ' AS c';
		$query .= ' ON c.' . $db->nameQuote('id') . ' = b.' . $db->nameQuote('category_id');
		$query .= ' LEFT JOIN ' . $db->nameQuote('#__discuss_post_types') . ' AS pt';
		$query .= ' ON b.`post_type` = pt.' . $db->nameQuote('alias');
		$query .= ' WHERE a.' . $db->nameQuote('user_id') . ' = ' . $db->Quote($userId);
		$query .= ' AND a.' . $db->nameQuote('published') . ' = ' . $db->Quote(1);
		$query .= ' AND a.`parent_id` != ' . $db->Quote('0');

		if ($respectAnonymous) {
			$query .= ' AND a.`anonymous` = 0';
		}

		if (!$includeCluster) {
			$query .= ' AND b.`cluster_id` = 0';
		}

		$query .= ' AND b.' . $db->nameQuote('published') . ' = ' . $db->Quote(1);

		if ($respectPrivacy) {

			// category ACL:
			$catOptions = array();
			$catOptions['idOnly'] = true;
			$catOptions['includeChilds'] = true;

			$catModel = ED::model('Categories');
			$catIds = $catModel->getCategoriesTree(0, $catOptions);

			// if there is no categories return, means this user has no permission to view all the categories.
			// if that is the case, just return empty array.
			if (!$catIds) {
				return array();
			}

			$query .= " and b.`category_id` IN (" . implode(',', $catIds) . ")";

		}

		$query .= ' GROUP BY b.`id`';

		if (!empty($ordering)) {

			switch ($ordering) {
				case 'latest':
					$query .= ' ORDER BY a.`created` DESC';
					break;
				case 'lastreplied':
					$query .= ' ORDER BY b.`replied` DESC';
					break;
			}
		}

		$this->_total = $this->_getListCount($query);
		$this->_pagination = DiscussHelper::getPagination($this->_total, $limitstart, $limit);
		$this->_data = $this->_getList($query, $limitstart , $limit);

		return $this->_data;
	}


	public function getUserReplies($postId, $excludeLastReplyUser	= false)
	{
		$db = ED::db();

		$repliesUser = '';
		$lastReply = '';

		if ($excludeLastReplyUser) {
			$query	= 'SELECT `id`, `user_id`, `poster_name`, `poster_email` FROM `#__discuss_posts` where `published` = 1 and `parent_id` = ' . $db->Quote($postId) ;
			$query	.= ' ORDER BY `id` DESC LIMIT 1';

			$db->setQuery($query);
			$lastReply	= $db->loadAssoc();
		}

		if (isset($lastReply['id'])) {
			$query = 'SELECT DISTINCT `user_id`, `poster_email`, `poster_name` FROM `#__discuss_posts`';
			$query .= ' WHERE `published` = ' . $db->Quote('1');
			$query .= ' and `parent_id` = ' . $db->Quote($postId);
			$query .= ' and `id` != ' . $db->Quote($lastReply['id']);

			if (!empty($lastReply['user_id'])) {
				$query .= ' and `user_id` != ' . $db->Quote($lastReply['user_id']);
			}

			if (!empty($lastReply['poster_email'])) {
				$query .= ' and `poster_email` != ' . $db->Quote($lastReply['poster_email']);
			}

			$query .= ' ORDER BY `id` DESC';
			$query .= ' LIMIT 5';

			$db->setQuery($query);

			$repliesUser = $db->loadObjectList();
		}

		return $repliesUser;
	}

	public function getCategoryId($postId)
	{
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT ' . $db->nameQuote('category_id');
		$query[] = 'FROM ' . $db->nameQuote('#__discuss_posts');
		$query[] = 'WHERE ' . $db->nameQuote('id') . '=' . $db->Quote($postId);
		$query = implode(' ' , $query);

		$db->setQuery($query);
		$categoryId	= $db->loadResult();

		return $categoryId;
	}

	/**
	 * Retrieves a list of user id's that has participated in a discussion
	 *
	 * @access	public
	 * @param	int $postId		The main discussion id.
	 * @return	Array	An array of user id's.
	 *
	 **/
	public function getParticipants($postId)
	{
		$db = ED::db();

		$query = 'SELECT DISTINCT `user_id` FROM `#__discuss_posts`';
		$query .= ' WHERE `parent_id` = ' . $db->Quote($postId);

		$db->setQuery($query);
		$participants = $db->loadResultArray();

		return $participants;
	}

	/**
	 * Determines if the post has attachments
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function hasAttachments($postId)
	{
		$db = $this->db;

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_attachments') . ' '
				. 'WHERE ' . $db->nameQuote('uid') . '=' . $db->Quote($postId) . ' '
				. 'AND ' . $db->nameQuote('published') . '=' . $db->Quote(1);
		$db->setQuery($query);
		$result	= $db->loadResult();

		return $result;
	}

	public function hasPolls($postId)
	{
		static $cache = array();

		if (!isset($cache[$postId])) {
			$db = ED::db();
			$query = 'SELECT COUNT(DISTINCT(`post_id`)) FROM ' . $db->nameQuote('#__discuss_polls') . ' '
					. 'WHERE ' . $db->nameQuote('post_id') . '=' . $db->Quote($postId);
			$db->setQuery($query);
			$result	= $db->loadResult();

			$cache[$postId] = $result;
		}

		return $cache[$postId];
	}

	/**
	 * When merging posts, we need to update attachments type
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function updateAttachments($postId , $type)
	{
		$db = ED::db();

		$where = $type == 'questions' ? 'replies' : 'questions';

		$query = 'UPDATE ' . $db->nameQuote('#__discuss_attachments') . ' SET ' . $db->nameQuote('type') . '=' . $db->Quote($type);
		$query .= ' WHERE ' . $db->nameQuote('uid') . '=' . $db->Quote($postId) . ' AND ' . $db->nameQuote('type') . '=' . $db->Quote($where);

		$db->setQuery($query);

		$db->query();
	}

	/**
	 * Updates existing posts to a new parent.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function updateNewParent($currentParent, $newParent, $threadId = null)
	{
		$db = ED::db();

		$query = array();

		$query[] = 'UPDATE ' . $db->nameQuote('#__discuss_posts');
		$query[] = 'SET ' . $db->nameQuote('parent_id') . '=' . $db->Quote($newParent);
		$query[] = ', ' . $db->nameQuote('thread_id') . '=' . $db->Quote($threadId);
		$query[] = 'WHERE ' . $db->nameQuote('parent_id') . '=' . $db->Quote($currentParent);

		$query = implode(' ', $query);
		$db->setQuery($query);
		$db->Query();
	}

	/**
	 * Get unread post for particular user
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getUnread($id, $category = 0)
	{

		$excludeCats = ED::getPrivateCategories();

		if (!is_array($category)) {
			$category = array($category);
		}

		$model = ED::model('Categories');

		$childs = array();
		$categoryIds = false;

		if (!empty($category)) {
			foreach ($category as $categoryId) {

				$data = $model->getChildIds($categoryId);

				if ($data) {
					foreach ($data as $childCategory) {
						$childs[] = $childCategory;
					}
				}

				$childs[] = $categoryId;
			}

			$categoryIds = array_diff($childs, $excludeCats);

			if (empty($categoryIds)) {
				return false;
			}
		}

		// Load the user
		$user = ED::user($id);
		$user->load($id);

		$readPosts = $user->posts_read;
		$extraSQL = '';

		if ($readPosts) {
			$readPosts = unserialize($readPosts);
			$extraSQL = ' AND `id` != ' . $this->db->Quote($readPosts[0]);

			if (count($readPosts) > 1) {
				$extraSQL = implode(',', $readPosts);
				$extraSQL = ' AND `id` NOT IN (' . $extraSQL . ')';
			}
		}

		$query = 'SELECT `id` FROM `#__discuss_posts`';
		$query .= ' WHERE `published` = ' . $this->db->Quote(1);
		$query .= ' AND `parent_id` = ' . $this->db->Quote(0);

		if ($categoryIds && !(count($categoryIds) == 1 && empty($categoryIds[0]))) {

			if (count($categoryIds) == 1) {
				$query .= ' AND `category_id` = ' . $this->db->Quote($categoryIds[0]);
			} else {
				$query .= ' AND `category_id` IN (' . implode(',', $categoryIds) .')';
			}
		}

		$query .= ' AND `answered` = ' . $this->db->Quote(0);

		if (!ED::isSiteAdmin() && !ED::isModerator()) {
			$query	.= ' AND `private`=' . $this->db->Quote(0);
		}

		$query .= ' AND `legacy` = ' . $this->db->Quote(0);

		$query .= $extraSQL;

		$this->db->setQuery($query);
		$result = $this->db->loadObjectList();

		return $result;
	}

	/**
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getMostVoted($limit, $options = array())
	{
		$count = isset($limit) ? $limit : 10;

		$includeReplies = isset($options['includeReplies']) ? $options['includeReplies'] : null;

		$db = ED::db();

		$queryExclude = '';
		$excludeCats = ED::getPrivateCategories();

		if (!empty($excludeCats)) {
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		// posts
		$query = 'select a.`isresolve`, a.`id`, a.`user_id`, a.`user_type`, a.`poster_name`, a.`title`, a.`id` as `parent_id`, a.category_id,';
		$query .= ' (select count(1) from `#__discuss_votes` as b1 where b1.`post_id` = a.`id`) as `VotedCnt`, count(c.id) as `num_replies`';
		$query .= ' from `#__discuss_posts` as a ';
		$query .= ' left join `#__discuss_posts` as c on a.`id` = c.`parent_id`';
		$query .= ' 	and c.`published` = 1';
		$query .= ' inner join `#__discuss_votes` as b on a.`id` = b.`post_id`';
		$query .= ' where a.`parent_id` = 0';
		$query .= ' and a.`published` = 1';

		if (!empty($excludeCats)) {
			$query .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query .= ' group by a.`id`';

		// union both posts and replies
		$query .= ' union ';

		// replies
		$query .= ' select c.`isresolve`, a.`id`, a.`user_id`, a.`user_type`, a.`poster_name`, a.`title`, a.`parent_id`, c.category_id, ';
		$query .= ' count(b.id) as `VotedCnt`, 0 as `num_replies`';
		$query .= ' from `#__discuss_posts` as a';
		$query .= '   inner join `#__discuss_posts` as c on a.`parent_id` = c.`id`';
		$query .= '   inner join `#__discuss_votes` as b on a.`id` = b.`post_id`';
		$query .= ' where a.`published` = 1';
		$query .= ' and c.`published` = 1';

		if (!empty($excludeCats)) {
			$query .= ' and c.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		if (!$includeReplies) {
			$query .= ' and a.`parent_id` = 0';
		}

		$query .= ' group by a.`id`';

		// ordring
		$query .= ' order by VotedCnt desc';

		if ($count > 0)
			$query .= ' limit ' . $count ;

		$db->setQuery($query);
		$posts = $db->loadObjectList();

		return $posts;
	}

	/**
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function deleteThread($threadId)
	{
		$db = ED::db();

		$query = "delete from " . $db->nameQuote('#__discuss_thread');
		$query .= " where `id` = " . $db->Quote($threadId);

		$db->setQuery($query);

		return $db->query();
	}

	/**
	 * Get row number of a reply
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getRowNumber($threadId, $replyId)
	{
		$config = ED::config();
		$db = ED::db();

		$pagelimit = $config->get('layout_replies_list_limit');
		$replysort = ED::getDefaultRepliesSorting();

		// we cannot get the row_number correctly when the sorting is base on voted / likes.
		if ($replysort == 'voted' || $replysort == 'likes') {
			return false;
		}

		$sortCondition = '';
		$sortOrderBy = '';

		switch ($replysort) {
			case 'oldest':
				$sortCondition = " and a.`created` >= b.`created`";
				$sortOrderBy = " ORDER BY a.`created` ASC";
				break;
			case 'latest':
				$sortCondition = " and a.`created` <= b.`created`";
				$sortOrderBy = " ORDER BY a.`created` DESC";
				break;
		}

		$query = "select count(1) as `row_number`";
		$query .= " from `#__discuss_posts` as a";
		$query .= " inner join `#__discuss_posts` as b";
		$query .=" 		on a.`thread_id` = b.`thread_id`";
		$query .= "			and a.`thread_id` = " . $db->Quote($threadId);
		$query .= $sortCondition;
		$query .= " where a.`published` = " . $db->Quote(DISCUSS_ID_PUBLISHED);
		$query .= " and a.`id` = " . $db->Quote($replyId);
		$query .= " group by a.`id`";
		$query .= $sortOrderBy;

		$db->setQuery($query);
		$row = $db->loadResult();

		// since the query count on the parent post as well. We need to manually adjust the row number by minus off 1
		// this is because when order by ascending, the parent post always at the 1st.
		if ($replysort == 'oldest') {
			$row = $row - 1;
		}

		return $row;
	}

	/**
	 * Method used to get user's posts for GDPR download.
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getPostsGDPR($userid, $options = array())
	{
		$db = ED::db();

		$limit = isset($options['limit']) ? $options['limit'] : 20;
		$exclude = isset($options['exclude']) ? $options['exclude'] : array();

		if ($exclude && !is_array($exclude)) {
			$exclude = ED::makeArray($exclude);
		}

		$query = "select a.*";
		$query .= " from `#__discuss_posts` as a";
		$query .= " where a.`user_id` = " . $db->Quote($userid);
		$query .= " and a.`published` = 1";

		if ($exclude) {
			$query .= " and a.`id` NOT IN (" . implode(',', $exclude) . ")";
		}

		$query .= " LIMIT " . $limit;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		$posts = array();

		if ($results) {
			foreach ($results as $row) {
				$post = ED::post($row);
				$posts[] = $post;
			}
		}

		return $posts;
	}

	/**
	 * Method used to get user's comments for GDPR download.
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getCommentsGDPR($userid, $options = array())
	{
		$db = ED::db();

		$limit = isset($options['limit']) ? $options['limit'] : 20;
		$exclude = isset($options['exclude']) ? $options['exclude'] : array();

		if ($exclude && !is_array($exclude)) {
			$exclude = ED::makeArray($exclude);
		}

		$query = "select *";
		$query .= " from `#__discuss_comments`";
		$query .= " where `user_id` = " . $db->Quote($userid);
		$query .= " and `published` = 1";

		if ($exclude) {
			$query .= " and `id` NOT IN (" . implode(',', $exclude) . ")";
		}

		$query .= " LIMIT " . $limit;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}
}
