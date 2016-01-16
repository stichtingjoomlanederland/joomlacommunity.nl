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

require_once( DISCUSS_ROOT . '/views.php' );

class EasyDiscussViewFavourites extends EasyDiscussView
{
	public function favourite()
	{
		$my			= JFactory::getUser();
		$ajax		= DiscussHelper::getHelper( 'ajax' );
		$config		= DiscussHelper::getConfig();

		// Get the post.
		$postId	 	= JRequest::getInt( 'postid' );
		$post		= DiscussHelper::getTable( 'Post' );
		$post->load( $postId );

		// Is this a reply?
		if( $post->isReply() )
		{
			$ajax->reject();
			return $ajax->send();
		}

		// Is the backend option enabled?
		if( !$config->get( 'main_favorite' ) )
		{
			// show error msg
			$ajax->reject();
			return $ajax->send();
		}

		// Is it a valid user?
		if( !$my->id )
		{
			$ajax->reject();
			return $ajax->send();
		}

		// Get favourite table
		$favModel	= DiscussHelper::getModel( 'Favourites' );

		// Check to see is it favourited previously
		// If the status is false means there is no record in the database, not yet favourited.
		$status		= $favModel->isFav( $post->id, $my->id );

		// Determine what action to take
		$type		= $status ? 'removeFav' : 'addFav';

		if( $type == 'addFav' )
		{
			DiscussHelper::getHelper( 'easysocial' )->favouriteStream( $post );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.favourite.discussion' , $my->id );
		}
		else
		{
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.unfavourite.discussion' , $my->id );
		}

		// Is the process run successfully?
		$result		= $favModel->$type( $post->id, $my->id );

		if( !$result )
		{
			$ajax->reject();
			return $ajax->send();
		}

		// Tell the js controller what action you took
		$action		= $type == 'addFav';


		//Count total favourites
		$favCount	= $post->getMyFavCount();

		// True = just added favourite
		// False = just removed favourite
		$ajax->resolve( $action, $favCount );
		return $ajax->send();
	}

	public function remove()
	{
		$my			= JFactory::getUser();
		$ajax		= DiscussHelper::getHelper( 'ajax' );
		$config		= DiscussHelper::getConfig();

		// Get the post.
		$postId	 	= JRequest::getInt( 'postid' );
		$post		= DiscussHelper::getTable( 'Post' );
		$post->load( $postId );

		// Is this a reply?
		if( $post->isReply() )
		{
			$ajax->reject();
			return $ajax->send();
		}

		// Is the backend option enabled?
		if( !$config->get( 'main_favorite' ) )
		{
			// show error msg
			$ajax->reject();
			return $ajax->send();
		}

		// Is it a valid user?
		if( !$my->id )
		{
			$ajax->reject();
			return $ajax->send();
		}

		// Get favourite table
		$favModel	= DiscussHelper::getModel( 'Favourites' );
		$result		= $favModel->removeFav( $post->id, $my->id );

		if( !$result )
		{
			$ajax->reject();
			return $ajax->send();
		}

		// Update JomSocial Status
		// code..

		$ajax->resolve( $postId );
		return $ajax->send();
	}
}
