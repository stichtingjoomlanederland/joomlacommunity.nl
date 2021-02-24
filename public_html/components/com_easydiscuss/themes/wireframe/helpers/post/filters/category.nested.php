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
?>
<ul class="ed-filter-menu ed-filter-menu--nested" data-category-nested>
	<li class="ed-filter-menu__item ed-filter-menu__item-back" data-category-back>
		<a href="javascript:void(0);" class="ed-filter-menu__link">
			<i class="fa fa-angle-left"></i>&nbsp; <?php echo JText::_('COM_ED_BACK'); ?>
		</a>
	</li>

	<?php foreach ($categories as $category) { ?>
	<li class="ed-filter-menu__item <?php echo $activeCategory->id == $category->id ? 'is-active' : '';?>" data-category-filter="<?php echo $category->id;?>">
		<a href="javascript:void(0);"
			title="<?php echo $this->html('string.escape' , $category->getTitle());?>"
			<?php if ($category->isContainer()) { ?>
			data-category-nav
			<?php } else { ?>
			data-ed-filter="category"
			data-id="<?php echo $category->id;?>"
			<?php } ?>
			class="ed-filter-menu__link">
			<?php echo $this->html('category.identifier', $category); ?>&nbsp; <?php echo $category->getTitle();?>
		</a>
		
		<?php if ($category->hasChildren()) { ?>
			<a href="javascript:void(0);" class="ed-filter-menu__toggle" data-category-nav>
				<i class="fa fa-angle-right"></i>
			</a>
		<?php } ?>
	</li>
	<?php } ?>
</ul>
