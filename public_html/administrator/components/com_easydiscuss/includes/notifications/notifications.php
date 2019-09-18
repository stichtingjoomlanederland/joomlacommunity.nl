<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussNotifications extends EasyDiscuss
{
	static $rules = array(
						DISCUSS_NOTIFICATIONS_REPLY,
						DISCUSS_NOTIFICATIONS_FAVOURITE,
						DISCUSS_NOTIFICATIONS_RESOLVED,
						DISCUSS_NOTIFICATIONS_ACCEPTED,
						DISCUSS_NOTIFICATIONS_FEATURED,
						DISCUSS_NOTIFICATIONS_COMMENT,
						DISCUSS_NOTIFICATIONS_MENTIONED,
						DISCUSS_NOTIFICATIONS_LIKES_DISCUSSION,
						DISCUSS_NOTIFICATIONS_LIKES_REPLIES,
						DISCUSS_NOTIFICATIONS_LOCKED,
						DISCUSS_NOTIFICATIONS_UNLOCKED,
						DISCUSS_NOTIFICATIONS_ON_HOLD,
						DISCUSS_NOTIFICATIONS_WORKING_ON,
						DISCUSS_NOTIFICATIONS_REJECTED,
						DISCUSS_NOTIFICATIONS_NO_STATUS,
						DISCUSS_NOTIFICATIONS_VOTE_UP_REPLY,
						DISCUSS_NOTIFICATIONS_VOTE_DOWN_REPLY,
						DISCUSS_NOTIFICATIONS_VOTE_UP_DISCUSSION,
						DISCUSS_NOTIFICATIONS_VOTE_DOWN_DISCUSSION,
						DISCUSS_NOTIFICATIONS_MODERATE_QUESTION,
						DISCUSS_NOTIFICATIONS_MODERATE_REPLY
					);

	public function clear($id)
	{
		// Clear notifications for the post.
		$model = ED::model('Notification');
		$model->markRead($this->my->id, $id, self::$rules);
	}

	/**
	 * Formats and aggregates notification items.
	 *
	 * @access	public
	 * @param	Array	$items	An array of notification items
	 *
	 **/
	public function format(&$items, $group = false)
	{
		// Since these are just grouped items, fetch all child items.
		foreach ($items as $item) {
			$childs	= $this->getChilds($item);

			$this->aggregate($item, $childs);

			// Unique all authors
			$item->author = array_unique($item->author);

			// Reset the keys
			$item->author = array_values($item->author);

			$item->authorAvatar = '';

			if (count($item->author) == 1 and $item->author[0] == '-1') {
				$item->authorProfile = ED::user('0');
			} else {
				// the array might contain '-1'
				$tmpArr = array_diff($item->author, array('-1'));
				$item->authorProfile = ED::user($tmpArr[0]);
			}

			// Set the permalink
			$item->permalink = $item->component == 'com_easydiscuss' ? EDR::_($item->permalink) : $item->permalink;

			// Get the author string
			$item->authorHTML = $this->getAuthorHTML($item->author);

			$item->title = str_ireplace('{authors}', $item->authorHTML, $item->title);
			$item->postTitle = $item->title;

			// Set a permalink to the title.
			$title = explode(',', $item->title);
			if (count($title) > 1) {
				// Links the post title
				$postTitle = $title[1];
				$action = str_ireplace($postTitle, '', $item->title);

				$item->title = $action . '<a href="' . $item->permalink .'">' . $postTitle . '</a>';
			}

			// Get the lapsed time
			$item->touched = ED::Date()->toLapsed($item->created);
		}

		if ($group) {
			$items = $this->group($items);
		}
	}

	/**
	 * Get the author html output
	 */
	private function getAuthorHTML($authors)
	{
		// @TODO: Make this option configurable
		// This option sets the limit on the number of authors to be displayed in the notification
		$limit = 3;

		$html = '';

		//preload users
		$userIds = array();
		foreach($authors as $author) {

			if ($author != '-1') {
				$userIds[] = $author;
			}
		}

		if ($userIds) {
			ED::user($userIds);
		}

		for ($i = 0; $i < count($authors); $i++) {

			// var_dump($authors[$i]);

			if ($authors[$i] == '-1') {
				$html .= ' <b>' . JText::_('COM_EASYDISCUSS_ANONYMOUS_USER') . '</b>';
			} else {
				$profile = ED::user($authors[$i]);
				$html .= ' <b>' . $profile->getName() . '</b>';
			}

			if ($i + 1 == $limit) {
				// Calculate the balance
				$balance = count($authors) - ($i + 1);
				$html .= ' ' . ED::string()->getNoun('COM_EASYDISCUSS_AND_OTHERS', $balance, true);
				break;
			}

			if (isset($authors[$i + 2])) {
				$html .= JText::_(',');
			} else {
				if (isset($authors[$i + 1])) {
					$html .= ' ' . JText::_('COM_EASYDISCUSS_AND');
				}
			}
		}

		return $html;
	}

	/**
	 * Retrieves the child notification items.
	 *
	 * @access	private
	 * @param	int	$cid	The parent id
	 * @param	int	$type	The parent type
	 * @param	int $target	The target user
	 **/
	private function getChilds($parent)
	{
		$db = ED::db();

		$query	= 'SELECT * FROM ' . $db->nameQuote('#__discuss_notifications') . ' '
				. 'WHERE ' . $db->nameQuote('target') . '=' . $db->Quote($parent->target) . ' '
				. 'AND ' . $db->nameQuote('cid') . '=' . $db->Quote($parent->cid) . ' '
				. 'AND ' . $db->nameQuote('type') . '=' . $db->Quote($parent->type) . ' '
				. 'AND DATE_FORMAT( ' . $db->nameQuote('created') . ', "%Y%m%d" ) =' . $db->Quote($parent->day) . ' '
				. 'ORDER BY `created` ASC';

		$db->setQuery($query);
		$childs	= $db->loadObjectList();

		return $childs;
	}

	/**
	 * Aggregates certain notification item
	 *
	 * @access	private
	 * @param	Object	$parent	The parent item.
	 * @param	Object	$childs	The notification item.
	 **/
	private function aggregate(&$parent, &$childs)
	{
		$parent->author = array($parent->author);

		if ($parent->anonymous) {
			$parent->author = array('-1');
		}

		foreach($childs as $child) {

			if ($child->anonymous) {
				$parent->author[] = '-1';
			} else {
				$parent->author[] = $child->author;
			}

			$parent->latest = $child->created;
		}
	}

	/**
	 * Group up items by days
	 *
	 * @access	private
	 * @param	Array	$items	An array of db items
	 */
	private function group(&$items)
	{
		$result	= array();
		$config = ED::config();

		foreach ($items as $item) {

			$date = ED::date($item->created);
			$day = $date->display(JText::_('DATE_FORMAT_LC1'));

			if (!isset($result[$day])) {
				$result[$day]	= array();
			}

			$result[$day][]	= $item;
		}

		return $result;
	}

	public function getAdminEmails()
	{
		$db = ED::db();

		$saUsersIds	= ED::getSAUsersIds();

		$query	= 'SELECT `name`, `email`';
		$query	.= ' FROM #__users';
		$query	.= ' WHERE id IN (' . implode(',', $saUsersIds) . ')';
		$query	.= ' AND `sendEmail` = ' . $db->Quote('1');

		$db->setQuery($query);

		if ($db->getErrorNum()) {
			JError::raiseError(500, $db->stderr());
		}

		$result = $db->loadObjectList();
		return $result;

	}

	public function getAdmins()
	{
		$db	= ED::db();
		$saUsersIds	= ED::getSAUsersIds();

		$query	= 'SELECT `id`';
		$query	.= ' FROM #__users';
		$query	.= ' WHERE id IN (' . implode(',', $saUsersIds) . ')';
		$query	.= ' AND `sendEmail` = ' . $db->Quote('1');

		$db->setQuery($query);

		if ($db->getErrorNum()) {
			JError::raiseError(500, $db->stderr());
		}

		$result = $db->loadObjectList();
		return $result;
	}

	/**
	 * Sets the "From" e-mail address
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	public function getFromEmail($data)
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
	public function getFromName($data)
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

	public function addQueue($toEmails, $subject = '', $body = '', $template='', $data = array())
	{
		$mainframe = JFactory::getApplication();
		$config = ED::config();

		$mailfrom = $this->getFromEmail($data);
		$fromname = $this->getFromName($data);

		$emailTo = array();

		if (is_array($toEmails)) {
			foreach ($toEmails as $email) {
				$emailTo[] = $email;
			}
		} else {
			$emailTo[] = $toEmails;
		}

		//load the email template
		$tplBody = '';
		if (!empty($template)) {
			$tplBody = $this->getEmailTemplateContent($template, $data);
		} else {
			$tplBody = $body;
		}

		//send as html or plaintext
		$asHtml	= (bool) $config->get('notify_html_format');

		if (!$asHtml) {
			$tplBody = strip_tags($tplBody);
		}

		//now we process the email sending.
		foreach ($emailTo as $recipient) {
			// Porcess the message and title
			$search = array('{actor}', '{target}');
			$replace = array($fromname, '');

			$emailSubject = JString::str_ireplace($search, $replace, $subject);
			$emailBody = JString::str_ireplace($search, $replace, $tplBody);

			$date = ED::date();
			$mailq = ED::table('MailQueue');

			$mailq->mailfrom = $mailfrom;
			$mailq->fromname = $fromname;
			$mailq->recipient = $recipient;
			$mailq->subject = $emailSubject;
			$mailq->body = $emailBody;
			$mailq->created = $date->toMySQL();
			$mailq->ashtml = $asHtml;
			$mailq->store();
		}

	}

	public function add($from='', $to, $subject = '', $body = '', $template='', $data = array())
	{
		$mainframe = JFactory::getApplication();
		$mailfrom = $mainframe->getCfg( 'mailfrom' );
		$fromname = $mainframe->getCfg( 'fromname' );


		if (!empty($from)) {

			$userFrom = JFactory::getUser($from);

			if ($userFrom->id != 0) {
				$mailfrom = $userFrom->name;
				$fromname = $userFrom->email;
			}
		}


		$userTo	= array();

		if (!is_array($to)) {
			if (strtolower($to) == 'admin') {
				$userTo	= $this->getAdminEmails();

			} else {
				$user = JFactory::getUser($to);
				$userTo[] = $user;
			}
		} else {
			foreach ($to as $ids) {
				if (!empty($ids)) {
					$user = JFactory::getUser($ids);
					$userTo[] = $user;
				}
			}
		}

		//load the email template
		if (!empty($template)) {
			$tplBody = $this->getEmailTemplateContent( $template, $data );
		} else {
			$tplBody = $body;
		}

		//now we process the email sending.
		foreach ($userTo as $recipient) {

			// Process the message and title
			$search = array('{actor}', '{target}');
			$replace = array($fromname, $recipient->getName());

			$emailSubject = JString::str_ireplace($search, $replace, $subject);
			$emailBody = JString::str_ireplace($search, $replace, $tplBody);

			$date = ED::date();
			$mailq = ED::table('MailQueue');

			$mailq->mailfrom = $mailfrom;
			$mailq->fromname = $fromname;
			$mailq->recipient = $recipient->email;
			$mailq->subject = $emailSubject;
			$mailq->body = $emailBody;
			$mailq->created = $date->toMySQL();
			$mailq->store();
		}
	}

	public function getEmailTemplateContent($template, $data)
	{
		$config = ED::config();
		$app = JFactory::getApplication();
		$output = '';

		if (!isset($data['unsubscribeLink'])) {
			$data['unsubscribeLink'] = '';
		}

		$replyBreakText = $config->get('mail_reply_breaker');

		if ($replyBreakText) {
			$replyBreakText = JText::sprintf('COM_EASYDISCUSS_EMAILTEMPLATE_REPLY_BREAK', $replyBreakText);
		}

		// If this uses html, we need to switch the template file
		if ($config->get('notify_html_format')) {
			$template .= '.html';
		}

		$defaultJoomlaTemplate = ED::getCurrentTemplate();
		
		// Set the logo for the generic email template
		$override = JPATH_ROOT . '/templates/' . $defaultJoomlaTemplate . '/html/com_easydiscuss/emails/logo.png';
		$logo = rtrim(JURI::root(), '/') . '/components/com_easydiscuss/themes/wireframe/images/emails/logo.png';

		if (JFile::exists($override)) {
			$logo = rtrim(JURI::root(), '/') . '/templates/' . $defaultJoomlaTemplate . '/html/com_easydiscuss/emails/logo.png';
		}

		$theme = ED::themes();

		foreach ($data as $key => $val) {
			$theme->set($key, $val);
		}

		$contents = $theme->output('site/emails/' . $template, array('emails' => true));
		unset($theme);

		$theme = ED::themes();
		$jConfig = ED::jconfig();

		$theme->set('logo', $logo);

		$theme->set('emailTitle', $config->get('notify_email_title', $jConfig->get('sitename')));
		$theme->set('contents', $contents);
		$theme->set('unsubscribeLink', $data['unsubscribeLink']);
		$theme->set('subscriptionsLink', ED::mailer()->getSubscriptionsManagerLink());
		$theme->set('replyBreakText', $replyBreakText);

		if ($config->get('notify_html_format')) {
			$output = $theme->output('site/emails/email.template.html', array('emails'=> true));
		} else {
			$output = $theme->output('site/emails/email.template.text', array('emails'=> true));
		}

		return $output;
	}

// 	/**
//      * Determines if this notification post is featured
//      *
//      * @since   4.0
//      * @access  public
//      * @param   string
//      * @return
//      */
//     public function isFeatured()
//     {
//         static $items = array();

//         $post = ED::post();

//         $key = $this->post->id;

//         if (!isset($items[$key])) {
//             $items[$key] = $post->featured;
//         }
// var_dump(expression)
//         return $items[$key];
//     }


}
