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

require_once dirname(__FILE__) . '/model.php';

class EasyDiscussModelSpools extends EasyDiscussAdminModel
{
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;

	public function __construct()
	{
		parent::__construct();


		$mainframe = JFactory::getApplication();

		$limit = $mainframe->getUserStateFromRequest('com_easydiscuss.spools.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

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
		$db = ED::db();

		// Lets load the content if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery(false, true);
			$db->setQuery($query);

			$this->_total = $db->loadResult();
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
	public function _buildQuery($publishedOnly = false, $isCount = false)
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere($publishedOnly);
		$orderby = $this->_buildQueryOrderBy();
		$db = DiscussHelper::getDBO();

		$query = 'SELECT *';

		if ($isCount) {
			$query = 'SELECT COUNT(1)';
		}

		$query .= ' FROM ' . $db->nameQuote('#__discuss_mailq');
		$query .= $where;

		$query .= $orderby;

		return $query;
	}

	public function _buildQueryWhere()
	{
		$mainframe = JFactory::getApplication();
		$db = DiscussHelper::getDBO();

		$filter_state = $mainframe->getUserStateFromRequest('com_easydiscuss.spools.filter_state', 'filter_state', 'U', 'word');
		$search = $mainframe->getUserStateFromRequest('com_easydiscuss.spools.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(JString::strtolower($search)));

		$where = array();

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = $db->nameQuote('status') . '=' . $db->Quote('1');
			} else if ($filter_state == 'U') {
				$where[] = $db->nameQuote('status') . '=' . $db->Quote('0');
			}
		}

		if ($search) {
			$where[] = ' LOWER(subject) LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$mainframe = JFactory::getApplication();

		$filter_order = $mainframe->getUserStateFromRequest('com_easydiscuss.spools.filter_order', 'filter_order', 'created', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_easydiscuss.spools.filter_order_Dir', 'filter_order_Dir', 'desc', 'word');

		$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

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
		$limit = $this->getState('limit', 0);
		$limitstart = $this->getState('limitstart', 0);

		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();

			if ($usePagination && $limit) {

				// $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

				$db = ED::db();
				$query .= ' LIMIT ' . $limitstart . ',' . $limit;
				$db->setQuery($query);

				$this->_data = $db->loadObjectList();
			} else {
				$this->_data = $this->_getList($query);
			}
		}

		return $this->_data;
	}
}
