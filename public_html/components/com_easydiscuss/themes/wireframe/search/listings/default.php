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
<div class="ed-search-wrapper">
	<div class="l-stack" data-posts>
		<h2 class="o-title"><?php echo JText::_('COM_EASYDISCUSS_SEARCH'); ?></h2>

		<?php echo ED::renderModule('easydiscuss-search-start'); ?>

		<form name="tags" method="post" action="<?php echo JRoute::_('index.php'); ?>">
			<div class="o-grid t-mb--lg">
				<div class="o-grid__cell">
					<div class="o-input-group">
						<input type="text" name="query"
							value="<?php echo $this->html('string.escape', $query);?>"
							class="o-form-control" 
							placeholder="<?php echo JText::_('COM_ED_SEARCH_PLACEHOLDER'); ?>" 
							aria-label="<?php echo JText::_('COM_ED_SEARCH_PLACEHOLDER'); ?>" 
							aria-describedby="searchButton"
						>
						<button id="searchButton" class="o-btn o-btn--default-o" data-search-button=""><?php echo JText::_('COM_ED_TAGS_SEARCH_BUTTON'); ?></button>
					</div>
				</div>
			</div>

			<input type="hidden" name="option" value="com_easydiscuss" />
			<input type="hidden" name="controller" value="search" />
			<input type="hidden" name="task" value="query" />
			<input type="hidden" name="Itemid" value="<?php echo EDR::getItemId('search'); ?>" />
			<?php echo $this->html('form.token'); ?>
		</form>

		<?php echo $this->html('post.filters', $baseUrl, $filter, $activeCategory, $activeSort, [
			'selectedLabels' => $postLabels, 
			'selectedTypes' => $postTypes, 
			'selectedPriorities' => $postPriorities,
			'search' => $query
		]); ?>

		<?php if ($query) { ?>
		<div class="l-stack t-mt--md">
			<div data-search-header>
				<?php if ($pagination->total > 0) { ?>
					<?php echo $this->html('search.header', $query, $pagination->total); ?>
				<?php } ?>
			</div>

			<div class="<?php echo !$posts ? 'is-empty' : '';?>" data-ed-list-wrapper>
				<div class="ed-posts-list l-stack" data-ed-list itemscope itemtype="http://schema.org/ItemList">
					<?php echo $this->output('site/posts/list', [
						'featured' => [],
						'posts' => $posts,
						'pagination' => $pagination,
						'hideTitles' => true,
						'isSearch' => true
					]); ?>
				</div>

				<?php echo $this->html('loading.block'); ?>

				<?php echo $this->html('card.emptyCard', 'fa fa-search', 'COM_ED_NO_RESULT_BASED_ON_SEARCH_CRITERIA'); ?>
			</div>
		</div>
		<?php } ?>

		<?php echo ED::renderModule('easydiscuss-search-end'); ?>
	</div>
</div>
