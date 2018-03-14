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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

jimport('joomla.access.access');

/**
 * Helper class for the user object for Joomla 2.5.
 *
 * @since	1.0
 * @access	public
 * @author	Mark Lee <mark@stackideas.com>
 */
class EasyDiscussUserHelperJoomla
{
	/**
	 * This is the current user's object.
	 * @var EasyDiscussUser
	 */
	private $access	= null;

	/**
	 * This is the current user's object.
	 * @var EasyDiscussUser
	 */
	private $user	= null;

	static $_usergroups = array();

	/**
	 * Class Constructor
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct( &$user )
	{
		$this->user 	=  $user;
	}

	public function getGroupChildrenTree($gid)
	{
		return JHTML::_('access.usergroups', 'jform[groups]', $gid, true);
	}

	/**
	 * Gets a list of user group that the user belongs to.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	Array
	 */
	public function getUserGroups()
	{
		if(! $this->user->id )
			return array();

		if(! isset( self::$_usergroups[ $this->user->id ] ) )
		{
			// Load our own db.
			$db		= FD::db();

			$query		= array();
			$query[]	= 'SELECT b.' . $db->nameQuote( 'group_id' ) . ' AS ' . $db->nameQuote( 'id' ) . ', a.' . $db->nameQuote( 'title' );
			$query[]	= 'FROM ' . $db->nameQuote( '#__usergroups' ) . ' AS a';
			$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__user_usergroup_map' ) . ' AS b';
			$query[]	= 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'group_id' );
			$query[]	= 'WHERE b.' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $this->user->id );

			$query 		= implode( ' ' , $query );
			$db->setQuery( $query );

			$result 	= $db->loadObjectList();

			if( !$result )
			{
				return $result;
			}

			$groups 	= array();

			foreach( $result as $row )
			{
				// Do not use title because by default, JUser::groups array format is id => id, NOT id => title
				// Because we set it as title here, it affected the original JUser object for the current logged in user
				// And hence forth causes some plugin to have issues
				// $groups[ $row->id ]	= $row->title;
				$groups[$row->id] = $row->id;
			}

			self::$_usergroups[ $this->user->id ] = $groups;
		}

		return self::$_usergroups[ $this->user->id ];
	}

	public static function setUserGroupsBatch( $userIds )
	{
		$db  = FD::db();
		$sql = $db->sql();

		$myids = array();
		foreach( $userIds as $id )
		{
			if(! isset( self::$_usergroups[ $id ] ) )
			{
				$myids[] = $id;
			}
		}

		if( $myids )
		{
			foreach( $myids as $uid )
			{
				self::$_usergroups[ $uid ] = array();
			}

			$myids = implode( ',', $myids );

			$query	= 'SELECT b.`user_id`, b.`group_id` AS `id`, a.`title`';
			$query	.= ' FROM `#__usergroups` AS a';
			$query	.= ' INNER JOIN `#__user_usergroup_map` AS b';
			$query	.= ' ON a.`id` = b.`group_id`';
			$query	.= ' WHERE b.`user_id` IN (' . $myids . ')';

			$sql->raw( $query );

			$db->setQuery( $sql );

			$result = $db->loadObjectList();

			foreach( $result as $row )
			{
				self::$_usergroups[ $row->user_id ][ $row->id ] = $row->title;
			}

		}

	}

	/**
	 * Binds the data given to the user object.
	 *
	 */
	public function bind( &$user , $data )
	{
		// Map the user groups based on the given data.
		if( !empty( $data[ 'gid' ] ) )
		{
			$user->groups	= array();

			foreach( $data[ 'gid' ] as $id )
			{
				$user->groups[ $id ]	= $id;
			}
		}
	}

	public function loadSession( $user )
	{
		return true;
	}

	public function getAccess()
	{
		if (!$this->access)
		{
			$this->access	= new JAccess();
		}

		return $this->access;
	}

}
