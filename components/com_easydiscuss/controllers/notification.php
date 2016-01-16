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

class EasyDiscussControllerNotification extends EasyDiscussController
{
	/**
	 * Allows user to mark notification items as read
	 **/
	public function markread()
	{
		$id		= JRequest::getInt( 'id' );
		$my		= JFactory::getUser();

		$notification	= DiscussHelper::getTable( 'Notifications' );
		$notification->load( $id );

		if( !$my->id )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_PLEASE_LOGIN_FIRST' ) , 'error' );
			JFactory::getApplication()->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss' , false ) );
		}

		if( $notification->target != $my->id )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED_TO_MARK_READ' ) , 'error' );
			JFactory::getApplication()->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss' , false ) );
		}

		$notification->state	= 0;
		$notification->store();

		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_NOTIFICATION_MARKED_AS_READ' ) );
		JFactory::getApplication()->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=notifications' , false ) );
	}

	/**
	 * Allows user to mark all their notification items as read
	 **/
	public function markreadall()
	{
		$my		= JFactory::getUser();

		if( !$my->id )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_PLEASE_LOGIN_FIRST' ) , 'error' );
			JFactory::getApplication()->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss' , false ) );
		}

		$db		= DiscussHelper::getDBO();
		$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_notifications' ) . ' '
				. 'SET ' . $db->nameQuote( 'state' ) . '=' . $db->Quote( 0 ) . ' '
				. 'WHERE ' . $db->nameQuote( 'target' ) . '=' . $db->Quote( $my->id );
		$db->setQuery( $query );
		$db->Query();

		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_ALL_NOTIFICATIONS_MARKED_AS_READ' ) );
		JFactory::getApplication()->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=notifications' , false ) );
	}
}
