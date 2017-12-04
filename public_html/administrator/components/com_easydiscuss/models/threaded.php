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
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname( __FILE__ ) . '/model.php');

class EasyDiscussModelThreaded extends EasyDiscussAdminModel
{
	public $total = null;

	public function __construct()
	{
		parent::__construct();

		// Get the posts limit
		$limit = $this->app->getUserStateFromRequest('com_easydiscuss.posts.limit', 'limit', $this->app->getCfg('list_limit') , 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Allows caller to retrieve posts
	 *
	 * @since	4.0.9
	 * @access	public
	 */
	public function getPosts($options = array())
	{
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT a.*';
		$query[] = 'FROM ' . $db->qn('#__discuss_posts') . ' AS a';

		$filter = isset($options['filter']) ? $options['filter'] : '';
		$category = isset($options['category']) ? $options['category'] : '';

		$where = array();

		// We only want to fetch the parent if needed
		if (isset($options['questions']) && $options['questions']) {
			$where[] = 'a.`parent_id` = ' . $db->Quote('0');
		}

		// We only want to fetch the parent if needed
		if (isset($options['replies']) && $options['replies']) {
			$where[] = 'a.`parent_id` != ' . $db->Quote('0');
		}

		// Render only pending posts
		if (isset($options['pending']) && $options['pending']) {
			$where[] = 'a.' . $db->qn('published') . '=' . $db->Quote(DISCUSS_ID_PENDING);
		} else {
			$where[] = 'a.' . $db->qn('published') . '!=' . $db->Quote(DISCUSS_ID_PENDING);
		}

		// Determines if we need to filter posts by category
		if ($category) {
			$where[] = 'a.' . $db->qn( 'category_id' ) . '=' . $db->Quote($category);
		}

		// Filter posts that are published
		if ($filter == 'published') {
			$where[] = $db->qn('a.published') . '=' . $db->Quote('1');
		}

		// Filter posts that are unpublished
		if ($filter == 'unpublished') {
			$where[] = $db->qn('a.published') . '=' . $db->Quote('0');
		}

		// Search queries
		$search = isset($options['search']) ? $options['search'] : '';
		$search = $db->getEscaped(trim(JString::strtolower($search)));

		// Try to see if we are trying search for specific sections
		$search = $this->getSearchFragments($search);

		// Get the ordering and order direction of posts
		$stateKey = isset($options['stateKey']) ? $options['stateKey'] : 'posts';

		if ($search->type == 'standard') {

			if ($search->query && $stateKey == 'posts') {
				$where[] = ' LOWER( a.`title` ) LIKE ' . $db->Quote('%' . $search->query . '%');
			}

			if ($search->query && $stateKey == 'replies') {
				$where[] = ' LOWER(a.`content`) LIKE ' . $db->Quote('%' . $search->query . '%');
			}

			if ($search->query && $stateKey == 'pending') {
				$where[] = ' (LOWER(a.`title`) LIKE ' . $db->Quote('%' . $search->query . '%') . ' OR LOWER(a.`content`) LIKE ' . $db->Quote('%' . $search->query . '%') . ')';
			}
		} else {
			if ($search->type == 'author') {
				$search->query = trim($search->query);
				$isUserId = (int) $search->query !== 0;

				if ($isUserId) {
					$where[] = 'a.`user_id`=' . $db->Quote($search->query);
				} else {

					// Search by username or name. Instead of joining the table, we just fire another query
					// to get the id's to prevent all those collation and performance issues
					$userQuery = 'SELECT `id` FROM `#__users` WHERE (`name` LIKE ' . $db->Quote('%' . $search->query . '%') . ' OR `username` LIKE ' . $db->Quote('%' . $search->query . '%') . ')';
					$db->setQuery($userQuery);
					$userIds = $db->loadColumn();

					if ($userIds) {
						$where[] = 'a.`user_id` IN(' . implode(',', $userIds) . ')';
					}
				}
			}
		}

		$where = count($where) ? ' WHERE ' . implode( ' AND ', $where ) : '' ;

		$query[] = $where;


		$ordering = $this->app->getUserStateFromRequest('com_easydiscuss.' . $stateKey . '.filter_order', 'filter_order', 'a.id', 'cmd');
		$direction = $this->app->getUserStateFromRequest('com_easydiscuss.' . $stateKey . '.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

		$query[] = 'ORDER BY ' . $ordering . ' ' . $direction . ', ordering';

		// Glue the queries together.
		$query = implode(' ', $query);

		// Get the total number of items
		$limitQuery = str_ireplace('a.*', 'COUNT(1)', $query);
		$db->setQuery($limitQuery);
		$total = (int) $db->loadResult();
		$this->total = $total;

		// Get the pagination
		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');
		if ($limit) {
			$query .= ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$db->setQuery($query);
		$items = $db->loadObjectList();

		return $items;
	}

	/**
	 * Allows caller to retrieve the number of pending items on the site
	 *
	 * @since	4.0.10
	 * @access	public
	 */
	public function getPendingCount()
	{
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__discuss_posts');
		$query[] = 'WHERE ' . $db->qn('published') . '=' . $db->Quote(DISCUSS_ID_PENDING);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$total = $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves the pagination for the posts
	 *
	 * @since	4.0.10
	 * @access	public
	 */
	public function getPagination()
	{
		jimport('joomla.html.pagination');

		$pagination = ED::getPagination($this->total, $this->getState('limitstart'), $this->getState('limit'));

		return $pagination;
	}

	/**
	 * Method to publish or unpublish posts
	 *
	 * @since	4.0
	 * @access	public
	 * @return	array
	 */
	public function publishThread($ids = array(), $publish = 1)
	{
		if (!$ids) {
			return false;
		}

		$db = ED::db();
		$ids = implode(',', $ids);

		$query = 'UPDATE ' . $db->nameQuote('#__discuss_thread') . ' '
				. 'SET ' . $db->nameQuote('published') . '=' . $db->Quote($publish) . ' '
				. 'WHERE ' . $db->nameQuote('post_id') . ' IN (' . $ids . ')';

		$db->setQuery($query);

		if (!$db->query()) {
			$this->setError($db->getErrorMsg());

			return false;
		}

		return true;
	}

	/**
	 * Retrieves all posts from the site
	 *
	 * @since	4.0.9
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAllPosts()
	{
		$db = ED::db();

		$query = 'SELECT IFNULL(a.id, b.id) AS pid, b.* FROM `#__discuss_posts` AS a ' .
				 '	RIGHT JOIN #__discuss_posts AS b ' .
				 '	ON a.id = b.parent_id' .
				 ' ORDER BY a.created, b.created';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
}
