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
<div class="ed-categories" data-category>
	<div class="l-stack">
		<?php echo $this->html('card.activeCategory', $activeCategory); ?>

		<?php if ($subcategories) { ?>
			<div class="t-d--flex t-align-items--c">
				<div class="t-flex-grow--1">
					<div class="o-title-01"><?php echo JText::sprintf('COM_ED_SUBCATEGORIES_COUNT', count($subcategories));?></div>
				</div>
			</div>
			
			<?php foreach ($subcategories as $subcategory) { ?>
				<?php echo $this->html('card.category', $subcategory); ?>
			<?php } ?>
		<?php } ?>

		<?php echo $this->html('post.filters', $baseUrl, $activeFilter, $activeCategory->id, $activeSort, [
			'showCategories' => false,
			'selectedLabels' => $postLabels, 
			'selectedTypes' => $postTypes, 
			'selectedPriorities' => $postPriorities
		]); ?>

		<div class="<?php echo !$posts && !$featured ? 'is-empty' : '';?>" data-ed-list-wrapper>
			<div class="ed-posts-list l-stack" data-ed-list itemscope itemtype="http://schema.org/ItemList">
				<?php echo $this->output('site/posts/list', [
					'featured' => $featured,
					'posts' => $posts,
					'pagination' => $pagination
				]); ?>
			</div>

			<?php echo $this->html('loading.block'); ?>
			
			<?php echo $this->html('card.emptyCard', 'far fa-newspaper', 'COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST'); ?>
		</div>
	</div>
</div>