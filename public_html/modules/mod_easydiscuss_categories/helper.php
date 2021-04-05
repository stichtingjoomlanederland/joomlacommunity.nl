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

class EasyDiscussModCategoriesHelper
{
	public function getData($params)
	{
		// For flat design, we'll allow the subcategories.
		$showSubCategories = false;

		if ($params->get('layouttype') == 'flat' && !$params->get('exclude_child_categories', false)) {
			$showSubCategories = true;
		}

		$model = ED::model('categories');
		$categories = $model->getCategoryTree([], [
			'showSubCategories' => $showSubCategories,
			'showPostCount' => true,
			'limit' => $params->get('count', 5),
			'ordering' => $params->get('order', 'default'),
			'sorting' => $params->get('sort', 'asc')
		]);

		if ($params->get('hideemptypost', false)) {
			foreach ($categories as $key => $category) {
				if (!$category->postCount) {
					unset($categories[$key]);
					continue;
				}
			}
		}

		return $categories;
	}

	public function getCategoryItem($category, $params)
	{
		ob_start();
			require(JModuleHelper::getLayoutPath('mod_easydiscuss_categories', 'tree_item'));
			$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	public function getNestedCategories($categoryId, $params, $activeCategory)
	{
		if ($params->get('exclude_child_categories', 0)) {
			return ;
		}

		$model = ED::model('categories');
		$categories = $model->getChildCategories($categoryId);
		
		foreach ($categories as $key => &$category) {
			$category = ED::category($category);

			if ($params->get('hideemptypost', false)) {
				if (!$category->getTotalPosts()) {
					unset($categories[$key]);
					continue;
				}
			}
		}

		if (!$categories) {
			return;
		}

		ob_start();?>
		<div class="ed-filter-menu ed-filter-menu--nested t-d--none" data-category-nested>
			<div class="ed-filter-menu__item ed-filter-menu__item-back" data-category-back>
				<a href="javascript:void(0);" class="ed-filter-menu__link">
					<i class="fa fa-angle-left"></i>&nbsp; <?php echo JText::_('COM_ED_BACK'); ?>
				</a>
			</div>

			<?php foreach ($categories as $item) { ?>
				<div class="ed-filter-menu__item <?php echo $activeCategory->id == $item->id ? 'active' : '';?>" data-category-filter="<?php echo $item->id;?>">
					<?php echo $this->getCategoryItem($item, $params); ?>

					<?php echo $this->getNestedCategories($item->id, $params, $activeCategory); ?>
				</div>
				
			<?php } ?>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}
