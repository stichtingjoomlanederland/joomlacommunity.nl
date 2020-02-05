<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussMailer extends EasyDiscuss
{
	// Array of subscription emails store in Mailqueue
	protected static $sentEmails = array();

	// Checking the email against the static array $sentEmails
	protected static function isEmailSent($email)
	{
		$email = trim($email);

		if (in_array($email, self::$sentEmails)) {
			return true;
		}

		array_push(self::$sentEmails, $email);

		return false;
	}


	public static function notifyAdministrators($data, $excludes = array(), $notifyAdmins = false, $notifyModerators = false, $onlyRetrieveEmails = false)
	{
		// Get and unique emails from admins, custom admins,
		// category moderators and custom category moderators
		$emails = array();

		if (!$notifyAdmins && !$notifyModerators) {
			return $emails;
		}

		$catId = isset($data['cat_id']) ? $data['cat_id'] : null;

		$emails = self::_getAdministratorsEmails($catId, $notifyAdmins, $notifyModerators);

		if (count($emails) > 0 && count($excludes) > 0) {
			$emails = array_diff($emails, $excludes);
		}

		// Only want to retrieve a list of administrator and moderator email
		if ($onlyRetrieveEmails && !empty($emails)) {
			return $emails;
		}

		if (!empty($emails)) {
			foreach ($emails as $email) {

				if ($email) {
					self::_storeQueue($email, $data);
				}
			}
		}

		return $emails;
	}

	/**
	 * Notifies subscribers on the site
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	public static function notifySubscribers($data, $excludes = array(), $aclType = DISCUSS_CATEGORY_ACL_ACTION_VIEW)
	{
		// Store all the sent emails
		$emailSent = array();

		// Notify site subscribers
		$siteSubscribers = self::getSubscribers('site', 0, $data['cat_id'], array(), $excludes, $aclType);

		foreach ($siteSubscribers as $subscriber) {
			$emailSent[] = $subscriber->email;
		}

		// Notify category subscribers
		$catSubscribers = self::getSubscribers('category', $data['cat_id'], $data['cat_id'], '', $excludes, $aclType);

		foreach ($catSubscribers as $subscriber) {
			$emailSent[] = $subscriber->email;
		}

		if (is_array($siteSubscribers) && is_array($catSubscribers)) {

			$results = array_unique(array_merge($siteSubscribers, $catSubscribers), SORT_REGULAR);
			$tobeSent = array();

			// Remove dupes records
			foreach ($results as $item) {

				// Add first item
				if (empty($tobeSent)) {
					$tobeSent[] = $item;
				}

				$isAdded = false;

				foreach ($tobeSent as $item2) {
					if ($item->email == $item2->email) {
						$isAdded = true;
					}
				}

				if (!$isAdded) {
					$tobeSent[] = $item;
				}
			}
		}

		// _saveQueue will not help you to unique out the emails
		self::_saveQueue($tobeSent, $data);

		$emailSent = array_unique($emailSent);

		return $emailSent;
	}

	public static function notifyThreadSubscribers($data, $excludes = array(), $aclType = DISCUSS_CATEGORY_ACL_ACTION_VIEW)
	{
		$subscribers = self::getSubscribers('post', $data['post_id'], $data['cat_id'], array(), $excludes, $aclType);

		self::_saveQueue($subscribers, $data);

		$emails = array();

		if (count($subscribers) > 0) {

			foreach ($subscribers as $sub) {
				$emails[] = $sub->email;
			}
		}

		return $emails;
	}

	/**
	 * Notifies the person who started the discussion
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	public static function notifyThreadOwner($data, $excludes = array())
	{
		self::_storeQueue($data['owner_email'], $data);

		return;
	}

	/**
	 * Notifies the who the actor of the post
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	public static function notifyActor($data, $excludes = array())
	{
		self::_storeQueue($data['actor_email'], $data);

		return;
	}

	/**
	 * Notify people that are involved in the discussion
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	public static function notifyThreadParticipants($data, $excludes = array())
	{
		$config = ED::config();
		$db = ED::db();
		$adminsEmails = array();

		// Need to exclude this for prevent send duplicate email to the same user with the new reply notification
		if ($config->get('notify_admin_onreply') || $config->get('notify_moderator_onreply')) {

			// retrieve site super admin
			$query = 'SELECT `email` FROM `#__users`';

			if (ED::getJoomlaVersion() >= '1.6') {

				$saUsersIds	= ED::getSAUsersIds();
				$query .= ' WHERE id IN (' . implode(',', $saUsersIds) . ')';
			} else {
				$query .= ' WHERE LOWER( `usertype` ) = ' . $db->Quote('super administrator');
			}

			$query .= ' AND `sendEmail` = ' . $db->Quote('1');
			$db->setQuery($query);

			$adminsEmails = $db->loadResultArray();
		}

		// merge with the admin and excludes emails
		$excludes = array_merge($adminsEmails, $excludes);

		// make it unique email
		$excludes = array_unique($excludes);
		$participants = self::_getParticipants($data['post_id'], $data['cat_id']);

		// need to do some exclusion here.
		if ($excludes && count($excludes) > 0) {
			$participants = array_diff($participants, $excludes);
		}

		if ($participants && count($participants) > 0) {

			$participants = array_unique($participants);

			foreach ($participants as $part) {
				self::_storeQueue($part, $data);
			}
		}

		return $participants;
	}

	public static function _getParticipants($postId, $catId)
	{
		$db = ED::db();

		$emails = array();
		$participants = array();
		$excludes = array();

		if (empty($postId)) {
			return $emails;
		}

		$my = JFactory::getUser();

		$query = 'SELECT DISTINCT a.`poster_email`, b.`email`, a.`user_id`';
		$query .= ' FROM `#__discuss_posts` AS a';
		$query .= '  LEFT JOIN `#__users` AS b ON a.`user_id` = b.`id`';
		$query .= ' WHERE (a.`parent_id` = ' . $db->Quote( $postId ) . ' OR a.`id` = ' . $db->Quote( $postId ) .')';

		if ($my->id > 0) {
			$query .= ' AND a.`user_id` != ' . $db->Quote( $my->id );
		}

		$db->setQuery($query);
		$results = $db->loadObjectList();

		// if this thread has participants
		if (count($results) > 0) {

			foreach ($results as $item) {
				$emails[] = (empty($item->email)) ? $item->poster_email : $item->email;

				$obj = new stdClass();
				$obj->id = $item->user_id;
				$obj->email = (empty($item->email)) ? $item->poster_email : $item->email;

				$participants[] = $obj;
			}

			$catModel = ED::model('Category');

			// retrieve a list of participant user id
			foreach ($participants as $participant) {

				$userId = (int) $participant->id;

				// this will return a list of disallowed category id which this user id doesn't have permission to access post reply
				$disallowed = $catModel->getDisallowedCategories($userId, DISCUSS_CATEGORY_ACL_ACTION_VIEWREPLY);

				// assign to this excludes variable if those participant user doesn't have permission to access post reply
				if (in_array($catId, $disallowed)) {
					$excludes[] = $participant->email;
				}
			}
		}

		// Exclude those participants user who do not have permission to access post reply
		$emails = array_diff($emails, $excludes);

		return $emails;
	}


	public static function notifyMention($emails, $data)
	{
		if (! $emails) {
			return;
		}

		foreach ($emails as $email) {
			self::_storeQueue($email, $data);
		}

		return;
	}



	/**
	 * Notify all subscribers except admins and mods.
	 * Store notification emails in mailqueue.
	 *
	 * @param	array	$data		data
	 * @param	array	$except		extra emails to be excluded
	 */
	public static function notifyAllMembers($data, $excludes = array())
	{
		$db = ED::db();
		$query	= 'SELECT DISTINCT(`email`) FROM ' . $db->nameQuote('#__users') . ' AS a';

		$config = ED::config();

		if (isset($data['cat_id']) && $config->get('notify_all_respect_category')) {
			$query .= ' INNER JOIN `#__user_usergroup_map` AS b';
			$query .= ' ON a.`id` = b.`user_id`';
		}

		$query .= ' WHERE a.`block` = 0 ';

		if (isset($data['cat_id']) && $config->get('notify_all_respect_category')) {
			$category = ED::table('Category');
			$category->load($data['cat_id']);
			$groups = $category->getViewableGroups();

			// when there is nothing return, this mean
			// there is no jooml user group that can view posts
			// from this category. If that is the case,
			// we dont have to process further. #573
			if (!$groups) {
				return;
			}

			$query .= ' AND b.`group_id` IN(' . implode(',', $groups) . ')';
		}

		if (!empty($excludes)) {

			for ($i = 0; $i < count($excludes); $i++) {
				$excludes[$i] = $db->Quote( $excludes[$i] );
			}

			$query	.= ' AND a.' . $db->nameQuote('email') . ' NOT IN (' . implode(',', $excludes) . ')';
		}

		// // Debug
		// echo str_ireplace('#__', 'jos_', $query);exit;
		$db->setQuery($query);

		$emails = $db->loadResultArray();

		if (!empty($emails)) {
			foreach ($emails as $email) {
				self::_storeQueue( $email, $data );
			}
		}
	}

	/**
	 * Inserts a record into the mail queue table and inserts an unsubscribe link
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	private static function _saveQueue($subscribers, $data)
	{
		if (!$subscribers) {
			return;
		}

		foreach ($subscribers as $subscriber) {

			// Append post title as unique id of the emails
			$isSent = self::isEmailSent($subscriber->email . '.' . $data['postTitle']);

			if (!$isSent) {
				$hash = base64_encode("type=".$subscriber->type."\r\nsid=".$subscriber->id."\r\nuid=".$subscriber->userid."\r\ntoken=".md5($subscriber->id.$subscriber->created));
				$data['unsubscribeLink'] = EDR::getRoutedURL('index.php?option=com_easydiscuss&controller=subscription&task=unsubscribe&data='.$hash, false, true);

				self::_storeQueue($subscriber->email, $data);
			}
		}

		return;
	}

	/**
	 * Inserts a record into the mail queue table
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	private static function _storeQueue($emailTo, $data)
	{
		if (!$emailTo) {
			return;
		}

		$mailq = ED::table('MailQueue');
		$mailq->recipient = $emailTo;
		$mailq->subject = $data['emailSubject'];
		$mailq->body = self::_prepareBody($data);
		$mailq->created = ED::date()->toSql();
		$mailq->ashtml = ED::config()->get('notify_html_format');

		$mailq->mailfrom = self::getMailFrom($data);
		$mailq->fromname = self::getFromName($data);
		$mailq->status = 0;

		return $mailq->store();
	}

	// Insert to MailQueue Table
	public function addQueue($emailTo, $subject, $content, $mailfrom = '', $fromname = '')
	{
		if (!$emailTo) {
			return;
		}

		$mailq = ED::table('MailQueue');
		$mailq->recipient = $emailTo;
		$mailq->subject = $subject;
		$mailq->body = $content;
		$mailq->created = ED::date()->toSql();
		$mailq->ashtml = ED::config()->get('notify_html_format');

		$mailq->mailfrom = ($mailfrom) ? $mailform : self::getMailFrom();
		$mailq->fromname = ($fromname) ? $fromname : self::getFromName();
		$mailq->status = 0;

		return $mailq->store();
	}

	/**
	 * Determine which Joomla 3.x or 2.5 version should get the administrator from
	 *
	 * @since	4.1.12
	 * @access	public
	 */
	private static function getAdministrators()
	{
		$db = ED::db();
		$admins = array();

		$joomlaVersion = ED::getJoomlaVersion();
		$saUsersIds	= ED::getSAUsersIds();

		if ($joomlaVersion >= '1.6' && $saUsersIds) {

			$query = 'SELECT `email` FROM `#__users`';
			$query .= ' WHERE id IN (' . implode(',', $saUsersIds) . ')';
			$query .= ' AND `sendEmail` = ' . $db->Quote('1');

			$db->setQuery($query);
			$admins = $db->loadResultArray();

			return $admins;
		}

		if ($joomlaVersion <= '1.5') {

			$query = 'SELECT `email` FROM `#__users`';
			$query .= ' WHERE LOWER( `usertype` ) = ' . $db->Quote('super administrator');
			$query .= ' AND `sendEmail` = ' . $db->Quote('1');

			$db->setQuery($query);
			$admins = $db->loadResultArray();

			return $admins;
		}

		return $admins;
	}

	/**
	 * Includes admins, custom admins, moderators, custom moderators
	 * unique these emails
	 */
	private static function _getAdministratorsEmails($catId, $notifyAdmins = false, $notifyModerators = false)
	{
		$config	= ED::config();
		$db = ED::db();

		$admins = array();
		$customAdmins = array();
		$mods = array();
		$customMods = array();

		if ($notifyAdmins) {

			$admins = self::getAdministrators();

			$siteCustomMods = $config->get('notify_custom');

			if ($siteCustomMods) {
				$customAdmins = explode(',' , $siteCustomMods);
			}
		}

		if ($notifyModerators) {
			$mods = ED::moderator()->getModeratorsEmails($catId);

			$customMods = array();

			if ($catId) {
				$category = ED::category($catId);

				$customMods = array();
				$catCustomMods = $category->getParam('cat_notify_custom');

				if (!empty($catCustomMods)) {
					$customMods = explode(',', $category->getParam('cat_notify_custom'));
				}
			}
		}

		$emails = array_unique(array_merge($admins, $customAdmins, $mods, $customMods));

		return $emails;
	}

	/**
	 * Get subscribers according to type
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getSubscribers($type, $cid, $categoryId, $params = array(), $excludes = array(), $aclType = DISCUSS_CATEGORY_ACL_ACTION_VIEW)
	{
		$db = ED::db();

		$query	= 'SELECT `content_id` FROM `#__discuss_category_acl_map`';
		$query	.= ' WHERE `category_id` = ' . $db->Quote($categoryId);
		$query	.= ' AND `acl_id` = ' . $db->Quote($aclType);
		$query	.= ' AND `type` = ' . $db->Quote('group');

		$db->setQuery($query);
		$categoryGrps = $db->loadResultArray();

		if (!empty($categoryGrps)) {

			// based on category permission.

			$result = array();
			$aclItems = array();
			$nonAclItems = array();

			// Site members
			$queryCatIds = implode(',', $categoryGrps);


			$query	= 'SELECT ds.* FROM `#__discuss_subscription` AS ds';
			$query	.= ' INNER JOIN `#__user_usergroup_map` as um on um.`user_id` = ds.`userid`';
			$query	.= ' WHERE ds.`interval` = ' . $db->Quote('instant');
			$query	.= ' AND ds.`type` = ' . $db->Quote($type);
			$query	.= ' AND ds.`cid` = ' . $db->Quote( $cid );
			$query	.= ' AND um.`group_id` IN (' . $queryCatIds. ')';


			$db->setQuery($query);
			$aclItems  = $db->loadObjectList();

			$guestGroupId = JComponentHelper::getParams('com_users')->get('guest_usergroup');

			// Now get the guest subscribers
			if (in_array('1', $categoryGrps) || in_array($guestGroupId, $categoryGrps)) {
				$query	= 'SELECT * FROM `#__discuss_subscription` AS ds';
				$query	.= ' WHERE ds.`interval` = ' . $db->Quote('instant');
				$query	.= ' AND ds.`type` = ' . $db->Quote($type);
				$query	.= ' AND ds.`cid` = ' . $db->Quote( $cid );
				$query	.= ' AND ds.`userid` = ' . $db->Quote('0');

				$db->setQuery($query);
				$nonAclItems  = $db->loadObjectList();
			}

			$result = array_merge($aclItems, $nonAclItems);

		} else {
			$query	= 'SELECT * FROM `#__discuss_subscription` '
					. ' WHERE `type` = ' . $db->Quote( $type )
					. ' AND `cid` = ' . $db->Quote( $cid )
					. ' AND `interval` = ' . $db->Quote( 'instant' );

			// Add email exclusions if there are any exclusions
			if (!empty($excludes)) {
				$excludes = !is_array($excludes) ? array($excludes) : $excludes;

				$query 	.= 'AND ' . $db->nameQuote( 'email' ) . ' NOT IN(';

				for ($i = 0; $i < count($excludes); $i++) {

					$query .= $db->Quote($excludes[$i]);

					if (next($excludes) !== false) {
						$query .= ',';
					}
				}

				$query .= ')';
			}

			$db->setQuery($query);
			$result = $db->loadObjectList();
		}

		// lets run another checking to ensure the emails doesnt exists in exclude array
		$finalResult = array();

		if (count($excludes) > 0 && count($result) > 0) {
			foreach ($result as $item) {
				$email = $item->email;

				if (!in_array($email, $excludes)) {
					$finalResult[] = $item;
				}
			}
		}

		if (empty($excludes)) {
			$finalResult = $result;
		}

		if (isset($params['emailOnly']) && $params['emailOnly']) {
			$emails = array();

			foreach ($finalResult as $subscriber) {
				$emails[] = $subscriber->email;
			}

			return $emails;
		}

		return $finalResult;
	}

	/**
	 * Sets the "From" e-mail address
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	public static function getMailFrom($data = array())
	{
		$config = ED::config();

		// Modify the from name to the user that generated this activity
		if ($config->get('notify_modify_from') && isset($data['senderObject']) && $data['senderObject']) {
			return $data['senderObject']->user->email;
		}

		static $mailfrom = null;

		if (!$mailfrom) {
			$config = ED::config();
			$mailfrom = $config->get('notification_sender_email', ED::jconfig()->getValue('mailfrom'));
		}

		return $mailfrom;
	}

	/**
	 * Sets the "From" name
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	public static function getFromName($data = array())
	{
		$config = ED::config();

		// Modify the from name to the user that generated this activity
		if ($config->get('notify_modify_from') && isset($data['senderObject']) && $data['senderObject']) {
			return $data['senderObject']->getName();
		}

		static $fromname = null;

		if (!$fromname) {

			$fromname = $config->get('notification_sender_name', ED::jconfig()->getValue('fromname'));
		}

		return $fromname;
	}

	public static function getSiteName()
	{
		static $sitename = null;

		if (!$sitename) {
			$jConfig = ED::jconfig();
			$sitename = $jConfig->getValue('sitename');
		}

		return $sitename;
	}

	public static function getHeadingTitle()
	{
		static $title = null;

		if (!$title) {
			$config = ED::config();
			$jConfig = ED::jconfig();
			$title = $config->get('notify_email_title') ? $config->get('notify_email_title') : $jConfig->getValue('sitename');
		}

		return $title;
	}

	public static function getReplyBreaker()
	{
		static $string = null;

		if (is_null($string)) {
			$config	= ED::config();
			$string = $config->get('mail_reply_breaker') ? JText::sprintf('COM_EASYDISCUSS_EMAILTEMPLATE_REPLY_BREAK', $config->get('mail_reply_breaker')) : '';
		}

		return $string;
	}

	public static function getSubscriptionsManagerLink()
	{
		static $link = null;

		if (!$link) {
			$link = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=profile#Subscriptions', false, true);
		}

		return $link;
	}

	protected static function _prepareBody($data)
	{
		$config = ED::config();
		$app = JFactory::getApplication();

		$type = $config->get('notify_html_format') ? 'html' : 'text';

		// Set the logo for the generic email template
		$logo = ED::getLogo();

		$template = $data['emailTemplate'];
		$replyBreaker = false;

		// We only want to show reply breaker for reply email.
		if ($template == 'email.post.reply.new.php') {
			$replyBreaker = self::getReplyBreaker();
		}

		$template = str_ireplace('.php', '', $template);
		// If this uses html, we need to switch the template file
		if ($type == 'html') {
			$template = $template . '.html';
		}

		$theme	= ED::themes();

		$theme->set('logo', $logo);


		foreach ($data as $key => $val) {
			$theme->set($key, $val);
		}

		$file = 'site/emails/' . $template;
		$contents = $theme->output($file, array('emails' => true));


		$emailTitle	= self::getHeadingTitle();

		$unsubscribeLink = isset($data['unsubscribeLink']) ? $data['unsubscribeLink'] : '';
		$subscriptionsLink = self::getSubscriptionsManagerLink();

		$theme->set('emailTitle', $emailTitle);
		$theme->set('contents', $contents);
		$theme->set('unsubscribeLink', $unsubscribeLink);
		$theme->set('subscriptionsLink', $subscriptionsLink);
		$theme->set('replyBreakText', $replyBreaker);

		$template = "email.template.{$type}";

		$file = 'site/emails/' . $template;
		$output = $theme->output($file, array('emails'=> true));

		if ($type != 'html') {
			$output = strip_tags($output);
		}

		return $output;
	}

	/**
	 * Trim email content before send
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function trimEmail($content)
	{
		if ($this->config->get('layout_editor') != 'bbcode') {

			if ($this->config->get('main_notification_max_length') > '0') {
				$filterHtmlTag = '<p><div><table><tr><td><thead><tbody><br><br />';

				// Remove html + img tags
				$content = strip_tags($content, $filterHtmlTag);

				// Truncate the content
				$content = $this->truncate($content);
			}

			return $content;
		}

		// Truncate the content
		$content = $this->truncate($content);

		// Remove video codes from the e-mail since it will not appear on e-mails
		$content = ED::videos()->strip($content);

		return $content;
	}

	/**
	 * Truncate email content
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function truncate($content)
	{
		// Convert HTML entities to characters e.g. &lt;br&gt; => <br>
		// $content = html_entity_decode($content);

		if ($this->config->get('main_notification_max_length') > '0') {
			
			$content = strip_tags($content);
			$content = substr($content, 0, $this->config->get('main_notification_max_length'));
			$content = $content . '...';
		}

		return $content;
	}

	/**
	 * Get email moderation link
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getModerationLink($approveURL, $rejectURL)
	{
		$content  = '<div style="display:inline-block;width:100%;padding:20px;border-top:1px solid #ccc;padding:20px 0 10px;margin-top:20px;line-height:19px;color:#555;font-family:\'Lucida Grande\',Tahoma,Arial;font-size:12px;text-align:left">';
		$content .= '<a href="' . $approveURL . '" style="display:inline-block;padding:5px 15px;background:#fc0;border:1px solid #caa200;border-bottom-color:#977900;color:#534200;text-shadow:0 1px 0 #ffe684;font-weight:bold;box-shadow:inset 0 1px 0 #ffe064;-moz-box-shadow:inset 0 1px 0 #ffe064;-webkit-box-shadow:inset 0 1px 0 #ffe064;border-radius:2px;moz-border-radius:2px;-webkit-border-radius:2px;text-decoration:none!important">' . JText::_('COM_EASYDISCUSS_EMAIL_APPROVE_POST') . '</a>';
		$content .= ' ' . JText::_('COM_EASYDISCUSS_OR') . ' <a href="' . $rejectURL . '" style="color:#477fda">' . JText::_('COM_EASYDISCUSS_REJECT') . '</a>';
		$content .= '</div>';

		return $content;
	}

	/**
	 * Notifies only a specific set of Joomla user groups
	 *
	 * @since	4.0.19
	 * @access	public
	 */
	public function notifyUserGroups($data, $excludes = array())
	{
		$ids = trim($this->config->get('notify_joomla_groups_ids'));

		if (!$ids) {
			return;
		}

		$ids = explode(',', $ids);

		$db = ED::db();
		$query = 'SELECT DISTINCT(a.`email`) FROM ' . $db->nameQuote('#__users') . ' AS a';
		$query .= ' INNER JOIN ' . $db->nameQuote('#__user_usergroup_map') . ' AS b';
		$query .= ' ON a.' . $db->qn('id') . ' = b.' . $db->qn('user_id');
		$query .= ' WHERE a.`block` = 0 ';

		if (!empty($excludes)) {

			for ($i = 0; $i < count($excludes); $i++) {
				$excludes[$i] = $db->Quote($excludes[$i]);
			}

			$query .= ' AND a.' . $db->nameQuote('email') . ' NOT IN (' . implode(',', $excludes) . ')';
		}

		$query .= ' AND b.group_id IN (';

		$i = 1;
		$total = count($ids);

		foreach ($ids as $groupId) {
			$query .= $db->Quote($groupId);

			if ($i < $total) {
				$query .= ',';
			}

			$i++;
		}

		$query .= ')';

		$db->setQuery($query);

		$emails = $db->loadResultArray();

		if (!empty($emails)) {
			foreach ($emails as $email) {
				self::_storeQueue($email, $data);
			}
		}
	}

	/**
	 * Public layer to call _prepareBody proctected function
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function generateEmailBody($data)
	{
		return $this->_prepareBody($data);
	}
}
