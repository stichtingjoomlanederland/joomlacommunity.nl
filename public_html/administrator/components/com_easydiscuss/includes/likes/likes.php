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

jimport('joomla.utilities.date');

class EasyDiscussLikes extends EasyDiscuss
{
	/**
	 * Unlikes a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unlike(EasyDiscussPost $post)
	{
		// If the post is not published, we shouldn't allow them to like
		if (!$post->isPublished()) {
			return false;
		}


		// If the user previously did not like the post, they shouldn't be able to unlike the post
		$liked = $post->isLikedBy($this->my->id);

		if (!$liked) {
			return false;
		}

		// If this is an unlike request, we need to remove it.
		$this->removeLikes($post->id, $this->my->id);

		if ($post->user_id != $this->my->id) {
			if ($post->isQuestion()) {
				// Remove unlike
				ED::history()->removeLog('easydiscuss.like.discussion', $this->my->id, $post->id);

				ED::badges()->assign('easydiscuss.unlike.discussion', $this->my->id);
				ED::points()->assign('easydiscuss.unlike.discussion', $this->my->id);

				// Deduct points from post owner
				ED::history()->log('easydiscuss.discussion.unlike', $post->user_id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_DISCUSSION_UNLIKE', $post->title), $post->id);

				ED::badges()->assign('easydiscuss.discussion.unlike', $post->user_id);
				ED::points()->assign('easydiscuss.discussion.unlike', $post->user_id);				
			}

			if ($post->isReply()) {
				// Remove unlike
				ED::history()->removeLog('easydiscuss.like.reply', $this->my->id, $post->id);

				ED::badges()->assign('easydiscuss.unlike.reply', $this->my->id);
				ED::points()->assign('easydiscuss.unlike.reply', $this->my->id);

				// Deduct points from reply owner
				ED::history()->log('easydiscuss.reply.unlike', $post->user_id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_REPLY_UNLIKE', $post->title), $post->id);

				ED::badges()->assign('easydiscuss.reply.unlike', $post->user_id);
				ED::points()->assign('easydiscuss.reply.unlike', $post->user_id);
			}
		}

		// Get the like's text.
		$text = $this->html($post->id, $this->my->id, 'post');

		if (!$text) {
			$text = JText::_('COM_EASYDISCUSS_BE_THE_FIRST_TO_LIKE');
		}

		return $text;
	}

	/**
	 * Likes a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function like(EasyDiscussPost $post)
	{
		// If the user previously liked the item, they shouldn't be able to like this again.
		$liked = $post->isLikedBy($this->my->id);

		if ($liked) {
			return false;
		}

		// If the post is not published, we shouldn't allow them to like
		if (!$post->isPublished()) {
			return false;
		}

		// Add the likes
		$this->addLikes($post->id, 'post', $this->my->id);

		// Try get the reply parent id
		$question = ED::post($post->id);
		$question = $question->getParent();

		// Add activity in jomsocial and easysocial
		if ($post->user_id != $this->my->id) {

			// Add integrations when a post is liked for questions
			if ($post->isQuestion()) {
				// Add logging for user.
				ED::history()->log('easydiscuss.like.discussion', $this->my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_LIKE_DISCUSSION', $post->title), $post->id);

				ED::badges()->assign( 'easydiscuss.like.discussion' , $this->my->id);
				ED::points()->assign( 'easydiscuss.like.discussion' , $this->my->id);

				// Assign badge for EasySocial
				ED::easysocial()->assignBadge('like.question', $this->my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_LIKE_DISCUSSION', $post->title));

				// Add points to post author
				ED::history()->log('easydiscuss.discussion.like', $post->user_id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_DISCUSSION_LIKE', $post->title), $post->id);

				ED::badges()->assign( 'easydiscuss.discussion.like' , $post->user_id);
				ED::points()->assign( 'easydiscuss.discussion.like' , $post->user_id);

				ED::easysocial()->assignBadge('discussion.like', $post->user_id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_LIKE_DISCUSSION', $post->title));
			}

			// Add integrations when a post is liked for replies
			if ($post->isReply()) {
				// Add logging for user.
				ED::history()->log('easydiscuss.like.reply', $this->my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_LIKE_REPLY', $post->title), $post->id);

				ED::badges()->assign('easydiscuss.like.reply', $this->my->id);
				ED::points()->assign('easydiscuss.like.reply', $this->my->id);

				ED::easysocial()->assignBadge('discussion.like', $this->my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_LIKE_DISCUSSION', $post->title));

				// Add points to reply author
				ED::history()->log('easydiscuss.reply.like', $post->user_id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_REPLY_LIKE', $post->title), $post->id);

				ED::badges()->assign( 'easydiscuss.reply.like' , $post->user_id);
				ED::points()->assign( 'easydiscuss.reply.like' , $post->user_id);

				ED::easysocial()->assignBadge('reply.like', $post->user_id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_REPLY_LIKE', $post->title));
			}

			ED::easysocial()->notify('new.likes', $post, $question);
		}

		// Add likes activity
		ED::jomsocial()->addActivityLikes($post, $question);

		// Only generated stream if user likes a question.
		if ($post->isQuestion()) {
			ED::easysocial()->likesStream($post, $question);
		}

		// Notify post owner
		$this->notifyPostOwner($post);

		// Get the like's text.
		$text = $this->html($post->id, $this->my->id, 'post');

		if (!$text) {
			$text = JText::_('COM_EASYDISCUSS_BE_THE_FIRST_TO_LIKE');
		}

		return $text;
	}

	/**
	 * Generates the like button
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function button(EasyDiscussPost $post)
	{
		$button = new stdClass();

		// If the post cannot be liked, we should not display anything
		if (!$post->canLike()) {
			return;
		}

		// Load likes lib
		$button->authors = $this->html($post->id, $this->my->id, 'post');

		// Get the total like from the post
		$model = ED::model('Likes');
		$button->total = $model->getTotalLikes($post->id);

		// If there are no authors, we need to set a default value
		if (!$button->authors) {
			$button->authors = JText::_('COM_EASYDISCUSS_BE_THE_FIRST_TO_LIKE');
		}

		// By default, we treat all users as never liked the post before
		$liked = false;

		// Determine if the user liked this post before
		if ($this->my->id) {
			$liked = $post->isLikedBy($this->my->id);
		}

		if ($this->my->id && !$button->authors) {
			$button->message = 'COM_EASYDISCUSS_LIKE_THIS_POST';
			$button->label = 'COM_EASYDISCUSS_LIKES';

			if ($liked) {
				$button->message = 'COM_EASYDISCUSS_UNLIKE_THIS_POST';
				$button->label = 'COM_EASYDISCUSS_UNLIKE';
			}
		}

		$theme = ED::themes();
		$theme->set('button', $button);
		$theme->set('liked', $liked);
		$theme->set('post', $post);

		$output = $theme->output('site/likes/button');

		return $output;
	}

	/**
	 * Notifies the post owner when someone likes his post
	 *
	 * @since	4.1.7
	 * @access	private
	 */
	private function likeNotify($post)
	{
		if (!$this->config->get('main_notifications_liked')) {
			return;
		}

		$question = $post->isQuestion() ? $post : $post->getParent();

		// Add notifications to the post owner.
		$notification = ED::table('Notifications');
		$text = $post->isQuestion() ? 'COM_EASYDISCUSS_LIKE_DISCUSSION_NOTIFICATION_TITLE' : 'COM_EASYDISCUSS_LIKE_REPLY_NOTIFICATION_TITLE';
		$title = $question->title;
		$likeType = $post->isQuestion() ? DISCUSS_NOTIFICATIONS_LIKES_DISCUSSION : DISCUSS_NOTIFICATIONS_LIKES_REPLIES;

		$permalink = 'index.php?option=com_easydiscuss&view=post&id=' . $question->id;

		if (!$post->isQuestion()) {
			$permalink = 'index.php?option=com_easydiscuss&' . $post->getReplyPermalink();
		}

		$notification->bind(array(
			'title'	=> JText::sprintf($text, $title),
			'cid' => $question->id,
			'type' => $likeType,
			'target' => $post->user_id,
			'author' => $this->my->id,
			'permalink'	=> $permalink
		));

		$notification->store();
	}

