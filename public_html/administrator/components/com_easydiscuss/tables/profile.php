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

ED::import('admin:tables/table');

class DiscussProfile extends EasyDiscussTable
{
	public $id = null;
	public $nickname = null;
	public $avatar = null;
	public $description	= null;
	public $url = null;
	public $params = null;
	public $alias = null;
	public $points = null;
	public $latitude = null;
	public $longitude = null;
	public $location = null;
	public $signature = null;

	/**
	* Determines if the user's profile has been edited or not.
	* @var bool
	*/
	public $edited = null;
	
	/**
	* store the posts that has been read by user
	* @var serialized string.
	*/
	public $posts_read = null;

	public $site = null;
	public $auth = null;

	public function __construct(&$db)
	{
		parent::__construct('#__discuss_users', 'id', $db);
	}

	public function bind($data , $ignore = array())
	{
		parent::bind($data);

		$this->url = $this->_appendHTTP($this->url);

		// Default to nickname if alias is empty.
		if (empty($this->alias)) {
			$this->alias = $this->nickname;
		}

		// If the alias still empty, then we'll use username.
		if (empty($this->alias)) {
			$user = JFactory::getUser();
			$this->alias = $user->username;
		}

		// Clean the alias.
		$this->alias = ED::permalinkSlug($this->alias);

		return true;
	}

	public function bindAvatar($file, $acl = array()) 
	{
		// TODO: Check ACL.

		// Try to upload the avatar
		$avatar = ED::avatar();

		// Get the avatar path
		$this->avatar = $avatar->upload($file, $this->id);

		// Assign points.
		// @rule: If this is the first time the user is changing their profile picture, give a different point
		if ($this->avatar == 'default.png') {

			// @rule: Process AUP integrations
			ED::Aup()->assign(DISCUSS_POINTS_NEW_AVATAR, $this->id, $this->avatar);
		} else {
			// @rule: Process AUP integrations
			ED::Aup()->assign(DISCUSS_POINTS_UPDATE_AVATAR, $this->id, $this->avatar);
		}

		// @rule: Badges when they change their profile picture
		ED::history()->log('easydiscuss.new.avatar', $this->id, JText::_('COM_EASYDISCUSS_BADGES_HISTORY_UPDATED_AVATAR'));

		ED::badges()->assign('easydiscuss.new.avatar', $this->id);
		ED::points()->assign('easydiscuss.new.avatar', $this->id);

		// Reset the points
		$this->updatePoints();

	}

	/**
	 * Deprecated. Use @load instead
	 *
	 * @deprecated	5.0
	 */
	public function init($id = null)
	{
		return $this->load($id);
	}

	/**
	 * Load user profile record. If user profile not exists, create new record.
	 *
	 * @update	5.0.0
	 */
	public function load($id = null, $skipGuestChecking = false)
	{
		static $users = array();

		if (!isset($users[$id])) {
			$user = JFactory::getUser($id);

			// If this is a guest, then do not process further.
			if ($id && $user->guest && !$skipGuestChecking) {
				$users[$id] = $this;

				return $users[$id];
			}

			if (!empty($id)) {
				$state = parent::load($id);

				// If the user is not found, then we'll create new.
				if (!$state) {
					$obj = new stdClass();
					$obj->id = $user->id;
					$obj->nickname = $user->name;
					$obj->avatar = 'default.png';
					$obj->description = '';
					$obj->url = '';
					$obj->params = '';
					$obj->location = '';
					$obj->signature = '';
					$obj->site = '';
					$obj->auth = '';
					$obj->edited = '';
					$obj->alias = ED::permalinkSlug($user->username);

					$db = ED::db();

					if ($db->insertObject('#__discuss_users', $obj)) {
						$this->bind($obj);
					}
				}
			}

			$users[$id] = $this;
		}

		return $users[$id];
	}

	public function store($updateNulls = false)
	{
		$user = JFactory::getUser($this->id);

		// Check if the user existed in Joomla.
		if ($user->guest && !$this->id && !$this->nickname) {
			return;
		}

		$result = parent::store($updateNulls);

		return $result;
	}

	/**
	 * Retrieves the user's link
	 *
	 * @since	4.0
	 */
	public function getPermalink($anchor = '', $external = false, $xhtml = false)
	{
		static $items = array();

		$key = $this->id . $anchor . (int) $external;

		if (!isset($items[$key])) {
			$integration = ED::integrate();
			$config = ED::config();

			$field = $integration->getField($this);

			if ($this->id && $field['integration'] == 'easydiscuss') {
				$items[$key] = EDR::_('view=profile&id=' . $this->id, $xhtml) . $anchor;

				return $items[$key];
			} 

			$items[$key] = $field['profileLink'];
		}

		return $items[$key];
	}

	/**
	 * Retrieves the user's edit profile link
	 *
	 * @since	4.0
	 */
	public function getEditProfileLink($anchor = '')
	{
		static $items = array();

		$key = $this->id . $anchor;

		if (!isset($items[$key])) {
			$field = ED::integrate()->getField($this);

			$config = ED::config();

			$items[$key] = EDR::_('view=profile&layout=edit');

			if ((isset($field['editProfileLink']) && $field['editProfileLink']))  {
				$items[$key] = $field['editProfileLink'];
			}
		}

		return $items[$key];
	}

