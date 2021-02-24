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

class EasyDiscussViewIndex extends EasyDiscussView
{
	/**
	 * Displays a list of recent posts list.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Try to detect if there's any category id being set in the menu parameter.
		$activeMenu = $this->app->getMenu()->getActive();

		// Get active category
		$activeCategory = $this->input->get('category', 0, 'int');

		// If there is an active menu, render the params
		$registry = new JRegistry();

		if ($activeMenu && !$activeCategory) {
			$registry->loadString($activeMenu->getParams());

			if ($registry->get('category_id')) {
				$activeCategory = (int) $registry->get('category_id');
			}
		}

		// Determines if we should be sorting the view
		$activeSort = $this->input->get('sort', $registry->get('sort'), 'string');

		// Get the pagination limit
		$limit = (int) $registry->get('limit', '-2');
		$limit = ED::getLimitValue($limit);

		// Add view to this page.
		$this->logView();

		$this->renderHeaders();

		// Get the model.
		$postsModel = ED::model('Posts');

		// Determines how we should be filtering the posts
		$filter = $this->input->get('filter', $registry->get('filter', 'all'), 'string');
		
		// Allows caller to filter posts by post types
		$postTypes = $this->input->get('types', array(), 'string');

		// Allows caller to filter posts by labels
		$postLabels = $this->input->get('labels', array(), 'int');

		// Allows caller to filter posts by priority
		$postPriorities = $this->input->get('priorities', array(), 'int');

		// Get featured posts from this particular category.
		$featuredOptions = [
			'pagination' => false,
			'category' => $activeCategory,
			'filter' => $filter,
			'sort' => 'latest',
			'postTypes' => $postTypes,
			'postLabels' => $postLabels,
			'limit' => DISCUSS_NO_LIMIT,
			'featured' => true
		];

		$featured = $postsModel->getDiscussions($featuredOptions);

		$options = [
			'category' => $activeCategory,
			'sort' => $activeSort,
			'filter' => $filter,
			'limit' => $limit,
			'postTypes' => $postTypes,
			'postLabels' => $postLabels,
			'postPriorities' => $postPriorities,
			'featured' => false
		];

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

		// Base url to use
		$baseUrl = 'view=index';

		$this->set('baseUrl', $baseUrl);
		$this->set('filter', $filter);
		$this->set('postLabels', $postLabels);
		$this->set('postTypes', $postTypes);
		$this->set('postPriorities', $postPriorities);
		$this->set('activeSort', $activeSort);
		$this->set('activeCategory', $activeCategory);
		$this->set('posts', $posts);
		$this->set('featured', $featured);
		$this->set('pagination', $pagination);

		return parent::display('frontpage/default');
	}

	/**
	 * Render headers for this page
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function renderHeaders()
	{
		// Set page title.
		ED::setPageTitle('COM_EASYDISCUSS_TITLE_RECENT');

		// Set the meta of the page.
		ED::setMeta();

		// Add rss feed into headers
		ED::feeds()->addHeaders('index.php?option=com_easydiscuss&view=index');

		// Add canonical tag for this page
		$this->canonical('index.php?option=com_easydiscuss&view=index');
	}
}
