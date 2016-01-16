<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';
require_once DISCUSS_HELPERS . '/image.php';
require_once DISCUSS_HELPERS . '/date.php';

class DiscussCategory extends JTable
{
	public $id				= null;
	public $created_by		= null;
	public $title			= null;
	public $alias			= null;
	public $avatar			= null;
	public $parent_id		= null;
	public $private			= null;
	public $created			= null;
	public $status			= null;
	public $published		= null;
	public $ordering		= null;
	public $description		= null;
	public $params			= null;
	public $container		= null;

	public $level			= null;
	public $lft				= null;
	public $rgt				= null;

	private $_params		= null;
	private $_moderators	= null;

	static $_data 	= array();
	public $checked_out		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_category' , 'id' , $db );
	}

	public function load( $key = null , $permalink = false )
	{
		static $loaded  = array();

		$sig    = $key  . (int) $permalink;
		$doBind = true;

		if( ! isset( $loaded[ $sig ] ) )
		{
			if( !$permalink )
			{
				parent::load( $key );
				$loaded[ $sig ]   = $this;
				//return $this->id;
			}
			else
			{

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

				parent::load( $id );
				$loaded[ $sig ]   = $this;
			}

			$doBind = false;
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

	/**
	 * Overrides parent's delete method to add our own logic.
	 *
	 * @return boolean
	 * @param object $db
	 */
	public function delete( $pk = null )
	{
		$db		= DiscussHelper::getDBO();
		$config = DiscussHelper::getConfig();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'category_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$count	= $db->loadResult();

		if( $count > 0 )
		{
			return false;
		}

		$this->removeAvatar();

		$this->removeACL();

		return parent::delete();
	}

	public function removeACL()
	{
		$db		= DiscussHelper::getDbo();
		$query	= 'DELETE FROM `#__discuss_category_acl_map`'
				. ' WHERE `category_id` = ' . $db->quote( $this->id )
				. ' AND `type` = ' . $db->quote( 'group' );
		$db->setQuery( $query );
		$db->query();

		return true;
	}

	public function removeAvatar( $store = false )
	{
		$config		= DiscussHelper::getConfig();

		/* TODO */
		//remove avatar if previously already uploaded.
		$avatar = $this->avatar;

		if( $avatar != 'cdefault.png' && !empty($avatar))
		{

			$avatar_config_path	= $config->get('main_categoryavatarpath');
			$avatar_config_path	= rtrim($avatar_config_path, '/');
			$avatar_config_path	= JString::str_ireplace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

			$upload_path		= JPATH_ROOT . '/' . $avatar_config_path;

			$target_file_path	= $upload_path;
			$target_file		= JPath::clean($target_file_path . '/' . $avatar);

			if(JFile::exists( $target_file ))
			{
				if( !JFile::delete( $target_file ) )
				{
					return false;
				}

				$this->avatar	= '';

				if( $store )
				{
					$this->store();
				}
			}
		}
		return true;
	}

	public function aliasExists( $alias )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_category' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $alias );

		if( $this->id != 0 )
		{
			$query	.= ' AND ' . $db->nameQuote( 'id' ) . '!=' . $db->Quote( $this->id );
		}
		$db->setQuery( $query );

		return $db->loadResult() > 0 ? true : false;
	}

	public function generateAlias( $title )
	{
		return JFilterOutput::stringURLSafe( $title );
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

		// do not set alias during bind. @sam @ 07 dec 2012
 		//$this->setAlias();

	}

	public function setAlias()
	{
		jimport( 'joomla.filesystem.filter.filteroutput');

		$i		= 1;

		$alias	= $this->alias ? $this->alias : $this->title;
		$alias	= DiscussHelper::permalinkSlug( $alias );
		$tmp	= $alias;

		while( $this->aliasExists( $tmp ) || empty( $tmp ) )
		{
			$alias	= empty( $alias ) ? DiscussHelper::permalinkSlug( $this->title ) : $alias;
			$tmp	= empty( $tmp ) ? DiscussHelper::permalinkSlug( $this->title ) : $alias . '-' . $i;

			$i++;
		}

		$this->alias = $tmp;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getTitle()
	{
		return JText::_( $this->title );
	}

	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Retrieves the RSS link for the category.
	 *
	 * @since	3.0
	 * @access	public
	 * @return	string	The RSS url.
	 */
	public function getRSSLink()
	{
		return DiscussHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easydiscuss&view=categories&category_id=' . $this->id );
	}

	/**
	 * Retrieves the Atom link for the category.
	 *
	 * @since	3.0
	 * @access	public
	 * @return	string	The RSS url.
	 */
	public function getAtomLink()
	{
		return DiscussHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easydiscuss&view=categories&category_id=' . $this->id , true );
	}

	public function getAvatar()
	{
		$avatar_link = '';

		if($this->avatar == 'cdefault.png'
			|| $this->avatar == 'default_category.png'
			|| $this->avatar == 'components/com_easydiscuss/themes/default/images/default_category.png'
			|| $this->avatar == 'components/com_easydiscuss/assets/images/cdefault.png'
			|| $this->avatar == 'components/com_easydiscuss/themes/simplistic/images/default_category.png'
			|| empty($this->avatar))
		{
			$avatar_link   = 'components/com_easydiscuss/themes/simplistic/images/default_category.png';
		}
		else
		{
			$avatar_link   = DiscussImageHelper::getAvatarRelativePath('category') . '/' . $this->avatar;
		}

		return rtrim(JURI::root(), '/') . '/' . $avatar_link;
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

// 		//getNewCount
// 		$getNewCountSQL = $this->buildCountQuery( $ids, $excludeFeatured, $excludeCats, 'newcount');
// 		if( count($result) > 0 )
// 		{
// 			$sig = 'newcount';
//
// 			foreach( $result as $row )
// 			{
// 				self::$_data[ $row->category_id ][$sig] = $row->cnt;
// 			}
// 		}

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
						$model 	= DiscussHelper::getModel( 'Categories' );
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
			case 'newcount':

				foreach( $ids as $category )
				{
					$query	= 'SELECT COUNT(a.`id`) as `cnt`, ' . $category. ' as `category_id` FROM `#__discuss_posts` AS a';
					$query	.= ' WHERE a.`parent_id` = ' . $db->Quote('0');
					$query	.= ' AND a.`published`=' . $db->Quote('1');

					if( $featuredOnly === true )
					{
						$query	.= ' AND a.`featured`=' . $db->Quote('1');
					}
					else if( $featuredOnly === false)
					{
						$query	.= ' AND a.`featured`=' . $db->Quote('0');
					}

					$query	.= ' AND DATEDIFF( ' . $db->Quote( DiscussHelper::getDate()->toMySQL() ) . ', a.`created`) <= ' . $db->Quote( $config->get( 'layout_daystostaynew' ) );

					if( $category )
					{
						$model	= DiscussHelper::getModel( 'Categories' );
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
				$my			= JFactory::getUser();
				$profile    = DiscussHelper::getTable( 'Profile' );
				$profile->load( $my->id );


				$readPosts  = $profile->posts_read;
				$extraSQL   = '';


				if( $readPosts )
				{
					$readPosts  = unserialize( $readPosts );
					if( count( $readPosts ) > 1 )
					{
						$extraSQL   = implode( ',', $readPosts);
						$extraSQL   = ' AND `id` NOT IN (' . $extraSQL . ')';
					}
					else
					{
						$extraSQL   = ' AND `id` != ' . $db->Quote( $readPosts[0] );
					}
				}

				foreach( $ids as $category )
				{

					$catModel	= DiscussHelper::getModel('Categories');
					$childs		= $catModel->getChildIds( $category );
					$childs[]	= $category;

					$categoryIds	= array_diff($childs, $excludeCats);

					$query = 'SELECT COUNT(`id`) as `cnt`, ' . $category. ' as `category_id` FROM `#__discuss_posts`';
					$query .= ' WHERE `published` = ' . $db->Quote( '1' );
					$query .= ' AND `parent_id` = ' . $db->Quote( '0' );
					$query .= ' AND `legacy` = ' . $db->Quote( '0' );

					if( $categoryIds )
					{
						if( count( $categoryIds ) == 1 )
						{
							$query .= ' AND `category_id` = ' . $db->Quote( $categoryIds[0] );
						}
						else
						{
							$query .= ' AND `category_id` IN (' . implode( ',', $categoryIds ) .')';
						}
					}

					if( $excludeFeatured )
					{
						$query .= ' AND `featured` = ' . $db->Quote( '0' );
					}


					$query .= $extraSQL;

					$mainQuery[] = $query;

				}

				break;
			case 'unansweredcount':

				foreach( $ids as $category )
				{

					$excludeCats	= DiscussHelper::getPrivateCategories();
					$catModel		= DiscussHelper::getModel('Categories');
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
	 * Retrieves the total number of unresolved posts from this category.
	 *
	 * @since	3.0
	 * @access	public
	 * @return	int
	 */
	public function getUnresolvedCount( $excludeFeatured = false )
	{
		$sig = 'unresolvedcount-' . (int) $excludeFeatured;

		if(! isset( self::$_data[ $this->id ][$sig] ) )
		{
			$model 	= DiscussHelper::getModel( 'Posts' );
			$featuredOnly   = ( $excludeFeatured ) ? false : 'all';
			$count 	= $model->getUnresolvedCount( '' , $this->id , null, $featuredOnly );

			self::$_data[ $this->id ][$sig] = $count;
		}

		return self::$_data[ $this->id ][$sig];
	}

	/**
	 * Retrieves the total number of unresolved posts from this category.
	 *
	 * @since	3.0
	 * @access	public
	 * @return	int
	 */
	public function getNewCount()
	{
		if(! isset( self::$_data[ $this->id ]['newcount'] ) )
		{
			$model 	= DiscussHelper::getModel( 'Posts' );
			$count 	= $model->getNewCount( '' , $this->id , null );

			self::$_data[ $this->id ]['newcount'] = $count;
		}

		return self::$_data[ $this->id ]['newcount'];
	}


	/**
	 * Retrieves the total number of unread posts from this category by user
	 *
	 * @since	3.0
	 * @access	public
	 * @return	int
	 */
	public function getUnreadCount( $excludeFeatured = false)
	{
		$sig = 'unreadcount-' . (int) $excludeFeatured;

		if(! isset( self::$_data[ $this->id ][$sig] ) )
		{
			$model 	= DiscussHelper::getModel( 'Posts' );
			$count 	= $model->getUnreadCount( $this->id, $excludeFeatured );

			self::$_data[ $this->id ][$sig] = $count;
		}

		return self::$_data[ $this->id ][$sig];
	}

	/**
	 * Retrieves the total number of unanswered posts from this category.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getUnansweredCount( $excludeFeatured = false )
	{
		$sig = 'unansweredcount-' . (int) $excludeFeatured;

		if(! isset( self::$_data[ $this->id ][$sig] ) )
		{
			$count  =  DiscussHelper::getUnansweredCount( $this->id, $excludeFeatured );
			self::$_data[ $this->id ][$sig] = $count;
		}

		return self::$_data[ $this->id ][$sig];
	}

	/**
	 * Retrieves the number of post count from a particular category.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getPostCount()
	{
		$db		= DiscussHelper::getDBO();
		$my		= JFactory::getUser();

		$queryExclude		= '';
		$excludeCats		= array();

		// We need to determine if the user is a guest.
		// If it is a guest, we need to retrieve all private categories.
		if( !$my->id )
		{
			$query 		= 'SELECT a.' . $db->nameQuote( 'id' ) . ',' . $db->nameQuote( 'private' );
			$query 		.= ' FROM ' . $db->nameQuote( '#__discuss_category' ) . ' AS a';
			$query 		.= ' WHERE a.' . $db->nameQuote( 'private' ) . '=' . $db->Quote( 1 );

			$db->setQuery( $query );
			$result 	= $db->loadObjectList();

			for($i=0; $i < count($result); $i++)
			{
				$item	= $result[$i];
				$item->childs = null;

				DiscussHelper::buildNestedCategories($item->id, $item);

				$catIds		= array();
				$catIds[]	= $item->id;
				DiscussHelper::accessNestedCategoriesId($item, $catIds);

				$excludeCats	= array_merge($excludeCats, $catIds);
			}
		}

		$model 		= DiscussHelper::getModel( 'Categories' );
		$childs		= $model->getChildIds( $this->id );
		$total		= count( $childs );
		$subcategories		= array();
		$subcategories[]	= $this->id;

		if( $childs )
		{
			for( $i = 0; $i < $total; $i++ )
			{
				$subcategories[]	= $childs[ $i ];
			}
		}
		$filtered	= array_diff($subcategories, $excludeCats);

		if (empty($filtered))
		{
			// just a temp fix when DiscussHelper::getPrivateCategories()
			// failed to get correct result and it will cause the following
			// query fails with error 500.
			return;
		}

		$allowedCategories 	= array();

		foreach( $filtered as $filteredCategory )
		{
			if( $filteredCategory )
			{
				$allowedCategories[]	= $db->Quote( $filteredCategory );
			}
		}

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'category_id' ) . ' IN (' . implode( ',' , $allowedCategories ) . ') '
				. 'AND ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( 0 ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( DISCUSS_ID_PUBLISHED );
		$db->setQuery($query);

		$count 	= $db->loadResult();

		return $count;
	}

	public function getRecentPosts( $count = 5 )
	{
		$db 	= DiscussHelper::getDBO();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'category_id' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( 0 ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( DISCUSS_ID_PUBLISHED )
				. 'LIMIT 0,' . $count;
		$db->setQuery($query);

		$data	= $db->loadObjectList();
		$total	= count( $data );
		$posts	= array();

		for( $i = 0; $i < $total; $i++ )
		{
			$post	= JTable::getInstance( 'Posts' , 'Discuss' );
			$post->bind( $data[ $i ] );

			$posts[]	= $post;
		}

		return $posts;
	}

	public function getPermalink( $external = false )
	{
		return DiscussRouter::_( 'index.php?option=com_easydiscuss&view=categories&layout=listings&category_id=' . $this->id );
	}

	public function getRSSPermalink( $external = false )
	{
		return DiscussRouter::_( 'index.php?option=com_easydiscuss&format=feed&type=rss&view=categories&category_id=' . $this->id );
	}

	public function getChildCount()
	{
		$db = DiscussHelper::getDBO();

		$query  = 'SELECT count(1) FROM `#__discuss_category` WHERE `parent_id` = ' . $db->Quote($this->id);
		$db->setQuery($query);

		return $db->loadResult();
	}

	/*
	 * Retrieves a list of active bloggers that contributed in this category.
	 *
	 * @param	null
	 * @return	Array	An array of TableProfile objects.
	 */
	public function getActivePosters()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT DISTINCT(`user_id`) FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'category_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$rows		= $db->loadObjectList();

		if( !$rows )
		{
			return false;
		}

		$bloggers	= array();
		foreach( $rows as $row )
		{
			$profile	= JTable::getInstance( 'Profile' , 'Discuss' );
			$profile->load( $row->user_id );

			$bloggers[]	= $profile;
		}

		return $bloggers;
	}

	public function store( $alterOrdering = false )
	{
		if( empty( $this->created ))
		{
			$offset			= DiscussDateHelper::getOffSet();
			$newDate 		= DiscussHelper::getDate( '' , $offset );

			$this->created	= $newDate->toMySQL();
		}
		else
		{
			$newDate 		= DiscussHelper::getDate( $this->created );
			$this->created 	= $newDate->toMySQL();
		}

		$this->setAlias();

		// Figure out the proper nested set model
		// No parent id, we use the current lft,rgt
		if( $alterOrdering )
		{
			if( $this->parent_id )
			{
				$left		= $this->getLeft( $this->parent_id );
				$this->lft	= $left;
				$this->rgt	= $this->lft + 1;

				// Update parent's right
				$this->updateRight( $left );
				$this->updateLeft( $left );
			}
			else
			{
				$this->lft	= $this->getLeft() + 1;
				$this->rgt	= $this->lft + 1;
			}
		}

		return parent::store();
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

	public function move( $direction , $where = '' )
	{
		$db = DiscussHelper::getDBO();

		if( $direction == -1) //moving up
		{
			// getting prev parent
			$query  = 'select `id`, `lft`, `rgt` from `#__discuss_category` where `lft` < ' . $db->Quote($this->lft);
			if($this->parent_id == 0)
				$query  .= ' and parent_id = 0';
			else
				$query  .= ' and parent_id = ' . $db->Quote($this->parent_id);
			$query  .= ' order by lft desc limit 1';

			$db->setQuery($query);
			$preParent  = $db->loadObject();

			// calculating new lft
			$newLft = $this->lft - $preParent->lft;
			$preLft = ( ($this->rgt - $newLft) + 1) - $preParent->lft;

			//get prevParent's id and all its child ids
			$query  = 'select `id` from `#__discuss_category`';
			$query  .= ' where lft >= ' . $db->Quote($preParent->lft) . ' and rgt <= ' . $db->Quote($preParent->rgt);
			$db->setQuery($query);

			$preItemChilds = $db->loadResultArray();
			$preChildIds   = implode(',', $preItemChilds);
			$preChildCnt   = count($preItemChilds);

			//get current item's id and it child's id
			$query  = 'select `id` from `#__discuss_category`';
			$query  .= ' where lft >= ' . $db->Quote($this->lft) . ' and rgt <= ' . $db->Quote($this->rgt);
			$db->setQuery($query);

			$itemChilds = $db->loadResultArray();
			$childIds   = implode(',', $itemChilds);
			$ChildCnt   = count($itemChilds);

			//now we got all the info we want. We can start process the
			//re-ordering of lft and rgt now.
			//update current parent block
			$query  = 'update `#__discuss_category` set';
			$query  .= ' lft = lft - ' . $db->Quote($newLft);
			if( $ChildCnt == 1 ) //parent itself.
			{
				$query  .= ', `rgt` = `lft` + 1';
			}
			else
			{
				$query  .= ', `rgt` = `rgt` - ' . $db->Quote($newLft);
			}
			$query  .= ' where `id` in (' . $childIds . ')';

			$db->setQuery($query);
			$db->query();

			$query  = 'update `#__discuss_category` set';
			$query  .= ' lft = lft + ' . $db->Quote($preLft);
			$query  .= ', rgt = rgt + ' . $db->Quote($preLft);
			$query  .= ' where `id` in (' . $preChildIds . ')';

			$db->setQuery($query);
			$db->query();

			//now update the ordering.
			if( $this->ordering > 0 )
			{
				$query  = 'update `#__discuss_category` set';
				$query  .= ' `ordering` = `ordering` - 1';
				$query  .= ' where `id` = ' . $db->Quote($this->id);
				$db->setQuery($query);
				$db->query();
			}

			//now update the previous parent's ordering.
			$query  = 'update `#__discuss_category` set';
			$query  .= ' `ordering` = `ordering` + 1';
			$query  .= ' where `id` = ' . $db->Quote($preParent->id);
			$db->setQuery($query);
			$db->query();

			return true;
		}
		else //moving down
		{
			// getting next parent
			$query  = 'select `id`, `lft`, `rgt` from `#__discuss_category` where `lft` > ' . $db->Quote($this->lft);
			if($this->parent_id == 0)
				$query  .= ' and parent_id = 0';
			else
				$query  .= ' and parent_id = ' . $db->Quote($this->parent_id);
			$query  .= ' order by lft asc limit 1';

			$db->setQuery($query);
			$nextParent  = $db->loadObject();


			$nextLft	= $nextParent->lft - $this->lft;
			$newLft		= ( ($nextParent->rgt - $nextLft) + 1) - $this->lft;


			//get nextParent's id and all its child ids
			$query  = 'select `id` from `#__discuss_category`';
			$query  .= ' where lft >= ' . $db->Quote($nextParent->lft) . ' and rgt <= ' . $db->Quote($nextParent->rgt);
			$db->setQuery($query);

			$nextItemChilds	= $db->loadResultArray();
			$nextChildIds	= implode(',', $nextItemChilds);
			$nextChildCnt	= count($nextItemChilds);

			//get current item's id and it child's id
			$query	= 'select `id` from `#__discuss_category`';
			$query	.= ' where lft >= ' . $db->Quote($this->lft) . ' and rgt <= ' . $db->Quote($this->rgt);
			$db->setQuery($query);

			$itemChilds	= $db->loadResultArray();
			$childIds	= implode(',', $itemChilds);

			//now we got all the info we want. We can start process the
			//re-ordering of lft and rgt now.

			//update next parent block
			$query	= 'update `#__discuss_category` set';
			$query	.= ' `lft` = `lft` - ' . $db->Quote($nextLft);
			if( $nextChildCnt == 1 ) //parent itself.
			{
				$query  .= ', `rgt` = `lft` + 1';
			}
			else
			{
				$query  .= ', `rgt` = `rgt` - ' . $db->Quote($nextLft);
			}
			$query  .= ' where `id` in (' . $nextChildIds . ')';

			$db->setQuery($query);
			$db->query();

			//update current parent
			$query	= 'update `#__discuss_category` set';
			$query	.= ' lft = lft + ' . $db->Quote($newLft);
			$query	.= ', rgt = rgt + ' . $db->Quote($newLft);
			$query	.= ' where `id` in (' . $childIds. ')';

			$db->setQuery($query);
			$db->query();

			//now update the ordering.
			$query	= 'update `#__discuss_category` set';
			$query	.= ' `ordering` = `ordering` + 1';
			$query	.= ' where `id` = ' . $db->Quote($this->id);

			$db->setQuery($query);
			$db->query();

			if( $nextParent->ordering > 0)
			{
				//now update the previous parent's ordering.
				$query	= 'update `#__discuss_category` set';
				$query	.= ' `ordering` = `ordering` - 1';
				$query	.= ' where `id` = ' . $db->Quote($nextParent->id);

				$db->setQuery($query);
				$db->query();
			}

			return true;
		}
	}

	public function rebuildOrdering($parentId = null, $leftId = 0 )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'select `id` from `#__discuss_category`';
		$query	.= ' where parent_id = ' . $db->Quote( $parentId );
		$query	.= ' order by lft, id';

		$db->setQuery( $query );
		$children = $db->loadObjectList();

		// The right value of this node is the left value + 1
		$rightId = $leftId + 1;

		// execute this function recursively over all children
		foreach ($children as $node)
		{
			// $rightId is the current right value, which is incremented on recursion return.
			// Increment the level for the children.
			// Add this item's alias to the path (but avoid a leading /)
			$rightId = $this->rebuildOrdering($node->id, $rightId );

			// If there is an update failure, return false to break out of the recursion.
			if ($rightId === false) return false;
		}

		// We've got the left value, and now that we've processed
		// the children of this node we also know the right value.
		$updateQuery	= 'update `#__discuss_category` set';
		$updateQuery	.= ' `lft` = ' . $db->Quote( $leftId );
		$updateQuery	.= ', `rgt` = ' . $db->Quote( $rightId );
		$updateQuery	.= ' where `id` = ' . $db->Quote($parentId);

		$db->setQuery($updateQuery);

		// If there is an update failure, return false to break out of the recursion.
		if (! $db->query())
		{
			return false;
		}

		// Return the right value of this node + 1.
		return $rightId + 1;
	}

	public function getAssignedACL( $type = 'group' )
	{
		$db		= DiscussHelper::getDBO();
		$acl	= array();

		$query	= 'SELECT a.`category_id`, a.`content_id`, a.`status`, b.`id` as `acl_id`';
		$query	.= ' FROM `#__discuss_category_acl_map` as a';
		$query	.= ' LEFT JOIN `#__discuss_category_acl_item` as b';
		$query	.= ' ON a.`acl_id` = b.`id`';
		$query	.= ' WHERE a.`category_id` = ' . $db->Quote( $this->id );
		$query	.= ' AND a.`type` = ' . $db->Quote( $type );

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		if( count($result) > 0 )
		{
			$acl = null;
			if( $type == 'group' )
			{
				$joomlaGroups = DiscussHelper::getJoomlaUserGroups();
				$acl	= $this->_mapRules($result, $joomlaGroups);
			}
			else
			{
				$users	= $this->getAclUsers( $result );
				$acl	= $this->_mapRules($result, $users);
			}

			return $acl;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Return an array of moderators' user id
	 */
	public function getModerators()
	{
		if( !$this->_moderators )
		{
			$db			= DiscussHelper::getDBO();
			$moderators = array();

			$usergroups = $this->getAssignedModerator('group');
			if( !empty($usergroups) )
			{
				$gids = array();
				foreach ($usergroups as $usergroup)
				{
					$gids[]	= $usergroup->content_id;
				}

				$gids = implode(', ', $gids);

				$query = '';
				if( DiscussHelper::getJoomlaVersion() >= '1.6' )
				{
					$query = 'select b.`id` from `#__users` as b inner join `#__user_usergroup_map` AS a on b.`id` = a.`user_id`';
					$query .= ' WHERE a.`group_id` IN (' . $gids . ')';
				}
				else
				{
					$query	= 'SELECT c.`value` as `user_id`';
					$query	.= '  FROM `#__core_acl_aro` AS `c`';
					$query	.= '  INNER JOIN `#__core_acl_groups_aro_map` AS `d` ON c.`id` = d.`aro_id`';
					$query  .= ' where d.group_id IN (' . $gids . ')';
					$query	.= ' AND c.`section_value` = ' . $db->Quote('users');
				}

				$db->setQuery($query);
				$result = $db->loadResultArray();

				$moderators = array_merge($moderators, $result);
			}

			$users = $this->getAssignedModerator('user');
			if( !empty($users) )
			{
				foreach ($users as $user) {
					$moderators[] = $user->content_id;
				}
			}

			$this->_moderators = array_unique($moderators);
			sort($this->_moderators);
		}

		return $this->_moderators;
	}

	public function getAssignedModerator( $type = 'group' )
	{
		$db		= DiscussHelper::getDBO();
		$acl	= array();

		if( $type == 'group' )
		{
			if( DiscussHelper::getJoomlaVersion() >= '1.6' )
			{
				$querySelect	= ' b.title AS title';
				$queryJoin		= ' LEFT JOIN `#__usergroups` AS b ON b.id = a.content_id';
			} else {
				$querySelect	= ' b.name AS title';
				$queryJoin		= ' LEFT JOIN `#__core_acl_aro_groups` AS b ON b.id = a.content_id';
			}
		}
		else // type is user
		{
			$querySelect	= ' b.name AS title';
			$queryJoin		= ' LEFT JOIN `#__users` AS b ON b.id = a.content_id';
		}

		$query	= 'SELECT a.*, '
				. $querySelect
				. ' FROM `#__discuss_category_acl_map` AS a'
				. $queryJoin
				. ' WHERE a.`category_id` = ' . $db->quote( $this->id )
				. ' AND a.`acl_id` = ' . $db->quote( DISCUSS_CATEGORY_ACL_MODERATOR )
				. ' AND a.`type` = ' . $db->quote( $type )
				. ' AND a.`status` = 1';

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		return $result;
	}

	public function getAclUsers( $aclUsers )
	{
		$db = DiscussHelper::getDBO();

		$users  = array();

		foreach( $aclUsers as $item)
		{
			$users[] = $item->content_id;
		}

		$userlist   = '';

		foreach($users as $user)
		{
			$userlist .= ( $userlist == '') ? $db->Quote($user) : ', ' . $db->Quote($user);
		}


		$query  = 'select id, name from `#__users` where `id` IN (' . $userlist . ')';
		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	public function saveACL( $post )
	{
		$catRuleItems	= JTable::getInstance( 'CategoryAclItem' , 'Discuss' );
		$categoryRules	= $catRuleItems->getAllRuleItems();

		$itemtypes		= array('group', 'user');

		$added  = 0;

		foreach( $categoryRules as $rule)
		{
			foreach( $itemtypes as $type )
			{
				$key	= 'acl_'.$type.'_'.$rule->action;
				if( isset( $post[ $key ] ) )
				{
					if( count( $post[ $key ] ) > 0)
					{
						foreach( $post[ $key ] as $contendid)
						{
							//now we reinsert again.
							$catRule	= JTable::getInstance( 'CategoryAclMap' , 'Discuss' );

							$catRule->category_id	= $this->id;
							$catRule->acl_id		= $rule->id;
							$catRule->type			= $type;
							$catRule->content_id	= $contendid;
							$catRule->status		= '1';
							$catRule->store();

							$added++;
						} //end foreach

					} //end if
				}//end if
			}
		}


		// we need to handle if user forget to set the acl, we will assign a default
		// set here using Joomla group.
		if( empty( $added ) )
		{
			// let assign this category a default acl items.

			$defaultKeys	= array('1','2','3','4');
			$joomlaGroups	= DiscussHelper::getJoomlaUserGroups();

			foreach( $defaultKeys as $ruleId)
			{
				foreach( $joomlaGroups as $joomlaGroup)
				{
					$catRule	= JTable::getInstance( 'CategoryAclMap' , 'Discuss' );

					$catRule->category_id	= $this->id;
					$catRule->acl_id		= $ruleId;
					$catRule->type			= 'group';
					$catRule->content_id	= $joomlaGroup->id;
					$catRule->status		= '1';
					$catRule->store();
				}
			}

		}

	}

	public function deleteACL( $aclId = '' )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'delete from `#__discuss_category_acl_map`';
		$query	.= ' where `category_id` = ' . $db->Quote( $this->id );
		if( !empty($aclId) )
			$query	.= ' and `acl_id` = ' . $db->Quote( $aclId );

		$db->setQuery( $query );
		$db->query();

		return true;
	}

	public function _mapRules( $catRules, $joomlaGroups)
	{
		$db		= DiscussHelper::getDBO();
		$acl	= array();

		$query	= 'select * from `#__discuss_category_acl_item` order by id';
		$db->setQuery( $query );

		$result = $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		foreach( $result as $item )
		{
			$aclId		= $item->id;
			$default	= $item->default;

			foreach( $joomlaGroups as $joomla )
			{
				$groupId		= $joomla->id;
				$catRulesCnt	= count($catRules);
				//now match each of the catRules
				if( $catRulesCnt > 0)
				{
					$cnt = 0;
					foreach( $catRules as $rule)
					{
						if($rule->acl_id == $aclId && $rule->content_id == $groupId)
						{
							$acl[$aclId][$groupId]				= new stdClass();
							$acl[$aclId][$groupId]->status		= $rule->status;
							$acl[$aclId][$groupId]->acl_id		= $aclId;
							$acl[$aclId][$groupId]->groupname	= $joomla->name;
							$acl[$aclId][$groupId]->groupid		= $groupId;
							break;
						}
						else
						{
							$cnt++;
						}
					}

					if( $cnt == $catRulesCnt)
					{
						//this means the rules not exist in this joomla group.
						$acl[$aclId][$groupId]				= new stdClass();
						$acl[$aclId][$groupId]->status		= '0';
						$acl[$aclId][$groupId]->acl_id		= $aclId;
						$acl[$aclId][$groupId]->groupname	= $joomla->name;
						$acl[$aclId][$groupId]->groupid		= $groupId;
					}
				}
				else
				{
					$acl[$aclId][$groupId]->status		= $default;
					$acl[$aclId][$groupId]->acl_id		= $aclId;
					$acl[$aclId][$groupId]->groupname	= $joomla->name;
					$acl[$aclId][$groupId]->groupid		= $groupId;
				}
			}
		}

		return $acl;
	}

	/**
	 * Determines if the user can start a new discussion in this category.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function canPost()
	{
		static $canPost 	= null;

		// Admin is always allowed
		if( DiscussHelper::isSiteAdmin() )
		{
			return true;
		}

		if( $this->private == DISCUSS_PRIVACY_PUBLIC )
		{
			return true;
		}

		if(! isset( $canPost[ $this->id ] ) )
		{
			$my 		= JFactory::getUser();
			$config 	= DiscussHelper::getConfig();
			$allowed 	= false;

			// If this is a private category, we need to do additional checks here.
			$excludeCats 	= DiscussHelper::getPrivateCategories( DISCUSS_CATEGORY_ACL_ACTION_SELECT );

			if( in_array( $this->id , $excludeCats ) )
			{
				$allowed 	= false;
			}
			else
			{
				$allowed 	= true;
			}

			$canPost[$this->id ] = $allowed;

		}

		return $canPost[ $this->id ];
	}

	public function canPublicAccess()
	{
		if( $this->private == DISCUSS_PRIVACY_PUBLIC )
			return true;

		if( $this->private == DISCUSS_PRIVACY_PRIVATE )
			return false;

		// if reach here means the private is a acl type.
		// lets check if this category is allow public access or not.
		$db		= DiscussHelper::getDBO();

		$publicGrp = ( DiscussHelper::getJoomlaVersion() == '1.5' ) ? '0' : '1';

		$query	= 'SELECT a.id'
				. ' FROM `#__discuss_category_acl_map` AS a'
				. ' WHERE a.`category_id` = ' . $db->Quote( $this->id )
				. ' AND a.`acl_id` = ' . $db->Quote( DISCUSS_CATEGORY_ACL_ACTION_VIEW )
				. ' AND a.`type` = ' . $db->Quote( 'group' )
				. ' AND a.`content_id` = ' . $db->Quote( $publicGrp )
				. ' AND a.`status` = 1';

		$db->setQuery( $query );
		$result = $db->loadResult();

		return ( empty($result) ) ? false : true;
	}

	public function canAccess()
	{
		$privCats   = DiscussHelper::getPrivateCategories();

		if( in_array( $this->id,  $privCats) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function canReply()
	{
		$privCats   = DiscussHelper::getPrivateCategories( DISCUSS_CATEGORY_ACL_ACTION_REPLY );

		if( in_array( $this->id,  $privCats) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function canViewReplies()
	{
		if( DiscussHelper::isModerator( $this->id ) )
		{
			return true;
		}

		$privCats	= DiscussHelper::getPrivateCategories( DISCUSS_CATEGORY_ACL_ACTION_VIEWREPLY );
		$canView	= in_array( $this->id,  $privCats) ? false : true;

		return $canView;
	}

	public function getPathway()
	{
		$obj		= new stdClass();
		$obj->link	= $this->getPermalink();
		$obj->title	= $this->getTitle();

		$data		= array( $obj );

		// @task: Detects if it has any parent.
		if( !$this->parent_id )
		{
			return $data;
		}

		$this->getNestedPathway( $this->parent_id , $data );

		// Reverse the data so we get it in a proper order.
		$data	= array_reverse( $data );

		return $data;
	}

	private function getNestedPathway( $parent , &$data )
	{
		$category	= DiscussHelper::getTable( 'Category' );
		$category->load( $parent );

		$obj		= new stdClass();
		$obj->title	= $category->getTitle();
		$obj->link	= $category->getPermalink();

		$data[]		= $obj;

		if( $category->parent_id )
		{
			$this->getNestedPathway( $category->parent_id , $data );
		}
	}

	public function loadParams()
	{
		$this->_params 	= DiscussHelper::getRegistry( $this->params );
	}

	/**
	 * Returns parameter values.
	 *
	 * @access 	public
	 * @param 	string $index 	The parameter key
	 * @return 	mixed
	 **/
	public function getParam( $index , $default = '')
	{
		if( !isset( $this->_params ) )
		{
			$this->loadParams();
		}

		return $this->_params->get( $index , $default );
	}

	public function checkin($pk = null)
	{
		return true;
	}
}
