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
	public function load()
	{
		$my		= JFactory::getUser();
		$ajax	= DiscussHelper::getHelper( 'Ajax' );

		$config	= DiscussHelper::getConfig();

		if( $my->id <= 0 || !$config->get( 'main_notifications') )
		{
			$ajax->fail( JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' ) );
			return;
		}

		$model = $this->getModel( 'Notification' );
		$notifications = $model->getNotifications( $my->id , true , $config->get( 'main_notifications_limit') );

		DiscussHelper::getHelper( 'Notifications' )->format( $notifications );

		$theme = new DiscussThemes();
		$theme->set( 'notifications' , $notifications );
		$html = $theme->fetch( 'toolbar.notification.item.php' );

		$ajax->success( $html );
	}

	public function count()
	{
		$my		= JFactory::getUser();
		$ajax	= DiscussHelper::getHelper( 'Ajax' );

		if( $my->id <= 0 )
		{
			$ajax->fail('User is not logged in.');
			return;
		}

		$model	= $this->getModel( 'Notification' );
		$count	= $model->getTotalNotifications( $my->id );

		$ajax->success( $count );
	}
}
