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

class DiscussCategoryHelper
{
	public static function getChildIds( $parentId = 0 )
	{
		static $childIds = array();

		if( !isset( $childIds[$parentId] ) )
		{
			$result = array();
			self::getNestedIds( $parentId , $result );

			$childIds[$parentId] = $result;
		}

		return $childIds[$parentId];
	}

	private static function getNestedIds( $parentId , &$result )
	{
		static $categories = array();

		if( empty($categories) )
		{
			$db		= DiscussHelper::getDBO();
			$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_category' ) . ' order by lft asc';

			$db->setQuery( $query );
			$items	= $db->loadObjectList();

			foreach ( $items as $category)
			{
				if( !empty( $category->parent_id ) )
				{
					$categories[$category->parent_id][] = $category;
				}
			}
		}

		if( isset( $categories[ $parentId ] ) )
		{
			foreach ($categories[ $parentId ] as $category)
			{
				$result[] = $category->id;
				self::getNestedIds( $category->id , $result );
			}

		}
	}

	public static function getChildCategories($parentId , $isPublishedOnly = false, $includePrivate = true)
	{
		static $categories = array();

		$sig = $parentId . '-' . (int) $isPublishedOnly . '-' . (int) $includePrivate;

		if(! array_key_exists($sig, $categories) )
		{
			$db			= DiscussHelper::getDBO();
			$my			= JFactory::getUser();
			$config		= DiscussHelper::getConfig();
			$mainframe	= JFactory::getApplication();

			$sortConfig = $config->get('layout_ordering_category','latest');

			$query	= 	'SELECT a.`id`, a.`title`, a.`alias`, a.`private`,a.`default`, a.`container`';
			$query	.=  ' FROM `#__discuss_category` as a';
			$query	.=  ' WHERE a.parent_id = ' . $db->Quote($parentId);

			if( $isPublishedOnly )
			{
				$query	.=  ' AND a.`published` = ' . $db->Quote('1');
			}

			if ( !$mainframe->isAdmin() )
			{

				if( !$includePrivate )
				{
					//check categories acl here.
					$catIds  = DiscussHelper::getAclCategories(DISCUSS_CATEGORY_ACL_ACTION_VIEW, $my->id, $parentId);

					if( count($catIds) > 0 )
					{
						$strIds = '';
						foreach( $catIds as $cat )
						{
							$strIds = ( empty( $strIds ) ) ? $cat->id : $strIds . ', ' . $cat->id;
						}

						$query .= ' AND a.id NOT IN (';
						$query .= $strIds;
						$query .= ')';
					}
				}

			}

			switch($sortConfig)
			{
				case 'alphabet' :
					$orderBy = ' ORDER BY a.`title` ';
					break;
				case 'ordering' :
					$orderBy = ' ORDER BY a.`lft` ';
					break;
				case 'latest' :
					$orderBy = ' ORDER BY a.`created` ';
					break;
				default	:
					$orderBy = ' ORDER BY a.`lft` ';
					break;
			}

			$sort = $config->get('layout_sort_category', 'asc');	

			$query  .= $orderBy.$sort;

			$db->setQuery($query);
			$result = $db->loadObjectList();

			$categories[$sig] = $result;
		}

		return $categories[$sig];
	}
}
