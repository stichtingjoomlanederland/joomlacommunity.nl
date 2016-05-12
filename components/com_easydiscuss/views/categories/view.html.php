<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewCategories extends EasyDiscussView
{
	/**
	 * Generates the categories listing
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tmpl = null)
	{
		// Set the pathway
		$this->setPathway(JText::_('COM_EASYDISCUSS_BREADCRUMBS_CATEGORIES'));

		ED::setPageTitle(JText::_('COM_EASYDISCUSS_CATEGORIES_TITLE'));

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
		parent::display('categories/default');

	}

	public function listings()
	{
		$categoryId = $this->input->get('category_id', 0, 'int');
		$registry = new JRegistry();

		// Try to detect if there's any category id being set in the menu parameter.
		$activeMenu = $this->app->getMenu()->getActive();

		// If there is an active menu, render the params
		if ($activeMenu && !$categoryId) {
			$registry->loadString($activeMenu->params);

			if ($registry->get('category_id')) {
				$categoryId	= $registry->get('category_id');
			}
		}

		// Get the pagination limit
		$limit = $registry->get('limit',5);
		$limit = ($limit == '-2') ? ED::getListLimit() : $limit;
		$limit = ($limit == '-1') ? $this->jconfig->get('list_limit') : $limit;

		// Add view to this page.
		$this->logView();


		// // Set the meta of the page.
		// ED::setMeta();

		// Add rss feed into headers
		ED::feeds()->addHeaders('index.php?option=com_easydiscuss&view=categories&layout=listings&category_id=' . $categoryId);

		// Get list of categories on the site.
		$model = ED::model('Posts');

		$options = array(
						'sort' => $registry->get('sort'),
						'limitstart' => $this->input->get('limitstart', 0),
						'filter' => $registry->get('filter'),
						'category' => $categoryId,
						'limit' => $this->config->get('layout_post_category_limit', $limit),
						'userId' => $this->my->id,
						'includeChilds' => false
					);

		// Get all the posts in this category and it's childs
		$posts = $model->getDiscussions($options);

		$threads = array();

		if ($posts) {

			// Preload the post id's.
			ED::post($posts);

			// Format normal entries
			$posts = ED::formatPost($posts);

			// Grouping the posts based on categories.
			foreach ($posts as $post) {

				if (!isset($threads[$post->category_id])) {
					$thread = new stdClass();
					$thread->category = ED::category($post->category_id);
					$thread->posts = array();

					$threads[$post->category_id] = $thread;
				}

				$threads[$post->category_id]->posts[] = $post;
			}
		}

		// Get the current active category
		$activeCategory = ED::category($categoryId);
		$breadcrumbs = $activeCategory->getBreadcrumbs();

		if ($breadcrumbs) {
			foreach ($breadcrumbs as $bdc) {
				$this->setPathway($bdc->title, $bdc->link);
			}

			$this->setPathway(JText::_('COM_EASYDISCUSS_FORUMS_BREADCRUMB_LAYOUT'));
		}

		ED::setPageTitle($activeCategory->title);

		// Get the pagination
		$pagination = $model->getPagination();


		// get cats immediate childs
		$childs = ED::category()->getChildCategories($categoryId, true);


		$subcategories = array();

		if ($childs) {
			foreach($childs as $item) {
				$c = ED::category($item);
				$subcategories[] = $c;
			}
		}

		$this->set('listing', true);
		$this->set('breadcrumbs', $breadcrumbs);
		$this->set('activeCategory', $activeCategory);
		$this->set('threads', $threads);
		$this->set('pagination', $pagination);
		$this->set('includeChild', false);
		$this->set('childs', $subcategories);

		parent::display('forums/listings');

	}

}
