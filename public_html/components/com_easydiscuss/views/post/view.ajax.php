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

class EasyDiscussViewPost extends EasyDiscussView
{
	protected $err  = null;

	/**
	 * Displays a dialog to allow users to move the post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function move()
	{
		$id = $this->input->get('id', 0, 'int');
		$post = ED::post($id);

		if (!$post->id || !$id) {
			echo JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID');
			return $this->ajax->send();
		}

		// Ensure that the user really can move the post
		if (!$post->canMove()) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
		}

		// Get list of categories.
		$categories = ED::populateCategories('', '', 'select', 'category_id', false, true, true, true, true, 'o-form-select');

		$theme = ED::themes();
		$theme->set('categories', $categories);
		$theme->set('id', $id);

		$contents = $theme->output('site/post/dialogs/move');

		return $this->ajax->resolve($contents);
	}
	
	/**
	 * Displays a confirmation to delete a post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function confirmDelete()
	{
		$id = $this->input->get('id', 0, 'int');
		$post = ED::post($id);

		if (!$id || !$post->id) {
			return $this->ajax->reject();
		}

		if (!$post->canDelete()) {
			return $this->ajax->reject();
		}

		// Get the return url
		$return = EDR::_('view=index', false);

		if ($post->isReply()) {
			$return = EDR::_('view=post&id=' . $post->parent_id, false);
		}


		$theme = ED::themes();
		$theme->set('post', $post);
		$theme->set('id', $id);
		$theme->set('return', base64_encode($return));

		$contents = $theme->output('site/post/dialogs/delete');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Displays a confirmation dialog to accept a reply item as an answer.
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function confirmAccept()
	{
		$id = $this->input->get('id', 0, 'int');

		$post = ED::post($id);

		$theme = ED::themes();
		$theme->set('id', $id);

		if ($post->isQuestion()) {
			$contents = $theme->output('site/post/dialogs/mark.post.resolve');
		} else {
			$contents = $theme->output('site/post/dialogs/accept.answer');
		}

		return $this->ajax->resolve($contents);
	}

	/**
	 * Displays a confirmation dialog to reject a reply item as an answer.
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function confirmReject()
	{
		$id = $this->input->get('id', 0, 'int');

		$post = ED::post($id);

		$theme = ED::themes();
		$theme->set('id', $id);

		if ($post->isQuestion()) {
			$contents = $theme->output('site/post/dialogs/mark.post.unresolve');
		} else {
			$contents = $theme->output('site/post/dialogs/reject.answer');
		}

		return $this->ajax->resolve($contents);
	}

	/**
	 * Display confirmation dialog to approve a moderated post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function confirmApprovePending()
	{
		$id = $this->input->get('id', 0, 'int');

		$theme = ED::themes();
		$theme->set('id', $id);
		$contents = $theme->output('site/post/dialogs/approve.post');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Display confirmation dialog to reject a moderated post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function confirmRejectPending()
	{
		$id = $this->input->get('id', 0, 'int');

		$theme = ED::themes();
		$theme->set('id', $id);
		$contents = $theme->output('site/post/dialogs/reject.post');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Allows caller to lock a post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function lock()
	{
		// Get the post id from the request
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'));
		}

		// load the post lib
		$post = ED::post($id);

		// Check if the current user is allowed to lock this post
		if (!$post->canLock()) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
		}

		// Because we know that the user can lock the post now.
		$state = $post->lock();

		if (!$state) {
			return $this->ajax->reject($post->getError());
		}

		return $this->ajax->resolve(JText::_('COM_EASYDISCUSS_POST_LOCKED'));
	}

	/**
	 * Allows caller to unlock a post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function unlock()
	{
		// Get the post id from the request
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'));
		}

		// load the post lib
		$post = ED::post($id);

		if (!$post->canLock()) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
		}

		$state = $post->unlock();

		if (!$state) {
			return $this->ajax->reject($post->getError());
		}

		return $this->ajax->resolve(JText::_('COM_EASYDISCUSS_POST_UNLOCKED'));
	}

	/**
	 * Allows caller to mark a post as resolved
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function resolve()
	{
		// Get the post id
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'));
		}

		// load the post lib
		$post = ED::post($id);

		// Ensure that the user can really resolve this
		if (!$post->canResolve()) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
		}

		// Try to resolve it now
		$state = $post->markResolved();

		if (!$state) {
			return $this->ajax->reject($post->getError());
		}

		return $this->ajax->resolve(JText::_('COM_EASYDISCUSS_ENTRY_RESOLVED'));
	}

	/**
	 * Allows caller to unresolve a post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function unresolve()
	{
		// Get the post id
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'));
		}

		// load the post lib
		$post = ED::post($id);

		// Ensure that the user can really resolve this
		if (!$post->canResolve()) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
		}

		//update isresolve flag
		$state = $post->markUnresolve();

		if (!$state) {
			return $this->ajax->reject($post->getError());
		}

		return $this->ajax->resolve(JText::_('COM_EASYDISCUSS_ENTRY_UNRESOLVED'));
	}

	/**
	 * Get the correct alias to display after update an alias
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function getAlias()
	{
		// Get the post id
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'));
		}

		$alias = $this->input->get('alias', '', 'string');

		$alias = ED::badwords()->filter($alias);
		$alias = ED::getAlias($alias, 'post', $id);

		return $this->ajax->resolve($alias);
	}

	/**
	 * This is triggered when user tries to edit their reply
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function update()
	{
		// Get the post id
		$id = $this->input->get('id', 0, 'int');

		// Get the reply seq currently being edited
		$seq = $this->input->get('seq', 0, 'int');

		$fromAnswer = $this->input->get('fromAnswer', false, 'bool');

		// Get the posted data
		$data = $this->input->post->getArray();

		// For contents, we need to get the raw data.
		$data['content'] = $this->input->get('dc_content', '', 'raw');

		$post = ED::post($id);
		$post->bind($data, false, false, true);

		// Check if it is valid
		$valid = $post->validate($data);

		// if one of the validate is not pass through
		if ($valid === false) {
			$output['message'] = $post->getError();
			$output['type'] = 'error';

			echo $this->showJsonContents($output);
			return false;
		}

		// Try to save the post now
		$state = $post->save();

		// Save the reply
		if (!$state) {
			$output['message'] = $post->getError();
			$output['type'] = 'error';

			echo $this->showJsonContents($output);
			return false;
		}

		// We need the composer for editing purposes
		$opts = array('editing', $post);
		$composer = ED::composer($opts);

		// Get the post's parent
		$question = $post->getParent();
		$questionCategory = $question->getCategory();

		// Prepare the reply permalink
		$post->permalink = EDR::getReplyRoute($question->id, $post->id);
		$post->seq = $seq;

		$post->comments = [];

		if ($this->config->get('main_commentpost')) {
			$commentLimit = $this->config->get('main_comment_pagination') ? $this->config->get('main_comment_pagination_count') : null;
			$post->comments = $post->getComments($commentLimit);

			// get post comments count
			$post->commentsCount = $post->getTotalComments();
		}

		// Get the output so we can append the reply into the list of replies
		$namespace = 'site/post/replies/item';

		$poll = $post->getPoll();

		$theme = ED::themes();
		$theme->set('composer', $composer);
		$theme->set('post', $post);
		$theme->set('poll', $poll);

		if ($post->isAnswer() && $fromAnswer) {
			$theme->set('fromAnswer', true);
		}

		$html = $theme->output($namespace);

		// Prepare the result object
		$output = array();
		$output['message'] = JText::_('COM_ED_EDIT_REPLY_SUCCESS');
		$output['type'] = 'success.edit';
		$output['html'] = $html;
		$output['id'] = $post->id;

		echo $this->showJsonContents($output);
		exit;
	}

	/**
	 * Allows caller to submit a new reply to a discussion
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function reply()
	{
		// Process when a new reply is made from bbcode / wysiwyg editor
		$data = $this->input->post->getArray();
		$output = array();

		// For contents, we need to get the raw data.
		$data['content'] = $this->input->get('dc_content', '', 'raw');

		$parentId = $this->input->get('parent_id', 0, 'int');

		// Load the post library
		$post = ED::post();
		$post->bind($data, false, false, true);

		if ($parentId) {
			$threadPost = ED::post($parentId);

			if (!$threadPost->canReply() || ($threadPost->isLocked() && !ED::isModerator($post->category_id))) {
				$output['message'] = JText::_('COM_ED_REPLY_NOT_ALLOWED');
				$output['type'] = 'error';

				echo $this->showJsonContents($output);
				return false;
			}
		}

		// check the reply validate is it pass or not
		$valid = $post->validate($data, 'replying');

		// if one of the validate is not pass through
		if ($valid === false) {
			$output['message'] = $post->getError();
			$output['type'] = 'error';

			echo $this->showJsonContents($output);
			return false;
		}

		// Try to save the post now
		$state = $post->save();

		// Save the reply
		if (!$state) {
			$output['message'] = $post->getError();
			$output['type'] = 'error';

			echo $this->showJsonContents($output);
			return false;
		}

		// Process T&C
		$tnc = ED::tnc();
		$tnc->storeTnc('reply');

		// We need the composer for editing purposes
		$opts = array('editing', $post);
		$composer = ED::composer($opts);

		// Get the post's parent
		$question = $post->getParent();
		$questionCategory = $question->getCategory();

		// Prepare the reply permalink
		$post->permalink = EDR::getReplyRoute($question->id, $post->id);
		$post->seq = $question->getTotalReplies();

		// Get the output so we can append the reply into the list of replies
		$namespace = $post->isPending() ? 'moderation' : 'item';
		$namespace = 'site/post/replies/' . $namespace;

		// Get comments for the post
		$post->comments = array();

		if ($this->config->get('main_commentpost')) {
			$commentLimit = $this->config->get('main_comment_pagination') ? $this->config->get('main_comment_pagination_count') : null;
			$post->comments = $post->getComments($commentLimit);

			// get post comments count
			$post->commentsCount = $post->getTotalComments();
		}

		$poll = $post->getPoll();

		$theme = ED::themes();
		$theme->set('composer', $composer);
		$theme->set('post', $post);
		$theme->set('poll', $poll);

		$html = $theme->output($namespace);

		// Prepare the result object
		$output = array();
		$output['slashHtml'] = '';

		// If this reply has slash command, we process everything here
		if ($post->processedLabels || $post->processedActions) {

			$labelText = '';

			if ($post->processedLabels) {
				$processedLabels = $post->processedLabels;

				// load the activity table
				$tbl = ED::table('activity');
				$tbl->load($processedLabels);

				if ($tbl->id) {
					$label = ' ~' . $tbl->getLabel();
					$labelText = JText::sprintf('COM_ED_SUCCESS_LABELS_ADDED', $label);

					// Generate label activity log item
					$theme = ED::themes();
					$theme->set('log', $tbl);

					$output['slashHtml'] .= $theme->output('site/post/activities/item');
				}
			}

			$actionText = '';

			if ($post->processedActions) {
				$processedActions = $post->processedActions;
				
				foreach ($processedActions as $activityId) {
					// load the activity table
					$tbl = ED::table('activity');
					$tbl->load($activityId);

					if ($tbl->id) {
						$action = explode('.', $tbl->action);
						$actionText .= JText::_('COM_ED_SLASH_' . strtoupper($action[1]));

						// Generate label activity log item
						$theme = ED::themes();
						$theme->set('log', $tbl);

						$output['slashHtml'] .= $theme->output('site/post/activities/item');
					}
				}
			}
			
			$output['slashText'] = $actionText . ' ' . $labelText;

			// Get the latest available commands
			$commands = $post->getSlashCommands();
			$output['commands'] = $commands;

			// if this reply ONLY has slash command, we need to set a flag to not add the reply item
			$output['noContent'] = empty(trim($post->getContent(true))) ? true : false;
		}

		$output['message'] = $post->isPending() ? JText::_('COM_EASYDISCUSS_MODERATION_REPLY_POSTED') : JText::_('COM_EASYDISCUSS_SUCCESS_REPLY_POSTED');
		$output['type'] = $post->isPending() ? 'info' : 'success';
		$output['html'] = $html . $output['slashHtml'];
		$output['postId'] = $post->id;

		// Perhaps the viewer is unable to view the replies.
		if (!$questionCategory->canViewReplies()) {
			$output['message'] = JText::_('COM_EASYDISCUSS_REPLY_SUCCESS_BUT_UNABLE_TO_VIEW_REPLIES');
		}

		// Reload captcha if necessary
		$recaptcha = '';
		$enableRecaptcha = $this->config->get('antispam_recaptcha', 0);
		$publicKey = $this->config->get('antispam_recaptcha_public');

		if ($enableRecaptcha && !empty($publicKey) && $recaptcha) {
			$output['type'] = 'success.captcha';
		}

		echo $this->showJsonContents($output);
		exit;
	}

	/**
	 * Generates the text for slash commands
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function processSlashActions($commands)
	{
		
	}

	/**
	 * Generates the output for json calls
	 *
	 * @since   4.0
	 * @access  public
	 */
	private function showJsonContents($output = null)
	{
		return '<script type="text/json" id="ajaxResponse">' . json_encode($output) . '</script>';
	}

