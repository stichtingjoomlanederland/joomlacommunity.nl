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
defined('_JEXEC') or die('Restricted access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewAsk extends EasyDiscussView
{
	/**
	 * Responds to the getcategory ajax call by return a list of category items.
	 *
	 * @access	public
	 * @param	null
	 */
	public function getCategory()
	{
		$id = $this->input->get('id', 0, 'int');
		$model = ED::model('categories');
		$items = $model->getChildCategories($id, true, true);

		if (!$items) {
			return $this->ajax->resolve(array());
		}

		$categories = array();

		for ($i = 0; $i < count($items); $i++) {
			
			$item = $items[$i];

			$category = ED::table('Category');
			$category->load($item->id);

			$item->hasChild = $category->getChildCount();
		}

		$this->ajax->resolve($items);
	}

	public function buildcategorytier()
	{
		$id = $this->input->get('id', 0, 'int');

		if (empty($id)) {
			return $this->ajax->reject();
		}

		$loop = true;
		$scategory = ED::table('Category');
		$scategory->load($id);

		$model = ED::model('categories');
		$tier = array();

		$searchId = $scategory->parent_id;
		
		while($loop) {
			
			if (empty($searchId)) {
				$loop = false;
			} else {
				$category = ED::table('Category');
				$category->load($searchId);
				$tier[]	= $category;

				$searchId = $category->parent_id;
			}
		}

		// get the root tier
		$root = array_pop($tier);

		//reverse the array order
		$tier = array_reverse($tier);
		array_push($tier, $scategory);

		$categories = array();
		
		foreach ($tier as $cat) {
			$pItem = new stdClass();
			$pItem->id = $cat->id;
			$pItem->parent_id = $cat->parent_id;
			$pItem->hasChild = 1;

			$items = $model->getChildCategories($cat->parent_id, true, true);

			if (!$items) {
				$pItem->hasChild = 0;
				$categories[] = $pItem;
				continue;
			}

			for ($i = 0; $i < count($items); $i++) {
				$item = $items[$i];

				$category = ED::table('Category');
				$category->load($item->id);

				$item->hasChild = $category->getChildCount();
			}

			$pItem->childs = $items;
			$categories[] = $pItem;
		}

		$this->ajax->resolve($categories);
	}
}
