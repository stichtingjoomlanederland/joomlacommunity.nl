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
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/base.php');

class EasyDiscussMigratorDiscussions extends EasyDiscussMigratorBase
{
	public function migrate()
	{
		$config = ED::config();

		// Get the total number of Discussions items
		$total = $this->getTotalDiscussionsPosts();

		// Get all Discussions Posts that is not yet migrated
		$items = $this->getDiscussionsPosts(null, 10);

		// Determines if there is still items to be migrated
		$balance = $total - count($items);

		$status = '';

		// If there's nothing to load just skip this
		if (!$items) {
			return $this->ajax->resolve('noitem');
		}

		foreach ($items as $item) {
			
			$post = ED::post();

			// Map the item to discuss post
			$state = $this->mapDiscussionsItem($item, $post);

			// If everything okay, migrate the replies
			if ($state) {
				$this->mapDiscussionsItemChilds($item, $post);
			}

			$status .= JText::_('COM_EASYDISCUSS_MIGRATOR_MIGRATED_DISCUSSIONS') . ': ' . $item->id . JText::_('COM_EASYDISCUSS_MIGRATOR_EASYDISCUSS') . ': ' . $post->id . '<br />';
		}

		$hasmore = false;

		if ($balance) {
			$hasmore = true;
		}

		return $this->ajax->resolve($hasmore, $status);

	}

	public function mapDiscussionsItemChilds($dItem, $parent)
	{
		// try to get the childs
		$items = $this->getDiscussionsPosts($dItem, null);

		if (!$items) {
			return false;
		}

		foreach ($items as $dChildItem) {
			
			// Load the post library
			$post = ED::post($parent->id);

			// If this post is not a question, we'll need to get the parent id.
			if (!$post->isQuestion()) {
				$parent = $post->getParent();

				// Re-assign $post to be the parent.
				$post = ED::post($parent->id);
			}

			// For contents, we need to get the raw data.
	        $data['content'] = $dChildItem->message;
	        $data['parent_id'] = $post->id;
	        $data['user_id'] = $dChildItem->userid;

	        $saveOptions = array('migration' => true);

	        // Load the post library
	        $post = ED::post();
	        $post->bind($data);

	        // Try to save the post now
	        $state = $post->save($saveOptions);
		}
	}


	public function mapDiscussionsItem($item, &$post, $parent = null)
	{
		$config = ED::config();

		$data = array();

		$lastreplied = (isset($item->threadlastreplied))? $item->threadlastreplied : $item->time;

		// Create category if this item's category does not exist on the site
		$categoryId = $this->migrateCategory($item);

		$data['content'] = $item->message;
		$data['title'] = $item->subject;
		$data['category_id'] = $categoryId;
		$data['user_id'] = $item->user_id;
		$data['user_type'] = DISCUSS_POSTER_MEMBER;
		$data['hits'] = $item->hits;
		$data['created'] = ED::date($item->time)->toMySQL();
		$data['modified'] = ED::date($item->time)->toMySQL();
		$data['replied'] = ED::date($lastreplied)->toMySQL();
		$data['poster_name'] = $item->name;
		$data['ip'] = $item->ip;
		$data['content_type'] = 'bbcode';
		$data['parent_id'] = 0;
		$data['islock'] = $item->locked;
		$data['poster_email'] = $item->email;

		$state = ($item->hold == 0)? DISCUSS_ID_PUBLISHED : DISCUSS_ID_UNPUBLISHED;
		$data['published'] = $state;

		if (!$item->user_id) {
			$data['user_type'] = DISCUSS_POSTER_GUEST;
		}

		$saveOptions = array('migration' => true);

		$post->bind($data);
        $post->save($saveOptions);

        // Add this to migrators table
		$this->added('com_discussions', $post->id, $item->id, 'post');

		return true;
	}

	
	public function migrateCategory($item)
	{
		// By default, the category id is 1 because EasyBlog uses the first category as uncategorized
		$default = 1;

		// If there's no category assigned in this item
		if (!$item->cat_id) {
			return $default;
		}

		// Get Discussions's category
		$discussionsCategory = $this->getDiscussionsCategory($item->cat_id);

		// Determine if this category has already been created in EasyBlog
		$easydiscussCategoryId = $this->easydiscussCategoryExists($discussionsCategory);

		return $easydiscussCategoryId;
	}

	public function getDiscussionsCategory($id)
	{
		$query  = 'SELECT * FROM `#__discussions_categories` where `id` = ' . $this->db->Quote($id);
		$this->db->setQuery($query);
		$result = $this->db->loadObject();

		// Mimic Joomla's category behavior
		if ($result) {
			$result->title = $result->name;
		}

		return $result;
	}

	public function getTotalDiscussionsPosts()
	{
		$db	= $this->db;

		$query = 'SELECT COUNT(1) FROM `#__discussions_messages` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__discuss_migrators` AS b WHERE b.`external_id` = a.`id` and `component` = ' . $this->db->Quote('com_discussions');
		$query .= ' )';
		$query .= ' AND ' . $db->nameQuote('parent_id') . '=' . $db->Quote(0);

		$db->setQuery($query);
		$items = $db->loadResult();

		return $items;
	}

	public function getDiscussionsPosts($item = null, $limit = null)
	{
		$db	= $this->db;

		$query = 'SELECT * FROM `#__discussions_messages` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__discuss_migrators` AS b WHERE b.`external_id` = a.`id` and `component` = ' . $this->db->Quote('com_discussions');
		$query .= ' )';

		// If item is not null, caller trying to get the replies for that item
		if (!is_null($item)) {
			$query .= ' AND ' . $db->nameQuote('parent_id') . '=' . $db->Quote($item->id);
		} else {
			$query .= ' AND ' . $db->nameQuote('parent_id') . '=' . $db->Quote(0);
		}

		$query .= ' ORDER BY a.`id`';

		if ($limit) {
			$query .= ' LIMIT ' . $limit;
		}
		
		$db->setQuery($query);
		$items = $db->loadObjectList();

		return $items;

	}
}
