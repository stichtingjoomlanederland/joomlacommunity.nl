<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class KomentoACLHelper
{
	protected static $rules = array();

	public static function allow( $type, $comment = '', $component = '', $cid = '' )
	{
		// for complicated acl situations
		// $type = ['edit', 'delete', 'publish', 'unpublish', 'stick', 'delete_attachment'];

		if( !empty( $comment ) && ( empty( $component ) || empty( $cid ) ) )
		{
			if( !is_object( $comment ) )
			{
				$comment = Komento::getComment( $comment );
			}

			$component = $comment->component;
			$cid = $comment->cid;
		}

		if( empty( $component ) || empty( $cid ) )
		{
			return false;
		}

		$profile		= Komento::getProfile();
		$application	= Komento::loadApplication( $component )->load( $cid );

		Komento::setCurrentComponent( $component );

		switch( $type )
		{
			case 'edit':
				if( $profile->id != 0 && ($profile->allow( 'edit_all_comment' ) || ( $profile->id == $application->getAuthorId() && $profile->allow( 'author_edit_comment' ) ) || ( $profile->id == $comment->created_by && $profile->allow( 'edit_own_comment' ))))
				{
					return true;
				}
				break;
			case 'delete':
				if( $profile->id != 0 && ($profile->allow( 'delete_all_comment' ) || ( $profile->id == $application->getAuthorId() && $profile->allow( 'author_delete_comment' ) ) || ( $profile->id == $comment->created_by && $profile->allow( 'delete_own_comment' ))))
				{
					return true;
				}
				break;
			case 'publish':
				if( $profile->allow( 'publish_all_comment' ) || ( $profile->id == $application->getAuthorId() && $profile->allow( 'author_publish_comment' ) ) )
				{
					return true;
				}
				break;
			case 'unpublish':
				if( $profile->allow( 'unpublish_all_comment' ) || ( $profile->id == $application->getAuthorId() && $profile->allow( 'author_unpublish_comment' ) ) )
				{
					return true;
				}
				break;
			case 'stick':
				if( $profile->allow( 'stick_all_comment' ) || ( $profile->id == $application->getAuthorId() && $profile->allow( 'author_stick_comment' ) ) )
				{
					return true;
				}
				break;
			case 'like':
				if( $profile->allow( 'like_comment' ) )
				{
					return true;
				}
				break;
			case 'report':
				if( $profile->allow( 'report_comment' ) )
				{
					return true;
				}
				break;
			case 'delete_attachment':
				if( $profile->allow( 'delete_all_attachment' ) || ( $profile->id == $application->getAuthorId() && $profile->allow( 'author_delete_attachment' ) ) || ( $profile->id == $comment->created_by && $profile->allow( 'delete_own_attachment' ) ) )
				{
					return true;
				}
				break;
		}

		return false;
	}

	public static function check( $action, $component = 'com_content', $userId )
	{
		$userId		= (int) $userId;
		$signature	= serialize(array($userId, $component));
		$result		= false;

		$rules = self::getRules( $userId, $component );

		if( isset( $rules[$action] ) )
		{
			$result = (bool) $rules[$action];
		}

		return $result;
	}

	public static function getRules( $userId, $component = 'com_content' )
	{
		$signature	= serialize( array( $userId, $component ) );

		if( empty( self::$rules[$signature] ) )
		{
			$profile = Komento::getProfile( $userId );

			$model	= Komento::getModel( 'acl' );
			$data	= array();

			// check user group specific rules
			$gids = $profile->getUsergroups();

			foreach( $gids as $gid )
			{
				$data[]	= $model->getAclObject( $gid, 'usergroup', $component );
			}

			// check user specific rules
			$data[] = $model->getAclObject( $userId, 'user', $component );

			// remove empty set
			foreach( $data as $key => $value )
			{
				if( empty( $value ) )
				{
					unset( $data[$key] );
				}
			}

			if( count( $data ) < 1 )
			{
				$data[] = KomentoACLHelper::getEmptySet( true );
			}

			self::$rules[$signature] = self::merge( $data );
		}

		return self::$rules[$signature];
	}

	private static function merge( $data )
	{
		$result	= array();

		$json = Komento::getJSON();

		foreach( $data as $ruleset )
		{
			if( !empty( $ruleset ) )
			{
				foreach( $ruleset as $key => $value )
				{
					if( isset( $result[$key] ) )
					{
						// This logics prioritizes FALSE
						if( (bool) $result[$key] )
						{
							$result[$key] = $value;
						}

						// This logics prioritizes TRUE
						/*if( !(bool) $result[$key] )
						{
							$result[$key] = $value;
						}*/
					}
					else
					{
						$result[$key] = $value;
					}
				}
			}
		}

		return $result;
	}

	public static function getEmptySet( $flat = false )
	{
		static $acl = null;

		if( empty( $acl ) )
		{
			$rulesFile = KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'acl.json';

			if( !JFile::exists( $rulesFile ) )
			{
				return false;
			}

			$contents = JFile::read( $rulesFile );

			$json = Komento::getJSON();

			$acl = $json->decode( $contents );
		}

		if( $flat === false )
		{
			return $acl;
		}

		$data = new stdClass();

		foreach( $acl as $section => $rules )
		{
			foreach( $rules as $key => $value )
			{
				$data->$key = $value;
			}
		}

		return $data;
	}
}
