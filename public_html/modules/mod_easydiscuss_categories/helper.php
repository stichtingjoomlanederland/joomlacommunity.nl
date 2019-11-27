<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

class modEasydiscussCategoriesHelper
{
	public static function getData($params)
	{
		$db = ED::db();

		$hideEmptyPost = $params->get('hideemptypost', '0');
		$displayType = $params->get('layouttype', 'flat');

		$order = $params->get('order', 'default');
		$sort = $params->get('sort', 'asc');
		$count = (int) trim($params->get('count', 0));

		$options = array();
		$options['ordering'] = $order;
		$options['sorting'] = $sort;

		$excludeChild = $params->get('exclude_child_categories', false);

		if ($excludeChild) {
			$options['showSubCategories'] = false;
		} else {
			$options['showSubCategories'] = true;
		}

		$options['showPostCount'] = true;
		$options['limit'] = $count;

		// retrieve certain category to show
		$inclusionCatId = $params->get('category', array(), 'array');
		$options['categoryIds'] = $inclusionCatId;

		$categories = array();

		if ($displayType == 'tree') {
			// if this is a tree, we will limit the count manually
			$options['limit'] = 0;
		} else {
			// if this is a flat layout, we do not 'sort based on the parent'
			$options['sortParentChild'] = false;
		}

		$catsModel = ED::model('categories');
		$categories = $catsModel->getCategoryTree(array(), $options);

		$model = ED::model('category');

		// we need to manually do some grouping here.
		$parents = array();

		if ($categories) {
			// get parents
			foreach ($categories as $category) {
				if ($params->get('hideemptypost', false) && $category->postCount <= 0) {
					continue;
				}

				// Get the total subcategories based on permission
				$totalSubcategories = 0;
				$model->getTotalViewableChilds($category->id, $totalSubcategories);
				$category->totalSubcategories = $totalSubcategories;

				if ($displayType == 'tree') {
					if (!$category->parent_id && !$category->depth) {
						$parents[$category->id] = $category;
					}
				} else {
					$parents[] = $category;
				}
			}

			if ($displayType == 'tree') {
				// now assign childs into parents
				foreach ($parents as $parent) {

					$parentid = $parent->id;
					$lft = $parent->lft;
					$rgt = $parent->rgt;

					$childs = array();

					foreach ($categories as $category) {
						if ($category->lft > $lft && $category->lft < $rgt) {

							if ($params->get('hideemptypost', false) && $category->postCount <= 0) {
								continue;
							}

							$childs[] = $category;
						}
					}

					$parent->childs = $childs;
				}
			}
		}

		if ($displayType == 'tree' && $count) {
			$parents = array_slice($parents, 0, $count);
		}

		return $parents;
	}

	public static function printTree($child, $level, $params)
	{
		$wrapperStart = '<ul class="ed-tree__list">';
		$wrapperEnd = '</ul>';

		$addWrapper = false;
		$tree = '';

		foreach ($child as $item) {
			if ($item->parent_id == $level) {

				$addWrapper = true;

				$output = '';
				$category = $item;

				ob_start();
					require(JModuleHelper::getLayoutPath('mod_easydiscuss_categories', 'tree_item'));
					$output = ob_get_contents();
				ob_end_clean();

				$tree = $tree . '<li class="ed-tree__item">' . $output . self::printTree($child, $item->id, $params) . "</li>";
			}
		}

		$tree = $addWrapper ? $wrapperStart . $tree . $wrapperEnd : $tree;
		return $tree;
	}
}
