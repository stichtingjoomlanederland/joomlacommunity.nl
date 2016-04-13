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

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
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
	 * @param	string
	 * @return
	 */
	public function canConvert()
	{
		$acl = ED::acl();

		if (!ED::isSiteAdmin()) {
			return false;
		}

		return true;
	}

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
     * Performs after comment save operation
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
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
			$notification->bind( array(
						'title'	=> JText::sprintf( $liveNotificationText , $question->title ),
						'cid' => $question->id,
						'type' => DISCUSS_NOTIFICATIONS_COMMENT,
						'target' => $post->user_id,
						'author' => $my->id,
						'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $question->id
			));

			$notification->store();
		}

		// Try to assign badge and points to the current user.

		// Add logging for user.
		ED::history()->log('easydiscuss.new.comment', $my->id, JText::_('COM_EASYDISCUSS_BADGES_HISTORY_NEW_COMMENT'), $post->id);

		// Assign badge for EasySocial
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
		$emailData['postLink'] = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $question->id, false, true);

		// Get the list of emails to be sent
		$emails	= array();

		// Send email to the post owner only if the commenter is not the post owner.
		if ($post->user_id != 0 && $post->id != $my->id ) {
			$user = JFactory::getUser($post->user_id);
			$emails[] = $user->email;
		}

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

		// Ensure the emails are all unique.
		$emails = array_unique($emails);

		// Only send email when email is not empty.
		if (!empty($emails)) {
			$notify	= ED::getNotification();
			$notify->addQueue($emails, JText::sprintf('COM_EASYDISCUSS_EMAIL_TITLE_NEW_COMMENT', JString::substr($question->content, 0, 15)) . '...' , '', 'email.post.comment.new', $emailData);
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
