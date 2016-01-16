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

require_once DISCUSS_CLASSES . '/themes.php';

class DNotification
{
	function getAdminEmails()
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT `name`, `email`';
		$query	.= ' FROM #__users';

		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			$saUsersIds	= DiscussHelper::getSAUsersIds();
			$query	.= ' WHERE id IN (' . implode(',', $saUsersIds) . ')';
		}
		else
		{
			$query	.= ' WHERE LOWER( `usertype` ) = ' . $db->Quote('super administrator');
		}

		$query	.= ' AND `sendEmail` = ' . $db->Quote('1');
		$db->setQuery( $query );

		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}

		$result = $db->loadObjectList();
		return $result;

	}

	function getAdmins()
	{
		$db	= DiscussHelper::getDBO();

		$query	= 'SELECT `id`';
		$query	.= ' FROM #__users';

		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			$saUsersIds	= DiscussHelper::getSAUsersIds();
			$query	.= ' WHERE id IN (' . implode(',', $saUsersIds) . ')';
		}
		else
		{
			$query	.= ' WHERE LOWER( `usertype` ) = ' . $db->Quote('super administrator');
		}
		$query	.= ' AND `sendEmail` = ' . $db->Quote('1');

		$db->setQuery( $query );

		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}

		$result = $db->loadObjectList();
		return $result;
	}

	function addQueue($toEmails, $subject = '', $body = '', $template='', $data = array())
	{
		$mainframe	= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();

		$mailfrom	= $config->get( 'notification_sender_email' , DiscussHelper::getJConfig()->getValue( 'mailfrom' ) );
		$fromname	= $config->get( 'notification_sender_name' , DiscussHelper::getJConfig()->getValue( 'fromname' ) );

		$emailTo	= array();

		if(is_array($toEmails))
		{
			foreach($toEmails as $email)
			{
				$emailTo[]  = $email;
			}
		}
		else
		{
			$emailTo[]	= $toEmails;
		}

		//load the email template
		$tplBody	= '';
		if(! empty($template))
		{
			$tplBody	= $this->getEmailTemplateContent( $template, $data );
		}
		else
		{
			$tplBody	= $body;
		}

		//send as html or plaintext
		$config	= DiscussHelper::getConfig();
		$asHtml	= (bool) $config->get( 'notify_html_format' );

		if ( !$asHtml )
		{
			$tplBody = strip_tags($tplBody);
		}

		//now we process the email sending.
		foreach($emailTo as $recipient)
		{
			// Porcess the message and title
			$search 	= array('{actor}', '{target}');
			$replace 	= array($fromname, '');

			$emailSubject	= JString::str_ireplace($search, $replace, $subject);
			$emailBody		= JString::str_ireplace($search, $replace, $tplBody);

			$date	= DiscussHelper::getDate();
			$mailq	= DiscussHelper::getTable( 'MailQueue' );

			$mailq->mailfrom	= $mailfrom;
			$mailq->fromname	= $fromname;
			$mailq->recipient	= $recipient;
			$mailq->subject		= $emailSubject;
			$mailq->body		= $emailBody;
			$mailq->created		= $date->toMySQL();
			$mailq->ashtml		= $asHtml;
			$mailq->store();
		}

	}

	function add($from='', $to, $subject = '', $body = '', $template='', $data = array())
	{
		$mainframe	= JFactory::getApplication();
		$mailfrom	= $mainframe->getCfg( 'mailfrom' );
		$fromname	= $mainframe->getCfg( 'fromname' );


		if(! empty($from))
		{
			$userFrom	= JFactory::getUser($from);
			if($userFrom->id != 0)
			{
				$mailfrom	= $userFrom->name;
				$fromname	= $userFrom->email;
			}
		}


		$userTo	= array();

		if(! is_array($to))
		{
			if(strtolower($to) == 'admin')
			{
				$userTo	= $this->getAdminEmails();
			}
			else
			{
				$user		= JFactory::getUser($to);
				$userTo[]	= $user;
			}
		}
		else
		{
			foreach($to as $ids)
			{
				if(! empty($ids))
				{
					$user		= JFactory::getUser($ids);
					$userTo[]	= $user;
				}
			}
		}

		//load the email template
		$tplBody	= '';
		if(! empty($template))
		{
			$tplBody	= $this->getEmailTemplateContent( $template, $data );
		}
		else
		{
			$tplBody	= $body;
		}

		//now we process the email sending.
		foreach($userTo as $recipient)
		{
			$recipientUser	= DiscussHelper::getTable( 'Profile' );
			$recipientUser->setUser($recipient);

			// Process the message and title
			$search		= array('{actor}', '{target}');
			$replace	= array($fromname, $recipient->getName());

			$emailSubject	= JString::str_ireplace($search, $replace, $subject);
			$emailBody		= JString::str_ireplace($search, $replace, $tplBody);

			$date	= DiscussHelper::getDate();
			$mailq	= DiscussHelper::getTable( 'MailQueue' );

			$mailq->mailfrom	= $mailfrom;
			$mailq->fromname	= $fromname;
			$mailq->recipient	= $recipient->email;
			$mailq->subject		= $emailSubject;
			$mailq->body		= $emailBody;
			$mailq->created		= $date->toMySQL();
			$mailq->store();
		}
	}

	function getEmailTemplateContent( $template, $data )
	{
		$config 	= DiscussHelper::getConfig();
		$output 	= '';

		if(!isset($data['unsubscribeLink']))
		{
			$data['unsubscribeLink'] = '';
		}

		$replyBreakText = '';
		if( $replyBreakText = $config->get('mail_reply_breaker') )
		{
			$replyBreakText = JText::sprintf('COM_EASYDISCUSS_EMAILTEMPLATE_REPLY_BREAK', $replyBreakText);
		}

		// If this uses html, we need to switch the template file
		if( $config->get( 'notify_html_format' ) )
		{
			$template 	= str_ireplace( '.php' , '.html.php' , $template );
		}

		$theme	= new DiscussThemes();

		foreach( $data as $key => $val )
		{
			$theme->set( $key , $val );
		}

		$contents 	= $theme->fetch( $template , array( 'emails' => true ) );
		unset($theme);

		$theme 		= new DiscussThemes();
		$jConfig	= DiscussHelper::getJConfig();

		$theme->set( 'emailTitle' , $config->get( 'notify_email_title' , $jConfig->getValue( 'sitename' ) ) );
		$theme->set( 'contents' 	, $contents );
		$theme->set( 'unsubscribeLink'	, $data[ 'unsubscribeLink' ] );
		$theme->set( 'replyBreakText', $replyBreakText );

		if( $config->get( 'notify_html_format' ) )
		{
			$output 	= $theme->fetch( 'email.template.html.php', array('emails'=> true ) );
		}
		else
		{
			$output 	= $theme->fetch( 'email.template.text.php', array('emails'=> true ) );
		}

		return $output;
	}
}
