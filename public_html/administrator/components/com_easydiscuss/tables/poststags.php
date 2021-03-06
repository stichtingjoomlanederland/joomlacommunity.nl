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

ED::import('admin:/tables/table');

class DiscussPostsTags extends EasyDiscussTable
{
	/*
	 * The id of the tag
	 * @var int
	 */
	public $id		= null;

	/*
	* Post ID
	* @var string
	*/
	public $post_id	= null;

	/*
	* Tag ID
	* @var string
	*/
	public $tag_id	= null;


	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_posts_tags' , 'id' , $db );
	}

// 	function load( $id )
// 	{
// 		if( !$loadByTitle)
// 		{
// 			return parent::load( $id );
// 		}
//
// 		$db		= ED::db();
// 		$query	= 'SELECT *';
// 		$query	.= ' FROM ' 	. $db->nameQuote('#__discuss_posts_tags');
// 		$query	.= ' WHERE (' 	. $db->nameQuote('title') . ' = ' .  $db->Quote( EDJString::str_ireplace( ':' , '-' , $id ) );
// 		$query	.= ' OR ' 	. $db->nameQuote('alias') . ' = ' .  $db->Quote( EDJString::str_ireplace( ':' , '-' , $id ) ) . ')';
// 		$query	.= ' LIMIT 1';
//
// 		$db->setQuery($query);
// 		$result	= $db->loadObject();
//
// 		$this->id			= $result->id;
// 		$this->post_id		= $result->post_id;
// 		$this->tag_id		= $result->tag_id;
// 		$this->created		= $result->created;
// 		$this->published	= $result->published;
// 		$this->user_id		= $result->user_id;
//
// 		return true;
// 	}

// 	function aliasExists()
// 	{
// 		$db		= ED::db();
//
// 		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_tags' ) . ' '
// 				. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $this->alias );
//
// 		if( $this->id != 0 )
// 		{
// 			$query	.= ' AND ' . $db->nameQuote( 'id' ) . '!=' . $db->Quote( $this->id );
// 		}
// 		$db->setQuery( $query );
//
// 		return $db->loadResult() > 0 ? true : false;
// 	}

	public function exists( $title )
	{
		$db	= ED::db();

		$query	= 'SELECT COUNT(1) '
				. 'FROM ' 	. $db->nameQuote('#__discuss_tags') . ' '
				. 'WHERE ' 	. $db->nameQuote('title') . ' = ' . $db->quote($title) . ' '
				. 'LIMIT 1';
		$db->setQuery($query);

		$result	= $db->loadResult() > 0 ? true : false;

		return $result;
	}

	/**
	 * Overrides parent's delete method to add our own logic.
	 *
	 * @return boolean
	 * @param object $db
	 */
	public function delete( $pk = null )
	{
		$db		= ED::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_posts_tags' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'tag_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$count	= $db->loadResult();

		if( $count > 0 )
		{
			return false;
		}

		return parent::delete();
	}

	// method to delete all the blog post that associated with the current tag
	public function deletePostTag()
	{
		$db		= ED::db();

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_posts_tags' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'tag_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		if($db->query($db))
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	// method to delete all the blog post that associated with the current tag
	public function clearTags($postId)
	{
		$db = ED::db();

		$query = "delete from " . $db->nameQuote('#__discuss_posts_tags');
		$query .= " where " . $db->nameQuote('post_id') . ' = ' . $db->Quote($postId);

		$db->setQuery( $query );
		$state = $db->query();

		return $state;
	}


}
