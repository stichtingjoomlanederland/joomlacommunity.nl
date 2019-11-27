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

ED::import('admin:/tables/table');

class DiscussComment extends EasyDiscussTable
{
	public $id = null;
	public $post_id	= null;
	public $comment = null;
	public $name = null;
	public $title = null;
	public $email = null;
	public $url	= null;
	public $ip = null;
	public $created	= null;
	public $modified = null;
	public $published = null;
	public $ordering = null;
	public $user_id	= null;
	public $parent_id = null;
	public $sent = null;
	public $lft = null;
	public $rgt = null;

	public function __construct(& $db)
	{
		parent::__construct('#__discuss_comments', 'id', $db);
	}

	public function bind($post, $isPost = false)
	{
		parent::bind($post);

		if ($isPost) {

			$date = ED::date();
			jimport('joomla.filter.filterinput');
			$filter	= JFilterInput::getInstance();

			//replace a url to link
			$comment = $filter->clean($post->comment);
			$comment = ED::string()->url2link($comment);

			$this->comment = $comment;
			$this->name = $filter->clean($post->name);
			$this->email = $filter->clean($post->email);
			$this->created = $date->toMySQL();
			$this->modified = $date->toMySQL();
			$this->published = '1';
		}

		return true;
	}

	/**
	 * Determines if the comment can be converted to a discussion reply by the current user.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function canConvert()
	{
		$acl = ED::acl();

		if (!ED::isSiteAdmin()) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if a comment can be deleted
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function canDeleteComment($pk = null, $joins = null)
	{
		$aclHelper = ED::acl();

		// If the user is site admin or able to delete all comments
		if (ED::isSiteAdmin() || $aclHelper->allowed('delete_comment')) {
			return true;
		}

		// If the user is the comment's owner and has permission to delete own comment
		if ($aclHelper->isOwner($this->user_id) && $aclHelper->allowed('delete_own_comment')) {
			return true;
		}

		return false;
	}

	/**
	 * Generates the permalink for the comment
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getPermalink($external = false, $xhtml = true, $anchor = true)
	{
		$post = ED::post($this->post_id);

		$permalink = $post->getPermalink($external, $xhtml, false, false, false);

		if ($anchor) {
			$permalink .= '#comments-' . $this->id;
		}

		return $permalink;
	}

	/**
	 * Generates an array of data for REST api
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function toData()
	{
		$post = ED::post($this->post_id);
		$permalink = $post->getPermalink(true, true, true);

		$data = new stdClass();
		$data->id = $this->id;
		$data->permalink = $permalink;
		$data->comment = $this->comment;
		$data->ip = $this->ip;
		$data->name = $this->name;
		$data->user_id = $this->user_id;

		return $data;
	}

	/**
	 * Performs after comment save operation
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function postSave()
	{
		$config = ED::config();
		// Load the post for this comment
		$post = ED::post($this->post_id);

		$my = JFactory::getUser();

		// Load user profile's object.
		$profile = ED::user($my->id);

		// Try to detect if the comment is posted to the main question or a reply.
		$liveNotificationText   = '';
		$question = ED::table('Post');

		if ($post->parent_id) {
			$question->load($post->parent_id);
			$liveNotificationText = 'COM_EASYDISCUSS_COMMENT_REPLY_NOTIFICATION_TITLE';
		} else {
			$question->load($post->id);
			$liveNotificationText = 'COM_EASYDISCUSS_COMMENT_QUESTION_NOTIFICATION_TITLE';
		}

		if ($this->published && !$question->private) {

			// Create notification item in EasySocial
			ED::easySocial()->notify('new.comment', $post, $question, $this);

			// AUP integrations
			ED::aup()->assign(DISCUSS_POINTS_NEW_COMMENT, $this->user_id, '');

			// jomsocial activity stream
			ED::jomsocial()->addActivityComment($post, $question);

			ED::easysocial()->commentDiscussionStream($this, $post, $question);
		}

		// Add notification to the post owner.
		if ($post->user_id != $my->id && $this->published && $config->get('main_notifications_comments')) {
			$notification = ED::table('Notifications');

			// for the live notification part have to store non-sef
			$commentPermalink = 'index.php?option=com_easydiscuss&view=post&id=' . $question->id . '#comments-' . $this->id;

			$notification->bind( array(
						'title'	=> JText::sprintf($liveNotificationText, $question->title),
						'cid' => $question->id,
						'type' => DISCUSS_NOTIFICATIONS_COMMENT,
						'target' => $post->user_id,
						'author' => $my->id,
						'permalink'	=> $commentPermalink
			));

			$notification->store();
		}

		ED::history()->log('easydiscuss.new.comment', $my->id, JText::_('COM_EASYDISCUSS_BADGES_HISTORY_NEW_COMMENT'), $post->id);
		ED::easySocial()->assignBadge('create.comment', $my->id, JText::_('COM_EASYDISCUSS_BADGES_HISTORY_NEW_COMMENT'));
		ED::badges()->assign('easydiscuss.new.comment', $my->id);
		ED::points()->assign('easydiscuss.new.comment', $my->id, $this);

		// Apply badword filtering for the comment.
		$this->comment = ED::badwords()->filter($this->comment);

		// Process the email data
		$emailData = array();
		$emailData['commentContent'] = $this->comment;
		$emailData['commentAuthor']	= $profile->getName();
		$emailData['commentAuthorAvatar'] = $profile->getAvatar();
		$emailData['postTitle']	= $question->title;
		$emailData['postLink'] = $this->getPermalink(true);

		// This is used when we need to alter the sender information
		$emailData['senderObject'] = $profile;
		$emailData['cat_id'] = $post->category_id;

		// lets determine if we need to include the unsubcribe link here or not.
		$addUnsubscribeLink = false;
		$replyAuthorEmail = $post->user_id ? $post->getOwner()->getEmail() : $post->poster_email;

		if ($post->user_id == $question->user_id && !$question->user_id) {
			// this means both reply author and topic author are guest. Let check based on email.
			if ($post->poster_email != $question->poster_email) {
				$addUnsubscribeLink = true;
			}
		} else if ($post->user_id && $post->user_id != $question->user_id) {
			$addUnsubscribeLink = true;
		}

		// add the unsubcribe link to reply author if the reply author is not the post owner.
		if ($addUnsubscribeLink && $replyAuthorEmail) {
			$subTable = ED::table('Subscribe');
			$subTable->load(array('email'=>$replyAuthorEmail, 'type' => 'post', 'cid' => $question->id));

			// var_dump($subTable->id);

			if ($subTable->id) {
				$hash = base64_encode("type=".$subTable->type."\r\nsid=".$subTable->id."\r\nuid=".$subTable->userid."\r\ntoken=".md5($subTable->id.$subTable->created));
				$emailData['unsubscribeLink'] = EDR::getRoutedURL('index.php?option=com_easydiscuss&controller=subscription&task=unsubscribe&data='.$hash, false, true);
			}
		}

		// var_dump($addUnsubscribeLink, $replyAuthorEmail);
		// exit;

		$subTitle = $question->title;
		if (JString::strlen($question->title) > 100) {
			$subTitle = JString::substr($question->title, 0, 100) . '...';
		}

		$isGroup = $question->cluster_id;

		$notify = ED::notifications();
		
		// If notify all member setting is enabled we no need to check for other setting as this setting have
		// higher predecence.
		if ($config->get('notify_comment_all_members') && !$question->private && !$isGroup) {
			$ignoreEmails = array();
			$myId = JFactory::getUser($my->id);
			$ignoreEmails[] = $myId->email;

			$model = ED::model('Category');

			// action select is the user permission for view discussion
			$allowViewPost = $model->getAssignedGroups( $post->category_id, 'view');

			$guestUserGroupId = JComponentHelper::getParams('com_users')->get('guest_usergroup');

			$includesGuest = true;
			if (!in_array($guestUserGroupId, $allowViewPost)) {
				$includesGuest = false;
			}

			// Generalize the email subject
			$subject = JText::sprintf('COM_EASYDISCUSS_EMAIL_TITLE_NEW_COMMENT', $post->id, $subTitle);
			$notify->sendToAllUsers($subject, $emailData, $ignoreEmails, 'email.post.comment.new', '', $allowViewPost, $includesGuest);
		} else {
			// We should skip it all together if these 2 setting didn't enabled.
			if (!$config->get('notify_comment_participants') && !$config->get('main_subscription_include_comments')) {
				return false;
			}

			// Get the list of emails to be sent
			$emails	= array();

			// Send email to the post owner only if the commenter is not the post owner.
			if ($post->user_id != 0 && $post->user_id != $my->id ) {
				$user = JFactory::getUser($post->user_id);
				$emails[] = $user->email;
			}

			if ($config->get('notify_comment_participants')) {

				// Retrieve the list of user emails from the list of comments made on the post.
				$existingComments = $post->getComments();

				if ($existingComments) {

					foreach ($existingComments as $existingComment) {
						// Only add the email when the user id is not the current logged in user who is posting the comment.
						// It should not send email to the post owner as well since the post owner will already get a notification.
						if ($existingComment->user_id != 0 && $existingComment->user_id != $my->id && $existingComment->user_id != $post->user_id) {
							$user = JFactory::getUser($existingComment->user_id);
							$emails[] = $user->email;
						}
					}
				}
			}

			// notify to site admin and moderator based on the notification setting
			$administratorEmails = ED::mailer()->notifyAdministrators($emailData, array(), $config->get('notify_admin'), $config->get('notify_moderator'), true);

			// if the subscription part enable include comment setting
			if ($config->get('main_subscription_include_comments') && !$question->private) {

				// Now we also need to get the site and category subscribers emails if they chose to include comments notification
				if ($config->get('main_sitesubscription')) {
					$siteSubscribers = ED::Mailer()->getSubscribers('site', 0, $post->category_id, array('emailOnly' => true), array($my->email));
					$emails = array_merge($emails, $siteSubscribers);
				}

				if ($config->get('main_ed_categorysubscription')) {
					$categorySubscribers = ED::Mailer()->getSubscribers('category', $post->category_id, $post->category_id, array('emailOnly' => true), array($my->email));
					$emails = array_merge($emails, $categorySubscribers);
				}

				// We also need to notify to the post subcribers
				if ($config->get('main_postsubscription')) {
					$postSubscribers = ED::Mailer()->getSubscribers('post', $post->id, $post->category_id, array('emailOnly' => true), array($my->email));
					$emails = array_merge($emails, $postSubscribers);
				}
			}

			// Ensure the emails are all unique.
			$emails = array_merge($emails, $administratorEmails);
			$emails = array_unique($emails);

			// Only send email when email is not empty.
			if (!empty($emails)) {
				$notify->addQueue($emails, JText::sprintf('COM_EASYDISCUSS_EMAIL_TITLE_NEW_COMMENT', $post->id, $subTitle) , '', 'email.post.comment.new', $emailData);
			}

			// If the notify_actor is enabled, we directly send the email to the comment poster
			if ($config->get('notify_actor')) {
				$notify->addQueue($profile->getEmail(), JText::sprintf('COM_EASYDISCUSS_EMAIL_TITLE_YOU_ADDED_NEW_COMMENT', $subTitle) , '', 'email.comment.new', $emailData);
			}
		}

		// Process comment triggers.
		if ($config->get('main_content_trigger_comments')) {
			$this->content = $this->comment;

			// process content plugins
			ED::events()->importPlugin('content');
			ED::events()->onContentPrepare('comment', $this);

			$this->event = new stdClass();

			$results = ED::events()->onContentBeforeDisplay('comment', $this);
			$this->event->beforeDisplayContent = trim(implode("\n", $results));

			$results = ED::events()->onContentAfterDisplay('comment', $this);
			$this->event->afterDisplayContent = trim(implode("\n", $results));

			$this->comment = $this->content;
		}
	}
}
