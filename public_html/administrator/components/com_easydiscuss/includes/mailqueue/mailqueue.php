<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussMailQueue extends EasyDiscuss
{
	/**
	 * Processes e-mails from the queue and dispatch them out
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	public function sendOnPageLoad()
	{
		$db = ED::db();
		$config	= ED::config();
		$max = (int) $config->get('main_mailqueuenumber');

		$query  = 'SELECT `id` FROM `#__discuss_mailq` WHERE `status` = 0';
		$query  .= ' ORDER BY `created` ASC';
		$query  .= ' LIMIT ' . $max;

		$db->setQuery($query);

		$result = $db->loadObjectList();

		// Get the reply to
		$replyToEmail = $config->get('notification_sender_email', ED::jconfig()->getValue('mailfrom'));
		$replyToName = $config->get('notification_sender_name', ED::jconfig()->getValue('fromname'));

		// Standard arguments
		$cc = null;
		$bcc = null;
		$attachment = null;

		if (!empty($result)) {

			foreach($result as $mail) {

				$mailq = ED::table('MailQueue');
				$mailq->load($mail->id);

				$mail = JFactory::getMailer();
				$state = $mail->sendMail($mailq->mailfrom, $mailq->fromname, $mailq->recipient, $mailq->subject, $mailq->getBody(), $mailq->ashtml, $cc, $bcc, $attachment, $replyToEmail, $replyToName);
				
				// update the status to 1 == proccessed
				if ($state) {
					$mailq->status = 1;
				}

				$mailq->store();
			}
		}
	}

	/**
	 * Fetch e-mails on the remote server
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	public function parseEmails()
	{
		$mailbox = ED::mailbox();

		// Try to connect to the mail server
		$state = $mailbox->connect($this->config->get('main_email_parser_username'), $this->config->get('main_email_parser_password'));

		if ($state) {
			self::processEmails($mailbox);
		}

		// Category email parser
		$model = ED::model('Categories');
		$cats = $model->getAllCategories();

		if (is_array($cats)) {
			foreach ($cats as $cat) {

				$category = ED::category($cat->id);

				$enable = explode( ',' , $category->getParam( 'cat_email_parser_switch') );

				if ($enable[0]) {
					$catMail = explode( ',' , $category->getParam('cat_email_parser'));
					$catPass = explode( ',' , $category->getParam('cat_email_parser_password'));


					$mailbox = ED::Mailbox();
					$state = $mailbox->connect($catMail[0], $catPass[0]);

					if ($state) {
						self::processEmails($mailbox, $category);
					}
				}

			}
		}

		return true;
	}

	/**
	 * Retrieve the sender details
	 *
	 * @since   4.0.13
	 * @access  public
	 */
	public function getSenderEmail($data)
	{
		$from = '';

		if (isset($data->from)) {
			$info = $data->from[0];

			if (!empty($info->mailbox) && !empty($info->host)) {
				$from = $info->mailbox . '@' . $info->host;
			}
		}

		if (!$from) {
			$from = $data->fromemail;
		}

		return $from;
	}
	
	/**
	 * Process emails from the mailbox
	 *
	 * @since	4.0.13
	 * @access	public
	 */
	private function processEmails($mailer = '', $category = '')
	{
		// Bind file attachments
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.utilities.utility');

		$searchCriteria = 'UNSEEN';

		// Only search for messages that are new.
		$unread	= $mailer->searchMessages($searchCriteria);

		// If there is no unread emails, just skip this altogether
		if (!$unread) {
			echo JText::_('COM_EASYDISCUSS_NO_EMAILS_TO_PARSE');
			return false;
		}

		// retrieve the current fetch email limit
		$limit = $this->config->get('main_email_parser_limit', 10);
		sort($unread);

		// Specifies the length of the returned array
		$unread = array_slice($unread, 0, $limit);

		$config = $this->config;
		$acl = $this->acl;
		$filter = JFilterInput::getInstance();
		$total = 0;

		$replyBreaker = $this->config->get('mail_reply_breaker');

		foreach ($unread as $sequence) {

			// Get the message info
			$info = $mailer->getMessageInfo($sequence);
			$from = $info->from;

			// Get the sender's e-mail address
			$senderEmail = $this->getSenderEmail($info);

			// Ensure that the sender is whitelisted
			if (!$this->isSenderAllowed($senderEmail)) {
				continue;
			}

			$senderName = 'Unknown';

			if (isset($info->from)) {
				$from = $info->from;

				if (isset($from[0]->personal)) {
					$senderName = $from[0]->personal;
				} else if (isset($from[0]->mailbox)) {
					$senderName = $from[0]->mailbox;
				}
			}

			// Get the subject of the email and clean it to avoid any unclose html tags
			$subject = '';
			$matches = array();

			if (isset($info->subject) && $info->subject) {
				$subject = $info->subject;

				$subject = $filter->clean($subject);
				
				preg_match('/\[\#(.*)\]/is', $subject, $matches);
			}

			$isReply = !empty($matches);
			$message = ED::MailerMessage($mailer->stream, $sequence);

			// Load up the post object
			$post = ED::post();

			$data = array();

			// Get the html output
			$html = $message->getHTML();

			// Default allowed html codes
			$allowed = '<img>,<a>,<br>,<table>,<tbody>,<th>,<tr>,<td>,<div>,<span>,<p>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<b>,<i>,<u>';

			// Remove disallowed tags
			$html = strip_tags($html, $allowed);

			// Remove img tags because we do not support email embeded images
			$pattern = array();
			$pattern[] = '/<img.*?src=["|\'](.*?)["|\'].*?\>/ims';
			$html = preg_replace( $pattern, array( '' ), $html );

			$editor = $config->get('layout_editor');
			$contentType = $editor == 'bbcode' ? 'bbcode' : 'html';

			if ($editor == 'bbcode') {

				// remove &nbsp; from content if there is any
				$html = EDJString::str_ireplace('&nbsp;', ' ', $html);

				// Switch html back to bbcode
				$html = ED::parser()->html2bbcode($html);

				// Update the quote messages
				$html = ED::parser()->quoteBbcode($html);

				//since the editor is a bbcode, we should not allow any html tags.
				$html = strip_tags($html);
			}
			
			if ($this->config->get('main_email_parser_appendemail')) {
				$newline = $editor == 'bbcode' ? "\r\n\r\n" : "<br /><br />";

				$html .= $newline . $senderEmail;
			}

			// Insert default subject if emails do not contain title
			if (empty($subject)) {
				$subject = JText::_('COM_EASYDISCUSS_EMAIL_NO_SUBJECT');
			}

			$data['content'] = $html;
			$data['content_type'] = $contentType;
			$data['title'] = $subject;
			$data['alias'] = ED::getAlias($subject, 'post');
			$data['published'] = DISCUSS_ID_PUBLISHED;
			$data['created'] = ED::date()->toSql();
			$data['replied'] = ED::date()->toSql();
			$data['modified'] = ED::date()->toSql();


			// If this is a reply, and the site isn't configured to parse replies, skip this
			if ($isReply && !$config->get('main_email_parser_replies')) {
				continue;
			}

			//add this for category email parser
			if (!empty($category)) {
				$data['category_id'] = $category->id;

			} else {
				// By default, set the category to the one pre-configured at the back end.
				$data['category_id'] = $config->get('main_email_parser_category');
			}

			if ($isReply) {
				$parentId = (int) $matches[1];
				$data['parent_id'] = $parentId;

				// Trim content, get text before the defined line
				if( $replyBreaker ) {
					if( $pos = EDJString::strpos($data['content'], $replyBreaker) ) {
						$data['content'] = EDJString::substr($data['content'], 0, $pos);
					}
				}

				// Since this is a reply, we need to determine the correct category for it based on the parent discussion.
				$parent = ED::table('Post');
				$parent->load($parentId);

				$data['category_id'] = $parent->category_id;
			}

			// Lookup for the user based on their email address.
			$user = ED::getUserByEmail($senderEmail);

			if (($user instanceof JUser) && $this->config->get('main_email_parser_mapuser')) {
				$data['user_id'] = $user->id;
				$data['user_type'] = DISCUSS_POSTER_MEMBER;
			} else {
				// Guest posts
				$data['user_type'] = DISCUSS_POSTER_GUEST;
				$data['poster_name'] = $senderName;
				$data['poster_email'] = $senderEmail;
			}

			// check if guest can post question or not. if not skip the processing.
			if ($data['user_type'] == DISCUSS_POSTER_GUEST) {
				$acl = ED::acl();
				$model = ED::model('Category');
				// action select is the user permission for create discussion
				$allow = $model->getAssignedGroups( $data['category_id'], 'select');
				$guestGroupId = JComponentHelper::getParams('com_users')->get('guest_usergroup');

				$isNotAllow = false;
				if (!in_array($guestGroupId, $allow)) {
					$isNotAllow = true;
				}

				if (!$acl->allowed('add_question') || $isNotAllow) {
					continue;
				}
			}

			// If the system is configured to moderate all emails, then we should update the state accordingly
			if ($config->get('main_email_parser_moderation')) {
				$data['published'] = DISCUSS_ID_PENDING;
			}

			// Indicate that this post is imported from email parser
			$data['params_fromEmailParser'] = true;

			// bind the data
			$post->bind($data);

			$saveOptions = array('ignorePreSave' => true);
			if ($config->get('main_email_parser_moderation')) {
				$saveOptions['forceModerate'] = true;
			}

			$post->save($saveOptions);

			// Log an acitivty for it
			if (!$post->isReply()) {
				$actLib = ED::activity();

				$tmpl = $actLib->getTemplate();
				$tmpl->setAction('post.email.parser');
				$tmpl->setType('post', $post->id);
				$tmpl->setActor($post->getAuthor()->id);
				$tmpl->setContent(0, 1);

				$actLib->log($tmpl);
			}

			// @task: Increment the count.
			$total += 1;

			$attachments = array();
			$attachments = $message->getAttachment();
			$totalAttachments = 0;

			if ($attachments) {

				$tmp_dir = JPATH_ROOT . '/' . 'tmp' . '/';
				$allowed = explode( ',', $config->get('main_attachment_extension'));

				foreach ($attachments as $file) {

					if (strpos($file['name'], '/') !== FALSE) {
						$file['name'] = substr($file['name'], strrpos($file['name'],'/')+1 );

					} elseif(strpos($file['name'], '\\' !== FALSE)) {
						$file['name'] = substr($file['name'], strrpos($file['name'],'\\')+1 );
					}

					// @task: check if the attachment has file extension. ( assuming is images )
					$imgExts = array('jpg', 'png', 'gif', 'JPG', 'PNG', 'GIF', 'jpeg', 'JPEG', 'pdf', 'PDF');
					$imageSegment = explode('.', $file['name']);

					// Detect the extension of the file
					$extension  = JFile::getExt( $file['name'] );

					if (! in_array($imageSegment[ count( $imageSegment ) - 1 ], $imgExts) && (!isset($extension) || !$extension)) {
						$file['name'] = $file['name'] . '.jpg';
					}

					$maxSize	= (double) $config->get( 'attachment_maxsize' ) * 1024 * 1024;

					// Skip empty data's.
					if (!isset($extension) || !$extension || !in_array(strtolower($extension), $allowed)) {
						echo 'Invalid extension.';
						continue;
					}

					// store into tmp folder 1st
					$file['tmp_name']	= $tmp_dir . $file['name'];
					JFile::write( $file['tmp_name'], $file['data']);

					// Check the mime contains the attachment type, if not we insert our own
					$mime = $file['mime'];
					$imgExts = array('jpg', 'png', 'gif', 'JPG', 'PNG', 'GIF', 'jpeg', 'JPEG');

					if (in_array($mime, $imgExts)) {
						$mime = 'image/' . $mime;
					} else {
						$mime = 'application/' . $mime;
					}

					$file['type'] = $mime;
					$file['error'] = '';

					// Upload an attachment
					$attachment = ED::attachment();
					$attachment->upload($post, $file);

					$totalAttachments++;
				}
			}

			// We need to update the thread's num_attachments again is because the thread has been created and stored before the attachments from the email being uploaded if not it will always be 0. #811
			if ($totalAttachments) {
				
				$threadId = 0;
				$table = ED::table('Post');
				$table->load($post->id);

				if ($table->id) {
					$threadId = $table->thread_id;
				}

				$thread = ED::table('Thread');
				$thread->load($threadId);

				if ($thread->id) {
					$thread->num_attachments = $totalAttachments;
					$thread->store();
				}
			}

			// all done. now mark this email as 'read'
			$mailer->markAsRead($mailer, $sequence);

			echo JText::sprintf('COM_EASYDISCUSS_EMAIL_PARSED' , $total);
		}
	}

	/**
	 * Ensure that the sender is allowed
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function isSenderAllowed($sender)
	{
		// Check if there are any blacklisted e-mails
		if ($this->isBlacklisted($sender)) {
			return false;
		}

		// filter email according to the whitelist
		if ($this->isWhitelisted($sender)) {
			return true;
		}


		return true;
	}

	/**
	 * Determines if an e-mail address is blacklisted
	 *
	 * @since	4.1.15
	 * @access	public
	 */
	private function isBlacklisted($email)
	{
		$list = $this->config->get('main_email_parser_sender_blacklist');
		$emails = $this->getEmailsFromList($list);

		if (!$emails) {
			return false;
		}

		if (in_array($email, $emails)) {
			$this->setError(JText::sprintf('COM_ED_MAILBOX_BLACKLISTED', $email));
			return true;
		}

		return false;
	}

	/**
	 * Determines if an e-mail address is blacklisted
	 *
	 * @since	4.1.15
	 * @access	public
	 */
	private function isWhitelisted($email)
	{
		$list = $this->config->get('main_email_parser_sender_whitelist');
		$emails = $this->getEmailsFromList($list);

		if (!$emails) {
			return true;
		}

		if (!in_array($email, $emails)) {
			$this->setError(JText::sprintf('COM_ED_MAILBOX_NOT_WHITELISTED', $email));
			return false;
		}

		return true;
	}

	/**
	 * Given the list of e-mails, clean up the content
	 *
	 * @since	4.1.15
	 * @access	public
	 */
	private function getEmailsFromList($list)
	{
		$filter = JFilterInput::getInstance();

		$list = $filter->clean($list, 'string');
		$list = EDJString::trim($list);

		if (!$list) {
			return array();
		}

		$pattern = '([\w\.\-]+\@(?:[a-z0-9\.\-]+\.)+(?:[a-z0-9\-]{2,4}))';

		preg_match_all($pattern, $list, $matches);
		$emails = @$matches[0];

		return $emails;
	}
}
