<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussModelUsers extends EasyDiscussAdminModel
{
	public $_total = null;
	public $_pagination = null;
	public $_data = null;

	public function __construct()
	{
		parent::__construct();

		$app = JFactory::getApplication();
		$limit = $app->getUserStateFromRequest('com_easydiscuss.users.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limit = ED::getLimitValue($limit);

		$limitstart	= $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Determines if a user exceeded their moderation threshold so they won't be moderated again.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function exceededModerationThreshold($userId, $isQuestion, $limit)
	{
		$db = $this->db;

		$query = array();
		$query[] = 'SELECT COUNT(1) as `CNT` FROM `#__discuss_posts` AS a';
		$query[] = ' WHERE a.`user_id` = ' . $db->Quote($userId);
		$query[] = ' AND a.`published` = ' . $db->Quote('1');

		$checkParentIdQuery = ' AND a.`parent_id` = ' . $db->Quote('0');

		if (!$isQuestion) {
			$checkParentIdQuery = ' AND a.`parent_id` != ' . $db->Quote('0');
		}

		$query[] = $checkParentIdQuery;

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		if ($result >= $limit) {
			return true;
		}

		return false;
	}

	/**
	 * Get the latest user that registered on the site.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getLatestUser()
	{
		$db		= ED::db();

		$query		= array();
		$query[]	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__users' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'block' ) . '=' . $db->Quote( 0 );
		$query[]	= 'ORDER BY ' . $db->nameQuote( 'id' ) . ' DESC';
		$query[]	= 'LIMIT 1';

		$query		= implode( ' ' , $query );
		$db->setQuery( $query );

		$id			= $db->loadResult();

		return $id;
	}

	/**
	 * Get logged in users from the site.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getOnlineUsers()
	{
		$jConfig = ED::jConfig();
		$lifespan = $jConfig->getValue('lifetime');
		$online = time() - ($lifespan * 60);
		$sharedSess = $jConfig->get('shared_session', 0);


		$db = $this->db;
		$query = 'SELECT a.* FROM ' . $db->nameQuote('#__discuss_views') . ' AS a ';
		$query .= ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS b ';
		$query .= ' ON a.' . $db->nameQuote('user_id') . ' = b.' . $db->nameQuote('id');
		$query .= ' INNER JOIN ' . $db->nameQuote('#__session') . ' AS c ';
		$query .= ' ON c.' . $db->nameQuote('userid') . ' = b.' . $db->nameQuote('id');
		$query .= ' WHERE a.' . $db->nameQuote('user_id') . ' !=' . $db->Quote(0);
		$query .= ' AND c.' . $db->nameQuote('time') . ' >= ' . $db->Quote($online);

		if (!$sharedSess) {
			$query .= ' AND c.' . $db->nameQuote('client_id') . ' = ' . $db->Quote('0');
		}

		$query .= ' GROUP BY a.' . $db->nameQuote('user_id');

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if (!$result) {
			return false;
		}

		//lets preload users
		$userIds = array();

		foreach ($result as $res) {
			$userIds[] = $res->user_id;
		}

		$userIds = array_unique($userIds);

		ED::user($userIds);

		$users	= array();

		foreach ($result as $res) {
			$profile = ED::user($res->user_id);
			$users[] = $profile;
		}

		return $users;
	}

	/**
	 * Get page viewers
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPageViewers($hash)
	{
		$jConfig = ED::jConfig();
		$lifespan = $jConfig->getValue('lifetime');
		$online = time() - ($lifespan * 60);

		$db	= $this->db;
		$query = array();

		$query[] = 'SELECT a.* FROM ' . $db->nameQuote('#__discuss_views') . ' AS a ';
		$query[] = 'INNER JOIN ' . $db->nameQuote('#__users') . ' AS b ';
		$query[] = 'ON a.' . $db->nameQuote('user_id') . ' = b.' . $db->nameQuote('id');
		$query[] = 'INNER JOIN ' . $db->nameQuote('#__session') . ' AS c ';
		$query[] = 'ON c.' . $db->nameQuote('userid') . ' = b.' . $db->nameQuote('id');
		$query[] = 'WHERE ' . $db->nameQuote('hash') . '=' . $db->Quote($hash);
		$query[] = 'AND a.' . $db->nameQuote('user_id') . ' != ' . $db->Quote(0);
		$query[] = 'AND c.' . $db->nameQuote('time') . ' >= ' . $db->Quote( $online );
		$query[] = 'AND c.' . $db->nameQuote('client_id') . ' = ' . $db->Quote('0');
		$query[] = 'GROUP BY a.' . $db->nameQuote('user_id');

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result	= $db->loadObjectList();

		if (!$result) {
			$this->totalViewers = 0;

			return false;
		}

		//lets preload users
		$userIds = array();

		foreach ($result as $res) {
			$userIds[] = $res->user_id;
		}

		$userIds = array_unique($userIds);
		ED::user($userIds);

		$users = array();
		$total = 0;

		foreach ($result as $res) {
			$profile = ED::user($res->user_id);
			$users[] = $profile;

			$total++;
		}

		$this->totalViewers = $total;

		return $users;
	}

	/**
	 * Get total number of page viewers.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getTotalPageViewers()
	{
		return $this->totalViewers;
	}

	/**
	 * Get total number of guests that is viewing the site.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTotalGuests()
	{
		$db = $this->db;
		$jconfig = ED::jconfig();
		$lifespan = $jconfig->getValue('lifetime');
		$online = time() - ($lifespan * 60);

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__session');
		$query[] = 'WHERE ' . $db->nameQuote('guest') . '=' . $db->Quote(1);
		$query[] = 'AND ' . $db->nameQuote('time') . '>=' . $db->Quote($online);
		$query = implode(' ', $query);
		$db->setQuery($query);

		$total = $db->loadResult();

		return $total;
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal($search = null)
	{
		$db = ED::db();

		// Lets load the content if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery(true, $search);
			$db->setQuery($query);

			//$this->_total = $this->_getListCount($query);
			$this->_total = $db->loadResult();
		}

		return $this->_total;
	}

	public function getTotalUsers()
	{
		$db = ED::db();
		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__users') . ' AS u';
		$query .= ' WHERE u.' . $db->nameQuote( 'block' ) . '=' . $db->Quote( 0 );

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
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
			$this->_pagination = ED::getPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPaginationFrontend($search = '')
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			$this->_pagination = ED::getPagination($this->getTotal($search), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the users
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery($isTotalCnt = false, $name = '', $usePagination = true, $exclusions = array())
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere($name, $exclusions);
		$orderby = $this->_buildQueryOrderBy();
		$db = ED::db();

		if ($isTotalCnt) {
			$query = 'SELECT COUNT(u.`id`) from `#__users` as u ';
			$query .= 'LEFT JOIN ' . $db->nameQuote('#__discuss_users') . ' AS d ON d.`id`= u.`id`';
			$query .= $where;
		} 

		if (!$isTotalCnt) {

			$query = "SELECT u.`id`, u.`name`, u.`username`, u.`email`, u.`registerDate`, u.`lastvisitDate`, u.`params`, u.`block`,";
			$query .= " d.`nickname`, d.`avatar`, d.`description`, d.`url`, d.`alias`";
			$query .= " FROM (";

			// wrap the main query as subquery:
			$query .= " select u.id from #__users as u";
			$query .= "    LEFT JOIN `#__discuss_users` AS d ON d.`id` = u.`id`";
			$query .= $where;
			$query .= $orderby;

			// set limit inside wrapper
			$limit = (int) $this->getState('limit');
			$limitstart = (int) $this->getState('limitstart');

			if ($usePagination) {
				$query .= " LIMIT $limitstart, $limit";
			} else {
				$query .= " LIMIT $limit";
			}
			// end

			$query .= ") as x";
			$query .= " inner join `#__users` as u on u.`id` = x.`id`";
			$query .= " LEFT JOIN `#__discuss_users` AS d ON d.`id` = u.`id`";
			$query .= $orderby;
		}

		return $query;
	}

	public function _buildQueryWhere($name = null, $exclusions = array())
	{
		$app = JFactory::getApplication();
		$db = ED::db();

		$config = ED::config();

		// Used in administration
		$filter_state = $app->getUserStateFromRequest('com_easydiscuss.users.filter_state', 'filter_state', '', 'word');
		$search = $app->getUserStateFromRequest('com_easydiscuss.users.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EDJString::strtolower($search)));

		$where = array();

		$where[] = 'u.`block` = ' . $db->Quote(0);

		// Backend user searching
		if (ED::isFromAdmin() && $search) {
			$where[] = ' (LOWER(`name`) LIKE ' . $db->Quote('%' . $search . '%') . ') OR (LOWER(`username`) LIKE ' . $db->Quote('%' . $search . '%') . ')';
		} 

		// Frontend user searches
		if (!ED::isFromAdmin() && $name) {
			$displayname = $config->get('layout_nameformat');

			if ($displayname == 'name') {
				$where[] = ' LOWER(`name`) LIKE ' . $db->Quote('%' . $name . '%');
			}

			if ($displayname == 'username') {
				$where[] = ' LOWER(`username`) LIKE ' . $db->Quote('%' . $name . '%');
			}

			if ($displayname == 'nickname') {
				$where[] = ' LOWER(d.`nickname`) LIKE ' . $db->Quote('%' . $name . '%');
			}
		}

		if ($exclusions) {
			$where[] = ' u.`id` NOT IN(' . implode(',', $exclusions) . ')';
		}

		$where = (count($where) ? ' WHERE ' . implode('AND', $where) : '');

		return $where;
	}


	public function _buildQueryOrderBy()
	{
		$app = JFactory::getApplication();

		$filter_order = $app->getUserStateFromRequest( 'com_easydiscuss.users.filter_order', 		'filter_order', 	'name', 'cmd' );
		$filter_order_Dir = $app->getUserStateFromRequest( 'com_easydiscuss.users.filter_order_Dir',	'filter_order_Dir',		'asc', 'word' );

		$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Retrieves the list of users from the site
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getData($name = '', $exclusions = array())
	{
		$db = ED::db();

		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {

			if (!is_array($exclusions)) {
				$exclusions = str_replace(' ', '', $exclusions);

				if ($exclusions) {
					$exclusions = explode(',', $exclusions);
				}

				if (!$exclusions) {
					$exclusions = array();
				}
			}

			$query = $this->_buildQuery(false, $name, true, $exclusions);
			$db->setQuery($query);
			$result = $db->loadObjectlist();

			$this->_data = $result;
		}

		return $this->_data;
	}

	/**
	 * Method to get users item data
	 *
	 * @access public
	 * @return array
	 */
	public function getUsers($usePagination = true)
	{
		$db = ED::db();

		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery(false, '', $usePagination);
			$db->setQuery($query);
			$this->_data = $db->loadObjectlist();
		}

		return $this->_data;
	}

	/**
	 * Method to get moderators from a category
	 *
	 * @access public
	 * @return array
	 */
	public function getModerators($categoryId)
	{
		$db = ED::db();
		$app = JFactory::getApplication();

		// set limit inside wrapper
		$limit = (int) $this->getState('limit');
		$limitstart = (int) $this->getState('limitstart');

		// Used in administration
		$filter_state = $app->getUserStateFromRequest('com_easydiscuss.users.filter_state', 'filter_state', '', 'word');
		$search = $app->getUserStateFromRequest('com_easydiscuss.users.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EDJString::strtolower($search)));

		// get category moderator groups.
		$model = ED::model('category');
		$groups = $model->getAssignedModerator($categoryId, 'group');

		// stop here.
		if (!$groups) {
			$this->_total = 0;
			return array();
		}

		$gids = '';
		foreach ($groups as $group) {
			$gids .= $gids ? ',' . $db->Quote($group->content_id) : $db->Quote($group->content_id);
		}


		//build where condition
		$where = " where u.`block` = " . $db->Quote(0);
		if ($search) {
			$where .= " and ((LOWER(u.`name`) LIKE " . $db->Quote('%' . $search . '%') . ") OR (LOWER(u.`username`) LIKE " . $db->Quote('%' . $search . '%') . "))";
		}
		$where .= " and um.`group_id` in (" . $gids . ")";

		// build order by
		$orderby = $this->_buildQueryOrderBy();

		// main query
		$query = "SELECT u.`id`, u.`name`, u.`username`, u.`email`, u.`registerDate`, u.`lastvisitDate`, u.`params`, u.`block`";
		$query .= " FROM (";
		// wrap the main query as subquery:
		$query .= " select distinct u.`id` from `#__users` as u";
		$query .= " inner join `#__user_usergroup_map` as um on u.`id` = um.`user_id`";
		$query .= $where;
		$query .= $orderby;
		$query .= " LIMIT $limitstart, $limit";
		$query .= ") as x";

		$query .= " inner join `#__users` as u on u.`id` = x.`id`";
		$query .= $orderby;

		// echo $query;
		// exit;

		// lets get the total records count used in pagination.
		$cntQuery = "select count(distinct(id))";
		$cntQuery .= " from `#__users` as u";
		$cntQuery .= " inner join `#__user_usergroup_map` as um on u.`id` = um.`user_id`";
		$cntQuery .= $where;

		$db->setQuery($cntQuery);
		$this->_total = $db->loadResult();

		$users = array();

		if ($this->_total > 0) {
			$db->setQuery($query);
			$users = $db->loadObjectList();
		}

		return $users;
	}

	/**
	 * Search for users
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function search($search = '', $excludeUsers = array())
	{
		$db = $this->db;
		$config = ED::config();

		// Determine which field to use
		$field = $config->get('layout_nameformat') == 'name' ? 'name' : 'username';

		$query = 'SELECT * FROM ' . $db->qn('#__users');
		$query .= ' WHERE ' . $db->qn($field) . ' LIKE(' . $db->quote('%' . $search . '%') . ')';
		$query .= ' AND ' . $db->qn('block') . ' = ' . $db->quote(0);

		// Normalize the exclusion to ensure that the exclusion is an array
		if (!is_array($excludeUsers) && $excludeUsers) {
			$excludeUsers = array($excludeUsers);
		}

		if ($excludeUsers) {
			$query .= ' AND ' . $db->qn('id') . ' NOT IN(';

			foreach ($excludeUsers as $id) {
				$query .= $db->Quote($id);

				if (next($excludeUsers) !== false) {
					$query .= ',';
				}
			}

			$query .= ')';
		}

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves a list of user data based on the given ids.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getUsersMeta($ids = array())
	{
		$db = ED::db();

		static $const = array();

		$loaded = array();
		$new = array();

		if (!empty($ids)) {
			foreach ($ids as $id) {
				if (is_numeric($id)) {
					if (isset($const[$id])) {
						$loaded[]	= $const[$id];
					} else {
						$new[]	= $id;
					}
				}
			}
		}

		// New ids detected. lets load the users data
		if ($new) {

			foreach ($new as $id) {
				$const[$id] = false;
			}

			$query = "select u.*,";
			$query .= " e.`id` as `ed_id`, e.`nickname`, e.`avatar`,";
			$query .= " e.`description`, e.`url`, e.`params` as `ed_params`, e.`alias`, e.`points`,";
			$query .= " e.`latitude`, e.`longitude`, e.`location`, e.`signature`, e.`edited`, e.`posts_read`, e.`site`, e.`auth`";
			// $query .= ', (select count(1) from  `#__discuss_posts` as p1 where p1.`user_id` = u.`id` and p1.`parent_id` = 0 and p1.`published` = 1) as `numPostCreated`';
			// $query .= ', (select count(1) from  `#__discuss_posts` as p2 where p2.`user_id` = u.`id` and p2.`parent_id` != 0 and p2.`published` = 1) as `numPostAnswered`';
			$query .= " from `#__users` as u";
			$query .= " left join `#__discuss_users` as e ON u.`id` = e.`id`";

			if (count($new) > 1) {
				$query .= " where u.`id` IN (" . implode(',', $new) . ")";
			} else {
				$query .= " where u.`id` = " . $new[0];
			}

			$db->setQuery($query);
			$users = $db->loadObjectList();

			if ($users) {
				foreach ($users as $user) {
					$loaded[] = $user;
					$const[$user->id] = $user;
				}
			}
		}

		$return = array();

		if ($loaded) {
			foreach ($loaded as $user) {
				if (isset($user->id)) {
					$return[] = $user;
				}
			}
		}

		return $return;
	}


	public function getAllEmails( $exclusion = array(), $force = false )
	{
		$db 	= ED::db();
		$query	= 'SELECT `email` FROM ' . $db->nameQuote( '#__users' );

		if( !$force )
		{
			$query .= ' WHERE `block` = 0 ';
		}

		if( !is_array( $exclusion ) )
		{
			$exclusion	= array( $exclusion );
		}

		if( !empty( $exclusion ) )
		{
			$query	.= ' AND ' . $db->nameQuote( 'email' ) . ' NOT IN (';
			for( $i = 0; $i < count( $exclusion ); $i++ )
			{
				$query	.= $db->Quote( $exclusion[ $i ] );

				if( next( $exclusion ) !== false )
				{
					$query	.= ',';
				}
			}
			$query	.= ')';
		}

		$db->setQuery( $query );

		$emails = $db->loadResultArray();

		return $emails;
	}

	/**
	 * Retrieves the total number of posts a user created
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTotalQuestions($userId = null)
	{
		// If user id is not provided, we just try to get it from the current logged in user.
		$userId = JFactory::getUser($userId)->id;

		if (!$userId) {
			return 0;
		}

		$my = JFactory::getUser();

		$respectAnonymous = $my->id == $userId ? false : true;

		$db = $this->db;
		$query = "SELECT COUNT(1) FROM `#__discuss_posts` WHERE `parent_id`=" . $db->Quote(0);
		$query .= " AND `user_id`=" . $db->Quote($userId);
		$query .= " AND `published`=" . $db->Quote(1);
		if ($respectAnonymous) {
			$query .= " and `anonymous` = 0";
		}

		$db->setQuery($query);

		$total = $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves the total number of posts a user created
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTotalReplies($userId = null, $options = array())
	{
		// If user id is not provided, we just try to get it from the current logged in user.
		$userId = JFactory::getUser($userId)->id;

		if (!$userId) {
			return 0;
		}

		$ignoreCategoryACL = isset($options['ignoreCategoryACL']) ? $options['ignoreCategoryACL'] : false;
		$respectAnonymous = ($this->my->id && $this->my->id == $userId) ? false : true;
		$respectPrivacy = ($this->my->id == $userId) ? false : true;

		$includeCluster = false;

		$db = $this->db;
		$query 	= 'SELECT COUNT(a.`id`) ';
		$query	.= ' FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' AS a ';
		$query	.= ' INNER JOIN ' . $db->nameQuote( '#__discuss_thread' ) . ' AS b ';
		$query	.= ' ON a.' . $db->nameQuote( 'thread_id' ) . ' = b.' . $db->nameQuote( 'id' );
		$query	.= '     AND a.' . $db->nameQuote( 'id' ) . ' != b.' . $db->nameQuote( 'post_id' );

		$query 	.= ' WHERE a.`user_id` = ' . $db->Quote($userId);

		if ($respectAnonymous) {
			$query 	.= ' AND a.`anonymous` = 0';
		}

		$query	.= ' AND b.' . $db->nameQuote( 'published' ) . ' = ' . $db->Quote( 1 );

		if ($this->my->id != $userId) {
			$query	.= ' AND b.' . $db->nameQuote('private') . ' = ' . $db->Quote(0);
		}

		if (!$includeCluster) {
			$query .= ' AND b.`cluster_id` = 0';
		}

		if (!$ignoreCategoryACL && $respectPrivacy) {

			// category ACL:
			$catOptions = array();
			$catOptions['idOnly'] = true;
			$catOptions['includeChilds'] = true;

			$catModel = ED::model('Categories');
			$catIds = $catModel->getCategoriesTree(0, $catOptions);

			// if there is no categories return, means this user has no permission to view all the categories.
			// if that is the case, just return empty array.
			if (! $catIds) {
				return 0;
			}

			$query .= " and b.`category_id` IN (" . implode(',', $catIds) . ")";

		}

		// echo $query;exit;

		$db->setQuery($query);

		$total = $db->loadResult();

		// Return 0 if there is no replies found.
		if (!$total) {
			return 0;
		}

		return $total;
	}

	/**
	 * Generates the posts graph for the user
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPostsGraph($userId)
	{
		// Get dbo
		$db = ED::db();

		// Get the past 7 days
		$dates = array();

		for ($i = 0 ; $i < 7; $i++) {

			$date = JFactory::getDate('-' . $i . ' day');
			$dates[] = $date->format('Y-m-d');
		}


		// Reverse the dates
		$dates = array_reverse($dates);

		// Prepare the main result
		$result = new stdClass();
		$result->dates = $dates;
		$result->count = array();

		$i = 0;
		foreach ($dates as $date) {

			$query   = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__discuss_thread');
			$query[] = 'WHERE DATE_FORMAT(' . $db->quoteName('created') . ', GET_FORMAT(DATE, "ISO")) =' . $db->Quote($date);
			$query[] = 'AND ' . $db->quoteName('user_id') . '=' . $db->Quote($userId);
			$query[] = 'AND ' . $db->quoteName('published') . '=' . $db->Quote(1);

			$query = implode(' ', $query);

			$db->setQuery($query);
			$total = $db->loadResult();

			$result->count[$i] = $total;

			$i++;
		}

		return $result;
	}

	/**
	 * Allow caller to retrieve a list of users ordered by specific post count
	 *
	 * @since	4.0.16
	 * @access	public
	 */
	public function getTopUsers($options = array())
	{
		$db	= ED::db();

		$count = isset($options['count']) ? $options['count'] : false;
		$order = isset($options['order']) ? $options['order'] : 'points';
		$exclude = isset($options['exclude']) ? $options['exclude'] : false;

		$exclusion ='';

		if ($exclude) {
			$exclusion = 'AND a.`id` NOT IN(' . implode(', ',$exclude) . ') ';
		}

		$duration = isset($options['duration']) ? $options['duration'] : 0;
		$durationQuery = '';

		if ($duration && $order == 'posts') {
			$date = ED::date();
			$durationQuery = 'AND b.`created` >= DATE_SUB(' . $db->Quote($date->toSql()) . ', INTERVAL ' . $duration . ' DAY) ';
		}

		if ($order == 'posts') {
			$query	= 'SELECT a.' . $db->nameQuote('id') . ', '
					. '(select count(1) from ' . $db->nameQuote('#__discuss_posts') . ' AS b' . ' '
					. 'where b.' . $db->nameQuote('user_id') . '  = a.' . $db->nameQuote('id') . ' '
					. $durationQuery
					. 'AND b.' . $db->nameQuote('published') . ' = ' . $db->Quote('1') . ') AS ' . $db->nameQuote('total_posts') . ' '
					. 'FROM ' . $db->nameQuote('#__discuss_users') . ' AS a '
					. 'INNER JOIN ' . $db->nameQuote('#__users') . ' AS c '
					. 'ON c.' . $db->nameQuote('id') . '=a.' . $db->nameQuote('id') . ' '
					. 'WHERE c.' . $db->nameQuote('block') . '=' . $db->Quote(0) . ' '
					. $exclusion
					. 'ORDER BY ' . $db->nameQuote('total_posts') . ' DESC '
					. 'LIMIT 0,' . $count;
		}

		if ($order == 'points') {
			$query	= 'SELECT a.' . $db->nameQuote('id') . ', '
					. 'a.' . $db->nameQuote('points') . ' AS ' . $db->nameQuote('total_points') . ' '
					. 'FROM ' . $db->nameQuote('#__discuss_users') . ' AS a '
					. 'INNER JOIN ' . $db->nameQuote('#__users') . ' AS c '
					. 'ON c.' . $db->nameQuote('id') . ' = a.' . $db->nameQuote('id') . ' '
					. 'WHERE c.' . $db->nameQuote('block') . '=' . $db->Quote(0) . ' '
					. $exclusion
					. 'ORDER BY ' . $db->nameQuote('total_points') . ' DESC '
					. 'LIMIT 0,' . $count;
		}

		if ($order == 'answers') {
			$query	= 'SELECT a.' . $db->nameQuote('id') . ', '
					. '(select count(1) from ' . $db->nameQuote('#__discuss_posts') . ' AS b' . ' '
					. 'where b.' . $db->nameQuote('user_id') . '  = a.' . $db->nameQuote('id') . ' '
					. 'AND b.' . $db->nameQuote('answered') . ' = ' . $db->Quote('1') . ' '
					. 'AND b.' . $db->nameQuote('parent_id') . ' != ' . $db->Quote('0') . ' ' . ') AS ' . $db->nameQuote('total_answers') . ' '
					. 'FROM ' . $db->nameQuote('#__discuss_users') . ' AS a '
					. 'INNER JOIN ' . $db->nameQuote('#__users') . ' AS c '
					. 'ON c.' . $db->nameQuote('id') . ' = a.' . $db->nameQuote('id') . ' '
					. 'WHERE c.' . $db->nameQuote('block') . '=' . $db->Quote(0) . ' '
					. $exclusion
					. 'ORDER BY ' . $db->nameQuote('total_answers') . ' DESC '
					. 'LIMIT 0,' . $count;
		}

		$db->setQuery($query);

		$rows = $db->loadObjectList();
		$users = array();

		//preload users;
		if (! $rows) {
			return $users;
		}

		$ids = array();

		foreach ($rows as $row) {
			$ids[] = $row->id;
		}

		ED::user($ids);

		foreach ($rows as $row) {
			$user = ED::user($row->id);

			// Custom properties
			if ($order == 'posts') {
				$user->total_posts 	= $row->total_posts;
			}
			if ($order == 'points') {
				$user->total_points = $row->total_points;
			}
			if ($order == 'answers') {
				$user->total_answers = $row->total_answers;
			}

			$users[] = $user;
		}

		return $users;
	}

	/**
	 * Reset all users' point
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function resetPoints()
	{
		$db	= ED::db();
		$query	= 'UPDATE ' . $db->nameQuote('#__discuss_users')
				. ' SET ' . $db->nameQuote('points') . ' = ' . $db->Quote(0);
		$db->setQuery($query);
		$db->query();
	}


	/**
	 * Retrieve user profile data for GDPR
	 *
	 * @since   4.1
	 * @access  public
	 */
	public function getProfileDataGDPR($options = array())
	{
		$db = ED::db();

		$userId = isset($options['userid']) ? $options['userid'] : null; 
		$limit = isset($options['limit']) ? $options['limit'] : 20;
		$exclusion = isset($options['exclusion']) ? $options['exclusion'] : array();

		if ($exclusion && !is_array($exclusion)) {
			$exclusion = ED::makeArray($exclusion);
		}

		$query = 'SELECT a.`id`, a.`nickname`, a.`description`, a.`params`, a.`points`, a.`location`, a.`signature`'; 
		$query .= ' FROM `#__discuss_users` AS a';
		$query .= ' WHERE a.`id` = ' . $db->Quote($userId);

		if ($exclusion) {
			$query .= ' AND a.`id` NOT IN (' . implode(',', $exclusion) . ')';
		}

		$query .= ' LIMIT ' . $limit;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		if (!$results) {
			return $results;
		}

		$extraDetails = array();

		if ($results) {
			foreach ($results as $row) {
				$user = ED::user($row->id);

				$extraDetails[] = $user;
			}
		}

		return $extraDetails;
	}
}
