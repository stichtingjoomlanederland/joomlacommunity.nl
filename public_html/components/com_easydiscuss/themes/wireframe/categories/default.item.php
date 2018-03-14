<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-cat-item">
	<div class="o-flag">
		<?php if ($this->config->get('layout_category_show_avatar', true)) { ?>
		<div class="o-flag__image o-flag--top">
			<a class="o-avatar o-avatar--md" href="<?php echo EDR::getCategoryRoute($category->id);?>">
				<img src="<?php echo $category->getAvatar();?>" alt="<?php echo $this->html('string.escape', $category->getTitle());?>" />
			</a>
		</div>
		<?php } ?>
		<div class="o-flag__body">
			<a class="ed-cat-name" href="<?php echo EDR::getCategoryRoute($category->id); ?>"><?php echo $category->getTitle();?></a>
			<?php if (!$this->config->get('layout_category_description_hidden')) { ?>
			<ol class="g-list-inline g-list-inline--delimited ed-cat-item-meta">
				<?php echo $category->getDescription(); ?>
			</ol>
			<?php } ?>
			<ol class="g-list-inline g-list-inline--delimited ed-cat-item-meta">
				<li><?php echo $this->getNouns('COM_EASYDISCUSS_ENTRY_COUNT', $category->getTotalPosts(), true);?></li>

				<li data-breadcrumb="·"><?php echo $this->getNouns('COM_EASYDISCUSS_CATEGORIES_SUBCATEGORIES_COUNT', $category->totalSubcategories, true);?></li>

				<?php if (!$category->container) { ?>
					<?php if ($this->config->get('main_rss')) { ?>
					<li data-breadcrumb="·">
						<a href="<?php echo $category->getRSSPermalink();?>" target="_blank">
							<i class="fa fa-rss-square"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_RSS_FEED');?>
						</a>
					</li>
					<?php } ?>
				<?php } ?>

			</ol>
		</div>
	</div>
</div>