<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ED::import('admin:/tables/table');

class DiscussTags extends EasyDiscussTable
{
	public $id = null;
	public $title = null;
	public $alias = null;
	public $created	= null;
	public $published = null;
	public $user_id	= null;

	public function __construct(&$db)
	{
		parent::__construct('#__discuss_tags', 'id', $db);
	}

	public function load($id = null, $loadByTitle = false)
	{
		static $loaded = array();

		$sig = $id . $loadByTitle;
		$doBind = true;

		if (!isset($loaded[$sig])) {
			
			if (!$loadByTitle) {

				if (ED::cache()->exists($id, 'tag')) {
					$data = ED::cache()->get($id, 'tag');
					$loaded[$sig] = $data;
				} else {
					parent::load($id);
					$loaded[$sig] = $this;
				}
			} else {

				$db = ED::db();

				$query = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote($this->_tbl) . ' '
						. 'WHERE ' . $db->nameQuote('title') . '=' . $db->Quote($id);

				$db->setQuery($query);

				$db->setQuery($query);
				$tid = $db->loadResult();

				// Try replacing ':' to '-' since Joomla replaces it
				if (!$tid) {
					$query = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $this->_tbl . ' '
							. 'WHERE ' . $db->nameQuote('alias') . '=' . $db->Quote(JString::str_ireplace(':' , '-' , $id));
					$db->setQuery($query);

					$tid = $db->loadResult();
				}

				parent::load($tid);
				$loaded[$sig] = $this;

				$doBind = false;
			}
		}

		if ($doBind) {
			return parent::bind($loaded[$sig]);
		} else {
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
	public function bind($data, $ignore = array(), $generateAlias = true)
	{
		parent::bind( $data );

		if (!$this->created) {
			$this->created = ED::date()->toSql();
		}

		if ($generateAlias) {

			$alias = $this->alias ? $this->alias : $this->title;
			$alias = ED::permalinkSlug($alias);

			$tmp = $alias;

			$i = 1;
			while ($this->aliasExists($tmp, $this->id) || !$tmp) {
				$alias = !$alias ? ED::permalinkSlug($this->title) : $alias;
				$tmp = !$tmp ? ED::permalinkSlug($this->title) : $alias . '-' . $i;
				$i++;
			}
			$this->alias = $tmp;

			$this->alias = ED::getAlias($this->alias, 'tag', $this->id);			
		}
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