	/**
	 * Deprecated. Use @getPermalink instead
	 *
	 * @deprecated	5.0
	 */
	public function getLink($anchor = '')
	{
		return $this->getPermalink($anchor);
	}

	public function getLinkHTML( $defaultGuestName = '' )
	{
		if ($this->id == 0) {
			return $this->getName($defaultGuestName);
		}

		return '<a href="'.$this->getLink().'" title="'.$this->getName().'">'.$this->getName().'</a>';
	}

	/**
	 * Adds a badge for a specific user.
	 *
	 * @since	3.0
	 */
	public function addBadge($badgeId)
	{
		// Check if there's already a badge assigned to the user.
		$badgeUser = ED::table('BadgesUsers');
		$exists = $badgeUser->loadByUser($this->id , $badgeId);

		if ($exists) {
			$this->setError('COM_EASYDISCUSS_BADGE_ALREADY_ASSIGN_TO_USER');
			return false;
		}

		$badgeUser->badge_id = $badgeId;
		$badgeUser->user_id = $this->id;
		$badgeUser->created = ED::date()->toMySQL();
		$badgeUser->published = 1;

		return $badgeUser->store();
	}

	/**
	 * Method to remove badge
	 *
	 * @since	3.0
	 */
	public function removeBadge($badgeId)
	{
		$model = ED::model('Badges');
		$model->removeBadge($this->id, $badgeId);

		return true;
	}

	/**
	 * Method to add point
	 *
	 * @since	4.1.3
	 */
	public function addPoint($point, $isUndoVote = false, $isVotedBefore = false, $offsetPoint = false)
	{
		// if this is undo vote process
		if ($isUndoVote) {

			// convert positive value to negative in order to deduct user point
			if ($point > 0) {
				$point = -$point;
			} else {
				// Convert point to negative value
				$point = abs($point);
			}

		// if the system detected this user changing their vote
		// for example :
		// 1. User upvote first then system add 10 point to this him.
		// 2. User decide to downvote then system have to minus his 10 point and another 10 point for that downvote point rule limit
		} elseif (!$isUndoVote && $isVotedBefore) {

			if ($offsetPoint !== false) {
				// now we offset user's point based on previos action.
				$this->points += $offsetPoint;
			}

			// $point = $point * 2;
		}

		$this->points += $point;
	}

	/**
	 * Retrieves the name of the user
	 *
	 * @since	4.0
	 */
	public function getName($default = '')
	{
		static $cache = array();

		if (!isset($cache[$this->id])) {

			// If this is a guest.
			if (!$this->id) {
				if ($default) {
					return $default;
				}

				return JText::_('COM_EASYDISCUSS_GUEST');
			}

			$user = JFactory::getUser($this->id);
			$displayName = ED::config()->get('layout_nameformat');
			$name = $user->name;

			if ($displayName == 'username') {
				$name = $user->username;
			}

			if ($displayName == 'nickname') {
				$name = $this->nickname;
			}
			
			$cache[$this->id] = $name;
		}

		return $cache[$this->id];
	}

	public function getNameInitial($isAnonymous = false, $debug = false)
	{
		$name = $this->getName();

		if (!$this->id && isset($this->poster_name) && $this->poster_name) {
			$name = $this->poster_name;
		}

		// Override to this anonymous name if this is anonymous post
		if ($isAnonymous) {
			$name = JText::_('COM_EASYDISCUSS_ANONYMOUS_USER');
		}

		$initial = new stdClass();
		$initial->text = '';
		$initial->code = '';

		$text = '';

		if (ED::string()->isAscii($name)) {

			//lets split the name based on empty space
			$segments = explode(' ', $name);

			if (count($segments) >= 2) {
				$tmp = array();
				$tmp[] = substr($segments[0], 0, 1);
				$tmp[] = substr($segments[count($segments) - 1], 0, 1);

				$text = implode('', $tmp);
				$initial->text = $text;
			} else {
				$initial->text = substr($name, 0, 1);
			}

			$initial->text = strtoupper($initial->text);
			$text = $initial->text;

		} else {
			// If the name given is not ascii, then we'll need to format the name appropriately.
			$name = $this->getEmail() ?: $name;

			$initial->text = strtoupper(EDJString::substr($name, 0, 1));
			$text = $initial->text;
		}

		// get the color code
		$initial->code = $this->getNameInitialCode($text);

		return $initial;

	}

	private function getNameInitialCode($text)
	{
		if (! $this->id) {
			// guest always return 1;
			return '1';
		}

		$char = substr($text, 0, 1);
		$codes = array(1 => array('A','B','C','D','E'),
					   2 => array('F','G','H','I','J'),
					   3 => array('K','L','M','N','O'),
					   4 => array('P','Q','R','S','T'),
					   5 => array('U','V','W','X','Y','Z'));


		foreach($codes as $key => $sets) {
			if (in_array($char, $sets)) {
				return $key;
			}
		}

		// if nothing found, just return 1
		return '1';
	}