	/**
	 * Notifies the post owner when someone likes his post
	 *
	 * @since	4.0
	 * @access	private
	 */
	private function notifyPostOwner(EasyDiscussPost $post)
	{
		// Do not process notification if post owner like their own post.
		if ($post->user_id == $this->my->id) {
			return;
		}

		$this->likeNotify($post);

		if (!$this->config->get('notify_owner_like')) {
			return;
		}

		$question = $post->isQuestion() ? $post : $post->getParent();

		// Add email notification to the post owner.
		$notify	= ED::getNotification();
		$profile = ED::user($this->my->id);
		$title = $question->title;

		$permalink = 'index.php?option=com_easydiscuss&view=post&id=' . $question->id;

		if (!$post->isQuestion()) {
			$permalink = 'index.php?option=com_easydiscuss&' . $post->getReplyPermalink();
		}

		$emailText = $post->isQuestion() ? 'POST' : 'REPLY';
		$emailSubject = JText::sprintf('COM_EASYDISCUSS_USER_LIKED_YOUR_' . $emailText, $profile->getName(), $title);
		$emailTemplate = 'email.like.post';

		$emailData = array();
		$emailData['emailContent'] = JText::sprintf('COM_EASYDISCUSS_EMAIL_TEMPLATE_LIKES_' . $emailText, $profile->getName(), $title);
		$emailData['replyContent'] = $post->content;
		$emailData['postLink'] = EDR::getRoutedURL($permalink, false, true);

		$recipient = JFactory::getUser($post->user_id);

		$notify->addQueue($recipient->email, $emailSubject, '', $emailTemplate, $emailData);		
	}

