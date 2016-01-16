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

/**
 * EasyDiscuss Email Notification Helper
 *
 * notifyAdministrators.
 *  - To admins, moderators and custom emails to admin and mod.
 *  - Only one notification per unique email.
 *  - Cannot unsubscribe.
 *
 * notifySubscribers
 *  - To site, category, thread subscribers
 *  - Subscribers get one notification per unique email.
 *  - Site subscribers > Category subscribers
 *  - Discussion owner > Discussion subscribers
 *  - Unsubscribe links provided.
 *
 * notifyAllMembers
 *  - To all the users
 *  - Cannot unsubscribe
 *
 * Todo: List of actions
 * new_asked || new_replied ||  new_resolved || new_labeled || new_assigned || new_tagged || new_featured || new_published || new_commented || new_liked || new_favorited
 *
 * Todo: List of notification receivers
 * all, all_subscribers
 * admins, custom_admin_emails,
 * category_moderators, custom_category_emails,
 * category_subscribers,
 * tag_subscribers,
 * thread_owner,
 * thread_subscribers,
 *
 */
class DiscussMailerHelper
{
	// Array of subscription emails store in Mailqueue
	protected static $sentEmails = array();

	// Checking the email against the static array $sentEmails
	protected static function isEmailSent( $email )
	{
		$email = trim($email);

		if( in_array($email, self::$sentEmails) ) {
			return true;
		}

		array_push(self::$sentEmails, $email);

		return false;
	}


	public static function notifyAdministrators( $data, $excludes = array(), $notifyAdmins = false, $notifyModerators = false )
	{
		// Get and unique emails from admins, custom admins,
		// category moderators and custom category moderators

		if( !$notifyAdmins && !$notifyModerators ) {
			return;
		}

		$catId = isset($data['cat_id']) ? $data['cat_id'] : null;

		$emails = self::_getAdministratorsEmails( $catId, $notifyAdmins, $notifyModerators );

		if( count( $emails ) > 0 && count( $excludes ) > 0 )
		{
			$emails   = array_diff( $emails, $excludes );
		}

		if( !empty($emails) ) {
			foreach ($emails as $email) {
				self::_storeQueue( $email, $data );
			}
		}

		return $emails;
	}

	public static function notifySubscribers( $data, $excludes = array() )
	{

		// Do not notify admin again because if admin also subscribe to the site, he will get double email.
		// $adminEmails 	= array();
		// $adminEmails 	= self::_getAdministratorsEmails( $data['cat_id'], true, true );
		// $excludes 		= array_unique( array_merge( $excludes, $adminEmails ) );

		// Store all the sent emails
		$emailSent = array();
		//$tobeSent  = array();

		// Notify site subscribers
		$siteSubscribers = self::getSubscribers( 'site', 0, 0 , array() , $excludes );
		//self::_saveQueue($siteSubscribers, $data);

		foreach( $siteSubscribers as $subscriber )
		{
			$emailSent[] = $subscriber->email;
			//$tobeSent[]  = $subscriber;
		}

		// Notify category subscribers
		$catSubscribers = self::getSubscribers( 'category', $data['cat_id'], $data['cat_id'], '', $excludes );
		//self::_saveQueue($catSubscribers, $data);

		foreach( $catSubscribers as $subscriber )
		{
			$emailSent[] = $subscriber->email;
			//$tobeSent[]  = $subscriber;
		}

		if( is_array($siteSubscribers) && is_array($catSubscribers) )
		{
			$results = array_unique( array_merge( $siteSubscribers, $catSubscribers ), SORT_REGULAR );
			$tobeSent = array();

			// Remove dupes records
			foreach ($results as $item)
			{
				if( empty($tobeSent) )
				{
					// Add first item
					$tobeSent[] = $item;
				}

				$isAdded = false;

				foreach( $tobeSent as $item2 )
				{
					if( $item->email == $item2->email )
					{
						$isAdded = true;
					}
				}

				if( !$isAdded )
				{
					$tobeSent[] = $item;
				}
			}
		}

		// _saveQueue will not help you to unique out the emails
		self::_saveQueue($tobeSent, $data);

		$emailSent = array_unique( $emailSent );


		// We doing this is because super user might be subscribers too, hence we need to get the emails
		// and exclude it during the next step notify admin.
		return $emailSent;
	}