	public function getUsername()
	{
		$user = JFactory::getUser($this->id);

		return $user->username;
	}

	/**
	 * Retrieves the JUser object
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getUser()
	{
		static $cache = array();

		if (!isset($cache[$this->id])) {
			$user = JFactory::getUser($this->id);

			$cache[$this->id] = $user;
		}

		return $cache[$this->id];
	}

	public function getEmail()
	{
		$user = JFactory::getUser($this->id);
		return $user->email;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getOriginalAvatar()
	{
		jimport('joomla.filesystem.file');
		$config = ED::config();

		if ($config->get('layout_avatarIntegration') == 'jfbconnect') {
			$integrate = new EasyDiscussIntegrate;
			$hasAvatar = $integrate->jfbconnect($this);

			if ($hasAvatar) {
				return false;
			}
		}

		if ($config->get( 'layout_avatarIntegration') != 'default') {
			return false;
		}

		$path = JPATH_ROOT . '/' . trim($config->get('main_avatarpath'), DIRECTORY_SEPARATOR);

		// If original image doesn't exist, skip this
		if (!JFile::exists($path . '/original_' . $this->avatar)) {
			return false;
		}

		$path = trim($config->get('main_avatarpath'), '/') . '/' . 'original_' . $this->avatar;
		$uri = rtrim(JURI::root(), '/');
		$uri .= '/' . $path;

		return $uri;
	}

	/**
	 * Retrieve the author's avatar
	 *
	 * @since	4.0
	 */
	public function getAvatar($isThumb = true)
	{
		$config = ED::config();
		$db = ED::db();

		static $avatar;

		// Ensure that avatars are enabled
		if ($config->get('layout_avatarIntegration') == 'default' && $config->get('layout_text_avatar')) {
			return false;
		}

		$key = $this->id . '_' . (int) $isThumb;

		if (!isset($avatar[$key])) {
			$field = ED::integrate()->getField($this, $isThumb);

			$avatar[$key] = $field['avatarLink'];
		}

		$avatarLink = $avatar[$key];

		return $avatarLink;
	}

	public function getNickname()
	{
		$user = JFactory::getUser();

		$nickname = $this->nickname ? $this->nickname : $user->name;
		return $nickname;
	}

	public function getDescription($raw = false)
	{
		static $cache = [];

		$key = $this->id . (int) $raw;

		if (!isset($cache[$key])) {
			
			if ($raw) {
				$cache[$key] = $this->description;
				return $cache[$key];
			}

			if (ED::config()->get('layout_editor') == 'bbcode') {
				$cache[$key] = nl2br(ED::parser()->bbcode($this->description));
			} else {
				$cache[$key] = EDJString::trim($this->description);
			}
		}

		return $cache[$key];
	}

	public function getParams($registry = false)
	{
		if ($registry) {
			return ED::registry($this->params);
		}

		return $this->params;
	}

	/**
	 * https://docs.joomla.org/Potential_backward_compatibility_issues_in_Joomla_3_and_Joomla_Platform_12.2
	 * JUser::$usertype has been removed.
	 *
	 * @deprecated	5.0
	 */
	public function getUserType()
	{
		throw new Exception(sprintf('%s() is deprecated. JUser::$usertype has been removed.', __METHOD__));
	}

	public function _appendHTTP($url)
	{
		$returnStr	= '';
		$regex = '/^(http|https|ftp):\/\/*?/i';
		if (preg_match($regex, trim($url), $matches)) {
			$returnStr	= $url;
		} else {
			$returnStr	= 'http://' . $url;
		}

		return $returnStr;
	}

	/**
	 * Retrieves the rss feed for a user
	 *
	 * @since	4.0
	 */
	public function getRSS($atom = false)
	{
		$url = 'index.php?option=com_easydiscuss&view=profile&id=' . $this->id;

		return ED::feeds()->getFeedURL($url, $atom);
	}

	/**
	 * Retrieves the atom rss feed for a user
	 *
	 * @since	4.0
	 */
	public function getAtom()
	{
		return $this->getRSS(true);
	}

	/**
	 * Returns a total number of topics a user has marked as favourite.
	 *
	 * @since	3.0
	 */
	public function getTotalFavourites()
	{
		$db = ED::db();

		$my = ED::user();
		$respectPrivacy = ($my->id == $this->id) ? false : true;


		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_favourites' ) . ' AS a';
		$query[] = 'INNER JOIN ' . $db->nameQuote( '#__discuss_posts' ) . ' AS b';
		$query[] = 'ON a.' . $db->nameQuote( 'post_id') . ' = b.' . $db->nameQuote('id');
		$query[] = 'WHERE ' . $db->nameQuote( 'created_by') . '=' . $db->Quote($this->id);
		$query[] = 'AND b.' . $db->nameQuote( 'published') . '=' . $db->Quote(1);
		$query[] = 'AND b.' . $db->nameQuote('cluster_id') . '=' . $db->Quote(0);

		if ($respectPrivacy) {

			// category ACL:
			$catOptions = array();
			$catOptions['idOnly'] = true;
			$catOptions['includeChilds'] = true;

			$catModel = ED::model('Categories');
			$catIds = $catModel->getCategoriesTree(0, $catOptions);

			// if there is no categories return, means this user has no permission to view all the categories.
			// if that is the case, just return empty array.
			if (! $catIds) {
				return 0;
			}

			$query[] = " and b.`category_id` IN (" . implode(',', $catIds) . ")";

			// var_dump($catIds);
		}


		$query = implode(' ' , $query);

		$db->setQuery( $query );

		$total = $db->loadResult();

		return $total;
	}

