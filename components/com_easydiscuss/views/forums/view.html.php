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

class EasyDiscussViewForums extends EasyDiscussView
{
	/**
	 * Displays the forums layout
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		// If the categoryId is provided, this means that we're in the inner category.
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

		// Set page title.
		ED::setPageTitle();

		// Set the meta of the page.
		ED::setMeta();

		// Add rss feed into headers
		ED::feeds()->addHeaders('index.php?option=com_easydiscuss&view=forums');

		// Get list of categories on the site.
		$model = ED::model('Categories');

		// If the categoryId is provided, this means that we are in the parentCategory view.
		// We'll need to include this parentCategory as well.
		$parentCategory = array();
		if ($categoryId) {
			$parentCategory = is_array($categoryId) ? $categoryId : array($categoryId);
		}

		$options = array(
					'id_only' => true,
					'pagination' => true,
					'limit' => $this->config->get('layout_categories_limit', $limit)
					);

		// Get a list of parent categories
		$parents = $model->getParentCategoriesOnly($categoryId, $options);

		// Join parent with subcategories
		$parents = array_merge($parentCategory, $parents);

		$options = array(
						'sort' => $registry->get('sort'),
						'filter' => $registry->get('filter'),
						'pagination' => true,
						'limit' => $this->config->get('layout_post_category_limit', $limit),
						'includeFeatured' => true,
						'featuredSticky' => true,
						'includeChilds' => $categoryId ? false : true
					);

		// Get all the posts in this category and it's childs
		$posts = $model->getCategoryTreePosts($parents, $options);

		// Preload posts
		ED::post($posts);

		$threads = array();

		if ($posts) {

			// Preload the post id's.
			foreach ($posts as $row) {
				// Load it into our post library
				$post = ED::post($row);

				$lists[] = $post;
			}

			// Grouping the posts based on categories.
			foreach ($lists as $post) {

				if (!isset($threads[$post->cat_parent_id])) {
					$thread = new stdClass();
					$thread->category = ED::category($post->cat_parent_id);
					$thread->posts = array();

					$threads[$post->cat_parent_id] = $thread;
				}

				$threads[$post->cat_parent_id]->posts[] = $post;
			}
		}

		// Get the current active category
		$activeCategory = null;
		$breadcrumbs = null;

		if ($categoryId) {
			$activeCategory = ED::category($categoryId);
			$breadcrumbs = $activeCategory->getBreadcrumbs();

			if ($breadcrumbs) {
				foreach ($breadcrumbs as $bdc) {
					$link = $bdc->link;

					if ($bdc->id == $activeCategory->id) {
						$link = '';
					}

					$this->setPathway($bdc->title, $link);
				}
			}

			ED::setPageTitle($activeCategory->title);
		}
		
		// Get the pagination
		$pagination = $model->getPagination();

		$this->set('listing', false);
		$this->set('breadcrumbs', $breadcrumbs);
		$this->set('activeCategory', $activeCategory);
		$this->set('pagination', $pagination);
		$this->set('threads', $threads);
		// $this->set('includeChilds', true);

		parent::display('forums/default');
	}

	public function getBreadcrumbs()
	{

	}
}
