<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ED::import('admin:/tables/table');

class DiscussCategory extends EasyDiscussTable
{
	public $id = null;
	public $created_by = null;
	public $title = null;
	public $description = null;
	public $alias = null;
	public $created = null;
	public $status = null;
	public $published = null;
	public $ordering = null;
	public $avatar = null;
	public $parent_id = null;
	public $private = null;
	public $default = null;
	public $level = null;
	public $lft = null;
	public $rgt = null;
	public $params = null;
	public $container = null;
	public $language = null;

	public $checked_out = null;

	public function __construct(& $db)
	{
		parent::__construct('#__discuss_category', 'id', $db);
	}

	/**
	 * Override the parent's load behavior.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function load($key = null, $permalink = false)
	{
		static $loaded  = array();

		$sig    = $key  . (int) $permalink;
		$doBind = true;

		if (! isset($loaded[$sig])) {
			if (!$permalink) {

				// lets check if we cache this category or not.
				if (ED::cache()->exists($key, 'category')) {
					$data = ED::cache()->get($key, 'category');
					$loaded[$sig] = $data;
				} else {
					parent::load($key);
					$loaded[$sig] = $this;
				}

			} else {

				$db = ED::db();

				$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote($this->_tbl) . ' '
						. 'WHERE ' . $db->nameQuote('alias') . '=' . $db->Quote($key);
				$db->setQuery( $query );

				$id	= $db->loadResult();

				// Try replacing ':' to '-' since Joomla replaces it
				if ( !$id ) {
					$query	= 'SELECT `id` FROM ' . $this->_tbl . ' '
							. 'WHERE `alias` =' . $db->Quote(JString::str_ireplace(':' , '-' , $key));
					$db->setQuery($query);

					$id = $db->loadResult();
				}

				parent::load( $id );
				$loaded[$sig]   = $this;

				$doBind = false;
			}

		}

		if ( $doBind ) {
			return parent::bind($loaded[$sig]);
		} else {
			return $this->id;
		}
	}

	public function generateAlias( $title )
	{
		return JFilterOutput::stringURLSafe( $title );
	}

	/**
	 * Retrieves the total number of various counts for categories
	 *
	 * @since	3.0
	 * @access	public
	 */

	public function initCounts( $ids, $excludeFeatured = false)
	{
		//$ids = implode(',', $ids);

		$db	= DiscussHelper::getDBO();

		$excludeCats	= array();

		// get all private categories id
		$excludeCats	= DiscussHelper::getPrivateCategories();

		//getUnresolvedCount
		$getUnresolvedCountSQL = $this->buildCountQuery( $ids, $excludeFeatured, $excludeCats, 'unresolvedcount');
		$db->setQuery( $getUnresolvedCountSQL );
		$result = $db->loadObjectList();

		if( count($result) > 0 )
		{
			$sig = 'unresolvedcount-' . (int) $excludeFeatured;

			foreach( $result as $row )
			{
				self::$_data[ $row->category_id ][$sig] = $row->cnt;
			}
		}

		//getUnreadCount
		$getUnreadCountSQL = $this->buildCountQuery( $ids, $excludeFeatured, $excludeCats, 'unreadcount');
		$db->setQuery( $getUnreadCountSQL );
		$result = $db->loadObjectList();

		if( count($result) > 0 )
		{
			$sig = 'unreadcount-' . (int) $excludeFeatured;

			foreach( $result as $row )
			{
				self::$_data[ $row->category_id ][$sig] = $row->cnt;
			}
		}


		$getUnansweredCount = $this->buildCountQuery( $ids, $excludeFeatured, $excludeCats, 'unansweredcount');
		$db->setQuery( $getUnansweredCount );
		$result = $db->loadObjectList();

		if( count($result) > 0 )
		{
			$sig = 'unansweredcount-' . (int) $excludeFeatured;

			foreach( $result as $row )
			{
				self::$_data[ $row->category_id ][$sig] = $row->cnt;
			}
		}
	}

