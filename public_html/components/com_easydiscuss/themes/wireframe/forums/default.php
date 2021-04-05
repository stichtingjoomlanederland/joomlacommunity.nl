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
<?php echo ED::renderModule('easydiscuss-forums-start'); ?>

<div class="ed-forums l-stack" data-ed-forums>
	<?php if ($activeCategory) { ?>
		<?php echo $this->html('card.activeCategory', $activeCategory); ?>
	<?php } ?>

	<?php if (!empty($threads)) { ?>
	<div class="l-stack">
		<?php foreach ($threads as $thread) { ?>
		<div class="o-card o-card--ed-forum-category">
			<div class="o-card__body l-stack">
				<div class="t-d--flex t-align-items--c">
					<div class="t-flex-grow--1 t-min-width--0 t-pr--lg">
						<div class="o-media">
							<div class="o-media__body">
								<div class="o-title">
									<a href="<?php echo EDR::getForumsRoute($thread->category->id); ?>" class="si-link">
										<?php echo $thread->category->getTitle(); ?>
									</a>
								</div>
							</div>
						</div>
					</div>

					<div class="t-ml--auto sm:t-d--none lg:t-d--block">
						<div class="t-text--600" data-ed-post-counter data-id="<?php echo $thread->category->id; ?>">
							<div class="o-loader o-loader--sm o-loader--inline is-active">&nbsp;</div>
						</div>
					</div>
				</div>

				<div class="ed-forums-items l-stack">
					<?php if ($thread->posts) { ?>
						<?php foreach ($thread->posts as $post) { ?>
							<?php echo $this->html('card.forumPost', $post); ?>
						<?php } ?>
					<?php } ?>

					<?php if (!$thread->posts) { ?>
						<?php echo $this->html('card.emptyCard', 'fa-book', 'COM_EASYDISCUSS_FORUMS_CATEGORY_EMPTY_DISCUSSION_LIST'); ?>
					<?php } ?>
				</div>

				<div class="t-text--center">
					<?php if (!$thread->category->container) { ?>
					<a href="<?php echo EDR::getCategoryRoute($thread->category->id); ?>" class="si-link">
						<?php echo JText::_('COM_ED_VIEW_ALL_POSTS_FROM_CATEGORY'); ?>
					</a>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>
</div>

<?php if (isset($pagination)) { ?>
	<div class="ed-pagination">
		<?php echo $pagination->getPagesLinks();?>
	</div>
<?php } ?>

<?php echo ED::renderModule('easydiscuss-forums-end'); ?>
