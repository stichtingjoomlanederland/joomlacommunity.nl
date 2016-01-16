<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

class DiscussBadges extends JTable
{
	public $id			= null;
	public $rule_id		= null;
	public $command		= null;
	public $title		= null;
	public $description	= null;
	public $avatar		= null;
	public $count		= null;
	public $created		= null;
	public $published	= null;
	public $rule_limit	= null;
	public $alias		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_badges' , 'id' , $db );
	}

	public function load( $key = null, $permalink = false )
	{
		if( !$permalink )
		{
			return parent::load( $key );
		}

		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $key );
		$db->setQuery( $query );

		$id		= $db->loadResult();

		// Try replacing ':' to '-' since Joomla replaces it
		if( !$id )
		{
			$query	= 'SELECT id FROM ' . $this->_tbl . ' '
					. 'WHERE alias=' . $db->Quote( JString::str_ireplace( ':' , '-' , $key ) );
			$db->setQuery( $query );

			$id		= $db->loadResult();
		}
		return parent::load( $id );
	}

	public function achieved( $userId )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_badges_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId ) . ' '
				. 'AND ' . $db->nameQuote( 'badge_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$exists	= $db->loadResult() > 0;

		return $exists;
	}

	public function bindImage( $elementName )
	{
		$file	= JRequest::getVar( $elementName , '' , 'FILES' );

		if( !isset( $file[ 'tmp_name' ] ) || empty( $file['tmp_name' ] ) )
		{
			return false;
		}

		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );

		// @task: Test if the folder containing the badges exists
		if( !JFolder::exists( DISCUSS_BADGES_PATH ) )
		{
			JFolder::create( DISCUSS_BADGES_PATH );
		}

		// @task: Test if the folder containing uploaded badges exists
		if( !JFolder::exists( DISCUSS_BADGES_UPLOADED ) )
		{
			JFolder::create( DISCUSS_BADGES_UPLOADED );
		}

		require_once DISCUSS_CLASSES . '/simpleimage.php';

		$image	= new SimpleImage();
		$image->load( $file['tmp_name'] );

		if( $image->getWidth() > 64 || $image->getHeight() > 64 )
		{
			return false;
		}

		$storage	= DISCUSS_BADGES_UPLOADED;
		$name		= md5( $this->id . DiscussHelper::getDate()->toMySQL() ) . $image->getExtension();

		// @task: Create the necessary path
		$path				= $storage . '/' . $this->id;

		if( !JFolder::exists( $path ) )
		{
			JFolder::create( $path );
		}

		// @task: Copy the original image into the storage path
		JFile::copy( $file['tmp_name'] , $path . '/' . $name );

		// @task: Resize to the 16x16 favicon
		$image->resize( DISCUSS_BADGES_FAVICON_WIDTH , DISCUSS_BADGES_FAVICON_HEIGHT );
		$image->save( $path . '/' . 'favicon_' . $name );

		$this->avatar		= $this->id . '/' . $name;
		$this->thumbnail	= $this->id . '/' . 'favicon_' . $name;

		return $this->store();
	}

	public function delete($pk = null)
	{
		$state	= parent::delete($pk);

		return $state;
	}

	public function getAvatar()
	{
		$path	= DISCUSS_BADGES_URI . '/' . JPath::clean( $this->avatar );
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
		$badgeUser	= DiscussHelper::getTable( 'BadgesUsers' );
		$badgeUser->loadByUser( $userId , $this->id );

		$date	= DiscussHelper::getHelper('Date')->dateWithOffset( $badgeUser->created );

		return $date->toFormat( '%d/%m/%Y' );
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
		$db 	= DiscussHelper::getDBO();
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
	 * List users that have already achieved this badge
	 **/
	public function getUsers( $excludeSelf = false )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT DISTINCT(`user_id`) FROM ' . $db->nameQuote( '#__discuss_badges_users' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_badges' ) . ' AS b '
				. 'ON b.' . $db->nameQuote( 'id' ) . '=a.' . $db->nameQuote( 'badge_id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'badge_id' ) . ' = ' . $db->Quote( $this->id ) . ' '
				. 'AND a.' . $db->nameQuote( 'published' ) . '=' .$db->Quote( 1 ) . ' '
				. 'AND a.' . $db->nameQuote( 'user_id' ) . '!= ' . $db->Quote( 0 );

		if( $excludeSelf )
		{
			$my		= JFactory::getUser();
			$query	.= ' AND a.' . $db->nameQuote( 'user_id' ) . '!=' . $db->Quote( $my->id );
		}
		$db->setQuery( $query );

		$result	= $db->loadResultArray();

		if( !$result )
		{
			return false;
		}

		$users	= array();

		foreach( $result as $res )
		{
			$user	= DiscussHelper::getTable( 'Profile' );
			$user->load( $res );

			$users[]	= $user;
		}

		return $users;
	}
}
