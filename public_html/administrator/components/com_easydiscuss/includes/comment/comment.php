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

class EasyDiscussComment extends EasyDiscuss
{
	private $table = null;
	private $original = null;

	public function __construct($id, $options = array())
	{
		parent::__construct();

		$this->table = ED::table('Comment');

		if (is_numeric($id) || is_string($id)) {
			$this->table->load($id);
		}

		// If passed in argument is already a post, table just assign it.
		if ($id instanceof DiscussComment) {
			$this->table = $id;
		}

		if (is_object($id)) {

			if (!$id instanceof DiscussComment) {
				$this->table->bind($id);
			}
		}

		$this->original = clone($this->table);
	}

	/**
	 * Magic method to get properties which don't exist on this object but on the table
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function __get($key)
	{
		if (isset($this->table->$key)) {
			return $this->table->$key;
		}

		if (isset($this->$key)) {
			return $this->$key;
		}

		return $this->table->$key;
	}

	/**
	 * Magic method to get properties which don't exist on this object but on the table
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function __set($key, $value)
	{
		if (isset($this->table->$key)) {
			$this->table->$key = $value;;
		}

		if (isset($this->$key)) {
			$this->$key = $value;
		}
	}

	/**
	 * Allows caller to bind data to the table
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function bind($data = array(), $applyFilters = false)
	{
		$data = (object) $data;

		$this->table->bind($data, $applyFilters);

		return $this->table;
	}

	/**
	 * Determines if the comment can be converted to a discussion reply by the current user.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function canConvert()
	{
		if (!ED::isSiteAdmin()) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if a comment can be deleted
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function canDelete($pk = null, $joins = null)
	{
		$acl = ED::acl();

		// If the user is site admin or able to delete all comments
		if (ED::isSiteAdmin() || $acl->allowed('delete_comment')) {
			return true;
		}

		// If the user is the comment's owner and has permission to delete own comment
		if ($acl->isOwner($this->user_id) && $acl->allowed('delete_own_comment')) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if a comment can be edited
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function canEdit()
	{
		$acl = ED::acl();

		if (ED::isSiteAdmin() || $acl->allowed('edit_comment')) {
			return true;
		}

		if ($this->user_id == $this->my->id && $acl->allowed('edit_own_comment')) {
			return true;
		}

		return false;
	}

	/**
	 * Deletes the comment
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function delete()
	{
		$state = $this->table->delete();

		// AUP Integrations
		ED::aup()->assign(DISCUSS_POINTS_DELETE_COMMENT, $this->user_id, '');

		return $state;
	}

	/**
	 * Determines if the comment is new
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function isNew()
	{
		$isNew = true;

		if ($this->original->id) {
			$isNew = false;
		}

		return $isNew;
	}

	/**
	 * Determines if the comment is published
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function isPublished()
	{
		return (bool) $this->published;
	}

	/**
	 * Validates the data
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function validate($data = array())
	{
		if (!$this->comment) {
			$this->setError('COM_EASYDISCUSS_COMMENT_IS_EMPTY');

			return false;
		}

		if (!$this->validateAntispam()) {
			return false;
		}

		return true;
	}

	/**
	 * Validates with the necessary antispam
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function validateAntispam()
	{
		if (!$this->honeypot()) {
			return false;
		}

		if (!$this->akismet()) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves the author of the comment
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getAuthor()
	{
		static $authors = array();

		if (!isset($authors[$this->user_id])) {
			$authors[$this->user_id] = ED::profile($this->user_id);
		}

		return $authors[$this->user_id];
	}

	/**
	 * Retrieves the comment message
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getMessage($raw = false, $useCache = true)
	{
		if ($raw) {
			return $this->comment;
		}

		static $messages = array();

		if (!$useCache || !isset($messages[$this->id])) {
			$message = ED::badwords()->filter($this->comment);
			$message = nl2br($message);

			// Temporarily removing this
			// // Process comment triggers.
			// if ($this->config->get('main_content_trigger_comments')) {
			// 	$this->content = $this->comment;

			// 	// process content plugins
			// 	ED::events()->importPlugin('content');
			// 	ED::events()->onContentPrepare('comment', $this);

			// 	$this->event = new stdClass();

			// 	$results = ED::events()->onContentBeforeDisplay('comment', $this);
			// 	$this->event->beforeDisplayContent = trim(implode("\n", $results));

			// 	$results = ED::events()->onContentAfterDisplay('comment', $this);
			// 	$this->event->afterDisplayContent = trim(implode("\n", $results));

			// 	$this->comment = $this->content;
			// }

			$messages[$this->id] = $message;
		}

		return $messages[$this->id];
	}

	/**
	 * Retrieves the duration string since the comment was posted
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getDuration()
	{
		static $cache = [];

		if (!isset($cache[$this->id])) {
			$cache[$this->id] = ED::date()->toLapsed($this->modified);
		}

		return $cache[$this->id];
	}

	/**
	 * Generates the permalink for the comment
	 *
	 * @since	5.0.0
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
	 * Retrieves the discussion associated with the comment
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getPost()
	{
		static $cache = array();

		if (!isset($cache[$this->post_id])) {
			$cache[$this->post_id] = ED::post($this->post_id);
		}

		return $cache[$this->post_id];
	}

	/**
	 * Honeypot validation
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function honeypot()
	{
		// Check for honeypot traps
		$honeypot = ED::honeypot();
		$trapped = $honeypot->isTrapped('comments');

		if ($trapped) {
			return false;
		}

		return true;
	}

	/**
	 * Validate with akismet to see if the comment is a spam
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function akismet()
	{
		if (!$this->config->get('antispam_akismet') || !$this->config->get('antispam_akismet_key')) {
			return true;
		}

		// Check for akismet
		require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/akismet/akismet.php');

		// Run through akismet screening if necessary.
		if ($this->config->get('antispam_akismet') && ($this->config->get('antispam_akismet_key'))) {
			$akismetData = array(
				'author' => $this->name,
				'email' => $this->email,
				'website' => DISCUSS_JURIROOT,
				'body' => $this->comment,
				'alias' => ''
			);

			$akismet = new Akismet(DISCUSS_JURIROOT, $this->config->get('antispam_akismet_key'), $akismetData);

			if ($akismet->isSpam()) {
				$this->setError('COM_EASYDISCUSS_AKISMET_SPAM_DETECTED');
				return false;
			}
		}

		return true;
	}

	/**
	 * Saves the comment
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function save()
	{
		$state = $this->table->store();

		// Process items only when it is a new comment
		if ($this->isNew()) {

			$this->postSave();

			$tnc = ED::tnc();
			$tnc->storeTnc('comment');
		}

		return $state;
	}

	/**
	 * Occurs after saving to process other misc stuffs
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function postSave()
	{
		$post = $this->getPost();
		$question = $post;

		// Author of the comment
		$profile = $this->getAuthor();

		// Try to detect if the comment is posted to the main question or a reply.
		$liveNotificationText = 'COM_EASYDISCUSS_COMMENT_QUESTION_NOTIFICATION_TITLE';

		if ($post->parent_id) {
			$question = ED::post($post->parent_id);
			$liveNotificationText = 'COM_EASYDISCUSS_COMMENT_REPLY_NOTIFICATION_TITLE';
		}

		// Notify users when a comment is posted
		if ($this->isPublished() && !$question->private) {
			ED::easySocial()->notify('new.comment', $post, $question, $this);

			ED::aup()->assign(DISCUSS_POINTS_NEW_COMMENT, $this->user_id, '');

			ED::jomsocial()->addActivityComment($post, $question);

			ED::easysocial()->commentDiscussionStream($this, $post, $question);
		}

		// Add notification to the post owner.
		if ($post->user_id != $this->my->id && $this->isPublished() && $this->config->get('main_notifications')) {
			$notification = ED::table('Notifications');

			// for the live notification part have to store non-sef
			$commentPermalink = 'index.php?option=com_easydiscuss&view=post&id=' . $question->id . '#comments-' . $this->id;

			$notification->bind(array(
						'title'	=> JText::sprintf($liveNotificationText, $question->title),
						'cid' => $question->id,
						'type' => DISCUSS_NOTIFICATIONS_COMMENT,
						'target' => $post->user_id,
						'author' => $this->my->id,
						'permalink'	=> $commentPermalink
			));

			$notification->store();
		}

		ED::history()->log('easydiscuss.new.comment', $this->my->id, JText::_('COM_EASYDISCUSS_BADGES_HISTORY_NEW_COMMENT'), $post->id);
		ED::easySocial()->assignBadge('create.comment', $this->my->id, JText::_('COM_EASYDISCUSS_BADGES_HISTORY_NEW_COMMENT'));
		ED::badges()->assign('easydiscuss.new.comment', $this->my->id);
		ED::points()->assign('easydiscuss.new.comment', $this->my->id, $this);

		// Process the email data
		$emailData = array();
		$emailData['commentContent'] = $this->getMessage();
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

			if ($subTable->id) {
				$hash = base64_encode("type=".$subTable->type."\r\nsid=".$subTable->id."\r\nuid=".$subTable->userid."\r\ntoken=".md5($subTable->id.$subTable->created));
				$emailData['unsubscribeLink'] = EDR::getRoutedURL('index.php?option=com_easydiscuss&controller=subscription&task=unsubscribe&data='.$hash, false, true);
			}
		}


		$subTitle = $question->title;

		if (EDJString::strlen($question->title) > 100) {
			$subTitle = EDJString::substr($question->title, 0, 100) . '...';
		}

		$isGroup = $question->cluster_id;

		$notify = ED::notifications();
		
		// If notify all member setting is enabled we no need to check for other setting as this setting have
		// higher predecence.
		if ($this->config->get('notify_comment_all_members') && !$question->private && !$isGroup) {
			$ignoreEmails = array(JFactory::getUser($this->my->id)->email);

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
			if (!$this->config->get('notify_comment_participants') && !$this->config->get('main_subscription_include_comments')) {
				return false;
			}

			// Get the list of emails to be sent
			$emails	= array();

			// Send email to the post owner only if the commenter is not the post owner.
			if ($post->user_id != 0 && $post->user_id != $this->my->id ) {
				$user = JFactory::getUser($post->user_id);
				$emails[] = $user->email;
			}

			if ($this->config->get('notify_comment_participants')) {

				// Retrieve the list of user emails from the list of comments made on the post.
				$existingComments = $post->getComments();

				if ($existingComments) {

					foreach ($existingComments as $existingComment) {
						// Only add the email when the user id is not the current logged in user who is posting the comment.
						// It should not send email to the post owner as well since the post owner will already get a notification.
						if ($existingComment->user_id != 0 && $existingComment->user_id != $this->my->id && $existingComment->user_id != $post->user_id) {
							$user = JFactory::getUser($existingComment->user_id);
							$emails[] = $user->email;
						}
					}
				}
			}

			// notify to site admin and moderator based on the notification setting
			$administratorEmails = ED::mailer()->notifyAdministrators($emailData, array(), $this->config->get('notify_admin'), $this->config->get('notify_moderator'), true);

			// if the subscription part enable include comment setting
			if ($this->config->get('main_subscription_include_comments') && !$question->private) {

				// Now we also need to get the site and category subscribers emails if they chose to include comments notification
				if ($this->config->get('main_sitesubscription')) {
					$siteSubscribers = ED::Mailer()->getSubscribers('site', 0, $post->category_id, array('emailOnly' => true), array($this->my->email));
					$emails = array_merge($emails, $siteSubscribers);
				}

				if ($this->config->get('main_ed_categorysubscription')) {
					$categorySubscribers = ED::Mailer()->getSubscribers('category', $post->category_id, $post->category_id, array('emailOnly' => true), array($this->my->email));
					$emails = array_merge($emails, $categorySubscribers);
				}

				// We also need to notify to the post subcribers
				if ($this->config->get('main_postsubscription')) {
					$postSubscribers = ED::Mailer()->getSubscribers('post', $post->id, $post->category_id, array('emailOnly' => true), array($this->my->email));
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
			if ($this->config->get('notify_actor')) {
				$notify->addQueue($profile->getEmail(), JText::sprintf('COM_EASYDISCUSS_EMAIL_TITLE_YOU_ADDED_NEW_COMMENT', $subTitle) , '', 'email.comment.new', $emailData);
			}
		}
	}
}
