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

class EasyDiscussMaintenanceScriptUpdateTableEngine extends EasyDiscussMaintenanceScript
{
	public static $title = "Update database tables engine type";
	public static $description = "This script will attempt to update the existing EasyDiscuss table engine type to follow the default engine type used on the server.";

	public function main()
	{
		$db = ED::db();

		$defaultEngine = $this->getDefaultEngineType();
		$requireConvert = $this->isRequireConvertion();

		if ($defaultEngine != 'myisam' && $requireConvert) {
			$tables = $this->getEDTables();

			if ($tables) {
				try {

					// first we drop the discuss_post_titlecontent index from discuss_post table if exists
					$postIndexes = $db->getTableIndexes('#__discuss_posts');
					if (in_array('discuss_post_titlecontent', $postIndexes)) {
						$query = "ALTER TABLE `#__discuss_posts` DROP INDEX `discuss_post_titlecontent`";
						$db->setQuery($query);
						$db->query();
					}

					foreach ($tables as $table) {
						$query = "alter table " . $db->nameQuote($table) . " engine=InnoDB";
						$db->setQuery($query);
						$db->query();
					}
					
				} catch (Exception $err) {
					// do nothing.
				}
			}
		}

		return true;
	}

	/**
	 * Get default database table engine from mysql server
	 *
	 * @since	5.0
	 * @access	public
	 */
	private function getDefaultEngineType()
	{
		$default = 'myisam';
		$db = ED::db();

		try {

			$query = "SHOW ENGINES";
			$db->setQuery($query);

			$results = $db->loadObjectList();

			if ($results) {
				foreach ($results as $item) {
					if ($item->Support == 'DEFAULT') {
						$default = strtolower($item->Engine);
						break;
					}
				}

				if ($default != 'myisam' && $default != 'innodb') {
					$default = 'myisam';
				}
			}

		} catch (Exception $err) {
			$default = 'myisam';
		}

		return $default;
	}

	/**
	 * Determine if we need to convert myisam engine to innodb
	 *
	 * @since	5.0
	 * @access	public
	 */
	private function isRequireConvertion()
	{
		$require = false;
		$db = ED::db();

		try {
			$query = "SHOW TABLE STATUS WHERE `name` LIKE " . $db->Quote('%_discuss_configs');
			$db->setQuery($query);
			$result = $db->loadObject();

			if ($result) {
				$currentEngine = strtolower($result->Engine);
				if ($currentEngine == 'myisam') {
					$require = true; 
				}
			}

		} catch (Exception $err) {
			// do nothing.
			$require = false;
		}

		return $require;
	}

	/**
	 * Get EasyDiscuss tables names
	 *
	 * @since	5.4
	 * @access	public
	 */
	private function getEDTables()
	{
		$tables = array();

		try {

			// we should not change discuss_thread and discuss_tags table as these two table still need the fulltext index.

			// for now we do the manual work.
			$tables = array(
				'#__discuss_acl',
				'#__discuss_acl_group',
				'#__discuss_assignment_map',
				'#__discuss_attachments',
				'#__discuss_attachments_tmp',
				'#__discuss_badges',
				'#__discuss_badges_history',
				'#__discuss_badges_rules',
				'#__discuss_badges_users',
				'#__discuss_captcha',
				'#__discuss_category',
				'#__discuss_category_acl_item',
				'#__discuss_category_acl_map',
				'#__discuss_comments',
				'#__discuss_configs',
				'#__discuss_conversations',
				'#__discuss_conversations_message',
				'#__discuss_conversations_message_maps',
				'#__discuss_conversations_participants',
				'#__discuss_customfields',
				'#__discuss_customfields_acl',
				'#__discuss_customfields_rule',
				'#__discuss_customfields_value',
				'#__discuss_download',
				'#__discuss_external_groups',
				'#__discuss_favourites',
				'#__discuss_hashkeys',
				'#__discuss_holidays',
				'#__discuss_honeypot',
				'#__discuss_languages',
				'#__discuss_likes',
				'#__discuss_mailq',
				'#__discuss_migrators',
				'#__discuss_notifications',
				'#__discuss_oauth',
				'#__discuss_oauth_posts',
				'#__discuss_optimizer',
				'#__discuss_points',
				'#__discuss_polls',
				'#__discuss_polls_question',
				'#__discuss_polls_users',
				'#__discuss_post_labels',
				'#__discuss_post_types',
				'#__discuss_post_types_category',
				'#__discuss_posts',
				'#__discuss_posts_references',
				'#__discuss_posts_tags',
				'#__discuss_priorities',
				'#__discuss_ranks',
				'#__discuss_ranks_users',
				'#__discuss_ratings',
				'#__discuss_reports',
				'#__discuss_roles',
				'#__discuss_rules',
				'#__discuss_subscription',
				'#__discuss_sync_request',
				'#__discuss_tnc',
				'#__discuss_users',
				'#__discuss_users_banned',
				'#__discuss_users_history',
				'#__discuss_views',
				'#__discuss_votes'
			);

		} catch (Exception $err) {
			// do nothing.
		}

		return $tables;
	}
}