	public static function notifyThreadSubscribers( $data, $excludes = array() )
	{
		$subscribers = self::getSubscribers( 'post', $data['post_id'], $data['cat_id'] , array() , $excludes );

		self::_saveQueue($subscribers, $data);

		$emails = array();

		if( count($subscribers) > 0 )
		{
			foreach( $subscribers as $sub)
			{
				$emails[] = $sub->email;
			}
		}

		return $emails;
	}

	public static function notifyThreadOwner( $data, $excludes = array() )
	{
		self::_storeQueue( $data['owner_email'], $data );

		return;
	}

	public static function notifyThreadParticipants( $data, $excludes = array() )
	{
		$excludes 		= array_unique( $excludes );
		$participants	= self::_getParticipants( $data['post_id'] );

		//need to do some exclusion here.
		if( count( $excludes ) > 0 )
		{
			$participants   = array_diff( $participants, $excludes );
		}

		if( count($participants) > 0 )
		{
			$participants   = array_unique($participants);

			foreach( $participants as $part )
			{
				self::_storeQueue( $part, $data );
			}
		}
		return $participants;
	}

	public static function _getParticipants( $postId )
	{
		$db = DiscussHelper::getDBO();

		$emails = array();

		if( empty( $postId ) )
			return $emails;

		$my = JFactory::getUser();

		$query = 'SELECT a.`poster_email`, b.`email`';
		$query .= ' FROM `#__discuss_posts` AS a';
		$query .= '  LEFT JOIN `#__users` AS b ON a.`user_id` = b.`id`';
		$query .= ' WHERE (a.`parent_id` = ' . $db->Quote( $postId ) . ' OR a.`id` = ' . $db->Quote( $postId ) .')';
		if( $my->id > 0 )
		{
			$query .= ' AND a.`user_id` != ' . $db->Quote( $my->id );
		}

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		if( count($result) > 0 )
		{
			foreach( $result as $item )
			{
				$emails[] = ( empty($item->email) ) ? $item->poster_email : $item->email;
			}
		}

		// Ensure that they are always unique.
		$emails 	= array_unique( $emails );

		return $emails;
	}




	/**
	 * Notify all subscribers except admins and mods.
	 * Store notification emails in mailqueue.
	 *
	 * @param	array	$data		data
	 * @param	array	$except		extra emails to be excluded
	 */
	public static function notifyAllMembers( $data, $excludes = array() )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT `email` FROM ' . $db->nameQuote( '#__users' );
		$query .= ' WHERE `block` = 0 ';

		if( !empty($excludes) )
		{
			for( $i = 0; $i < count( $excludes ); $i++ )
			{
				$excludes[$i] = $db->Quote( $excludes[$i] );
			}

			$query	.= ' AND ' . $db->nameQuote( 'email' ) . ' NOT IN (' . implode(',', $excludes) . ')';
		}

		$db->setQuery( $query );

		$emails = $db->loadResultArray();

