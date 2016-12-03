<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelNotification extends EasyDiscussAdminModel
{
	public $_total = null;
	public $_pagination = null;
	public $_data = null;

	public $_parent	= null;
	public $_isaccept = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest( 'com_easydiscuss.posts.limit', 'limit', ED::getListLimit(), 'int');
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to return the total number of rows
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// user must call the getdata before they can call this method or else the total will be empty
		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return object
	 */
	public function getPagination()
	{
		$limit = $this->config->get('main_notification_listings_limit', 5);

		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			$this->_pagination = ED::pagination($this->getTotal(), $this->getState('limitstart'), $limit);
		}

		return $this->_pagination;
	}

	/**
	 * Returning the total notification of the particular user.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	int $userId		The user id
	 * @return
	 */
	public function getTotalNotifications($userId)
	{
		$db = $this->db;

		$query = 'SELECT COUNT(1) FROM (SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__discuss_notifications') . ' '
				. 'WHERE ' . $db->nameQuote('target') . '=' . $db->Quote($userId) . ' '
				. 'AND ' . $db->nameQuote('state') . '=' . $db->Quote(DISCUSS_NOTIFICATION_NEW) . ' '
				. 'GROUP BY ' . $db->nameQuote('cid') . ',' . $db->nameQuote('type') . ') as a';

		$db->setQuery($query);

		$notifications = $db->loadResult();

		return $notifications;
	}

	/**
	 * Returns a list of notifications for a specific user
	 *
	 * @since	4.0
	 * @access	public
	 * @param	int	$userId		The target
	 * @param	int	$limit		The limit of notifications to fetch
	 * @return	array
	 */
	public function getNotifications($userid, $showNewOnly = false, $limit = null, $limitstart = null)
	{
		$db = $this->db;

		$limit	= (int) $limit;

		$query = array();
		$query[] = 'SELECT x.items , a.`id` , a.`cid` , a.`type` , a.`title`, a.`target`, a.`author`, a.`permalink`, a.`created`, a.`component`, a.`anonymous`,';
		$query[] = 'a.`state`, a.`favicon`, DATE_FORMAT( a.`created`, GET_FORMAT(DATE, "ISO")) as `day`';
		$query[] = 'FROM `#__discuss_notifications` a';
		$query[] = 'inner join (';
		$query[] = 'select count(b.`cid`) as items, max(b.id) as `id`, ';
		$query[] = 'DATE_FORMAT(b.`created`, GET_FORMAT(DATE, "ISO")) as `day` ';
		$query[] = 'from `#__discuss_notifications` b';
		$query[] = 'WHERE b.`target`=' . $db->Quote($userid);

		if ($showNewOnly) {
			$query[] = 'AND b.`state`=' . $db->Quote('1');
		}

		$query[] = 'GROUP BY b.`cid`,b.`type`,`day`';
		$query[] = ') as x ON x.id = a.id';
		$query[] = 'WHERE a.`target`=' . $db->Quote($userid);

		if ($showNewOnly) {
			$query[] = 'AND a.`state`=' . $db->Quote(1);
		}

		$query[] = 'ORDER BY a.`id` DESC';

		$query = implode(' ', $query);

		$db->setQuery($query);

		$this->_data = $db->loadObjectList();

		// now execute found_row() to get the number of records found.
		$cntQuery = 'select FOUND_ROWS()';
		$db->setQuery($cntQuery);
		$this->_total = $db->loadResult();

		$limitstart = is_null($limitstart) ? $this->getState( 'limitstart') : $limitstart;
		$limit = is_null($limit) ? $this->getState('limit') : $limit;		

		if ($limit) {
			$queryLimit = ' LIMIT ' . $limitstart . ', ' . $limit;
			$query = $query . $queryLimit;
		}	

		$db->setQuery($query);

		$this->_data = $db->loadObjectList();

		return $this->_data;
	}

	/**
	 * Mark everything as read
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function markAllRead($userId = null)
	{
		$userId = JFactory::getUser($userId)->id;
		$db = $this->db;

		$query	= 'UPDATE ' . $db->qn( '#__discuss_notifications' ) . ' '
				. 'SET ' . $db->qn( 'state' ) . '=' . $db->Quote( 0 ) . ' '
				. 'WHERE ' . $db->qn( 'target' ) . '=' . $db->Quote($userId);
		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Updates notifications since the browser / viewer / user has already read the topic
	 *
	 * @access	public
	 * @param	int	$userId		The current user that is viewing
	 * @param	int $cid		The unique id of notification to clear
	 * @param	Array $types	The type of notification to clear
	 **/
	public function markRead($userId, $cid = false, $types)
	{
		$db		= $this->db;
		$query	= 'UPDATE #__discuss_notifications '
				. 'SET ' . $db->nameQuote( 'state' ) . '=' . $db->Quote(DISCUSS_NOTIFICATION_READ) . ' '
				. 'WHERE ' . $db->nameQuote( 'target' ) . '=' . $db->Quote($userId);


		// If cid is not provided, caller might just want to clear all notifications for a specific user when they view certain actions.
		if ($cid) {
			$query	.= ' AND ' . $db->nameQuote('cid') . '=' . $db->Quote($cid);
		}

		if (!is_array($types)) {
			$types = array($types);
		}

		$query .= ' AND ' . $db->nameQuote('type') . ' IN(';

		for ($i = 0; $i < count($types); $i++) {
			$query .= $db->Quote($types[$i]);

			if (next($types) !== false) {
				$query	.= ',';
			}
		}

		$query .= ')';
		$db->setQuery($query);

		$db->Query();
	}

	/**
     * Delete post's notifications
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
	public function deleteNotifications($postId = null)
	{
		if (!empty($postId)) {

			$query = 'DELETE FROM ' . $this->db->nameQuote('#__discuss_notifications')
					. ' WHERE ' . $this->db->nameQuote('cid') . '=' . $this->db->Quote($postId);

			$this->db->setQuery($query);
			$this->db->Query();
		}
	}
}
