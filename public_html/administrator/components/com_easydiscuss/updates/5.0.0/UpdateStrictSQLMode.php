<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/includes/maintenance/dependencies.php');

class EasyDiscussMaintenanceScriptUpdateStrictSQLMode extends EasyDiscussMaintenanceScript
{
	public static $title = "Update strict SQL MODE for Joomla 4.";
	public static $description = "Dropping DEFAULT on NOT NULL column definition for Joomla 4 strict SQL mode.";

	public function main()
	{
		$db = ED::db();

		$queries = array();
		$queries[] = "ALTER TABLE `#__discuss_category` MODIFY `description` TEXT";

		$queries[] = "ALTER TABLE `#__discuss_badges` ALTER `points_threshold` SET DEFAULT '0'";

		$queries[] = "ALTER TABLE `#__discuss_badges_users` MODIFY `custom` TEXT";

		$queries[] = "ALTER TABLE `#__discuss_notifications` MODIFY `favicon` TEXT";
		
		$queries[] = "ALTER TABLE `#__discuss_posts` 
						ALTER `poster_name` SET DEFAULT '', 
						ALTER `poster_email` SET DEFAULT ''
					";

		$queries[] = "ALTER TABLE `#__discuss_thread` 
						ALTER `poster_name` SET DEFAULT '',
						ALTER `poster_email` SET DEFAULT '',
						ALTER `last_poster_name` SET DEFAULT '',
						ALTER `last_poster_email` SET DEFAULT ''
					";

		$queries[] = "ALTER TABLE `#__discuss_assignment_map` MODIFY `description` TEXT";

		$queries[] = "ALTER TABLE `#__discuss_users` 
						MODIFY `location` TEXT,
						MODIFY `signature` TEXT,
						MODIFY `edited` TEXT,
						MODIFY `auth` VARCHAR(255) NOT NULL DEFAULT ''
					";

		$queries[] = "ALTER TABLE `#__discuss_users_history` 
						MODIFY `title` TEXT,
						MODIFY `command` TEXT
					";

		$queries[] = "ALTER TABLE `#__discuss_users_banned` MODIFY `reason` TEXT";

		$queries[] = "ALTER TABLE `#__discuss_oauth` 
						MODIFY `request_token` TEXT,
						MODIFY `access_token` TEXT,
						MODIFY `message` TEXT,
						MODIFY `params` TEXT
					";

		foreach ($queries as $query) {
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}
}