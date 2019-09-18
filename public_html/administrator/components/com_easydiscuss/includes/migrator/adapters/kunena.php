<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
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
		$query .= ' INNER JOIN `#__kunena_topics` as c on c.`id` = a.`thread` and c.`moved_id` = 0';
		$query .= ' WHERE ' . $db->nameQuote('a.id') . ' != ' . $db->nameQuote('c.first_post_id');
		$query .= ' and b.`id` is null';

		// debug
		// $query .= ' and c.id in (46195, 46241)';

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
		$query .= ' INNER JOIN `#__kunena_topics` as c on c.`id` = a.`thread` and c.`moved_id` = 0';
		$query .= ' WHERE ' . $db->nameQuote('a.id') . ' != c.`first_post_id`';
		$query .= ' and b.`id` is null';

		// debug
		// $query .= ' and c.id in (46195, 46241)';

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

	public function migrateReplies($total = 0)
	{
		$db	= $this->db;

		$edInternalIds = array();
		$edParents = array();

		$config = ED::config();

		// Get all Kunena Posts that is not yet migrated
		$items = $this->getKunenaReplies(null, $this->num);

		// Determines if there is still items to be migrated
		$balance = $total - count($items);

		$status = '';

		// If there's nothing to load just skip this
		if (!$items) {

			if (!empty($status)) {
				return $this->ajax->resolve(false, $status, '0');
			}

			return $this->ajax->resolve('noreplies', '', '0');
		}

		$threads = array();

		foreach ($items as $item) {

			// Get the kunena parent
			$kunenaParentId = $item->first_post_id;

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
				$post = ED::table('Post');
				$post->load($edParentId);

				// If this post is not a question, we'll need to get the parent id.
				if ($post->parent_id) {
					$post->load($post->parent_id);
				}

				$edParents[$edParentId] = $post;
			} else {
				$post = $edParents[$edParentId];
			}

			// temp keep the parent id here.
			$threads[$post->thread_id] = $post->thread_id;

			// Get the content
			$content = $this->getKunenaMessage($item);

			$data = array();

			// For contents, we need to get the raw data.
			$data['content'] = $content;

			// process confidential tag
			$data = $this->processConfidentialTag($data);
			$content = $data['content'];

			// process code tag
			$content = $this->processCodeTag($content);

			$data['title'] = ($item->subject) ? $item->subject : 'RE:';

			$alias = ED::permalinkSlug($data['title']) . '-' . $item->id;
			$data['alias'] = $alias;

			$data['parent_id'] = $post->id;
			$data['thread_id'] = $post->thread_id;
			$data['content_type'] = 'bbcode';
			$data['category_id'] = $post->category_id;
			$data['user_id'] = $item->userid;
			$data['user_type'] = DISCUSS_POSTER_MEMBER;
			$data['poster_name'] = $item->name;
			$data['created'] = ED::date($item->time)->toMySQL();
			$data['modified'] = ED::date($item->time)->toMySQL();


			if (!$item->userid) {
				$data['user_type'] = DISCUSS_POSTER_GUEST;
			}

			// some joomla editor htmlentity the content before it send to server. so we need
			// to do the god job to fix the content.
			$content = ED::string()->unhtmlentities($content);
			$preview = $content;

			// now we need to 'translate the content into preview mode so that frontend no longer need to do this heavy process'
			if ($data['content_type'] == 'bbcode') {
				$preview = ED::parser()->bbcode($content);

				$preview = nl2br($preview);

				// Before we store this as preview, we need to filter the badwords
				$preview = ED::badwords()->filter($preview);
			}

			$data['content'] = $content;
			$data['preview'] = $preview;
			$data['legacy'] = 0;

			$state = ($item->hold == 0)? DISCUSS_ID_PUBLISHED : DISCUSS_ID_UNPUBLISHED;
			$data['published'] = $state;

			$tbl = ED::table('Post');
			$tbl->bind($data);
			$state = $tbl->store();

			if ($state) {

				$files = $this->getKunenaAttachments($item);

				if ($files) {
					$this->processAttachments($files, $tbl);

					$preview = ED::parser()->replaceAttachmentsEmbed($tbl->preview, $tbl);
					$tbl->preview = $preview;
					$tbl->store();
				}

			}

			// // Map child item likes
			$this->mapKunenaItemLikes($item, $tbl);

			// Add this to migrators table
			$this->added('com_kunena', $tbl->id, $item->id, 'reply');

			$status .= JText::sprintf('COM_EASYDISCUSS_MIGRATOR_MIGRATED_KUNENA_REPLY', $item->id, $tbl->id) . '<br />';

		}

		if ($threads) {
			foreach ($threads as $thread_id) {
				// update last user_id
				// update last replied_date
				// update replies count

				$query = "update `#__discuss_thread` as a";
				$query .= " inner join `#__discuss_posts` as b";
				$query .= " on a.`id` = b.`thread_id` and b.`id` = (select max(id) from `#__discuss_posts` as c where c.`thread_id` = a.`id`)";
				$query .= "  set a.`last_user_id` = b.`user_id`,";
				$query .= "  a.`last_poster_name` = b.`poster_name`,";
				$query .= "  a.`last_poster_email` = b.`poster_email`,";
				$query .= "  a.`replied` = b.`created`,";
				$query .= "  a.`num_replies` = (select count(1) - 1 from `#__discuss_posts` as b1 where b1.`thread_id` = a.`id` and b1.`published` = 1)";
				$query .= " where a.id = " . $db->Quote($thread_id);
				$query .= " and b.parent_id > 0";

				$db->setQuery($query);
				$db->query();
			}
		}

		$hasmore = false;

		if ($balance) {
			$hasmore = true;
		}

		return $this->ajax->resolve($hasmore, $status, $balance);

	}

	public function migrate($resetHits, $migrateSignature, $migrateAvatar)
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

		// We need to migrate user's avatar regardless there is post to migrate or not
		if ($migrateAvatar) {
			$this->migrateUserAvatar();

			$status .= JText::_('COM_EASYDISCUSS_MIGRATOR_MIGRATED_KUNENA_AVATAR') . '<br />';
		}

		// If there's nothing to load just skip this
		if (!$items) {

			$repliesCount = $this->getTotalKunenaReplies();

			if (!empty($status)) {
				return $this->ajax->resolve(false, $status, $repliesCount);
			}

			return $this->ajax->resolve('noitem', '', $repliesCount);
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

		$repliesCount = 0;

		if (!$hasmore) {

			// Next, we migrate all the category that not yet migrated
			$this->migrateAllCategories();

			$repliesCount = $this->getTotalKunenaReplies();
		}

		return $this->ajax->resolve($hasmore, $status, $repliesCount);

	}

	public function migrateUserSignature()
	{
		$db = $this->db;

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

	public function migrateUserAvatar()
	{
		$db = $this->db;
		$config = ED::config();

		$query = 'select * from `#__kunena_users`';

		$db->setQuery($query);
		$kunenaUsers = $db->loadObjectList();

		if (count($kunenaUsers) <= 0) {
			return null;
		}

		foreach ($kunenaUsers as $kunenaUser) {

			if (!$kunenaUser->avatar) {
				continue;
			}

			$userid = $kunenaUser->userid;

			// media/kunena/avatars/users/avatar***.jpg
			$imagePath = JPATH_ROOT . '/media/kunena/avatars/' . $kunenaUser->avatar;

			if (!JFile::exists($imagePath)) {
				continue;
			}

			$fileName = basename($imagePath);

			$avatar_config_path	= $config->get('main_avatarpath');
			$avatar_config_path	= rtrim($avatar_config_path, '/');
			$avatar_config_path	= JString::str_ireplace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

			// Get the upload path
			$target_file_path = JPATH_ROOT . '/' . $avatar_config_path;

			if (!JFolder::exists($target_file_path)) {
				JFolder::create($target_file_path);
			}

			// Makesafe on the file
			$date = ED::date();
			$file_ext = ED::Image()->getFileExtention($fileName);
			$fileName = $userid . '_' . JFile::makeSafe(md5($fileName.$date->toSql())) . '.' . strtolower($file_ext);

			$target_file = JPath::clean($target_file_path . '/' . JFile::makeSafe($fileName));
			$original = JPath::clean($target_file_path . '/' . 'original_' . JFile::makeSafe($fileName));

			$profile = ED::table('Profile');
			$profile->load($userid);

			//rename the file 1st.
			$oldAvatar = $profile->avatar;
			$tempAvatar	= '';
			$isNew = ($oldAvatar == 'default.png')? true : false ;

			if (!$isNew) {
				$session = JFactory::getSession();
				$sessionId = $session->getToken();

				$fileExt = JFile::getExt(JPath::clean($target_file_path . '/' . $oldAvatar));
				$tempAvatar	= JPath::clean($target_file_path . '/' . $sessionId . '.' . $fileExt);

				// Test if old original file exists. If exist, remove it.
				if (JFile::exists($target_file_path . '/original_' . $oldAvatar)) {
					JFile::delete($target_file_path . '/original_' . $oldAvatar);
				}

				if ($oldAvatar) {
					JFile::move($target_file_path . '/' . $oldAvatar, $tempAvatar);
				}
			}

			// image size should be in ratio of 1:1,
			// Means whatever is set in width, must also being used for height
			$configWidth = $config->get('layout_avatarwidth', 160);
			$configHeight = $configWidth;

			$originalWidth = $config->get('layout_originalavatarwidth', 400);
			$originalHeight = $originalWidth;

			// Copy the original image files over
			$image = ED::simpleimage();
			$image->load($imagePath);
			$image->resizeOriginal($originalWidth, $originalHeight, $configWidth, $configHeight);
			$image->save($original, $image->image_type);
			unset($image);

			$image = ED::simpleimage();
			$image->load($imagePath);
			$image->resizeToFill($configWidth, $configHeight);
			$image->save($target_file, $image->image_type);

			//now we update the user avatar. If needed, we remove the old avatar.
			if (!$isNew) {
				if (JFile::exists($tempAvatar)) {
					JFile::delete($tempAvatar);
				}
			}

			$profile->avatar = JFile::makeSafe($fileName);
			$profile->store();
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

				// It seems that there is a new column `filename_real` added in their attachment table since version 3.1.0 and at year 2013
				// We will take the value of `filename_real` instead of `filename` IF the column `filename_real` exsists
				if (!is_null($kAttachment->filename_real) && $kAttachment->filename_real) {
					// Detect if the filename_real consists file path, we will take filename
					if (substr($kAttachment->filename_real, 0, 1) == "/") {
						$attachment->set('title', $kAttachment->filename);
					} else {
						$attachment->set('title', $kAttachment->filename_real);
					}
				} else {
					$attachment->set('title', $kAttachment->filename);
				}

				$attachment->set('uid', $post->id);
				$attachment->set('size', $kAttachment->size);
				
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

			// Determine if this category has already been created in EasyDiscuss
			$easydiscussCategoryId = $this->easydiscussCategoryExists($category, $easydiscussParentCategoryId);
		}
	}

	public function getKunenaCategory($id)
	{
		$query = 'SELECT *, `name` as `title` FROM `#__kunena_categories` where `id` = ' . $this->db->Quote($id);

		$this->db->setQuery($query);

		$result = $this->db->loadObject();

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
		$query .= ' AND c.' . $db->nameQuote('hold') . '=' . $db->Quote(0);
		$query .= ' AND c.' . $db->nameQuote('moved_id') . '=' . $db->Quote(0);



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
			$query .= ' AND c.' . $db->nameQuote('hold') . '=' . $db->Quote(0);
			$query .= ' AND c.' . $db->nameQuote('moved_id') . '=' . $db->Quote(0);

			// debug
			// $query .= ' and a.id in (46195, 46241)';
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
		$query = 'SELECT *, `name` as `title` FROM ' . $db->nameQuote('#__kunena_categories');

		// do not change the ordering sequence!
		$query .= ' ORDER BY ' . $db->nameQuote('parent_id') . ', ' . $db->nameQuote('ordering');

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