	/**
	 * Returns a total number of topic posted by the current user.
	 *
	 * @since	2.0
	 */
	public function getNumTopicPosted()
	{
		static $cache = array();

		if (empty($this->id)) {
			return 0;
		}

		$index = $this->id;

		$my = ED::user();

		$respectPrivacy = ($my->id == $this->id) ? false : true;


		if (!isset($cache[$index])) {
			$db = ED::db();

			$query	= 'SELECT count(1) AS CNT FROM ' . $db->nameQuote('#__discuss_thread') . ' AS a'
					. ' INNER JOIN ' . $db->nameQuote('#__discuss_posts') . ' AS b ON a.`post_id` = b.`id`'
					.' WHERE ' . $db->nameQuote('a.user_id') . '=' . $db->Quote($this->id)
					.' AND ' . $db->nameQuote('a.published') . '=' . $db->Quote('1');

			if ($my->id != $this->id) {
				$query .=' AND ' . $db->nameQuote('a.private') . '=' . $db->Quote('0');
			}

			// Do not include anything from cluster.
			$query .= ' AND '. $db->nameQuote('a.cluster_id') . '=' . $db->Quote('0');

			if ($respectPrivacy) {

				// category ACL:
				$catOptions = array();
				$catOptions['idOnly'] = true;
				$catOptions['includeChilds'] = true;

				$catModel = ED::model('Categories');
				$catIds = $catModel->getCategoriesTree(0, $catOptions);

				// if there is no categories return, means this user has no permission to view all the categories.
				// if that is the case, just return empty array.
				if (! $catIds) {
					$cache[$index] = '0';
					return $cache[$index];
				}

				$query .= " and a.`category_id` IN (" . implode(',', $catIds) . ")";

			}

			// If the post is anonymous we shouldn't show to public.
			if ($my->id != $this->id) {
				$query .=' AND ' . $db->nameQuote('b.anonymous') . '=' . $db->Quote('0');
			}

			$db->setQuery($query);
			$data = $db->loadResult();

			$cache[$index] = $data;
		}

		return $cache[ $index ];
	}



	/**
	 * Returns a total number of topic posted by group of users.
	 *
	 * @since	3.0
	 */
	public function getNumTopicPostedGroup( $userIds )
	{
		$db = ED::db();

		$ids    = implode( ',', $userIds );

		$query	= 'SELECT COUNT(1) AS CNT, `user_id` FROM `#__discuss_posts`';
		$query	.= ' WHERE `user_id` IN (' . $ids . ')';
		$query	.= ' AND `parent_id` = 0';
		$query	.= ' AND `published` = 1';
		$query	.= ' group by `user_id`';

		$db->setQuery($query);
		$data	= $db->loadObjectList();

		//foreach( $userIds as $uid )
		$result = array();
		foreach( $data as $row )
		{
			$result[$row->user_id] = $row->CNT;
		}

		return $result;
	}

	/**
	 * Retrieve the number of replies the user has posted
	 * @since	3.0
	 */
	public function getNumTopicAnsweredGroup( $userIds )
	{
		$db = ED::db();

		$ids    = implode( ',', $userIds );

		$query	= 'SELECT COUNT(a.`id`) AS CNT, a.`user_id` FROM `#__discuss_posts` AS a ';
		$query	.= ' WHERE a.`user_id` IN (' . $ids . ')';
		$query	.= ' AND a.`published` = 1';
		$query	.= ' AND a.`parent_id` > 0';
		$query	.= ' GROUP BY a.`user_id`';
		$query 	.= ' ORDER BY NULL';

		$db->setQuery($query);

		$data	= $db->loadObjectList();

		//foreach( $userIds as $uid )
		$result = array();
		foreach( $data as $row )
		{
			$result[$row->user_id] = $row->CNT;
		}

		return $result;
	}

