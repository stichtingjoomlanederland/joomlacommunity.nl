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

class EasyDiscussMigratorKunena extends EasyDiscussMigratorBase
{
	public function migrate()
	{
		$config = ED::config();

		// Get the total number of Kunena items
		$total = $this->getTotalKunenaPosts();

		// Get all Kunena Posts that is not yet migrated
		$items = $this->getKunenaPosts(null, 10);

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
			$state = $this->mapKunenaItem($item, $post);

			// If everything okay, migrate the replies
			if ($state) {
				$this->mapKunenaItemChilds($item, $post);
			}

			$status .= JText::_('COM_EASYDISCUSS_MIGRATOR_MIGRATED_KUNENA') . ': ' . $item->id . JText::_('COM_EASYDISCUSS_MIGRATOR_EASYDISCUSS') . ': ' . $post->id . '<br />';

			// adding poll items to this thread
			$this->mapKunenaItemPolls($item, $post);
			
		}

		$hasmore = false;

		if ($balance) {
			$hasmore = true;
		}

		if (!$hasmore) {

			// Once finished, we reset the user's point
			if ($config->get('migrator_reset_points')) {
				$model = ED::model('users');
				$model->resetPoints();
			}

		}

		return $this->ajax->resolve($hasmore, $status);

	}

	public function mapKunenaItemPolls($kItem, $item)
	{
		$db	= $this->db;

		$query = 'select * from `#__kunena_polls` where `threadid` = ' . $db->Quote($kItem->thread);

		// echo $query;

		$db->setQuery($query);
		$kPolls = $db->loadObjectList();

		if ($kPolls) {
			foreach ($kPolls as $kPoll) {
				$pollQuestion = ED::table( 'PollQuestion');

				$pollQuestion->post_id = $item->id;
				$pollQuestion->title = $kPoll->title;
				$pollQuestion->multiple = 0;
				$pollQuestion->locked = 0;

				$pollQuestion->store();

				// get the poll options.
				$query = 'select * from `#__kunena_polls_options` where `pollid` = ' . $db->Quote( $kPoll->id );
				$db->setQuery($query);
				$kPollsOptions = $db->loadObjectList();

				if ($kPollsOptions) {
					foreach ($kPollsOptions as $kPollOption) {
						$poll = ED::table('Poll');

						$poll->post_id = $item->id;
						$poll->value = $kPollOption->text;
						$poll->count = $kPollOption->votes;

						$poll->store();

						// now we need to insert the users who vote for this option.
						$query = 'select * from `#__kunena_polls_users` where `pollid` = ' . $db->Quote($kPoll->id);
						$query .= ' and `lastvote` = ' . $db->Quote($kPollOption->id);

						$db->setQuery($query);
						$kPollsUsers = $db->loadObjectList();

						if ($kPollsUsers) {
							foreach ($kPollsUsers as $kPollUser) {
								$pollUser = ED::table('PollUser');

								$pollUser->poll_id = $poll->id;
								$pollUser->user_id = $kPollUser->userid;

								$pollUser->store();
							}

						} // if kPollsUsers

					} // foreach kPollsOptions

				} // if kPollsOptions

			} // foreach kPolls

		} // if kPolls

	}

	public function mapKunenaItemChilds($kItem, $parent)
	{
		// try to get the childs
		$items = $this->getKunenaPosts($kItem, null);

		if (!$items) {
			return false;
		}

		foreach ($items as $kChildItem) {
			
			// Load the post library
			$post = ED::post($parent->id);

			// If this post is not a question, we'll need to get the parent id.
			if (!$post->isQuestion()) {
				$parent = $post->getParent();

				// Re-assign $post to be the parent.
				$post = ED::post($parent->id);
			}

			// Get the content
			$content = $this->getKunenaMessage($kChildItem);

			// For contents, we need to get the raw data.
	        $data['content'] = $content;
	        $data['parent_id'] = $post->id;
	        $data['user_id'] = $kChildItem->userid;

	        // Load the post library
	        $post = ED::post();
	        $post->bind($data);

	        // Try to save the post now
	        $state = $post->save();
		}
	}


	public function mapKunenaItem($item, &$post, $parent = null)
	{
		// Get the content
		$content = $this->getKunenaMessage($item);

		$config = ED::config();

		$data = array();

		$lastreplied = (isset($item->threadlastreplied))? $item->threadlastreplied : $item->time;

		$subject = $item->subject;

		if (!$parent && isset($item->threadsubject)) {
			$subject = $item->threadsubject;
		}

		// Create category if this item's category does not exist on the site
		$categoryId = $this->migrateCategory($item);

		$data['content'] = $content;
		$data['title'] = $subject;
		$data['category_id'] = $categoryId;
		$data['user_id'] = $item->userid;
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

		if (!$item->userid) {
			$data['user_type'] = DISCUSS_POSTER_GUEST;
		}

		$post->bind($data);

		// Validate the posted data to ensure that we can really proceed
        if (!$post->validate($data)) {
        	return false;
        }

        $post->save();

		// @task: Get attachments
		$files = $this->getKunenaAttachments($item);

		if ($files) {
			foreach ($files as $kAttachment){
				$attachment	= ED::table('Attachments');

				$attachment->set('uid', $post->id);
				$attachment->set('size', $kAttachment->size);
				$attachment->set('title', $kAttachment->filename);
				$attachment->set('type', $post->getType());
				$attachment->set('published', DISCUSS_ID_PUBLISHED);
				$attachment->set('mime', $kAttachment->filetype);

				$hash = ED::getHash($kAttachment->filename . ED::date()->toSql() . uniqid());

				$attachment->set('path', $hash);

				// Copy files over.
				$config = ED::config();

				$storagePath = ED::attachment()->getStoragePath();
				$storage = $storagePath . '/' . $hash;
				$kStorage = JPATH_ROOT . '/' . rtrim($kAttachment->folder, '/')  . '/' . $kAttachment->filename;

				// create folder if it not exists
				if (!JFolder::exists($storagePath)) {
					JFolder::create($storagePath);
					JFile::copy(DISCUSS_ROOT . '/index.html', $hash . '/index.html');
				}

				if (JFile::exists($kStorage)) {
					
					JFile::copy($kStorage, $storage);

					if (ED::image()->isImage($kStorage)) {

						$image = ED::simpleimage();;

						@$image->load($kStorage);
						@$image->resizeToFill(160, 120);
						@$image->save($storage . '_thumb', $image->image_type);
					}
				}

				// @task: Since Kunena does not store this, we need to generate the own creation timestamp.
				$attachment->created = ED::date()->toSql();

				$attachment->store();
			}
		}

        // Add this to migrators table
		$this->added('com_kunena', $post->id, $item->id, 'post');

		return true;
	}

	public function getKunenaAttachments($kItem)
	{
		$db = $this->db;
		$query	= 'SELECT * FROM ' . $db->nameQuote('#__kunena_attachments') . ' '
				. 'WHERE ' . $db->nameQuote('mesid') . '=' . $db->Quote($kItem->id);
		$db->setQuery($query);
		$attachments = $db->loadObjectList();

		return $attachments;
	}

	public function getKunenaMessage($kItem)
	{
		$db	= $this->db;

		$query	= 'SELECT ' . $db->nameQuote('message') . ' FROM ' . $db->nameQuote('#__kunena_messages_text') . ' '
				. 'WHERE ' . $db->nameQuote('mesid') . '=' . $db->Quote($kItem->id);
		
		$db->setQuery($query);

		$message	= $db->loadResult();

		// @task: Replace unwanted bbcode's.
		$message	= preg_replace( '/\[attachment\="?(.*?)"?\](.*?)\[\/attachment\]/ms' , '' , $message );
		$message	= preg_replace( '/\[quote=(.+?)\d+\]/ms' , '[quote]' , $message );

		return $message;
	}

	public function migrateCategory($item)
	{
		// By default, the category id is 1 because EasyBlog uses the first category as uncategorized
		$default = 1;

		// If there's no category assigned in this item
		if (!$item->catid) {
			return $default;
		}

		// Get Kunena's category
		$kunenaCategory = $this->getKunenaCategory($item->catid);

		// Determine if this category has already been created in EasyBlog
		$easydiscussCategoryId = $this->easydiscussCategoryExists($kunenaCategory);

		return $easydiscussCategoryId;
	}

	public function getKunenaCategory($id)
	{
		$query  = 'SELECT * FROM `#__kunena_categories` where `id` = ' . $this->db->Quote($id);
		$this->db->setQuery($query);
		$result = $this->db->loadObject();

		// Mimic Joomla's category behavior
		if ($result) {
			$result->title = $result->name;
		}

		return $result;
	}

	public function getTotalKunenaPosts()
	{
		$db	= $this->db;

		$query = 'SELECT COUNT(1) FROM `#__kunena_messages` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__discuss_migrators` AS b WHERE b.`external_id` = a.`id` and `component` = ' . $this->db->Quote('com_kunena');
		$query .= ' )';
		$query .= ' AND ' . $db->nameQuote('parent') . '=' . $db->Quote(0);

		$db->setQuery($query);
		$items = $db->loadResult();

		return $items;
	}

	public function getKunenaPosts($item, $limit = null)
	{
		$db	= $this->db;

		$query = 'SELECT * FROM `#__kunena_messages` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__discuss_migrators` AS b WHERE b.`external_id` = a.`id` and `component` = ' . $this->db->Quote('com_kunena');
		$query .= ' )';

		// If item is not null, caller trying to get the replies for that item
		if (!is_null($item)) {
			$query .= ' AND ' . $db->nameQuote('thread') . ' = ' . $db->Quote($item->thread);
			$query .= ' AND ' . $db->nameQuote('id') . '!=' . $db->Quote($item->id);
		} else {
			$query .= ' AND ' . $db->nameQuote('parent') . '=' . $db->Quote(0);
		}

		$query .= ' ORDER BY a.`id`';

		if ($limit) {
			$query .= ' LIMIT ' . $limit;
		}
		
		$db->setQuery($query);
		$items = $db->loadObjectList();

		return $items;

	}

	/**
	 * Retrieves a list of categories in Kunena
	 *
	 * @param	null
	 * @return	string	A JSON string
	 **/
	public function getKunenaCategories()
	{
		require_once JPATH_ROOT . '/administrator/components/com_kunena/api.php';

		$columnName = 'parent';

		if (class_exists('KunenaForum') && KunenaForum::version() >= '2.0') {
			$columnName = 'parent_id';
		}

		$db		= $this->db;
		$query	= 'SELECT * FROM ' . $db->nameQuote('#__kunena_categories')
				. ' where ' . $db->nameQuote($columnName) . ' = ' . $db->Quote('0')
				. ' ORDER BY ' . $db->nameQuote('ordering') . ' ASC';

		$db->setQuery($query);
		$result	= $db->loadObjectList();

		if (!$result) {
			return false;
		}

		return $result;
	}

	public function getKunenaCategoriesCount()
	{
		$db = $this->db;

		$query = 'select count(1) from ' . $db->nameQuote('#__kunena_categories');
		$db->setQuery($query);
		$result	= $db->loadResult();

		if (!$result) {
			return 0;
		}

		return $result;

	}

}
