<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once( DISCUSS_ADMIN_INCLUDES . '/post/post.php');

class EasyDiscussCache extends EasyDiscuss
{
	public $posts = null;
	public $categories = null;
	public $tags = null;
	public $polls = null;
	public $replies = null;

	// Local scope
	private $post = array();
	private $category = array();
	private $tag = array();
	private $like = array();
	private $poll = array();

	private $types = array('post', 'category', 'tag');

	public function cachePosts($items, $options = array())
	{
		$_cacheReplies = isset($options['cacheReplies']) ? $options['cacheReplies'] : true;
		$_cacheTags = isset($options['cacheTags']) ? $options['cacheTags'] : true;
		$_cacheCategories = isset($options['cacheCategories']) ? $options['cacheCategories'] : true;

		// lets preload the post's related items here
		$catIds = array();

		foreach ($items as $item) {
			// $post = ED::post($item);

			// just cache the data object into post.
			$this->set($item, 'post');

			// we need to batch load the tags, replies,
			if ($_cacheReplies) {

			}

			if ($_cacheTags) {
                //test
			}

			if ($_cacheCategories) {
				$catIds[] = $item->category_id;
			}
		}

		if ($catIds) {
			$catModel = ED::model('Categories');
			$results = $catModel->preloadCategories($catIds);

			if ($results) {
				foreach ($results as $item) {
					$this->set($item, 'category');
				}
			}
		}
	}


	public function cacheReplies($replies = array())
	{
		if (! $replies) {
			return;
		}


		$repliesIds = array();

		foreach ($replies as $reply) {
			$repliesIds[] = $reply->id;
		}


	}


	/**
	 * Adds a cache for a specific item type
	 *
	 * @since	5.0
	 * @access	public
	 * @param	std object (non jtable object), string
	 * @type 	'post', 'category', 'meta', 'tag', 'author', 'revision', 'team'
	 * @return  boolean
	 */
	public function set($item, $type = 'post')
	{
		// Check if this item already exists.
		$this->{$type}[$item->id] = $item;
	}

	/**
	 * set cache for the object type
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string, string
	 * @type 	'post', 'category', 'meta', 'tag', 'author', 'revision'
	 * @return  std object (non jtable) /  array
	 */
	public function get($id, $type = 'post')
	{
		if (isset($this->$type) && isset($this->{$type}[$id])) {
			return $this->{$type}[$id];
		}

		// There should be a fallback method if the cache doesn't exist
		// return $this->fallback($id, $type);
	}

	// /**
	//  * Retrieves a fallback
	//  *
	//  * @since	5.0
	//  * @access	public
	//  * @param	string
	//  * @return
	//  */
	// public function fallback($id, $type)
	// {
	// 	// if ($type == 'team') {
	// 	// 	$table = EB::table('Teamblog');
	// 	// 	$table->load($id);
	// 	// }

	// 	// if ($type == 'author') {
	// 	// 	$table = EB::user($id);
	// 	// }

	// 	// if ($type != 'team' && $type != 'author') {

	// 	// 	$table = EB::table($type);
	// 	// 	$table->load($id);
	// 	// }

	// 	$table = ED::table($type);
	// 	$table->load($id);

	// 	$this->set($table, $type);

	// 	return $table;
	// }

	/**
	 * check if the cache for the object type exists or not
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string, string
	 * @type 	'post', 'category', 'meta', 'tag', 'author', 'revision'
	 * @return  boolean
	 */
	public function exists($id, $type = 'post')
	{
		if (isset($this->$type) && isset($this->{$type}[$id])) {
			return true;
		}

		return false;
	}

}
