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
<div class="o-popbox-content">
	<div class="o-popbox-content__bd">
		<div class="t-p--md">
			<div class="o-media o-media--rev">
				<div class="o-media__image">
					<a href="<?php echo $category->getPermalink();?>">
						<?php echo $this->html('category.identifier', $category, 'md'); ?>
					</a>
				</div>
				<div class="o-media__body">
					<div class="o-title t-text--truncate">
						<a href="<?php echo $category->getPermalink();?>" class="t-text--100"><?php echo $category->getTitle();?></a>
					</div>
				</div>
			</div>
		</div>

		<div class="o-grid o-grid--gutters t-px--md t-mb--sm">
			<div class="o-grid__cell o-grid__cell--6x">
				<div class="t-bg--700 t-rounded--lg t-py--sm t-text--center t-text--truncate">
					<?php echo JText::sprintf('COM_ED_CATEGORY_POPOVER_POSTS', $category->getTotalPosts()); ?>
				</div>
			</div>
			<div class="o-grid__cell o-grid__cell--6x">
				<div class="t-bg--700 t-rounded--lg t-py--sm t-text--center t-text--truncate">
					<?php echo JText::sprintf('COM_ED_CATEGORY_POPOVER_SUBCATEGORIES', $category->getTotalSubcategories()); ?>
				</div>
			</div>
		</div>
	</div>

	<?php if ($ask) { ?>
	<div class="o-popbox-content__ft t-border--600">
		<div class="t-p--md">
			<a href="<?php echo $category->getAskPermalink();?>" class="o-btn o-btn--primary o-btn--block">
				<b>
					<?php echo JText::_('COM_ED_CATEGORY_POPOVER_CREATE_NEW_POST'); ?>
				</b>
			</a>
		</div>
	</div>
	<?php } ?>
</div>
