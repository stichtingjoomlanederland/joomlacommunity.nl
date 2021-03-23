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
<div class="ed-timeline__item <?php echo $post->isAnswer() && !$fromAnswer ? 't-d--none' : '';?>"
	data-ed-post-item
	data-ed-reply-item
	data-id="<?php echo $post->id;?>"
>
	<div>
		<div class="o-card o-card--ed-reply-item
		<?php echo $post->isPending() ? 'ed-post-pending' : ''; ?> 
		<?php echo $post->isAnswer() && $fromAnswer ? 'is-answer' : '';?>
		" data-ed-reply-card-wrapper>
			<div class="o-card__body l-stack">
				<div class="lg:t-d--flex t-align-items--c sm:t-flex-direction--c">
					<div class="t-flex-grow--1 t-min-width--0 lg:t-pr--lg sm:t-mb--md">
						<a id="<?php echo JText::_('COM_EASYDISCUSS_REPLY_PERMALINK');?>-<?php echo $post->id;?>"></a>

						<div class="o-media">
							<div class="o-media__image">
								<?php echo $this->html('user.avatar', $post->getOwner(), [
									'rank' => false, 
									'status' => false
								], $post->isAnonymous()); ?>
							</div>
							<div class="o-media__body">
								<div class="l-stack l-spaces--2xs">
									<div class="t-d--flex t-align-items--c">
										<div class="t-min-width--0 t-text--truncate t-font-size--01">
											<?php echo $this->html('user.username', $post->getOwner(), [
												'isAnonymous' => $post->isAnonymous(),
											]); ?>
										</div>

										<?php echo $this->html('post.badges', $post->getOwner()); ?>
									</div>

									<div>
										<div class="o-meta l-cluster l-spaces--sm">
											<div>
												<div>
													<?php echo $post->getReplyDate(); ?>
												</div>

												<div class="t-d--flex">
													<div class="t-mr--xs">&middot;</div>
													<a href="<?php echo $post->permalink; ?>" data-ed-post-reply-seq="<?php echo $post->seq; ?>">#<?php echo $post->id;?></a>
												</div>

												<?php if ($post->isFromEmailParser()) { ?>
												<div class="t-d--flex">
													<div class="t-mr--xs">&middot;</div>
													<?php echo $this->html('post.email'); ?>
												</div>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="lg:t-ml--auto t-flex-shrink--0 ">
						<div class="ed-entry-actions" data-ed-post-actions-bar data-id="<?php echo $post->id;?>">
							<?php if ($post->isAnswer()) { ?>
							<div class="ed-entry-actions-group t-border--200" role="group">
								<div class="o-btn sm:t-flex-grow--1 o-btn--success t-text--nowrap o-btn--accepted">
									<?php echo JText::_('COM_EASYDISCUSS_ENTRY_ACCEPTED_ANSWER'); ?>
								</div>
							</div>
							<?php } ?>

							<?php if ($post->isPending()) { ?>
							<div class="ed-entry-actions-group" role="group">
								<button type="button" class="o-btn sm:t-flex-grow--1 t-bg--warning t-text--100 t-text--nowrap">
									<?php echo JText::_('COM_EASYDISCUSS_PENDING_MODERATION'); ?>
								</button>
							</div>
							<?php } ?>

							<?php echo $this->html('post.votes', $post); ?>

							<?php echo $this->output('site/post/item/actions', [
								'post' => $post
							]); ?>
						</div>
					</div>
				</div>

				<div class="o-body is-editor-markup" data-ed-post-content>
					<?php echo $post->getContent(); ?>
				</div>

				<div data-ed-reply-editor></div>

				<div>
					<div class="o-body l-stack" data-ed-post-widget-group>
						<?php if ($post->hasPolls() && $this->config->get('main_polls_replies')) { ?>
							<?php echo $this->output('site/post/widgets/polls/default', array('post' => $post)); ?>
						<?php } ?>

						<?php if ($post->hasAttachments() && $this->acl->allowed('download_attachment')) { ?>
							<?php echo $this->output('site/post/widgets/attachments/default', array('post' => $post)); ?>
						<?php } ?>

						<?php echo $this->output('site/post/widgets/fields/default', array('post' => $post)); ?>

						<?php echo $this->html('post.location', $post); ?>

						<?php echo $this->html('triggers.html', 'easydiscuss', 'onAfterPostContent', array(&$post)); ?>

						<?php echo $this->html('post.signature', $post); ?>
					</div>
				</div>

				<?php if ($post->canLike()) { ?>
				<div class="ed-post-feedback t-d--flex">
					<div class="t-flex-grow--1">
						<div class="l-cluster">
							<div class="">
								<div>
									<?php echo ED::likes()->button($post); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>

			<?php if ($this->config->get('main_comment')) { ?>
				<?php echo $this->output('site/comments/default', array('post' => $post)); ?>
			<?php } ?>
		</div>
	</div>
</div>