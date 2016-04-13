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

class EasyDiscussModelGroups extends EasyDiscussAdminModel
{
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easydiscuss.groups.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
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
			// WIP
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
	 * Method to get user groups id
	 *
	 * @access public
	 * @return array
	 */
	public function getUserGroups($userId)
	{
		$db = $this->db;

		$query = 'select a.`cluster_id` from `#__social_clusters_nodes` as a';
		$query .= '	inner join `#__social_clusters` as b on a.`cluster_id` = b.`id`';
		$query .= '		and b.`cluster_type` = ' . $db->Quote( SOCIAL_TYPE_GROUP ) . ' and b.`state` = ' . $db->Quote( SOCIAL_STATE_PUBLISHED );
		$query .= ' where a.`uid` = ' . $db->Quote($userId);
		$query .= ' and a.`state` = ' . $db->Quote( SOCIAL_GROUPS_MEMBER_PUBLISHED );
		$query .= ' ORDER BY `a`.`created` DESC';

		$db->setQuery($query);

		$result = $db->loadColumn();		

		return $result;
	}

	/**
	 * Method to get user posts based on groups
	 *
	 * @access public
	 * @return array
	 */
	public function getPostsGroups($options)
	{
		$db = $this->db;
		$date = ED::date();

		$groupsId = isset($options['groupId']) ? $options['groupId'] : null;
		$userId = isset($options['userId']) ? $options['userId'] : null;
		
		// First we need to get the list of groups that user joined
		if (!$groupsId) {
			$groupsId = $this->getUserGroups($userId);
		}

		if (!$groupsId) {
			return false;
		}

		$sort = isset($options['sort']) ? $options['sort'] : 'latest';
		$limit = isset($options['limit']) ? $options['limit'] : null;
		$includeFeatured = isset($options['includeFeatured']) ? $options['includeFeatured'] : true;
		$featuredSticky = isset($options['featuredSticky']) ? $options['featuredSticky'] : false;	

		if (! is_array($groupsId)) {
			$groupsId = array($groupsId);
		}

		$joins = array();

		foreach($groupsId as $groupId) {
			$query = "select $groupId as `group_id`, a.`post_id`, a.`has_polls` as `polls_cnt`, a.`num_fav` as `totalFavourites`, a.`num_replies`, a.`num_attachments` as attachments_cnt,";
			$query .= " a.`num_likes` as `likeCnt`, a.`sum_totalvote` as `VotedCnt`,";
			$query .=  " a.`replied` as `lastupdate`, a.vote as `total_vote_cnt`,";
			$query .= ' DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created`) as `noofdays`, ';
			$query .= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', a.`created`) as `timediff`,';
			$query .= " a.`post_status`, a.`post_type`";
			$query .= " from " . $db->nameQuote('#__discuss_thread') . " as a";
			$query .= " where a.`published` = " . $db->Quote('1');
			$query .= " and a.`cluster_id` = " . $db->Quote($groupId);

			if (!$includeFeatured) {
				$query .= " and a.featured = 0";
			}

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

			if ($limit) {
				$query .= " LIMIT $limit";
			}

			$joins[] = $query;
		}

		$joinQuery = "(" . implode(") UNION ALL (", $joins) . ")";

		$query = "select b.*, th.*,";
		$query .= " pt.`suffix` AS post_type_suffix, pt.`title` AS `post_type_title`, e.`title` AS `group_title`";
		$query .= " FROM (" . $joinQuery . ") as th";
		$query .= " 	INNER JOIN " . $db->nameQuote('#__discuss_posts') . " as b on th.`post_id` = b.`id`";

		// Join with post types table
		$query 	.= "	LEFT JOIN " . $db->nameQuote('#__discuss_post_types') . " AS pt ON b.`post_type`= pt.`alias`";

		// Join with easysocial cluster table.
		$query	.= "	LEFT JOIN " . $db->nameQuote('#__social_clusters') . " AS e ON b.`cluster_id` = e.`id`";

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}	
}
