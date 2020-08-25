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

require_once(DISCUSS_ADMIN_ROOT . '/includes/maintenance/dependencies.php');

class EasyDiscussMaintenanceScriptSyncThreadReplyCount extends EasyDiscussMaintenanceScript
{
	public static $title = "Sync Thread Reply Count";
	public static $description = "Synchronise thread's reply counts.";

	public function main()
	{
		$db = ED::db();
		$max = 50;

		$query = "select distinct a.`thread_id` from `#__discuss_posts` as a";
		$query .= " inner join `#__discuss_thread` as b on a.`thread_id` = b.`id`";
		$query .= " inner join `#__users` as u on a.`user_id` = u.`id`";
		$query .= " where a.`published` = 1";
		$query .= " and u.`block` = 1";
		$query .= " and b.`num_replies` > 0";
		$query .= " limit $max";

		$db->setQuery($query);
		$items = $db->loadColumn();

		if ($items) {

			$threadIds = implode(',', $items);

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
		}
		
		return true;
	}
}