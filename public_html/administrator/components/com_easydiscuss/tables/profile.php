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

ED::import('admin:tables/table');

class DiscussProfile extends EasyDiscussTable
{
	public $id = null;
	public $nickname = null;
	public $avatar = null;
	public $description	= null;
	public $url = null;
	public $params = null;
	public $user = null;
	public $alias = null;
	public $points = null;
	public $latitude = null;
	public $longitude = null;
	public $location = null;
	public $signature = null;
	public $site = null;
	public $auth = null;

	/**
	* Determines if the user's profile has been edited or not.
	* @var bool
	*/
	public $edited		= null;

	/**
	* store the posts that has been read by user
	* @var serialized string.
	*/
	public $posts_read	= null;


	private $_data		= array();

	static $instances 	= array();
	/*
	 * Below attribute are the virtual which created when user is being loaded.
	 *
	 * numPostCreated
	 * numPostAnswered
	 * created
	 */

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_users' , 'id' , $db );

		$this->numPostCreated = 0;
		$this->numPostAnswered = 0;
		$this->profileLink = '';
		$this->avatarLink = '';

		$this->config = ED::config();


	}

	public function bind($data , $ignore = array())
	{
		parent::bind( $data );

		$this->url	= $this->_appendHTTP($this->url);

		$this->user	= JFactory::getUser($this->id);

		//default to nickname for blogger alias if empty
		if (empty($this->alias)) {
			$this->alias = $this->nickname;
		}

		if (empty($this->alias)) {
			$this->alias = $this->user->username;
		}

		$this->alias = DiscussHelper::permalinkSlug($this->alias);
		return true;
	}

	public function _createDefault( $id )
	{
		$db = ED::db();

		$date = ED::date();
		$user = JFactory::getUser($id);

		if ($user->id) {
			$obj = new stdClass();
			$obj->id = $user->id;
			$obj->nickname = $user->name;
			$obj->avatar = 'default.png';
			$obj->description = '';
			$obj->url = '';
			$obj->params = '';

			//default to username for blogger alias
			$obj->alias = DiscussHelper::permalinkSlug( $user->username );

			$isCreated = $db->insertObject('#__discuss_users', $obj);
			if ($isCreated) {
				$this->bind($obj);
			}
		}
	}


	public function init( $id = null )
	{
		if (is_array($id)) {
			$tmpArr = array();

			foreach ($id as $uid) {
				if (!isset(self::$instances[$uid])) {
					$tmpArr[] = $uid;
				}
			}

			if (empty($tmpArr)) {
				return;
			}

			if (count($tmpArr) == 1) {
				$id = array_pop( $tmpArr );
				self::load( $id );
			} else {
				$db = ED::db();
				$ids = implode(',', $tmpArr);

				$query  = 'select a.*';

				$query .= ', (select count(1) from  `#__discuss_posts` as p1 where p1.`user_id` = a.`id` and p1.`parent_id` = 0 and p1.`published` = 1) as `numPostCreated`';
				$query .= ', (select count(1) from  `#__discuss_posts` as p2 where p2.`user_id` = a.`id` and p2.`parent_id` != 0 and p2.`published` = 1) as `numPostAnswered`';

				$query .= ' from `#__discuss_users` as a';
				$query .= ' where a.`id` IN (' . $ids . ')';

				$db->setQuery($query);
				$results = $db->loadObjectList();

				// $numPostCreated	 = self::getNumTopicPostedGroup( $tmpArr );
				// $numPostAnswered = self::getNumTopicAnsweredGroup( $tmpArr );

				foreach ($results as $row) {

					$user   = new DiscussProfile($db);
					$user->bind($row);

					// $user->numPostCreated	= isset( $numPostCreated[$row->id] ) ? $numPostCreated[$row->id] : 0;
					// $user->numPostAnswered	= isset( $numPostAnswered[$row->id] ) ? $numPostAnswered[$row->id] : 0;

					$juser	= JFactory::getUser($row->id);
					$user->user	= $juser;

					self::$instances[ $row->id ]	= $user;
				}
			}
		}
		else
		{
			self::load( $id );
		}
	}


	/**
	 * override load method.
	 * if user record not found in eblog_profile, create one record.
	 *
	 */
	public function load($id = null , $reset = true , $reload = false, $skipGuestChecking = false)
	{
		if (!isset(self::$instances[$id])) {
			$createNew  = false;
			$user = JFactory::getUser($id);

			if ($id && $user->guest && !$skipGuestChecking) {
				//do not process any further;
				$this->user = $user;
				self::$instances[ $id ]	= $this;

				return self::$instances[ $id ];
			}

			if (!empty($id)) {
				$state = parent::load( $id );

				if (!$state) {
					$this->_createDefault($id);
					$createNew  = true;
				}
			}

			if (!$createNew) {
				$this->numPostCreated	= $this->getNumTopicPosted();
				$this->numPostAnswered	= $this->getNumTopicAnswered();
			}

			$this->user	= $user;

			self::$instances[ $id ]	= $this;
		} else {
			// At times we might want to reload the user's data.
			if ($reload) {
				parent::load( $id );

				$this->numPostCreated	= $this->getNumTopicPosted();
				$this->numPostAnswered	= $this->getNumTopicAnswered();

				$user = JFactory::getUser($id);
				$this->user	= $user;

				$users[ $id ]		= $this;
			} else {
				$this->bind(self::$instances[$id]);
			}
		}

		return self::$instances[ $id ];
	}

	public function store( $updateNulls = false )
	{
		// we need to check if the user exists in joomla or not before we can store this record.
		$juser = $this->user;
		if ($juser->guest && !$this->id && !$this->nickname) {
			return;
		}

		$tmpNumPostCreated	= $this->numPostCreated;
		$tmpNumPostAnswered	= $this->numPostAnswered;
		$tmpProfileLink		= $this->profileLink;
		unset($this->numPostCreated);
		unset($this->numPostAnswered);
		unset($this->profileLink);
		unset($this->avatarLink);

		$result	= parent::store();

		if ($result) {
			$this->numPostCreated	= $tmpNumPostCreated;
			$this->numPostAnswered	= $tmpNumPostAnswered;
			$this->profileLink		= $tmpProfileLink;
		}

		return $result;
	}

	public function setUser( $my )
	{
		$this->load($my->id);
		$this->user = $my;
	}

	/**
	 * Retrieves the user's link
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPermalink($anchor = '', $external = false)
	{
		static $items = array();

		$key = $this->id . $anchor . (int) $external;

		if (!isset($items[$key])) {
			$field = ED::integrate()->getField($this);

			$config = ED::config();

			if (!$config->get('layout_avatarLinking') && $this->id) {
				$items[$key] = EDR::_('view=profile&id=' . $this->id, false) . $anchor;
			} else {
				$items[$key] = $field['profileLink'];
			}
		}

		return $items[$key];
	}

	/**
	 * Retrieves the user's edit profile link
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getEditProfileLink($anchor = '')
	{
		static $items = array();

		$key = $this->id . $anchor;

		if (!isset($items[$key])) {
			$field = ED::integrate()->getField($this);

			$config = ED::config();

			$items[$key] = EDR::_('view=profile&layout=edit');

			if ($config->get('layout_avatarLinking') && (isset($field['editProfileLink']) && $field['editProfileLink']))  {
				$items[$key] = $field['editProfileLink'];
			}
		}

		return $items[$key];
	}

	/**
	 * Deprecated. Use @getPermalink instead
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
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
	 * @access	public
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
	 * @access	public
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
	 * @access	public
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
	 * @access	public
	 */
	public function getName($default = '')
	{
		if ($this->id == 0) {
			return $default ? $default : JText::_('COM_EASYDISCUSS_GUEST');
		}

		$config = ED::config();
		$displayName = $config->get('layout_nameformat');

		if ($displayName == 'name') {
			$name = $this->user->name;
		}

		if ($displayName == 'username') {
			$name = $this->user->username;
		}

		if ($displayName == 'nickname') {
			$name = $this->nickname;

			if (!$name) {
				$name = $this->user->name;
			}
		}

		return $name;
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
			$initial->text = JString::substr($name, 0, 1);

			$text = ($this->id) ? $this->user->email : '';
			if (!$text && isset($this->poster_email)) {
				$text = $this->poster_email;
				$text = strtoupper($text);
			}
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
		return $this->user->username;
	}

	public function getEmail()
	{
		return $this->user->email;
	}

	public function getId(){
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
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAvatar( $isThumb = true )
	{
		$config = ED::config();
		$db = ED::db();

		static $avatar;

		// Ensure that avatars are enabled
		if (!$config->get('layout_avatar')) {
			return false;
		}

		$key = $this->id . '_' . (int) $isThumb;

		if (!isset($avatar[$key])) {
			$field = ED::integrate()->getField($this, $isThumb);

			$avatar[$key] = $field['avatarLink'];
		}

		$this->avatarLink = $avatar[$key];

		return $this->avatarLink;
	}

	public function getNickname()
	{
		$nickname = $this->nickname ? $this->nickname : $this->user->name;
		return $nickname;
	}

	public function getDescription($raw = false){

		if ($raw) {
			return $this->description;
		}

		if ($this->config->get('layout_editor') == 'bbcode') {
			return nl2br(ED::parser()->bbcode($this->description));
		}

		return trim($this->description);
	}

	public function getWebsite(){
		return $this->url;
	}

	public function getParams(){
		return $this->params;
	}

	public function getUserType(){
		return $this->user->usertype;
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
	 * @access	public
	 * @param	string
	 * @return
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
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAtom()
	{
		return $this->getRSS(true);
	}

	/**
	 * Returns a total number of topics a user has marked as favourite.
	 *
	 * @since	3.0
	 * @access	public
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
				return array();
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
	 * @access	public
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

			$query	= 'SELECT count(1) AS CNT FROM ' . $db->nameQuote('#__discuss_posts')
					.' WHERE ' . $db->nameQuote('user_id') . '=' . $db->Quote($this->id)
					.' AND ' . $db->nameQuote('parent_id') . '=' . $db->Quote('0')
					.' AND ' . $db->nameQuote('published') . '=' . $db->Quote('1');

			// Do not include anything from cluster.
			$query .= ' AND '. $db->nameQuote('cluster_id') . '=' . $db->Quote('0');

			// If the post is anonymous we shouldn't show to public.
			if (ED::user()->id != $this->id) {
				$query .=' AND ' . $db->nameQuote('anonymous') . '=' . $db->Quote('0');
			}

			if (ED::user()->id != $this->id) {
				$query .=' AND ' . $db->nameQuote('private') . '=' . $db->Quote('0');
			}


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
					return array();
				}

				$query .= " and `category_id` IN (" . implode(',', $catIds) . ")";

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
	 * @access	public
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
	 * @access	public
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
	 * Retrieve the number of replies the user has posted
	 * @since	2.0
	 * @access	public
	 */
	public function getNumTopicAnswered()
	{
		static $cache 	= array();

		if( empty( $this->id ) )
			return '0';

		$index 	= $this->id;

		if( !isset( $cache[ $index ] ) )
		{
			$db = ED::db();

			$my = JFactory::getUser();
			$respectAnonymous = ($my->id == $this->id) ? false : true;

			$query	= 'SELECT COUNT(a.`id`) AS CNT FROM `#__discuss_posts` AS a ';
			$query	.= ' INNER JOIN #__discuss_posts AS b ';
			$query	.= ' ON a.`parent_id`=b.`id`';
			$query	.= ' AND a.`parent_id` > 0';
			$query	.= ' WHERE a.`user_id` = ' . $db->Quote($this->id);
			$query	.= ' AND a.`published` = 1';
			$query	.= ' AND b.`published` = 1';

			if ($respectAnonymous) {
				$query	.= ' AND a.`anonymous` = 0';
			}

			$db->setQuery($query);

			$data 	= $db->loadResult();
			$cache[ $index ]	= $data;
		}
		return $cache[ $index ];
	}

	/**
	 * Get number of unresolved posts
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getNumTopicUnresolved()
	{
		static $cache = array();

		$index = $this->id;

		if (!isset($cache[$index])) {
			$db = ED::db();

			$my = JFactory::getUser();
			$respectPrivacy = ($my->id == $this->id) ? false : true;

			$query	= 'SELECT COUNT(a.`id`) AS CNT FROM `#__discuss_posts` AS a ';
			$query	.= ' WHERE a.`user_id` = ' . $db->Quote($this->id);
			$query	.= ' AND a.`published` = 1';
			$query	.= ' AND a.`isresolve` = 0';
			$query	.= ' AND a.`parent_id` = 0';

			// Do not include anything from cluster.
			$query .= ' AND a.'. $db->nameQuote('cluster_id') . '=' . $db->Quote('0');

			// If the post is anonymous we shouldn't show to public.
			if (ED::user()->id != $this->id) {
				$query .=' AND a.' . $db->nameQuote('anonymous') . '=' . $db->Quote('0');
			}

			if (ED::user()->id != $this->id) {
				$query .=' AND a.' . $db->nameQuote('private') . '=' . $db->Quote('0');
			}

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
					return array();
				}

				$query .= " and a.`category_id` IN (" . implode(',', $catIds) . ")";

			}

			$db->setQuery($query);

			$result	= $db->loadResult();

			$cache[ $index ]	= $result;
		}

		return $cache[ $index ];
	}

	/**
	 * Returns the total number of posts a user has made on the site.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getTotalPosts()
	{
		static $cache 	= array();

		$index 	= $this->id;

		if( !isset( $cache[ $index ] ) )
		{
			$db 	= ED::db();
			$query	= 'SELECT COUNT(1) FROM `#__discuss_posts` AS a ';
			$query	.= ' WHERE a.`user_id` = ' . $db->Quote($this->id);
			$query	.= ' AND a.`published` = 1';
			$db->setQuery($query);

			$count 	= $db->loadResult();

			$cache[ $index ]	= $count;
		}

		return $cache[ $index ];
	}

	/**
	 * Returns the total number of posts a user has made on the site.
	 *
	 * @since	3.0
	 * @access	public
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
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalReplies()
	{
		static $cache = array();

		if (!isset($cache[$this->id])) {
			$model = ED::model('Users');
			$cache[$this->id] = $model->getTotalReplies($this->id);
		}

		return $cache[$this->id];
	}

	/**
	 * Retrieves the total number of assigned post the user made
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
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
	 * Retrieves the total number of resolved post the user made
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
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
	 * @access	public
	 * @param	string
	 * @return
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
	 * @access	public
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
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDateJoined()
	{
		$date = ED::date($this->user->registerDate);

		return $date->display(JText::_('DATE_FORMAT_LC1'));
	}

	/**
	 * Get last online date
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLastOnline($front = false)
	{
		$date = ED::date($this->user->lastvisitDate);
		$config = ED::config();
		$timelapse = $config->get('layout_timelapse', 1);
		$format = JText::_('DATE_FORMAT_LC1');

		if ($front && $timelapse) {
			return $date->toLapsed($this->user->lastvisitDate);
		}

		return $date->display($format);
	}

	public function getURL( $raw = false , $xhtml = false )
	{
		$url	= 'index.php?option=com_easydiscuss&view=profile&id=' . $this->id;
		$url	= $raw ? $url : DiscussRouter::_( $url , $xhtml );

		return $url;
	}

	/**
	 * Determines if the user is an admin on the site.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isAdmin()
	{
		return DiscussHelper::isSiteAdmin( $this->id );
	}

	public function isOnline()
	{
		static	$loaded	= array();

		if (! $this->id) {
			//guest, also return false
			return false;
		}

		if (!isset($loaded[$this->id])) {
			$db		= ED::db();
			$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__session' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $this->id ) . ' '
					. 'AND ' . $db->nameQuote( 'client_id') . '<>' . $db->Quote( 1 );
			$db->setQuery( $query );

			$loaded[$this->id]	= $db->loadResult() > 0 ? true : false;
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
	 * Get a list of badges for this user.
	 *
	 * @access	public
	 * @return	Array	An array of DiscussTableBadges
	 **/
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

				// $badge->custom = $res->custom;
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
	 * @access	public
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

	public function getSignature( $raw = false )
	{
		if (!array_key_exists('signature', $this->_data)) {

			if ($raw) {
				$this->_data['signature'] = $this->signature;
			} else {
				if ($this->config->get('layout_editor') == 'bbcode') {
					$this->_data['signature'] = nl2br(ED::parser()->bbcode($this->signature));
				} else {
					$this->_data['signature'] = trim($this->signature);
				}
			}
		}

		return $this->_data['signature'];
	}

	/**
	 * Retrieve's user points
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPoints()
	{
		if (ED::aup()->exists()) {
			return ED::aup()->getUserPoints($this->id);
		}

		if (ED::easysocial()->exists()) {

			$esUserPoint = ED::easysocial()->getUserPoints($this->id);

			if ($esUserPoint === false) {
				return $this->points;
			}
			
			return $esUserPoint;
		}

		return $this->points;
	}

	public function getRole()
	{
		static $_cache = array();

		$key = $this->id;

		if (! isset($_cache[$key])) {
			$id = $this->id;

			// If the id is null (guest), asign to use 0
			if (is_null($id)) {
				$id = 0;
			}

			$user = JFactory::getUser($id);
			$userGroupId = ED::getUserGroupId($user, false);

			$role = ED::table('Role');
			$title = $role->getTitle($userGroupId);
			$_cache[$key] = $title;
		}

		return $_cache[$key];
	}

	public function getRoleLabelClassname()
	{
		$id = $this->id;

		// If the id is null (guest), asign to use 0
		if (is_null($id)) {
			$id = 0;
		}

		$user = JFactory::getUser($id);
		$userGroupId = ED::getUserGroupId($user, false);

		$role = ED::table('Role');
		$color = $role->getRoleColor($userGroupId);

		$classname = $color;

		return $classname;
	}

	public function getRoleId()
	{
		if (! $this->id) {
			return '0';
		}

		$userGroupId = ED::getUserGroupId( $this->user );

		$role	= ED::table( 'Role' );
		$roleid	= $role->getRoleId( $userGroupId );
		return $roleid;
	}

	public function read($postId)
	{
		$posts = array();
		$doAdd = true;

		if (empty($this->id))
			return false;

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
	 * @access	public
	 * @param	string
	 * @return
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
	 * @access	public
	 * @param	string
	 * @return
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
	 * @access	public
	 * @param	string
	 * @return
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
	 * @access	public
	 * @param	string
	 * @return
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
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function moderateUsersPost()
	{
		static $items = array();

		if (!isset($items[$this->id])) {
			$config = ED::config();

			$moderationEnabled = $config->get('main_moderatepost');
			$moderationAutomatedEnabled = $config->get('main_moderation_automated');
			$limit = $config->get('moderation_threshold');

			if (($moderationAutomatedEnabled && !$limit) || (!$moderationAutomatedEnabled)) {

				$items[$this->id] = false;

				if ($moderationEnabled) {
					$items[$this->id] = true;
				}

				return $items[$this->id];
			}

			$model = ED::model('Users');

			// By default they should be moderated unless they exceeded the moderation threshold
			$items[$this->id] = true;

			// If exceeded, they shouldn't be moderated
			if ($model->exceededModerationThreshold($this->id)) {
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

		if ($filter == 'favourites' || $filter == 'assigned' || $filter == 'replies') {
			$functionName = 'getTotal' . ucfirst($filter);

			return $this->$functionName();
		}
	}
}
