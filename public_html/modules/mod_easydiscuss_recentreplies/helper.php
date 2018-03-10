<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

class modRecentRepliesHelper
{
	public static function getData($params)
	{
		$db = ED::db();
		$limit = (int) $params->get('count', 10);
		$catid = intval($params->get('category', 0));
		$catfil	= (int) $params->get('category_option', 0);

		if ($limit == 0) {
			$limit = '';
		} else {
			$limit = ' LIMIT 0,' . $limit;
		}

		if (!$catfil || $catid == 0) {
			$catid = '';
		} else {
			$catid = ' AND a.`category_id` = '.$db->quote($catid) . ' ';
		}

		$query	= 'SELECT a.*, a.`title` AS `post_title`';
		$query	.= ' FROM ' . $db->nameQuote( '#__discuss_thread' ) . ' AS a';
		$query	.= ' LEFT JOIN ' . $db->nameQuote( '#__discuss_posts' ) . ' AS b';
		$query 	.= '	ON b.' . $db->nameQuote( 'parent_id' ) . '= a.' . $db->nameQuote('post_id');
		$query 	.= '	AND b.' . $db->nameQuote( 'published' ) . '=' . $db->Quote(1);
		$query 	.= ' WHERE a.' . $db->nameQuote( 'published' ) . '=' . $db->Quote(1);
		$query 	.= $catid;
		$query 	.= ' GROUP BY a.' . $db->nameQuote('id');
		$query	.= ' ORDER BY a.' . $db->nameQuote('replied') . ' DESC';
		$query	.= $limit;

		$db->setQuery($query);

		$results	= $db->loadObjectList();

		if (!$results) {
			return false;
		}

		$posts = array();

		// preload users
		$userIds = array();
		foreach ($results as $row) {
			$userIds[] = $row->user_id;
		}

		ED::user($userIds);

		foreach($results as $row)
		{
			// Format the posts
			$post = ED::post($row->post_id);

			// We also need to check if the viewer can view replies from this post's category
			$category = $post->getCategory();

			if (!$category->canViewReplies()) {
			    continue;
			}

			// Get the last replier for the particular post.
			$db	= ED::db();
			$query = 'SELECT `id` as replyId, `user_id`, `content` FROM `#__discuss_posts` WHERE ' . $db->nameQuote('parent_id') . ' = ' . $db->Quote($row->post_id) . ' ORDER BY '  . $db->nameQuote('created') . ' DESC LIMIT 1';
			$db->setQuery($query);
			$result = $db->loadObject();

			if (!$result) {
				continue;
			}

			$post->user_id = $result->user_id;
			$post->last_reply_id = $result->replyId;

			$content = $result->content;

			$limit = $params->get('reply_content_truncation', 50);


			if ($limit && strlen($content) > $limit) {
				$content = substr(strip_tags($result->content), 0, $params->get('reply_content_truncation', 50));
				$content = $content . JText::_('COM_EASYDISCUSS_ELLIPSES');
			}

			$profile = ED::user($post->user_id);

			$post->user = $profile;
			$post->content = ED::parser()->bbcode($content);
			$post->content = ED::parser()->filter($content);

			$posts[] = $post;
		}

		// Append profile objects to the result
		return $posts;
	}
}