	/**
	 * Determines if the user is able to access dashboard in frontend
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function canAccessDashboard()
	{
		static $cache = [];

		if (!isset($cache[$this->id])) {
			$canAccess = false;

			if (ED::isSiteAdmin()) {
				$cache[$this->id] = true;

				return $cache[$this->id];
			}

			$acl = ED::acl($this->id);
			$config = ED::config();

			if ($acl->allowed('manage_pending') || ($config->get('main_work_schedule') && $acl->allowed('manage_holiday'))) {
				$cache[$this->id] = true;

				return $cache[$this->id];
			}

			$cache[$this->id] = false;
		}

		return $cache[$this->id];
	}

	/**
	 * Retrieve the number of replies the user has posted
	 * @since	2.0
	 */
	public function getNumTopicAnswered()
	{
		static $cache = array();
		$user = JFactory::getUser();

		// If this guest, then return 0.
		if ($this->id && $user->guest) {
			return 0;
		}

		if (!isset($cache[$this->id])) {
			$db = ED::db();

			$my = JFactory::getUser();
			$respectAnonymous = ($my->id == $this->id) ? false : true;

			$query = 'SELECT COUNT(a.`id`) AS CNT FROM `#__discuss_posts` as a';
			$query .= ' INNER JOIN `#__discuss_posts` as b';
			$query .= ' ON a.`parent_id` = b.`id`';
			$query .= ' AND a.`parent_id` > 0';
			$query .= ' WHERE a.`user_id` = ' . $db->Quote($this->id);
			$query .= ' AND a.`published` = 1';
			$query .= ' AND b.`published` = 1';

			if ($respectAnonymous) {
				$query .= ' AND a.`anonymous` = 0';
			}

			$db->setQuery($query);
			$result = $db->loadResult();

			$cache[$this->id] = $result;
		}

		return $cache[$this->id];
	}

	/**
	 * Get number of unresolved posts
	 *
	 * @since   4.0
	 */
	public function getNumTopicUnresolved()
	{
		static $cache = array();

		if (!isset($cache[$this->id])) {
			$db = ED::db();

			$my = JFactory::getUser();
			$respectPrivacy = ($my->id == $this->id) ? false : true;

			$query = 'SELECT COUNT(a.`id`) AS CNT FROM `#__discuss_posts` AS a';
			$query .= ' WHERE a.`user_id` = ' . $db->Quote($this->id);
			$query .= ' AND a.`published` = 1';
			$query .= ' AND a.`isresolve` = 0';
			$query .= ' AND a.`parent_id` = 0';

			// Do not include anything from cluster.
			$query .= ' AND a.`cluster_id` = 0';

			// If the post is anonymous/private we shouldn't show to public.
			if (ED::user()->id != $this->id) {
				$query .= ' AND a.`anonymous` = 0';
				$query .= ' AND a.`private` = 0';
			}

			if ($respectPrivacy) {
				// Category ACL.
				$catOptions = array();
				$catOptions['idOnly'] = true;
				$catOptions['includeChilds'] = true;

				$catModel = ED::model('Categories');
				$catIds = $catModel->getCategoriesTree(0, $catOptions);

				// If there is no category return, means this user has no permission to view all the categories.
				// If that is the case, then just return empty array.
				if (!$catIds) {
					return 0;
				}

				$query .= ' AND a.`category_id` IN (' . implode(',', $catIds) . ')';
			}

			$db->setQuery($query);
			$result = $db->loadResult();

			$cache[$this->id] = $result;
		}

		return $cache[$this->id];
	}

	/**
	 * Retrieves the total number of posts user made on the site (questions)
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getTotalPosts()
	{
		static $cache = array();

		if (!isset($cache[$this->id])) {
			$model = ED::model('Users');
			$cache[$this->id] = $model->getTotalQuestions($this->id);
		}

		return $cache[$this->id];
	}

	/**
	 * Returns the total number of posts a user has made on the site.
	 *
	 * @since	3.0
	 */
	public function getTotalQuestions()
	{
		static $cache = array();

		if (!isset($cache[$this->id])) {
			$model = ED::model('Users');

			$total = $model->getTotalQuestions($this->id);
			$cache[$this->id] = $total ? $total : '0';
		}


		return $cache[$this->id];
	}

	/**
	 * Retrieves the total number of replies the user made
	 *
	 * @since	4.0
	 */
	public function getTotalReplies($options = array())
	{
		static $cache = array();

		if (!isset($cache[$this->id])) {
			$model = ED::model('Users');
			$cache[$this->id] = $model->getTotalReplies($this->id, $options);
		}

		return $cache[$this->id];
	}

	/**
	 * Retrieves the total number of assigned post the user made
	 *
	 * @since	4.0
	 */
	public function getTotalAssigned()
	{
		static $cache = array();

		if (!isset($cache[$this->id])) {

			$model = ED::model('Assigned');
			$total = $model->getTotalAssigned($this->id);

			$cache[$this->id] = $total ? $total : '0';
		}

		return $cache[$this->id];
	}

	/**
	 * Retrieves the total number of pending post the user made
	 *
	 * @since	4.1
	 */
	public function getTotalPending()
	{
		static $cache = array();

		if (!isset($cache[$this->id])) {
			$model = ED::model('Posts');
			$total = $model->getTotalPending($this->id);

			$cache[$this->id] = $total;
		}

		return $cache[$this->id];
	}

