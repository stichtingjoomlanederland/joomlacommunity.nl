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

class DiscussPostHelper
{

	static $_isLiked    = array();

	/*
	 * Function to determine a post should minimise or not.
	 * return true or false
	 */
	public static function toMinimizePost( $count )
	{
		$config		= DiscussHelper::getConfig();

		$breakPoint	= $config->get( 'layout_autominimisepost' );
		$minimize	= ($count <= $breakPoint && $breakPoint != 0 ) ? true : false;

		return $minimize;
	}

	public function setIsLikedBatch( $ids, $userId = null, $type = DISCUSS_ENTITY_TYPE_POST )
	{
		$db = DiscussHelper::getDBO();

		if( is_null($userId) )
		{
			$userId = JFactory::getUser()->id;
		}

		if( count( $ids ) > 0 )
		{
			$query  = 'SELECT `id`, `content_id` FROM `#__discuss_likes`';
			$query	.= ' WHERE `type` = ' . $db->Quote($type);
			if( count( $ids ) == 1 )
			{
				$query	.= ' AND `content_id` = ' . $db->Quote( $ids[0] );
			}
			else
			{
				$query	.= ' AND `content_id` IN (' . implode(',', $ids) . ')';
			}
			$query	.= ' AND `created_by` = ' . $db->Quote( $userId );


			$db->setQuery( $query );
			$result = $db->loadObjectList();

			if( count( $result ) > 0 )
			{
				foreach( $result as $item )
				{
					$sig    = $item->content_id .'-'. $userId .'-'. $type;
					self::$_isLiked[ $sig ] = $item->id;
				}
			}

			foreach( $ids as $id )
			{
				$sig    = $id .'-'. $userId .'-'. $type;

				if(! isset(self::$_isLiked[ $sig ]) )
				{
					self::$_isLiked[ $sig ] = '';
				}
			}

		}


	}

	public function isLiked( $contentId, $userId = null, $type = DISCUSS_ENTITY_TYPE_POST )
	{
		$db = DiscussHelper::getDBO();

		if( is_null($userId) )
		{
			$userId = JFactory::getUser()->id;
		}

		$sig    = $contentId .'-'. $userId .'-'. $type;

		if( isset(self::$_isLiked[ $sig ]) )
		{
			return self::$_isLiked[ $sig ];
		}

		$query  = 'SELECT `id` FROM `#__discuss_likes`'
				. ' WHERE `type` = ' . $db->Quote($type)
				. ' AND `content_id` = ' . $db->Quote( $contentId )
				. ' AND `created_by` = ' . $db->Quote( $userId );

		$db->setQuery($query);
		$result = $db->loadResult();

		self::$_isLiked[ $sig ] = $result;

		return self::$_isLiked[ $sig ];
	}

	public static function isFav( $postId, $userId = null )
	{
		$db = DiscussHelper::getDBO();

		if( is_null($userId) )
		{
			$userId = JFactory::getUser()->id;
		}

		$query	= 'SELECT ' . $db->nameQuote( 'id' )
				. ' FROM ' . $db->nameQuote( '#__discuss_favourites' )
				. ' WHERE ' . $db->nameQuote( 'user_id' ) . ' = ' . $db->quote( $userId )
				. ' AND ' . $db->nameQuote( 'post_id' ) . ' = ' . $db->quote( $postId ) ;

		$db->setQuery($query);
		$result = $db->loadResult();

		return (bool) $result;
	}

	public static function isVoted( $postId, $user_id = null )
	{
		if( $user_id )
		{
			$my		= JFactory::getUser( (int) $user_id);
		}
		else
		{
			$my		= JFactory::getUser();
		}

		if( !$my->id )
		{
			return 0;
		}

		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT count(d.id) as `isVoted` FROM `#__discuss_posts` AS a';
		$query	.= ' LEFT JOIN `#__discuss_votes` AS d ON a.`id` = d.`post_id`';
		$query	.= ' where a.id = ' . $db->Quote( $postId );
		$query	.= ' and d.user_id = ' . $db->Quote( $my->id );


		$db->setQuery( $query );
		$result	= (int) $db->loadResult();

		return $result;
	}

	public static function getAttachmentOwner($attachmentId)
	{
		$data   = self::getAttachementPostDetails( $attachmentId );
		return $data['user_id'];
	}

	public static function getAttachmentPostId($attachmentId)
	{
		$data   = self::getAttachementPostDetails( $attachmentId );
		return $data['post_id'];
	}

	public static function getAttachmentPostParentId($attachmentId)
	{
		$data   = self::getAttachementPostDetails( $attachmentId );
		return $data['parent_id'];
	}


	public static function getAttachementPostDetails( $attachmentId )
	{
		static $load = array();

		if( isset( $load[$attachmentId] ) )
		{
			return $load[$attachmentId];
		}


		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT p.user_id, p.`id` as `post_id`, p.`parent_id` as `parent_id` '
				. ' FROM `#__discuss_attachments` AS a'
				. ' LEFT JOIN `#__discuss_posts` AS p ON p.id = a.uid'
				. ' WHERE a.id = ' . $db->quote( (int) $attachmentId )
				. ' LIMIT 1';

		$db->setQuery( $query );
		$result	= $db->loadAssoc();

		if( empty( $result ) )
		{
			$result['user_id'] 		= null;
			$result['post_id'] 		= null;
			$result['parent_id'] 	= null;
		}

		return $result;

	}


}
