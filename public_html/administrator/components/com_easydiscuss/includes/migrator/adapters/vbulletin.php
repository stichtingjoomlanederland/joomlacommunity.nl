<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
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

		$status = '';

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

			$status .= JText::_('COM_EASYDISCUSS_MIGRATOR_MIGRATED_VBULLETIN') . ': ' . $item->postid . JText::_('COM_EASYDISCUSS_MIGRATOR_EASYDISCUSS') . ': ' . $post->id . '<br />';
		}

		$hasmore = false;

		if ($balance) {
			$hasmore = true;
		}

		return $this->ajax->resolve($hasmore, $status);
	}

	public function mapVbulletinItemChilds($item, $parent, $prefix)
	{
		
		// try to get the childs
		$items = $this->getVbulletinPosts($prefix, null, $item->postid);

		if (!$items) {
			return false;
		}

		foreach ($items as $childItem) {
			
			$userColumn = 'username';
			$user = null;

			if ($childItem->{$userColumn}) {
				$user = $this->getDiscussUser($childItem->{$userColumn}, $prefix);
			}

			// Load the post library
			$post = ED::post($parent->id);

			// If this post is not a question, we'll need to get the parent id.
			if (!$post->isQuestion()) {
				$parent = $post->getParent();

				// Re-assign $post to be the parent.
				$post = ED::post($parent->id);
			}

			if (empty($childItem->{$userColumn}) || empty($user)) {
				$data['user_id'] = 0;
				$data['user_type'] = DISCUSS_POSTER_GUEST;
				$postername = $childItem->username ? $childItem->username : 'guest';
				$data['poster_name'] = $postername;
				$data['poster_email'] = '';
			} else {
				$data['user_id'] = $user->id;
				$data['user_type'] = DISCUSS_POSTER_MEMBER;
				$data['poster_name'] = $user->name;
				$data['poster_email'] = $user->email;
			}

			// For contents, we need to get the raw data.
			$data['content'] = $childItem->pagetext;
			$data['parent_id'] = $post->id;

			$saveOptions = array('migration' => true);

			// Load the post library
			$post = ED::post();
			$post->bind($data);

			// Try to save the post now
			$state = $post->save($saveOptions);

			// lets process attachments.
			$this->processAttachments($childItem, $post, $prefix);

		}
	}

	public function mapVbulletinItem($item, &$post, $prefix)
	{
		$config = ED::config();

		$data = array();

		$userColumn = 'username';
		$user = null;

		if ($item->{$userColumn}) {
			$user = $this->getDiscussUser($item->{$userColumn}, $prefix);
		}

		if (empty($item->{$userColumn}) || empty($user)) {
			$data['user_id'] = 0;
			$data['user_type'] = DISCUSS_POSTER_GUEST;
			$postername = $item->username ? $item->username : 'guest';
			$data['poster_name'] = $postername;
			$data['poster_email'] = '';

		} else {
			$data['user_id'] = $user->id;
			$data['user_type'] = DISCUSS_POSTER_MEMBER;
			$data['poster_name'] = $user->name;
			$data['poster_email'] = $user->email;
		}

		// Create category if this item's category does not exist on the site
		$categoryId = $this->migrateCategory($item, $prefix);

		$data['content'] = $item->pagetext;
		$data['title'] = $item->title;
		$data['category_id'] = $categoryId;
		$data['hits'] = $item->hits;
		$data['created'] = ED::date($item->created)->toMySQL();
		$data['modified'] = ED::date($item->created)->toMySQL();
		$data['replied'] = ED::date($item->replied)->toMySQL();
		$data['ip'] = $item->ipaddress;
		$data['content_type'] = 'bbcode';
		$data['parent_id'] = 0;
		$data['islock'] = 0;
		$data['published'] = DISCUSS_ID_PUBLISHED;

		$saveOptions = array('migration' => true);

		$post->bind($data);
		$post->save($saveOptions);

		// lets process attachments.
		$this->processAttachments($item, $post, $prefix);

		// Add this to migrators table
		$this->added('vbulletin', $post->id, $item->postid, 'post');
	}

	/**
	 * Process attachments
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function processAttachments($item, $post, $prefix)
	{
		$config = ED::config();

		$numAttachments = 0;
		$rebuildPreview = false;

		// if this post has attachments
		if ($item->attach) {

			$db = ED::db();

			// now we check if attachments stored in db or not.
			$query = "select a.`contentid`, a.`attachmentid`, a.`filename`, f.* ";
			$query .= " from " . $db->nameQuote($prefix . 'attachment') . ' as a'; 
			$query .= " inner join " . $db->nameQuote($prefix . 'filedata') . ' as f on a.`filedataid` = f.`filedataid`';
			$query .= " where a.`contentid` = " . $db->Quote($item->postid);

			$db->setQuery($query);
			$attachments = $db->loadObjectList();

			if ($attachments) {
				foreach ($attachments as $aitem) {

					$data = $aitem->filedata;
					$filename = $aitem->filename;
					$extension = $aitem->extension == 'jpg' ? 'jpeg' : $aitem->extension;
					$attachmentId = $aitem->attachmentid;

					$hash = ED::getHash($filename . ED::date()->toSql() . uniqid());

					$storagePath = ED::attachment()->getStoragePath();
					$storage = $storagePath . '/' . $hash;

					if (!JFolder::exists($storagePath)) {
						JFolder::create($storagePath);
						JFile::copy(DISCUSS_ROOT . '/index.html', $storagePath . '/index.html');
					}

					// write the image data directly into folder.
					$state = JFile::write($storage, $data);

					if ($state && ED::image()->isImage($storage)) {

						// create thumbnail
						$image = ED::simpleimage();

						@$image->load($storage);
						@$image->resizeToFill(160, 120);
						@$image->save($storage . '_thumb', $image->image_type);
					}

					$mime = '';
					if(class_exists('finfo')) { // php 5.3+
						$finfo = new finfo(FILEINFO_MIME);
						$mime = explode('; ', $finfo->file($storage));
						$mime = $mime[0];
					} elseif(function_exists('mime_content_type')) { // PHP 5.2
						$mime = mime_content_type($storage);
					}

					// now we add the record into attachment table.
					$attachment	= ED::table('Attachments');
					$attachment->set('title', $filename);

					$attachment->set('uid', $post->id);
					$attachment->set('size', $aitem->filesize);
				
					$attachment->set('published', DISCUSS_ID_PUBLISHED);
					$attachment->set('mime', $mime);

					$attachment->set('path', $hash);
					$attachment->created = ED::date()->toSql();

					$attachment->store();

					// now we need to replace the content if there is this [ATTACH=CONFIG]37[/ATTACH]
					$pattern = '[ATTACH=CONFIG]' . $attachmentId . '[/ATTACH]';

					if (strpos($post->post->content, $pattern) !== false) {

						$newString = '[attachment]' . $filename . '[/attachment]';
						$post->post->content = EDJString::str_ireplace($pattern, $newString, $post->post->content);

						$rebuildPreview = true;
					}

					$numAttachments++;
				}
			}

			if ($numAttachments && $post->post->parent_id == 0) {
				// lets update thread table.
				$thread = ED::table('Thread');
				$thread->load($post->post->thread_id);

				$thread->num_attachments = $numAttachments;
				$thread->store();
			}

			if ($rebuildPreview) {
				$preview = $post->formatContent(false, true, true);
				$post->post->preview = $preview;
				$post->post->store();
			}
		}

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
				$item->catid = $item->forumid;
				$item->hits = $item->views;
				$item->created = $item->dateline;
				$item->replied = $item->lastpost;
			}
		}
		return $items;
	}

	public function getDiscussUser($vbUserKeyValue, $prefix)
	{
		$db = ED::db();
		
		// currently we not sure there are how many way of bridging the user from vbulletin to joomla.
		// for now, we assume the username is the key to communicate btw vbulletin and joomla
		$column = 'username';

		$query = 'SELECT b.* FROM ' . $db->nameQuote( '#__users' ) . ' AS b'
				. ' WHERE b.' . $db->nameQuote($column) . '=' . $db->Quote($vbUserKeyValue);

		$db->setQuery($query);
		$result = $db->loadObject();

		return $result;
	}
}