	private function buildCountQuery($ids, $excludeFeatured, $excludeCats, $type )
	{
		$db		= DiscussHelper::getDBO();
		$config	= DiscussHelper::getConfig();

		$mainQuery  	= '';
		$queryExclude	= '';

		$featuredOnly   = ( $excludeFeatured ) ? false : 'all';


		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		switch( $type )
		{
			case 'unresolvedcount':

				foreach( $ids as $category )
				{

					$query	= 'SELECT COUNT(a.`id`) as `cnt`, ' . $category. ' as `category_id` FROM `#__discuss_posts` AS a';

					$query	.= ' WHERE a.`parent_id` = ' . $db->Quote(0);
					$query	.= ' AND a.`published`=' . $db->Quote(1);

					// @rule: Should not calculate resolved posts
					$query	.= ' AND a.`isresolve`=' . $db->Quote(0);

					if( $featuredOnly === true )
					{
						$query	.= ' AND a.`featured`=' . $db->Quote(1);
					}
					else if( $featuredOnly === false)
					{
						$query	.= ' AND a.`featured`=' . $db->Quote(0);
					}

					if( $category )
					{
						$model 	= ED::model( 'Categories' );
						$childs	= $model->getChildIds( $category );
						$childs[]	 = $category;

						if( count( $childs ) == 1 )
						{
							$query	.= ' AND a.`category_id` = ' . $childs[0];
						}
						else
						{
							$query	.= ' AND a.`category_id` IN (' . implode( ',' , $childs ) . ')';
						}
					}

					$query	.= $queryExclude;

					$mainQuery[] = $query;
				}

				break;

			case 'unreadcount':
				$my	= JFactory::getUser();
				$profile = ED::user($my->id);

				$readPosts  = $profile->posts_read;
				$extraSQL   = '';

				if ($readPosts) {
					$readPosts  = unserialize($readPosts);
					if (count($readPosts) > 1) {
						$extraSQL   = implode( ',', $readPosts);
						$extraSQL   = ' AND `id` NOT IN (' . $extraSQL . ')';
					} else {
						$extraSQL   = ' AND `id` != ' . $db->Quote( $readPosts[0] );
					}
				}

				foreach ($ids as $category) {

					$catModel	= ED::model('Categories');
					$childs		= $catModel->getChildIds( $category );
					$childs[]	= $category;

					$categoryIds	= array_diff($childs, $excludeCats);

					$query = 'SELECT COUNT(`id`) as `cnt`, ' . $category. ' as `category_id` FROM `#__discuss_posts`';
					$query .= ' WHERE `published` = ' . $db->Quote( '1' );
					$query .= ' AND `parent_id` = ' . $db->Quote( '0' );
					$query .= ' AND `legacy` = ' . $db->Quote( '0' );

					if ($categoryIds) {
						if (count($categoryIds) == 1) {
							$query .= ' AND `category_id` = ' . $db->Quote( $categoryIds[0] );
						} else {
							$query .= ' AND `category_id` IN (' . implode( ',', $categoryIds ) .')';
						}
					}

					if ($excludeFeatured) {
						$query .= ' AND `featured` = ' . $db->Quote('0');
					}

					$query .= $extraSQL;

					$mainQuery[] = $query;

				}

				break;
			case 'unansweredcount':

				foreach( $ids as $category )
				{

					$excludeCats	= DiscussHelper::getPrivateCategories();
					$catModel		= ED::model('Categories');
					$childs			= $catModel->getChildIds( $category );
					$childs[]		= $category;

					$categoryIds	= array_diff($childs, $excludeCats);

					$query	= 'SELECT COUNT(a.`id`) as `cnt`, ' . $category. ' as `category_id` FROM `#__discuss_posts` AS a';
					$query	.= '  LEFT JOIN `#__discuss_posts` AS b';
					$query	.= '    ON a.`id`=b.`parent_id`';
					$query	.= '    AND b.`published`=' . $db->Quote('1');
					$query	.= ' WHERE a.`parent_id` = ' . $db->Quote('0');
					$query	.= ' AND a.`published`=' . $db->Quote('1');
					$query	.= ' AND a.`isresolve`=' . $db->Quote('0');
					$query	.= ' AND b.`id` IS NULL';

					if( $categoryIds )
					{
						if( count( $categoryIds ) == 1 )
						{
							$query .= ' AND a.`category_id` = ' . $db->Quote( $categoryIds[0] );
						}
						else
						{
							$query .= ' AND a.`category_id` IN (' . implode( ',', $categoryIds ) .')';
						}
					}

					if( $excludeFeatured )
						$query 	.= ' AND a.`featured`=' . $db->Quote( '0' );


					$mainQuery[] = $query;

				}

				break;

		}

		if( empty( $mainQuery ) )
			return '';

		$mainQuery = implode( ') UNION (', $mainQuery );
		$mainQuery  = '(' . $mainQuery . ')';

		return $mainQuery;
	}

