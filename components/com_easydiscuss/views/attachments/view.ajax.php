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

class EasyDiscussViewAttachments extends EasyDiscussView
{
	/**
	 * Displays a confirmation dialog to delete an attachment.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int 	The id of the attachment.
	 */
	public function confirmDelete( $id )
	{
		$ajax	= new Disjax();
		$user	= JFactory::getUser();

		// @rule: Do not allow empty id or guests to delete files.
		if( empty( $id ) || empty( $user->id ) )
		{
			return false;
		}

		$attachment	= DiscussHelper::getTable( 'Attachments' );
		$attachment->load( $id );

		// Ensure that only post owner or admin can delete it.
		if( !$attachment->deleteable() )
		{
			return false;
		}

		$theme				= new DiscussThemes();
		$theme->set( 'id' , $id );
		$content			= $theme->fetch( 'ajax.attachment.delete.php' , array('dialog'=> true ) );

		$options			= new stdClass();
		$options->content	= $content;
		$options->title		= JText::_( 'COM_EASYDISCUSS_ATTACHMENT_DELETE_CONFIRMATION_TITLE' );

		$buttons			= array();

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_CANCEL' );
		$button->action		= 'disjax.closedlg();';
		$buttons[]			= $button;

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_DELETE' );
		$button->action		= "disjax.loadingDialog();disjax.load('attachments','delete','" . $id . "');";
		$button->className	= 'btn-danger';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$ajax->dialog( $options );
		$ajax->send();
	}

	public function delete( $id )
	{
		$ajax	= new Disjax();
		$user	= JFactory::getUser();

		// @rule: Do not allow empty id or guests to delete files.
		if( empty( $id ) || empty( $user->id ) )
		{
			return false;
		}

		$attachment	= DiscussHelper::getTable( 'Attachments' );
		$attachment->load( $id );

		// Ensure that only post owner or admin can delete it.
		if( !$attachment->deleteable() )
		{
			return false;
		}

		// Ensure that only post owner or admin can delete it.
		if( !$attachment->delete() )
		{
			return false;
		}

		$theme				= new DiscussThemes();
		$content 			= JText::_( 'COM_EASYDISCUSS_ATTACHMENT_DELETED_SUCCESSFULLY' );

		$options			= new stdClass();

		$options->content	= $content;
		$options->title		= JText::_( 'COM_EASYDISCUSS_ATTACHMENT_DELETE_CONFIRMATION_TITLE' );

		$buttons			= array();

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_CLOSE' );
		$button->action		= 'disjax.closedlg();';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$ajax->script( 'EasyDiscuss.$("#attachment-' . $attachment->id . '" ).trigger("itemRemoved").remove();' );

		$ajax->dialog( $options );

		$ajax->send();
	}
}
