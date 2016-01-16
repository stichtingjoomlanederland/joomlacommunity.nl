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

class DiscussTags extends JTable
{
	/*
	 * The id of the tag
	 * @var int
	 */
	public $id			= null;

	/*
	* Tag title
	* @var string
	*/
	public $title		= null;

	/*
	* Tag alias
	* @var string
	*/
	public $alias		= null;

	/*
	* Created datetime of the tag
	* @var datetime
	*/
	public $created		= null;

	/*
	* Tag publishing status
	* @var int
	*/
	public $published	= null;

	/*
	* The author of the tag
	* @var int
	*/
	public $user_id		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_tags' , 'id' , $db );
	}

	public function load( $id = null , $loadByTitle = false )
	{
		static $loaded  = array();

		$sig    = $id  . (int) $loadByTitle;
		$doBind = true;

		if( ! isset( $loaded[ $sig ] ) )
		{
			if( !$loadByTitle)
			{
				parent::load( $id );
				$loaded[ $sig ] = $this;
			}
			else
			{

				$db		= DiscussHelper::getDBO();

				$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( $this->_tbl ) . ' '
						. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $id );
				$db->setQuery( $query );

				$db->setQuery($query);
				$tid	= $db->loadResult();

				// Try replacing ':' to '-' since Joomla replaces it
				if( !$tid )
				{
					$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $this->_tbl . ' '
							. 'WHERE ' . $db->nameQuote( 'title' ) . '=' . $db->Quote( JString::str_ireplace( ':' , '-' , $id ) );
					$db->setQuery( $query );

					$tid		= $db->loadResult();
				}

				parent::load( $tid );
				$loaded[ $sig ]   = $this;

				$doBind = false;
			}
		}

		if( $doBind )
		{
			return parent::bind( $loaded[ $sig ] );
		}
		else
		{
			return $this->id;
		}
	}

	public function loadOld( $id = null , $loadByTitle = false )
	{
		if( !$loadByTitle)
		{
			return parent::load( $id );
		}

		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT *';
		$query	.= ' FROM ' 	. $db->nameQuote('#__discuss_tags');
		$query	.= ' WHERE (' 	. $db->nameQuote('title') . ' = ' .  $db->Quote( JString::str_ireplace( ':' , '-' , $id ) );
		$query	.= ' OR ' 	. $db->nameQuote('alias') . ' = ' .  $db->Quote( JString::str_ireplace( ':' , '-' , $id ) ) . ')';
		$query	.= ' LIMIT 1';

		$db->setQuery($query);
		$result	= $db->loadObject();

		// Fixed if the the alias was translated
		if( !$result ) {
			$db->setQuery('SELECT * FROM `#__discuss_tags`');
			$tags	= $db->loadObjectList();

			foreach ($tags as $tag) {
				if( $id == DiscussHelper::permalinkSlug( $tag->alias ) ) {
					return parent::load( $tag->id );
				}
			}
		}

		$this->id			= $result->id;
		$this->title		= $result->title;
		$this->alias		= $result->alias;
		$this->created		= $result->created;
		$this->published	= $result->published;
		$this->user_id		= $result->user_id;

		return true;
	}

	public function aliasExists()
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_tags' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $this->alias );

		if( $this->id != 0 )
		{
			$query	.= ' AND ' . $db->nameQuote( 'id' ) . '!=' . $db->Quote( $this->id );
		}
		$db->setQuery( $query );

		return $db->loadResult() > 0 ? true : false;
	}

	public function exists( $title )
	{
		$db	= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) '
				. 'FROM ' 	. $db->nameQuote('#__discuss_tags') . ' '
				. 'WHERE ' 	. $db->nameQuote('title') . ' = ' . $db->quote($title) . ' '
				. 'LIMIT 1';
		$db->setQuery($query);

		$result	= $db->loadResult() > 0 ? true : false;

		return $result;
	}

	/**
	 * Overrides parent's bind method to add our own logic.
	 *
	 * @param Array $data
	 **/
	public function bind( $data , $ignore = array() )
	{
		parent::bind( $data );

		if( empty( $this->created ) )
		{
			$date			= DiscussHelper::getDate();
			$this->created	= $date->toMySQL();
		}

		jimport( 'joomla.filesystem.filter.filteroutput');

		$i	= 1;
		while( $this->aliasExists() || empty($this->alias) )
		{
			$this->alias	= empty($this->alias) ? $this->title : $this->alias . '-' . $i;
			$i++;
		}

		$this->alias 	= DiscussHelper::permalinkSlug( $this->alias );
	}

	/**
	 * Overrides parent's delete method to add our own logic.
	 *
	 * @return boolean
	 * @param object $db
	 */
	public function delete( $pk = null )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_posts_tags' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'tag_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$count	= $db->loadResult();

		if( $count > 0 )
		{
			$this->deletePostTag();
		}

		return parent::delete();
	}

	public function deletePostTag()
	{
		$db		= DiscussHelper::getDBO();

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
}
