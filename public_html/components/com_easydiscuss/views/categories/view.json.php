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
		$categoryModel = ED::model('Categories');
		$categories = $categoryModel->getCategoryTree();

		// we need to manually do some grouping here.
		$parents = array();

		if ($categories) {
			// get parents
			foreach ($categories as $category) {
				if (!$category->parent_id && !$category->depth) {
					$parents[$category->id] = $category;
				}
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

		$items = array();

		foreach ($parents as $category) {
			$items[] = $category->toData();
		}

		$this->set('categories', $items);

		parent::display();
	}

}
