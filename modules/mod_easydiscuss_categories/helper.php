<?php
/**
 * @package     EasyDiscuss
 * @copyright   Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

class modEasydiscussCategoriesHelper
{
	public static function getData( $params )
	{
		$db				= DiscussHelper::getDBO();
		$my				= JFactory::getUser();

		$order			= $params->get('order', 'popular');
		$sort			= $params->get('sort', 'desc');
		$count			= (INT)trim($params->get('count', 0));
		$hideEmptyPost	= $params->get('hideemptypost', '0');
		$excludeChild	= $params->get( 'exclude_child_categories', '1' );
		$top_level		= 1;

		// get all private categories id
		$queryExclude   = '';
		$excludeCats	= DiscussHelper::getPrivateCategories();
		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query  = 'SELECT '
				. 'a.' . $db->nameQuote('id') . ', '
				. 'a.' . $db->nameQuote('title') . ', '
				. 'a.' . $db->nameQuote('avatar') . ', '
				. 'a.' . $db->nameQuote('alias') . ', '
				. 'a.' . $db->nameQuote('parent_id') . ', '
				. $db->quote($top_level) . ' AS level, '
				. 'COUNT(b.' . $db->nameQuote('id') . ') AS ' . $db->nameQuote('discussioncount') . ', '
				. '( SELECT COUNT(id) FROM ' . $db->nameQuote( '#__discuss_category' ) . ' '
				. 'WHERE lft < a.lft AND rgt > a.rgt AND a.lft != ' . $db->Quote( 0 ) . ' ) AS depth' . ' '
				. 'FROM ' . $db->nameQuote('#__discuss_category') . ' AS ' . $db->nameQuote('a') . ' '
				. 'LEFT JOIN ' . $db->nameQuote('#__discuss_posts') . ' AS ' . $db->nameQuote('b') . ' '
				. 'ON b.' . $db->nameQuote('category_id') . ' = a.' . $db->nameQuote('id') . ' '
				. 'AND b.' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( 0 ) . ' '
				. 'AND b.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 ) . ' '
				. 'WHERE a.' . $db->nameQuote('published'). ' = ' . $db->quote(1) . ' '
				. 'AND a.' . $db->nameQuote('parent_id') . ' = ' . $db->quote(0);





		$query	.= $queryExclude;

		if(!$hideEmptyPost)
		{
			$query  .= ' GROUP BY a.`id`';
		}
		else
		{
			$query  .= ' GROUP BY a.`id` HAVING (COUNT(b.`id`) > 0)';
		}

		switch($order)
		{
			case 'popular' :
				$orderBy    = ' ORDER BY `discussioncount` ';
				break;
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ';
				break;
			case 'latest' :
				$orderBy = ' ORDER BY a.`created` ';
				break;
			default :
				$orderBy = ' ORDER BY a.`lft` ';
				break;
		}
		$query  .= $orderBy.$sort;

		if(!empty($count))
		{
			$query  .= ' LIMIT ' . $count;
		}

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		$categories = array();
		if( !$excludeChild )
		{
			modEasydiscussCategoriesHelper::getChildCategories( $result , $params , $categories , ++$top_level );
		}
		else
		{
			$categories = $result;
		}

		// Since running the iteration will invert the ordering, we'll need to reverse it back.
		$categories = array_reverse( $categories );

		return $categories;
	}

	public static function getChildCategories( &$result , $params , &$categories, $level = 1 )
	{
		$db				= DiscussHelper::getDBO();
		$my				= JFactory::getUser();
		$mainframe		= JFactory::getApplication();
		$order			= $params->get('order', 'popular');
		$sort			= $params->get('sort', 'desc');
		$count			= (INT)trim($params->get('count', 0));
		$hideEmptyPost	= $params->get('hideemptypost', '0');

		$queryExclude   = '';
		$excludeCats	= DiscussHelper::getPrivateCategories();
		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		foreach($result as $row )
		{

			$categories[ $row->id ] = $row;
			$categories[ $row->id ]->childs = array();

			$query  = 'SELECT a.`id`, a.`title`, a.`parent_id`, a.`alias`, a.`avatar`, COUNT(b.`id`) AS `discussioncount`' . ', '
					. $db->quote($level) . ' AS level,'
					. ' ( SELECT COUNT(id) FROM ' . $db->nameQuote( '#__discuss_category' )
					. ' WHERE lft < a.lft AND rgt > a.rgt AND a.lft != ' . $db->Quote( 0 ) . ' ) AS depth'
					. ' FROM ' . $db->nameQuote( '#__discuss_category' ) . ' AS `a`'
					. ' LEFT JOIN '. $db->nameQuote( '#__discuss_posts' ) . ' AS b'
					. ' ON a.`id` = b.`category_id`'
					. ' AND b.`parent_id` = ' . $db->Quote( '0' )
					. ' AND b.`published` = ' . $db->Quote( '1' );

			$query  .= ' WHERE a.`published` = 1';
			$query  .= ' AND a.`parent_id`=' . $db->Quote( $row->id );
			$query   .= $queryExclude;

			if(!$hideEmptyPost)
			{
				$query  .= ' GROUP BY a.`id`';
			}
			else
			{
				$query  .= ' GROUP BY a.`id` HAVING (COUNT(b.`id`) > 0)';
			}

			switch($order)
			{
				case 'popular' :
					$orderBy = ' ORDER BY `discussioncount` ';
					break;
				case 'alphabet' :
					$orderBy = ' ORDER BY a.`title` ';
					break;
				case 'latest' :
				default :
					$orderBy = ' ORDER BY a.`created` ';
					break;
			}
			$query  .= $orderBy.$sort;

			$db->setQuery( $query );

			$records = $db->loadObjectList();

			if( $records )
			{
				modEasydiscussCategoriesHelper::getChildCategories( $records , $params , $categories[ $row->id ]->childs, ++$level );
			}
		}
	}

	public static function _getMenuItemId(&$params)
	{
		$itemid = DiscussRouter::getItemId('categories');
		return $itemid;
	}

	public static function getAvatar($category)
	{
		$categorytable = DiscussHelper::getTable( 'Category' );
		$categorytable->bind($category);

		return $categorytable->getAvatar();
	}

	public static function accessNestedCategories( &$categories , $selected , $params , $level = null )
	{
		$itemid = modEasydiscussCategoriesHelper::_getMenuItemId($params);

		foreach($categories as $category)
		{
			if( is_null( $level ) )
			{
				$level 	= 0;
			}

			$css = '';

			if($category->id == $selected)
			{
				$css = 'font-weight: bold;';
			}

			if( $params->get( 'layouttype' ) == 'tree' )
			{
				// $category->level	-= 1;
				$padding	= $level * 30;
			}

			ob_start();
			include( JModuleHelper::getLayoutPath('mod_easydiscuss_categories', 'item') );
			$contents 	= ob_get_contents();
			ob_end_clean();

			echo $contents;
			
			if( $params->get( 'layouttype' ) == 'tree' || $params->get( 'layouttype' ) == 'flat' )
			{
				if( isset( $category->childs ) && is_array( $category->childs ) )
				{
					modEasydiscussCategoriesHelper::accessNestedCategories( $category->childs , $selected, $params ,  $level + 1 );
				}
			}
		}
	}
}


