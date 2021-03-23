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
	public function display($tmpl = null)
	{
		// Set the pathway
		if (! EDR::isCurrentActiveMenu('categories')) {
			$this->setPathway(JText::_('COM_EASYDISCUSS_BREADCRUMBS_CATEGORIES'));
		}

		ED::setPageTitle('COM_EASYDISCUSS_CATEGORIES_TITLE');

		// Set the meta for the page
		ED::setMeta();

		// Add view
		$this->logView();

		$categoryModel = ED::model('Categories');
		$model = ED::model('category');
		$categories = $categoryModel->getCategoryTree();

		// we need to manually do some grouping here.
		$parents = array();

		if ($categories) {
			// get parents
			foreach ($categories as $category) {
				if (!$category->parent_id && !$category->depth) {
					$parents[$category->id] = $category;
				}

				// Get the total subcategories based on permission
				$totalSubcategories = 0;
				$model->getTotalViewableChilds($category->id, $totalSubcategories);
				$category->totalSubcategories = $totalSubcategories;
			}

			// now assign childs into parents
			foreach ($parents as $parent) {

				$parentid = $parent->id;
				$lft = $parent->lft;
				$rgt = $parent->rgt;

				$childs = array();

				foreach ($categories as $category) {
					if ($category->lft > $lft && $category->lft < $rgt) {
						$childs[] = $category;
					}
				}

				$parent->childs = $childs;
			}
		}

		$this->set('categories', $parents);
		parent::display('categories/listings/default');
	}

	/**
	 * Single category layout
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function listings()
	{
		$categoryId = $this->input->get('category_id', 0, 'int');
		$view = $this->input->get('view', 'index', 'cmd');
		$registry = new JRegistry();

		// If the category isn't found on the site throw an error.
		if (!$categoryId) {
			throw ED::exception('COM_ED_CATEGORY_NOT_FOUND', ED_MSG_ERROR);
		}

		// Try to detect if there's any category id being set in the menu parameter.
		$activeMenu = $this->app->getMenu()->getActive();

		// If there is an active menu, render the params
		if ($activeMenu && !$categoryId) {
			$registry->loadString($activeMenu->getParams());

			if ($registry->get('category_id')) {
				$categoryId	= $registry->get('category_id');
			}
		}

		// Get the pagination limit
		$limit = $registry->get('limit',5);
		$limit = ($limit == '-2') ? ED::getListLimit() : $limit;
		$limit = ($limit == '-1') ? $this->jconfig->get('list_limit') : $limit;

		// Get the current active category
		$activeCategory = ED::category($categoryId);

		// Set the meta for the page
		ED::setMeta($activeCategory->id, ED_META_TYPE_CATEGORY);

		// Add view to this page.
		$this->logView();

		// Add rss feed into headers
		ED::feeds()->addHeaders($activeCategory->getRSSPermalink(true));

		// Get list of categories on the site.
		$postsModel = ED::model('Posts');

		// If user trying to access this category page but he didn't get allowed, show error.
		if (!$activeCategory->canAccess()) {
			throw ED::exception('COM_ED_CATEGORY_NOT_FOUND', ED_MSG_ERROR);
		}

		// Update the breadcrumbs
		$breadcrumbs = $activeCategory->getBreadcrumbs();

		if (!EDR::isCurrentActiveMenu('categories', $activeCategory->id, 'category_id') && $breadcrumbs) {
			foreach ($breadcrumbs as $bdc) {
				$this->setPathway($bdc->title, $bdc->link);
			}

			$this->setPathway(JText::_('COM_EASYDISCUSS_FORUMS_BREADCRUMB_LAYOUT'));
		}

		// Add canonical tag for this page
		$this->canonical('view=categories&layout=listings&category_id=' . $activeCategory->id);


		$activeSort = $this->input->get('sort', $registry->get('sort'), 'string');
		$activeFilter = $this->input->get('filter', 'all', 'string');

		// Allows caller to filter posts by post types
		$postTypes = $this->input->get('types', array(), 'string');

		// Allows caller to filter posts by labels
		$postLabels = $this->input->get('labels', array(), 'int');

		// Allows caller to filter posts by priority
		$postPriorities = $this->input->get('priorities', array(), 'int');

		// Set the page title
		ED::setPageTitle($activeCategory->getTitle());

		// Get featured posts from this particular category.
		$featuredOptions = [
			'pagination' => false,
			'filter' => $activeFilter,
			'category' => $activeCategory->id,
			'sort' => 'latest',
			'featured' => true,
			'postTypes' => $postTypes,
			'postLabels' => $postLabels,
			'postPriorities' => $postPriorities,
			'limit' => DISCUSS_NO_LIMIT
		];

		$featured = $postsModel->getDiscussions($featuredOptions);


		$options = [
			'filter' => $activeFilter,
			'category' => (int) $activeCategory->id,
			'sort' => $activeSort,
			'limit' => $this->config->get('layout_single_category_post_limit', $limit),
			'postTypes' => $postTypes,
			'postLabels' => $postLabels,
			'postPriorities' => $postPriorities,
			'featured' => false,
			'limitstart' => $this->input->get('limitstart', 0, 'int')
		];

		// Get all the posts in this category and it's childs
		$posts = $postsModel->getDiscussions($options);
		$pagination = $postsModel->getPagination();

		// Only load the data when we really have data
		if ($featured || $posts) {
			ED::post(array_merge($featured, $posts));

			// Format featured entries.
			if ($featured) {
				$featured = ED::formatPost($featured, false, true);
			}

			// Format normal entries
			if ($posts) {
				$posts = ED::formatPost($posts, false, true);
			}
		}

		$subcategories = $activeCategory->getSubcategories();

		// Used in post filters
		$baseUrl = 'view=categories&layout=listings&category_id=' . $activeCategory->id;

		$this->set('postLabels', $postLabels);
		$this->set('postTypes', $postTypes);
		$this->set('postPriorities', $postPriorities);
		$this->set('activeSort', $activeSort);
		$this->set('activeFilter', $activeFilter);
		$this->set('baseUrl', $baseUrl);
		$this->set('featured', $featured);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('subcategories', $subcategories);
		$this->set('activeCategory', $activeCategory);

		return parent::display('categories/item/default');
	}
}
