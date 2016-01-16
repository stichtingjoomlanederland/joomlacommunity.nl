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

class EasyDiscussViewNotifications extends EasyDiscussView
{
	public function display( $tpl = null )
	{
		$my		= JFactory::getUser();

		if( !$my->id )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_PLEASE_LOGIN_FIRST' ) , 'error' );
			JFactory::getApplication()->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss' , false ) );
		}

		$model	= $this->getModel( 'Notification' );
		$this->setPathway( JText::_( 'COM_EASYDISCUSS_BREADCRUMBS_NOTIFICATIONS') );
		
		// Make this configurable?
		$limit	= 100;

		$notifications	= $model->getNotifications( $my->id , false , $limit );
		
		DiscussHelper::getHelper( 'Notifications' )->format( $notifications , true );

		$theme	= new DiscussThemes();
		$theme->set( 'notifications' , $notifications );

		echo $theme->fetch( 'notifications.php' );
	}
}
