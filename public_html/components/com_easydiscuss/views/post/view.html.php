<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ROOT . '/views/views.php');

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

		// Ensure that the viewer can view the post
		if (!$post->canView($this->my->id) || !$post->isPublished() || !$post->isQuestion()) {
			// Set a proper redirection according to the settings.
			ED::getErrorRedirection(JText::_('COM_EASYDISCUSS_SYSTEM_POST_NOT_FOUND'));
		}

		// Determine if user are allowed to view the discussion item that belong to another cluster.
		if ($post->isCluster()) {
			$easysocial = ED::easysocial();

			if (!$easysocial->isGroupAppExists()) {
				ED::getErrorRedirection(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
			}

			$cluster = $easysocial->getCluster($post->cluster_id, $post->getClusterType());

			if (!$cluster->canViewItem()) {
				ED::getErrorRedirection(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
			}
		}

		// Get the posts' category
		$category = $post->getCategory();

		// Set breadcrumbs for this discussion.
		if (! EDR::isCurrentActiveMenu('post', $post->id)) {

			// Add pathway for category here.
			if (! EDR::isCurrentActiveMenu('forums', $category->id, 'category_id')) {
				ED::breadcrumbs()->insertCategory($category);
			}

			$this->setPathway($post->getTitle());
		}

		// Mark as viewed for notifications.
		$this->logView();

		// Update hit count for this discussion.
		$post->hit();

		// Set page headers
		$this->setPageHeaders($post);

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

		$replies = $post->getReplies(true, $limitReplies, $sort, $limitstart, $isLastPage);

		$emptyMessage = JText::_('COM_EASYDISCUSS_NO_REPLIES_YET');

		// Display proper empty message if the user are not allowed to reply or view the replies.
		if ((!$post->canReply() || !$post->canView()) && $post->isReply()) {
			$emptyMessage = JText::_('COM_EASYDISCUSS_POST_REPLY_NOT_ALLOWED');
		}

		// We need to double check again whether this post already have accepted answer while this total replies equal to 1
		if (empty($replies) && $post->getTotalReplies() > 0) {
			$emptyMessage = JText::_('COM_EASYDISCUSS_VIEW_REPLIES_NOT_ALLOWED');
		}

		// since now we treat accepted reply as reply, we need to add flag for this
		$onlyAcceptedReply = false;

		if (empty($replies) && ($post->isPostReplyAccepted() && $post->getTotalReplies() == 1)) {
			$onlyAcceptedReply = true;
		}

		// Get comments for the post
		$post->comments = array();

		if ($this->config->get('main_commentpost')) {
			$commentLimit = $this->config->get('main_comment_pagination') ? $this->config->get('main_comment_first_sight_count') : null;
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

		// Get site details that are associated with the post.
		$post->siteDetails = $post->getSiteDetails();

		$navigation = $post->getPostNavigation();

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
		$this->set('navigation', $navigation);
		$this->set('onlyAcceptedReply', $onlyAcceptedReply);

		// If this post is password protected, we need to display the form to enter password
		if ($post->isProtected() && !$post->canViewProtectedPost($this->my->id)) {
			parent::display('post/default.protected');
			return;
		}

		parent::display('post/default');
	}

	/**
	 * Displays the edit form for reply only
	 *
	 * @since	4.0
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
			return JError::raiseError(404, JText::_('COM_EASYDISCUSS_SYSTEM_POST_NOT_FOUND'));
		}

		// There is a possibility that this post is being edited so we try to load it first.
		$post = ED::post($id);

		$threadUrl = EDR::_('index.php?option=com_easydiscuss&view=post&id=' . $post->parent_id, false);

		if (!$post->isReply()) {
			ED::getErrorRedirection(JText::_('COM_EASYDISCUSS_SYSTEM_POST_NOT_FOUND'));
		}

		if (!$post->canEdit()) {
			return $this->app->redirect($threadUrl, JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
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

		// Test if reference is passed in query string.
		$reference = $this->input->get('reference', '', 'word');
		$referenceId = $this->input->get('reference_id', 0, 'int');
		$redirect = $this->input->get('redirect', '', 'default');

		// Prepare the cancel link
		$cancel = $threadUrl;

		$this->set('cancel', $cancel);
		$this->set('post', $post);
		$this->set('composer', $composer);
		$this->set('attachments', $attachments);
		$this->set('redirect', $redirect);
		$this->set('my', $my);

		parent::display('post/default.edit');
	}

	private function getSessionData(&$post)
	{
		// Get form values from session.
		$data = ED::getSession('NEW_POST_TOKEN');

		if (!empty($data)) {

			// Try to bind the data from the object.
			$post->bind($data, true);

			$post->tags	= array();
			$post->attachments = array();

			if (isset($data['tags'])) {

				foreach ($data['tags'] as $tag) {
					$obj = new stdClass();
					$obj->title	= $tag;

					$post->tags[] = $obj;
				}
			}

			if (isset($data['polls']) && isset($data['pollitems']) && is_array($data['pollitems'])) {

				$polls = array();

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
			$customfields = array();
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
	 * Sets the page headers for this post
	 *
	 * @since	4.0
	 * @access	private
	 */
	private function setPageHeaders(EasyDiscussPost $post)
	{
		$postTitle = $post->getTitle();
		$pageTitle = htmlspecialchars_decode($postTitle, ENT_QUOTES);
		$pageContent = strip_tags($post->preview);
		$pageContent = JString::substr($pageContent, 0, 160);

		$description = preg_replace('/\s+/', ' ', $pageContent);

		// Set page title.
		ED::setPageTitle($pageTitle);

		// Default data
		$this->doc->setTitle($pageTitle);
		$this->doc->setDescription($description);
		$this->doc->setMetadata('keywords', $pageTitle);
		$this->doc->setMetadata('description', $description);

		// Set canonical link to avoid URL duplication.
		$url = EDR::getPostRoute($post->id, false);
		$this->doc->addHeadLink($url, 'canonical', 'rel');

		// Add opengraph tags
		if ($this->config->get('integration_facebook_opengraph')) {
			ED::facebook()->addOpenGraph($post);
		}

		if ($this->config->get('integration_twitter_card')) {
			ED::twitter()->addCard($post);
		}
	}
}