		if( !empty($emails) ) {
			foreach ($emails as $email) {
				self::_storeQueue( $email, $data );
			}
		}
	}

	private static function _saveQueue( $subscribers, $data )
	{
		if( !empty($subscribers) ) {
			foreach ($subscribers as $subscriber) {
				if( !$isSent = self::isEmailSent($subscriber->email) ) {
					$hash = base64_encode("type=".$subscriber->type."\r\nsid=".$subscriber->id."\r\nuid=".$subscriber->userid."\r\ntoken=".md5($subscriber->id.$subscriber->created));
					$data['unsubscribeLink'] = DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&controller=subscription&task=unsubscribe&data='.$hash, false, true);
					self::_storeQueue( $subscriber->email, $data );
				}
			}
		}

		return;
	}

	// Insert to MailQueue Table
	private static function _storeQueue($emailTo, $data)
	{
		if( !$emailTo ) {
			return;
		}

		$mailq	= DiscussHelper::getTable( 'MailQueue' );
		$mailq->recipient	= $emailTo;
		$mailq->subject		= $data['emailSubject'];
		$mailq->body		= self::_prepareBody($data);
		$mailq->created		= DiscussHelper::getDate()->toMySQL();
		$mailq->ashtml		= DiscussHelper::getConfig()->get( 'notify_html_format' );

		$mailq->mailfrom	= self::getMailFrom();
		$mailq->fromname	= self::getFromName();
		$mailq->status		= 0;

		return $mailq->store();
	}

	/**
	 * Includes admins, custom admins, moderators, custom moderators
	 * unique these emails
	 */
	private static function _getAdministratorsEmails( $catId, $notifyAdmins = false, $notifyModerators = false )
	{
		$config	= DiscussHelper::getConfig();
		$db		= DiscussHelper::getDBO();

		$admins			= array();
		$customAdmins	= array();
		$mods			= array();
		$customMods		= array();

		if( $notifyAdmins )
		{
			$query	= 'SELECT `email` FROM `#__users`';

			if( DiscussHelper::getJoomlaVersion() >= '1.6' )
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

			$admins = $db->loadResultArray();

			$customAdmins = explode( ',' , $config->get( 'notify_custom') );
		}

		if( $notifyModerators )
		{
			$mods = DiscussHelper::getHelper( 'Moderator' )->getModeratorsEmails( $catId );

			$customMods = array();

			if( $catId ) {
				$category = DiscussHelper::getTable( 'Category' );
				$category->load( $catId );
				$customMods = explode( ',' , $category->getParam( 'cat_notify_custom') );
			}
		}

		$emails =  array_unique(array_merge($admins, $customAdmins, $mods, $customMods));

		return $emails;
	}

	/**
	 * Get subscribers according to type
	 */
	public static function getSubscribers( $type, $cid, $categoryId, $params = array() , $excludes = array() )
	{
		$db		= DiscussHelper::getDbo();

		$query	= 'SELECT `content_id` FROM `#__discuss_category_acl_map`';
		$query	.= ' WHERE `category_id` = ' . $db->Quote($categoryId);
		$query	.= ' AND `acl_id` = ' . $db->Quote(DISCUSS_CATEGORY_ACL_ACTION_VIEW);
		$query	.= ' AND `type` = ' . $db->Quote('group');

		$db->setQuery( $query );
		$categoryGrps	= $db->loadResultArray();

		if( !empty($categoryGrps) )
		{
			$result			= array();
			$aclItems		= array();
			$nonAclItems	= array();

			// Site members
			$queryCatIds = implode( ',', $categoryGrps );

			if( DiscussHelper::getJoomlaVersion() >= '1.6' )
			{
				$query	= 'SELECT ds.* FROM `#__discuss_subscription` AS ds';
				$query	.= ' INNER JOIN `#__user_usergroup_map` as um on um.`user_id` = ds.`userid`';
				$query	.= ' WHERE ds.`interval` = ' . $db->Quote('instant');
				$query	.= ' AND ds.`type` = ' . $db->Quote($type);
				$query	.= ' AND ds.`cid` = ' . $db->Quote( $cid );
				$query	.= ' AND um.`group_id` IN (' . $queryCatIds. ')';
			}
			else
			{
				$query	= 'SELECT ds.* FROM `#__discuss_subscription` AS ds';
				$query	.= ' INNER JOIN `#__core_acl_aro` as caa on caa.`value` = ds.`userid`';
				$query	.= ' INNER JOIN `#__core_acl_groups_aro_map` as cagam on cagam.`aro_id` = caa.`id`';
				$query	.= ' WHERE ds.`interval` = ' . $db->Quote('instant');
				$query	.= ' AND ds.`type` = ' . $db->Quote($type);
				$query	.= ' AND ds.`cid` = ' . $db->Quote( $cid );
				$query	.= ' AND cagam.`group_id` IN (' . $queryCatIds. ')';
			}

			$db->setQuery( $query );
			$aclItems  = $db->loadObjectList();

			// Now get the guest subscribers
			if( in_array( '1', $categoryGrps ) || in_array( '0', $categoryGrps ) )
			{
				$query	= 'SELECT * FROM `#__discuss_subscription` AS ds';
				$query	.= ' WHERE ds.`interval` = ' . $db->Quote('instant');
				$query	.= ' AND ds.`type` = ' . $db->Quote($type);
				$query	.= ' AND ds.`cid` = ' . $db->Quote( $cid );
				$query	.= ' AND ds.`userid` = ' . $db->Quote('0');

				$db->setQuery( $query );
				$nonAclItems  = $db->loadObjectList();
			}

			$result = array_merge($aclItems, $nonAclItems);
		}
		else
		{
			$query	= 'SELECT * FROM `#__discuss_subscription` '
					. ' WHERE `type` = ' . $db->Quote( $type )
					. ' AND `cid` = ' . $db->Quote( $cid )
					. ' AND `interval` = ' . $db->Quote( 'instant' );

			// Add email exclusions if there are any exclusions
			if( !empty( $excludes ) )
			{
				$excludes	= !is_array( $excludes ) ? array( $excludes ) : $excludes;

				$query 	.= 'AND ' . $db->nameQuote( 'email' ) . ' NOT IN(';

				for( $i = 0; $i < count( $excludes); $i++ )
				{
					$query 	.= $db->Quote( $excludes[ $i ] );

					if( next( $excludes ) !== false )
					{
						$query 	.= ',';
					}
				}

				$query 	.= ')';
			}

			$db->setQuery($query);
			$result = $db->loadObjectList();
		}

		//lets run another checking to ensure the emails doesnt exists in exclude array
		$finalResult    = array();
		if( count( $excludes ) > 0 && count($result) > 0 )
		{
			foreach( $result as $item)
			{
				$email  = $item->email;
				if( !in_array($email, $excludes) )
				{
					$finalResult[]  = $item;
				}
			}
		}

		if( empty($excludes) )
		{
			$finalResult = $result;
		}

		return $finalResult;
	}

	public static function getMailFrom()
	{
		static $mailfrom = null;

		if( !$mailfrom) {
			$config		= DiscussHelper::getConfig();
			$mailfrom	= $config->get( 'notification_sender_email' , DiscussHelper::getJConfig()->getValue( 'mailfrom' ) );
		}

		return $mailfrom;
	}

	public static function getFromName()
	{
		static $fromname = null;

		if( !$fromname) {
			$config		= DiscussHelper::getConfig();
			$fromname	= $config->get( 'notification_sender_name' , DiscussHelper::getJConfig()->getValue( 'fromname' ) );
		}

		return $fromname;
	}

	public static function getSiteName()
	{
		static $sitename = null;

		if( !$sitename ) {
			$jConfig	= DiscussHelper::getJConfig();
			$sitename	= $jConfig->getValue( 'sitename' );
		}

		return $sitename;
	}

	public static function getHeadingTitle()
	{
		static $title = null;

		if( !$title )
		{
			$config		= DiscussHelper::getConfig();
			$jConfig 	= DiscussHelper::getJConfig();
			$title		= $config->get( 'notify_email_title' ) ? $config->get( 'notify_email_title' ) : $jConfig->getValue( 'sitename' );
		}

		return $title;
	}

	public static function getReplyBreaker()
	{
		static $string = null;

		if( is_null($string) ) {
			$config	= DiscussHelper::getConfig();
			$string = $config->get('mail_reply_breaker') ? JText::sprintf('COM_EASYDISCUSS_EMAILTEMPLATE_REPLY_BREAK', $config->get('mail_reply_breaker')) : '';
		}

		return $string;
	}

	public static function getSubscriptionsManagerLink()
	{
		static $link = null;

		if( !$link ) {
			$link = DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=profile#Subscriptions', false, true);
		}

		return $link;
	}

	protected static function _prepareBody( $data )
	{
		$config	= DiscussHelper::getConfig();
		$type	= $config->get( 'notify_html_format' ) ? 'html' : 'text';

		$template   = $data['emailTemplate'];
		// If this uses html, we need to switch the template file
		if( $type == 'html' )
		{
			$template	= str_ireplace( '.php' , '.html.php' , $template );
		}

		$theme	= new DiscussThemes();

		foreach( $data as $key => $val )
		{
			$theme->set( $key , $val );
		}

		$contents	= $theme->fetch( $template , array( 'emails' => true ) );
		unset($theme);

		$emailTitle	= self::getHeadingTitle();
		$replyBreaker = self::getReplyBreaker();

		$unsubscribeLink = isset($data[ 'unsubscribeLink' ]) ? $data[ 'unsubscribeLink' ] : '';
		$subscriptionsLink = self::getSubscriptionsManagerLink();

		$theme		= new DiscussThemes();
		$theme->set( 'emailTitle'		, $emailTitle );
		$theme->set( 'contents'			, $contents );
		$theme->set( 'unsubscribeLink'	, $unsubscribeLink );
		$theme->set( 'subscriptionsLink'	, $subscriptionsLink );
		$theme->set( 'replyBreakText'	, $replyBreaker );

		$template = "email.template.{$type}.php";
		$output = $theme->fetch( $template, array('emails'=> true) );

		if ( $type != 'html' ) {
			$output = strip_tags($output);
		}

		return $output;
	}

	private static function _trimEmail( $content )
	{
		$config	= DiscussHelper::getConfig();

		if( $config->get('main_notification_max_length') > '0' )
		{
			$content	= substr( $content, 0, $config->get('main_notification_max_length') );
			$content	= $content . '...';
		}

		return $content;
	}
}
