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

require_once dirname(__FILE__) . '/model.php';

class EasyDiscussModelTags extends EasyDiscussAdminModel
{
	public $_total = null;
	public $_pagination = null;
	public $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easydiscuss.tags.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
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
			$this->_pagination = ED::getPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		$db			= ED::db();

		$query	= 'SELECT * FROM ' . $db->nameQuote('#__discuss_tags')
				. $where . ' '
				. $orderby;

		return $query;
	}

	public function _buildQueryWhere()
	{
		$mainframe			= JFactory::getApplication();
		$db					= ED::db();

		$filter_state 		= $mainframe->getUserStateFromRequest('com_easydiscuss.tags.filter_state', 'filter_state', '', 'word');
		$search 			= $mainframe->getUserStateFromRequest('com_easydiscuss.tags.search', 'search', '', 'string');
		$search 			= $db->getEscaped(trim(JString::strtolower($search)));

		$where = array();

		if ($filter_state)
		{
			if ($filter_state == 'P')
			{
				$where[] = $db->nameQuote('published') . '=' . $db->Quote('1');
			}
			else if ($filter_state == 'U')
			{
				$where[] = $db->nameQuote('published') . '=' . $db->Quote('0');
			}
		}

		if ($search)
		{
			$where[] = ' LOWER(title) LIKE \'%' . $search . '%\' ';
		}

		$where 		= (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest('com_easydiscuss.tags.filter_order', 		'filter_order', 	'id', 'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest('com_easydiscuss.tags.filter_order_Dir',	'filter_order_Dir',		'', 'word');

		$orderby 			= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData($usePagination = true)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			if($usePagination)
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			else
				$this->_data = $this->_getList($query);
		}

		return $this->_data;
	}

	/**
	 * Method to publish or unpublish tags
	 *
	 * @access public
	 * @return array
	 */
	public function publish(&$tags = array(), $publish = 1)
	{
		if(count($tags) > 0)
		{
			$db		= ED::db();

			$ids	= implode(',' , $tags);

			$query	= 'UPDATE ' . $db->nameQuote('#__discuss_tags') . ' '
					. 'SET ' . $db->nameQuote('published') . '=' . $db->Quote($publish) . ' '
					. 'WHERE ' . $db->nameQuote('id') . ' IN (' . $ids . ')';
			$db->setQuery($query);

			if(!$db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
		return false;
	}

	public function searchTag($title)
	{
		$db	= ED::db();

		$query	= 'SELECT ' . $db->nameQuote('id') . ' '
				. 'FROM ' 	. $db->nameQuote('#__discuss_tags') . ' '
				. 'WHERE ' 	. $db->nameQuote('title') . ' = ' . $db->quote($title) . ' '
				. 'LIMIT 1';
		$db->setQuery($query);

		$result	= $db->loadObject();

		return $result;
	}

	public function getTagName($id)
	{
		$db	= ED::db();

		$query	= 'SELECT ' . $db->nameQuote('title') . ' '
				. 'FROM ' 	. $db->nameQuote('#__discuss_tags') . ' '
				. 'WHERE ' 	. $db->nameQuote('id') . ' = ' . $db->quote($id) . ' '
				. 'LIMIT 1';
		$db->setQuery($query);

		$result	= $db->loadResult();

		return $result;
	}

	public function getTagNames($ids)
	{
		$names = array();
		foreach ($ids as $id)
		{
			$names[] = $this->getTagName($id);
		}

		$names = implode(' + ', $names);

		return $names;
	}

	public function isExist($tagName, $excludeTagIds = '0')
	{
		$db = ED::db();

		$query  = 'SELECT COUNT(1) FROM #__discuss_tags';
		$query  .= ' WHERE `title` = ' . $db->Quote($tagName);
		if($excludeTagIds != '0')
			$query  .= ' AND `id` != ' . $db->Quote($excludeTagIds);

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Method to get total tags created so far iregardless the status.
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotalTags($userId = 0)
	{
		$db		= ED::db();
		$where	= array();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_tags');

		if(! empty($userId))
			$where[]  = '`user_id` = ' . $db->Quote($userId);

		$extra	= (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$query	= $query . $extra;


		$db->setQuery($query);

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Returns the number of discussion entries created within this tag.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUsedCount($tagId, $published = false)
	{
		$db = $this->db;
		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_posts_tags') . ' '
				. 'WHERE ' . $db->nameQuote('tag_id') . '=' . $db->Quote($tagId);

		if ($published) {
			$query .= ' AND ' . $db->nameQuote('published') . '=' . $db->Quote(1);
		}
		// echo $query; exit;

		$db->setQuery($query);
		$result	= $db->loadResult();

		return $result;
	}

	public function getTagCloud($limit='', $order='title', $sort='asc' , $userId = '', $usePagination = false)
	{
		$db = ED::db();

		$limitstart = (int) $this->getState('limitstart', 0);

		$query =  'select SQL_CALC_FOUND_ROWS a.`id`, a.`title`, a.`alias`, a.`created`, count(c.`id`) as `post_count`';
		$query .= ' from #__discuss_tags as a';
		$query .= ' left join #__discuss_posts_tags as b on a.`id` = b.`tag_id`';
		$query .= ' left join #__discuss_posts as c';
		$query .= '	on b.post_id = c.id';
		$query .= '	and c.`private`=' . $db->Quote(0);
		$query .= '	and c.`published` = ' . $db->Quote('1');

		// Do not include cluster item here.
		$query .= ' AND c.`cluster_id` = ' . $db->Quote(0);

		$exclude = DiscussHelper::getPrivateCategories();

		if (!empty($exclude)) {
			$query .= ' AND c.`category_id` NOT IN(' . implode(',', $exclude) . ')';
		}

		// exclude blocked users posts.
		$query .= " left join " . $db->nameQuote('#__users') . " as uu on c.`user_id` = uu.`id`";


		$query .= ' where a.`published` = ' . $db->Quote('1');

		if (!empty($userId)) {
			$query .= ' AND a.`user_id`=' . $db->Quote($userId);
		}

		// exlude block users. #788
		$query .= " AND (uu.`block` = 0 or uu.`id` is null)";


		$query .= ' group by (a.`id`)';

		//order
		switch ($order) {
			case 'postcount':
				$query	.=  ' ORDER BY (post_count)';
				break;
			case 'title':
			default:
				$query	.=  ' ORDER BY (a.`title`)';
		}

		//sort
		switch ($sort) {
			case 'asc':
				$query	.=  ' asc ';
				break;
			case 'desc':
			default:
				$query	.=  ' desc ';
		}

		//limit
		if (!empty($limit)) {
			if ($usePagination) {
				$query .= " LIMIT $limitstart, $limit";

			} else {
				$query	.=  ' LIMIT ' . (INT)$limit;
			}
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if ($limit && $usePagination) {

			$cntQuery = "select FOUND_ROWS()";
			$db->setQuery($cntQuery);
			$this->_total = $db->loadResult();
			$this->_pagination = ED::pagination($this->_total, $limitstart, $limit);

		}


		return $result;
	}

	public function getTags($count="")
	{
		$db		= ED::db();

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

	public function suggestTags($text)
	{
		$db	= ED::db();

		$query = "select `id`, `title` from `#__discuss_tags`";
		$query .= " where `published` = " . $db->Quote('1');
		$query .= " and `title` LIKE " . $db->Quote('%' . $text . '%');
		$query .= " order by `title`";

		$db->setQuery($query);
		$result	= $db->loadObjectList();

		return $result;
	}

	/**
	 * Method used to get user's tags for GDPR download.
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getTagsGDPR($userid, $options = array())
	{
		$db = ED::db();

		$limit = isset($options['limit']) ? $options['limit'] : 20;
		$exclude = isset($options['exclude']) ? $options['exclude'] : array();

		if ($exclude && !is_array($exclude)) {
			$exclude = ED::makeArray($exclude);
		}

		$query = "select *";
		$query .= " from `#__discuss_tags`";
		$query .= " where `user_id` = " . $db->Quote($userid);

		if ($exclude) {
			$query .= " and `id` NOT IN (" . implode(',', $exclude) . ")";
		}

		$query .= " LIMIT " . $limit;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}
}
