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

class DiscussModeratorHelper
{
	public static function isModerator( $categoryId = null , $userId = null )
	{
		static $result	= array();

		if( !$userId )
		{
			$userId 	= JFactory::getUser()->id;
		}

		// If user id is 0, we know for sure they are not a moderator.
		if (!$userId) {
			return false;
		}

		// Site admin is always a moderator.
		if(DiscussHelper::isSiteAdmin($userId))
		{
			return true;
		}

		// If category is not supplied, caller might just want to check if
		// the user is a moderator of any category.
		if( is_null( $categoryId ) )
		{
			if( isset( $result[ 'isModerator' ] ) )
			{
				return $result[ 'isModerator' ];
			}

			$db 		= DiscussHelper::getDBO();

			// Get the user's groups first.
			$gids 		= DiscussHelper::getUserGids( $userId );

			// Now, check if the current user has any assignments to this acl id or not.
			$query 		= array();
			$query[]	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_category_acl_map' );
			$query[]	= 'WHERE ' . $db->nameQuote( 'acl_id' ) . ' = ' . $db->Quote( DISCUSS_CATEGORY_ACL_MODERATOR );

			if( $userId )
			{
				$query[]	= 'AND ((';

				if( $gids )
				{
					$query[]	= $db->nameQuote( 'type' ) . '=' . $db->Quote( 'group' );
					$query[]	= 'AND ' . $db->nameQuote( 'content_id' ) . ' IN(';

					for( $i = 0; $i < count( $gids ); $i++ )
					{
						$query[]	= $db->Quote( $gids[ $i ] );

						if( next( $gids ) !== false )
						{
							$query[]	= ',';
						}
					}

					$query[]	= ')';
				}

				$query[]	= ')';
				$query[]	= 'OR';
				$query[]	= '(' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( 'user' );
				$query[]	= 'AND ' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote( $userId );
				$query[]	= '))';
			}

			$query 		= implode( ' ' , $query );

			$db->setQuery( $query );

			$count 			= $db->loadResult();

			$isModerator	= $count > 0;

			$result[ 'isModerator' ]	= $isModerator;

			return $result[ 'isModerator' ];
		}

		if( !array_key_exists('groupId', $result) )
		{
			$table = DiscussHelper::getTable( 'Category' );
			$table->load( $categoryId );
			$result[$categoryId] = $table->getModerators();
		}

		$isModerator = in_array($userId, $result[$categoryId]);

		return $isModerator;
	}

	// Return an array of moderators names, given an array of moderators ids
	public static function getModeratorsNames( $moderatorIds )
	{
		$modNames = array();

		if( !empty($moderatorIds) )
		{
			foreach ($moderatorIds as $userId) {
				$profile = DiscussHelper::getTable( 'Profile' );
				$profile->load($userId);
				$modNames[] = $profile->getLinkHTML();
			}
		}

		return $modNames;
	}

	public static function showModeratorNameHTML( $categoryId )
	{

		$category = DiscussHelper::getTable( 'Category' );
		if( !$category->load( $categoryId ) )
		{
			return '';
		}
		$moderators = $category->getModerators();
		$modNames = self::getModeratorsNames( $moderators );

		if( !empty($modNames) )
		{
			return JText::_('COM_EAYDISCUSS_CATEGORY_MODERATORS') . ': ' . implode(', ', $modNames);
		}

		return false;
	}

	public static function getSelectOptions( $categoryId )
	{
		$category	= DiscussHelper::getTable( 'Category' );
		$category->load( (int) $categoryId );
		$mods		= $category->getModerators();

		$options	= array();
		$options[]	= JHTML::_('select.option', 0, JText::_('COM_EASYDISCUSS_MODERATOR_OPTION_NONE'));

		foreach ($mods as $userId) {
			$profile = DiscussHelper::getTable( 'Profile' );
			$profile->load( $userId );

			$options[] = JHTML::_('select.option', $userId, $profile->getName());
		}

		return $options;
	}

	/**
	 * Displays a drop down list of moderators on the site.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int 	The unique category id.
	 *
	 */
	public static function getModeratorsDropdown( $categoryId )
	{
		$category = DiscussHelper::getTable( 'Category' );

		if( !$category->load( $categoryId ) )
		{
			return '';
		}

		$moderators		= array();
		$moderators[0]	= JText::_('COM_EASYDISCUSS_MODERATOR_OPTION_NONE');

		$list 			= $category->getModerators();

		// Find super admins.
		$siteAdmins 	= DiscussHelper::getSAUsersIds();
		foreach( $siteAdmins as $id )
		{
			$list[] = $id;
		}

		// lets preload all the moderator.
		$list       = array_unique($list);
		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->init( $list );

		// Get the current logged in user
		$my 		= JFactory::getUser();

		if( $list )
		{
			foreach( $list as $userId )
			{
				$profile = DiscussHelper::getTable( 'Profile' );
				$profile->load( $userId );
				$moderators[$profile->id] = $my->id == $profile->id ? JText::_('COM_EASYDISCUSS_MYSELF') : $profile->getName();
			}
		}

		return $moderators;
	}


	public static function getModeratorsDropdownOld( $categoryId )
	{
		$category = DiscussHelper::getTable( 'Category' );

		if( !$category->load( $categoryId ) )
		{
			return '';
		}

		$moderators		= array();
		$moderators[0]	= JText::_('COM_EASYDISCUSS_MODERATOR_OPTION_NONE');

		$list 			= $category->getModerators();

		if( $list )
		{
			foreach( $list as $userId )
			{
				$profile = DiscussHelper::getTable( 'Profile' );
				$profile->load( $userId );
				$moderators[$profile->id] = $profile->getName();
			}
		}

		// Find super admins.
		$siteAdmins 	= DiscussHelper::getSAUsersIds();

		$ids 			= array_keys( $moderators );

		foreach( $siteAdmins as $id )
		{
			if( !in_array( $id , $ids ) )
			{
				$profile = DiscussHelper::getTable( 'Profile' );
				$profile->load( $id );
				$moderators[ $id ]	= $profile->getName();
			}
		}

		return $moderators;
	}

	public static function getModeratorsEmails( $categoryId )
	{
		$category = DiscussHelper::getTable( 'Category' );
		$category->load( $categoryId );

		if( !$category->id )
		{
			return array();
		}

		$emails = array();

		$moderators = $category->getModerators();
		if( $moderators ) {
			foreach ($moderators as $userid) {
				$emails[] = JFactory::getUser( $userid )->email;
			}
		}

		return $emails;
	}
}
