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

class EasyDiscussModFiltersHelper extends EasyDiscuss
{
	public function __construct(&$params)
	{
		$this->params = $params;
		$this->input = JFactory::getApplication()->input;
	}

	/**
	 * Retrieves the list of filters for a filter type
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getFilters()
	{
		$type = $this->params->get('type', 'category');
		$method = 'get' . ucfirst($type) . 'Filters';

		$filters = $this->$method();

		return $filters;
	}

	/**
	 * Retrieves labels from the site
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function getLabelsFilters()
	{
		static $labels = array();

		if (empty($labels) && ED::config()->get('main_labels')) {
			$model = ED::model('PostLabels');
			$labels = $model->getLabels();

			$activeLabels = $this->input->get('labels', [], 'int');

			if ($labels) {
				foreach ($labels as &$label) {
					$label->active = false;

					if ($activeLabels) {
						$label->active = in_array($label->id, $activeLabels);
					}
				}
			}				
		}

		return $labels;
	}

	/**
	 * Retrieves post types from the site
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function getTypesFilters()
	{
		static $types = array();

		if (empty($types) && ED::config()->get('layout_post_types')) {

			$catId = $this->input->get('category_id', 0, 'int');

			$model = ED::model('PostTypes');
			$types = $model->getPostTypesOnListings($catId);

			$activeTypes = $this->input->get('types', [], 'word');

			if ($types) {
				foreach ($types as &$type) {
					$type->active = false;

					if ($activeTypes) {
						$type->active = in_array($type->alias, $activeTypes);
					}
				}
			}
		}

		return $types;
	}

	/**
	 * Retrieves priorities from the site
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function getPriorityFilters()
	{
		static $priorities = array();

		if (empty($priorities) && ED::config()->get('post_priority')) {
			$model = ED::model('Priorities');
			$priorities = $model->getAllPriorities();

			$activePriorities = $this->input->get('priorities', [], 'int');
			
			if ($priorities) {
				foreach ($priorities as &$priority) {
					$priority->active = false;

					if ($activePriorities) {
						$priority->active = in_array($priority->id, $activePriorities);
					}
				}
			}
		}

		return $priorities;
	}

	/**
	 * Retrieve category filters from the site
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function getCategoryFilters()
	{
		$model = ED::model('categories');
		$categories = [];
		$categories = $model->getCategoryTree([], [
			'showSubCategories' => false,
			'showPostCount' => false
		]);

		return $categories;
	}

	/**
	 * Retrieves the standard filters for posts
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function getStandardFilters()
	{		
		$filters = [
			'all',
			'unresolved',
			'resolved',
			'unanswered',
			'answered',
			'unread',
			'locked'
		];

		$items = [];

		$active = $this->input->get('filter', 'all', 'word');

		foreach ($filters as $filter) {

			if ($filter != 'all' && !$this->params->get('standard_' . $filter)) {
				continue;
			}
			
			$item = new stdClass();
			$item->filter = $filter;
			$item->title = JText::_('COM_ED_FILTERS_' . strtoupper($filter));
			$item->active = $filter == $active;

			$items[] = $item;
		}

		return $items;
	}

	/**
	 * Determines if the module should be rendered on the current page
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function shouldRender()
	{
		static $shouldRender = null;

		if (is_null($shouldRender)) {
			$option = $this->input->get('option', '', 'option');
			$view = $this->input->get('view', '', 'cmd');
			$layout = $this->input->get('layout', '', 'cmd');
			$id = $this->input->get('id', 0, 'int');

			if ($option != 'com_easydiscuss') {
				$shouldRender = false;

				return false;
			}

			if ($view == 'index' || ($view == 'categories' && $layout == 'listings') || ($view == 'tags' && $id)) {
				$shouldRender = true;

				return true;
			}
		}

		return $shouldRender;
	}
}
