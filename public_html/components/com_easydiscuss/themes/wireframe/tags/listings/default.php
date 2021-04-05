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
<div class="ed-tags-wrapper">
	<div class="l-stack">
		<h2 class="o-title"><?php echo JText::_('COM_EASYDISCUSS_TAGS'); ?></h2>

		<form name="tags" method="post" action="<?php echo JRoute::_('index.php'); ?>">
			<div class="o-grid">
				<div class="o-grid__cell">
					<div class="o-input-group">
						<input type="text" name="search"
							value="<?php echo $this->html('string.escape', $search);?>"
							class="o-form-control" 
							placeholder="<?php echo JText::_('COM_ED_TAGS_SEARCH_TAGS_PLACEHOLDER'); ?>" 
							aria-label="<?php echo JText::_('COM_ED_TAGS_SEARCH_TAGS_PLACEHOLDER'); ?>" 
							aria-describedby="searchButton"
						>
						<button id="searchButton" class="o-btn o-btn--default-o" data-search-button=""><?php echo JText::_('COM_ED_TAGS_SEARCH_BUTTON'); ?></button>
					</div>
				</div>

				<div class="o-grid__cell--auto t-pl--lg">
					<select class="o-form-select" data-index-sort-filter>
						<option value="title" <?php echo $activeSort == 'title' ? ' selected="true"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_SORT_TITLE');?></option>
						<option value="postcount" <?php echo $activeSort == 'postcount' ? ' selected="true"' : '';?>><?php echo JText::_('COM_ED_SORT_TAG_WEIGHT');?></option>
					</select>
				</div>
			</div>

			<input type="hidden" name="option" value="com_easydiscuss" />
			<input type="hidden" name="controller" value="tags" />
			<input type="hidden" name="task" value="search" />

			<?php echo $this->html('form.token'); ?>
		</form>

		<div class="<?php echo !$tags ? 'is-empty ' : '';?>" data-list-wrapper>
			
			<div class="l-grid l-grid--above-min" data-list-result>
				<?php if ($tags) { ?>
					<?php foreach ($tags as $tag) { ?>
						<?php echo $this->html('card.tag', $tag); ?>
					<?php } ?>
				<?php } ?>
			</div>

			<?php echo $this->html('loading.block'); ?>

			<?php echo $this->html('card.emptyCard', 'fa fa-tags', 'COM_ED_NO_TAGS_FOUND'); ?>
		</div>
		<?php if ($tags) { ?>
		<div data-tags-pagination>
			<?php echo $pagination->getPagesLinks();?>
		</div>
		<?php } ?>
	</div>
	
</div>
