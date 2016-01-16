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

class KomentoNotificationHelper
{
	/**
	 * Push the email notification to MailQ
	 * @param	string	$type			type of notification
	 * @param	string	$recipient		recipient (subscribers,admins,author,me)
	 * @param	array	$options		various options
	 *
	 * @return nothing
	 */
	public function push( $type, $recipients, $options = array() )
	{
		if( !empty( $options['commentId'] ) )
		{
			$comment = Komento::getComment( $options['commentId'] );
			$options['comment'] = $comment;
			$options['component'] = $comment->component;
			$options['cid'] = $comment->cid;

			$options['comment'] = Komento::getHelper( 'comment' )->process( $options['comment'] );


			unset( $options['commentId'] );
		}

		if( !isset( $options['component'] ) || !isset( $options['cid'] ) )
		{
			return;
		}

		if( $type == 'new' && $options['comment']->parent_id )
		{
			$type = 'reply';
		}

		$recipients		= explode(',', $recipients);
		$rows			= array();
		$skipMe			= true;

		// process requested recipients first
		foreach ($recipients as $recipient)
		{
			$recipient		= 'get' . ucfirst( strtolower( trim($recipient) ) );

			if( !method_exists($this, $recipient) )
			{
				continue;
			}

			if( $recipient == 'getMe' )
			{
				$skipMe = false;
			}

			$result = $this->$recipient( $type, $options );

			// stacking up all the emails and details
			$rows	= $rows + $result;
		}

		// process usergroups notification based on notification type
		$rows = $rows + $this->getUsergroups( $type );

		if( $type == 'report' )
		{
			$admins = $this->getAdmins();

			foreach( $admins as $admin )
			{
				if( isset($rows[$options['comment']->email]) && $options['comment']->email === $admin->email )
				{
					$skipMe = false;
				}
			}
		}

		if( empty($rows) )
		{
			return;
		}

		// Do not send to the commentor/actor
		if( $skipMe && isset($rows[$options['comment']->email]) )
		{
			unset( $rows[$options['comment']->email] );
		}

		$lang = JFactory::getLanguage();

		// Load English first as fallback
		$konfig = Komento::getKonfig();
		if( $konfig->get( 'enable_language_fallback' ) )
		{
			$lang->load( 'com_komento', JPATH_ROOT, 'en-GB', true );
		}

		// Load site's selected language
		$lang->load( 'com_komento', JPATH_ROOT, $lang->getDefault(), true );

		// Load user's preferred language file
		$lang->load( 'com_komento', JPATH_ROOT, null, true );

		$jconfig	= JFactory::getConfig();
		$data		= $this->prepareData( $type, $options );
		$template	= $this->prepareTemplate( $type, $options );
		$subject	= $this->prepareTitle( $type, $options );

		$mailfrom	= Komento::_( 'JFactory::getConfig', 'mailfrom' );
		$fromname	= Komento::_( 'JFactory::getConfig', 'fromname' );

		$config	= Komento::getConfig();
		$sendHTML = $config->get( 'notification_sendmailinhtml' ) ? 'html' : 'text';

		// Storing notifications into mailq
		foreach ($rows as $row)
		{
			if( Komento::trigger( 'onBeforeSendNotification', array( 'component' => $options['component'], 'cid' => $options['cid'], 'recipient' => &$row ) ) === false )
			{
				continue;
			}

			if( empty( $row->email ) )
			{
				continue;
			}

			$body	= $this->getTemplateBuffer( $template, $data, array( 'recipient' => $row ) );
			$mailQ	= Komento::getTable( 'mailq' );
			$mailQ->mailfrom	= $mailfrom;
			$mailQ->fromname	= $fromname;
			$mailQ->recipient	= $row->email;
			$mailQ->subject		= $subject;
			$mailQ->body		= $body;
			$mailQ->created		= Komento::getDate()->toMySQL();
			$mailQ->type		= $sendHTML;
			$mailQ->status		= 0;
			$result = $mailQ->store();
		}
	}

	public function getTemplateBuffer( $template, $data, $params = array() )
	{
		$theme	= Komento::getTheme();

		foreach( $data as $key => $val )
		{
			$theme->set( $key , $val );
		}

		$document = JFactory::getDocument();

		$theme->set( 'data', $data );
		$theme->set( 'options', $params );
		$theme->set( 'document', $document );

		$contents	= $theme->fetch( $template );

		return $contents;
	}

