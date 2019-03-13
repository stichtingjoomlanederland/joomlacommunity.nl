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
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelBadges extends EasyDiscussAdminModel
{
	public $_data = null;
	public $_pagination = null;
	public $_total;
	public $_parent	= null;
	public $_isaccept = null;

	public function __construct()
	{
		parent::__construct();

		//get the number of events from database
		$limit = $this->app->getUserStateFromRequest('com_easydiscuss.badges.limit', 'limit', $this->app->getCfg('list_limit') , 'int');
		$limitstart = $this->input->get('limitstart', '0', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Remove badge based on user id and/or badge id
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function removeBadge($userId = null, $badgeId = null)
	{
		$db = $this->db;

		$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_badges_users');
		$query .= ' WHERE ' . $db->nameQuote('user_id' ) . '=' . $db->Quote($userId);

		if ($badgeId) {
			$query .= ' AND ' . $db->nameQuote('badge_id') . '=' . $db->Quote($badgeId);
		}

		$db->setQuery($query);

		if (!$db->query()) {
			return false;
		}

		return true;
	}

	/**
	 * Determine whether this user have any badges or not
	 *
	 * @since	4.1.2
	 * @access	public
	 */
	public function hasUserBadges($userId)
	{
		if (!$userId) {
			return false;
		}

		$db = $this->db;

		$query = "select count(1) from `#__discuss_badges_users` as a";
		$query .= " inner join `#__discuss_badges` as b on a.badge_id = b.id";
		$query .= " where a.`published` = 1";
		$query .= " and a.`user_id` = " . $db->Quote($userId);
		$query .= " and b.`published` = 1";

		$db->setQuery($query);
		$result = $db->loadColumn();

		return $result ? true : false;
	}

	/**
	 * Retrieved a list of all badges available from the site.
	 * Retrieved a list of all user's achieved badges from the site.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	Array
	 * @return	Array	Array of DiscussBadges Object
	 */
	public function getSiteBadges($options = array())
	{
		$db = $this->db;
		$query = array();

		if (isset($options['user'])) {
			$query[] = 'SELECT a.*, b.`custom` FROM ' . $db->nameQuote('#__discuss_badges') . ' AS a';
			$query[] = 'INNER JOIN ' . $db->nameQuote('#__discuss_badges_users') . ' AS b';
			$query[] = 'ON b.`badge_id` = a.`id`';
			$query[] = 'AND b.`published` = ' . $db->Quote('1');
			$query[] = 'AND b.`user_id` = ' . $db->Quote($options['user']);
		} else {
			$query[] = 'SELECT a.* FROM ' . $db->nameQuote('#__discuss_badges') . ' AS a';
		}

		$query[] = 'WHERE a.`published` = ' . $db->Quote('1');

		$query = implode(' ', $query);
		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		// Binds the badges into badge table
		$badges = array();
		foreach ($result as $row) {
			$badge = ED::table('Badges');
			$badge->bind($row);

			// We'll need to re-assign the badge description if the custom message was set at the backend.
			$badge->description = isset($row->custom) ? ($row->custom != '') ? $row->custom : $badge->description : $badge->description;
			$badges[] = $badge;
		}

		return $badges;
	}

	public function getBadges( $exclusion = false )
	{
		if(empty($this->_data) )
		{
			$this->_data	= $this->_getList( $this->buildQuery( $exclusion ) , $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}

	private function buildQuery( $exclusion = false )
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere( $exclusion );
		$orderby	= $this->_buildQueryOrderBy();
		$db			= DiscussHelper::getDBO();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_badges' ) . ' AS a ';
		$query	.= $where . ' ';
		$query	.= $orderby;

		return $query;
	}

	public function _buildQueryWhere($exclusion = false)
	{
		$db	= ED::db();

		$filter_state = $this->app->getUserStateFromRequest('com_easydiscuss.badges.filter_state', 'filter_state', '', 'word');
		$search	= $this->app->getUserStateFromRequest('com_easydiscuss.badges.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(JString::strtolower($search)));

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

		$exclusion	= trim($exclusion);

		if ($exclusion) {

			$exclusion = explode(',', $exclusion);

			$query = ' a.' . $db->nameQuote( 'id' ) . ' NOT IN(';

			for ($i = 0; $i < count($exclusion); $i++) {

				$query .= $db->Quote($exclusion[$i]);

				if (next($exclusion) !== false) {
					$query .= ',';
				}
			}

			$query .= ')';

			$where[] = $query;
		}

		$where = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$filter_order		= $this->app->getUserStateFromRequest( 'com_easydiscuss.badges.filter_order', 		'filter_order', 	'a.created', 'cmd' );
		$filter_order_Dir	= $this->app->getUserStateFromRequest( 'com_easydiscuss.badges.filter_order_Dir',	'filter_order_Dir',	'ASC', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}


	/**
	 * Get a list of user badge history
	 *
	 **/
	public function getBadgesHistory( $userId )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote('#__discuss_users_history') . ' '
				. 'WHERE ' . $db->nameQuote('user_id') . '=' . $db->Quote($userId) . ' '
				. 'ORDER BY ' . $db->nameQuote('id') . ' DESC '
				. 'LIMIT 0,30 ';

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		return $result;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	public function &getPagination()
	{
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
		if( empty($this->_total) )
		{
			$this->_total	= $this->_getListCount( $this->buildQuery() );
		}

		return $this->_total;
	}

	public function getRules()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_rules' );
		$db->setQuery( $query );
		return $db->loadObjectList();
	}

	/**
	 * Get badges by points achieve type
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getBadgesByCommand($command, $userId)
	{
		$db = $this->db;

		$query = array();

		$query[] = 'SELECT * from `#__discuss_badges`';
		$query[] = 'WHERE ' . $db->nameQuote('achieve_type') . ' = ' . $db->Quote('points');
		$query[] = 'AND (';
		$query[] = $db->nameQuote('badge_achieve_rule') . ' = ' . $db->Quote($command);
		$query[] = 'OR';
		$query[] = $db->nameQuote('badge_remove_rule') . ' = ' . $db->Quote($command);
		$query[] = ')';
		$query[] = 'AND ' . $db->nameQuote('published') . ' = ' . $db->Quote('1');

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$badges = array();

		foreach ($result as $row) {
			$badge = ED::table('Badges');
			$badge->bind($row);

			$badges[] = $badge;
		}

		return $badges;
	}
}
