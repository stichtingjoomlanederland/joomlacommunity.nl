<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussMailbox extends EasyDiscuss
{
	// IMAP stream resource
	public $stream			= null;
	// Mailbox status
	public $info			= null;

	private $initiated		= false;
	private $mailbox_params	= '';
	private $flags			= '';
	// private $config			= array();
	private $username		= '';
	private $password		= '';

	public $server			= '';
	public $port			= 0;
	public $mailbox_name	= '';
	public $service			= '';
	public $anonymous		= false;
	public $secure			= false;
	public $debug			= false;
	public $norch			= false;
	public $validate_cert	= false;
	public $tls				= false;
	public $readonly		= false;

	public $enabled			= false;

	/**
	 * Initiate class variables
	 */
	public function __construct()
	{
        parent::__construct();


		$this->enabled			= $this->config->get( 'main_email_parser' );
		$this->server			= $this->config->get( 'main_email_parser_server' );
		$this->port				= $this->config->get( 'main_email_parser_port' );
		$this->mailbox_name		= $this->config->get( 'main_email_parser_mailbox' );
		$this->service			= $this->config->get( 'main_email_parser_service' );
		$this->username			= $this->config->get( 'main_email_parser_username' );
		$this->password			= $this->config->get( 'main_email_parser_password' );
		$this->ssl				= $this->config->get( 'main_email_parser_ssl' );
		$this->validate_cert	= $this->config->get( 'main_email_parser_validate' );

		$this->flags			= '';
		$this->flags			.= $this->service 	? '/'.$this->service : '';
		$this->flags			.= $this->ssl 		? '/ssl' : '';
		$this->flags			.= $this->debug 	? '/debug' : '';
		$this->flags			.= $this->norch 	? '/norch' : '';
		$this->flags			.= $this->validate_cert ? '' : '/novalidate-cert';
		//$this->flags			.= $this->tls 		? '/tls' : '/notls';
		$this->flags			.= $this->readonly ? '/readonly' : '';

		$this->mailbox_params	= '{'.$this->server.':'.$this->port.$this->flags.'}'.$this->mailbox_name;

		$this->initiated		= true;
	}

	/**
	 * Open an IMAP stream to a mailbox.
	 * Return true on success, return false on error.
	 */
	public function connect( $username = '', $password = '' )
	{
		if (!$this->initiated)
		{
			$this->init();
		}

		if (!$this->enabled || !function_exists('imap_open') || !function_exists('imap_fetchheader') || !function_exists('imap_body'))
		{
			$this->setError('PHP IMAP not available.');
			return false;
		}




		/*
		 * Connect to mailbox
		 */
		// if( !empty($username) && !empty($password) )
		// {
			// echo JText::sprintf( 'COM_EASYDISCUSS_CONNECTING_TO', $username );
			$this->stream	= imap_open( $this->mailbox_params, $username, $password );
		// }
		// else
		// {
		// 	$this->stream	= imap_open( $this->mailbox_params, $this->username, $this->password );
		// }

		if( $this->stream === false )
		{
			$this->setError( imap_errors() );
			return false;
		}


		return true;
	}

	public function testConnect($server, $port, $service, $ssl, $mailbox, $user, $pass )
	{
		$flags	= '';
		$flags	= $service ? $flags.'/'.$service : $flags;
		$flags	= $ssl ? $flags.'/ssl' : $flags;
		$flags	= true ? $flags.'/novalidate-cert' : $flags;

		if (!function_exists('imap_open') || !function_exists('imap_fetchheader') || !function_exists('imap_body')) {
			$result	= '<span style="color:red;">Failed, imap is not compiled with PHP</span>';
			return $result;
		}

		// note: pop3 doesn't support OP_HALFOPEN
		$stream	= @imap_open('{'.$server.':'.$port.$flags.'}', $user, $pass);
		$result	= imap_errors();

		if ($stream === false) {
			if (is_array($result)) {
				$result	= $result[0];
			}

			if ($result === false) {
				$result = 'Failed';
			}
		} else {
			$result	= '<span style="color:green">Success</span>';
			imap_close($stream);
		}

		return $result;
	}

	public function getStream()
	{
		return $this->stream;
	}

	public function getError( $i = null, $toString = true )
	{
		return parent::getError() ? parent::getError() : imap_last_error();
	}

	public function getErrors()
	{
		$errors	= array_merge((array)$this->_errors, (array)imap_errors());
		$errors = !empty($errors) ? $errors : '';

		return $errors;
	}

	public function disconnect()
	{
		if (!$this->stream)
		{
			return false;
		}

		imap_expunge($this->stream);

		$errors	= $this->getErrors();
		if (!empty($errors))
		{
		}

		imap_close($this->stream);
	}

	public function searchMessages($criteria)
	{
		if( !$this->stream )
		{
			return false;
		}

		return imap_search($this->stream, $criteria);
	}

	public function getMessageInfo($sequence)
	{
		$headers	= @imap_headerinfo($this->stream, $sequence);

		if (empty($headers))
		{
			return false;
		}

		// decode headers
		foreach($headers as $key => $value)
		{
			if (!is_array($value))
			{
				$header	= imap_mime_header_decode($value);
				$header	= $header[0];
				$header->charset	= strtoupper($header->charset);

				if ($header->charset != 'DEFAULT' && $header->charset != 'UTF-8')
				{
					$header->text		= iconv($header->charset, 'UTF-8', $header->text);
					$header->subject	= iconv($header->charset, 'UTF-8', $header->subject);
				}
				$headers->$key	= $header->text;
			}
		}

		$from		= $headers->fromaddress;

		if (!$from)
		{
			$from	= $header->senderaddress;
		}
		if (!$from)
		{
			$from	= $header->reply_toaddress;
		}

		$pattern	= '([\w\.\-]+\@(?:[a-z0-9\.\-]+\.)+(?:[a-z0-9\-]{2,4}))';
		preg_match( $pattern, $from, $matches );
		$from		= isset($matches[0]) ? $matches[0] : '';

		if (!$from)
		{
			$from	= $headers->from[0]->mailbox . '@' . $headers->from[0]->host;
		}
		if (!$from)
		{
			$from	= $headers->sender[0]->mailbox . '@' . $headers->sender[0]->host;
		}
		if (!$from)
		{
			$from	= $headers->reply_to[0]->mailbox . '@' . $headers->reply_to[0]->host;
		}

		$headers->fromemail	= $from;

		return $headers;
	}


	/**
	 * Get the number of messages in the current mailbox
	 */
	public function getMessageCount()
	{
		return imap_num_msg($this->stream);
	}

	/**
	 * Get information about the current mailbox
	 */
	public function getInfo()
	{
		// note: imap_mailboxmsginfo not quite support pop3
		if ($this->service == 'imap')
		{
			$this->info	= imap_mailboxmsginfo($this->stream);
		} else {
			$this->info	= imap_status($this->stream, $this->mailbox_params, $option);
		}

		return $this->info;
	}

	public function getCount($label)
	{
		if (!$this->info)
		{
			$this->getInfo();
		}

		$label	= strtolower($label);
		$count	= 0;

		switch ($label) {
			case 'unread':
				$count	= ($this->service == 'imap') ? $this->info->Unread : $this->info->unseen;
				break;
			case 'recent':
				$count	= ($this->service == 'imap') ? $this->info->Recent : $this->info->recent;
				break;
			case 'deleted':
				$count	= ($this->service == 'imap') ? $this->info->Deleted : false;
				break;
			case 'size':
				$count	= ($this->service == 'imap') ? $this->info->Size : false;
				break;
			default:
				break;
		}

		return $count;
	}

	// Causes a store to add the specified flag to the flags set for the messages in the specified sequence.
	public function setMessageFlag($sequence, $flag)
	{
		return imap_setflag_full($this->stream, $sequence, $flag);
	}

	/**
	 * Mark a mail item as read
	 *
	 **/
	public function markAsRead($mailbox, $sequence)
	{
		if($mailbox->service == 'pop3')
		{
			$mailbox->deletePop3Message($sequence);
		}

		if($mailbox->service == 'imap')
		{
			$mailbox->setMessageFlag($sequence, '\Seen');
		}
	}

	// Clears flags on messages
	public function clearMessageFlag( $sequence, $flag, $options=0 )
	{
		return imap_clearflag_full($this->stream, $sequence, $flag, $options);
	}

	// Create a new mailbox
	public function createMailbox( $mailbox )
	{
		return imap_createmailbox($this->stream, $mailbox);
	}

	// Mark a message for deletion from current mailbox
	public function deleteMailbox( $mailbox )
	{
		return imap_deletemailbox($this->stream, $mailbox);
	}

	// Mark a message for deletion from current mailbox
	public function deleteMessage( $sequence, $options = 0 )
	{
		return imap_delete($this->stream, $sequence);
	}

	// Mark a message for deletion from current mailbox
	public function deletePop3Message( $sequence, $options = 0 )
	{
		// Only for POP3
		// Mark email to be deleted.
		$state = imap_delete($this->stream, $sequence);

		if ($state) {
			// Delete the emails that are marked
			imap_expunge($this->stream);
		}

		return $state;
	}

	// Move specified messages to a mailbox
	public function moveMessage( $msglist, $mailbox )
	{
		return imap_mail_move($this->stream, $msglist, $mailbox);
	}

	// Send an email message
	public function sendMessage($to, $subject, $message)
	{
		return imap_mail($to, $subject, $message);
	}

	// Subscribe to a mailbox
	public function subscribe($mailbox)
	{
		return imap_subscribe($this->stream, $mailbox);
	}

	// Unsubscribe from a mailbox
	public function unsubscribe($mailbox)
	{
		return imap_unsubscribe($this->stream, $mailbox);
	}
}