	/**
	 * Retrieves the total number of resolved post the user made
	 *
	 * @since	4.0
	 */
	public function getTotalResolved()
	{
		static $cache = array();

		if (!isset($cache[$this->id])) {
			$model = ED::model('Assigned');
			$cache[$this->id] = $model->getTotalSolved($this->id) ? $model->getTotalSolved($this->id) : '0';
		}

		return $cache[$this->id];
	}

	/**
	 * Retrieves the total number of subscription the user made
	 *
	 * @since	4.0
	 */
	public function getTotalSubscriptions()
	{
		static $cache = array();

		if (!isset($cache[$this->id])) {
			$model = ED::model('Subscribe');
			$results = $model->getTotalSubscriptions($this->id);

			$cache[$this->id] = 0;

			if ($results) {
				$cache[$this->id] = $results->total;
			}
		}

		return $cache[$this->id];
	}

	/**
	 * Retrieve the total number of tags created by the user
	 *
	 * @since	3.0
	 */
	public function getTotalTags()
	{
		static $cache 	= array();

		$index 	= $this->id;

		if( !isset( $cache[ $index ] ) )
		{
			$db		= ED::db();
			$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_tags' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $this->id ) . ' '
					. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
			$db->setQuery( $query );
			$total	= $db->loadResult();

			$cache[ $index ]	= $total;
		}

		return $cache[ $index ];
	}

	/**
	 * Retrieve the joined date of a user
	 *
	 * @since	4.0
	 */
	public function getDateJoined()
	{
		static $cache = array();

		if (!isset($cache[$this->id])) {
			$user = JFactory::getUser($this->id);
			$date = ED::date($user->registerDate);

			$cache[$this->id] = $date->display(JText::_('DATE_FORMAT_LC1'));
		}

		return $cache[$this->id];
	}

	/**
	 * Get last online date
	 *
	 * @since	4.0
	 */
	public function getLastOnline($front = false, $timelapse = true)
	{
		static $cache = array();
		$key = $this->id . '.' . (int) $front . '.' . (int) $timelapse;

		if (!isset($cache[$key])) {
			$user = JFactory::getUser($this->id);

			if ($front && $timelapse) {
				$cache[$key] = ED::date()->toLapsed($user->lastvisitDate);

				return $cache[$key];
			}

			$cache[$key] = ED::date($user->lastvisitDate)->display(JText::_('DATE_FORMAT_LC1'));
		}

		return $cache[$key];
	}

	/**
	 * Deprecated. Use @getPermalink instead.
	 *
	 * @deprecated	5.0
	 */
	public function getURL( $raw = false , $xhtml = false )
	{
		return $this->getPermalink();
	}

	/**
	 * Determines if the user is an admin on the site.
	 *
	 * @since	3.0
	 */
	public function isAdmin()
	{
		return ED::isSiteAdmin($this->id);
	}

	/**
	 * Determines if the user is currently online or not
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function isOnline($useCache = true)
	{
		static $loaded = array();

		if (!$this->id) {
			//guest, also return false
			return false;
		}

		if (!$useCache || !isset($loaded[$this->id])) {
			$jConfig = ED::jconfig();
			$sharedSess = $jConfig->get('shared_session', 0);
			$db = ED::db();

			$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__session');
			$query .= ' WHERE ' . $db->nameQuote('userid') . ' = ' . $db->Quote($this->id);

			if (!$sharedSess) {
				$query .= ' AND ' . $db->nameQuote('client_id') . ' <> ' . $db->Quote(1);
			}

			$db->setQuery($query);
			$loaded[$this->id] = $db->loadResult() > 0 ? true : false;
		}

		return $loaded[$this->id];
	}

	/**
	 * Determines if the user is blocked
	 *
	 * @since   4.0.15
	 * @access  public
	 */
	public function isBlocked()
	{
		$db	= ED::db();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__users') . ' '
				. 'WHERE ' . $db->nameQuote('id') . '=' . $db->Quote($this->id) . ' '
				. 'AND ' . $db->nameQuote('block') . '=' . $db->Quote(0);
		$db->setQuery($query);

		$result = $db->loadResult() > 0 ? false : true;

		return $result;
	}

	/**
	 * Retrieves a list of badges earned by the user
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getBadges()
	{
		static $loaded = array();

		if (!isset($loaded[$this->id])) {

			$model = ED::model('Badges');
			$result = $model->getSiteBadges(array('user' => $this->id));

			if (!$result) {
				return $result;
			}

			$badges	= array();

			foreach ($result as $res) {
				$badge = ED::table('Badges');
				$badge->bind($res);

				$badges[] = $badge;
			}

			$loaded[$this->id] = $badges;
		}

		return $loaded[$this->id];
	}

	/**
	 * Determine whether this user have any badges or not
	 *
	 * @since	4.1.2
	 */
	public function hasUserBadges()
	{
		static $loaded = array();

		if (!$this->id) {
			return false;
		}

		if (!isset($loaded[$this->id])) {

			$model = ED::model('Badges');
			$result = $model->hasUserBadges($this->id);

			$loaded[$this->id] = $result;
		}

		return $loaded[$this->id];
	}

