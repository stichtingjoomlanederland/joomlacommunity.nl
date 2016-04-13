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

class EasyDiscussMigratorVbulletin extends EasyDiscussMigratorBase
{
	public function migrate($prefix = null)
	{
		if (!$prefix) {
			return $this->ajax->reject();
		}

		// Get the total number of items
		$total = $this->getTotalVbulletinPosts($prefix);

		// Get all Kunena Posts that is not yet migrated
		$items = $this->getVbulletinPosts($prefix, 10);

		// Determines if there is still items to be migrated
		$balance = $total - count($items);

		// If there's nothing to load just skip this
		if (!$items) {
			return $this->ajax->resolve('noitem');
		}

		foreach ($items as $item) {
			
			$post = ED::post();

			// Map the item to discuss post
			$this->mapVbulletinItem($item, $post, $prefix);

			// Map the replies
			$this->mapVbulletinItemChilds($item, $post, $prefix);

			$this->ajax->append('[data-progress-status]', JText::_('COM_EASYDISCUSS_MIGRATOR_MIGRATED_VBULLETIN') . ': ' . $item->postid . JText::_('COM_EASYDISCUSS_MIGRATOR_EASYDISCUSS') . ': ' . $post->id . '<br />');
		}

		$hasmore = false;

		if ($balance) {
			$hasmore = true;
		}

		if (!$hasmore) {
			$this->ajax->append('[data-progress-status]', JText::_('COM_EASYDISCUSS_MIGRATOR_FINISHED'));
		}

		return $this->ajax->resolve($hasmore);
	}

	public function mapVbulletinItemChilds($item, $parent, $prefix)
	{
		// try to get the childs
		$items = $this->getVbulletinPosts($prefix, null, $item->postid);

		if (!$items) {
			return false;
		}

		foreach ($items as $childItem) {
			
			// Load the post library
			$post = ED::post($parent->id);

			// If this post is not a question, we'll need to get the parent id.
			if (!$post->isQuestion()) {
				$parent = $post->getParent();

				// Re-assign $post to be the parent.
				$post = ED::post($parent->id);
			}

			// For contents, we need to get the raw data.
	        $data['content'] = $childItem->pagetext;
	        $data['parent_id'] = $post->id;

	        // Load the post library
	        $post = ED::post();
	        $post->bind($data);

	        // Try to save the post now
	        $state = $post->save();
		}
	}

	public function mapVbulletinItem($item, &$post, $prefix)
	{
		$config = ED::config();

		$data = array();

		// Create category if this item's category does not exist on the site
		$categoryId = $this->migrateCategory($item, $prefix);

		$data['content'] = $item->pagetext;
		$data['title'] = $item->title;
		$data['category_id'] = $categoryId;
		$data['user_id'] = $this->getDefaultSuperUserId();
		$data['user_type'] = DISCUSS_POSTER_MEMBER;
		$data['hits'] = $item->hits;
		$data['created'] = ED::date($item->created)->toMySQL();
		$data['modified'] = ED::date($item->created)->toMySQL();
		$data['replied'] = ED::date($item->replied)->toMySQL();
		$data['poster_name'] = $item->username;
		$data['ip'] = $item->ipaddress;
		$data['content_type'] = 'bbcode';
		$data['parent_id'] = 0;
		$data['islock'] = 0;
		$data['published'] = DISCUSS_ID_PUBLISHED;

		$post->bind($data);

		// Validate the posted data to ensure that we can really proceed
        if (!$post->validate($data)) {

            //failed
        }

        $post->save();

		//perform cleanup

        // Add this to migrators table
		$this->added('vbulletin', $post->id, $item->postid, 'post');
	}

	public function migrateCategory($item, $prefix)
	{
		// By default, the category id is 1 because EasyBlog uses the first category as uncategorized
		$default = 1;

		// If there's no category assigned in this item
		if (!$item->catid) {
			return $default;
		}

		// Get Kunena's category
		$vbulletinCategory = $this->getVbulletinCategory($item->catid, $prefix);

		// Standardize object to have alias
		$vbulletinCategory->alias = $vbulletinCategory->title;

		// Determine if this category has already been created in EasyBlog
		$easydiscussCategoryId = $this->easydiscussCategoryExists($vbulletinCategory);

		return $easydiscussCategoryId;
	}

	public function getVbulletinCategory($id, $prefix)
	{

		$db = $this->db;
		
		$query = 'SELECT * FROM ' . $db->nameQuote($prefix . 'forum') . ' '
				. 'WHERE ' . $db->nameQuote('forumid') . '=' . $db->Quote($id);
		$db->setQuery($query);

		return $db->loadObject();
	}

	public function getTotalVbulletinPosts($prefix) 
	{
		$db = ED::db();
		
		$query = 'SELECT COUNT(1) ';
		$query .= ' from ' . $db->nameQuote($prefix . 'post') . ' as a';
		$query .= ' left join ' . $db->nameQuote($prefix . 'thread')  . ' as b';
		$query .= ' 	on a.`threadid` = b.`threadid`';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__discuss_migrators` AS b WHERE b.`external_id` = a.`postid` and `component` = ' . $this->db->Quote('vbulletin');
		$query .= ' )';
		$query .= ' and a.`parentid` = ' . $db->Quote(0);

		$db->setQuery($query);
		$total = $db->loadResult();

		return $total;
	}

	public function getVbulletinPosts($prefix, $limit, $parentid = 0) 
	{
		$db = ED::db();
		
		$query = 'select a.*, b.`forumid`, b.`views`, b.`dateline`, b.`lastpost` ';
		$query .= ' from ' . $db->nameQuote($prefix . 'post') . ' as a';
		$query .= ' left join ' . $db->nameQuote($prefix . 'thread')  . ' as b';
		$query .= ' 	on a.`threadid` = b.`threadid`';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__discuss_migrators` AS b WHERE b.`external_id` = a.`postid` and `component` = ' . $this->db->Quote('vbulletin');
		$query .= ' )';
		$query .= ' and a.`parentid` = ' . $db->Quote($parentid);

		if ($limit) {
			$query .= ' LIMIT ' . $limit;
		}

		$db->setQuery($query);
		$items = $db->loadObjectList();

		if ($items) {	
			foreach ($items as $item) {
				$item->catid 	= $item->forumid;
				$item->hits  	= $item->views;
				$item->created  = $item->dateline;
				$item->replied  = $item->lastpost;
			}
		}
		return $items;
	}
}
