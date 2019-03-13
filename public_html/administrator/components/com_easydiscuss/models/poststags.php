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

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelPostsTags extends EasyDiscussAdminModel
{
	static $_postTags = array();
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function setPostTagsBatch( $ids )
	{
		$db = ED::db();

		if (count($ids) > 0) {

			$query	= 'SELECT a.*, b.`post_id`';
			$query .= ' FROM `#__discuss_tags` AS a';
			$query .= ' LEFT JOIN `#__discuss_posts_tags` AS b';
			$query .= ' ON a.`id` = b.`tag_id`';
			if (count( $ids ) == 1) {
				$query .= ' WHERE b.`post_id` = ' . $db->Quote($ids[0]);

			} else {
				$query .= ' WHERE b.`post_id` IN (' . implode(',', $ids) . ')';
			}

			$query .= ' AND a.`published`=' . $db->Quote( 1 );


			$db->setQuery( $query );
			$result = $db->loadObjectList();

			if (count($result) > 0) {
				foreach ($result as $item) {
					$postId = $item->post_id;

					self::$_postTags[$postId][] = $item;

					// now lets cache the tag;
					$tag = ED::table('Tags');

					// prepare data to bind
					unset($item->post_id);
					$tag->bind($item, array(), false);

					$cache = ED::cache();
					$cache->set($tag, 'tag');
				}
			}

			foreach ($ids as $id) {
				if (! isset(self::$_postTags[$id])) {
					self::$_postTags[$id] = array();
				}
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
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
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
		$db				= DiscussHelper::getDBO();

		$obj			= new stdClass();
		$obj->tag_id	= $tagId;
		$obj->post_id	= $postId;
		$obj->created	= $creationDate;

		return $db->insertObject( '#__discuss_posts_tags' , $obj );
	}

	public function deletePostTag($postId)
	{
		$db	= DiscussHelper::getDBO();

		$query	= ' DELETE FROM ' . $db->nameQuote('#__discuss_posts_tags')
				. ' WHERE ' . $db->nameQuote('post_id') . ' =  ' . $db->quote($postId);

		$db->setQuery($query);
		$result	= $db->Query();

		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}

		return $result;
	}
}