	/**
	 * Displays confirmation to feature a post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function feature()
	{
		$id = $this->input->get('id', 0, 'int');
		$theme = ED::themes();

		$theme->set('id', $id);
		$contents = $theme->output('site/post/dialogs/feature');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Displays confirmation to unfeature a post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function unfeature()
	{
		$id = $this->input->get('id', 0, 'int');
		$theme = ED::themes();

		$theme->set('id', $id);
		$contents = $theme->output('site/post/dialogs/unfeature');

		return $this->ajax->resolve($contents);
	}

	public function deleteAttachment( $id = null )
	{
		require_once JPATH_ROOT . '/components/com_easydiscuss/controllers/attachment.php';

		$disjax     = new Disjax();

		$controller = new EasyDiscussControllerAttachment();

		$msg        = JText::_('COM_EASYDISCUSS_ATTACHMENT_DELETE_FAILED');
		$msgClass   = 'o-alert o-alert--error';
		if($controller->deleteFile($id))
		{
			$msg        = JText::_('COM_EASYDISCUSS_ATTACHMENT_DELETE_SUCCESS');
			$msgClass   = 'dc_success';
			$disjax->script( 'EasyDiscuss.$( "#dc-attachments-'.$id.'" ).remove();' );
		}

		$disjax->assign( 'dc_post_notification .msg_in' , $msg );
		$disjax->script( 'EasyDiscuss.$( "#dc_post_notification .msg_in" ).addClass( "'.$msgClass.'" );' );
		$disjax->script( 'EasyDiscuss.$( "#button-delete-att-'.$id.'" ).prop("disabled", false);' );

		$disjax->send();
	}

	public function nameSuggest( $part )
	{
		$ajax       = ED::getHelper( 'Ajax' );
		$db         = ED::db();
		$config     = ED::config();
		$property   = $this->config->get( 'layout_nameformat' );

		$query      = 'SELECT a.`id`,a.`' . $property . '` AS title FROM '
					. $db->nameQuote( '#__users' ) . ' AS a '
					. 'LEFT JOIN ' . $db->nameQuote( '#__discuss_users' ) . ' AS b '
					. 'ON a.`id`=b.`id`';

		if( $property == 'nickname' )
		{
			$query  .= ' WHERE b.' . $db->nameQuote( $property ) . ' LIKE ' . $db->Quote( '%' . $part . '%' );
		}
		else
		{
			$query  .= ' WHERE a.' . $db->nameQuote( $property ) . ' LIKE ' . $db->Quote( '%' . $part . '%' );
		}

		$db->setQuery( $query );
		$names      = $db->loadObjectList();

		require_once DISCUSS_CLASSES . '/json.php';
		$json       = new Services_JSON();
		$ajax->success( $json->encode( $names ) );
		$ajax->send();
	}

	/**
	 * Renders the video embed dialog form
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function showVideoDialog()
	{
		$element = $this->input->get('editorName', '', 'word');
		$caretPosition = $this->input->get('caretPosition', '', 'int');
		$contents = $this->input->get('contents', '', 'raw');
		$dialogRecipient = $this->input->get('dialogRecipient', 0, 'int');

		$theme = ED::themes();
		$theme->set('element', $element);
		$theme->set('caretPosition', $caretPosition);
		$theme->set('contents', $contents);
		$theme->set('dialogRecipient', $dialogRecipient);

		$output = $theme->output('site/composer/dialogs/video');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the photo url dialog form
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function showPhotoDialog()
	{
		$element = $this->input->get('editorName', '', 'word');
		$caretPosition = $this->input->get('caretPosition', '', 'int');
		$contents = $this->input->get('contents', '', 'raw');
		$dialogRecipient = $this->input->get('dialogRecipient', 0, 'int');

		$theme = ED::themes();
		$theme->set('element', $element);
		$theme->set('caretPosition', $caretPosition);
		$theme->set('contents', $contents);
		$theme->set('dialogRecipient', $dialogRecipient);

		$output = $theme->output('site/composer/dialogs/photo');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the insert link url dialog form
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function showLinkDialog()
	{
		$element = $this->input->get('editorName', '', 'word');
		$caretPosition = $this->input->get('caretPosition', '', 'int');
		$contents = $this->input->get('contents', '', 'raw');
		$dialogRecipient = $this->input->get('dialogRecipient', 0, 'int');

		$theme = ED::themes();
		$theme->set('element', $element);
		$theme->set('caretPosition', $caretPosition);
		$theme->set('contents', $contents);
		$theme->set('dialogRecipient', $dialogRecipient);

		$output = $theme->output('site/composer/dialogs/link');

		return $this->ajax->resolve($output);
	}


	/**
	 * Renders the insert link url dialog form
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function showArticleDialog()
	{
		$element = $this->input->get('editorName', '', 'word');
		$caretPosition = $this->input->get('caretPosition', '', 'int');
		$contents = $this->input->get('contents', '', 'raw');
		$dialogRecipient = $this->input->get('dialogRecipient', 0, 'int');

		$theme = ED::themes();
		$theme->set('element', $element);
		$theme->set('caretPosition', $caretPosition);
		$theme->set('contents', $contents);
		$theme->set('dialogRecipient', $dialogRecipient);

		$output = $theme->output('site/composer/dialogs/article');

		return $this->ajax->resolve($output);
	}

	/**
	 * Check for updates
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function getUpdateCount()
	{
		$ajax = ED::ajax();

		$id = $this->input->get('id', 0, 'int');;

		if ($id === 0) {
			$ajax->reject();
			return $ajax->send();
		}

		$model = ED::model('posts');

		$totalReplies = (int) $model->getTotalReplies($id);
		$totalComments = (int) $model->getTotalComments($id, 'thread');

		$ajax->resolve($totalReplies, $totalComments);
		return $ajax->send();
	}

	/**
	 * Get comments for particular post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getComments()
	{
		$model  = ED::model('Posts');
		$config = ED::config();

		// Get the post id
		$id = $this->input->get('id', 0, 'int');

		// Get the total of the current comment list
		$start = $this->input->get('start', 0, 'int');

		// Get the comment ids to exclude
		$excludeIds = $this->input->get('excludeIds', array(), 'array');
		$excludeTotal = 0;

		if (!empty($excludeIds)) {
			$excludeTotal = count($excludeIds);
		}

		// Get the total comments for this post
		$total = $model->getTotalComments($id);

		// If the current comment is more than the total comment, return false
		if ($start >= $total) {
			return $this->ajax->reject();
		}

		$limit = $this->config->get('main_comment_pagination_count');

		// Get the comments based on the start value
		$comments = $model->getComments($id, $limit, $start, $excludeIds);

		if (empty($comments)) {
			return $this->ajax->reject();
		}

		$count = count($comments);

		$nextCycle = ($start + $count) < ($total - $excludeTotal);

		$comments = ED::formatComments($comments);

		$contents = '';

		$theme = ED::themes();

		foreach($comments as $comment) {
			$theme->set('id', $id);
			$theme->set('comment', $comment);
			$theme->set('isNew', false);
			$contents .= $theme->output('site/comments/item/default');
		}

		return $this->ajax->resolve($contents, $nextCycle);
	}

	/**
	 * Get replies based on pagination load more
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function getReplies()
	{
		$theme  = new DiscussThemes();
		$ajax   = ED::getHelper( 'Ajax' );
		$model  = ED::model( 'Posts' );
		$config = ED::config();

		$id     = $this->input->get('id', 0, 'int');

		$sort   = $this->input->get('sort', ED::getDefaultRepliesSorting(), 'string');

		$start  = $this->input->get('start', 0, 'int');

		$total  = $model->getTotalReplies( $id );

		if( $start >= $total )
		{
			return $ajax->reject();
		}

		$replies = $model->getReplies( $id, $sort, $start, $this->config->get( 'layout_replies_list_limit' ) );

		if( empty( $replies ) )
		{
			return $ajax->reject();
		}

		$count = count( $replies );

		$nextCycle = ( $start + $count ) < $total;

		// Load the category
		$post       = ED::table('Posts' );
		$post->load( $id );
		$category   = ED::table('Category' );
		$category->load( (int) $post->category_id );

		$replies = ED::formatReplies( $replies, $category );

		$html = '';

		foreach( $replies as $reply )
		{
			$theme->set('category', $category);
			$theme->set('question', $post);
			$theme->set('post', $reply);
			$html .= '<li>' . $theme->fetch( 'post.reply.item.php' ) . '</li>';
		}

		return $ajax->resolve( $html, $nextCycle );
	}

	/**
	 * Allows caller to generate an edit reply form
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function editReply()
	{
		$id = $this->input->get('id', 0, 'int');
		$seq = $this->input->get('seq', 0, 'int');

		if ($id === 0) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'));
		}

		// Load the post table
		$post = ED::post($id);

		// Set the reply seq. We do not know which reply currently being edited
		$post->seq = $seq;

		// Determine if this person can edit this post
		if (!$post->canEdit()) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
		}

		// Load up the composer and retrieve the form
		$composer = ED::composer(array('editing', $post));
		$form = $composer->getComposer($post->category_id);

		return $this->ajax->resolve($form);
	}


	public function checkEmpty($post)
	{
		// do checking here!
		if (empty($post['dc_content'])) {
			return $this->ajax->reject('error', JText::_('COM_EASYDISCUSS_ERROR_REPLY_EMPTY'));
			exit;
		}
	}

	/**
	 * Determines if the captcha is correct
	 *
	 * @since   1.0
	 * @access  public
	 */
	public function checkCaptcha($post)
	{
		// Get recaptcha configuration
		$recaptcha = $this->config->get('antispam_recaptcha');
		$public = $this->config->get('antispam_recaptcha_public');
		$private = $this->config->get('antispam_recaptcha_private');

		if (DiscussRecaptcha::isRequired()) {
			$obj = DiscussRecaptcha::recaptcha_check_answer($private, $_SERVER['REMOTE_ADDR'], $post['recaptcha_challenge_field'], $post['recaptcha_response_field']);

			if (!$obj->is_valid) {
				$this->ajax->reloadCaptcha();
				return $this->ajax->reject('error', JText::_('COM_EASYDISCUSS_POST_INVALID_RECAPTCHA_RESPONSE'));
			}
		} else if ($this->config->get('antispam_easydiscuss_captcha')) {

			$runCaptcha = ED::captcha()->showCaptcha();

			if ($runCaptcha) {

				$response = $this->input->get('captcha-response', '', 'var');
				$captchaId = $this->input->get('captcha-id', '', 'int');

				$discussCaptcha = new stdClass();
				$discussCaptcha->captchaResponse = $response;
				$discussCaptcha->captchaId = $captchaId;

				$state = ED::captcha()->verify($discussCaptcha);

				if (!$state) {
					return $this->ajax->reject('error', JText::_('COM_EASYDISCUSS_INVALID_CAPTCHA'));
				}
			}
		}

		return true;
	}

