<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/model.php');

class EasyDiscussModelPostsTags extends EasyDiscussAdminModel
{
	static $_postTags = array();

	public function setPostTagsBatch($ids)
	{
		if (!$ids || !count($ids)) {
			return;
		}

		$db = ED::db();

		$query = 'SELECT a.*, b.`post_id`';
		$query .= ' FROM `#__discuss_tags` AS a';
		$query .= ' LEFT JOIN `#__discuss_posts_tags` AS b';
		$query .= ' ON a.`id` = b.`tag_id`';

		$multipleIds = count($ids) > 1;

		if (!$multipleIds) {
			$query .= ' WHERE b.`post_id` = ' . $db->Quote($ids[0]);
		} 

		if ($multipleIds) {
			$query .= ' WHERE b.`post_id` IN (' . implode(', ', $ids) . ')';
		}

		$query .= ' AND a.`published` = ' . $db->Quote(1);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (count($result) > 0) {
			foreach ($result as $item) {
				$postId = $item->post_id;

				self::$_postTags[$postId][$item->id] = $item;

				// Now lets cache the tags.
				$tag = ED::table('Tags');

				// Prepare data to bind.
				unset($item->post_id);
				$tag->bind($item);

				$cache = ED::cache();
				$cache->set($tag, 'tag');
			}
		}

		// If the provided ids do not have tags associated, we'll return empty array.
		foreach ($ids as $id) {
			if (!isset(self::$_postTags[$id])) {
				self::$_postTags[$id] = [];
			}
		}
	}

	/*
	 * method to get post tags.
	 *
	 * param postId - int
	 * return object list
	 */
	public function getPostTags($postId)
	{
		if (isset(self::$_postTags[$postId])) {
			return self::$_postTags[ $postId ];
		}

		$db = ED::db();

		$query	= 'SELECT a.*';
		$query .= ' FROM `#__discuss_tags` AS a';
		$query .= ' LEFT JOIN `#__discuss_posts_tags` AS b';
		$query .= ' ON a.`id` = b.`tag_id`';
		$query .= ' WHERE b.`post_id` = '.$db->Quote($postId);
		$query .= ' AND a.`published`=' . $db->Quote( 1 );

		$db->setQuery($query);

		if($db->getErrorNum() > 0) {
			throw ED::exception($db->getErrorMsg() . $db->stderr(), ED_MSG_ERROR);
		}

		$result	= $db->loadObjectList();

		if ($result) {
			foreach ($result as $item) {

				$tag = ED::table('Tags');
				$tag->bind($item);

				$cache = ED::cache();
				$cache->set($tag, 'tags');
			}
		}

		self::$_postTags[ $postId ] = $result;
		return $result;

	}

	public function add( $tagId , $postId , $creationDate )
	{
		$db				= ED::db();

		$obj			= new stdClass();
		$obj->tag_id	= $tagId;
		$obj->post_id	= $postId;
		$obj->created	= $creationDate;

		return $db->insertObject( '#__discuss_posts_tags' , $obj );
	}

	public function deletePostTag($postId)
	{
		$db	= ED::db();

		$query	= ' DELETE FROM ' . $db->nameQuote('#__discuss_posts_tags')
				. ' WHERE ' . $db->nameQuote('post_id') . ' =  ' . $db->quote($postId);

		$db->setQuery($query);
		$result	= $db->Query();

		if ($db->getErrorNum()) {
			throw ED::exception($db->stderr(), ED_MSG_ERROR);
		}

		return $result;
	}
}
