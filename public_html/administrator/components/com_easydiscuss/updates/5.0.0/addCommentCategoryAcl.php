<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/includes/maintenance/dependencies.php');

class EasyDiscussMaintenanceScriptAddCommentCategoryAcl extends EasyDiscussMaintenanceScript
{
	public static $title = "Add Comment in Category ACL";
	public static $description = "Add Comment in Category ACL";

	public function main()
	{
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT `id` FROM ' . $db->nameQuote('#__discuss_category_acl_item');
		$query[] = 'WHERE ' . $db->nameQuote('action') . ' = ' . $db->Quote('comment');

		$query = implode(' ' , $query);

		$db->setQuery($query);
		$aclId = $db->loadResult();

		if (!$aclId) {
			// this is unlikely to happen as the acl item should be added in 
			// administrator/components/com_easydiscuss/queries/categories.sql
			// but we might still need to create for our internal dev and we need to hardcode the id here
			// so that it will tally with the id added from the categories.sql

			// we need to hardcode the acl id here.
			$aclId = '7';

			$query = array();
			$query[] = 'INSERT INTO ' . $db->nameQuote('#__discuss_category_acl_item');
			$query[] = '(' . $db->nameQuote('id') . ', ' . $db->nameQuote('action') . ', ' . $db->nameQuote('description') . ', ' . $db->nameQuote('published') . ', ' . $db->nameQuote('default') . ') VALUES';
			$query[] = '(' . $db->Quote($aclId) . ', ' . $db->Quote('comment') . ', ' . $db->Quote('can add comment in this category.') . ', ' . $db->Quote(1) . ', ' . $db->Quote(1) . ')';

			$query = implode(' ' , $query);
			$db->setQuery($query);
			$state = $db->query();

			if (!$state) {
				$aclId = '';
			}
		}

		if ($aclId) {
			// now we need to this new category permission into each Joomla group on each category.
			$groups = ED::getJoomlaUserGroups();
			if ($groups) {

				// now we get the acl for add_comment
				$query = "select b.`content_id`, b.`status`";
				$query .= " from `#__discuss_acl` as a";
				$query .= " inner join `#__discuss_acl_group` as b on a.`id` = b.`acl_id`";
				$query .= " where b.`type` = " . $db->Quote('group');
				$query .= " and a.`action` = " . $db->Quote('add_comment');

				$db->setQuery($query);
				$acls = $db->loadObjectList('content_id');

				foreach ($groups as $item) {

					$gid = $item->id;

					if (isset($acls[$gid])) {

						// check if add_comment is allowed in this joomla group
						$allowed = isset($acls[$gid]->status) && $acls[$gid]->status ? true : false;

						if ($allowed) {

							// need to make sure there will be no duplicate records on this permission.
							$query = "delete from `#__discuss_category_acl_map`";
							$query .= " where `acl_id` = " . $db->Quote($aclId);
							$query .= " and `type` = " . $db->Quote('group');
							$query .= " and `content_id` = " . $db->Quote($gid);
							$db->setQuery($query);
							$db->query();

							// lets add the mapping into each category.
							$query = "insert into `#__discuss_category_acl_map` (`category_id`, `acl_id`, `type`, `content_id`, `status`)";
							$query .= " select `id`, " . $db->Quote($aclId) . ", 'group', " . $db->Quote($gid) . ", 1 from `#__discuss_category`";
							$db->setQuery($query);
							$db->query();
						}
					}
				}
			}

		}
		
		return true;
	}
}