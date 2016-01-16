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

jimport( 'joomla.application.component.view');

class EasyDiscussViewSpools extends JView
{
	public function preview( $blogId )
	{
		$ajax	= new Ejax();
		$mailq	= DiscussHelper::getTable( 'Mailqueue' );
		$mailq->load( $blogId );

		$options	= new stdClass();
		$options->title		= JText::_( 'COM_EASYDISCUSS_EMAIL_PREVIEW' );
		$options->content	= $mailq->body;

		$buttons 			= array();
		$button 			= new stdClass();
		$button->title 		= JText::_( 'COM_EASYDISCUSS_OK' );
		$button->action 	= 'disjax.closedlg();';
		$button->className 	= 'btn-primary';
		$buttons[]			= $button;
		$options->buttons	= $buttons;

		$ajax->dialog( $options );
		$ajax->send();
	}
}
