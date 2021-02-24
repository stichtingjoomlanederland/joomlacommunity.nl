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
<div class="o-card o-card--ed-active-category">
	<div class="o-card__body">
		<div class="t-text--center l-stack">
			<div>
				<a href="<?php echo $category->getPermalink();?>">
					<?php echo $this->html('category.identifier', $category, 'md'); ?>
				</a>
			</div>
			
			<h2 class="o-title t-text--truncate t-mb--no">
				<a href="<?php echo $category->getPermalink();?>" class="t-text--700"><?php echo $category->getTitle(); ?></a>
			</h2>

			<?php if ($description && $category->getParams()->get('show_description')) { ?>
			<div class="o-body t-text--600">
				<?php echo $description; ?>
			</div>
			<?php } ?>

			<?php if ($category->canPost()) { ?>
			<div>
				<a href="<?php echo $category->getAskPermalink();?>" class="o-btn o-btn--primary">
					<i class="far fa-edit"></i>&nbsp; <?php echo JText::_('COM_ED_NEW_POST');?>
				</a>
			</div>
			<?php } ?>
		</div>
	</div>
	<div class="o-card__footer">
		<div class="g-list-flex g-list-flex--separator t-justify-content--c o-descriptions">

			<?php if (!$category->isSubcategory()) { ?>
			<div class="g-list-flex__item">
				<a href="<?php echo EDR::_('view=categories');?>" class="t-text--600">
					<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? '→' : '←';?> <?php echo JText::_('COM_ED_BACK'); ?> 
				</a>
			</div>
			<?php } ?>
			
			<?php if ($category->isSubcategory()) { ?>
			<div class="g-list-flex__item">
				<a href="<?php echo $category->getParent()->getPermalink();?>" class="t-text--600">
					<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? '→' : '←';?> <?php echo JText::_('COM_ED_BACK'); ?> 
				</a>
			</div>
			<?php } ?>

			<?php if (!$category->container && $this->config->get('main_rss')) { ?>
			<div class="g-list-flex__item t-text--600">•</div>
			<div class="g-list-flex__item">
				<a href="<?php echo $category->getRSSPermalink();?>" class="t-text--600">
					<i class="fas fa-rss"></i>&nbsp; <?php echo JText::_('COM_ED_SUBSCRIBE_VIA_RSS_READER');?>
				</a> 
			</div>
			<?php } ?>

			<?php if ($renderSubscription) { ?>
			<div class="g-list-flex__item t-text--600">•</div>
			
			<div class="g-list-flex__item">
				<?php echo ED::subscription()->html($this->my->id, $category->id, 'category', [
					'customClass' => 't-text--600'
				]); ?>
			</div>
			<?php } ?>

			<div class="g-list-flex__item t-text--600">•</div>
			<div class="g-list-flex__item" data-ed-post-counter data-id="<?php echo $category->id; ?>">
				<div class="o-loader o-loader--sm o-loader--inline is-active">&nbsp;</div>
			</div>
			
		</div>
	</div>
</div>


