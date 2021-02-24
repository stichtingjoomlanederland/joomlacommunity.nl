<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="ed" class="ed-mod ed-mod--categories <?php echo $lib->getModuleWrapperClass();?>">
	<div class="ed-mod-card">
		<div class="ed-mod-card__body">
			<div class="dl-menu-wrapper">
				<div class="ed-filters" data-ed-filters data-category="">	
					<div class="" data-ed-category-container>
						<div class="ed-filter-menu ed-filter-menu--parent o-tabs--dlmenu " data-ed-category-group>
							<?php foreach ($categories as $category) { ?>
							<div class="ed-filter-menu__item t-mb--sm <?php echo $activeCategory->id == $category->id ? 'active' : '';?>" data-category-filter="<?php echo $category->id;?>">
								<?php echo $helper->getCategoryItem($category, $params); ?>

								<?php echo $helper->getNestedCategories($category->id, $params, $activeCategory); ?>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>