	private function prepareData( $type = 'new', $options )
	{
		Komento::import( 'helper', 'date' );

		$data							= array();

		if( $type === 'confirm' )
		{
			$subscribeTable = Komento::getTable( 'subscription' );
			$subscribeTable->load( $options['subscribeId'] );

			$profile = Komento::getProfile( $subscribeTable->userid );
		}
		else
		{
			$profile	= Komento::getProfile( $options['comment']->created_by );

			$data['contentTitle']			= $options['comment']->contenttitle;
			$data['contentPermalink']		= $options['comment']->pagelink;
			$data['commentAuthorName']		= $options['comment']->name;
			$data['commentAuthorAvatar']	= $profile->getAvatar();
		}

		$config 					= Komento::getConfig();

		switch( $type )
		{
			case 'confirm':
				$data['confirmLink']	= rtrim( JURI::root() , '/' ) . '/index.php?option=com_komento&task=confirmSubscription&id=' . $options['subscribeId'];
				break;
			case 'pending':
			case 'moderate':
				$hashkeys = Komento::getTable( 'hashkeys' );
				$hashkeys->uid = $options['comment']->id;
				$hashkeys->type = 'comment';
				$hashkeys->store();
				$key = $hashkeys->key;

				$data['approveLink']	= rtrim( JURI::root() , '/' ) . '/index.php?option=com_komento&task=approveComment&token=' . $key;
				$data['commentContent']	= JFilterOutput::cleanText($options['comment']->comment);
				$date					= KomentoDateHelper::dateWithOffSet( $options['comment']->unformattedDate );
				$date					= KomentoDateHelper::toFormat( $date , $config->get( 'date_format' , '%A, %B %e, %Y' ) );
				$data['commentDate']	= $date;
				break;
			case 'report':
				$action = Komento::getTable( 'actions' );
				$action->load( $options['actionId'] );
				$actionUser = $action->action_by;

				$data['actionUser']			= Komento::getProfile( $actionUser );
				$data['commentPermalink']	= $data['contentPermalink'] . '#kmt-' . $options['comment']->id;
				$data['commentContent']		= JFilterOutput::cleanText($options['comment']->comment);
				$date						= KomentoDateHelper::dateWithOffSet( $options['comment']->unformattedDate );
				$date						= KomentoDateHelper::toFormat( $date , $config->get( 'date_format' , '%A, %B %e, %Y' ) );
				$data['commentDate']		= $date;
				break;
			case 'reply':
			case 'comment':
			case 'new':
			default:
				$data['commentPermalink']	= $data['contentPermalink'] . '#kmt-' . $options['comment']->id;
				$data['commentContent']		= JFilterOutput::cleanText($options['comment']->comment);
				$date						= KomentoDateHelper::dateWithOffSet( $options['comment']->unformattedDate );
				$date						= KomentoDateHelper::toFormat( $date , $config->get( 'date_format' , '%A, %B %e, %Y' ) );
				$data['commentDate']		= $date;
				$data['unsubscribe'] 		= rtrim( JURI::root(), '/' ) . '/index.php?option=com_komento&task=unsubscribe&id=';
				break;
		}

		return $data;
	}

	private function prepareTemplate( $type = 'new' )
	{
		$config			= Komento::getConfig();
		$templateType	= $config->get( 'notification_sendmailinhtml' ) ? 'html' : 'text';

		switch( $type )
		{
			case 'pending':
			case 'moderate':
				$file	= 'moderatecomment';
				break;
			case 'confirm':
				$file	= 'confirmsubscription';
				break;
			case 'report':
				$file	= 'reportcomment';
				break;
			case 'reply':
			case 'comment':
			case 'new':
			default:
				$file	= 'newcomment';
				break;
		}

		$file 	= 'emails/' . $file . '.' . $templateType . '.php';

		return $file;
	}

	private function prepareTitle( $type = 'new', $options = array() )
	{
		$subject = '';

		switch( $type )
		{
			case 'pending':
			case 'moderate':
				$subject = JText::_('COM_KOMENTO_NOTIFICATION_PENDING_COMMENT_SUBJECT') . ' (' . $options['comment']->contenttitle . ')';
				break;
			case 'confirm':
				$title = isset( $options['comment'] ) ? $options['comment']->contenttitle : '';
				$subject = JText::_('COM_KOMENTO_NOTIFICATION_CONFIRM_SUBSCRIPTION_SUBJECT') . ' (' . $title . ')';
				break;
			case 'report':
				$subject = JText::_('COM_KOMENTO_NOTIFICATION_REPORT_COMMENT_SUBJECT') . ' (' . $options['comment']->contenttitle . ')';
				break;
			case 'reply':
			case 'comment':
			case 'new':
			default:
				$subject = JText::_('COM_KOMENTO_NOTIFICATION_NEW_COMMENT_SUBJECT') . ' (' . $options['comment']->contenttitle . ')';
				break;
		}

		return $subject;
	}

