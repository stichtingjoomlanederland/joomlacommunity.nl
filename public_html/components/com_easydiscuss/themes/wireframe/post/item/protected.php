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
<div class="ed-entry <?php echo $post->getWrapperClass();?>"
	data-ed-post-wrapper
	data-ed-post-item
	data-id="<?php echo $post->id; ?>"
>
	<div data-ed-post-notifications></div>

	<?php echo $adsense->header; ?>

	<div class="ed-admin-bar">
		<div class="lg:t-d--flex">
			<div class="t-flex-grow--1">
				<?php if (ED::isModerator($post->category_id)) { ?>
					<?php echo $this->html('assignment.dropdown', $post); ?>
				<?php } ?>
			</div>

			<div class="t-flex-shrink--0">
				<div class="l-cluster">
					<div class="">
						<?php if ((!$post->isLocked() || ED::isModerator($post->category_id)) && $post->getCategory()->canViewReplies()) { ?>
						<div class="t-text--500 t-d--flex">
							<?php echo JText::_('COM_ED_REPLIES');?> <div class="t-text--700 t-ml--xs" data-ed-post-reply-counter><?php echo ED::formatNumbers($post->getTotalReplies());?></div>
						</div>
						<?php } ?>

						<div class="t-text--500 t-d--flex">
							<?php echo JText::_('COM_ED_LIKES');?> <div class="t-text--700 t-ml--xs" data-meta-likes><?php echo ED::formatNumbers($post->getTotalLikes());?></div>
						</div>

						<div class="t-text--500 t-d--flex">
							<?php echo JText::_('COM_ED_VIEWS');?> <div class="t-text--700 t-ml--xs"><?php echo ED::formatNumbers($post->getHits());?></div>
						</div>

						<div class="t-text--500 t-d--flex">
							<?php echo JText::_('COM_ED_VOTES');?> <div class="t-text--700 t-ml--xs" data-meta-votes><?php echo ED::formatNumbers($post->getTotalVotes()); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="o-card o-card--ed-entry-item">
		<div class="o-card__body">
			<div class="l-stack">
				<div class="lg:t-d--flex t-align-items--c">
					<div class="t-flex-grow--1">
						<div class="ed-post-status-bar o-body">
							<div class="l-cluster" data-post-states>
								<div>
									<?php echo $this->html('post.featured'); ?>

									<?php echo $this->html('post.newLabel'); ?>

									<?php echo $this->html('post.resolved'); ?>

									<?php echo $this->html('post.locked'); ?>

									<?php echo $this->html('post.password'); ?>

									<?php echo $this->html('post.hidden'); ?>

									<?php echo $this->html('post.priority', $post); ?>
								</div>
							</div>
						</div>
					</div>
					<div class="sm:t-mt--md">
						<div class="o-label-toolbar o-body" style="max-width: 280px">
							<div class="o-label-group t-text--truncate" data-post-status>
								<?php if ($post->hasLabel()) { ?>
									<?php echo $this->html('post.label', $post->getCurrentLabel()); ?>
								<?php } ?>

								<?php if ($post->getPostTypeObject()) { ?>
									<?php echo $this->html('post.type', $post); ?>
								<?php } ?>
							</div>
						</div>
					</div>
					
				</div>

				<h2 class="o-title">
					<a href="<?php echo $post->getPermalink();?>" class="si-link" data-ed-post-entry-title><?php echo $post->getTitle();?></a>
				</h2>
			</div>
		</div>

		<div class="o-card__body">
			<div class="lg:t-d--flex t-align-items--c">
				<div class="t-flex-grow--1">
					<div class="o-meta l-cluster">
						<div class="t-font-size--01">
							<?php echo $this->html('post.author', $post); ?>
							
							<div class="t-min-width--0" data-category data-id="<?php echo $post->getCategory()->id; ?>">
								<?php echo $this->html('post.category', $post->getCategory()); ?>
							</div>

							<div>
								<i class="far fa-clock"></i>&nbsp; <?php echo $post->date;?>
							</div>

							<?php if ($post->isFromEmailParser()) { ?>
								<?php echo $this->html('post.email'); ?>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="o-card__body l-stack">
			<div class="o-body is-editor-markup">
				<?php echo $post->getProtectedContent('content'); ?>
			</div>
		</div>
	</div>

	<?php echo ED::renderModule('easydiscuss-after-postcontent'); ?>

	<?php echo $this->html('post.viewers', $post); ?>

	<?php echo $adsense->beforereplies; ?>

	<?php echo $adsense->footer; ?>
</div>

<?php echo $this->html('post.schema', $post, $answer, $tags); ?>