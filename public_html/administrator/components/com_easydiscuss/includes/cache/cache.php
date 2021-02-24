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

class EasyDiscussCache
{
	public $posts = null;
	public $categories = null;
	public $tags = null;
	public $polls = null;
	public $replies = null;
	public $labels = [];
	public $posttypes = [];
	public $priorities = [];
	
	// Local scope
	private $post = array();
	private $category = array();
	private $tag = array();
	private $like = array();
	private $poll = array();

	private $types = array('post', 'category', 'tag', 'thread', 'repliesCount', 'attachmentCount', 'pollsCount', 'labels', 'posttypes');

	public function cachePosts($items, $options = array())
	{
		$_cacheReplies = isset($options['cacheReplies']) ? $options['cacheReplies'] : true;
		$_cachePostTags = isset($options['cachePostTags']) ? $options['cachePostTags'] : true;
		$_cacheCategories = isset($options['cacheCategories']) ? $options['cacheCategories'] : true;

		$_cacheLikes = isset($options['cacheLikes']) ? $options['cacheLikes'] : true;
		$_cacheLastReplies = isset($options['cacheLastReplies']) ? $options['cacheLastReplies'] : true;
		$_cacheRepliesCount = isset($options['cacheRepliesCount']) ? $options['cacheRepliesCount'] : true;
		$_cachePolls = isset($options['cachePolls']) ? $options['cachePolls'] : true;

		// lets preload the post's related items here
		$postIds = array();
		$catIds = array();
		$tagIds = array();
		$authorIds = array();

		foreach ($items as $item) {

			// lets check of this post already cached or not.
			// if yes, exclude this post.
			if ($this->exists($item->id, 'post')) {
				continue;
			}

			// just cache the data object into post.
			$this->set($item, 'post');

			// get the post ids to load other items in batch.
			$postIds[] = $item->id;

			$authorIds[] = $item->user_id;
			if (isset($item->last_user_id) && $item->last_user_id) {
				$authorIds[] = $item->last_user_id;
			}

			if ($_cacheCategories) {
				$catIds[] = $item->category_id;
			}
		}

		if ($authorIds) {
			//preload user
			$authorIds = array_unique($authorIds);
			ED::user($authorIds);
		}

		if ($_cachePostTags && $postIds) {
			$postTagsModel = ED::model('PostsTags');
			$postTagsModel->setPostTagsBatch($postIds);
		}

		if ($_cacheCategories && $catIds) {
			$catModel = ED::model('Categories');
			$catIds = array_unique($catIds);
			$results = $catModel->preloadCategories($catIds);

			if ($results) {
				foreach ($results as $item) {

					$category = ED::table('Category');
					$category->bind($item);

					$this->set($category, 'category');
				}
			}
		}
	}

	/**
	 * Adds a cache for a specific item type
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function set($item, $type = 'post', $key = 'id')
	{
		$this->{$type}[$item->$key] = $item;
	}

	/**
	 * set cache for the object type
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function get($id, $type = 'post')
	{
		if (isset($this->$type) && isset($this->{$type}[$id])) {
			return $this->{$type}[$id];
		}

		// There should be a fallback method if the cache doesn't exist
		// return $this->fallback($id, $type);
	}

	/**
	 * Retrieves a fallback
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function fallback($id, $type)
	{
		$table = ED::table($type);
		$table->load($id);

		$this->set($table, $type);

		return $table;
	}

	/**
	 * check if the cache for the object type exists or not
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function exists($id, $type = 'post')
	{
		if (isset($this->$type) && isset($this->{$type}[$id])) {
			return true;
		}

		return false;
	}

}
