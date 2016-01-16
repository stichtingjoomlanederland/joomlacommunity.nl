<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class KomentoMailQueue
{
	public function sendOnPageLoad( $max = 5 )
	{
		static $sent = null;

		if (!$sent)
		{
			$this->send($max);
			$sent = true;
		}
	}

	public function send( $max = 5 )
	{
		$konfig		= Komento::getKonfig();

		if( $konfig->get( 'disable_mailq' ) )
		{
			return false;
		}

		$db			= Komento::getDBO();
		$config		= Komento::getConfig();

		$sql = Komento::getSql();

		$sql->select( '#__komento_mailq' )
			->column( 'id' )
			->where( 'status', 0 )
			->order( 'created' )
			->limit( $max );

		$result = $sql->loadObjectList();

		if(! empty($result))
		{
			foreach($result as $mail)
			{
				$mailq	= Komento::getTable('mailq');
				$mailq->load($mail->id);

				$sendHTML = $mailq->type == 'html' ? 1 : 0;

				$state = 0;

				if( empty( $mailq->recipient ) )
				{
					$state = 1;
				}

				//send emails.
				if( Komento::isJoomla15() )
				{
					$state = JUtility::sendMail($mailq->mailfrom, $mailq->fromname, $mailq->recipient, $mailq->subject, $mailq->body, $sendHTML);
				}
				else
				{
					$mail = JFactory::getMailer();
					$state = $mail->sendMail($mailq->mailfrom, $mailq->fromname, $mailq->recipient, $mailq->subject, $mailq->body, $sendHTML);
				}

				if( $state )
				{
					// update the status to 1 == proccessed
					$mailq->status  = 1;
					$mailq->store();
				}
			}
		}
	}

	public function addMailq($component, $cid)
	{
		$config = Komento::getConfig();

		$subscribers = Komento::getModel('subscription')->getSubscribers($component, $cid);

		$mainframe	= JFactory::getApplication();
		$mailfrom	= $mainframe->getCfg( 'mailfrom' );
		$fromname 	= $mainframe->getCfg( 'fromname' );

		if( $config->get( 'admin_email' ) )
		{
			// get from config if config exist
		}

		if( count( $subscribers ) > 0 )
		{
			foreach( $subscribers as $subscriber )
			{
				$data = array(
					'mailfrom'	=> $mailfrom,
					'fromname'	=> $fromname,
					'recipient'	=> $subscriber->email,
					'subject'	=> 'new email',
					'body'		=> 'body',
					'created'	=> Komento::getDate()->toMySQL(),
					'status'	=> 0
				);

				$mailqTable = Komento::getTable( 'mailq' );

				$mailqTable->bind( $data );
				$mailqTable->store();
			}

			return true;
		}

		return false;
	}
}
