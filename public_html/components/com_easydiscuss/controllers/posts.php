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

class EasyDiscussControllerPosts extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		// Register task aliases
		$this->registerTask('unfeature', 'feature');
	}

	/**
	 * This occurs when the user tries to create a new discussion or edits an existing discussion
	 *
	 * @since   4.0.14
	 * @access  public
	 */
	public function save()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the id if available
		$id = $this->input->get('id', 0, 'int');

		// Get the date POST
		$data = $this->input->getArray('post');

		// Load the post library
		$post = ED::post($id);

		$isNew = $post->isNew();

		// Get the redirect URL
		$redirectUrl = EDR::_('view=ask', false);

		if (!$isNew) {
			$redirectUrl = EDR::_('view=ask&id=' . $post->id, false);
		}

		// Check the permissions to post a new question
		if (!$post->canPostNewDiscussion()) {
			ED::setMessage($post->getError(), 'error');
			return ED::redirect(EDR::_('', false));
		}

		// If this post is being edited, check for perssion if the user is able to edit or not.
		if ($post->id && !$post->canEdit()) {
			ED::setMessage($post->getError(), 'error');
			return ED::redirect(EDR::_('view=post&id='.$id, false));
		}

		// For contents, we need to get the raw data.
		$data['content'] = $this->input->get('dc_content', '', 'raw');

		// If cluster id is provided, we need to ensure that the cluster is really exists.
		if (isset($data['cluster_id']) && $data['cluster_id']) {
			$lib = ED::easysocial();
			$group = $lib->loadGroup($data['cluster_id']);

			// For now there is only one cluster type which is group. We hard coded it for now.
			$data['cluster_type'] = 'group';

			// If group is not exists, we save it normally.
			if (!$group) {
				$data['cluster_id'] = 0;
				$data['cluster_type'] = '';
			}
		}

		// normalize the post form password prevent for the browser auto-fill issue. 
		$data['password'] = $this->input->get('protection-password', '', 'default');

		// Bind the posted data
		// we need to indicate this bind is for creation / update
		$post->bind($data, false, false, true);

		// Validate the posted data to ensure that we can really proceed
		if (!$post->validate($data)) {

			$files = $this->input->files->get('filedata', array(), 'raw');
			$data['attachments'] = $files;

			ED::storeSession($data, 'NEW_POST_TOKEN');
			ED::setMessage($post->getError(), 'error');

			return ED::redirect(EDR::getAskRoute($post->getCategory()->id, false));
		}

		// Save
		// Need to check all the error and make sure it is standardized
		if (!$post->save()) {
			ED::setMessage($post->getError(), 'error');
			return ED::redirect(EDR::getAskRoute($redirectUrl, false));
		}

		// Process T&C
		$tnc = ED::tnc();
		$tnc->storeTnc('question');

		$message = ($isNew)? JText::_('COM_EASYDISCUSS_POST_STORED') : JText::_('COM_EASYDISCUSS_EDIT_SUCCESS');
		$state = 'success';

		// Let's set our custom message here.
		if ($post->isPending()){
			$message = JText::_('COM_EASYDISCUSS_NOTICE_POST_SUBMITTED_UNDER_MODERATION');
			$state = 'info';
		}

		ED::setMessage($message, $state);

		$redirect = $this->input->get('redirect', '', 'default');

		if (!empty($redirect)) {
			$redirect = base64_decode($redirect);

			// Decode any special characters to the correct url format, eg: &amp; to &
			$redirect = htmlspecialchars_decode($redirect);

			return ED::redirect(JRoute::_($redirect));
		}

		$redirect = EDR::getPostRoute($post->id, false);
		$redirectionOption = $this->config->get('main_post_redirection');

		if ($post->isPending()) {
			$redirect = EDR::_('view=index', false);
		}

		if ($redirectionOption == 'home') {
			$redirect = EDR::_('view=index', false);
		}

		if ($redirectionOption == 'mainCategory') {
			$redirect = EDR::_('view=categories', false);
		}

		if ($redirectionOption == 'currentCategory') {
			$redirect = EDR::getCategoryRoute($post->category_id, false);
		}

		if ($redirectionOption == 'myPosts') {
			$redirect = EDR::_('view=mypost', false);
		}

		return ED::redirect($redirect);
	}

	/**
	 * Branches a reply as a question
	 *
	 * @since   1.0
	 * @access  public
	 */
	public function branch()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the id of the reply.
		$id = $this->input->get('id', 0, 'int');

		$post = ED::post($id);

		if (!$id || !$post->id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_REPLY_ID_PROVIDED'), 'error');
			return ED::redirect(EDR::_('index.php?option=com_easydiscuss', false));
		}

		// Check the permission is it allowed
		if (!$post->canBranch()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_BRANCHING_NOT_ALLOWED'), 'error');
			return ED::redirect(EDR::getPostRoute($post->parent_id, false));
		}


		$state = $post->branch();


		$model = ED::model('Posts');

		// Update attachments
		$model->updateAttachments($post->id, 'questions');

		ED::setMessage(JText::_('COM_EASYDISCUSS_REPLY_BRANCHED_OUT_SUCCESSFULLY'), 'success');

		$redirect = EDR::getPostRoute($post->id, false);
		return ED::redirect($redirect);
	}

	/**
	 * Merges discussion.
	 *
	 * @since   1.0
	 * @access  public
	 */
	public function merge()
	{
		// Check for request forgeries
		ED::checkToken();

		$newParent = $this->input->get('id', 0, 'int');
		$currentParent = $this->input->get('current', 0, 'int');

		// check if require data present or not.
		if (!$newParent || !$currentParent) {
			ED::setMessage(JText::_('COM_ED_MERGE_INVALID_DATA'), 'error');
			return ED::redirect(EDR::_('view=index', false));
		}

		// check if currently logged user has the ability to merge the post or not.
		$post = ED::post($currentParent);
		if (!$post->canMove()) {
			ED::setMessage(JText::_('COM_ED_MERGE_NOT_ALLOWED'), 'error');
			return ED::redirect(EDR::getPostRoute($post->id, false));
		}

		$newPost = ED::table('Post');
		$newPost->load($newParent);

		// Update the current parent and change it's parent to the new parent.
		$post = ED::table('Post');
		$post->load($currentParent);

		// We need to get the old thread id so we can update the thread accordingly.
		$threadId = $post->thread_id;

		$post->parent_id = $newParent;
		$post->thread_id = $newPost->thread_id;

		// Update the posts.
		if (!$post->store()) {
			ED::setMessage(JText::sprintf('COM_EASYDISCUSS_MERGE_ERROR', $newPost->title), 'error');
			return ED::redirect(EDR::getPostRoute($newParent, false));
		}

		// Update all the child items from this parent to the new parent.
		$model = ED::model('Posts');
		$model->updateNewParent($currentParent, $newParent, $newPost->thread_id);

		// Update attachments
		$model->updateAttachments($post->id, 'replies');

		// Delete discussion from thread table
		$model->deleteThread($threadId);

		// Update new parent reply count
		$parentPost = ED::post($currentParent);
		$parentPost->updateReplyCount();

		// Set proper message in mail queue.
		ED::setMessage(JText::sprintf('COM_EASYDISCUSS_MERGE_SUCCESS', $newPost->title), 'success');
		return ED::redirect(EDR::getPostRoute($newParent, false));
	}

	/**
	 * Ban user.
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function banUser()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the post id from the request.
		$id = $this->input->get('postid', 0 ,'int');

		// Get the content from the form
		$message = $this->input->get('reporttext', '', 'string');

		// Get the post user name from the request
		$getBannedName = $this->input->get('postName', '', 'string');

		// Load the new post object
		$post = ED::post($id);
		$redirect = EDR::getPostRoute($post->id, false);

		// check ban user from question or reply for the redirection
		if (!$post->isQuestion()) {
			// Load the new post object for question
			$question = $post->getParent();
			$redirect = EDR::getPostRoute($question->id, false);
		}

		// load the category
		$category = ED::category($post->category_id);

		// check current post user can ban or not and
		if (!$post->canBanAuthor()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_BAN_FAILED'), 'error');
			return ED::redirect($redirect);
		}

		// if the user kena banned already, cant ban again.
		$model = ED::model('bans');
		$options = array('ip' => $post->ip, 'userId' => $post->user_id);

		if ($model->isBanned($options)) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_BAN_IN_THE_LIST_ALREADY'), 'error');
			return ED::redirect($redirect);
		}

		// get the ban duration time
		$duration = $this->input->get('duration', 0, 'int');

		// get the post owner id which you want to ban to
		$targetId = $post->getOwner()->id;

		// get the post owner ip
		$targetIp = $post->ip;

		// Check current date/time

		$date = JFactory::getDate();

		//calculate and convert to milisecond
		$mili = $date->toUnix() + ($duration * 60 * 60);
		// var_dump($duration, JFactory::getDate($date->toUnix() + $duration*60*60));exit;
		// var_dump($date->toUnix());exit;

		//Convert back to date object
		$end = JFactory::getDate($mili);
		$ban = ED::table('Ban');

		//store data
		$ban->userid = $post->user_id;
		$ban->banned_username = $getBannedName;
		$ban->ip = $targetIp;
		$ban->blocked = '1';
		$ban->created_by = $this->my->id;
		$ban->start = $date->toSQL();
		$ban->end = $end->toSQL();
		$ban->reason = $message;
		$ban->store();

		ED::setMessage(JText::_('COM_EASYDISCUSS_BAN_SUCCESSFULLY'), 'success');
		return ED::redirect($redirect);
	}

	/**
	 * Use to move a post to a new category.
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function move()
	{
		// Check for request forgeries
		ED::checkToken();

		$id = $this->input->get('id', 0, 'int');
		$categoryId = $this->input->get('category_id', 0, 'int');

		// Load the new post object
		$post = ED::post($id);

		// Load the category.
		$newCategory = ED::category($categoryId);

		// Get the current category
		$category = $post->getCategory();

		// Check if the params given is correct.
		if (!$id || !$post->id || !$categoryId || !$newCategory->id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_ID_PROVIDED'), 'error');
			return ED::redirect(EDR::_('index.php?option=com_easydiscuss', false));
		}

		// Check the permission is it allowed
		if (!$post->canMove()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'), 'error');
			return ED::redirect(EDR::getPostRoute($post->id, false));
		}

		// Move the post now
		$post->move($newCategory->id);

		ED::setMessage('COM_EASYDISCUSS_POST_MOVED_SUCCESSFULLY', 'success');
		return ED::redirect(EDR::getPostRoute($post->id, false));
	}

	/**
	 * Method to validate the password supplied by the user.
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function setPassword()
	{
		$id = $this->input->get('id', 0, 'int');
		$type = $this->input->get('type', '');

		// Since return URLs are base64 encoded, we need to decode it back again.
		$return = EDR::_('index.php?option=com_easydiscuss', false);

		// if the user trying to access the post view, redirect them to back to that view
		if ($type == 'content') {
			$return = EDR::_('index.php?option=com_easydiscuss&view=post&id=' . $id, false);
		}

		if (!$id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_ID_PROVIDED'), 'error');
			return ED::redirect($return);
		}

		// Get password from the request.
		$password = $this->input->get('discusspassword', '', 'var');

		// If password is empty, we should throw some errors here.
		if (!$password) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_PASSWORD_PROVIDED'), 'error');
			return ED::redirect($return);
		}

		// Set the password that the user posted into the session's name space.
		$session = JFactory::getSession();
		$session->set('DISCUSSPASSWORD_' . $id, $password, 'com_easydiscuss');

		$post = ED::post($id);

		// Verify that the password matches
		if ($post->password != $password) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_PASSWORD_PROVIDED'), 'error');
			return ED::redirect($return);
		}

		// If the user supplied the correct password, we want to redirect them to the correct page.
		$return = $this->input->get('return', '', 'var');
		$return = base64_decode($return);
		$return = EDR::_($return, false);

		// If the user passes here, then the page should be visible to the user.
		return ED::redirect($return);
	}

	/**
	 * Accepts a post as an answer
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function accept()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the discussion id.
		$id = $this->input->get('postid', 0, 'int');

		// If id isn't provided, we need to disallow the user.
		if (!$id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'), 'error');
			return ED::redirect(EDR::_('index.php?option=com_easydiscuss', false));
		}

		// Load the new post object for reply
		$reply = ED::post($id);

		// Load the new post object for question
		$question = $reply->getParent();

		if (!$reply->canAcceptAsAnswer()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'), 'error');
			return ED::redirect(EDR::getPostRoute($question->id, false));
		}

		// Set the reply as answer
		$state = $reply->setAsAnswer();

		if (!$state) {
			return $this->ajax->reject($post->getError());
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_REPLY_ACCEPTED_AS_ANSWER'), 'success');
		return ED::redirect(EDR::getPostRoute($question->id, false));
	}

	/**
	 * Rejects a post as an answer
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function reject()
	{
		// Check for request forgeries
		ED::checkToken();

		$id = $this->input->get('id', 0, 'int');

		if (!$id){
			ED::setMessage(JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'), 'error');
			return ED::redirect(EDR::_('index.php?option=com_easydiscuss', false));
		}

		// Load the new post object for reply
		$reply = ED::post($id);

		// Load the new post object for question
		$question = $reply->getParent();
		$redirect = $question->getPermalink(false, false);

		if (!$reply->canAcceptAsAnswer()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'), 'error');
			return ED::redirect($redirect);
		}

		// Update the reply state
		$state = $reply->rejectAnswer();

		if (!$state) {
			return $this->ajax->reject($post->getError());
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_REPLY_REJECTED_AS_ANSWER'), 'success');
		return ED::redirect($redirect);
	}


	/**
	 * Allows anyone to approve replies provided that they get the correct key
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function approvePost()
	{
		$key = $this->input->get('key', '', 'var');
		$redirect = EDR::_('index.php?option=com_easydiscuss&view=index', false);

		if (empty($key)) {
			return ED::redirect($redirect, JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'), 'error');
		}

		$hashkey = ED::table('HashKeys');
		$state = $hashkey->load(array('key' => $key));

		if (!$state) {
			return ED::redirect($redirect, JText::_('COM_ED_NOT_ALLOWED_ITEM_ALREADY_APPROVED'), 'error');
		}

		$post = ED::post($hashkey->uid);
		$state = $post->publish(1);

		// Delete the unused hashkey now.
		$hashkey->delete();

		$message = $hashkey->type == DISCUSS_REPLY_TYPE ? JText::_('COM_EASYDISCUSS_MODERATE_REPLY_PUBLISHED') : JText::_('COM_EASYDISCUSS_MODERATE_POST_PUBLISHED');
		$pid = $hashkey->type == DISCUSS_REPLY_TYPE ? $post->parent_id : $post->id;

		ED::setMessage($message, 'success');

		return ED::redirect(EDR::_('index.php?option=com_easydiscuss&view=post&id=' . $pid, false));
	}

	/**
	 * Allows anyone to reject replies provided that they get the correct key
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function rejectPost()
	{
		$key = $this->input->get('key', '', 'var');
		$redirect = EDR::_('index.php?option=com_easydiscuss&view=index', false);

		if (empty($key)) {
			return ED::redirect($redirect, JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'), 'error');
		}

		$hashkey = ED::table('HashKeys');
		$state = $hashkey->load(array('key' => $key));

		if (!$state) {
			return ED::redirect($redirect, JText::_('COM_ED_NOT_ALLOWED_ITEM_ALREADY_APPROVED'), 'error');
		}

		$post = ED::post($hashkey->uid);
		$state = $post->publish(0, true);

		// Delete the unused hashkey now.
		$hashkey->delete();

		$message = $hashkey->type == DISCUSS_REPLY_TYPE ? JText::_('COM_EASYDISCUSS_MODERATE_REPLY_UNPUBLISHED') : JText::_('COM_EASYDISCUSS_MODERATE_POST_UNPUBLISHED');
		$pid = $hashkey->type == DISCUSS_REPLY_TYPE ? $post->parent_id : $post->id;

		ED::setMessage($message, 'success');

		return ED::redirect(EDR::_('index.php?option=com_easydiscuss&view=index', false));
	}

	/**
	 * Allows caller to delete a post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function delete()
	{
		// Check for request forgeries
		ED::checkToken();

		$id = $this->input->get('id', 0, 'int');

		// Initialize the default redirection
		$return = $this->input->get('return', '', 'var');
		$redirect = EDR::_('view=index', false);

		if ($return) {
			$redirect = base64_decode($return);
		}

		// Load up the post
		$post = ED::post($id);

		if (!$id || !$post->id) {
			ED::setMessage('COM_EASYDISCUSS_ENTRY_DELETE_MISSING_ID', 'error');
			return ED::redirect($redirect);
		}

		// If return url is not provided, we need to figure this out on our own
		if (!$return) {
			if ($post->isReply()) {
				$redirect = EDR::_('view=post&id=' . $post->parent_id, false);
			}
		}

		// Check if the user really can delete
		if (!$post->canDelete()) {
			ED::setMessage('COM_EASYDISCUSS_ENTRY_DELETE_NO_PERMISSION', 'error');
			return ED::redirect($redirect);
		}

		// Do not allow user to delete a locked post
		if ($post->isLocked() && !ED::isSiteAdmin()) {
			ED::setMessage('COM_EASYDISCUSS_ENTRY_DELETE_LOCKED', 'error');
			return ED::redirect($redirect);
		}

		// Try to delete the post now
		$post->delete();

		ED::setMessage('COM_EASYDISCUSS_POST_DELETED_SUCCESS', 'success');

		return ED::redirect($redirect);
	}


	function _fieldValidate($post)
	{
		$valid = true;
		$message = '<ul class="unstyled">';

		if (!isset($post['title']) || EDJString::strlen($post['title']) == 0 || $post['title'] == JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE')) {
			$message .= '<li>' . JText::_('COM_EASYDISCUSS_POST_TITLE_CANNOT_EMPTY') . '</li>';
			$valid = false;
		}

		// quick_question_reply_content is from the module quick question
		if ((!isset($post['dc_reply_content']) || (EDJString::strlen($post['dc_reply_content']) == 0)) && (!isset($post['quick_question_reply_content']) || EDJString::strlen($post['quick_question_reply_content']) == 0)){
			$message .= '<li>' . JText::_('COM_EASYDISCUSS_POST_CONTENT_IS_EMPTY') . '</li>';
			$valid = false;
		}

		if (EDJString::strlen($post['dc_reply_content']) < $this->config('main_post_min_length')) {
			$message .= '<li>' . JText::sprintf('COM_EASYDISCUSS_POST_CONTENT_LENGTH_IS_INVALID', $this->config('main_post_min_length')) . '</li>';
			$valid = false;
		}

		if (empty($post['category_id'])) {
			$message .= '<li>' . JText::_('COM_EASYDISCUSS_POST_CATEGORY_IS_EMPTY') . '</li>';
			$valid = false;
		}

		if (empty($this->my->id)) {

			if(empty($post['poster_name'])) {
				$message .= '<li>' . JText::_('COM_EASYDISCUSS_POST_NAME_IS_EMPTY') . '</li>';
				$valid = false;
			}

			if (empty($post['poster_email'])) {
				$message .= '<li>' . JText::_('COM_EASYDISCUSS_POST_EMAIL_IS_EMPTY') . '</li>';
				$valid = false;

			} else {

				if (!ED::string()->isValidEmail($post['poster_email'])) {
					$message .= '<li>' . JText::_('COM_EASYDISCUSS_POST_EMAIL_IS_INVALID') . '</li>';
					$valid = false;
				}
			}
		}

		$message .= '</ul>';

		if (!$valid) {
			ED::setMessage($message, 'error');
		}

		return $valid;
	}

	/**
	 * Allows caller to feature a discussion
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function feature()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the post
		$id = $this->input->get('id', 0, 'int');
		$post = ED::post($id);

		// Default redirection
		$redirect = EDR::_('view=post&id=' . $id, false);

		if (!$post->id || !$id) {
			ED::setMessage('COM_EASYDISCUSS_INVALID_POST_ID', 'error');
			return ED::redirect($redirect);
		}

		// Ensure that the user is allowed to feature this post
		if (!$post->canFeature()) {
			ED::setMessage('COM_EASYDISCUSS_NO_PERMISSION_TO_PERFORM_THE_REQUESTED_ACTION', 'error');

			return ED::redirect($redirect);
		}

		// Get the current task
		$task = $this->getTask() == 'feature' ? 'feature' : 'unfeature';

		// Run the task
		$post->$task();

		$message = 'COM_EASYDISCUSS_FEATURE_POST_IS_FEATURED';

		if ($task == 'unfeature') {
			$message = 'COM_EASYDISCUSS_FEATURE_POST_IS_UNFEATURED';
		}

		ED::setMessage($message, 'success');

		return ED::redirect($redirect);
	}

	/**
	 * Saves an edited reply if the site is configured to use a WYSIWYG editor
	 *
	 * @since   3.2
	 * @access  public
	 */
	public function saveReply()
	{
		// Check for request forgeries
		ED::checkToken();

		$data = $this->input->getArray('post');
		$data['content'] = $this->input->get('dc_content', '', 'raw');

		// Check if the post data is valid
		if (!isset($data['id']) || !$data['id']) {
			ED::setMessage( JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'), 'error');
			return ED::redirect(EDR::_('index.php?option=com_easydiscuss&view=index', false));
		}

		$post = ED::post($data['id']);

		if (!$post->canEdit()) {
			ED::setMessage( JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'), 'error');
			return ED::redirect($threadUrl);
		}

		$threadUrl = EDR::_('index.php?option=com_easydiscuss&view=post&id=' . $post->parent_id, false);

		// bind data
		$post->bind($data);


		// other checking
		$valid = $post->validate($data, 'replying');


		if ($valid === false) {
			ED::setMessage($post->getError(), 'error');
			return ED::redirect(EDR::_('index.php?option=com_easydiscuss&view=post&layout=edit&id=' . $post->id, false));
		}

		// update the post
		$state = $post->save();

		// Try to store the post now
		if (!$state) {
			ED::setMessage( JText::_('COM_EASYDISCUSS_ERROR'), 'error');
			return ED::redirect($threadUrl);
		}

		$message = JText::_('COM_EASYDISCUSS_REPLY_SUCCESSFULLY_UPDATED');
		$state = 'success';

		// Let's set our custom message here.
		ED::setMessage($message, $state);

		$redirect = $this->input->get('redirect', '', 'var');

		if (!empty($redirect)) {
			$redirect = base64_decode($redirect);
			return ED::redirect($redirect);
		}

		ED::redirect($threadUrl);
		return;
	}

	/**
	 * Method to directly approve the post without reviewing the post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function approvePendingPost()
	{
		$postId = $this->input->get('postId', 0, 'int');

		if (!$postId) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			return $this->ajax->reject($message);         
		}

		$post = ED::post($postId);
		$post->publish(1);

		if (!$post->canModerate()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'), 'error');
			return ED::redirect(EDR::_('view=index', false));
		}

		// COM_EASYDISCUSS_MODERATE_REPLY_PUBLISHED
		$message = JText::_('COM_EASYDISCUSS_MODERATE_POST_PUBLISHED');
		$redirect = EDR::_('index.php?option=com_easydiscuss&view=dashboard');

		// Different message for reply
		if ($post->isReply()) {
			$message = JText::_('COM_EASYDISCUSS_MODERATE_REPLY_PUBLISHED');
		}

		// Let's set our custom message here.
		ED::setMessage($message, 'success');
		return ED::redirect($redirect);
	}

	/**
	 * Reject Pending Post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function rejectPendingPost()
	{
		$postId = $this->input->get('postId', 0, 'int');
		$view = $this->input->get('view', 'index', 'cmd');

		if (!$postId) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			return $this->ajax->reject($message);
		}

		$post = ED::post($postId);
		$post->publish(0, true);

		if (!$post->canModerate()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'), 'error');
			return ED::redirect(EDR::_('view=index', false));
		}

		$message = JText::_('COM_EASYDISCUSS_POST_REJECT');
		$redirect = 'index.php?option=com_easydiscuss&view=' . $view;

		if ($post->isReply()) {
			if ($view == 'post') {
				$redirect .= '&id=' . $post->getParent()->id;
			}

			$message = JText::_('COM_EASYDISCUSS_MODERATE_REPLY_UNPUBLISHED');
		}

		$redirect = EDR::_($redirect);

		// Let's set our custom message here.
		ED::setMessage($message, 'success');
		return ED::redirect($redirect);
	}

	/**
	 * Method to approve and edit pending post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function approvePending()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the id if available
		$id = $this->input->get('id', 0, 'int');

		// Get the date POST
		$data = $this->input->post->getArray();

		// For contents, we need to get the raw data.
		$data['content'] = $this->input->get('dc_content', '', 'raw');

		// Load the post library
		$post = ED::post($id);

		if (!$post->canModerate()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'), 'error');
			return ED::redirect(EDR::_('view=index', false));
		}

		// Bind post data
		$post->bind($data);

		// Validate the posted data to ensure that we can really proceed
		if (!$post->validate($data)) {

			$redirectUrl = 'view=ask&id=' . $post->id;

			ED::setMessage($post->getError(), 'error');
			return ED::redirect($redirectUrl);
		}

		// Reset previous publish state to moderated before publishing. #168
		$post->post->published = DISCUSS_ID_PENDING;

		// Toggle publish state
		$post->publish(1);

		$message = JText::_('COM_EASYDISCUSS_MODERATE_POST_PUBLISHED');
		ED::setMessage($message, DISCUSS_QUEUE_SUCCESS);

		$redirect = 'index.php?option=com_easydiscuss';

		$acl = ED::acl();
		if ($acl->allowed('manage_pending') || ED::isSiteAdmin()) {
			$redirect = 'index.php?option=com_easydiscuss&view=dashboard';	
		}

		ED::redirect($redirect);
	}

	/**
	 * Mark a post as resolve
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function resolve()
	{
		// Get the post id
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'), 'error');
			return ED::redirect(EDR::_('index.php?option=com_easydiscuss', false));
		}

		// load the post lib
		$post = ED::post($id);

		// Ensure that the user can really resolve this
		if (!$post->canResolve()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'), 'error');
			return ED::redirect(EDR::getPostRoute($post->id, false));
		}

		// Try to resolve it now
		$state = $post->markResolved();

		if (!$state) {
			return $this->ajax->reject($post->getError());
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_ENTRY_RESOLVED'), 'success');
		return ED::redirect(EDR::getPostRoute($post->id, false));		
	}

	/**
	 * Mark a post as unresolve
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function unresolve()
	{
		// Get the post id
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'), 'error');
			return ED::redirect(EDR::_('index.php?option=com_easydiscuss', false));
		}

		// load the post lib
		$post = ED::post($id);

		// Ensure that the user can really resolve this
		if (!$post->canResolve()) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'), 'error');
			return ED::redirect(EDR::getPostRoute($post->id, false));
		}

		// Try to resolve it now
		$state = $post->markUnresolve();

		if (!$state) {
			return $this->ajax->reject($post->getError());
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_ENTRY_UNRESOLVED'), 'success');
		return ED::redirect(EDR::getPostRoute($post->id, false));		
	}	
}
