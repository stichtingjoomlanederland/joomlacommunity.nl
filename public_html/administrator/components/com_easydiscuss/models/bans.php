<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/model.php');

class EasyDiscussModelBans extends EasyDiscussAdminModel
{
	public $_total = null;
	public $_pagination = null;
	public $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easydiscuss.bans.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function isBanned($options = array())
	{
		// select * from jos_discuss_users_banned where `end` > $now
		$db = ED::db();

		$now = JFactory::getDate()->toSQL();

		// count how many record
		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__discuss_users_banned') . ' WHERE ' . $db->quoteName('end') . ' > ' . $db->quote($now);

		$userId = isset($options['userId']) ? $options['userId'] : null;
		$ip = isset($options['ip']) ? $options['ip'] : null;

		if ($userId) {
			$query .= ' AND ' . $db->quoteName('userid') . ' = ' . $db->quote($options['userId']);
		}

		// If there is a user id, we do not need to check for the ip address
		if ($ip && !$userId) {
			$query .= ' AND ' . $db->quoteName('ip') . ' = ' . $db->quote($options['ip']);
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result > 0;
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
	 * Generates the pagination library
	 *
	 * @since	4.0.19
	 * @access	public
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
	 * Give you the list of the banned user
	 * @access private
	 * @return string
	 */
	public function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere();
		$orderby = $this->_buildQueryOrderBy();
		$db = ED::db();

		$query	= 'SELECT * FROM ' . $db->nameQuote('#__discuss_users_banned')
				. $where . ' '
				. $orderby;

		return $query;
	}

	public function _buildQueryWhere()
	{
		$mainframe = JFactory::getApplication();
		$db = ED::db();

		$filter_state = $mainframe->getUserStateFromRequest('com_easydiscuss.bans.filter_state', 'filter_state', '', 'word');
		$search = $mainframe->getUserStateFromRequest('com_easydiscuss.bans.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(JString::strtolower($search)));

		$where = array();

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = $db->nameQuote('blocked') . '=' . $db->Quote('1');
			} else if ($filter_state == 'U') {
				$where[] = $db->nameQuote('blocked') . '=' . $db->Quote('0');
			}
		}

		if ($search) {
			$where[] = ' LOWER(' . $db->nameQuote('banned_username') . ') LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$mainframe = JFactory::getApplication();

		$filter_order = $mainframe->getUserStateFromRequest('com_easydiscuss.bans.filter_order', 'filter_order', 'id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_easydiscuss.bans.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData( $usePagination = true)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();

			if ($usePagination) {
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			} else {
				$this->_data = $this->_getList($query);
			}
		}

		return $this->_data;
	}
}
