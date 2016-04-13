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

		// Selected filter
		$filter = $this->getUserState('posts.filter_state', 'filter_state', '*', 'word');

		// Search query
		$search = $this->getUserState('posts.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		// Ordering
		$order = $this->getUserState('posts.filter_order', 'filter_order', 'a.id', 'cmd');
		$orderDirection = $this->getUserState('posts.filter_order_Dir', 'filter_order_Dir', '', 'word');

		// If there is a parent id, we need to load all the replies
		$parentId = $this->input->get('pid', 0, 'int');
		$parentTitle = '';

		if ($parentId) {
			$post = ED::table('Posts');
			$post->load($parentId);

			$this->title(JText::sprintf('COM_EASYDISCUSS_BREADCRUMB_VIEWING_REPLIES', $post->title));
			$this->desc('COM_EASYDISCUSS_POSTS_PARENT_DESC');
		} else {
			$this->title('COM_EASYDISCUSS_BREADCRUMB_DISCUSSIONS');
			$this->desc('COM_EASYDISCUSS_POSTS_DESC');

			JToolbarHelper::addNew();
			JToolBarHelper::custom('showMove', 'move', '', JText::_('COM_EASYDISCUSS_MOVE_TOOLBAR'));
			JToolBarHelper::custom('feature', 'featured ', '', JText::_('COM_EASYDISCUSS_FEATURE_TOOLBAR'));
			JToolBarHelper::custom('unfeature', 'star-empty', '', JText::_('COM_EASYDISCUSS_UNFEATURE_TOOLBAR'));
			JToolBarHelper::divider();
		}

		// Display toolbars
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::unpublishList('resetVotes', JText::_('COM_EASYDISCUSS_RESET_VOTES'));
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

		$model = ED::model('Threaded', true);

		$filterCategory	= JRequest::getInt( 'category_id' );
		$categoryFilter = DiscussHelper::populateCategories('', '', 'select', 'category_id', $filterCategory , true, false , true , true);

		$posts = $model->getPosts();
		$pagination = $model->getPagination();

		// Format the posts
		if ($posts) {
			foreach ($posts as &$post) {

				if ($post->user_id == '0') {
					$post->creatorName = $post->poster_name;
				} else {
					$user = JFactory::getUser($post->user_id);
					$post->creatorName = $user->name;
				}

				$pid = '';

				if (!empty($parentId)) {
					$pid = '&pid=' . $parentId;
				}

				// backend link
				$post->editLink = 'index.php?option=com_easydiscuss&view=post&layout=edit&id=' . $post->id;

				// Format the display date
				$post->displayDate = ED::date($post->created)->display(JText::_('DATE_FORMAT_LC2'));

				// display only safe content.
				$post->content = strip_tags($post->content);

				$category = ED::table('Category');
				$category->load($post->category_id);

				$post->category = $category;
			}
		}


		$this->set('filter', $filter);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('categoryFilter', $categoryFilter);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);
		$this->set('parentId', $parentId);
		$this->set('parentTitle', $parentTitle);

		parent::display('posts/default');
	}
}