	/**
	 * Some desc
	 *
	 * @since   1.0
	 * @access  public
	 */
	public function processPolls($post)
	{
		// Process poll items
		$includePolls = $this->input->get('pollitems', false, 'bool');

		// Process poll items here.
		if ($includePolls && $this->config->get('main_polls')) {
			$pollItems = $this->input->get('pollitems', '', 'var');
			$pollItemsOri = $this->input->get('pollitemsOri', '', 'var');

			// Delete polls if necessary since this post doesn't contain any polls.
			if (count($pollItems) == 1 && empty($pollItems[0]) && !$isNew) {
				$post->removePoll();
			}

			// Check if the multiple polls checkbox is it checked?
			$multiplePolls = $this->input->get('multiplePolls', '0', 'var');

			if ($pollItems) {

				// As long as we need to create the poll answers, we need to create the main question.
				$pollTitle = $this->input->get('poll_question', '', 'var');

				// Since poll question are entirely optional.
				$pollQuestion = ED::table('PollQuestion');
				$pollQuestion->loadByPost($post->id);

				$pollQuestion->post_id = $post->id;
				$pollQuestion->title = $pollTitle;
				$pollQuestion->multiple = $this->config->get('main_polls_multiple') ? $multiplePolls : false;
				$pollQuestion->store();

				if (!$isNew) {

					// Try to detect which poll items needs to be removed.
					$remove = $this->input->get('pollsremove', '', 'var');

					if (!empty($remove)) {
						$remove = explode(',', $remove);

						foreach ($remove as $id) {
							$id = (int) $id;
							$poll = ED::table('Poll');
							$poll->load($id);
							$poll->delete();
						}
					}
				}

				for ( $i = 0; $i < count($pollItems); $i++) {
					$item = $pollItems[$i];
					$itemOri = isset($pollItemsOri[$i]) ? $pollItemsOri[$i] : '';

					$value = (string) $item;
					$valueOri = (string) $itemOri;

					if (trim($value) == '')
						continue;

					$poll = ED::table('Poll');

					if (empty($valueOri) && !empty($value)) {
						// this is a new item.
						$poll->set('value', $value);
						$poll->set('post_id', $post->get('id'));
						$poll->store();
					}
					else if (!empty($valueOri) && !empty($value)) {
						// update existing value.
						$poll->loadByValue($valueOri, $post->get('id'));
						$poll->set('value', $value );
						$poll->store();
					}

				}

			}
		}
	}


