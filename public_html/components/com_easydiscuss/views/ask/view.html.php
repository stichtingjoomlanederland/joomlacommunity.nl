<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewAsk extends EasyDiscussView
{
	/**
	 * Displays the post new question form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Load post item
		$id = $this->input->get('id', 0, 'int');

		// There is a possibility that this post is being edited so we try to load it first.
		$post = ED::post($id);

		// Determines if we are editing a post.
		$editing = (bool) $post->id;

		// Try to get from session if there are any because the user might hit an error and we need to reload the values
		if (!$editing) {
			$this->getSessionData($post);
		}

		// If caller passed in a category id, we need to select the category for them
		$categoryId = $this->input->get('category', $post->category_id, 'int');
		$category = ED::category($categoryId);

		// If caller passed in a cluster id, we need to associate this post with the cluster id.
		$clusterId = $this->input->get('group_id', 0, 'int');

		// Retrieves current uri that are being accessed
		$currentUri = base64_encode(EDR::getCurrentURI());

		// Check if user is allowed to post a discussion, we also need to check against the category acl
		if ($this->my->guest && !$this->acl->allowed('add_question', 0)) {
			return $this->app->redirect(ED::getLoginLink($currentUri));
		}

		// Ensure that logged in users can really post
		if (!$this->my->guest && !$this->acl->allowed('add_question', '0') && !$category->canPost()) {
			return $this->app->redirect(EDR::_('index.php?option=com_easydiscuss&view=index', false), JText::_('COM_EASYDISCUSS_NOT_ALLOWED_TO_POST_QUESTION'));
		}

		// Set the page title.
		$title = JText::_('COM_EASYDISCUSS_TITLE_ASK');

		if ($id && $post->id) {
			$title = JText::sprintf('COM_EASYDISCUSS_TITLE_EDIT_QUESTION', $post->getTitle());
		}
		
		$options = array();

		// Add canonical tag for this page;
		if ($categoryId) {
			$this->canonical('index.php?option=com_easydiscuss&view=ask&category=' . $categoryId);
			$title = $title . ' - ' . $category->title;
			$options = array('category' => true);

		} elseif ($id && !$categoryId) {
			$this->canonical('index.php?option=com_easydiscuss&view=ask&id=' . $post->id);
		
		} else {
			$this->canonical('index.php?option=com_easydiscuss&view=ask');
		}

		// Set the breadcrumbs.
		ED::setPageTitle($title, null, $options);

		if (! EDR::isCurrentActiveMenu('ask')) {
			$this->setPathway('COM_EASYDISCUSS_BREADCRUMBS_ASK');
		}

		if ($editing) {
			$isModerator = ED::moderator()->isModerator($post->category_id);

			if (!ED::isMine($post->user_id) && !ED::isSiteAdmin() && !$this->acl->allowed('edit_question') && !$isModerator) {
				return $this->app->redirect(EDR::_('index.php?option=com_easydiscuss&view=post&id='.$postid, false), JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
			}

			$tagsModel = ED::model('PostsTags');
			$post->tags	= $tagsModel->getPostTags($post->id);
		}

		// If this is a new post form and a category id is given, we should set it to the default.
		if ($categoryId) {
			$post->category_id = $categoryId;
		}

		$attachments = $post->getAttachments();

		// If there was an error on the form, reset the attachments
		if (isset($post->sessiondata)) {
			$attachments = '';
		}


		$model = ED::model('Posts');

		// @TODO: Very bad! Fix this
		$postCount = count($model->getPostsBy('user', $this->my->id));

		$onlyPublished = (empty($post->id)) ? true : false;

		// @rule: If there is a category id passed through the query, respect it first.
		$showPrivateCat = (!$post->id && $this->my->guest) ? false : true;

		$categoryModel = ED::model('Category');
		$defaultCategory = $categoryModel->getDefaultCategory();

		if ($categoryId && $category->id) {
			$defaultCategory = $category;
		}

		if ($categoryId == 0 && $defaultCategory !== false) {
			$categoryId = $defaultCategory->id;
		}

		// Category container cannot be selected when creating a discussion.
		if ($category->isContainer()) {

			// We must assign this category with its child.
			$childCategory = $category->getChildIds($categoryId);

			if (count($childCategory) > 0) {
				$categoryId = $childCategory[0];
			} else {
				$categoryId = ($defaultCategory !== false) ? $defaultCategory->id : '';
			}
		}

		// Sort the category by backend ordering.
		$categorySort = 'ordering';

		// Generate the categories dropdown
		// We know this dropdown need to check for 'select' category permission
		// So, we add DISCUSS_CATEGORY_ACL_ACTION_SELECT
		$categories = ED::populateCategories('', '', 'select', 'category_id', $categoryId , true, $onlyPublished, $showPrivateCat , true, 'form-control', '',  DISCUSS_CATEGORY_ACL_ACTION_SELECT, $categorySort);

		// If there are no categories, there is a possibility that the user doesn't have access
		if (!$categories) {

			ED::setMessage('COM_EASYDISCUSS_NOT_ALLOWED_TO_POST_QUESTION');

			$redirect = EDR::_('view=forums', false);

			return $this->app->redirect($redirect);
		}

		$editor = '';

		if ($this->config->get('layout_editor') != 'bbcode') {
			$editor	= JFactory::getEditor($this->config->get('layout_editor'));
		}

		// Get list of moderators from the site.
		$moderatorList = array();

		if ($this->config->get('main_assign_user')) {
			$moderatorList = ED::moderator()->getSelectOptions($post->category_id);
		}

		// Get post types list
		$postTypesModel = ED::model('PostTypes');
		// $postTypes = $postTypesModel->getTypes($categoryId);
		$postTypes = $postTypesModel->getPostTypes($categoryId);

		// Get the composer library
		$operation = $post->isNew() ? 'creating' : 'editing';

		// Determine how the content should be formatted in editing layout.
		$post->formatEditContent($operation);

		$composer = ED::composer($operation, $post);

		// Test if reference is passed in query string.
		$reference = $this->input->get('reference', '', 'word');
		$referenceId = $this->input->get('reference_id', 0, 'int');
		$redirect = $this->input->get('redirect', '', 'default');

		// Get a list of tags on the site
		$tagsModel = ED::model('Tags');
		$tags = $tagsModel->getTags();

		// Get post priorities
		$priorities = array();

		if ($this->config->get('post_priority')) {
			$prioritiesModel = ED::model('Priorities');
			$priorities = $prioritiesModel->getPriorities();
		}

		// Get minimum requirement for title.
		$minimumTitle = $this->config->get('antispam_minimum_title');

		// Determines if captcha should be enabled
		$captcha = ED::captcha();

		// Prepare the cancel link
		$cancel = EDR::_('view=forums');

		if ($post->id) {
			$cancel = $post->getPermalink();

			// Post is moderated
			if ($post->isPending() && ED::isSiteAdmin()) {
				$cancel = EDR::_('view=dashboard');
			}
		}

		// This fix for elements with non-unique id. #818
		$uid = uniqid();

		$this->set('uid', $uid);
		$this->set('defaultCategory', $defaultCategory);
		$this->set('minimumTitle', $minimumTitle);
		$this->set('cancel', $cancel);
		$this->set('post', $post);
		$this->set('categories', $categories);
		$this->set('postTypes', $postTypes);
		$this->set('captcha', $captcha);
		$this->set('tags', $tags);
		$this->set('priorities', $priorities);
		$this->set('redirect', $redirect);
		$this->set('composer', $composer);
		$this->set('attachments', $attachments);
		$this->set('editor', $editor);
		$this->set('moderatorList', $moderatorList);
		$this->set('clusterId', $clusterId);

		parent::display('ask/default');
	}

	private function getSessionData(&$post)
	{
		// Get form values from session.
		$data = ED::getSession('NEW_POST_TOKEN');

		// echo '<pre>';
		// var_dump($data);
		// echo '</pre>';


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

			if (isset($data['pollitems']) && is_array($data['pollitems'])) {
				$polls = array();

				foreach ($data['pollitems'] as $key => $value) {
					$poll = ED::table('Poll');
					$poll->id = '';
					$poll->value = $value;

					$polls[] = $poll;
				}

				$post->post->setPolls($polls);

				$poll = ED::table('PollQuestion');
				$poll->title = isset($data['poll_question']) ? $data['poll_question'] : '';
				$poll->multiple = isset($data['multiplePolls']) ? $data['multiplePolls'] : false;

				$post->post->setPollQuestions($poll, true);
			}

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
}
