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

class EasyDiscussModelFavourites extends EasyDiscussModel
{
	function __construct()
	{
		parent::__construct();


		$mainframe	= JFactory::getApplication();

		$limit			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.categories.limit', 'limit', DiscussHelper::getListLimit(), 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function isFav( $postId, $userId, $type = 'post' )
	{
		$db = DiscussHelper::getDBO();
		$query = 'SELECT ' . $db->nameQuote( 'id' )
				. ' FROM ' . $db->nameQuote( '#__discuss_favourites' )
				. ' WHERE ' . $db->nameQuote( 'created_by' ) . ' = ' . $db->quote( $userId )
				. ' AND ' . $db->nameQuote( 'post_id' ) . ' = ' . $db->quote( $postId )
				. ' AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( $type );

		$db->setQuery( $query );
		$result = $db->loadResultArray();

		return ( empty( $result ) ? false : true );
	}

	public function addFav( $postId, $userId, $type = 'post' )
	{
		$date	= DiscussHelper::getDate();
		$fav	= DiscussHelper::getTable( 'Favourites' );

		$fav->created_by = $userId;
		$fav->post_id = $postId;
		$fav->type = $type;
		$fav->created = $date->toMySQL();

		if( !$fav->store() )
		{
			return false;
		}

		return true;
	}

	public function removeFav( $postId, $userId, $type = 'post' )
	{
		// Remove favourite for single user at specific post
		$db = DiscussHelper::getDBO();
		$query = 'DELETE FROM ' . $db->nameQuote( '#__discuss_favourites' )
				. ' WHERE ' . $db->nameQuote( 'created_by' ) . '=' . $db->quote( $userId )
				. ' AND ' . $db->nameQuote( 'post_id' ) . '=' . $db->quote( $postId )
				. ' AND ' . $db->nameQuote( 'type' ) . '=' . $db->quote( $type );

		$db->setQuery( $query );

		if( !$db->query() )
		{
			return false;
		}

		return true;
	}

	/**
	 * Retrieve favourite count.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getFavouritesCount( $id , $type = 'post' )
	{
		$db 		= DiscussHelper::getDBO();

		$query 		= array();
		$query[] 	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_favourites' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $id );
		$query[]	= 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type );
		$query		= implode( ' ' , $query );

		$db->setQuery( $query );
		$total 		= $db->loadResult();

		return $total;
	}

	public function deleteAllFavourites( $id )
	{
		if( !$id )
		{
			return false;
		}

		$db = DiscussHelper::getDBO();

		$query = 'DELETE FROM ' . $db->nameQuote( '#__discuss_favourites' )
				. ' WHERE ' . $db->nameQuote('post_id') . '=' . $db->Quote( $id );

		$db->setQuery($query);

		if( !$db->query() )
		{
			return false;
		}

		return true;
	}

}
