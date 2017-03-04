<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewPosts extends EasyDiscussAdminView
{
	/**
	 * Renders the list of discussions created on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.posts');

		$this->title('COM_EASYDISCUSS_BREADCRUMB_DISCUSSIONS');
		$this->desc('COM_EASYDISCUSS_POSTS_DESC');

		JToolbarHelper::addNew();
		JToolBarHelper::custom('showMove', 'move', '', JText::_('COM_EASYDISCUSS_MOVE_TOOLBAR'));
		JToolBarHelper::custom('feature', 'featured ', '', JText::_('COM_EASYDISCUSS_FEATURE_TOOLBAR'));
		JToolBarHelper::custom('unfeature', 'star-empty', '', JText::_('COM_EASYDISCUSS_UNFEATURE_TOOLBAR'));
		JToolBarHelper::divider();

		// Display toolbars
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::unpublishList('resetVotes', JText::_('COM_EASYDISCUSS_RESET_VOTES'));
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

		// Selected filter
		$filter = $this->input->get('filter_state', '', 'word');

		// Search query
		$search = $this->input->get('search', '', 'string');
		$search = trim(strtolower($search));

		// Ordering
		$order = $this->getUserState('posts.filter_order', 'filter_order', 'a.id', 'cmd');
		$orderDirection = $this->getUserState('posts.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

		// Filter by category
		$category = $this->input->get('category_id', 0, 'int');

		// Get the dropdown for categories
		$categoryFilter = ED::populateCategories('', '', 'select', 'category_id', $category, true, false , true , true);

		// Fetch the list of posts 
		$model = ED::model('Threaded');
		$options = array('stateKey' => 'posts', 'questions' => true, 'filter' => $filter, 'category' => $category, 'search' => $search);
		$rows = $model->getPosts($options);
		$pagination = $model->getPagination();

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

				// backend link
				$post->editLink = 'index.php?option=com_easydiscuss&view=post&layout=edit&id=' . $post->id;

				// Format the display date
				$post->displayDate = ED::date($post->created)->toSql();

				// display only safe content.
				$post->content = strip_tags($post->content);

				$category = ED::table('Category');
				$category->load($post->category_id);

				$post->category = $category;

				$post->cnt = $post->getTotalReplies();

				$posts[] = $post;
			}
		}


		$this->set('filter', $filter);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('categoryFilter', $categoryFilter);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('posts/default');
	}

	/**
	 * Renders a list of replies at the back end
	 *
	 * @since	4.0.10
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function replies()
	{
		$this->checkAccess('discuss.manage.posts');

		$this->title('COM_EASYDISCUSS_SIDEBAR_REPLIES');

		// Display toolbars
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::unpublishList('resetVotes', JText::_('COM_EASYDISCUSS_RESET_VOTES'));
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

		// Selected filter
		$filter = $this->input->get('filter_state', '', 'word');

		// Search query
		$search = $this->input->get('search', '', 'string');
		$search = trim(strtolower($search));

		$model = ED::model("Threaded");
		$options = array('stateKey' => 'replies', 'replies' => true, 'filter' => $filter, 'search' => $search);
		$result = $model->getPosts($options);
		$pagination = $model->getPagination();
		
		// Format the posts
		$posts = array();

		if ($result) {
			foreach ($result as $row) {
				$post = ED::post($row);
				$post->editLink = 'index.php?option=com_easydiscuss&view=post&layout=edit&id=' . $post->id;

				$posts[] = $post;
			}
		}

		$this->set('filter', $filter);
		$this->set('search', $search);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);

		parent::display('posts/replies');
	}

	/**
	 * Renders a list of pending posts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function pending()
	{
		$this->checkAccess('discuss.manage.posts');

		$this->title('COM_EASYDISCUSS_TITLE_PENDING_POSTS');

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
				$post->editLink = 'index.php?option=com_easydiscuss&view=post&layout=pending&id=' . $post->id;

				$posts[] = $post;
			}
		}
	
		$this->set('search', $search);		
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);

		parent::display('posts/pending');
	}
}