	public static function addLikes($contentId, $type, $userId = null)
	{
		if (is_null($userId)) {
			$userId	= JFactory::getUser()->id;
		}

		$date = ED::date();
		$likes = ED::table('Likes');

		$params	= array();
		$params['type']	= $type;
		$params['content_id'] = $contentId;
		$params['created_by'] = $userId;
		$params['created'] = $date->toSql();

		$likes->bind($params);

		// Check if the user already likes or not. if yes, then return the id.
		$id	= $likes->likeExists();

		if ($id !== false) {
			return $id;
		}

		$likes->store();

		// We need to update the like count in post table
		if ($type == 'post') {
			$model = ED::model('Likes');
			$model->updatePostLikes($contentId);
		}

		return $likes->id;
	}

	public static function removeLikes($postId, $userId, $type = DISCUSS_ENTITY_TYPE_POST)
	{
		$likes = ED::table('Likes');
		$likes->loadByPost($postId, $userId);

		if ($likes->type == 'post') {

			// Update post likes by decreasing the count value.
			$model = ED::model('Likes');
			$model->updatePostLikes($postId, false);
		}

		return $likes->delete();
	}

	/**
	 * Retrieve likes from a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getLikes($contentId, $userId = null, $type = DISCUSS_ENTITY_TYPE_POST, $preloadedObj = null)
	{
		static $loaded = array();

		if (is_null($userId)) {
			$userId = ED::user()->id;
		}

		if (is_null($preloadedObj)) {
			$model = ED::model('Likes');
			$lists = $model->getPostLikes($contentId, $type);
		} else {
			$lists = $preloadedObj;
		}

		if (count($lists) <= 0) {
			return '';
		}

		$user = array();
		foreach ($lists as $key => $list) {
			$user[] = ED::user($list->user_id);
		}

		return $user;
	}

	public static function html($contentId, $userId = null, $type = DISCUSS_ENTITY_TYPE_POST, $preloadedObj = null)
	{
		static $loaded = array();

		if (is_null($userId)) {
			$userId = JFactory::getUser()->id;
		}

		if (is_null($preloadedObj)) {

			// Load up the likes model
			$model = ED::model('Likes');
			$list = $model->getPostLikes($contentId, $type);
		} else {
			$list = $preloadedObj;
		}

		if (count($list) <= 0) {
			return '';
		}

		$names = array();

		for ($i = 0; $i < count($list); $i++) {

			if ($list[$i]->user_id == $userId) {
				array_unshift($names, JText::_('COM_EASYDISCUSS_YOU'));
			} else {
				$user = ED::user($list[$i]->user_id);
				$names[] = '<a href="' . $user->getLink() . '">' . $list[$i]->displayname . '</a>';
			}
		}

		// Maximum names to be display
		$max = 3;
		$total = count($names);
		$break = 0;

		if ($total == 1) {
			$break = $total;

		} else {

			if ($max >= $total) {
				$break = $total - 1;

			} else if($max < $total) {
				$break = $max;
			}
		}

		$main = array_slice($names, 0, $break);
		$remain	= array_slice($names, $break);

		$stringFront = implode(", ", $main);
		$returnString = '';

		if (count($remain) > 1) {
			$returnString = JText::sprintf('COM_EASYDISCUSS_AND_OTHERS_LIKE_THIS', $stringFront, count($remain));

		} else if(count($remain) == 1) {
			$returnString = JText::sprintf('COM_EASYDISCUSS_AND_LIKE_THIS', $stringFront, $remain[0]);

		} else {

			if ($list[0]->user_id == $userId) {
				$returnString = JText::sprintf('COM_EASYDISCUSS_LIKE_THIS', $stringFront);
			} else {
				$returnString = JText::sprintf('COM_EASYDISCUSS_LIKES_THIS', $stringFront);
			}
		}

		return $returnString;
	}
}
