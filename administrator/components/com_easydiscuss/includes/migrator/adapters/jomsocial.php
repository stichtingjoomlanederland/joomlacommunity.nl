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

class EasyDiscussMigratorJomsocial extends EasyDiscussMigratorBase
{
	public function migrate()
	{
		// Get the total group
		$total = $this->getTotalGroups();

		// First, we need to get the groups
		$groups = $this->getJomSocialGroups();

		// Determines if there is still items to be migrated
		$balance = $total - count($groups);

		// Once we get the group, create a category for each group.
		foreach ($groups as $group) {
			$category = ED::table('category');
			$category = $this->createCategory($group, $category);

			// Now we migrate those discussion from every group
			$items = $this->getJomsocialPosts($group);

			// If no item, continue with other group.
			if (!$items) {
				continue;
			}

			$this->ajax->append('[data-progress-status]', JText::_('COM_EASYDISCUSS_MIGRATOR_MIGRATING_JOMSOCIAL_GROUP') . ': ' . $group->id . '<br />');

			// Migrate the items to Easydiscuss
			$this->migrateDiscussion($items, $category);

			// Add this to migrators table
    		$this->added('com_community', $category->id, $group->id, 'group');

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

	public function migrateDiscussion($items, $category)
	{
		$config = ED::config();

		foreach ($items as $item) {
			$post = ED::post();

			$message = $item->message;

			// Convert content to bbcode
			if ($config->get('layout_editor') == 'bbcode') {
				$message = ED::parser()->html2bbcode( $message );
			}

			$data['content'] = $message;
			$data['title'] = $item->title;
			$data['category_id'] = $category->id;
			$data['user_id'] = $item->creator;
			$data['user_type'] = DISCUSS_POSTER_MEMBER;
			$data['created'] = ED::date($item->created)->toMySQL();
			$data['modified'] = ED::date($item->created)->toMySQL();
			$data['replied'] = ED::date($item->lastreplied)->toMySQL();
			$data['content_type'] = 'bbcode';
			$data['parent_id'] = 0;
			$data['islock'] = $item->lock;

			$state = DISCUSS_ID_PUBLISHED;
			$data['published'] = $state;

			$post->bind($data);

			// Validate the posted data to ensure that we can really proceed
	        if (!$post->validate($data)) {

	            //failed
	        }

	        $post->save();

            // Add this to migrators table
    		$this->added('com_community', $post->id, $item->id, 'post');

    		$this->ajax->append('[data-progress-status]', JText::_('COM_EASYDISCUSS_MIGRATOR_MIGRATED_JOMSOCIAL_GROUP_DISCUSSION') . ': ' . $item->id . JText::_('COM_EASYDISCUSS_MIGRATOR_EASYDISCUSS_POST') . ': ' . $post->id . '<br />');

	        // Map the comment to replies in Easydiscuss
	        $this->migrateReplies($item, $post);
		}
	}

	public function migrateReplies($item = null, $post = null)
	{
		// Get the replies for this discussion
		$replies = $this->getJomsocialReplies($item);

		if (!$replies) {
			return;
		}

		foreach ($replies as $reply) {

			// Load the post library
			$post = ED::post($post->id);

			// If this post is not a question, we'll need to get the parent id.
			if (!$post->isQuestion()) {
				$parent = $post->getParent();

				// Re-assign $post to be the parent.
				$post = ED::post($parent->id);
			}

			// For contents, we need to get the raw data.
	        $data['content'] = $reply->comment;
	        $data['parent_id'] = $post->id;

	        // Load the post library
	        $post = ED::post();
	        $post->bind($data);

	        // Try to save the post now
	        $state = $post->save();
		}
	}

	public function getJomsocialReplies($discussion = null)
	{
		$db	= $this->db;
		$query = 'SELECT * FROM ' . $db->nameQuote('#__community_wall');

		$query .= ' WHERE ' . $db->nameQuote('contentid') . ' = ' . $db->Quote($discussion->id);
		$query .= ' AND ' . $db->nameQuote('type') . '=' . $db->Quote('discussions');

		$db->setQuery($query);

		$result	= $db->loadObjectList();

		if (!$result) {
			return false;
		}

		return $result;
	}

	public function getJomsocialPosts($group)
	{
		$db	= $this->db;
		$query	= 'SELECT * FROM ' . $db->nameQuote('#__community_groups_discuss') . ' '
				. 'WHERE ' . $db->nameQuote('groupid') . '=' . $db->Quote($group->id);
		$db->setQuery($query);
		$item = $db->loadObjectList();

		return $item;
	}

	public function createCategory($group, &$category)
	{
		$title = JString::strtolower($category->title);

		// Check if the category exists
		$query = 'select * from `#__discuss_category`';
		$query .= ' where lower(`title`) = ' . $this->db->Quote($title);
		$query .= ' LIMIT 1';

		$this->db->setQuery($query);
		$result = $this->db->loadResult();

		// If easydiscuss category doesn't exist, create a new category using Kunena's category data
		if ($result) {
			return $result;
		}

		$category = ED::table('Category');

		$category->title = $group->name;
		$category->alias = JString::strtolower($group->name);
		$category->published = $group->published;
		$category->created_by = $group->ownerid;

		// Now, try to save the category
		$category->store();

		return $category;
	}

	public function getJomSocialGroups()
	{
		$db = $this->db;
		$query = 'SELECT * FROM ' . $db->nameQuote('#__community_groups') . ' AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__discuss_migrators` AS b WHERE b.`external_id` = a.`id` and `component` = ' . $db->Quote('com_community');
		$query .= ' AND ' . $db->nameQuote('type') . '=' . $db->Quote('group');
		$query .= ' )';
		$query .= ' LIMIT 1';
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getTotalGroups()
	{
		$db = $this->db;
		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__community_groups') . ' AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__discuss_migrators` AS b WHERE b.`external_id` = a.`id` and `component` = ' . $db->Quote('com_community');
		$query .= ' AND ' . $db->nameQuote('type') . '=' . $db->Quote('group');
		$query .= ' )';
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

}
