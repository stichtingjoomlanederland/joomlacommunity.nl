<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelAcl extends EasyDiscussAdminModel
{
	public $_total = null;
	public $_pagination = null;
	public $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easydiscuss.acls.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', '0', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getTotal($type)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery($type);
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
	public function getPagination($type)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			$this->_pagination = ED::getPagination($this->getTotal($type), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Retrieve all the published rules available.
	 *
	 * @access public
	 * @return Object
	 */
	public function getRules($key = '', $type = '', $cid = '')
	{
		$db = $this->db;

		// This is for public group
		if ($type == 'group' && ($cid == '0' || $cid == '1')) {
			$sql = 'SELECT * FROM ' . $db->nameQuote('#__discuss_acl');
			$sql .= ' WHERE `published` = 1';
			$sql .= ' and `public` = 1';
			$sql .= ' ORDER BY `id` ASC';
		}
		else {
			$sql = 'SELECT * FROM ' . $db->nameQuote('#__discuss_acl').' WHERE `published` = 1 ORDER BY `id` ASC';
		}

		$db->setQuery($sql);

		return $db->loadObjectList($key);
	}

	/**
	 * Delete the existing rules set of the provided id.
	 *
	 * @access public
	 * @return
	 */
	public function deleteRuleset($cid, $type)
	{
		if (is_null($cid) || empty($type)) {
			return false;
		}

		$db = $this->db;

		$sql = 'DELETE FROM ' . $db->nameQuote('#__discuss_acl_group') . ' WHERE '. $db->nameQuote('content_id') . ' = ' . $db->quote($cid) . ' AND `type` = ' . $db->quote($type);

		$db->setQuery($sql);
		$result = $db->query();

		return $result;
	}

	public function insertRuleset($cid, $type, $saveData)
	{
		$db = DiscussHelper::getDBO();

		$rules = $this->getRules('action', $type, $cid);

		$newruleset = array();

		foreach ($rules as $rule) {
			$action = $rule->action;
			$str = "(".$db->quote($cid).", ".$db->quote($rule->id).", ".$db->quote($saveData[$action]).", ".$db->quote($type).")";
			array_push($newruleset, $str);
		}

		if (!empty($newruleset)) {
			$sql = 'INSERT INTO ' . $db->nameQuote('#__discuss_acl_group') . ' (`content_id`, `acl_id`, `status`, `type`) VALUES ';
			$sql .= implode(',', $newruleset);
			$db->setQuery($sql);

			return $result = $db->query();
		}

		return true;
	}

	/**
	 * Retrieves information for a ruleset
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRuleSet($type, $cid)
	{
		$db = $this->db;

		$ruleset = new stdClass();
		$ruleset->rules = array();

		// Retrieves all the rules available.
		$items = $this->getRules('id', $type, $cid);
		$rules = array();

		// Reorganize the rules by id
		if ($items) {
			foreach ($items as &$item) {
				$rules[$item->id] = $item;
			}
		}

		// Get the ruleset name
		$query = $this->_buildQuery($type, $cid);
		$db->setQuery($query);

		$jGroup = $db->loadObject();

		$ruleset->id = $jGroup->id;
		$ruleset->name = $jGroup->name;

		// Set the rules
		if ($rules) {
			foreach ($rules as $rule) {

				if (!isset($ruleset->rules[$rule->group])) {
					$ruleset->rules[$rule->group] = array();
				}

				$ruleset->rules[$rule->group][$rule->id] = $rule;
				$ruleset->rules[$rule->group][$rule->id]->value = $rule->default;
			}
		}

		// Get the stored values
		$query = 'SELECT * FROM ' . $db->qn('#__discuss_acl_group');
		$query .= ' WHERE ' . $db->qn('content_id') . '=' . $db->Quote($cid);
		$query .= ' AND ' . $db->qn('type') . '=' . $db->Quote($type);
		$db->setQuery($query);

		$rows = $db->loadObjectList();

		if ($rows) {
			foreach ($rows as $row) {

				if (! isset($rules[$row->acl_id])) {
					continue;
				}

				$group = $rules[$row->acl_id]->group;

				$ruleset->rules[$group][$row->acl_id]->value = $row->status;
			}
		}

		return $ruleset;
	}

	/**
	 * Retrieves a list of tabs
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTabs()
	{
		$db = $this->db;

		$query = 'SELECT DISTINCT(' . $db->qn('group') . ') FROM ' . $db->qn('#__discuss_acl') . ' WHERE ' . $db->qn('group') . ' != ""';
		$db->setQuery($query);

		$result = $db->loadColumn();

		if (!$result) {
			return;
		}

		$tabs = array();

		foreach ($result as $row) {



			$tabs[] = $tab;
		}

		return $tabs;
	}

	public function getRuleSets($type='group', $cid='')
	{
		$db 		= DiscussHelper::getDBO();

		$rulesets	= new stdClass();
		$ids		= array();

		$rules = $this->getRules('id');

		//get user
		$query = $this->_buildQuery($type, $cid);

		$pagination = $this->getPagination( $type );
		$rows = $this->_getList($query, $pagination->limitstart, $pagination->limit );

		if (!empty($rows)) {
			foreach ($rows as $row) {
				$rulesets->{$row->id}			= new stdClass();
				$rulesets->{$row->id}->id		= $row->id;
				$rulesets->{$row->id}->name		= $row->name;
				$rulesets->{$row->id}->level	= $row->level;

				foreach ($rules as $rule) {
					$rulesets->{$row->id}->{$rule->action} = (INT)$rule->default;
				}

				array_push($ids, $row->id);
			}

			//get acl group ruleset
			$sql = 'SELECT * FROM ' . $db->nameQuote('#__discuss_acl_group') . ' WHERE '. $db->nameQuote('type') . ' = ' . $db->quote($type) . ' AND `content_id` IN (' . implode( ' , ', $ids ) . ')';
			$db->setQuery($sql);
			$acl = $db->loadAssocList();

			if ( count( $acl ) > 0) {
				foreach ($acl as $data) {
					if (isset($rules[$data['acl_id']])) {
						$action = $rules[$data['acl_id']]->action;
						$rulesets->{$data['content_id']}->{$action} = $data['status'];
					}
				}
			}
		}

		return $rulesets;
	}

	/**
	 * Builds the main query
	 *
	 * @access public
	 * @return string
	 */
	public function _buildQuery($type = 'group', $cid = '')
	{
		$db = $this->db;

		switch ($type) {
			case 'group':
				$query = 'SELECT a.`id`, a.`title` AS `name`, COUNT(DISTINCT b.`id`) AS level';
				$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
				$query .= ' FROM `#__usergroups` AS a';
				$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';

				break;
			case 'assigned':
			default:
				$query	= 'SELECT DISTINCT(a.`id`), a.`name`, 0 as `level` FROM ' . $db->nameQuote('#__users') . ' a LEFT JOIN ' . $db->nameQuote('#__discuss_acl_group') . ' b ON a.`id` = b.`content_id` ';
		}

		$where = $this->_buildQueryWhere($type, $cid);
		$orderby = $this->_buildQueryOrderBy($type);

		if ($cid) {
			$query .= $where;
		}

		$query .= ' ' . $orderby;

		return $query;
	}

	/**
	 * Builds the where query
	 *
	 * @access public
	 * @return string
	 */
	public function _buildQueryWhere($type = 'group', $cid = '')
	{
		$db = $this->db;

		$search = $this->app->getUserStateFromRequest('com_easydiscuss.acls.search', 'search', '', 'string');
		$search = $db->getEscapedtrim(JString::strtolower($search));

		$where = array();

		if ($cid == '') {
			if ($type && $type == 'assigned') {
				$where[] = 'b.`type` = '.$db->quote($type);
			}

			if ($search) {
				$where[] = ' LOWER( name ) LIKE \'%' . $search . '%\' ';
			}
		}
		else {
			if ($type == 'group') {
				$where[] = 'a.`id` = ' . $db->quote($cid);

			}
			else if($type == 'assigned') {
				$where[] = 'a.`id` = '.$db->quote($cid);
				$where[] = 'b.`type` = '.$db->quote($type);
			}
		}

		$where = (count($where) ? ' WHERE ' .implode(' AND ', $where) : '');

		return $where;
	}

	/**
	 * Builds the order query
	 *
	 * @access public
	 * @return string
	 */
	public function _buildQueryOrderBy($type = 'group')
	{
		$filter_order = $this->app->getUserStateFromRequest('com_easydiscuss.acls.filter_order', 'filter_order', 'a.`id`', 'cmd');
		$filter_order_Dir = $this->app->getUserStateFromRequest('com_easydiscuss.acls.filter_order_Dir', 'filter_order_Dir', '', 'word');

		if($type == 'group') {
			$orderby = ' GROUP BY a.id';
			$orderby .= ' ORDER BY a.lft ASC';
		}
		else {
			$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;
		}

		return $orderby;
	}
}
