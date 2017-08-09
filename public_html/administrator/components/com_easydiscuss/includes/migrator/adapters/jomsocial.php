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
		$total = $this->getTotalJomsocialPosts();

		// Now we migrate those discussion from every group
		$items = $this->getJomsocialPosts(10);

		// Determines if there is still items to be migrated
		$balance = $total - count($items);

		$status = '';

		// Once we get the group, create a category for each group.
		foreach ($items as $item) {

			// Get Jomsocial group
			$group = $this->getJomsocialGroup($item);

			$category = $this->createCategory($group);

			// Migrate the items to Easydiscuss
			$state = $this->migrateDiscussion($item, $category);

			$status .= JText::_('COM_EASYDISCUSS_MIGRATOR_MIGRATING_JOMSOCIAL_DISCUSSION') . ': ' . $item->id . '<br />';
		}

		$hasmore = false;

		if ($balance) {
			$hasmore = true;
		}

		return $this->ajax->resolve($hasmore, $status);
	}

	public function migrateDiscussion($item, $category)
	{
		$config = ED::config();

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

		$saveOptions = array('migration' => true);

		$post->bind($data);

		$state = $post->save($saveOptions);

        if ($state) {
        	// Map the comment to replies in Easydiscuss
        	$this->migrateReplies($item, $post);
        }

        // Add this to migrators table
		$this->added('com_community', $post->id, $item->id, 'post');

		return true;
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
	        $data['user_id'] = $reply->post_by;

	        $saveOptions = array('migration' => true);

	        // Load the post library
	        $post = ED::post();
	        $post->bind($data);

	        // Try to save the post now
	        $state = $post->save($saveOptions);
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

	public function getTotalJomsocialPosts()
	{
		$db	= $this->db;
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__community_groups_discuss') . ' as a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__discuss_migrators` AS b WHERE b.`external_id` = a.`id` and `component` = ' . $db->Quote('com_community');
		$query .= ' AND ' . $db->nameQuote('type') . '=' . $db->Quote('post');
		$query .= ' )';

		$db->setQuery($query);
		$item = $db->loadResult();

		return $item;
	}

	public function getJomsocialPosts($limit = 10)
	{
		$db	= $this->db;
		$query	= 'SELECT * FROM ' . $db->nameQuote('#__community_groups_discuss') . ' as a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__discuss_migrators` AS b WHERE b.`external_id` = a.`id` and `component` = ' . $db->Quote('com_community');
		$query .= ' AND ' . $db->nameQuote('type') . '=' . $db->Quote('post');
		$query .= ' )';
		$query .= 'LIMIT ' . $limit;

		$db->setQuery($query);
		$item = $db->loadObjectList();

		return $item;
	}

	public function createCategory($group)
	{
		$title = JString::strtolower($group->name);

		// Check if the category exists
		$query = 'select * from `#__discuss_category`';
		$query .= ' where lower(`title`) = ' . $this->db->Quote($title);
		$query .= ' LIMIT 1';

		$this->db->setQuery($query);
		$result = $this->db->loadObject();

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

	public function getJomSocialGroup($item)
	{
		$db = $this->db;
		$query = 'SELECT * FROM ' . $db->nameQuote('#__community_groups');
		$query .= ' WHERE  ' . $db->nameQuote('id') . '=' . $db->Quote($item->groupid);
		$db->setQuery($query);
		$result = $db->loadObject();

		return $result;
	}
}
