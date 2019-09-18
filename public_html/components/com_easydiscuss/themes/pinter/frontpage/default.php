<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="discuss-list" data-posts data-view="index" data-category="0">
	<?php if ($featured) { ?>
		<h3 class="ed-page-title"><?php echo JText::_('COM_EASYDISCUSS_FEATURED_POSTS'); ?></h3>
		<div class="ed-posts-list" itemscope itemtype="http://schema.org/ItemList">
			<?php foreach ($featured as $featuredPost) { ?>
				<?php echo $this->output('site/posts/item', array('post' => $featuredPost)); ?>
			<?php } ?>
		</div>
	<?php } ?>

	<div class="ed-filters">
		<?php echo $this->output('site/frontpage/filters', array('baseUrl' => 'view=index', 'activeFilter' => $activeFilter, 'activeSort' => $activeSort, 'menuCatId' => $menuCatId, 'view' => $view, 'activeSort' => $activeSort)); ?>
	</div>

	<div class="ed-posts-list <?php echo !$posts ? 'is-empty' : '';?>" data-list-item itemscope itemtype="http://schema.org/ItemList">
		<?php if ($posts) { ?>
			<?php foreach ($posts as $post) { ?>
				<?php echo $this->output('site/posts/item', array('post' => $post)); ?>
			<?php } ?>
		<?php } ?>

		<div class="o-loading">
			<div class="o-loading__content">
				<i class="fa fa-spinner fa-spin"></i>
			</div>
		</div>

		<div class="o-empty o-empty--bordered">
			<div class="o-empty__content">
				<i class="o-empty__icon fa fa-flash"></i><br /><br />
				<div class="o-empty__text"><?php echo JText::_('COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST');?></div>
			</div>
		</div>
	</div>

	<div class="" data-frontpage-pagination>
		<?php echo $pagination->getPagesLinks();?>
	</div>

	<?php echo $this->html('forums.stats'); ?>
</div>
