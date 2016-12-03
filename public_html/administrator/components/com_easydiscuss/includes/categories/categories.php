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

class EasyDiscussCategories extends EasyDiscuss
{

	public function __construct()
	{
		parent::__construct();
	}

	public function getToolbarCategories($options = array(), $acl = DISCUSS_CATEGORY_ACL_ACTION_VIEW)
	{
		$db = ED::db();

		// search options
		$limit = isset($options['limit']) ? $options['limit'] : 30;

		$gid = array();
		$db = ED::db();

		if ($this->my->guest) {
			$gid = JAccess::getGroupsByUser(0, false);
		} else {
			$gid = JAccess::getGroupsByUser($this->my->id, false);
		}

		$gids = '';
		if (count($gid) > 0) {
			foreach ($gid as $id) {
				$gids .= (empty($gids)) ? $id : ',' . $id;
			}
		}

		$query = "SELECT a.*, COUNT(b.id) AS `post_cnt` FROM " . $db->nameQuote('#__discuss_category') . " AS a";
		$query .= " LEFT JOIN " . $db->nameQuote('#__discuss_posts') . " AS b ON a.`id` = b.`category_id`";
		$query .= " WHERE a.`published` = " . $db->Quote('1');
		$query .= " AND (";
		$query .= " 	( a.`private` = 0 ) OR";
		$query .= " 	( (a.`private` = 1) AND (" . $this->my->id . " > 0) ) OR";
		// joomla groups.
		$query .= " 	( (a.`private` = 2) AND ( (select count(1) from " . $db->nameQuote('#__discuss_category_acl_map') . " as cacl WHERE cacl.`category_id` = a.id AND cacl.`acl_id` = $acl AND cacl.type = 'group' AND cacl.`content_id` in (" . $gids . ")) > 0 ) )";
		$query .= " )";
		$query .= " GROUP BY a.id";
		$query .= " ORDER BY `post_cnt` DESC";
		$query .= " LIMIT $limit";


		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}
}
