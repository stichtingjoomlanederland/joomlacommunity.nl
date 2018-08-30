<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once dirname(__FILE__) . '/model.php';

class EasyDiscussModelPoints extends EasyDiscussAdminModel
{
	var $_data = null;
	var $_pagination = null;
	var $_total;

	public function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();

		//get the number of events from database
		$limit = $mainframe->getUserStateFromRequest('com_easydiscuss.points.limit', 'limit', $mainframe->getCfg('list_limit') , 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getPoints()
	{
		if (empty($this->_data)) {
			$this->_data = $this->_getList($this->buildQuery(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	private function buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere();
		$orderby = $this->_buildQueryOrderBy();
		$db	= ED::db();

		$query = 'SELECT * FROM ' . $db->nameQuote('#__discuss_points') . ' AS a ';
		$query .= $where . ' ';
		$query .= $orderby;

		return $query;
	}

	public function _buildQueryWhere()
	{
		$mainframe = JFactory::getApplication();
		$db	= ED::db();

		$filter_state = $mainframe->getUserStateFromRequest('com_easydiscuss.points.filter_state', 'filter_state', '', 'word');
		$search	= $mainframe->getUserStateFromRequest('com_easydiscuss.points.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(JString::strtolower($search)));

		$where = array();

		if ($filter_state) {

			if ($filter_state == 'P') {
				
				$where[] = $db->nameQuote('a.published') . '=' . $db->Quote('1');

			} else if ($filter_state == 'U') {
				
				$where[] = $db->nameQuote('a.published') . '=' . $db->Quote('0');
			}
		}

		if ($search) {
			$where[] = ' LOWER( a.title ) LIKE \'%' . $search . '%\' ';
		}

		$where = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$mainframe = JFactory::getApplication();

		$filter_order = $mainframe->getUserStateFromRequest('com_easydiscuss.points.filter_order', 'filter_order', 'a.created', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_easydiscuss.points.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');

		$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;

		return $orderby;
	}

	/**
	 * Get a list of user badge history
	 *
	 * @since	4.0
	 * @access	public
	 */	
	public function getPointsHistory($userId)
	{
		$db = ED::db();

		$my = JFactory::getUser();

		// Obtain the category that I can view

		// I am able to view if my usergroup is added in the category permission "view discussion"
		$viewableCats = array();

		// First get all the accessible parentId
		$parentCats = array();
		$childCats = array();
		$query = array();

		$parentCats = ED::getAccessibleCategories();

		foreach ($parentCats as $parentCat) {
			$viewableCats[] = $parentCat->id;
		}

		// Second get the child cats that are accessible
		foreach ($parentCats as $parentCat) {
			$childCats = ED::getAccessibleCategories($parentCat->id);

			foreach ($childCats as $childCat) {
				$viewableCats[] = $childCat->id;
			}
		}

		$defaultRules = array('easydiscuss.new.discussion', 
							  'easydiscuss.new.reply', 
							  'easydiscuss.answer.reply', 
							  'easydiscuss.new.comment', 
							  'easydiscuss.like.discussion', 
							  'easydiscuss.like.reply',
							  'easydiscuss.resolved.discussion',
							  'easydiscuss.vote.reply',
							  'easydiscuss.unvote.reply'
							);

		// Retrieve a list of published point rules
		$rulesPublished = $this->getPublishedPointRules();

		// return an array containing all of the values in array1 whose values exist
		$rules = array_intersect($defaultRules, $rulesPublished);
		$inclusionsRules = array();

		foreach ($rules as $rule) {
			$inclusionsRules[] = $db->Quote($rule);
		}

		$inclusionsRules = implode(',', $inclusionsRules);

		$query[] = 'SELECT a.*, ' . $db->Quote('post') . ' as `type`';
		$query[] = 'FROM ' . $db->nameQuote('#__discuss_users_history') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->nameQuote('#__discuss_posts') . ' AS b';
		$query[] = 'ON a.' . $db->nameQuote('content_id') . ' = ' . 'b.' . $db->nameQuote('id');
		$query[] = 'WHERE a.' . $db->nameQuote('user_id') . ' = ' . $db->Quote($userId);

		if ($inclusionsRules) {
			$query[] = 'AND a.' . $db->nameQuote('command') . ' IN (' . $inclusionsRules . ')';
		}

		if ($viewableCats) {
			$query[] = 'AND b.' . $db->nameQuote('category_id') . ' IN (' . implode($viewableCats, ',') . ')';
		}

		$query[] = 'UNION';
		$query[] = 'SELECT a.*, ' . $db->Quote('profile') . ' as `type`';
		$query[] = 'FROM ' . $db->nameQuote('#__discuss_users_history') . ' AS a';
		$query[] = 'WHERE a.' . $db->nameQuote('user_id') . ' = ' . $db->Quote($userId);
		$query[] = 'AND a.' . $db->nameQuote('command');
		$query[] = 'IN (' . $db->Quote('easydiscuss.new.avatar') . ' , ' . $db->Quote('easydiscuss.update.profile') .  ')';
		$query[] = 'ORDER BY ' . $db->nameQuote('created') . ' DESC';
		$query[] = 'LIMIT 0,20';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result	= $db->loadObjectList();

		return $result;
	}

	/**
	 * Method to get a pagination object for the events
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
	 * Method to return the total number of rows
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Load total number of rows
		if (empty($this->_total)) {
			$this->_total = $this->_getListCount($this->buildQuery());
		}

		return $this->_total;
	}

	/**
	 * Get the action rules of easydiscuss
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getRules($includePointsRule = false)
	{
		$db = $this->db;

		$query = 'SELECT * FROM ' . $db->nameQuote('#__discuss_rules');
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Get action rules that are being used on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getRulesWithState()
	{
		$db = $this->db;

		$query = 'SELECT a.*, b.`id` AS points_rule_id';
		$query .= ' FROM `#__discuss_rules` as a';
		$query .= ' LEFT JOIN `#__discuss_points` as b';
		$query .= ' ON a.`id` = b.`rule_id`';

		$db->setQuery($query);

		$result = $db->loadObjectList();

		// Format the result
		if ($result) {
			foreach ($result as $row) {
				$row->availability = true;

				if ($row->points_rule_id) {
					$row->availability = false;
				}
			}
		}

		return $result;
	}

	/**
	 * Get Points history given by command and user id
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTotalPointsHistory($userId, $command)
	{
		$db = $this->db;

		$query = array();

		$query[] = 'SELECT command, count(`command`) as total';
		$query[] = 'FROM `#__discuss_users_history`';
		$query[] = 'WHERE `user_id` = ' . $db->Quote($userId);

		if (is_array($command)) {
			$rule = implode(',', $command);
			$query[] = 'AND `command` IN(' . $db->Quote($rule) . ')';
		} else {
			$query[] = 'AND `command` = ' . $db->Quote($rule);
		}

		$query[] = 'GROUP BY `command`';

		$query = implode(' ', $query);

		$db->setQuery($query);

		$points = $db->loadObjectList();

		if (!$points) {
			return $points;
		}

		$pointsArray = array();

		foreach ($points as $data) {
			$pointsArray[$data->command] = $data->total;
		}

		return $pointsArray;
	}

	/**
	 * Get all points on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAllPoints()
	{
		$db = $this->db;

		$query = array();

		$query[] = 'SELECT * FROM ' . $db->nameQuote('#__discuss_points');
		$query[] = 'WHERE ' . $db->nameQuote('published') . ' = ' . $db->Quote('1');

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return false;
		}

		return $result;
	}

	/**
	 * Determine if the point rules publish/unpublished
	 *
	 * @since	4.1.3
	 * @access	public
	 */
	public function pointRulesExist($command)
	{
		$db = $this->db;

		$query = array();

		$query[] = 'SELECT a.`published` AS `published` FROM ' . $db->nameQuote('#__discuss_points') . ' AS a';
		$query[] = 'INNER JOIN '. $db->nameQuote('#__discuss_rules') . ' AS b';
		$query[] = 'ON b.' . $db->nameQuote('id') . ' = a.' . $db->nameQuote('rule_id');
		$query[] = 'WHERE b.' . $db->nameQuote('command') . '=' . $db->Quote($command);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

        return $result > 0 ? true : false;
	}

	/**
	 * Retrieve all the published point rules command
	 *
	 * @since	4.1.3
	 * @access	public
	 */
	public function getPublishedPointRules()
	{
		$db = $this->db;

		$query = array();

		$query[] = 'SELECT b.`command` AS `command` FROM ' . $db->nameQuote('#__discuss_points') . ' AS a';
		$query[] = 'INNER JOIN '. $db->nameQuote('#__discuss_rules') . ' AS b';
		$query[] = 'ON b.' . $db->nameQuote('id') . ' = a.' . $db->nameQuote('rule_id');
		$query[] = 'WHERE a.' . $db->nameQuote('published') . ' = ' . $db->Quote('1');

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResultArray();

		if (!$result) {
			return array();
		}

		return $result;
	}	
}
