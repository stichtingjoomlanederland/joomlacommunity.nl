<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once dirname( __FILE__ ) . '/model.php';


class EasyDiscussModelDownload extends EasyDiscussAdminModel
{
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easydiscuss.download.limit', 'limit', ED::getListLimit());
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves download requests
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getRequests($options = array())
	{
		$db = ED::db();
		$query = array();

		$query[] = 'SELECT * FROM ' . $db->qn('#__discuss_download');

		// Set the total number of items.
		$query = implode(' ', $query);


		$countQuery = str_replace('SELECT * FROM', 'SELECT COUNT(1) FROM', $query);
		$db->setQuery($countQuery);
		$this->_total = $db->loadResult();

		// Get the list of users
		$db->setQuery($query);
		$this->_data = $db->loadObjectList();
		$rows = $this->_data;

		if (!$rows) {
			return array();
		}

		$requests = array();

		foreach ($rows as $row) {
			$request = ED::table('Download');
			$request->bind($row);

			$requests[] = $request;
		}

		return $requests;
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @since  4.1
	 * @access public
	 */
	public function getTotal()
	{
		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @since  4.1
	 * @access public
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
	 * Method to retrieve records pending for cron processing.
	 *
	 * @since 4.1
	 * @access public
	 */
	public function getCronDownloadReq($max = 1)
	{
		$db = ED::db();

		$query = "select * from `#__discuss_download`";
		$query .= " where `state` IN (" . $db->Quote(DISCUSS_DOWNLOAD_REQ_NEW) . ',' . $db->Quote(DISCUSS_DOWNLOAD_REQ_PROCESS) . ")";
		$query .= " order by `id`";
		$query .= " limit 0," . $max;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}


	/**
	 * Method to retrieve records pending for cron processing.
	 *
	 * @since 4.1
	 * @access public
	 */
	public function getExpiredRequest($max = 10)
	{
		$db = ED::db();
		$config = ED::config();

		$days = $config->get('gdpr_archive_expiry', 14);
		$now = ED::date()->toMySQL();

		$query = "select a.* from `#__discuss_download` as a";
		$query .= " where a.`state` = " . $db->Quote(DISCUSS_DOWNLOAD_REQ_READY);
		$query .= " and a.`created` <= DATE_SUB(" . $db->Quote($now) . ", INTERVAL " . $days . " DAY)";
		$query .= " order by `id`";

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Removes all download requests and delete the files
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function purgeRequests()
	{
		$db = ED::db();
		$query = 'DELETE FROM ' . $db->qn('#__discuss_download');

		$db->setQuery($query);
		$db->query();

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$folders = JFolder::folders(DISCUSS_GDPR_DOWNLOADS, '.', false, true);

		if ($folders) {
			foreach ($folders as $folder) {
				JFolder::delete($folder);
			}
		}

		$files = JFolder::files(DISCUSS_GDPR_DOWNLOADS, '.', false, true);

		if ($files) {
			foreach ($files as $file) {
				JFile::delete($file);
			}
		}

		return true;
	}
}
