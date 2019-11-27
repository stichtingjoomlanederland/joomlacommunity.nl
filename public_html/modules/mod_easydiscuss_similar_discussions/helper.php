<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

class EDSimilarDiscussions
{
	public static function getSimilarPosts($postId, $params)
	{
		$db = ED::db();
		$config = ED::config();

		$post = ED::table('Posts');
		$post->load($postId);

		$title = $post->title;
		$categoryId = $post->category_id;
		$limit = (int)$params->get('count', '5');

		$query = $result = array();

		// If the title is empty, then just return empty array.
		if (empty($title)) {
			return $result;
		}

		// Clean search key
		$search = trim($title);
		$search = preg_replace("/(?![.=$'â‚-])\p{P}/u", "", $search);
		$numwords = explode(' ', $search);
		$fulltextType = ' WITH QUERY EXPANSION';

		if ($config->get('system_query') == 'count') {
			$fulltextType = ' IN BOOLEAN MODE';
		}

		$query[] = 'SELECT COUNT(1) AS `totalcnt`';
		$query[] = 'FROM ' . $db->nameQuote('#__discuss_thread') . ' AS a';
		$query[] = 'WHERE MATCH(a.`title`, a.`content`) AGAINST (' . $db->Quote($search) . $fulltextType . ')';
		$query[] = 'AND a.`published` = ' . $db->Quote('1');

		if ($params->get('resolved_only', 0)) {
			$query[] = 'AND a.`isresolve` = 1';
		}

		$query[] = 'AND a.`id` != ' . $db->Quote($postId);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$totalItem  = $db->loadResult();

		$discussions = array();

		if ($totalItem) {

			$date = ED::date();

			// now try to get the main topic
			$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created`) as `noofdays`, ';
			$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', a.`created`) as `timediff`';
			$query .= ', a.`id`,  a.`title`, a.`content`';
			$query .= ', MATCH(a.`title`, a.`content`) AGAINST (' . $db->Quote($search) . $fulltextType . ') AS score';
			$query .= ', b.`id` as `category_id`, b.`title` as `category_name`, a.`post_id`';

			$query .= ' FROM `#__discuss_thread` as a';
			$query .= ' inner join `#__discuss_category` as b ON a.category_id = b.id';
			$query .= " inner join " . $db->qn('#__discuss_posts') . " as c on a.post_id = c.id";

			// exclude blocked users #788
			$query .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";

			$query .= ' WHERE MATCH(a.`title`,a.`content`) AGAINST (' . $db->Quote($search) . $fulltextType . ')';
			$query .= ' AND a.`published` = ' . $db->Quote('1');

			if ($params->get('resolved_only', 0)) {
				$query .= ' AND a.`isresolve` = 1';
			}

			$query .= ' and a.`post_id` != ' . $db->Quote($postId);
			$query .= ' and c.`parent_id` != ' . $db->Quote($postId);

			// exclude blocked users #788
			$query .= " AND (uu.`block` = 0 OR uu.`id` is null)";

			$query .= ' ORDER BY score DESC';
			$query .= ' LIMIT ' . $limit;

			$db->setQuery($query);
			$result = $db->loadObjectList();

			foreach ($result as $row) {

				$durationObj = new stdClass();
				$durationObj->daydiff = $row->daydiff;
				$durationObj->timediff = $row->timediff;

				$row->content = ED::parser()->bbcode($row->content);
				$row->title = ED::parser()->filter($row->title);
				$row->content = strip_tags(html_entity_decode(ED::parser()->filter($row->content)));
				$row->duration = ED::getDurationString($durationObj);
				$row->permalink = EDR::getPostRoute($row->post_id);

				$discussions[] = $row;
			}
		}

		return $discussions;
	}
}
