<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ED::import('admin:/tables/table');

class DiscussReport extends EasyDiscussTable
{
	public $id = null;
	public $post_id	= null;
	public $reason = null;
	public $created_by = null;
	public $created	= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__discuss_reports', 'id', $db);
	}

	public function load($id = null, $reset = false){
		return parent::load($id, $reset);
	}

	public function bind($post, $isPost = false)
	{
		parent::bind($post);

		if ($isPost) {
			$date = ED::date();

			//replace a url to link
			$reason = $post['reporttext'];

			$filter = JFilterInput::getInstance();
			$this->reason = $filter->clean($reason);

			if (empty($this->created) || $this->created == '0000-00-00 00:00:00') {
				$this->created = $date->toMySQL();
			}

			$this->created_by = $post['created_by'];
		}

		return true;
	}

	public function getReportCount()
	{
		$db = ED::db();

		$query  = 'SELECT COUNT(1) FROM `#__discuss_reports` WHERE `post_id` = ' . $db->Quote($this->post_id);
		$db->setQuery($query);

		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	public function markPostReport()
	{
		$db = ED::db();

		$query	= 'UPDATE `#__discuss_posts` SET `isreport` = ' . $db->Quote('1');
		$query	.= ' WHERE `id` = ' . $db->Quote($this->post_id);

		$db->setQuery($query);
		$db->query();

		return true;
	}

	public function isPostReported()
	{
		$db = ED::db();

		$query = 'SELECT `isreport` FROM `#__discuss_posts`';
		$query .= ' WHERE `id` = ' . $db->Quote($this->post_id);

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

}
