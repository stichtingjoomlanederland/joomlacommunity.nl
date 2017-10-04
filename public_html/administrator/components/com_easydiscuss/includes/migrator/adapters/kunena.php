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
	private $num = 50;

	public function getKunenaReplies($item, $limit = null)
	{
		$db	= $this->db;

		$query = 'SELECT c.`last_post_time`, c.`first_post_id`, a.* FROM `#__kunena_messages` AS a';
		$query .= ' LEFT JOIN `#__discuss_migrators` AS b ON b.`external_id` = a.`id` and b.`component` = ' . $this->db->Quote('com_kunena') . ' and b.`type` = ' . $db->Quote('reply');
		$query .= ' INNER JOIN `#__kunena_topics` as c on c.`id` = a.`thread`';
		$query .= ' WHERE ' . $db->nameQuote('a.id') . ' != ' . $db->nameQuote('c.first_post_id');
		$query .= ' and b.`id` is null';

		if ($limit) {
			$query .= ' LIMIT ' . $limit;
		}

		$db->setQuery($query);
		$items = $db->loadObjectList();

		return $items;

	}

	public function getTotalKunenaReplies()
	{
		$db	= $this->db;

		$query = 'SELECT COUNT(1) FROM `#__kunena_messages` AS a';
		$query .= ' LEFT JOIN `#__discuss_migrators` AS b ON b.`external_id` = a.`id` and b.`component` = ' . $this->db->Quote('com_kunena') . ' and b.`type` = ' . $db->Quote('reply');
		$query .= ' INNER JOIN `#__kunena_topics` as c on c.`id` = a.`thread`';
		$query .= ' WHERE ' . $db->nameQuote('a.id') . ' != c.`first_post_id`';
		$query .= ' and b.`id` is null';

		$db->setQuery($query);
		$items = $db->loadResult();

		return $items;
	}

	public function getInternalId($externalId)
	{
		$db	= $this->db;

		$query = 'SELECT internal_id FROM `#__discuss_migrators` WHERE `external_id` = '. $this->db->Quote($externalId) . ' and `component` = ' . $this->db->Quote('com_kunena');

		$db->setQuery($query);
		$internalId = $db->loadResult();

		return $internalId;
	}

	public function migrateReplies()
	{

		$edInternalIds = array();
		$edParents = array();


		$config = ED::config();

		// Get the total number of Kunena items
		$total = $this->getTotalKunenaReplies();

		// Get all Kunena Posts that is not yet migrated
		$items = $this->getKunenaReplies(null, $this->num);

		// Determines if there is still items to be migrated
		$balance = $total - count($items);

		$status = '';

		// If there's nothing to load just skip this
		if (!$items) {

			if (!empty($status)) {
				return $this->ajax->resolve(false, $status);
			}

			return $this->ajax->resolve('noreplies');
		}

		foreach ($items as $item) {

			// Get the kunena parent
			$kunenaParentId = $item->parent;

			// try getting from cache;
			if (!isset($edInternalIds[$kunenaParentId])) {
				// Retrieve the internal id (ED parent)
				$edId = $this->getInternalId($kunenaParentId);

				if (! $edId) {
					// this could be the parent already deleted from kunena. let get the
					// first_post_id
					$firstPostId = $item->first_post_id;

					// check if this first post id already migrated or not. if yes, use it.
					$edId = $this->getInternalId($firstPostId);

					if (! $edId) {
						// if no, we will need to migrate this as parent
						//
						$parentPost = ED::post();

						$parentItem = $this->getKunenaItem($firstPostId);

						// Map the item to discuss post
						$state = $this->mapKunenaItem($parentItem, $parentPost);

						$status .= JText::_('COM_EASYDISCUSS_MIGRATOR_MIGRATED_KUNENA') . ': ' . $parentItem->id . JText::_('COM_EASYDISCUSS_MIGRATOR_EASYDISCUSS') . ': ' . $parentPost->id . '<br />';

						// adding poll items to this thread
						$this->mapKunenaItemPolls($parentItem, $parentPost);

						// Map item likes
						$this->mapKunenaItemLikes($parentItem, $parentPost);

						$edId = $parentPost->id;

						if ($item->id == $item->first_post_id) {
							$edInternalIds[$kunenaParentId] = $edId;
							continue;
						}

					}
				}

				$edInternalIds[$kunenaParentId] = $edId;
			}

			$edParentId = $edInternalIds[$kunenaParentId];

			$post = null;
			if (!isset($edParents[$edParentId])) {
				// Load the post library
				$post = ED::post($edParentId);

				// If this post is not a question, we'll need to get the parent id.
				if (!$post->isQuestion()) {
					$parent = $post->getParent();

					// Re-assign $post to be the parent.
					$post = ED::post($parent->id);
				}

				$edParents[$edParentId] = $post;
			} else {
				$post = $edParents[$edParentId];
			}

			// Get the content
			$content = $this->getKunenaMessage($item);

			$data = array();

			// For contents, we need to get the raw data.
			$data['content'] = $content;

			// process confidential tag
			$data = $this->processConfidentialTag($data);

			// process code tag
			$data['content'] = $this->processCodeTag($data['content']);

			$data['title'] = ($item->subject) ? $item->subject : 'RE:';
			$data['parent_id'] = $post->id;
			$data['user_id'] = $item->userid;
			$data['user_type'] = DISCUSS_POSTER_MEMBER;
			$data['poster_name'] = $item->name;
			$data['created'] = ED::date($item->time)->toMySQL();
			$data['modified'] = ED::date($item->time)->toMySQL();

			if (!$item->userid) {
				$data['user_type'] = DISCUSS_POSTER_GUEST;
			}

			// Load the post library
			$post = ED::post();
			$post->bind($data, false, true);

			// Try to save the post now
			$saveOptions = array('migration' => true);
			$state = $post->save($saveOptions);

			if ($state) {

				$files = $this->getKunenaAttachments($item);

				if ($files) {
					$this->processAttachments($files, $post);

					$preview = ED::parser()->replaceAttachmentsEmbed($post->post->preview, $post);
					$post->post->preview = $preview;
					$post->post->store();
				}

			}

			// Map child item likes
			$this->mapKunenaItemLikes($item, $post);

			// Add this to migrators table
			$this->added('com_kunena', $post->id, $item->id, 'reply');

			$status .= JText::sprintf('COM_EASYDISCUSS_MIGRATOR_MIGRATED_KUNENA_REPLY', $item->id, $post->id) . '<br />';

		}

		$hasmore = false;

		if ($balance) {
			$hasmore = true;
		}

		return $this->ajax->resolve($hasmore, $status);

	}

	public function migrate($resetHits, $migrateSignature)
	{
		$config = ED::config();

		// ini_set('max_execution_time', 1800);

		// Get the total number of Kunena items
		$total = $this->getTotalKunenaPosts();

		// Get all Kunena Posts that is not yet migrated
		$items = $this->getKunenaPosts(null, $this->num);

		// Determines if there is still items to be migrated
		$balance = $total - count($items);

		$status = '';

		// We reset the user's point if needed
		if ($resetHits) {
			$model = ED::model('users');
			$model->resetPoints();

			$status .= JText::_('COM_EASYDISCUSS_MIGRATOR_RESET_USER_POINTS') . '<br />';
		}

		// We need to migrate user's signature regardless there is post to migrate or not
		if ($migrateSignature) {
			$this->migrateUserSignature();

			$status .= JText::_('COM_EASYDISCUSS_MIGRATOR_MIGRATED_KUNENA_SIGNATURE') . '<br />';
		}

		// If there's nothing to load just skip this
		if (!$items) {

			if (!empty($status)) {
				return $this->ajax->resolve(false, $status);
			}

			return $this->ajax->resolve('noitem');
		}

		foreach ($items as $item) {

			$post = ED::post();

			// Map the item to discuss post
			$state = $this->mapKunenaItem($item, $post);

			$status .= JText::_('COM_EASYDISCUSS_MIGRATOR_MIGRATED_KUNENA') . ': ' . $item->id . JText::_('COM_EASYDISCUSS_MIGRATOR_EASYDISCUSS') . ': ' . $post->id . '<br />';

			// adding poll items to this thread
			$this->mapKunenaItemPolls($item, $post);

			// Map item likes
			$this->mapKunenaItemLikes($item, $post);
		}

		$hasmore = false;

		if ($balance) {
			$hasmore = true;
		}

		if (!$hasmore) {

			// Next, we migrate all the category that not yet migrated
			$this->migrateAllCategories();
		}

		return $this->ajax->resolve($hasmore, $status);

	}

	public function migrateUserSignature()
	{
		$db	= $this->db;

		// Get all the Kunena users
		$query = 'select * from `#__kunena_users`';

		$db->setQuery($query);
		$kUsers = $db->loadObjectList();

		foreach ($kUsers as $kUser) {
			if ($kUser->signature) {

				// Load discuss user
				$edUser = ED::user($kUser->userid);

				if ($edUser->id) {
					$edUser->signature = $kUser->signature;
					$edUser->store();
				}
			}
		}
	}

	public function mapKunenaItemLikes($kItem, $item)
	{
		$db	= $this->db;

		$query = 'select * from `#__kunena_thankyou` where `postid` = ' . $db->Quote($kItem->id);

		$db->setQuery($query);
		$kThanks = $db->loadObjectList();

		if ($kThanks) {
			foreach ($kThanks as $kThank) {
				ED::likes()->addLikes($item->id, 'post', $kThank->userid);
			}
		}
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

	protected function processConfidentialTag($data)
	{
		// lets check if there is this [credential] tags or not.

		$text = $data['content'];

		// We cannot decode the htmlentities here or else, xss will occur!
		preg_match_all('/\[confidential\](.*?)\[\/confidential\]/ims', $text, $matches, PREG_SET_ORDER);

		if (empty($matches) || !isset($matches[0]) || empty($matches[0])) {
			// nothing found.
			return $data;
		}

		$credentials = '';

		foreach ($matches as $match) {
			$code = $match[0];
			$credentials .= $match[1] . "<br />";

			// next, let replace the content with empty space.
			$text = JString::str_ireplace($code, '', $text);
		}

		$data["params_siteurl"] = '';
		$data["params_siteusername"] = '';
		$data["params_sitepassword"] = '';
		$data["params_ftpurl"] = '';
		$data["params_ftpusername"] = '';
		$data["params_ftppassword"] = '';
		$data["params_siteinfo"] = nl2br($credentials);

		// update the content
		$data['content'] = $text;

		return $data;
	}

	protected function processCodeTag($content)
	{
		$content = preg_replace( '/\[code\]/ms' , '[code type="markup"]' , $content );
		return $content;
	}

	public function mapKunenaItem($item, &$post, $parent = null)
	{
		// Get the content
		$content = $this->getKunenaMessage($item);

		$config = ED::config();

		$data = array();

		$lastreplied = (isset($item->last_post_time))? $item->last_post_time : $item->time;

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

		// process confidential tag
		$data = $this->processConfidentialTag($data);

		// process code tag
		$data['content'] = $this->processCodeTag($data['content']);

		$post->bind($data, false, true);

		$saveOptions = array('migration' => true);
		$post->save($saveOptions);

		// @task: Get attachments
		$files = $this->getKunenaAttachments($item);

		if ($files) {
			$this->processAttachments($files, $post);
		}

		$preview = ED::parser()->replaceAttachmentsEmbed($post->post->preview, $post);
		$post->post->preview = $preview;
		$post->post->store();

		// Add this to migrators table
		$this->added('com_kunena', $post->id, $item->id, 'post');

		return true;
	}


	public function processAttachments($files, $post)
	{
		$config = ED::config();

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

				// log the data.
				$this->added('com_kunena', $attachment->id, $kAttachment->id, 'attachment');

			}

		}


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
		$message	= preg_replace( '/\[attachment\="?(.*?)"?\](.*?)\[\/attachment\]/ms' , '[attachment]\2[/attachment]' , $message );
		$message	= preg_replace( '/\[quote=(.+?)\d+\]/ms' , '[quote]' , $message );
		$message	= preg_replace( '/\[url\](.*?)\[\/url\]/ms' , '[url="\1"]\1[/url]' , $message );


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

		$easydiscussParentCategoryId = 0;

		// Check if this kunena category has parent_id
		if ($kunenaCategory->parent_id != 0) {
			// Get the parent category
			$parentCategory = $this->getKunenaCategory($kunenaCategory->parent_id);

			$easydiscussParentCategoryId = $this->easydiscussCategoryExists($parentCategory);
		}

		// Determine if this category has already been created in EasyDiscuss
		$easydiscussCategoryId = $this->easydiscussCategoryExists($kunenaCategory, $easydiscussParentCategoryId);

		return $easydiscussCategoryId;
	}

	public function migrateAllCategories()
	{
		// First get all Kunena Categories
		$kunenaCategories = $this->getKunenaCategories();

		if (!$kunenaCategories) {
			return false;
		}

		foreach ($kunenaCategories as $category) {

			$easydiscussParentCategoryId = 0;

			// Check if this kunena category has parent_id
			if ($category->parent_id != 0) {
				// Get the parent category
				$parentCategory = $this->getKunenaCategory($category->parent_id);

				$easydiscussParentCategoryId = $this->easydiscussCategoryExists($parentCategory);
			}

			$category->title = $category->name;

			// Determine if this category has already been created in EasyDiscuss
			$easydiscussCategoryId = $this->easydiscussCategoryExists($category, $easydiscussParentCategoryId);
		}
	}

	public function getKunenaCategory($id)
	{
		$query = 'SELECT * FROM `#__kunena_categories` where `id` = ' . $this->db->Quote($id);

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
		$query .= ' INNER JOIN `#__kunena_topics` as c on c.`id` = a.`thread`';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__discuss_migrators` AS b WHERE b.`external_id` = a.`id` and `component` = ' . $this->db->Quote('com_kunena');
		$query .= ' )';
		// $query .= ' AND ' . $db->nameQuote('parent') . '=' . $db->Quote(0);
		$query .= ' AND a.' . $db->nameQuote('id') . '=' . 'c.`first_post_id`';


		$db->setQuery($query);
		$items = $db->loadResult();

		return $items;
	}

	public function getKunenaPosts($item = null, $limit = null)
	{
		$db	= $this->db;

		$query = 'SELECT c.`last_post_time`, a.* FROM `#__kunena_messages` AS a';
		$query .= ' INNER JOIN `#__kunena_topics` as c on c.`id` = a.`thread`';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__discuss_migrators` AS b WHERE b.`external_id` = a.`id` and `component` = ' . $this->db->Quote('com_kunena');
		$query .= ' )';

		// If item is not null, caller trying to get the replies for that item
		if (!is_null($item)) {
			$query .= ' AND a.' . $db->nameQuote('thread') . ' = ' . $db->Quote($item->thread);
			$query .= ' AND a.' . $db->nameQuote('id') . '!=' . $db->Quote($item->id);
		} else {
			// $query .= ' AND a.' . $db->nameQuote('parent') . '=' . $db->Quote(0);
			$query .= ' AND a.' . $db->nameQuote('id') . '=' . 'c.`first_post_id`';
		}

		$query .= ' ORDER BY a.`id`';

		if ($limit) {
			$query .= ' LIMIT ' . $limit;
		}

		$db->setQuery($query);
		$items = $db->loadObjectList();

		return $items;

	}

	public function getKunenaItem($itemId)
	{
		$db	= $this->db;

		$query = 'SELECT c.`last_post_time`, a.* FROM `#__kunena_messages` AS a';
		$query .= ' INNER JOIN `#__kunena_topics` as c on c.`id` = a.`thread`';
		$query .= ' WHERE a.' . $db->nameQuote('id') . '=' . $db->Quote($itemId);

		$db->setQuery($query);
		$item = $db->loadObject();

		return $item;
	}


	/**
	 * Retrieves a list of categories in Kunena
	 *
	 * @param	null
	 * @return	string	A JSON string
	 **/
	public function getKunenaCategories()
	{
		$db	= $this->db;
		$query = 'SELECT * FROM ' . $db->nameQuote('#__kunena_categories')
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
