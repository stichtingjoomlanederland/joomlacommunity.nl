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

class EasyDiscussControllerSpools extends EasyDiscussController
{
	function __construct()
	{
		parent::__construct();
	}

	public function purge()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$db 	= DiscussHelper::getDBO();
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_mailq' );

		$db->setQuery( $query );
		$db->Query();

		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_MAILS_PURGED' ) , DISCUSS_QUEUE_SUCCESS );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=spools' );
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mails		= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'success';

		if( empty( $mails ) )
		{
			$message	= JText::_('COM_EASYDISCUSS_NO_MAIL_ID_PROVIDED');
			$type		= 'error';
		}
		else
		{
			$table		= DiscussHelper::getTable( 'MailQueue' );

			foreach( $mails as $id )
			{
				$table->load( $id );

				if( !$table->delete() )
				{
					DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_SPOOLS_DELETE_ERROR' ) , DISCUSS_QUEUE_ERROR );

					$this->setRedirect( 'index.php?option=com_easydiscuss&view=spools' );
					return;
				}
			}
			$message	= JText::_('COM_EASYDISCUSS_SPOOLS_EMAILS_DELETED');
		}

		DiscussHelper::setMessageQueue( $message, $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=spools' );
	}
}
