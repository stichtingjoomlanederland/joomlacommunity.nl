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
	/**
	 * Renders the post view for a discussion
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Sorting and filters.
		$sort = $this->input->get('sort', ED::getDefaultRepliesSorting(), 'word');

		// to ensure the sort has the correct value as sometime sort might be empty string.
		$sort = $sort ? $sort : ED::getDefaultRepliesSorting();

		$filteractive = $this->input->get('filter', 'allposts', 'string');
		$pagination = $this->config->get('layout_replies_pagination');

		// Get the post id
		$id = $this->input->get('id', 0, 'int');

		// Add noindex for print view by default.
		$print = $this->input->get('print', 0, 'int');

		// If this is a print request, we wouldn't want crawlers to index this page
		if ($print) {
			$this->doc->setMetadata('robots', 'noindex,follow');
		}

		// New way of loading a post object
		$post = ED::post($id);

		// Users need to login to read the post if is required
		$post->requireLoginToRead();

		// Ensure that the viewer can view the post
		if (!$post->canView($this->my->id) || !$post->isQuestion()) {
			ED::getErrorRedirection(JText::_('COM_EASYDISCUSS_SYSTEM_POST_NOT_FOUND'));
			return;
		}

		// Determine if user are allowed to view the discussion item that belong to another cluster.
		if ($post->isCluster()) {
			$easysocial = ED::easysocial();

			if (!$easysocial->isGroupAppExists()) {
				ED::getErrorRedirection(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
				return;
			}

			$cluster = $easysocial->getCluster($post->cluster_id, $post->getClusterType());

			if (!$cluster->canViewItem()) {
				ED::getErrorRedirection(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
				return;
			}
		}

		// Render necessary data on the headers
		$post->renderHeaders();

		// Set canonical link to avoid URL duplication.
		$this->canonical('index.php?option=com_easydiscuss&view=post&id=' . $post->id);

		if ($this->config->get('main_amp')) {
			$this->amp($post->getPermalink(false, true, false, true, false, true), false);
		}

		// Get the posts' category
		$category = $post->getCategory();

		// Set breadcrumbs for this discussion.
		if (!EDR::isCurrentActiveMenu('post', $post->id)) {

			// Add pathway for category here.
			if (!EDR::isCurrentActiveMenu('forums', $category->id, 'category_id')) {
				ED::breadcrumbs()->insertCategory($category);
			}

			$this->setPathway($post->getTitle());
		}

		// Mark as viewed for notifications.
		// to avoid issue with multilingual, we need to pass the url manually.
		$this->logView($post->getNonSEFLink());

		// Update hit count for this discussion.
		$post->hit();

		// Before sending the title and content to be parsed, we need to store this temporarily in case it needs to be accessed.
		$post->title_clear = $post->title;

		// Get the tags for this discussion
		$tags = $post->getTags();

		// Get adsense codes here.
		$adsense = ED::adsense()->html();

		$model = ED::model('Posts');

		// Get the answer for this discussion.
		$answer = $post->getAcceptedReply(true);

		// Get a list of replies for this post.
		$limitReplies = $post->config->get('layout_replies_list_limit');
		$limitstart = $this->app->input->get('limitstart', 0);
		$isLastPage = $this->app->input->get('page', '') == 'last';

		$emptyMessage = JText::_('COM_EASYDISCUSS_NO_REPLIES_YET');

		// Display proper empty message if the user are not allowed to reply or view the replies.
		if ((!$post->canReply() || !$post->canView()) && $post->isReply()) {
			$emptyMessage = JText::_('COM_EASYDISCUSS_POST_REPLY_NOT_ALLOWED');
		}

		// We need to double check again whether this post already have accepted answer while this total replies equal to 1
		// if (empty($replies) && $post->getTotalReplies() > 0) {
		if (!$post->canViewReply()) {
			$emptyMessage = JText::_('COM_EASYDISCUSS_VIEW_REPLIES_NOT_ALLOWED');
		}

		// debug:
		// $limitReplies = 20;

		$rOptions = array('limit' => $limitReplies, 'sort' => $sort, 'limitstart' => $limitstart, 'isLastPage' => $isLastPage, 'includeAnswer' => true, 'nextReply' => true);
		$replies = $post->getReplies($rOptions);

		// get the next reply item from next pagination if exists
		$rOptions['nextReplyItem'] = $post->getNextReplyItem();

		// since now we treat accepted reply as reply, we need to add flag for this
		$onlyAcceptedReply = false;
		$totalReplies = $post->getTotalReplies();

		if (empty($replies) && ($post->isPostReplyAccepted() && $totalReplies == 1)) {
			$onlyAcceptedReply = true;
		}

		// now we try to merge the replies.
		$rOptions['totalReplies'] = $totalReplies;
		$rOptions['repliesPagination'] = $this->config->get('layout_replies_pagination', false);

		$replies = ED::activity()->fetch($post, $replies, $rOptions);

		// Get comments for the post
		$post->comments = [];

		if ($this->config->get('main_commentpost')) {
			$commentLimit = $this->config->get('main_comment_pagination') ? $this->config->get('main_comment_pagination_count') : null;
			$post->comments = $post->getComments($commentLimit);

			// get post comments count
			$post->commentsCount = $post->getTotalComments();
		}

		// Update the read status for this post
		$post->markRead();

		// Load social button lib
		$socialbuttons = ED::sharer()->html($post);

		// Get the post owner id
		$owner = $post->getOwner()->id;

		// Get the post access rule
		$access = $post->getAccess();

		// Render new composer
		$opts = array('replying', $post);
		$composer = ED::composer($opts);

		// Get the post created date
		$date = ED::date($post->created);
		$dateFormat = $date->getDateFormat(JText::_('DATE_FORMAT_LC1'));

		$post->date = JHtml::date($post->created, $dateFormat);

		// Get the pagination for replies
		if ($pagination) {
			$pagination = $model->getPagination();
		}

		// Get the poll of the post
		$poll = $post->getPoll();

		$ratings = $post->getRatings();

		$postLabelsModel = ED::model('PostLabels');
		$labels = $postLabelsModel->getLabels();

		$this->set('ratings', $ratings);
		$this->set('labels', $labels);
		$this->set('poll', $poll);
		$this->set('pagination', $pagination);
		$this->set('post', $post);
		$this->set('replies', $replies);
		$this->set('print', $print);
		$this->set('composer', $composer);
		$this->set('adsense', $adsense);
		$this->set('tags', $tags);
		$this->set('owner', $owner);
		$this->set('access', $access);
		$this->set('answer', $answer);
		$this->set('sort', $sort);
		$this->set('date', $date);
		$this->set('socialbuttons', $socialbuttons);
		$this->set('emptyMessage', $emptyMessage);
		$this->set('onlyAcceptedReply', $onlyAcceptedReply);

		// If this post is password protected, we need to display the form to enter password
		if ($post->isProtected() && !$post->canViewProtectedPost($this->my->id)) {
			parent::display('post/item/protected');
			return;
		}

		parent::display('post/item/default');
	}

	/**
	 * Displays the edit form for a reply if the post was created using a WYSIWYG editor
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function edit($tpl = null)
	{
		// Load post item
		$id = $this->input->get('id', 0, 'int');
		$seq = $this->input->get('seq', 0, 'int');

		// Load the actor
		$my = ED::user();

		if (!$id) {
			throw ED::exception('COM_EASYDISCUSS_SYSTEM_POST_NOT_FOUND', ED_MSG_ERROR);
		}

		// There is a possibility that this post is being edited so we try to load it first.
		$post = ED::post($id);

		$threadUrl = EDR::_('index.php?option=com_easydiscuss&view=post&id=' . $post->parent_id, false);

		if (!$post->isReply()) {
			ED::getErrorRedirection(JText::_('COM_EASYDISCUSS_SYSTEM_POST_NOT_FOUND'));
			return;
		}

		if (!$post->canEdit()) {
			return ED::redirect($threadUrl, JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
		}

		// Try to get from session if there are any because the user might hit an error and we need to reload the values
		$this->getSessionData($post);

		// Set the page title.
		$title = JText::_('COM_EASYDISCUSS_TITLE_EDIT_REPLY');

		// Set the breadcrumbs.
		ED::setPageTitle($title);

		$tagsModel = ED::model('PostsTags');
		$post->tags	= $tagsModel->getPostTags($post->id);

		$attachments = $post->getAttachments();

		// If there was an error on the form, reset the attachments
		if (isset($post->sessiondata)) {
			$attachments = '';
		}

		$model = ED::model('Posts');

		// Determine how the content should be formatted in editing layout.
		// It will ignore this if that is creating operation
		$post->formatEditContent('editing');

		$composer = ED::composer(array('editing', $post));

		$redirect = $this->input->get('redirect', '', 'default');

		// Prepare the cancel link
		$cancel = $threadUrl;

		// Determines if captcha should be enabled
		$captcha = ED::captcha();

		$this->set('captcha', $captcha);
		$this->set('currentCatId', $post->getCategory()->id);
		$this->set('cancel', $cancel);
		$this->set('post', $post);
		$this->set('composer', $composer);
		$this->set('attachments', $attachments);
		$this->set('redirect', $redirect);
		$this->set('my', $my);

		parent::display('post/edit/default');
	}

	private function getSessionData(&$post)
	{
		// Get form values from session.
		$data = ED::getSession('NEW_POST_TOKEN');

		if (!empty($data)) {

			// Try to bind the data from the object.
			$post->bind($data, true);

			$post->tags	= [];
			$post->attachments = [];

			if (isset($data['tags'])) {

				foreach ($data['tags'] as $tag) {
					$obj = new stdClass();
					$obj->title	= $tag;

					$post->tags[] = $obj;
				}
			}

			if (isset($data['polls']) && isset($data['pollitems']) && is_array($data['pollitems'])) {

				$polls = [];

				foreach ($data['pollitems'] as $key => $value) {
					$poll = ED::table('Poll');
					$poll->id = $key;
					$poll->value = $value;

					$polls[] = $poll;
				}

				$post->setPolls($polls);
			}

			$poll = ED::table('PollQuestion');
			$poll->title = isset($data['poll_question']) ? $data['poll_question'] : '';
			$poll->multiple = isset($data['multiplePolls']) ? $data['multiplePolls'] : false;

			// $post->setPollQuestions($poll);

			// Process custom fields.
			$customfields = [];
			$fieldIds = isset($data['customFields']) ? $data['customFields'] : '';

			if (!empty($fieldIds)) {

				foreach ($fieldIds as $fieldId) {

					$fields	= isset($data['customFieldValue_'.$fieldId]) ? $data['customFieldValue_'.$fieldId] : '';

					$customfields[] = array($fieldId => $fields);
				}

				$post->setCustomFields($customfields);
			}

			$post->bindParams($data);

			$post->sessiondata = true;
		}
	}

	/**
	 * Get the live updates of the discussion post via SSE
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function updates()
	{
		$mode = $this->input->get('mode', '', 'string');

		if ($mode != 'SSE' || !$this->config->get('layout_post_liveupdates') || !$this->my->id) {
			return;
		}

		// Get the current question and replies id
		$ids = $this->input->get('ids', '', 'array');
		$ids = explode(',', $ids[0]);

		ED::setSSEHeader();

		// Comments exclusion
		$cExclusion = [];

		// Replies exclusion
		$rExclusion = [];

		$from = gmdate('Y-m-d H:i:s');
		$sort = $this->config->get('layout_replies_sorting');

		while (true) {
			$data = [];

			foreach ($ids as $key => $id) {
				$post = ED::post($id);

				$obj = new stdClass();
				$obj->id = $post->id;
				$obj->isQuestion = false;
				$obj->isReply = false;
				$obj->isAnswer = $post->isAnswer() ? true : false;
				$obj->isModerator = ED::isSiteAdmin() || ED::isModerator() ? true : false;
				$obj->comments = [];

				// Skip it if the post id is invalid, reply has been deleted or the reply is no longer belongs to the discussion
				if (!is_numeric($id) || !$post->id || ($key != 0 && $post->post->parent_id != $ids[0])) {
					continue;
				}

				if (!$post->isPublished() || !$post->canView($this->my->id, false)) {
					continue;
				}

				$commentsSetting = $post->isQuestion() ? 'main_commentpost' : 'main_comment'; 

				if ($this->config->get($commentsSetting)) {
					// Check if there are new comments
					$comments = $post->getComments(null, null, false, $cExclusion, $from);
					$obj->hasNewComments = false;

					if ($comments) {
						// Reset it to get latest HTML of it
						$commentsHTML = [];

						foreach ($comments as $comment) {
							// Prevent double comment being added for the comment creator
							if ($comment->user_id == $this->my->id) {
								continue;
							}

							$commenter = ED::user($comment->user_id);
							$theme = ED::themes();

							$theme->set('comment', $comment);
							$theme->set('isNew', true);
							$content = $theme->output('site/comments/item/default');

							$commentsHTML[] = $content;
							$obj->newComments = $commentsHTML;

							$cExclusion[] = (int) $comment->id;
							$obj->commenterAvatar[$comment->id] = $theme->html('user.avatar', $commenter, ['size' => 'md'], false, true);
							$obj->newCommentMessage[$comment->id] = JText::sprintf('COM_ED_LIVE_UPDATES_DISCUSSION_HAS_NEW_COMMENT', $commenter->getName());
						}

						if (count($commentsHTML) > 0) {
							$obj->hasNewComments = true;
						}
					}
				}

				if ($post->isQuestion()) {
					$obj->isQuestion = true;
					$obj->canComment = false;

					$answer = $post->getAcceptedReply(true);
					$obj->hasAnswer = false;
					$obj->answerMessage = JText::_('COM_ED_LIVE_UPDATES_DISCUSSION_REMOVED_ANSWER_MESSAGE');

					if ($answer && $answer !== true) {
						$obj->hasAnswer = true;
						$obj->answerMessage = JText::_('COM_ED_LIVE_UPDATES_DISCUSSION_HAS_ANSWER_MESSAGE');

						// Retrieve the comments of the answer
						$commentLimit = $this->config->get('main_comment_pagination') ? $this->config->get('main_comment_pagination_count') : null;
						$answer->comments = $answer->getComments($commentLimit, null, false);

						$theme = ED::themes();
						$theme->set('post', $answer);
						$theme->set('poll', $answer->getPoll());
						$theme->set('fromAnswer', true);

						$obj->answer = $theme->output('site/post/replies/item');
						$obj->answerId = (int) $answer->id;
					}

					$obj->hasNewReplies = false;
					$obj->repliesSort = $sort;

					$rOptions = array('sort' => $sort, 'useCache' => false, 'excludeIds' => $rExclusion, 'since' => $from);
					$replies = $post->getReplies($rOptions);

					// Check for new replies
					if ($replies) {
						// Reset it to get latest HTML of it
						$repliesHTML = [];
						$obj->newReplyMessage = [];
						$obj->replyerAvatar = [];

						foreach ($replies as $reply) {
							// We also need to check for comments of the new replies as well
							$ids[] = $reply->id;

							// Prevent double reply being added for the reply creator
							if ($reply->user_id == $this->my->id) {
								continue;
							}

							$replyer = ED::user($reply->user_id);
							$theme = ED::themes();

							$theme->set('post', $reply);
							$theme->set('poll', $reply->getPoll());
							$theme->set('fromAnswer', false);
							$content = $theme->output('site/post/replies/item');

							$repliesHTML[] = $content;
							$obj->replies = $repliesHTML;

							$rExclusion[] = (int) $reply->id;
							$obj->replyerAvatar[$reply->id] = $theme->html('user.avatar', $replyer, ['size' => 'md'], $reply->isAnonymous(), true);
							$obj->newReplyMessage[$reply->id] = JText::sprintf('COM_ED_LIVE_UPDATES_DISCUSSION_HAS_NEW_REPLY', $replyer->getName());
						}

						if (count($repliesHTML) > 0) {
							$obj->hasNewReplies = true;
						}
					}

					$obj->totalReplies = $post->getTotalReplies(false);
				}

				if ($post->isReply()) {
					$obj->isReply = true;
				}

				$data[] = json_encode($obj);
			}

			echo ED::responseSSE('updates', $data);
			echo "\n\n";

			@ob_end_flush();
			@flush();

			// Some 3rd party plugin could block multiple request on same session. #4181 from ES
			// Refresh the session for each loop. #4181
			session_write_close();

			usleep(800000);
		}

		exit;
	}
}
