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

ED::import('admin:/tables/table');

class DiscussBadges extends EasyDiscussTable
{
	public $id = null;
	public $rule_id = null;
	public $command = null;
	public $title = null;
	public $description	= null;
	public $avatar = null;
	public $count = null;
	public $created = null;
	public $published	= null;
	public $rule_limit	= null;
	public $alias = null;
	public $achieve_type = null;
	public $badge_achieve_rule = null;
	public $badge_remove_rule = null;
	public $points_threshold = null;

	public function __construct(&$db)
	{
		parent::__construct('#__discuss_badges', 'id', $db);
	}

	public function load($key = null, $permalink = false)
	{
		if(!$permalink) {
			return parent::load($key);
		}

		$db = ED::db();

		$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote($this->_tbl) . ' '
				. 'WHERE ' . $db->nameQuote('alias') . '=' . $db->Quote($key);
		$db->setQuery($query);

		$id = $db->loadResult();

		// Try replacing ':' to '-' since Joomla replaces it
		if (!$id) {
			$query	= 'SELECT id FROM ' . $this->_tbl . ' '
					. 'WHERE alias=' . $db->Quote(EDJString::str_ireplace(':', '-', $key));
			$db->setQuery($query);

			$id = $db->loadResult();
		}

		return parent::load( $id );
	}

	/**
	 * Determines if the user achieved this badge
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function achieved($userId = null)
	{
		$my = JFactory::getUser($userId);
		$userId = $my->id;

		static $items = array();

		$key = $this->id . $userId;

		if (!isset($items[$key])) {
			$db = ED::db();

			$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_badges_users') . ' '
					. 'WHERE ' . $db->nameQuote('user_id') . '=' . $db->Quote($userId) . ' '
					. 'AND ' . $db->nameQuote('badge_id') . '=' . $db->Quote($this->id);

			$db->setQuery($query);
			$items[$key] = $db->loadResult() > 0;
		}

		return $items[$key];
	}

	public function bindImage($elementName)
	{
		$file = $this->input->files->get($elementName, '');

		if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
			return false;
		}

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// @task: Test if the folder containing the badges exists
		if (!JFolder::exists(DISCUSS_BADGES_PATH)) {
			JFolder::create(DISCUSS_BADGES_PATH);
		}

		// @task: Test if the folder containing uploaded badges exists
		if (!JFolder::exists(DISCUSS_BADGES_UPLOADED)) {
			JFolder::create(DISCUSS_BADGES_UPLOADED);
		}

		require_once DISCUSS_CLASSES . '/simpleimage.php';

		$image	= new SimpleImage();
		$image->load($file['tmp_name']);

		if ($image->getWidth() > 64 || $image->getHeight() > 64) {
			return false;
		}

		$storage = DISCUSS_BADGES_UPLOADED;
		$name = md5($this->id . ED::date()->toSql()) . $image->getExtension();

		// @task: Create the necessary path
		$path = $storage . '/' . $this->id;

		if (!JFolder::exists($path)) {
			JFolder::create($path);
		}

		// @task: Copy the original image into the storage path
		JFile::copy( $file['tmp_name'] , $path . '/' . $name );

		// @task: Resize to the 16x16 favicon
		$image->resize(DISCUSS_BADGES_FAVICON_WIDTH , DISCUSS_BADGES_FAVICON_HEIGHT);
		$image->save($path . '/' . 'favicon_' . $name);

		$this->avatar = $this->id . '/' . $name;
		$this->thumbnail = $this->id . '/' . 'favicon_' . $name;

		return $this->store();
	}

	public function delete($pk = null)
	{
		// retrieve the badge title here
		$badgeTitle = $this->title;

		$state = parent::delete($pk);

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlog->log('COM_ED_ACTIONLOGS_DELETED_BADGES', 'badges', array(
			'badgeTitle' => JText::_($badgeTitle)
		));

		return $state;
	}

	public function getAvatar()
	{
		$path = DISCUSS_BADGES_URI . '/' . JPath::clean($this->avatar);
		return $path;
	}

	/**
	 * Retrieves the date the user achieved this badge.
	 *
	 * @access	public
	 * @param	null
	 * @return	string	A datetime value
	 **/
	public function getAchievedDate( $userId )
	{
		$badgeUser	= ED::table('BadgesUsers');
		$badgeUser->loadByUser($userId , $this->id);

		$date = ED::date($badgeUser->created);

		return $date->display(JText::_('DATE_FORMAT_LC1'));
	}

	/**
	 * Returns the total number of achievers for this badge.
	 *
	 * @access 	public
	 *
	 * @param null
	 * @return int 	The total number of achievers.
	 */
	public function getTotalAchievers()
	{
		$db 	= ED::db();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_badges_users' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_badges' ) . ' AS b '
				. 'ON b.' . $db->nameQuote( 'id' ) . '=a.' . $db->nameQuote( 'badge_id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'badge_id' ) . ' = ' . $db->Quote( $this->id ) . ' '
				. 'AND a.' . $db->nameQuote( 'published' ) . '=' .$db->Quote( 1 );
		$db->setQuery( $query );
		$total 	= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves the permalink for the badge
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getPermalink($xhtml = true)
	{
		$url = EDR::_('view=badges&layout=listings&id=' . $this->id, $xhtml);

		return $url;
	}

	/**
	 * List users that have already achieved this badge
	 **/
	public function getUsers( $excludeSelf = false )
	{
		$db		= ED::db();
		$query	= 'SELECT DISTINCT(`user_id`) FROM ' . $db->nameQuote( '#__discuss_badges_users' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_badges' ) . ' AS b '
				. 'ON b.' . $db->nameQuote( 'id' ) . '=a.' . $db->nameQuote( 'badge_id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'badge_id' ) . ' = ' . $db->Quote( $this->id ) . ' '
				. 'AND a.' . $db->nameQuote( 'published' ) . '=' .$db->Quote( 1 ) . ' '
				. 'AND a.' . $db->nameQuote( 'user_id' ) . '!= ' . $db->Quote( 0 );

		if ($excludeSelf) {
			$my = JFactory::getUser();
			$query	.= ' AND a.' . $db->nameQuote('user_id') . '!=' . $db->Quote($my->id);
		}
		$db->setQuery($query);

		$result	= $db->loadResultArray();

		if (!$result) {
			return false;
		}

		$users = array();

		//preload users
		ED::user($result);

		foreach ($result as $res) {
			$user = ED::user($res);

			$users[] = $user;
		}

		return $users;
	}

	/**
	 * Method to publish for the badges
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function publish($items = array(), $state = 1, $userId = 0)
	{
		$this->published = 1;

		$state = parent::store();

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlog->log('COM_ED_ACTIONLOGS_PUBLISHED_BADGES', 'badges', array(
			'link' => 'index.php?option=com_easydiscuss&view=badges&layout=form&id=' . $this->id,
			'badgeTitle' => $this->title
		));

		return $state;	
	}

	/**
	 * Method to unpublish for the badges
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function unpublish($items = array())
	{
		$this->published = 0;

		$state = parent::store();

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlog->log('COM_ED_ACTIONLOGS_UNPUBLISHED_BADGES', 'badges', array(
			'link' => 'index.php?option=com_easydiscuss&view=badges&layout=form&id=' . $this->id,
			'badgeTitle' => $this->title
		));

		return $state;
	}
}
