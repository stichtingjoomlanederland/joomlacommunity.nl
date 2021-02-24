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

class EasyDiscussModelHoneypot extends EasyDiscussAdminModel
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
	 * Returns the a list of honeypot items
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getItems()
	{
		if (empty($this->_data)) {

			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Purges all the logs from honeypot
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function purge()
	{
		$db = $this->db;

		$db->setQuery('DELETE FROM `#__discuss_honeypot`');
		return $db->Query();
	}

	/**
	 * Method to build the query for the comments
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function _buildQuery()
	{
		$db = ED::db();
		$query = array(
			'SELECT * FROM ' . $db->qn('#__discuss_honeypot')
		);

		$query = implode(' ', $query);

		return $query;
	}
}
