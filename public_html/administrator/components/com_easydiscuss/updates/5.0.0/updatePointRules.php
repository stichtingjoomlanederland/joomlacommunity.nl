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

class EasyDiscussMaintenanceScriptUpdatePointRules extends EasyDiscussMaintenanceScript
{
	public static $title = "Updating points and rules typo";
	public static $description = "Updating points and rules title item typo in database.";

	public function main()
	{
		$db = ED::db();

		$query = 'UPDATE `#__discuss_rules` as a, `#__discuss_points` as b SET a.`title` = "Read a discussion", b.`title` = "Read a discussion" WHERE a.`id` = "8" AND b.`id` = "8"';

		$db->setQuery($query);
		$db->execute();

		return true;
	}
}