	/**
	 * Override's parent's implementation of the store
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store($alterOrdering = false)
	{
		// Figure out the proper nested set model
		// No parent id, we use the current lft,rgt
		if ($alterOrdering) {

			if ($this->parent_id) {
				$left = $this->getLeft($this->parent_id);
				$this->lft = $left;
				$this->rgt = $this->lft + 1;

				// Update parent's right
				$this->updateRight($left);
				$this->updateLeft($left);
			} else {
				$this->lft = $this->getLeft() + 1;
				$this->rgt = $this->lft + 1;
			}
		}

		$state = parent::store();

		return $state;
	}

	/**
	 * Retrieves a list of user groups that can view posts under this category
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	public function getViewableGroups()
	{
		$db = ED::db();

		// Get accessible groups
		$query = 'SELECT `content_id` FROM `#__discuss_category_acl_map` WHERE `acl_id`="2" AND `category_id`=' . $db->Quote($this->id);
		$db->setQuery($query);
		$groups = $db->loadColumn();

		return $groups;
	}

	/**
	 * Render params from the category
	 *
	 * @since	4.0.21
	 * @access	public
	 */
	public function getParams()
	{
		static $data = array();

		if (!isset($data[$this->id])) {
			$params = new JRegistry($this->params);

			$data[$this->id] = $params;
		}

		return $data[$this->id];
	}

	public function updateLeft( $left, $limit = 0 )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'UPDATE ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'SET ' . $db->nameQuote( 'lft' ) . '=' . $db->nameQuote( 'lft' ) . ' + 2 '
				. 'WHERE ' . $db->nameQuote( 'lft' ) . '>=' . $db->Quote( $left );

		if( !empty( $limit ) )
			$query  .= ' and `lft`  < ' . $db->Quote( $limit );

		$db->setQuery( $query );
		$db->Query();
	}

	public function updateRight( $right, $limit = 0 )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'UPDATE ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'SET ' . $db->nameQuote( 'rgt' ) . '=' . $db->nameQuote( 'rgt' ) . ' + 2 '
				. 'WHERE ' . $db->nameQuote( 'rgt' ) . '>=' . $db->Quote( $right );

		if( !empty( $limit ) )
			$query  .= ' and `rgt`  < ' . $db->Quote( $limit );

		$db->setQuery( $query );
		$db->Query();
	}

	public function getLeft( $parent = DISCUSS_CATEGORY_PARENT )
	{
		$db		= DiscussHelper::getDBO();

		if( $parent != DISCUSS_CATEGORY_PARENT )
		{
		$query	= 'SELECT `rgt`' . ' '
				. 'FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $parent );
		}
		else
		{
		$query	= 'SELECT MAX(' . $db->nameQuote( 'rgt' ) . ') '
				. 'FROM ' . $db->nameQuote( $this->_tbl );
// 				. 'WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $parent );
		}
		$db->setQuery( $query );

		$left   = (int) $db->loadResult();

		return $left;
	}

}
