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

require_once DISCUSS_HELPERS . '/date.php';
require_once DISCUSS_HELPERS . '/input.php';
require_once DISCUSS_HELPERS . '/filter.php';

class EasyDiscussControllerSubscription extends EasyDiscussController
{
	function remove()
	{
		$subs		= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'success';

		if( count( $subs ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type		= 'error';
		}
		else
		{

			$table		= JTable::getInstance( 'Subscribe' , 'Discuss' );
			foreach( $subs as $sub )
			{
				$table->load( $sub );

				if( ! $table->delete() )
				{

					DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_REMOVING_SUBSCRIPTION_PLEASE_TRY_AGAIN_LATER' ) , DISCUSS_QUEUE_ERROR );
					$this->setRedirect( 'index.php?option=com_easydiscuss&view=subscription' );
					return;
				}
			}

			$message	= JText::_('COM_EASYDISCUSS_SUBSCRIPTION_DELETED');

		}

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=subscription' );
	}

}
