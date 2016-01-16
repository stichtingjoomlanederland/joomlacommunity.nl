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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

require_once( DISCUSS_ROOT . '/views.php' );

class EasyDiscussViewSubscriptions extends JView
{
	public function unsubscribeMe()
	{
		$ajax	= DiscussHelper::getHelper( 'Ajax' );

		$my 	= JFactory::getUser();

		if( $my->id > 0 )
		{
			$db		= DiscussHelper::getDBO();
			$query	= 'DELETE FROM `#__discuss_subscription` WHERE `userid` = ' . $db->quote( (int) $my->id );
			$db->setQuery( $query );
			$db->query();

			return $ajax->success( JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_SUCCESS') );
		}

		return $ajax->fail( 'Subscription fail' );
	}
}
