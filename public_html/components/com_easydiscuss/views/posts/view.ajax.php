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

class EasyDiscussViewPosts extends EasyDiscussView
{
	/**
	 * Allows caller to render discussions with specific filters
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function render()
	{
		// Retrieve the base url so that we can provide the routed version of the url
		$baseUrl = $this->input->get('baseUrl', '', 'default');
		$baseUrl = base64_decode($baseUrl);

		parse_str($baseUrl, $query);

		$view = isset($query['view']) && $query['view'] ? $query['view'] : '';
		$category_id = isset($query['category_id']) && $query['category_id'] ? $query['category_id'] : '';
		$layout = isset($query['layout']) && $query['layout'] ? $query['layout'] : '';

		// Allows caller to filter posts by a specific filter type. Supported filters:
		// all, mine, favourites, assigned, answer, unanswered, locked, unread, unresolved, resolved, featured, questions, pending
		$filterType = $this->input->get('filter', 'all', 'word');

		// Allows caller to filter posts by providing an array of category ids
		$categoryId = $this->input->get('categoryIds', 0, 'int');

		// Allows caller to filter posts by providing an array of tag ids
		$tagId = $this->input->get('tagIds', 0, 'int');

		// Filter posts by priority
		$postPriorities = $this->input->get('postPriorities', array(), 'int');

		// Allows caller to filter posts by post types
		$postTypes = $this->input->get('postTypes', '', 'array');

		// Allows caller to filter posts by labels
		$postLabels = $this->input->get('postLabels', array(), 'int');

		// Allows caller to set ordering for result set
		$sort = $this->input->get('sort', 'latest', 'word');
		$sortDirection = $this->input->get('sortDirection', 'DESC', 'word');

		// Determines if we should build the routed url with category in mind
		$routeWithCategory = $this->input->get('routeWithCategory', true, 'bool');

		// Determine if the caller wants to limit the number of posts
		$limit = (int) $this->input->get('limit', ED::getListLimit(), 'int');

		// here we need to determine with limit we need to use.
		if ($view == 'categories' && $layout == 'listings') {
			$limit = $this->config->get('layout_single_category_post_limit', $limit);
		}

		$search = $this->input->get('search', '', 'string');

		$postsModel = ED::model('Posts');

		// Build url that should be used in the browser address bar
		$routedUrl = $this->buildUrl($baseUrl, $categoryId, $filterType, $postLabels, $postTypes, $postPriorities, $sort, $routeWithCategory);

		// If we are searching, we should merge the featured posts and not select them independantly
		$featured = [];

		if (!$search) {
			// Get featured posts from this particular category.
			$featuredOptions = [
				'pagination' => false,
				'category' => $categoryId,
				'tag' => $tagId,
				'filter' => $filterType,
				'sort' => 'latest',
				'postTypes' => $postTypes,
				'postLabels' => $postLabels,
				'postPriorities' => $postPriorities,
				'featured' => true,
				'limit' => DISCUSS_NO_LIMIT
			];

			$featured = $postsModel->getDiscussions($featuredOptions);
		}

		// Get normal discussion posts.
		$options = [
			'category' => $categoryId,
			'tag' => $tagId,
			'sort' => $sort,
			'sortDirection' => $sortDirection,
			'filter' => $filterType,
			'limit' => $limit,
			'postTypes' => $postTypes,
			'postLabels' => $postLabels,
			'postPriorities' => $postPriorities,
			'featured' => false
		];

		// Search for posts
		if ($search) {
			// this is most likely in search page.
			// we want to search featured posts as well.
			$options['search'] = $search;
			$options['searchIncludeReplies'] = true;
		}

		$posts = $postsModel->getDiscussions($options);
		$pagination = $postsModel->getPagination();

		// here we need to add the require vars into pagination links.
		$pagination = $this->buildPagination($baseUrl, $pagination, $categoryId, $filterType, $postLabels, $postTypes, $postPriorities, $sort);

		if (!$featured && !$posts) {
			return $this->ajax->resolve([], $routedUrl);
		}

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

		$theme = ED::themes();
		$theme->set('featured', $featured);
		$theme->set('posts', $posts);
		$theme->set('pagination', $pagination);
		if ($search) {
			$theme->set('isSearch', true);
		}
		$contents = $theme->output('site/posts/list');

		// If this request is trying to perform a search, we should also try to get the search header for the caller
		$searchResult = '';

		if ($search) {
			$searchResult = $theme->html('search.header', $search, $pagination->total);
		}

		return $this->ajax->resolve($contents, $routedUrl, $searchResult);
	}

	/**
	 * method to add require vars into pagination
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function buildPagination($baseUrl, $pagination, $categoryId, $filterType, $postLabels, $postTypes, $postPriorities, $sort)
	{
		parse_str($baseUrl, $query);

		$view = isset($query['view']) && $query['view'] ? $query['view'] : '';
		$category_id = isset($query['category_id']) && $query['category_id'] ? $query['category_id'] : '';
		$layout = isset($query['layout']) && $query['layout'] ? $query['layout'] : '';

		if ($query) {
			foreach ($query as $key => $val) {
				$pagination->setVar($key, $val);
			}
		}

		$pagination->setVar('filter', $filterType);

		if ($categoryId && $view != 'categories' && !$category_id) {
			$pagination->setVar('category', $categoryId);
		}

		if ($sort) {
			$pagination->setVar('sort', $sort);
		}

		if ($postLabels) {
			$i = 0;
			foreach ($postLabels as $item) {
				$pagination->setVar("labels[" . $i++ . "]", $item);
			}
		}

		if ($postTypes) {
			$i = 0;
			foreach ($postTypes as $item) {
				$pagination->setVar("types[" . $i++ . "]", $item);
			}
		}

		if ($postPriorities) {
			$i = 0;
			foreach ($postPriorities as $item) {
				$pagination->setVar("priorities[" . $i++ . "]", $item);
			}
		}

		return $pagination;
	}

	/**
	 * Builds url structure for post filtering
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function buildUrl($baseUrl, $categoryId, $filterType, $postLabels, $postTypes, $postPriorities, $sort, $routeWithCategory)
	{
		// Url to be updated on the browser
		$url = $baseUrl . '&filter=' . $filterType;

		if ($categoryId && $routeWithCategory) {
			$url .= '&category=' . $categoryId;
		}

		if ($sort) {
			$url .= '&sort=' . $sort;
		}

		$queryString = [];

		if ($postLabels) {
			$queryString['labels'] = array_values($postLabels);
		}

		if ($postTypes) {
			$queryString['types'] = array_values($postTypes);
		}

		if ($postPriorities) {
			$queryString['priorities'] = array_values($postPriorities);
		}

		$query = http_build_query($queryString);

		$url .= '&' . $query;

		// Provide the current url so that it can be updated on the browser
		$routedUrl = EDR::_($url, false);

		return $routedUrl;
	}
}