	public function getMe( $type, $options )
	{
		$obj		= new stdClass();
		$my			= JFactory::getUser();

		if( empty( $my->id ) )
		{
			if( $type === 'confirm' && isset( $options['subscribeId'] ) )
			{
				$subscribeTable = Komento::getTable( 'subscription' );
				$subscribeTable->load( $options['subscribeId'] );

				$obj->id		= 0;
				$obj->fullname	= $subscribeTable->fullname;
				$obj->email		= $subscribeTable->email;

				return array( $obj->email => $obj );
			}

			return array();
		}

		$obj->id		= $my->id;
		$obj->fullname	= JText::_( $my->name );
		$obj->email		= $my->email;

		return array( $my->email => $obj );
	}

	public function getAuthor( $type, $options )
	{
		$config		= Komento::getConfig();
		if( !$config->get( 'notification_to_author' ) )
		{
			return array();
		}

		$application = Komento::loadApplication( $options['component'] )->load( $options['cid'] );

		if( $application === false )
		{
			$application = Komento::getErrorApplication( $options['component'], $options['cid'] );
		}

		$userid		= $application->getAuthorId();

		$obj			= new stdClass();
		$user			= JFactory::getUser( $userid );
		$obj->id		= $user->id;
		$obj->fullname	= JText::_( $user->name );
		$obj->email		= $user->email;

		return array( $user->email => $obj );
	}

	public function getSubscribers( $type, $options )
	{
		$config		= Komento::getConfig();
		if( !$config->get( 'notification_to_subscribers' ) )
		{
			return array();
		}

		$sql = Komento::getSql();
		$sql->select( '#__komento_subscription' )
			->column( 'id', 'subscriptionid' )
			->column( 'userid', 'id' )
			->column( 'fullname' )
			->column( 'email' )
			->where( 'component', $options['component'] )
			->where( 'cid', $options['cid'] )
			->where( 'published', 1 );

		$subscribers = $sql->loadObjectList();

		if (!$subscribers)
		{
			return array();
		}
		else
		{
			$result = array();

			foreach ($subscribers as $subscriber)
			{
				$result[$subscriber->email] = $subscriber;
			}

			return $result;
		}
	}

	public function getUsergroups( $type )
	{
		$config = Komento::getConfig();

		$gids = '';

		switch( $type )
		{
			case 'confirm':
				break;
			case 'pending':
			case 'moderate':
				$gids = $config->get( 'notification_to_usergroup_pending' );
				break;
			case 'report':
				$gids = $config->get( 'notification_to_usergroup_reported' );
				break;
			case 'reply':
				$gids = $config->get( 'notification_to_usergroup_reply' );
				break;
			case 'comment':
			case 'new':
			default:
				$gids = $config->get( 'notification_to_usergroup_comment' );
				break;
		}

		if( !empty( $gids ) )
		{
			if( !is_array( $gids ) )
			{
				$gids = explode( ',', $gids );
			}

			$users = array();
			$ids = array();

			foreach( $gids as $gid )
			{
				$ids = $ids + Komento::getUsersByGroup( $gid );
			}

			foreach( $ids as $id )
			{
				$tmp = JFactory::getUser( $id );

				$user = array(
					'id' => $tmp->id,
					'fullname' => $tmp->name,
					'email' => $tmp->email
				);

				$users[$tmp->email] = (object) $user;
			}

			return $users;
		}

		return array();
	}

	public function getAdmins()
	{
		$config		= Komento::getConfig();
		if( !$config->get( 'notification_to_admins' ) )
		{
			return array();
		}

		$saUsersIds	= Komento::getSAUsersIds();

		$sql = Komento::getSql();

		$sql->select( '#__users' )
			->column( 'id' )
			->column( 'name', 'fullname' )
			->column( 'email' );

		if( $saUsersIds )
		{
			$sql->where( 'id', $saUsersIds, 'in' );
		}

		$sql->where( 'sendEmail', '1' );

		$admins	= $sql->loadObjectList();

		if( !$admins )
		{
			return array();
		}
		else
		{
			$result = array();

			foreach( $admins as $admin )
			{
				$result[$admin->email] = $admin;
			}

			return $result;
		}
	}
}
