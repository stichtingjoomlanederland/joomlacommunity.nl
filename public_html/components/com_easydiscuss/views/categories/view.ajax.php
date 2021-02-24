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

class EasyDiscussViewCategories extends EasyDiscussView
{
	/**
	 * Renders a popbox for category
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function popbox()
	{
		$id = $this->input->get('id', 0, 'int');

		// guest should not allowed.
		if (!$id) {
			return $this->ajax->fail(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'));
		}

		$category = ED::category($id);

		$theme = ED::themes();
		$theme->set('category', $category);
		$contents = $theme->output('site/popbox/category');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Renders list of categories for filter
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getCategoriesForFilter()
	{
		$activeCategoryId = $this->input->get('activeCategoryId', 0, 'int');
		$parentCategory = $this->input->get('parent_id', 0, 'int');

		$model = ED::model('categories');
		$categories = $model->getChildCategories($parentCategory);

		if ($categories) {
			foreach ($categories as &$category) {
				$category = ED::category($category);
			}
		}

		$activeCategory = ED::category();

		if ($activeCategoryId) {
			$activeCategory = ED::category($activeCategoryId);
		}

		$theme = ED::themes();
		$theme->set('rootLevel', false);
		$theme->set('activeCategory', $activeCategory);
		$theme->set('categories', $categories);
		$contents = $theme->output('site/helpers/post/filters/category.nested');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Display items in categories
	 *
	 * @since	4.0.22
	 * @access	public
	 */
	public function filter()
	{
		$categoryId = $this->input->get('category_id', 0, 'int');
		$registry = new JRegistry();

		// If the category isn't found on the site throw an error.
		if (!$categoryId) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_CATEGORY_NOT_FOUND'));
		}

		// Get the pagination limit
		$limit = $registry->get('limit', 5);
		$limit = ($limit == '-2') ? ED::getListLimit() : $limit;
		$limit = ($limit == '-1') ? $this->jconfig->get('list_limit') : $limit;

		// Add view to this page.
		$this->logView();

		// Get list of categories on the site.
		$model = ED::model('Posts');

		// Get the current active category
		$activeCategory = ED::category($categoryId);

		// If user trying to access this category page but he didn't get allowed, show error.
		if (!$activeCategory->canAccess()) {
			return $this->ajax->reject(JText::_('COM_ED_CATEGORY_NOT_ALLOWED'));
		}

		// determine if we should retrive posts from it sub categories or not.
		$includeChilds = false;
		if ($activeCategory->isContainer()) {
			$includeChilds = true;
		}

		$activeSort = $this->input->get('sort', 'latest', 'default');
		$activeFilter = $this->input->get('filter', 'allposts', 'string');

		$options = array(
						'sort' => $activeSort,
						'limitstart' => $this->input->get('limitstart', 0),
						'filter' => $activeFilter,
						'category' => $categoryId,
						'limit' => $this->config->get('layout_single_category_post_limit', $limit),
						'userId' => $this->my->id,
						'includeChilds' => $includeChilds,
						'featuredSticky' => true
					);

		// Get all the posts in this category and it's childs
		$posts = $model->getDiscussions($options);

		$threads = array();

		if ($posts) {

			// Preload the post id's.
			ED::post($posts);

			// Format normal entries
			$posts = ED::formatPost($posts);

			$thread = new stdClass();
			$thread->category = $activeCategory;
			$thread->posts = array();
			$threads[$activeCategory->id] = $thread;
			$threads[$activeCategory->id]->posts = $posts;
		}

		// Get the pagination
		$filtering = array();

		$filtering['category_id'] = $activeCategory->id;
		$filtering['sort'] = $activeSort;

		if ($activeFilter != 'allposts') {
			$filtering['filter'] = $activeFilter;
		}

		$pagination = $model->getPagination();
		$paginationHtml = $pagination->getPagesLinks('categories', $filtering, true);

		$themes = ED::themes();
		$themes->set('threads', $threads);
		$output = $themes->output('site/forums/threads');

		return $this->ajax->resolve($output, $paginationHtml);
	}

	/**
	 * get categories post count
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPostCount()
	{
		$ids = $this->input->get('ids', array(), 'Array');

		$data = array();

		if ($ids) {
			foreach ($ids as $id) {

				$id = (int) $id;

				if ($id) {
					$model = ED::model('Category');
					$count = $model->getTotalPosts($id);
					$text = ED::string()->getNoun('COM_ED_POST_COUNT', $count, true);
					
					$obj = new stdClass();
					$obj->id = $id;
					$obj->count = $count;
					$obj->text = $text;

					$data[] = $obj;
				}
			}
		}

		return $this->ajax->resolve($data);
	}
}
