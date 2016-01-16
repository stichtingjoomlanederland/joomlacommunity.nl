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

class EasyDiscussViewLocation extends EasyDiscussView
{
	/**
	 * Displays a confirmation dialog to remove a location
	 * from a post.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	null
	 */
	public function confirmRemoveLocation( $id )
	{
		$ajax 	= new Disjax();

		$theme		= new DiscussThemes();

		$content	= $theme->fetch( 'ajax.location.delete.php' , array('dialog'=> true ) );

		$options	= new stdClass();
		$options->content	= $content;

		$options->title		= JText::_( 'COM_EASYDISCUSS_DELETE_LOCATION_TITLE' );

		$buttons 			= array();

		$button 			= new stdClass();
		$button->title 		= JText::_( 'COM_EASYDISCUSS_BUTTON_NO' );
		$button->action 	= 'disjax.closedlg();';
		$buttons[]			= $button;

		$button 			= new stdClass();
		$button->title 		= JText::_( 'COM_EASYDISCUSS_BUTTON_YES' );
		$button->action 	= 'discuss.location.remove("' . $id . '");';
		$button->className 	= 'btn-primary';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$ajax->dialog( $options );

		return $ajax->send();
	}

	/**
	 * Remove a location from a post.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function removeLocation( $id )
	{
		$ajax 	= new Disjax();
		$post 	= DiscussHelper::getTable( 'Post' );
		$state	= $post->load( $id );
		$my 	= JFactory::getUser();

		if( !$id || !$state )
		{
			echo JText::_( 'COM_EASYDISCUSS_INVALID_ID' );
			return $ajax->send();
		}

		if( $post->user_id != $my->id && !DiscussHelper::isModerator( $post->category_id ) )
		{
			echo JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED_TO_REMOVE_LOCATION_FOR_POST' );
			return $ajax->send();
		}

		// Update the address, latitude and longitude of the post.
		$post->address		= '';
		$post->latitude 	= '';
		$post->longitude 	= '';
		$post->store();

		$content	= JText::_( 'COM_EASYDISCUSS_LOCATION_IS_REMOVED' );


		$options	= new stdClass();
		$options->content	= $content;

		$options->title		= JText::_( 'COM_EASYDISCUSS_DELETE_LOCATION_TITLE' );

		$buttons 			= array();

		$button 			= new stdClass();
		$button->title 		= JText::_( 'COM_EASYDISCUSS_BUTTON_CLOSE' );
		$button->action 	= 'disjax.closedlg();';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$ajax->script( 'discuss.location.removeHTML("' . $id . '");' );
		$ajax->dialog( $options );

		return $ajax->send();
	}


}
