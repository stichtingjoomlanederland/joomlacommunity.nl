<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/includes/maintenance/dependencies.php');

class EasyDiscussMaintenanceScriptSyncVotes extends EasyDiscussMaintenanceScript
{
	public static $title = "Sync Votes";
	public static $description = "Synchronize posts and replies votes properly so that they are tally with votes table.";

	public function main()
	{
		// Checks whether there is votes available or not.
		if (!$this->checkExistingVotes()) {
			return true;
		}

		$db = ED::db();

		// Get all posts.
		$query = "SELECT `id` FROM `#__discuss_posts`";

		$db->setQuery($query);
		$posts = $db->loadObjectList();

		// If there is no post, then no point to continue.
		if (empty($posts)) {
			return true;
		}

		foreach ($posts as $post) {

			$voteQuery = "SELECT COUNT(1) FROM `#__discuss_votes` WHERE `post_id` = " . $db->Quote($post->id);
			$db->setQuery($voteQuery);
			$vote = $db->loadResult();

			$num_negvoteQuery = "SELECT COUNT(1) FROM `#__discuss_votes` WHERE `post_id` = " . $db->Quote($post->id) . " AND `value` = " . $db->Quote('-1');
			$db->setQuery($num_negvoteQuery);
			$num_negvote = $db->loadResult();

			$sum_totalvoteQuery = "SELECT SUM(`value`) FROM `#__discuss_votes` WHERE `post_id` = " . $db->Quote($post->id);
			$db->setQuery($sum_totalvoteQuery);
			$sum_totalvote = $db->loadResult();

			$query = array();

			$query[] = "UPDATE `#__discuss_posts` a JOIN `#__discuss_thread` b";
			$query[] = "ON a.`id` = b.`post_id`";
			$query[] = "SET a.`vote` = " . $db->Quote($vote) . ", b.`vote` = " . $db->Quote($vote);
			$query[] = ", a.`num_negvote` = " . $db->Quote($num_negvote) . ", b.`num_negvote` = " . $db->Quote($num_negvote);
			$query[] = ", a.`sum_totalvote` = " . $db->Quote($sum_totalvote) . ", b.`sum_totalvote` = " . $db->Quote($sum_totalvote);
			$query[] = "WHERE a.`id` = " . $db->Quote($post->id);

			$query = implode(' ', $query);
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}

	public function checkExistingVotes()
	{
		$db = ED::db();

		$query = "SELECT COUNT(1) FROM `#__discuss_votes`";

		$db->setQuery($query);
		$result = $db->loadResult();

		if (!$result) {
			return false;
		}

		return true;
	}
}