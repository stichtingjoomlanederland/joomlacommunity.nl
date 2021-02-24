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

class EasyDiscussThemesHelperSearch
{
	/**
	 * Renders the search header
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function header($query, $total)
	{
		$theme = ED::themes();
		$theme->set('query', $query);
		$theme->set('total', $total);

		$output = $theme->output('site/helpers/search/header');

		return $output;
	}

	/**
	 * Generates the category label
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function category($category)
	{
		$theme = ED::themes();
		$theme->set('category', $category);
		$output = $theme->output('site/helpers/post/category');

		return $output;
	}

	/**
	 * Generates the featured label
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function featured()
	{
		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/featured');

		return $output;
	}

	/**
	 * Renders filters for posts
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function filters($baseUrl, $activeFilter, $activeCategory = null, $activeSort = null, $options = [])
	{
		static $labels = null;
		static $types = null;
		static $priorities = null;
		static $categories = null;

		// Normalize options
		$selectedLabels = ED::normalize($options, 'selectedLabels', []);
		$selectedTypes = ED::normalize($options, 'selectedTypes', []);
		$selectedPriorities = ED::normalize($options, 'selectedPriorities', []);
		$showCategories = ED::normalize($options, 'showCategories', true);
		$showSorting = ED::normalize($options, 'showSorting', true);
		$search = ED::normalize($options, 'search', '');

		$config = ED::config();

		if (is_null($categories) && $showCategories) {
			$model = ED::model('categories');
			$categories = $model->getCategoryTree([], [
				'showSubCategories' => false,
				'showPostCount' => false
			]);
		}

		if (is_null($labels)) {
			$model = ED::model('PostLabels');
			$labels = $model->getLabels();

			if ($labels) {
				foreach ($labels as $label) {
					ED::cache()->set($label, 'labels');
				}
			}
		}

		if (is_null($types)) {

			$types = [];

			if ($config->get('layout_post_types')) {
				$model = ED::model('PostTypes');
				$types = $model->getTypes(true);

				foreach ($types as $type) {
					ED::cache()->set($type, 'posttypes', 'alias');
				}
			}
		}

		if (is_null($priorities)) {
			$priorities = [];

			if ($config->get('post_priority')) {
				$model = ED::model('Priorities');
				$priorities = $model->getAllPriorities();

				foreach ($priorities as $priority) {
					ED::cache()->set($priority, 'priorities');
				}
			}
		}

		$activeLabels = [];

		if ($selectedLabels) {
			// Ensure that they are an array
			if (!is_array($selectedLabels)) {
				$selectedLabels = [$selectedLabels];
			}

			foreach ($selectedLabels as $selectedLabelId) {
				$selectedLabel = ED::cache()->get($selectedLabelId, 'labels');

				$activeLabels[] = $selectedLabel;
			}
		}

		$activePostTypes = [];

		if ($selectedTypes) {
			// Ensure that they are an array
			if (!is_array($selectedTypes)) {
				$selectedTypes = [$selectedTypes];
			}

			foreach ($selectedTypes as $selectedTypeId) {
				$selectedType = ED::cache()->get($selectedTypeId, 'posttypes');

				if ($selectedType) {
					$activePostTypes[] = $selectedType;
				}
			}
		}

		$activePriorities = [];

		if ($selectedPriorities) {
			// Ensure that they are an array
			if (!is_array($selectedPriorities)) {
				$selectedPriorities = [$selectedPriorities];
			}

			foreach ($selectedPriorities as $selectedPriorityId) {
				$selectedPriority = ED::cache()->get($selectedPriorityId, 'priorities');

				if ($selectedPriority) {
					$activePriorities[] = $selectedPriority;
				}
			}
		}

		// Prior to 5.0.x, the all filter was using 'allposts'. We need to change it to 'all'
		if ($activeFilter == 'allposts') {
			$activeFilter = 'all';
		}

		$activeCategory = ED::category($activeCategory);

		$theme = ED::themes();
		$theme->set('search', $search);
		$theme->set('showCategories', $showCategories);
		$theme->set('showSorting', $showSorting);
		$theme->set('activeSort', $activeSort);
		$theme->set('activeCategory', $activeCategory);
		$theme->set('categories', $categories);
		$theme->set('selectedLabels', $selectedLabels);
		$theme->set('selectedTypes', $selectedTypes);
		$theme->set('selectedPriorities', $selectedPriorities);
		$theme->set('activeLabels', $activeLabels);
		$theme->set('activePriorities', $activePriorities);
		$theme->set('activePostTypes', $activePostTypes);
		$theme->set('activeFilter', $activeFilter);
		$theme->set('baseUrl', $baseUrl);
		$theme->set('labels', $labels);
		$theme->set('types', $types);
		$theme->set('priorities', $priorities);
		$output = $theme->output('site/helpers/post/filters');

		return $output;		
	}

	/**
	 * Generates the hidden indicator for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function hidden()
	{
		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/hidden');

		return $output;
	}

	/**
	 * Generates the last replied time for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function lastReplied(EasyDiscussPost $post)
	{
		$replier = $post->getLastReplier();

		if (!$replier) {
			return;
		}

		$theme = ED::themes();
		$theme->set('replier', $replier);
		$theme->set('post', $post);
		$output = $theme->output('site/helpers/post/last.replied');

		return $output;
	}

	/**
	 * Generates the locked label for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function locked()
	{
		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/locked');

		return $output;
	}

	/**
	 * Generates the new label
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function new()
	{
		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/new');

		return $output;
	}

	/**
	 * Renders the custom post label for the post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function label($label)
	{
		$fontColor = '#666';

		if ($label->colour) {
			$color = ED::colors($label->colour);

			if ($color->isDark()) {
				$fontColor = '#fff';
			}
		}
		

		$theme = ED::themes();
		$theme->set('fontColor', $fontColor);
		$theme->set('label', $label);

		$output = $theme->output('site/helpers/post/label');

		return $output;
	}

	/**
	 * Generates the password label for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function password()
	{
		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/password');

		return $output;
	}

	/**
	 * Generates the protected label for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function protectedPost()
	{
		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/protected');

		return $output;
	}

	/**
	 * Renders the list of participants for the post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function participants($participants)
	{
		if (!$participants) {
			return;
		}

		$theme = ED::themes();
		$theme->set('participants', $participants);
		$output = $theme->output('site/helpers/post/participants');

		return $output;
	}

	/**
	 * Renders the list of participants for the post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function priority(EasyDiscussPost $post)
	{
		$priority = $post->getPriority();

		$theme = ED::themes();
		$theme->set('priority', $priority);
		$output = $theme->output('site/helpers/post/priority');

		return $output;
	}

	/**
	 * Generates the replies icon
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function replies(EasyDiscussPost $post)
	{
		$theme = ED::themes();
		$theme->set('post', $post);
		$output = $theme->output('site/helpers/post/replies');

		return $output;
	}

	/**
	 * Generates the resolved label for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function resolved()
	{
		if (!$this->config->get('main_qna')) {
			return;
		}

		$theme = ED::themes();
		$output = $theme->output('site/helpers/post/resolved');

		return $output;
	}

	/**
	 * Renders the title of the post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function title(EasyDiscussPost $post, $enforceMaxLength = true)
	{
		$title = $post->getTitle();
		$maxLength = $this->config->get('layout_title_maxlength');

		if ($enforceMaxLength && EDJString::strlen($title) > $maxLength) {
			$title = EDJString::substr($title, 0, $maxLength) . ' ' . JText::_('COM_ED_ELLIPSES');
		}

		$theme = ED::themes();
		$theme->set('post', $post);
		$theme->set('title', $title);
		
		$output = $theme->output('site/helpers/post/title');
		return $output;
	}

	/**
	 * Generates the post type label for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function type(EasyDiscussPost $post, $useCache = true)
	{
		$type = $post->getPostTypeObject($useCache);

		if (!$type) {
			return;
		}

		$theme = ED::themes();
		$theme->set('post', $post);
		$theme->set('type', $type);

		$output = $theme->output('site/helpers/post/type');

		return $output;
	}
}
