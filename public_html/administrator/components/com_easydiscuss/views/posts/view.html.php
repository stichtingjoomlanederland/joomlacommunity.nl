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

class EasyDiscussViewPosts extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.posts');

		$this->title('COM_EASYDISCUSS_BREADCRUMB_DISCUSSIONS');
		$this->desc('COM_EASYDISCUSS_POSTS_DESC');

		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();

		JToolBarHelper::custom('feature', 'featured ', '', JText::_('COM_EASYDISCUSS_FEATURE_TOOLBAR'));
		JToolBarHelper::custom('unfeature', 'star-empty', '', JText::_('COM_EASYDISCUSS_UNFEATURE_TOOLBAR'));

		JToolBarHelper::custom('showMove', 'move', '', JText::_('COM_EASYDISCUSS_MOVE_TOOLBAR'));
		JToolbarHelper::unpublishList('resetVotes', JText::_('COM_EASYDISCUSS_RESET_VOTES'));
		JToolbarHelper::custom('lock', 'lock', '', JText::_('COM_EASYDISCUSS_LOCK'));
		JToolbarHelper::custom('unlock', 'unlock', '', JText::_('COM_EASYDISCUSS_UNLOCK'));
		JToolbarHelper::custom('updateAuthor', 'author', '', JText::_('COM_ED_UPDATE_AUTHOR_BUTTON'));
		JToolbarHelper::custom('copy', 'copy', '', JText::_('COM_ED_COPY_BUTTON'));
		JToolbarHelper::deleteList();

		// Determines if the user is browsing to select a post
		$browse = $this->input->get('browse', false, 'bool');
		$browseFunction = $this->input->get('browseFunction', '', 'string');
		$limit = $this->app->getUserStateFromRequest('com_easydiscuss.posts.limit', 'limit', $this->app->getCfg('list_limit') , 'int');
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		// default to 'published' to speed up the query. #770
		$filter = $this->getUserState('posts.filter_state', 'filter_state', '', 'word');

		$search = $this->getUserState('posts.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		// Ordering
		$order = $this->getUserState('posts.filter_order', 'filter_order', 'b.id', 'cmd');
		$orderDirection = $this->getUserState('posts.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

		// Filter by category
		$categoryId = $this->getUserState('posts.category_id', 'category_id', 0, 'int');

		// Get the dropdown for categories
		$categoryFilter = ED::populateCategoryFilter('category_id', $categoryId, 'class="o-form-control" data-table-filter');

		// Selected post status filter
		$selectedPostLabel = $this->getUserState('posts.post_status', 'post_status', '', 'int');

		// Get the dropdown for post status
		$postLabelFilter = ED::populatePostLabelFilter('post_status', $selectedPostLabel);

		// Fetch the list of posts
		$model = ED::model('Threaded');
		$options = array('stateKey' => 'posts', 'questions' => true, 'filter' => $filter, 'category' => $categoryId, 'search' => $search, 'postLabel' => $selectedPostLabel);

		$options['loadPaginationCount'] = false;

		if ($browse) {
			$options['loadPaginationCount'] = true;
		}

		$rows = $model->getPosts($options);
		$pagination = null;

		if ($browse) {
			$pagination = $model->getPagination();
		}

		$posts = array();

		// Format the posts
		if ($rows) {
			foreach ($rows as &$row) {

				$post = ED::post($row);

				if ($post->user_id == '0') {
					$post->creatorName = $post->poster_name;
				} else {
					$user = JFactory::getUser($post->user_id);
					$post->creatorName = $user->name;
				}

				// Redirect the user to the frontend in order to edit it #1027
				$post->editLink = EDR::getRoutedURL('view=ask&id=' . $post->id, false, true);

				// Format the display date
				$post->displayDate = ED::date($post->created)->display(JText::_('DATE_FORMAT_LC5'));

				// display only safe content.
				$post->content = strip_tags($post->content);

				$category = ED::table('Category');
				$category->load($post->category_id);

				$post->category = $category;

				$post->cnt = $post->getTotalReplies();

				$posts[] = $post;
			}
		}

		$this->set('browseFunction', $browseFunction);
		$this->set('browse', $browse);
		$this->set('filter', $filter);
		$this->set('label', $selectedPostLabel);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('categoryFilter', $categoryFilter);
		$this->set('search', $search);
		$this->set('categoryId', $categoryId);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);
		$this->set('limit', $limit);
		$this->set('limitstart', $limitstart);
		$this->set('postLabelFilter', $postLabelFilter);

		parent::display('posts/default');
	}

	/**
	 * Renders a list of replies at the back end
	 *
	 * @since	4.0.10
	 * @access	public
	 */
	public function replies()
	{
		$this->checkAccess('discuss.manage.posts');

		$this->title('COM_EASYDISCUSS_SIDEBAR_REPLIES');

		// Load frontend language
		ED::loadLanguages();

		// Display toolbars
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::unpublishList('resetVotes', JText::_('COM_EASYDISCUSS_RESET_VOTES'));
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

		$limit = $this->app->getUserStateFromRequest('com_easydiscuss.posts.limit', 'limit', $this->app->getCfg('list_limit') , 'int');
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		// Selected filter
		// default to 'published' to speed up the query. #770
		$filter = $this->input->get('filter_state', 'published', 'word');

		// Search query
		$search = $this->input->get('search', '', 'string');
		$search = trim(strtolower($search));

		$model = ED::model("Threaded");
		$options = array('stateKey' => 'replies', 'replies' => true, 'filter' => $filter, 'search' => $search, 'loadPaginationCount' => false);

		$result = $model->getPosts($options);
		// $pagination = $model->getPagination();

		// Format the posts
		$posts = array();

		if ($result) {
			foreach ($result as $row) {
				$post = ED::post($row);

				// Redirect the user to the frontend in order to edit it #1027
				$post->editLink = EDR::getRoutedURL($post->getReplyPermalink(), false, true);

				$posts[] = $post;
			}
		}

		$this->set('filter', $filter);
		$this->set('search', $search);
		$this->set('posts', $posts);
		// $this->set('pagination', $pagination);
		$this->set('limit', $limit);
		$this->set('limitstart', $limitstart);


		parent::display('posts/replies');
	}

	/**
	 * Renders a list of pending posts
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function pending()
	{
		$this->checkAccess('discuss.manage.posts');

		$this->title('COM_EASYDISCUSS_TITLE_PENDING_POSTS');

		// Load frontend language
		ED::loadLanguages();

		// Display toolbars
		JToolbarHelper::publishList('publish', JText::_('COM_EASYDISCUSS_BTN_APPROVE'));
		JToolbarHelper::unpublishList('reject', JText::_('COM_EASYDISCUSS_BTN_REJECT'));
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

		// Search query
		$search = $this->input->get('search', '', 'string');
		$search = trim(strtolower($search));

		$model = ED::model("Threaded");
		$options = array('stateKey' => 'pending', 'pending' => true, 'search' => $search);
		$result = $model->getPosts($options);
		$pagination = $model->getPagination();
		$posts = array();

		if ($result) {
			foreach ($result as $row) {
				$post = ED::post($row);

				// Redirect the user to the frontend in order to edit it #1027
				if ($post->isQuestion()) {
					$post->editLink = EDR::getRoutedURL('view=ask&id=' . $post->id, false, true);
				}

				if ($post->isReply()) {
					$post->editLink = EDR::getRoutedURL($post->getReplyPermalink(), false, true);
				}

				$posts[] = $post;
			}
		}

		$this->set('search', $search);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->addHelpButton('/docs/easydiscuss/administrators/configuration/moderation-in-easydiscuss');

		parent::display('posts/pending');
	}

	/**
	 * Renders a list of pending posts
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function honeypot()
	{
		$this->checkAccess('discuss.manage.posts');

		$this->title('COM_ED_TITLE_HONEYPOT_TRAPS');

		JToolbarHelper::deleteList('COM_ED_CONFIRM_DELETE_HONEYPOT', 'removeHoneypotLog');
		JToolbarHelper::custom('purge', '', '', JText::_('COM_ED_PURGE_LOGS'), false);

		$limit = $this->app->getUserStateFromRequest('com_easydiscuss.posts.limit', 'limit', $this->app->getCfg('list_limit') , 'int');
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		$model = ED::model('Honeypot');
		$items = $model->getItems();
		$pagination = $model->getPagination();

		$this->set('limit', $limit);
		$this->set('items', $items);
		$this->set('pagination', $pagination);

		parent::display('posts/honeypot');
	}

	/**
	 * Redirect the post or reply to the frontend, for now only handle for the report view post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function redirectPost()
	{
		$id = $this->input->get('id', 0, 'int');
		$post = ED::post($id);

		if (!$post->id) {
			ED::setMessage('COM_EASYDISCUSS_INVALID_ID', ED_MSG_ERROR);
			$redirection = EDR::getRoutedURL('view=index', false, true);
			return $this->app->redirect($redirection);
		}

		// If this report someone reported from the question then we can just redirect to that question page without go through any process
		if ($post->isQuestion()) {
			$url = 'index.php?option=com_easydiscuss&view=post&id=' . $post->id;
			$redirection = EDR::getRoutedURL($url, false, true);
			return $this->app->redirect($redirection);
		}

		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

		$redirection = $post->getReplyPermalink();
		$redirection = EDR::getRoutedURL($redirection, false, true);

		return $this->app->redirect($redirection);
	}	
}
