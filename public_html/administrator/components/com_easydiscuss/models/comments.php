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

require_once dirname(__FILE__) . '/model.php';

class EasyDiscussModelComments extends EasyDiscussAdminModel
{
	public $_total = null;
	public $_pagination = null;
	public $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easydiscuss.comments.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getPagination()
	{
		if (empty($this->_pagination)) {
			$this->_pagination = ED::getPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	public function getTotal()
	{
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Returns the a list of comments created on the site.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getComments()
	{
		if (empty($this->_data)) {

			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Method to build the query for the comments
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function _buildQuery()
	{
		$db = ED::db();
		$query = array();

		$ordering = $this->app->getUserStateFromRequest('com_easydiscuss.tags.filter_order', 'filter_order', 'id', 'cmd');
		$orderingDirection = $this->app->getUserStateFromRequest('com_easydiscuss.tags.filter_order_Dir', 'filter_order_Dir', 'desc', 'word');
		$filter_state = $this->app->getUserStateFromRequest('com_easydiscuss.comments.filter_state', 'filter_state', '', 'word');

		$search = $this->app->getUserStateFromRequest('com_easydiscuss.comments.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EDJString::strtolower($search)));

		$query[] = 'SELECT * FROM ' . $db->nameQuote('#__discuss_comments') . ' AS c';
		$query[] = ' WHERE 1';

		if ($filter_state) {

			if ($filter_state == 'P') {
				$query[] = ' AND c.' . $db->nameQuote('published') . '=' . $db->Quote('1');
			
			} else if ($filter_state == 'U') {
				$query[] = ' AND c.' . $db->nameQuote('published') . '=' . $db->Quote('0');
			}
		}

		if ($search) {

			if (stripos($search, 'COMMENT:') === 0) {
				$query[] = ' AND c.`post_id` = ' . (int) substr($search, 8);
			
			} else {
				$query[] = ' AND (LOWER(c.`title`) LIKE ' . $db->Quote('%' . $search . '%') . ')';
			}
		}

		$query[] = ' ORDER BY ' . $ordering . ' ' . $orderingDirection;

		$query = implode(' ', $query);

		return $query;
	}
}
