<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

ED::import('admin:/tables/table');

class DiscussSyncRequest extends EasyDiscussTable
{
	public $id = null;
	public $command = null;
	public $params = null;
	public $total = null;
	public $created = null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db)
	{
		parent::__construct( '#__discuss_sync_request' , 'id' , $db );
	}

	/**
	 * Process sync request.
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function process()
	{
		$supportedCommands = array(DISCUSS_SYNC_THREAD_REPLY);

		if (!in_array($this->command, $supportedCommands)) {
			// skip this items.
			return true;
		}

		$state = true;

		switch ($this->command) 
		{
			case DISCUSS_SYNC_THREAD_REPLY:
				// sync thread's reply counts.
				$params = new JRegistry();
				$params->loadString($this->params);

				$userIds = $params->get('user_id', array());
				$userIds = ED::makeArray($userIds);

				if ($userIds) {

					$keys = array_keys($userIds);
					$key = array_shift($keys);

					$currentUser = $userIds[$key];

					$result = $this->syncThreadReplyCount($currentUser);

					if ($result === true) {
						// this user has nothing more to process.
						unset($userIds[$key]);
					}

					if (is_array($result)) {
						// lets add back this user into array;
						$userIds[$key] = $result;
					}

					// update the user lists
					$params->set('user_id', $userIds);
				}
				
				$this->params = $params->toString();
				$this->total = count($userIds);
				$state = $this->store();

				break;
			default:
				// do nothing
				break;
		}

		return $state;
	}


	/**
	 * sync thread's replies counts.
	 *
	 * @since	4.1
	 * @access	public
	 */
	private function syncThreadReplyCount($data)
	{
		$max = 50;
		$db = ED::db();

		$start = $data['current'];
		$userId = $data['id'];
		$total = $data['total'];


		$query = "select distinct a.`thread_id` from `#__discuss_posts` as a";
		$query .= " inner join `#__discuss_thread` as b on a.`thread_id` = b.`id`";
		$query .= " where a.`published` = 1";
		$query .= " and a.user_id = " . $db->Quote($userId);
		$query .= " limit $start, $max";

		$db->setQuery($query);
		$results = $db->loadColumn();

		if (!$results) {
			// if nothing to process, just return bool true.
			return true;
		}

		$threadIds = implode(',', $results);

		// re-sync num_replies into thread table.
		$query = "UPDATE `#__discuss_thread` AS a";
		$query .= " INNER JOIN `#__discuss_posts` AS b ON a.`id` = b.`thread_id`";
		$query .= " SET a.`num_replies` = (";
		$query .= "      SELECT count(1) FROM `#__discuss_posts` AS b1";
		$query .= "          LEFT JOIN `#__users` AS uu ON b1.`user_id` = uu.`id`"; 
		$query .= "         WHERE b1.`thread_id` = a.`id` AND b1.`published` = 1 AND b1.`parent_id` > 0";
		$query .= "         AND (uu.`block` = 0 or uu.`id` IS NULL)";
		$query .= " )";
		$query .= " where a.`id` IN (" . $threadIds . ")";

		$db->setQuery($query);
		$state = $db->query();

		if ($state) {

			$query = "update `#__discuss_thread` as a";
			$query .= " inner join `#__discuss_posts` as b";
			$query .= "  on a.`id` = b.`thread_id`";
			$query .= "    set a.`last_user_id` = b.`user_id`,";
			$query .= "    a.`last_poster_name` = b.`poster_name`,";
			$query .= "    a.`last_poster_email` = b.`poster_email`";
			$query .= " where a.`id` IN (" . $threadIds . ")";
			$query .= " and b.`id` = (";
			$query .= " 	select max(c.`id`) from `#__discuss_posts` as c";
			$query .= "			left join `#__users` as uu on c.`user_id` = uu.`id`";
			$query .= " 	where c.`thread_id` = a.`id`";
			$query .= "			and c.id != a.post_id";
			$query .= "			and (uu.`block` = 0 or uu.`id` is null)";
			$query .= ")";

			$db->setQuery($query);
			$state = $db->query();
		}


		// if all good, lets check if there is any more items to process
		// for this user or not.

		if (count($results) < $max) {
			// no more next batch for processing.
			return true;
		}

		// update the next start
		$data['current'] = $start + $max;
		return $data;
	}

}
