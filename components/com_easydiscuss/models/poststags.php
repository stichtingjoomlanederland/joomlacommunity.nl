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

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelPostsTags extends EasyDiscussModel
{
	static $_postTags = array();


	function setPostTagsBatch( $ids )
	{
		$db = DiscussHelper::getDBO();

		if( count( $ids ) > 0 )
		{

			$query	= 'SELECT a.`id`, a.`title`, a.`alias`, b.`post_id`';
			$query .= ' FROM `#__discuss_tags` AS a';
			$query .= ' LEFT JOIN `#__discuss_posts_tags` AS b';
			$query .= ' ON a.`id` = b.`tag_id`';
			if( count( $ids ) == 1 )
			{
				$query .= ' WHERE b.`post_id` = '.$db->Quote( $ids[0] );
			}
			else
			{
				$query .= ' WHERE b.`post_id` IN (' . implode(',', $ids) . ')';
			}

			$db->setQuery( $query );
			$result = $db->loadObjectList();

			if( count( $result ) > 0 )
			{
				foreach( $result as $item )
				{
					self::$_postTags[ $item->post_id ][] = $item;
				}
			}

			foreach( $ids as $id )
			{
				if(! isset( self::$_postTags[ $id ] ) )
				{
					self::$_postTags[ $id ] = array();
				}
			}


		}
	}



	/*
	 * method to get post tags.
	 *
	 * param postId - int
	 * return object list
	 */
	function getPostTags($postId)
	{

		if( isset( self::$_postTags[ $postId ] ) )
		{
			return self::$_postTags[ $postId ];
		}


		$db = DiscussHelper::getDBO();

		$query	= 'SELECT a.`id`, a.`title`, a.`alias`';
		$query .= ' FROM `#__discuss_tags` AS a';
		$query .= ' LEFT JOIN `#__discuss_posts_tags` AS b';
		$query .= ' ON a.`id` = b.`tag_id`';
		$query .= ' WHERE b.`post_id` = '.$db->Quote($postId);
		$query .= ' AND a.`published`=' . $db->Quote( 1 );

		$db->setQuery($query);

		if($db->getErrorNum() > 0)
		{
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		$result	= $db->loadObjectList();

		self::$_postTags[ $postId ] = $result;
		return $result;

	}

	function add( $tagId , $postId , $creationDate )
	{
		$db				= DiscussHelper::getDBO();

		$obj			= new stdClass();
		$obj->tag_id	= $tagId;
		$obj->post_id	= $postId;
		$obj->created	= $creationDate;

		return $db->insertObject( '#__discuss_posts_tags' , $obj );
	}

	function deletePostTag($postId)
	{
		$db	= DiscussHelper::getDBO();

		$query	= ' DELETE FROM ' . $db->nameQuote('#__discuss_posts_tags')
				. ' WHERE ' . $db->nameQuote('post_id') . ' =  ' . $db->quote($postId);

		$db->setQuery($query);
		$result	= $db->Query();

		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}

		return $result;
	}
}