	public function getTotalBadges()
	{
		$db		= ED::db();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_badges_users' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_badges' ) . ' AS b '
				. 'ON a.' . $db->nameQuote( 'badge_id' ) . '=b.' . $db->nameQuote( 'id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND b.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );

		return $db->loadResult();
	}

	public function updatePoints()
	{
		$db		= ED::db();
		$query	= 'SELECT ' . $db->nameQuote( 'points' ) . ' FROM '
				. $db->nameQuote( '#__discuss_users' ) . ' WHERE '
				. $db->nameQuote( 'id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery($query);

		$this->points	= $db->loadResult();
	}

	public function resetPoints()
	{
		$db		= ED::db();
		$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_users' )
				. ' SET ' . $db->nameQuote('points') . ' = ' . $db->Quote(0)
				. ' WHERE ' . $db->nameQuote('id') . '=' . $db->Quote($this->id);
		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Retrieves the skype social field for a user
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getFacebook()
	{
		$params = $this->getParams(true);

		$facebook = $params->get('facebook', '');

		return $facebook;
	}

	/**
	 * Retrieves the skype social field for a user
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getTwitter()
	{
		$params = $this->getParams(true);

		$twitter = $params->get('twitter', '');

		return $twitter;
	}

	/**
	 * Retrieves the skype social field for a user
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getLinkedin()
	{
		$params = $this->getParams(true);

		$linkedin = $params->get('linkedin', '');

		return $linkedin;
	}

	/**
	 * Retrieves the skype social field for a user
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getWebsite()
	{
		$params = $this->getParams(true);

		$value = $params->get('website', '');

		return $value;
	}

	/**
	 * Retrieves the skype social field for a user
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getSkype()
	{
		$params = $this->getParams(true);

		$skype = $params->get('skype', '');

		return $skype;
	}

	public function getSignature($raw = false)
	{
		static $signature = array();

		$key = $this->id . $raw;

		if (!isset($signature[$key])) {
			if ($raw) {
				$signature[$key] = $this->signature;
				return $signature[$key];
			}

			if (ED::config()->get('layout_editor') == 'bbcode') {
				$signature[$key] = nl2br(ED::parser()->bbcode($this->signature));
			} else {
				$signature[$key] = trim($this->signature);
			}
		}

		return $signature[$key];
	}

	/**
	 * Retrieve's user points
	 *
	 * @since	4.0
	 */
	public function getPoints()
	{
		static $cache = [];

		if (!isset($cache[$this->id])) {
			$cache[$this->id] = $this->points;

			if (ED::aup()->exists()) {
				$cache[$this->id] = ED::aup()->getUserPoints($this->id);
			}
			
			if (ED::easysocial()->exists()) {
				$esUserPoint = ED::easysocial()->getUserPoints($this->id);

				if ($esUserPoint) {
					$cache[$this->id] = $esUserPoint;
				}
			}
		}

		return $cache[$this->id];
	}

	/**
	 * Retrieves the role of a user
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getRole()
	{
		static $cache = array();

		$key = $this->id;

		if (!isset($cache[$key])) {
			$id = $this->id;

			// If the id is null (guest), asign to use 0
			if (is_null($id)) {
				$id = 0;
			}

			$user = JFactory::getUser($id);
			$userGroupId = ED::getUserGroupId($user, false);

			$role = ED::table('Role');
			$title = $role->getTitle($userGroupId);
			
			$cache[$key] = $title;
		}

		return $cache[$key];
	}

	/**
	 * Retrieve the label colour of the role
	 *
	 * @since	5.0
	 */
	public function getRoleLabelColour()
	{
		$id = $this->id;

		// If the id is null (guest), asign to use 0
		if (is_null($id)) {
			$id = 0;
		}

		$user = JFactory::getUser($id);
		$userGroupId = ED::getUserGroupId($user, false);

		$role = ED::table('Role');
		$colour = $role->getRoleColor($userGroupId);

		// Fixed colour code from the previous version
		$colourToFix = array(
			'success' => '#39b54a',
			'warning' => '#c77c11',
			'danger' => '#d9534f',
			'info' => '#5bc0de',
			'default' => '#777777'
		);

		$colourCode = $colour;

		if (array_key_exists($colourCode, $colourToFix)) {
			$colourCode = $colourToFix[$colour];
		} 

		return $colourCode;
	}

	public function getRoleId()
	{
		if (! $this->id) {
			return '0';
		}

		$user = JFactory::getUser();

		$userGroupId = ED::getUserGroupId($user);

		$role = ED::table('Role');
		$roleid	= $role->getRoleId($userGroupId);
		return $roleid;
	}

	public function read($postId)
	{
		$posts = array();
		$doAdd = true;

		if (empty($this->id)) {
			return false;
		}

		if ($this->posts_read) {
			$posts = unserialize($this->posts_read);
			if (in_array($postId, $posts)) {
				$doAdd = false;
			}
		}

		if ($doAdd) {
			$posts[] = $postId;
			$this->posts_read = serialize($posts);
			$this->store();
		}

		return true;
	}

	/**
	 * Deletes the user's avatar
	 *
	 * @since	4.0
	 */
	public function deleteAvatar()
	{
		$config	= ED::config();

		$path = $config->get('main_avatarpath');
		$path = rtrim( $path , '/');
		$path = JPATH_ROOT . '/' . $path;

		$original = $path . '/original_' . $this->avatar;
		$path = $path . '/' . $this->avatar;

		jimport('joomla.filesystem.file');

		// Test if the original file exists.
		if (JFile::exists($original)) {
			JFile::delete($original);
		}

		// Test if the avatar file exists.
		if (JFile::exists($path)) {
			JFile::delete($path);
		}

		$this->avatar = '';

		$this->store();
	}

	/**
	 * Determine if the user already read the post
	 *
	 * @since	4.0
	 */
	public function isRead($postId)
	{
		if ($this->posts_read) {
			$posts = unserialize($this->posts_read);
			return in_array($postId, $posts);
		} else {
			return false;
		}
	}

	/**
	 * Retrieve user id from jfbconnect table
	 *
	 * @since	4.0
	 */
	public function getJfbconnectUserId($userId)
	{
		$db = ED::db();

		// Get columns
		$columns = $db->getTableColumns('#__jfbconnect_user_map');

		// Set the default column
		$query = 'SELECT `fb_user_id` AS `id`';

		// If it is new version
		if (in_array('provider_user_id', $columns)) {
			$query = 'SELECT `provider_user_id` AS `id`';
		}

		$query .= ' FROM `#__jfbconnect_user_map` WHERE `j_user_id`=' . $db->Quote($userId);

		$db->setQuery( $query );
		$id = $db->loadResult();

		return $id;
	}

	/**
	 * Retrieve user details from jfbconnect table
	 *
	 * @since	4.0
	 */
	public function getJfbconnectUserDetails($userId)
	{
		$db = ED::db();

		// Get columns
		$columns = $db->getTableColumns('#__jfbconnect_user_map');

		// Set the default column
		$query = 'SELECT `fb_user_id` AS `id`, `provider`, `params`';

		// If it is new version
		if (in_array('provider_user_id', $columns)) {
			$query = 'SELECT `provider_user_id` AS `id`, `provider`, `params`';
		}

		$query .= ' FROM `#__jfbconnect_user_map` WHERE `j_user_id`=' . $db->Quote($userId);

		$db->setQuery($query);
		$data = $db->loadObjectList();

		$result = '';

		foreach ($data as $row) {
			$result = $row;
		}

		return $result;
	}

	/**
	 * This determines if the user should be moderated when they make a new posting
	 *
	 * @since	4.0
	 */
	public function moderateUsersPost($isQuestion)
	{
		static $items = array();

		if (!isset($items[$this->id])) {
			$config = ED::config();

			$moderationEnabled = $config->get('main_moderatepost');
			$moderationReplyEnabled = $config->get('main_moderatereply');
			$moderationAutomatedEnabled = $config->get('main_moderation_automated');
			$limit = $config->get('moderation_threshold');

			if (!$isQuestion) {
				$limit = $config->get('moderation_reply_threshold');
			}

			if (($moderationAutomatedEnabled && !$limit) || (!$moderationAutomatedEnabled)) {

				$items[$this->id] = false;

				if ($moderationEnabled || $moderationReplyEnabled) {
					$items[$this->id] = true;
				}

				return $items[$this->id];
			}

			$model = ED::model('Users');

			// By default they should be moderated unless they exceeded the moderation threshold
			$items[$this->id] = true;

			// If exceeded, they shouldn't be moderated
			if ($model->exceededModerationThreshold($this->id, $isQuestion, $limit)) {
				$items[$this->id] = false;
			}
		}

		return $items[$this->id];
	}

	public function hasLocation()
	{
		if (!$this->location || !$this->latitude || !$this->longitude) {
			return false;
		}

		return true;
	}

	public function getPostsNumCount($filter = 'questions')
	{
		if ($filter == 'questions') {
			return $this->getNumTopicPosted();
		}

		if ($filter == 'unresolved') {
			return $this->getNumTopicUnresolved();
		}

		if ($filter == 'pending') {
			return $this->getTotalPending();
		}

		if ($filter == 'favourites' || $filter == 'assigned' || $filter == 'replies') {
			$functionName = 'getTotal' . ucfirst($filter);

			return $this->$functionName();
		}
	}

	public function markRead($postId)
	{
		// Get the posts_read by user.
		$posts = $this->posts_read;

		if ($posts) {
			$posts = unserialize($posts);

			if (!in_array($postId, $posts)) {
				$posts[] = $postId;
			} else {
				// We'll return if the post is already in table.
				return true;
			}
		} else {
			$posts = array($postId);
		}

		$posts = serialize($posts);

		$db = ED::db();

		$query = 'UPDATE `#__discuss_users` SET `posts_read` = ' . $db->Quote($posts) . ' WHERE `id` = ' . $db->Quote($this->id);
		$db->setQuery($query);

		return $db->execute();
	}
}
