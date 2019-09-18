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

class EasyDiscussModelAssigned extends EasyDiscussAdminModel
{
	private $_total = null;
	private $_pagination = null;
	private $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = ($this->app->getCfg('list_limit') == 0) ? 5 : ED::getListLimit();
		$limitstart = $this->input->get('limitstart', '0', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

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
		// Load total number of rows
		if (empty($this->_total)) {
			$this->_total = $this->_getListCount($this->_buildQuery());
		}

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
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			$this->_pagination = ED::pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method to get an array of post assigned to
	 *
	 * @access public
	 * @return array
	 */
	public function _buildQuery($userid = null)
	{
		$db = ED::db();
		$date = ED::date();

		if (is_null($userid)) {
			$userid = JFactory::getUser()->id;
		}

		$respectPrivacy = ($this->my->id == $userid) ? false : true;

		$query = 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', b.`created`) as `noofdays`, ';
		$query .= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', IF(b.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', b.`created`, b.`replied`)) as `daydiff`, ';
		$query .= ' TIMEDIFF(' . $db->Quote($date->toMySQL()). ', IF(b.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', b.`created`, b.`replied`)) as `timediff`,';
		$query .= ' a.*,';
		$query .= ' e.`title` AS `category`,';
		$query .= ' pt.`suffix` AS post_type_suffix, pt.`title` AS post_type_title,';
		$query .= ' IF(b.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', b.`created`, b.`replied`) as `lastupdate`';

		$query .= ' FROM `#__discuss_thread` AS b';
		$query .= ' INNER JOIN `#__discuss_posts` AS a on a.id = b.post_id';
		$query .= ' LEFT JOIN ' . $db->nameQuote('#__discuss_category') . ' AS e ON e.`id` = a.`category_id`';
		$query .= ' INNER JOIN ' . $db->nameQuote('#__discuss_assignment_map') . ' AS am ON am.`post_id` = a.id';
		$query .= '	LEFT JOIN ' . $db->nameQuote('#__discuss_post_types') . ' AS pt ON a.`post_type` = pt.`alias`';

		$query .= ' WHERE am.`created` = (SELECT MAX(`created`) FROM `#__discuss_assignment_map` WHERE `post_id` = a.`id`)';
		$query .= ' AND am.`assignee_id` = ' . $db->Quote($userid);
		$query .= ' AND a.`published` = 1';

		if ($respectPrivacy) {
			// category ACL:
			$catOptions = array();
			$catOptions['idOnly'] = true;
			$catOptions['includeChilds'] = true;

			$catModel = ED::model('Categories');
			$catIds = $catModel->getCategoriesTree(0, $catOptions);

			// if there is no categories return, means this user has no permission to view all the categories.
			// if that is the case, just return empty array.
			if (! $catIds) {
				// force the sql to return null
				$query .= ' and a.`category_id` = 0';
			} else {
				$query .= ' and a.`category_id` IN (' . implode(',', $catIds) . ')';
			}
		}

        $query .= ' ORDER BY am.`created` DESC';
        
		// echo $query;
		// echo '<br><br>';

		return $query;
	}

	/**
	 * Get user's total assigned posts counts
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTotalAssigned($userId = null)
	{
		$db = ED::db();
		$date = ED::date();

		if (is_null($userId)) {
			$userId = JFactory::getUser()->id;
		}

		$respectPrivacy = ($this->my->id == $userId) ? false : true;

		$query	 = array();
		$query[] = 'SELECT COUNT(*)';
		$query[] = 'FROM ' . $db->nameQuote('#__discuss_posts') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->nameQuote('#__discuss_assignment_map') . ' AS b';
		$query[] = 'ON b.' . $db->nameQuote('post_id') . ' = a.' . $db->nameQuote('id');
		$query[] = 'WHERE';
		$query[] = 'b.' . $db->nameQuote('assignee_id') . '=' . $db->Quote($userId);
		$query[] = 'and b.`created` = (SELECT MAX(`created`) FROM `#__discuss_assignment_map` WHERE `post_id` = a.`id`)';

		$query[] = 'AND a.' . $db->nameQuote('parent_id') . '=' . $db->Quote(0);
		$query[] = 'AND a.' . $db->nameQuote('published') . '=' . $db->Quote(1);

		if ($respectPrivacy) {
			// category ACL:
			$catOptions = array();
			$catOptions['idOnly'] = true;
			$catOptions['includeChilds'] = true;

			$catModel = ED::model('Categories');
			$catIds = $catModel->getCategoriesTree(0, $catOptions);

			// if there is no categories return, means this user has no permission to view all the categories.
			// if that is the case, just return empty array.
			if (! $catIds) {
				// force the sql to return null
				return 0;
			} else {
				$query[] = 'AND a.`category_id` IN (' . implode(',', $catIds) . ')';
			}

		}

		$query = implode(' ', $query);
		// echo $query;exit;

		$db->setQuery($query);
		$total = $db->loadResult();

		if (!$total) {
			return 0;
		}

		return (int) $total;
	}

	/**
	 * Get user's total resolved posts counts
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTotalSolved($userId = null)
	{
		$db = ED::db();
		$date = ED::date();

		if (is_null($userId)) {
			$userId = JFactory::getUser()->id;
		}

		$query = array();
		$query[] = 'SELECT COUNT(*)';
		$query[] = 'FROM ' . $db->nameQuote('#__discuss_posts') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->nameQuote('#__discuss_assignment_map') . ' AS b';
		$query[] = 'ON b.' . $db->nameQuote('post_id') . ' = a.' . $db->nameQuote('id');
		$query[] = 'WHERE';
		$query[] = 'b.' . $db->nameQuote('assignee_id') . '=' . $db->Quote($userId);
		$query[] = 'AND a.' . $db->nameQuote('parent_id') . '=' . $db->Quote(0);
		$query[] = 'AND a.' . $db->nameQuote('isresolve') . '=' . $db->Quote(1);

		$query = implode(' ' , $query);

		$db->setQuery($query);

		$total = $db->loadResult();

		if (!$total) {
			return 0;
		}

		return (int) $total;
	}

	/**
	 * Get user's total unresolved counts
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTotalUnresolved($userId = null)
	{
		$db = DiscussHelper::getDBO();
		$userId	= JFactory::getUser($userId)->id;

		$query = array();
		$query[] = 'SELECT COUNT(*)';
		$query[] = 'FROM ' . $db->nameQuote('#__discuss_posts') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->nameQuote('#__discuss_assignment_map') . ' AS b';
		$query[] = 'ON b.' . $db->nameQuote('post_id') . ' = a.' . $db->nameQuote('id');
		$query[] = 'WHERE';
		$query[] = 'b.' . $db->nameQuote('assignee_id') . '=' . $db->Quote($userId);
		$query[] = 'AND a.' . $db->nameQuote('parent_id') . '=' . $db->Quote(0);
		$query[] = 'AND a.' . $db->nameQuote('isresolve') . '=' . $db->Quote(0);

		$query = implode(' ' , $query);

		$db->setQuery($query);

		$total = $db->loadResult();

		if (!$total) {
			return 0;
		}

		return (int) $total;
	}


	/**
	 * Get user's assigned posts
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPosts($userId = null)
	{
		if (empty($this->_data)) {
			$query = $this->_buildQuery($userId);

			$limitstart = $this->getState('limitstart');
			$limit = $this->getState('limit');
			$this->_data = $this->_getList($query, $limitstart , $limit);
		}

		return $this->_data;
	}

	/**
	 * Generates the assign posts graph for the user
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAssignPostGraph($userId)
	{
		$db = ED::db();

		// Get the past 7 days
		$dates = array();

		for($i = 0; $i < 7; $i++) {
			$date = JFactory::getDate('-' . $i . ' day');
			$dates[] = $date->format('Y-m-d');
		}

		//Reverse the dates
		$dates = array_reverse($dates);

		// Prepare the main result
		$result = new stdClass();
		$result->dates = $dates;
		$result->count = array();

		$i = 0;

		foreach ($dates as $date) {
			$query = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName("#__discuss_assignment_map");
			$query[] = 'WHERE DATE_FORMAT(' . $db->quoteName('created') . ', GET_FORMAT(DATE, "ISO")) =' . $db->Quote($date);
			$query[] = 'AND ' . $db->quoteName('assignee_id') . '=' . $db->Quote($userId);

			$query = implode(' ', $query);
			$db->setQuery($query);
			$total = $db->loadResult();

			$result->count[$i] = $total;

			$i++;
		}

		return $result;
	}

}
