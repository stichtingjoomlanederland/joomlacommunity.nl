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

class EasyDiscussModelRules extends EasyDiscussAdminModel
{
	public $_data = null;
	public $_pagination = null;
	public $_total;

	public function __construct()
	{
		parent::__construct();

		$mainframe 	= JFactory::getApplication();

		//get the number of events from database
		$limit = $mainframe->getUserStateFromRequest('com_easydiscuss.rules.limit', 'limit', $mainframe->getCfg('list_limit') , 'int');
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getRules()
	{
		if (empty($this->_data)) {

			$query = $this->buildQuery();

			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
	}

	private function buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere();
		$orderby = $this->_buildQueryOrderBy();
		$db	= ED::db();

		$query = 'SELECT * FROM ' . $db->nameQuote('#__discuss_rules') . ' AS a ';
		$query .= $where . ' ' . $orderby;

		return $query;
	}

	public function _buildQueryWhere()
	{
		$mainframe = JFactory::getApplication();
		$db	= ED::db();

		$filter_state = $mainframe->getUserStateFromRequest('com_easydiscuss.rules.filter_state', 'filter_state', '', 'word');
		$search = $mainframe->getUserStateFromRequest('com_easydiscuss.rules.search', 'search', '', 'string');
		$search	= $db->getEscaped(trim(EDJString::strtolower($search)));

		$where = array();

		if ($filter_state) {

			if ($filter_state == 'published') {
				$where[] = $db->nameQuote('a.published') . '=' . $db->Quote('1');

			} else if ($filter_state == 'unpublished') {
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

		$filter_order = $mainframe->getUserStateFromRequest('com_easydiscuss.rules.filter_order', '', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_easydiscuss.rules.filter_order_Dir', '',	'DESC', 'word');

		$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	public function &getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			$this->_pagination = ED::getPagination($this->getTotal() , $this->getState('limitstart'), $this->getState('limit'));
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

	public function publishRules($blogs = array(), $publish = 1)
	{
		if (count($blogs) > 0) {
			
			$db	= ED::db();
			$blogs = implode(',', $blogs);

			$query = 'UPDATE ' . $db->nameQuote('#__discuss_posts') . ' '
					. 'SET ' . $db->nameQuote('published') . '=' . $db->Quote($publish) . ' '
					. 'WHERE ' . $db->nameQuote('id') . ' IN (' . $blogs . ')';

			$db->setQuery($query);

			if (!$db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}

		return false;
	}

	public function deleteRules($blogs = array())
	{
		if (count($blogs) > 0) {
			
			$db = ED::db();
			$blogs = implode(',', $blogs);

			$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_posts') . ' '
					. 'WHERE ' . $db->nameQuote('id') . ' IN (' . $blogs . ')';
			$db->setQuery($query);

			if (!$db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}

		return false;
	}

	public function revertAnwered($blogs = array())
	{
		if (count($blogs) > 0) {
			
			$db	= ED::db();
			$blogs = implode(',', $blogs);

			$query = 'SELECT `parent_id` FROM `#__discuss_posts`';
			$query	.= ' WHERE `id` IN (' . $blogs . ')';
			$query	.= ' AND `answered` = ' . $db->Quote('1');
			$query	.= ' AND `parent_id` != ' . $db->Quote('0');

			$db->setQuery( $query );
			$parent = $db->loadResult();

			if (!empty($parent)) {
				
				$query = 'UPDATE ' . $db->nameQuote('#__discuss_posts') . ' '
						. 'SET ' . $db->nameQuote('isresolve') . '=' . $db->Quote(0) . ' '
						. 'WHERE `id` = ' . $db->Quote($parent);
				$db->setQuery($query);
				$db->query();
			}
		}
	}

}
