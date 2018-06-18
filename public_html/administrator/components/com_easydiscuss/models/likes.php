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

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelLikes extends EasyDiscussAdminModel
{
	public function isLike($type, $contentId, $userId)
	{
		$db = ED::db();

		$query	= 'SELECT `id` FROM `#__discuss_likes`';
		$query	.= ' WHERE `type` = ' . $db->Quote($type);

		$query	.= ' AND `content_id` = ' . $db->Quote($contentId);
		$query	.= ' AND `created_by` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}

	public function getTotalLikes($postId)
	{

		$db = ED::db();

		$query	= 'SELECT `id` FROM `#__discuss_likes`';
		$query	.= ' WHERE `content_id` = ' . $db->Quote($postId);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!empty($result)) {
			$result = count($result);
		} else {
			$result = 0;
		}
		
		return $result;
	}

	public function updatePostLikes($contentId, $increment = true)
	{
		$operator = ($increment) ? '+' : '-';

		// Now update the post
		$db = ED::db();
		$query = 'UPDATE `#__discuss_posts` SET `num_likes` = `num_likes` ' . $operator . ' 1';
		$query .= ' WHERE `id` = ' . $db->Quote($contentId);

		$db->setQuery($query);
		$db->query();

		// Now update the thread
		$db = ED::db();
		$query = 'UPDATE `#__discuss_thread` SET `num_likes` = `num_likes` ' . $operator . ' 1';
		$query .= ' WHERE `post_id` = ' . $db->Quote($contentId);

		$db->setQuery($query);
		$db->query();
	}

	public function getPostLikes($contentId, $type)
	{
		$db = ED::db();
		
		$displayFormat = $this->config->get('layout_nameformat');
		$displayName = '';

		switch($displayFormat){
			case "name" :
				$displayName = 'a.name';
				break;
			case "username" :
				$displayName = 'a.username';
				break;

			case "nickname" :
			default :
				$displayName = 'IF(c.`nickname` != \'\', c.`nickname`, a.`name`)';
				break;
		}

		$query = 'SELECT a.id as `user_id`, b.id, ' . $displayName . ' AS `displayname`';
		$query .= ' FROM ' . $db->nameQuote('#__discuss_likes') . ' AS b';
		$query .= ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS a';
		$query .= '    on b.created_by = a.id';
		$query .= ' INNER JOIN ' . $db->nameQuote('#__discuss_users') . ' AS c';
		$query .= '    on b.created_by = c.id';
		$query .= ' WHERE b.`type` = '. $db->Quote($type);
		$query .= ' AND b.content_id = ' . $db->Quote($contentId);
		$query .= ' ORDER BY b.id DESC';

		$db->setQuery($query);

		$list = $db->loadObjectList();

		return $list;
	}

	/**
	 * Method used to get user's likes for GDPR download.
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getLikesGDPR($userid, $options = array())
	{
		$db = ED::db();

		$limit = isset($options['limit']) ? $options['limit'] : 20;
		$exclude = isset($options['exclude']) ? $options['exclude'] : array();

		if ($exclude && !is_array($exclude)) {
			$exclude = ED::makeArray($exclude);
		}

		$query = "select a.*";
		$query .= " from `#__discuss_likes` as a";
		$query .= " inner join `#__discuss_posts` as b on a.`content_id` = b.`id`";
		$query .= " where `created_by` = " . $db->Quote($userid);

		if ($exclude) {
			$query .= " and a.`id` NOT IN (" . implode(',', $exclude) . ")";
		}

		$query .= " LIMIT " . $limit;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}
}
