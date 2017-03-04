<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="ed-categories">

	<?php echo $this->output('site/categories/default.item', array('category' => $category, 'showNewPostButton' => $category->canPost())); ?>

	<?php if ($featured) { ?>
	<div class="ed-posts-list" itemscope itemtype="http://schema.org/ItemList">
		<?php foreach ($featured as $featuredPost) { ?>
			<?php echo $this->loadTemplate('site/posts/item.php' , array('post' => $featuredPost)); ?>
		<?php } ?>
	</div>
	<?php } ?>

	<div class="ed-posts-list" itemscope itemtype="http://schema.org/ItemList">
		<div class="loading-bar loader" style="display:none;">
			<div class="discuss-loader"><?php echo JText::_('COM_EASYDISCUSS_LOADING'); ?></div>
		</div>

		<?php if ($posts) { ?>
			<?php foreach ($posts as $post) { ?>
				<?php echo $this->output('site/posts/item', array('post' => $post)); ?>
			<?php } ?>
		<?php } else { ?>
		<div class="empty">
			<?php echo JText::_('COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST');?>
		</div>
		<?php } ?>
	</div>

	<div class="ed-pagination">
		<?php echo $pagination->getPagesLinks();?>
	</div>

</div>