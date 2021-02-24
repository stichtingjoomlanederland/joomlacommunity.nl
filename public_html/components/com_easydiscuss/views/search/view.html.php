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

class EasyDiscussViewSearch extends EasyDiscussView
{
	public function display($tmpl = null)
	{
		// Set page attributes
		ED::setPageTitle('COM_EASYDISCUSS_SEARCH');
		ED::breadcrumbs('COM_EASYDISCUSS_SEARCH');
		ED::setMeta();

		// Search query
		$query = $this->input->get('query', '', 'string');

		// Determines how we should be filtering the posts
		$filter = $this->input->get('filter', 'all', 'string');
		
		// Allows caller to filter posts by post types
		$postTypes = $this->input->get('types', array(), 'string');

		// Allows caller to filter posts by labels
		$postLabels = $this->input->get('labels', array(), 'int');

		// Allows caller to filter posts by priority
		$postPriorities = $this->input->get('priorities', array(), 'int');

		// Get active category
		$activeCategory = $this->input->get('category', 0, 'int');

		// Determines if we should be sorting the view
		$activeSort = $this->input->get('sort', 'latest', 'string');

		// Get the pagination limit
		$options = [
			'category' => $activeCategory,
			'sort' => $activeSort,
			'filter' => $filter,
			'postTypes' => $postTypes,
			'postLabels' => $postLabels,
			'postPriorities' => $postPriorities,
			'search' => $query,
			'searchIncludeReplies' => true
		];

		$posts = [];
		$pagination = null;

		if ($query) {
			$postsModel = ED::model('Posts');
			$posts = $postsModel->getDiscussions($options);
			$pagination = $postsModel->getPagination();

			// Only load the data when we really have data
			if ($posts) {
				ED::post($posts);

				// Format normal entries
				if ($posts) {
					$posts = ED::formatPost($posts, false, true);
				}
			}
		}

		$baseUrl = 'view=search&query=' . $query;

		$this->set('activeSort', $activeSort);
		$this->set('activeCategory', $activeCategory);
		$this->set('filter', $filter);
		$this->set('postLabels', $postLabels);
		$this->set('postTypes', $postTypes);
		$this->set('postPriorities', $postPriorities);
		$this->set('baseUrl', $baseUrl);
		$this->set('query', $query);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		
		parent::display('search/listings/default');
	}
}
