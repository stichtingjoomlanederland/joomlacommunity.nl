<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'controller.php' );

class KomentoControllerSubscribers extends KomentoController
{
	function __construct()
	{
		parent::__construct();
	}

	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_komento&view=subscribers' );

		return;
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$subscribers	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		$message		= '';
		$type			= 'message';

		if( empty( $subscribers ) )
		{
			$message	= JText::_( 'COM_KOMENTO_SUBSCRIBERS_SUBSCRIBER_INVALID_ID' );
			$type		= 'error';
		}
		else
		{
			$model		= Komento::getModel( 'subscription' );

			if( $model->remove( $subscribers ) )
			{
				$message	= JText::_('COM_KOMENTO_SUBSCRIBERS_SUBSCRIBER_REMOVED');
			}
			else
			{
				$message	= JText::_('COM_KOMENTO_SUBSCRIBERS_SUBSCRIBER_REMOVE_ERROR');
				$type		= 'error';
			}
		}

		$this->setRedirect( 'index.php?option=com_komento&view=subscribers' , $message , $type );
	}

	function clean()
	{
		// access with c=subscribers&task=clean
		// clean up comments
		// eg remove all non existant article id
	}
}
