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

jimport('joomla.utilities.date');

class DiscussLikesHelper
{
	public static function addLikes($contentId, $type, $userId = null)
	{
		if( is_null($userId) )
		{
			$userId	= JFactory::getUser()->id;
		}

		$date	= DiscussHelper::getDate();
		$likes	= DiscussHelper::getTable( 'Likes' );

		$params	= array();
		$params['type']			= $type;
		$params['content_id']	= $contentId;
		$params['created_by']	= $userId;
		$params['created']		= $date->toMySQL();

		$likes->bind($params);

		// Check if the user already likes or not. if yes, then return the id.
		$id	= $likes->exists();
		if( $id !== false )
		{
			return $id;
		}

		$likes->store();

		if($type == 'post')
		{
			// Now update the post
			$db		= DiscussHelper::getDBO();
			$query	= 'UPDATE `#__discuss_posts` SET `num_likes` = `num_likes` + 1';
			$query	.= ' WHERE `id` = ' . $db->Quote($contentId);
			$db->setQuery($query);
			$db->query();
		}

		return $likes->id;
	}

	public static function removeLikes( $postId , $userId , $type = DISCUSS_ENTITY_TYPE_POST )
	{
		$likes	= DiscussHelper::getTable( 'Likes' );
		$likes->loadByPost( $postId , $userId );

		if( $likes->type == 'post' )
		{
			// Now update the post by decrement the count
			$db		= DiscussHelper::getDBO();
			$query	= 'UPDATE `#__discuss_posts` SET `num_likes` = `num_likes` - 1';
			$query	.= ' WHERE `id` = ' . $db->Quote($likes->content_id);
			$db->setQuery($query);
			$db->query();
		}

		return $likes->delete();
	}

	public static function getLikesHTML( $contentId, $userId = null, $type = DISCUSS_ENTITY_TYPE_POST, $preloadedObj = null )
	{
		static $loaded = array();

		$db		= DiscussHelper::getDBO();
		$config	= DiscussHelper::getConfig();

		if( is_null($userId) ) {
			$userId = JFactory::getUser()->id;
		}

		$list   = '';
		if( is_null( $preloadedObj ) )
		{
			$displayFormat	= $config->get('layout_nameformat');
			$displayName	= '';

			switch($displayFormat){
				case "name" :
					$displayName = 'a.name';
					break;
				case "username" :
					$displayName = 'a.username';
					break;

				case "nickname" :
				default :
					$displayName = 'IF(c.`nickname` != \'\', c.`nickname`, a.`name`)';
					break;
			}

			$query	= 'SELECT a.id as `user_id`, b.id, ' . $displayName . ' AS `displayname`';
			$query	.= ' FROM ' . $db->nameQuote( '#__discuss_likes' ) . ' AS b';
			$query	.= ' INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS a';
			$query	.= '    on b.created_by = a.id';
			$query	.= ' INNER JOIN ' . $db->nameQuote( '#__discuss_users' ) . ' AS c';
			$query	.= '    on b.created_by = c.id';
			$query	.= ' WHERE b.`type` = '. $db->Quote($type);
			$query	.= ' AND b.content_id = ' . $db->Quote($contentId);
			$query	.= ' ORDER BY b.id DESC';

			$db->setQuery($query);

			$list	= $db->loadObjectList();

		}
		else
		{
			$list   = $preloadedObj;
		}

		if(count($list) <= 0)
		{
			return '';
		}

		$names  = array();

		for($i = 0; $i < count($list); $i++)
		{

			if($list[$i]->user_id == $userId)
			{
				array_unshift($names, JText::_('COM_EASYDISCUSS_YOU') );
			}
			else
			{
				$profile 	= DiscussHelper::getTable( 'Profile' );
				$profile->load( $list[ $i ]->user_id );

				$names[] = '<a href="' . $profile->getLink() . '">' . $list[$i]->displayname . '</a>';
			}
		}

		$max	= 3;
		$total	= count($names);
		$break	= 0;

		if($total == 1)
		{
			$break  = $total;
		}
		else
		{
			if($max >= $total)
			{
				$break  = $total - 1;
			}
			else if($max < $total)
			{
				$break  = $max;
			}
		}

		$main	= array_slice($names, 0, $break);
		$remain	= array_slice($names, $break);

		$stringFront	= implode(", ", $main);
		$returnString	= '';

		if(count($remain) > 1)
		{
			$returnString	= JText::sprintf('COM_EASYDISCUSS_AND_OTHERS_LIKE_THIS', $stringFront, count($remain));
		}
		else if(count($remain) == 1)
		{
			$returnString	= JText::sprintf('COM_EASYDISCUSS_AND_LIKE_THIS', $stringFront, $remain[0]);
		}
		else
		{
			if( $list[0]->user_id == $userId )
			{
				$returnString	= JText::sprintf('COM_EASYDISCUSS_LIKE_THIS', $stringFront);
			}
			else
			{
				$returnString	= JText::sprintf('COM_EASYDISCUSS_LIKES_THIS', $stringFront);
			}
		}

		return $returnString;
	}
}