	public function saveReply()
	{
		// Get the posted data
		$data = $this->input->get('post', '', 'default');

		// Prepare the output data
		$output = array();
		$output['id'] = $data[ 'post_id' ];

		// Check for empty content
		$this->checkEmpty($data);

		// Rebind the post data because it may contain HTML codes
		$data['content'] = $this->input->get('dc_content', '', 'post', 'none', JREQUEST_ALLOWRAW);
		$data['content_type'] = ED::getEditorType('reply');

		// Load up the post lib
		$post = ED::post($data['post_id']);

		// Bind the post table with the data
		$post->bind($data);

		// Check if the post data is valid
		if (!$post->id || !$data['post_id']) {
			return $this->ajax->reject('error', JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'));
		}

		// Only allow users with proper access
		$isModerator = ED::moderator()->isModerator($post->category_id);

		// Do not allow unauthorized access
		if (!ED::isSiteAdmin() && $post->user_id != $this->my->id && !$this->acl->allowed('edit_reply', 0) && !$isModerator) {
			return $this->ajax->reject('error', JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
		}

		// Get the new content from the post data
		$post->content = $data['content'];

		// Validate captcha
		$this->checkCaptcha($data);

		// Bind any parameters needed to be stored
		$post->bindParams($data);

		// Bind file attachments
		if ($this->acl->allowed('add_attachment', '0')) {
			$post->bindAttachments();
		}

		// Determines if this is a new post.
		$isNew = false;

		// @trigger: onBeforeSave
		ED::events()->importPlugin('content');
		ED::events()->onContentBeforeSave('post', $post, $isNew);

		// Try to store the post now
		if (!$post->store()) {
			return $this->ajax->reject('error', JText::_('COM_EASYDISCUSS_ERROR'));
		}

		// Process polls
		$this->processPolls($post);



		// @trigger: onAfterSave
		ED::events()->onContentAfterSave('post', $post, $isNew);

		// Filter for badwords
		$post->title = ED::badwords()->filter($post->title);
		$post->content = ED::badwords()->filter($post->content);

		// Determines if the user is allowed to delete this post
		$canDelete = false;

		if (ED::isSiteAdmin() || $this->acl->allowed('delete_reply', '0') || $post->user_id == $this->my->id) {
			$canDelete = true;
		}

		// URL References
		$post->references = $post->getReferences();

		// Get the voted state
		$voteModel = ED::model('Votes');
		$post->voted = $voteModel->hasVoted($post->id);

		// Get total votes for this post
		$post->totalVote = $post->sum_totalvote;

		// Load profile info
		$creator = ED::user($post->user_id);

		// Assign creator
		$post->user = $creator;

		//raw content
		$tmp = $post->content;

		// Format the content.
		$post->preview = ED::formatContent($post);

		// Once the formatting is done, we need to escape the raw content
		$post->content = ED::string()->escape($tmp);

		// Store the default values
		//default value
		$post->isVoted = 0;
		$post->total_vote_cnt = 0;
		$post->likesAuthor = '';
		$post->minimize = 0;

		// Trigger reply
		$post->triggerReply();

		// Load up parent's post
		$question = ED::post($post->parent_id);

		$recaptcha = '';
		$enableRecaptcha = $this->config->get('antispam_recaptcha');
		$publicKey = $this->config->get('antispam_recaptcha_public');
		$skipRecaptcha = $this->config->get('antispam_skip_recaptcha');

		$model = ED::model('Posts');
		$postCount = count($model->getPostsBy('user', $this->my->id));

		if ($enableRecaptcha && !empty($publicKey) && $postCount < $skipRecaptcha) {
			$recaptcha  = DiscussRecaptcha::getRecaptchaData($publicKey, $this->config->get('antispam_recaptcha_theme'), $this->config->get('antispam_recaptcha_lang'), null, $this->config->get('antispam_recaptcha_ssl'), 'edit-reply-recaptcha' .  $post->id);
		}

		// Get the post access object here.
		$category = ED::category($post->category_id);

		$access = $post->getAccess($category);
		$post->access = $access;

		// Get comments for the post
		$commentLimit = $this->config->get('main_comment_pagination') ? $this->config->get('main_comment_pagination_count') : null;
		$comments = $post->getComments($commentLimit);
		$post->comments = ED::formatComments($comments);


		$theme = ED::themes();

		$theme->set('question', $question);
		$theme->set('post', $post);
		$theme->set('category', $category);

		// Get theme file output
		$contents = $theme->output('site/post/default');

		return $this->ajax->resolve($contents);
	}

	public function saveCustomFieldsValue()
	{
		$id = $this->input->get('id', 0, 'int');

		if (!empty($id)) {

			//Clear off previous records before storing
			$ruleModel = ED::model('CustomFields');
			$ruleModel->deleteCustomFieldsValue($id, 'update');

			$post = ED::table('Post');
			$post->load($id);

			// Process custom fields
			$fieldIds = $this->input->get('customFields', '', 'var');

			if (!empty($fieldIds)) {

				foreach ($fieldIds as $fieldId) {

					$fields = $this->input->get('customFieldValue_' . $fieldId);

					if (!empty($fields)) {

						// Cater for custom fields select list
						// To detect if there is no value selected for the select list custom fields

						if (in_array('defaultList', $fields)) {
							$tempKey = array_search('defaultList', $fields);
							$fields[ $tempKey ] = '';
						}
					}

					$post->bindCustomFields($fields, $fieldId);
				}
			}
		}
	}

	/**
	 * Displays confirmation to branch a reply
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function branchForm()
	{
		$id = $this->input->get('id', 0, 'int');

		// $model = ED::model('Posts');
		// $posts = $model->getDiscussions(array('limit' => DISCUSS_NO_LIMIT, 'exclude' => array($id)));

		$theme = ED::themes();
		$theme->set('id', $id);
		$contents = $theme->output('site/post/dialogs/branch');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Merges the current discussion into an existing discussion
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function mergeForm()
	{
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->resolve(JText::_('COM_ED_MERGE_INVALID_DATA'));
		}

		// check current user has the ability perform post merge or not.
		$post = ED::post($id);
		// stop here.
		if (!$post->canMove()) {
			return $this->ajax->resolve(JText::_('COM_ED_MERGE_NOT_ALLOWED'));
		}

		$theme  = ED::themes();
		$theme->set('id', $id);
		$theme->set('current', $id);

		$contents = $theme->output('site/post/dialogs/merge');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Search for post topics used in post merge
	 *
	 * @since   4.1.18
	 * @access  public
	 */
	public function mergePostSuggest()
	{
		$id = $this->input->get('id', 0, 'int');
		$text = $this->input->get('text', '', 'string');
		$text = trim($text);

		if (!$id) {
			// return nothing.
			return $this->ajax->resolve('');
		}

		// check current user has the ability perform post merge or not.
		$post = ED::post($id);

		// stop here.
		if (!$post->canMove()) {
			// return nothing.
			return $this->ajax->resolve('');
		}

		$model = ED::model('Posts');
		$posts = $model->suggestTopics($text, array($id));

		return $this->ajax->resolve($posts);
	}

	/**
	 * Renders the ban user form
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function banForm()
	{
		$id = $this->input->get('id', 0, 'int');

		// Load the new post object
		$post = ED::post($id);

		// if (!$post->canBanAuthor()) {
		//     return $this->ajax->reject();
		// }

		$theme = ED::themes();
		$theme->set('post', $post);

		$contents = $theme->output('site/post/dialogs/ban.user');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Retrieves the default checked state of private post when switching category
	 *
	 * @since   4.0.21
	 * @access  public
	 */
	public function getPrivateState()
	{
		$categoryId = $this->input->get('categoryId', 0, 'int');

		$category = ED::category($categoryId);
		$params = $category->getParams();

		$checked = $params->get('cat_default_private', false);
		$enforced = $params->get('cat_enforce_private', false);

		return $this->ajax->resolve($checked, $enforced);
	}

	/**
	 * Retrieves a list of post types associated with a particular category
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getPostTypes()
	{
		$categoryId = $this->input->get('categoryId', 0, 'int');
		$selected = null;

		// Get post types list
		$model = ED::model('PostTypes');
		$postTypes = $model->getPostTypes($categoryId);

		$theme = ED::themes();
		$theme->set('selected', $selected);
		$theme->set('postTypes', $postTypes);
		$output = $theme->output('site/composer/forms/post.types');

		return $this->ajax->resolve($output);
	}

	/**
	 * Allows caller to set the label of the post
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function label()
	{
		if (!$this->config->get('main_labels')) {
			die('Invalid request');
		}
		
		// Get the id of the post
		$id = $this->input->get('id', 0, 'int');

		$labelId = $this->input->get('labelId', 0, 'int');
		$title = $this->input->get('title', '', 'string');

		if (!$id) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID'));
		}

		$action = 'setLabel';

		// Show the remove label message if moderator chooses to
		if (!$labelId) {
			$action = 'removeLabel';
		}

		// Load up the post
		$post = ED::post($id);

		if (!$post->canLabel()) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
		}

		$state = $post->$action($title);

		if (!$state) {
			return $this->ajax->reject($post->getError());
		}

		$theme = ED::themes();
		$html = $theme->html('post.label', $post->getCurrentLabel());

		return $this->ajax->resolve($html);
	}

	/**
	 * Update the tabs after changing the category in the composer
	 *
	 * @since   4.2
	 * @access  public
	 */
	public function updateAllowedTabs()
	{
		$operation = $this->input->get('operation', '', 'default');
		$postId = $this->input->get('postId', 0, 'int');
		$categoryId = $this->input->get('categoryId', 0, 'int');
		$editorUuid = $this->input->get('editorUuid', '', 'string');

		$post = ED::post($postId);
		$composer = ED::composer($operation, $post);

		if ($editorUuid) {
			// override the composer uuid here.
			$composer->setComposerUuid($editorUuid);
		}

		$html = $composer->renderTabs($categoryId);

		return $this->ajax->resolve($html);
	}
}
