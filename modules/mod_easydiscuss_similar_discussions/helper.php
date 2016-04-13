<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
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

		$query[] = 'SELECT COUNT(a.`id`) AS `totalcnt`, SUM(MATCH(a.`title`, a.`content`) AGAINST(' . $db->Quote($search) . $fulltextType . ')) AS totalscore';
		$query[] = 'FROM ' . $db->nameQuote('#__discuss_thread') . ' AS a';
		$query[] = 'WHERE MATCH(a.`title`, a.`content`) AGAINST (' . $db->Quote($search) . $fulltextType . ')';
		$query[] = 'AND a.`published` = ' . $db->Quote('1');

		if ($params->get('resolved_only', 0)) {
			$query[] = 'AND a.`isresolve` = 1';
		}

		$query[] = 'AND a.`id` != ' . $db->Quote($postId);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$totalData = $db->loadObject();

		$totalScore = $totalData->totalscore;
		$totalItem  = round($totalData->totalcnt);

		$result = array();

		if ($totalItem) {

			$date = ED::date();

			// now try to get the main topic
			$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created`) as `noofdays`, ';
			$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', a.`created`) as `timediff`';
			$query .= ', a.`id`,  a.`title`, a.`content`, MATCH(a.`title`, a.`content`) AGAINST (' . $db->Quote($search) . $fulltextType . ') AS score';
			$query .= ', b.`id` as `category_id`, b.`title` as `category_name`';

			$query .= ' FROM `#__discuss_thread` as a';
			$query .= ' inner join `#__discuss_category` as b ON a.category_id = b.id';
			$query .= ' WHERE MATCH(a.`title`,a.`content`) AGAINST (' . $db->Quote($search) . $fulltextType . ')';
			$query .= ' AND a.`published` = ' . $db->Quote('1');

			if ($params->get('resolved_only', 0)) {
				$query .= ' AND a.`isresolve` = 1';
			}

			$query .= ' and a.`post_id` != ' . $db->Quote($postId);
			$query .= ' ORDER BY score DESC';
			$query .= ' LIMIT ' . $limit;

			// echo str_replace('#_', 'jos', $query);exit;

			$db->setQuery($query);
			$result = $db->loadObjectList();
			$discussions = array();

			foreach ($result as $row) {
				$score = round($row->score) * 100 / $totalScore;
				$row->score = $score;

				$durationObj = new stdClass();
				$durationObj->daydiff = $row->daydiff;
				$durationObj->timediff = $row->timediff;

				$row->content = ED::parser()->bbcode($row->content);
				$row->title = ED::wordFilter($row->title);
				$row->content = strip_tags(html_entity_decode(ED::wordFilter($row->content)));
				$row->duration = ED::getDurationString($durationObj);

				$discussions[] = $row;
			}
		}

		return $discussions;
	}

}
