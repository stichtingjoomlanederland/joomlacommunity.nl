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

class EasyDiscussModelRoles extends EasyDiscussAdminModel
{
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;

	public function __construct()
	{
		parent::__construct();

		$mainframe = JFactory::getApplication();

		$limit = $mainframe->getUserStateFromRequest( 'com_easydiscuss.roles.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

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
		if (empty($this->_total))
		{
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
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the roles
	 *
	 * @access private
	 * @return string
	 */
	protected function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere();
		$orderby = $this->_buildQueryOrderBy();
		$db = $this->db;

		$select	= ' b.title AS usergroup_title';
		$join	= ' LEFT JOIN `#__usergroups` AS b ON b.id = a.usergroup_id';

		$query	= 'SELECT a.*, '
				. $select
				. ' FROM `#__discuss_roles` AS a '
				. $join
				. $where . ' '
				. $orderby;

		return $query;
	}

	protected function _buildQueryWhere()
	{
		$db = $this->db;
		$state = $this->app->getUserStateFromRequest('com_easydiscuss.roles.filter_state', 'filter_state', '', 'word');
		$search = $this->app->getUserStateFromRequest('com_easydiscuss.roles.search', 'search', '', 'word');
		$where = array();

		if ($state) {
			if ($state == 'P') {
				$where[] = $db->nameQuote('a.published') . '=' . $db->Quote('1');
			}

			if ($state == 'U') {
				$where[] = $db->nameQuote('a.published') . '=' . $db->Quote('0');
			}
		}

		if ($search) {
			$where[] = $db->nameQuote('a.title') . ' LIKE ' . $db->Quote('%' . $search . '%');
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	protected function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.roles.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.roles.filter_order_Dir',	'filter_order_Dir',		'', 'word' );

		$orderby 			= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

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
	 * Method to publish or unpublish roles
	 *
	 * @access public
	 * @return array
	 */
	public function publish(&$roles = array(), $publish = 1)
	{
		$origPublishState = $publish;

		if (count($roles) > 0) {
			
			$db	= ED::db();
			$ids = implode(',', $roles);

			$query = 'UPDATE ' . $db->nameQuote('#__discuss_roles') . ' '
					. 'SET ' . $db->nameQuote('published') . '=' . $db->Quote($publish) . ' '
					. 'WHERE ' . $db->nameQuote('id') . ' IN (' . $ids . ')';

			$db->setQuery($query);

			if (!$db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			$actionString = $origPublishState ? 'COM_ED_ACTIONLOGS_AUTHOR_PUBLISH_USER_ROLE' : 'COM_ED_ACTIONLOGS_AUTHOR_UNPUBLISH_USER_ROLE';

			foreach ($roles as $roleId) {
				$role = ED::table('Role');
				$role->load($roleId);

				$actionlog = ED::actionlog();
				$actionlog->log($actionString, 'category', array(
					'link' => 'index.php?option=com_easydiscuss&view=roles&layout=form&id=' . $role->id,
					'roleTitle' => JText::_($role->title)
				));
			}

			return true;
		}

		return false;
	}

	/**
	 * Total roles
	 *
	 * @access public
	 */
	public function getTotalRoles()
	{
		$db = ED::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_roles');

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;		
	}

	/**
	 * Retrieve the Joomla User Group Ids which have its role already
	 *
	 * @since	4.1.8
	 * @access public
	 */
	public function getSelectedUserGroupIds($exclude = array())
	{
		$db = ED::db();

		$query = 'SELECT `usergroup_id` FROM ' . $db->nameQuote('#__discuss_roles');
		$where = array();

		if (!empty($exclude['id']) && $exclude['id']) {
			$where[] = ' `id` != ' . $db->quote($exclude['id']);
		}

		if (!empty($exclude['usergroup_id']) && $exclude['usergroup_id']) {
			$where[] = ' `usergroup_id` != ' . $db->quote($exclude['usergroup_id']);
		}

		if (!empty($where)) {
			$query .= ' WHERE ' . implode(' ', $where);
		}

		$db->setQuery($query);

		$result = $db->loadColumn();

		if (!$result) {
			return array();
		}

		$result = array_unique($result);

		return $result;
	}
}
