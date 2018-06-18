<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Import main table.
ED::import('admin:/tables/table');

class DiscussPost extends EasyDiscussTable
{
	public $id = null;
	public $title = null;
	public $alias = null;
	public $created = null;
	public $modified = null;
	public $replied = null;
	public $content = null;
	public $preview = null;
	public $published = null;
	public $ordering = null;
	public $vote = null;
	public $hits = null;
	public $islock = null;
	public $locdate = null;
	public $featured = null;
	public $isresolve = null;
	public $isreport = null;
	public $answered = null;
	public $user_id = null;
	public $parent_id = null;
	public $user_type = null;
	public $poster_name = null;
	public $poster_email = null;
	public $num_likes = null;
	public $num_negvote = null;
	public $sum_totalvote = null;
	public $category_id = null;
	public $params = null;
	public $password = null;
	public $legacy = null;
	public $address = null;
	public $latitude = null;
	public $longitude = null;
	public $content_type = null;
	public $post_status = null;
	public $post_type = null;
	public $private = null;
	public $ip = null;
	public $thread_id = null;
	public $priority = null;
	public $fields = null;
	public $anonymous = null;
	public $cluster_id = null;
	public $cluster_type = null;
	
	private $_data = array();
	static $_attachments = array();
	static $_pollsQuestion = array();
	static $_polls = array();
	static $_likes = array();
	static $_commentTotal = array();
	static $_comments = array();
	static $_voters = array();
	static $_likeAuthors = array();
	static $_loaded = array();
	static $_hasVoted = array();

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__discuss_posts' , 'id' , $db);
	}

	public function loadBatch($ids)
	{
		$db = ED::db();

		if (count($ids) > 0) {
			$query  = 'select * from ' . $this->_tbl;
			if (count($ids) == 1) {
				$query .= ' where id = ' . $db->Quote($ids[0]);
			} else {
				$query .= ' where id IN (' . implode(',', $ids) . ')';
			}

			$db->setQuery($query);
			$result = $db->loadObjectList();

			foreach ($result as $item) {
				$sig = $item->id . (int) false;
				self::$_loaded[$sig] = $item;
			}

		}
	}

	public function load($key = null , $alias = false)
	{
		// static $loaded = array();

		$alias = ($alias) ? true : false;
		$sig = $key . (int) $alias;

		if (!isset(self::$_loaded[$sig])) {
			if (!$alias) {
				parent::load($key);
				self::$_loaded[$sig] = $this;
			} else {
				$db = ED::db();

				if (strpos($key, ':') === false) {
					$query = 'SELECT id FROM ' . $this->_tbl . ' '
							. 'WHERE ' . $db->nameQuote('alias') . ' = ' . $db->Quote($key);
				} else {
					// Try replacing ':' to '-' since Joomla replaces it
					$query = 'SELECT id FROM ' . $this->_tbl . ' '
							. 'WHERE ' . $db->nameQuote('alias') . ' = ' . $db->Quote(JString::str_ireplace(':' , '-' , $key));

				}

				$db->setQuery($query);
				$id = $db->loadResult();

				parent::load($id);
				self::$_loaded[$sig] = $this;
			}
		}

		return parent::bind(self::$_loaded[$sig]);
	}

	public function setPollQuestions($obj)
	{
		self::$_pollsQuestion[$this->id] = $obj;
	}

	public function setPollQuestionsBatch($ids = array())
	{
		if (count($ids) > 0) {
			$db = ED::db();

			$query = 'select * from `#__discuss_polls_question`';
			
			if (count($ids) == 1) {
				$query .= ' where `post_id` = ' . $db->Quote($ids[0]);
			} else {
				$gids = implode(',', $ids);
				$query .= ' where `post_id` IN (' . $gids . ')';
			}

			$db->setQuery($query);
			$result = $db->loadObjectList();

			if (count($result) > 0) {
				foreach ($result as $item) {
					$poll = ED::table('PollQuestion');
					$poll->bind($item);

					self::$_pollsQuestion[$item->post_id] = $poll;
				}
			}

			foreach ($ids as $id) {
				if (!isset(self::$_pollsQuestion[$id])) {
					self::$_pollsQuestion[$id] = false;
				}
			}
		}
	}

	public function setPolls($obj)
	{
		$this->_data['polls'] = $obj;
	}

	public function setCustomFields($obj)
	{
		$this->_data['customfields'] = $obj;
	}


	/**
	 * Must only be bind when using POST data
	 **/
	public function bind($data, $post = false)
	{
		parent::bind($data);

		if ($post) {
			$my = JFactory::getUser();

			if ($this->id == 0) {
				// This is to check if superadmin assign blog author during blog creation. need to further check on this.
				if(empty($this->user_id) && $this->user_type != 'guest') {
					$this->user_id = $my->id;
				}
			}

			// Default joomla date obj
			$date = ED::date();
			$now = $date->toMySQL();
			$config = ED::config();

			//$this->content		= isset($data['dc_reply_content']) ? $data['dc_reply_content'] : '';
			//$this->created		= !empty($this->created) && $this->created != '0000-00-00 00:00:00' ? $this->created : $now;
			//$this->replied		= !empty($this->replied) ? $this->replied : $now;
			//$this->modified		= $now;

			// Default values to 0
			$this->num_likes = $this->num_likes ? $this->num_likes : 0;
			$this->num_negvote = $this->num_negvote ? $this->num_negvote : 0;
			$this->sum_totalvote = $this->sum_totalvote ? $this->sum_totalvote : 0;
		}

		return true;
	}


	/**
	 * Method to update parent total replies count and last reply time.
	 */
	public function addParentRepliesCount($parentId, $val)
	{
		if(empty($parentId))
		{
			return false;
		}

		$db		= ED::db();
		$query	= 'UPDATE `#__discuss_posts` SET `num_replies` = `num_replies` + ' . $db->Quote($val);

		if($val > 0)
		{
			$query .= ', `replied` = ' . $db->Quote($this->created);
		}

		$query .= ' WHERE `id` = ' . $db->Quote($parentId);
		$db->setQuery($query);
		$db->query();

		return true;
	}

	public function setHasVotedBatch($ids, $userId = null)
	{
		$db     = ED::db();
		$user	= JFactory::getUser($userId);

		if(count($ids) > 0)
		{
			$query  = 'SELECT `value`, `post_id` FROM `#__discuss_votes`';
			$query  .= ' WHERE `user_id` = ' . $db->Quote($user->id);
			if(count($ids) == 1)
			{
				$query  .= ' AND ' . $db->nameQuote('post_id') . ' = ' . $db->Quote($ids[0]);
			}
			else
			{
				$query  .= ' AND ' . $db->nameQuote('post_id') . ' IN (' . implode(',', $ids) . ')';
			}

			$db->setQuery($query);
			$result = $db->loadObjectList();

			if(count($result) > 0)
			{
				foreach($result as $item)
				{
					$sig = $user->id . '-' . $item->post_id;
					self::$_hasVoted[$sig] = $item->value;
				}
			}

			foreach($ids as $id)
			{
				$sig = $user->id . '-' . $id;
				if(! isset(self::$_hasVoted[$sig]))
				{
					self::$_hasVoted[$sig]  = '';
				}
			}

		}

	}

	/**
	 * Override parent's behavior as we need to assign badge history when a post is being read.
	 *
	 **/
	public function hit($pk = null)
	{
		$config = ED::config();
		$app = JFactory::getApplication();
		$my	= JFactory::getUser();

		// Determines if we should check against the session table
		if ($config->get('main_hits_session')) {

			$ip = $app->input->server->get('REMOTE_ADDR');

			if (!empty($ip) && !empty($this->id)) {

				$token = md5($ip . $this->id);
				$session = JFactory::getSession();
				$exists = $session->get($token , false);

				if ($exists) {
					return true;
				}

				$session->set($token , 1);
			}
		}

		$state = parent::hit();

		if ($this->published == DISCUSS_ID_PUBLISHED && $my->id != $this->user_id) {

			// @task: Assign badge
			ED::badges()->assign('easydiscuss.read.discussion', $my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_READ_POST', $this->title));

			// EasySocial integrations
			ED::easysocial()->assignBadge('read.question', $my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_READ_POST', $this->title));
			ED::easysocial()->assignPoints('read.discussion');

			// AUP Integrations
			ED::aup()->assign(DISCUSS_POINTS_VIEW_DISCUSSION, $my->id, $this->title);
		}

		return $state;
	}

	public function setCommentsBatch($ids, $limit = null, $limitstart = null)
	{
		if(count($ids) > 0)
		{
			$postModel 		= ED::model('Posts');

			$comments		= $postModel->getComments($ids);

			if(count($comments) > 0)
			{
				foreach($comments as $item)
				{
					self::$_comments[$item->post_id][] = $item;
				}
			}

			foreach($ids as $id)
			{
				if(! isset(self::$_comments[$id]))
				{
					self::$_comments[$id] = array();
				}
				else
				{
					if($limit !== null)
					{
						self::$_comments[$id] = array_slice(self::$_comments[$id], 0, $limit);
					}
				}
			}
		}
	}



	/**
	 * Retrieves the total number of replies for this particular discussion.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getTotalComments()
	{
		if(isset(self::$_commentTotal[$this->id]))
			return self::$_commentTotal[$this->id];

		// Get the post model.
		$postModel 							= ED::model('Posts');
		self::$_commentTotal[$this->id] 	= $postModel->getTotalComments($this->id);


		return self::$_commentTotal[$this->id];
	}


	/**
	 * Set the total number of replies for this particular discussion batch
	 *
	 * @since	3.0
	 * @access	public
	 * @return	int
	 */
	public function setTotalCommentsBatch($ids)
	{

		if(count($ids) > 0)
		{
			$db = ED::db();

			$query	= 'SELECT COUNT(1) as `CNT`, `post_id` FROM `#__discuss_comments`';

			if(count($ids) == 1)
			{
				$query	.= ' WHERE `post_id` = ' . $db->quote($ids[0]);
			}
			else
			{
				$query	.= ' WHERE `post_id` IN (' .  implode(',', $ids) . ')';
			}

			$query	.= ' GROUP BY `post_id`';

			$db->setQuery($query);
			$results    = $db->loadObjectList();

			if(count($results) > 0)
			{
				$items  = array();

				foreach($results as $item)
				{
					self::$_commentTotal [$item->post_id] = $item->CNT;
				}
			}

			foreach($ids as $id)
			{
				if(! isset(self::$_commentTotal [$id]))
				{
					self::$_commentTotal [$id] = '0';
				}
			}
		}

	}

	public function setVoterBatch($ids, $limit='5')
	{
		if(count($ids) > 0)
		{

			$db 	= ED::db();
			$query	= 'SELECT a.`user_id`, b.`name`, b.`username`, c.`nickname`, a.`post_id` ';
			$query	.= ' FROM ' . $db->nameQuote('#__discuss_votes') . ' as a ';
			$query	.= ' INNER JOIN ' . $db->nameQuote('#__users') . ' as b on a.`user_id` = b.`id` ';
			$query	.= ' INNER JOIN ' . $db->nameQuote('#__discuss_users') . ' as c on a.`user_id` = c.`id` ';
			if(count($ids) == 1)
			{
				$query	.= ' WHERE a.`post_id` = ' . $db->Quote($ids[0]);
			}
			else
			{

				$query	.= ' WHERE a.`post_id` IN (' . implode(',', $ids) . ')';
			}
			$query	.= ' LIMIT 0, ' . $limit;

			$db->setQuery($query);

			$result = $db->loadObjectList();

			foreach($result as $item)
			{
				self::$_voters[$item->post_id][] = $item;
			}

			foreach($ids as $id)
			{
				if(! isset(self::$_voters[$id]))
				{
					self::$_voters[$id] = array();
				}
			}

		}
	}


	public function getVoters($postid, $limit='5')
	{
		if(isset(self::$_voters[$postid]))
		{
			return self::$_voters[$postid];
		}

		$db 	= ED::db();
		$query	= 'SELECT a.`user_id`, b.`name`, b.`username`, c.`nickname` '
				. ' FROM ' . $db->nameQuote('#__discuss_votes') . ' as a '
				. ' INNER JOIN ' . $db->nameQuote('#__users') . ' as b on a.`user_id` = b.`id` '
				. ' INNER JOIN ' . $db->nameQuote('#__discuss_users') . ' as c on a.`user_id` = c.`id` '
				. ' WHERE a.`post_id` = ' . $db->Quote($postid) . ' '
				. ' ORDER BY a.`created` DESC'
				. ' LIMIT 0, ' . $limit;

		$db->setQuery($query);
		self::$_voters[$postid] = $db->loadObjectList();

		return self::$_voters[$postid];
	}

	public function getContent()
	{
		if(!isset($this->_data['content']))
		{
			$this->_data['content'] = ED::badwords()->filter($this->content);
		}
		return $this->_data['content'];
	}


	public function setAttachmentsData($type, $ids = array())
	{
		if(count($ids) > 0)
		{
			$db = ED::db();

			$query	= 'SELECT * FROM ' . $db->nameQuote('#__discuss_attachments');
			if(count($ids) == 1)
			{
				$query	.= ' WHERE ' . $db->nameQuote('uid') . '=' . $db->Quote($ids[0]);
			}
			else
			{
				$uids    = implode(',', $ids);
				$query	.= ' WHERE ' . $db->nameQuote('uid') . ' IN (' . $uids . ')';
			}

			$query	.= ' AND ' . $db->nameQuote('type') . '=' . $db->Quote($type);
			$query	.= ' AND ' . $db->nameQuote('published') . '=' . $db->Quote(1);

			$db->setQuery($query);
			$result = $db->loadObjectList();

			if(count($result) > 0)
			{
				foreach($result as $item)
				{
					$table	= JTable::getInstance('Attachments' , 'Discuss');
					$table->bind($item);

					$type = explode("/", $item->mime);
					$table->attachmentType = $type[0];

					self::$_attachments[$item->uid][] = $table;
				}//end foreach

				foreach($ids as $id)
				{
					if(! isset(self::$_attachments[$id]))
					{
						self::$_attachments[$id] = array();
					}
				}

			}//end if
		}
	}



	/*
	 * Returns the permalink of the current post data.
	 */
	public function getPermalink($external = false)
	{
		if(!isset($this->_data['permalink']))
		{
			$this->_data['permalink'] = DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $this->id);
		}
		return $this->_data['permalink'];
	}

	/**
	 * Given a user id, determine if the user has liked this discussion or reply.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The user's id.
	 */
	public function isLiked($userId)
	{
		return $this->isLikedBy($userId);
	}

	/**
	 * Gets the owner of the discussion or reply.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getOwner()
	{
		static $owners 	= null;

		if(!isset($owners[$this->user_id]))
		{
			$owner	= new stdClass();

			// Initialize default values first.
			$owner->id		= 0;
			$owner->name	= JText::_('COM_EASYDISCUSS_GUEST');
			$owner->link	= 'javascript:void(0)';

			// @TODO: Fill this with guest avatar.
			$owner->avatar		= DISCUSS_JURIROOT . '/media/com_easydiscuss/images/default_avatar.png';
			$owner->guest		= true;
			$owner->signature	= '';

			$owner->role		= '';
			$owner->roleid		= '';
			$owner->rolelabel	= '';


			if ($this->user_id > 0) {
				$user = ED::user($this->user_id);

				$owner->id			= $this->user_id;
				$owner->name		= $user->getName();
				$owner->link		= $user->getLink();
				$owner->avatar		= $user->getAvatar();
				$owner->guest		= false;
				$owner->signature	= $user->getSignature('true');

				$owner->role		= $user->getRole();
				$owner->roleid		= $user->getRoleId();
				$owner->rolelabel	= $user->getRoleLabelClassname();
			}



			$owners[$this->user_id] = $owner;
		}

		return $owners[$this->user_id];

	}

	public function getParams($key)
	{
		if(!isset($this->_data['params']))
		{
			$result		= array();
			$pattern	= '/params_' . $key . '[0-9]=(.*)/i';
			preg_match_all($pattern , $this->params , $matches);

			if(!empty($matches[1]))
			{
				foreach($matches[1] as $match)
				{
					$result[] = $match;
				}
			}

			$this->_data['params'] = $result;
		}

		return $this->_data['params'];
	}

	/**
	 * Get the post class css suffix
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostTypeSuffix()
	{
		$model = ED::model('Posttypes');
		$suffix	= $model->getSuffix($this->post_type);

		return $suffix;
	}

	/**
	 * Get the post class css suffix
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostType()
	{
		$model = ED::model('Posttypes');
		$title = $model->getTitle($this->post_type);

		return $title;
	}

	/*
	 * Retrieve the post creator's avatar
	 */
	public function getPosterAvatar()
	{
		if (!isset($this->_data['posteravatar'])) {
			$user = ED::user($this->user_id);
			$this->_data['posteravatar'] = $user->getAvatar();
		}

		$this->_data['posteravatar'];$user->getAvatar();
	}

	/**
	 * Overrides the parent's store behavior
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store($updateNulls = false)
	{
		// the introtext and text might be added from content plugins :(
		if (isset($this->introtext)) {
			unset($this->introtext);
		}

		if (isset($this->text)) {
			unset($this->text);
		}

		return parent::store();
	}

	public function updateParentLastRepliedDate()
	{
		$db = ED::db();

		if(!empty($this->parent_id))
		{
			$query  = 'UPDATE `#__discuss_posts` SET `replied` = ' . $db->Quote($this->created);
			$query  .= ' WHERE `id` = ' . $db->Quote($this->parent_id);

			$db->setQuery($query);
			$db->query();
		}

		return true;
	}

	/**
	 * Tests if the user has already voted for this discussion's poll before.
	 *
	 * @access	public
	 * @param	int $userId		The user id to check for.
	 * @return	boolean			True if voted, false otherwise.
	 */
	public function hasVotedPoll($userId)
	{
		$db		= ED::db();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_polls_users') . ' '
			. 'WHERE ' . $db->nameQuote('user_id') . '=' . $db->Quote($userId) . ' '
			. 'AND ' . $db->nameQuote('poll_id') . ' IN('
			. 'SELECT `id` FROM ' . $db->nameQuote('#__discuss_polls') . ' WHERE ' . $db->nameQuote('post_id') . '=' . $db->Quote($this->id)
			. ')';
		$db->setQuery($query);
		$voted	= $db->loadResult();

		return $voted > 0;
	}

	/**
	 * Returns the poll question
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getPollQuestion()
	{
		if(!isset(self::$_pollsQuestion[$this->id]))
		{
			$poll 	= ED::table('PollQuestion');

			if($this->id)
			{
				$poll->loadByPost($this->id);
			}

			self::$_pollsQuestion[$this->id] = $poll;
		}

		return self::$_pollsQuestion[$this->id];
	}


	public function setPollsBatch($ids)
	{
		if(count($ids) > 0)
		{
			$my = JFactory::getUser();
			$session = JFactory::getSession();
			$session->set('userid', $my->id);

			$db		= ED::db();
			$query	= 'SELECT a.*, count(b.`user_id`) as `meVoted`,';
			$query	.= ' sum(c.`count`) as `totalVoted`';
			$query	.= ' FROM ' . $db->nameQuote('#__discuss_polls') . ' AS a';
			$query  .= ' left join `#__discuss_polls_users` as b on a.`id` = b.`poll_id` and b.`user_id` = ' . $db->Quote($my->id);
			if($my->id == 0)
			{
				$query .= ' AND b.session_id =' . $db->Quote($session->getId());
			}
			$query  .= ' left join #__discuss_polls as c on a.post_id = c.post_id';

			if(count($ids) == 1)
			{
				$query	.= ' WHERE a.' . $db->nameQuote('post_id') . '=' . $db->Quote($ids[0]);
			}
			else
			{
				$query	.= ' WHERE a.' . $db->nameQuote('post_id') . ' IN (' . implode(',', $ids) . ')';
			}
			$query  .= ' GROUP BY a.' . $db->nameQuote('id');

			$db->setQuery($query);

			$result = $db->loadObjectList();

			if(count($result) > 0)
			{
				foreach($result as $item)
				{
					$poll	= ED::table('Poll');
					$poll->bind($item);

					$poll->meVoted  	= $item->meVoted;
					$poll->totalVoted  	= $item->totalVoted;

					self::$_polls[$item->post_id][] = $poll;
				}
			}

			foreach($ids as $id)
			{
				if(! isset(self::$_polls[$id]))
				{
					self::$_polls[$id]    = array();
				}
			}

		}
	}

	public function removePollVote($userId)
	{
		$polls 	= $this->getPolls();

		foreach($polls as $poll)
		{
			$poll->removeExistingVote($userId , $this->id);
		}
		$this->updatePollsCount();
	}

	/**
	 * Retrieve total number of replies for this particular discussion
	 *
	 **/
	public function getReplyCount()
	{
		if(!isset($this->_data['replycount']))
		{
			$db		= ED::db();
			$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote($this->_tbl) . ' '
					. 'WHERE ' . $db->nameQuote('parent_id') . '=' . $db->Quote($this->id);
			$db->setQuery($query);
			$this->_data['replycount']	= $db->loadResult();
		}

		return $this->_data['replycount'];
	}

	public function getReplies($limit = 10 , $limitstart = 0)
	{
		if(!isset($this->_data['replies']))
		{
			$replies	= array();
			$db		= ED::db();
			$query	= 'SELECT *, count(b.id) as `total_vote_cnt` FROM ' . $db->nameQuote($this->_tbl) . ' '
					. 'LEFT JOIN ' . $db->nameQuote('#__discuss_votes') . ' AS `b` '
					. 'ON a.' . $db->nameQuote('id') . '=b.' . $db->nameQuote('post_id') . ' '
					. 'WHERE ' . $db->nameQuote('parent_id') . '=' . $db->Quote($this->id) . ' '
					. 'AND ' . $db->nameQuote('published') . '=' . $db->Quote(1) . ' '
					. 'LIMIT ' . $limitstart . ',' . $limit;
			$db->setQuery($query);
			$result	= $db->loadObjectList();

			if($result	= $db->loadObjectList())
			{
				foreach($result as $res)
				{
					$post	= ED::table('Post');
					$post->bind($res);

					$replies[]	= $post;
				}
			}

			$this->_data['replies'] = $replies;
		}

		return $this->_data['replies'];
	}

	public function isFeatured()
	{
		return (bool) $this->featured;
	}

	public function mapCustomFieldsSession($dbFields)
	{
		if(isset(	$this->_data['customfields']) && count($this->_data['customfields']) > 0)
		{
			for($i = 0; $i < count($dbFields); $i++)
			{
				$row =& $dbFields[$i];

				foreach($this->_data['customfields'] as $key => $val)
				{
					if(isset($val[$row->id]) && !empty($val[$row->id]))
					{
						$values = '';
						if($row->type == 'text' || $row->type == 'area')
						{
							$values = array($val[$row->id][0]);
						}
						else
						{
							$values = $val[$row->id];
						}

						$row->value 	= serialize($values);
						break;
					}
				}
			}
		}

		return $dbFields;
	}

	/**
	 * Retrieves the html code for the like authors.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getLikeAuthors()
	{
		static $loaded = null;

		if(is_null($loaded))
		{
			$loaded		= ED::likes()->getLikesHTML($this->id);
		}

		return $loaded;
	}


	public function getLikeAuthorsObject($id)
	{
		if(isset(self::$_likeAuthors[$id]))
		{
			return self::$_likeAuthors[$id];
		}

		return null;
	}

	public function setLikeAuthorsBatch($ids)
	{
		$db 	= ED::db();
		$config = ED::config();

		if(count($ids) > 0)
		{

			$displayFormat	= $config->get('layout_nameformat');
			$displayName	= '';

			switch($displayFormat){
				case "name" :
					$displayName = 'a.name';
					break;
				case "username" :
					$displayName = 'a.username';
					break;

				case "nickname" :
				default :
					$displayName = 'IF(c.`nickname` != \'\', c.`nickname`, a.`name`)';
					break;
			}

			$query	= 'SELECT a.id as `user_id`, b.id, b.`content_id`, ' . $displayName . ' AS `displayname`';
			$query	.= ' FROM ' . $db->nameQuote('#__discuss_likes') . ' AS b';
			$query	.= ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS a';
			$query	.= '    on b.created_by = a.id';
			$query	.= ' INNER JOIN ' . $db->nameQuote('#__discuss_users') . ' AS c';
			$query	.= '    on b.created_by = c.id';
			$query	.= ' WHERE b.`type` = '. $db->Quote('post');

			if(count($ids) == 1)
			{
				$query	.= ' AND b.`content_id` = ' . $db->Quote($ids[0]);
			}
			else
			{
				$query	.= ' AND b.`content_id` in (' . implode(',', $ids)  . ')';
			}

			$db->setQuery($query);

			$result  = $db->loadObjectList();

			if(count($result) > 0)
			{
				foreach($result as $item)
				{
					self::$_likeAuthors[$item->content_id][] = $item;
				}
			}

			foreach($ids as $id)
			{
				if(! isset(self::$_likeAuthors[$id]))
				{
					self::$_likeAuthors[$id] = array();
				}
			}
		}
	}

	public function setLikedByBatch($ids, $userId)
	{
		$db = ED::db();

		if(count($ids) > 0)
		{
			$query	= 'SELECT `id`, `content_id` FROM `#__discuss_likes`';
			$query	.= ' WHERE `type` = ' . $db->Quote('post');
			if(count($ids) == 1)
			{
				$query	.= ' AND `content_id` = ' . $db->Quote($ids[0]);
			}
			else
			{
				$cids   = implode(',', $ids);
				$query	.= ' AND `content_id` IN (' . $cids . ')';
			}
			$query	.= ' AND `created_by` = ' . $db->Quote($userId);

			$db->setQuery($query);
			$result = $db->loadObjectList();

			if(count($result) > 0)
			{
				foreach($result as $item)
				{
					$key    = $item->content_id . $userId;
					self::$_likes[$key]   = $item->id;
				}
			}

			foreach($ids as $id)
			{
				$key    = $id . $userId;
				if(! isset(self::$_likes[$key]))
				{
					self::$_likes[$key]   = '';
				}
			}

		}
	}

	/**
	 * Use the post assignment table to return the latest assignee
	 */
	public function getAssigneeId()
	{
		$asssignment	= ED::getTable('PostAssignment');
		$asssignment->load($this->id);

		return $asssignment->assignee_id;
	}

	public function getLabel()
	{
		$postlabel	= ED::getTable('PostLabel');
		$postlabel->load($this->id);

		$label	= ED::getTable('Label');
		$label->load($postlabel->post_label_id);

		$this->label = $label;

		return $this->label;
	}

	/**
	 * Retrieves the status class of this post
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string
	 */
	public function getStatusClass()
	{

		if($this->post_status == 1)
		{
			return '-on-hold';
		}

		if($this->post_status == 2)
		{
			return '-accepted';
		}

		if($this->post_status == 3)
		{
			return '-working-on';
		}

		if($this->post_status == 4)
		{
			return '-reject';
		}

		return;
	}

	/**
	 * Retrieves the status message of the post.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string
	 */
	public function getStatusMessage()
	{
		if($this->post_status == 1)
		{
			return JText::_('COM_EASYDISCUSS_POST_STATUS_ON_HOLD');
		}

		if($this->post_status == 2)
		{
			return JText::_('COM_EASYDISCUSS_POST_STATUS_ACCEPTED');
		}

		if($this->post_status == 3)
		{
			return JText::_('COM_EASYDISCUSS_POST_STATUS_WORKING_ON');
		}

		if($this->post_status == 4)
		{
			return JText::_('COM_EASYDISCUSS_POST_STATUS_REJECT');
		}

		return;
	}

	/**
	 * Triggers the content plugin
	 *
	 * @since	3.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function triggerReply()
	{
		$config		= ED::config();

		if(!$config->get('main_content_trigger_replies'))
		{
			return;
		}

		// process content plugins
		ED::events()->importPlugin('content');
		ED::events()->onContentPrepare('reply', $postTable);

		$event 	= new stdClass();

		$args 	= array(&$this);

		$results						= ED::events()->onContentBeforeDisplay('reply' , $args);
		$event->beforeDisplayContent 	= trim(implode("\n", $results));

		$results						= ED::events()->onContentAfterDisplay('reply', $args);
		$event->afterDisplayContent		= trim(implode("\n", $results));
		$this->event 	= $event;
	}

	/**
	 * Allows caller to export this post for rest api
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function toRest()
	{
		$obj = new stdClass();

		$obj->id = $this->id;
		$obj->permalink = $this->getPermalink();
		$obj->title = $this->title;
		$obj->content = $this->content;
		$obj->published = $this->published;
		$obj->resolved = $this->isresolve;
		$obj->created = $this->created;

		return $obj;
	}
}
