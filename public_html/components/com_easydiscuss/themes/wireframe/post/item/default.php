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

	<?php if ($this->config->get('main_postassignment') && ED::isModerator($post->category_id)) { ?>
	<div class="ed-admin-bar">
		<div class="lg:t-d--flex">
			<div class="t-flex-grow--1">
				<?php echo $this->html('assignment.dropdown', $post); ?>
			</div>
		</div>
	</div>
	<?php } ?>

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
						<div>
							<div class="t-min-width--0" data-category data-id="<?php echo $post->getCategory()->id; ?>">
								<?php echo $this->html('post.category', $post->getCategory()); ?>
							</div>

							<?php echo $this->html('post.author', $post); ?>

							<div>
								<i class="far fa-clock"></i>&nbsp; <?php echo $post->date;?>
							</div>

							<?php if ((!$post->isLocked() || ED::isModerator($post->category_id))) { ?>
							<div>
								<i class="far fa-comments"></i>&nbsp; <?php echo JText::sprintf('COM_ED_META_REPLIES', '<span data-ed-post-reply-counter>' . ED::formatNumbers($post->getTotalReplies()) . '</span>');?>
							</div>
							<?php } ?>

							<div>
								<i class="far fa-eye"></i>&nbsp; <?php echo JText::sprintf('COM_ED_META_VISITS', ED::formatNumbers($post->getHits()));?>
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
			<div class="ed-entry-actions t-mb--md" data-ed-post-actions-bar data-id="<?php echo $post->id;?>">
				<?php echo $this->html('post.votes', $post); ?>

				<?php if ($this->config->get('main_postsubscription')) { ?>
				<div class="ed-entry-actions-group sm:t-justify-content--c" role="group">
					<?php echo ED::subscription()->html($this->my->id, $post->id, 'post', [
						'customClass' => 'o-btn o-btn--sm'
					]); ?>
				</div>
				<?php } ?>

				<?php echo $this->output('site/post/item/actions', array('post' => $post)); ?>
			</div>

			<div class="o-body is-editor-markup">
				<?php echo $post->getContent(); ?>
			</div>

			<div class="o-body l-stack" data-ed-post-widget-group>
				<?php if ($post->hasPolls() && $this->config->get('main_polls')) { ?>
					<?php echo $this->output('site/post/widgets/polls/default', array('post' => $post)); ?>
				<?php } ?>

				<?php if ($post->hasAttachments() && $this->acl->allowed('download_attachment')) { ?>
					<?php echo $this->output('site/post/widgets/attachments/default', array('post' => $post)); ?>
				<?php } ?>

				<?php echo $this->output('site/post/widgets/fields/default', array('post' => $post)); ?>

				<?php echo $this->html('post.location', $post); ?>

				<?php echo $this->html('triggers.html', 'easydiscuss', 'onAfterPostContent', array(&$post)); ?>

				<?php if ($this->config->get('main_master_tags') && $tags) { ?>
				<div class="o-meta-tag l-cluster l-spaces--sm">
					<div class="">
						<?php foreach ($tags as $tag) { ?>
							<div class="t-min-width--0">
								<div class="t-d--flex t-text--truncate">
								<a href="<?php echo EDR::getTagRoute($tag->id); ?>" class="o-label t-bg--300 t-text--600 t-text--truncate">
									<i class="fa fa-tag"></i>&nbsp; <?php echo $this->html('string.escape', $tag->title); ?>
								</a>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
			</div>

			<?php echo $this->html('post.signature', $post); ?>

			<?php echo $socialbuttons; ?>

			<div class="ed-post-feedback t-d--flex">
				<div class="t-flex-grow--1">
					<div class="l-cluster">
						<div class="">
							<?php if ($post->canLike()) { ?>
							<div>
								<?php echo ED::likes()->button($post); ?>
							</div>
							<?php } ?>

							<?php if ($post->canFav()) { ?>
							<div>
								<?php echo ED::favourite()->button($post); ?>
							</div>
							<?php } ?>

							<div>
								<?php echo $this->html('post.ratings', $post); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php if ($this->config->get('main_commentpost')) { ?>
			<?php echo $this->output('site/comments/default', array('post' => $post)); ?>
		<?php } ?>

	</div>

	<?php echo ED::renderModule('easydiscuss-after-postcontent'); ?>

	<?php echo $this->html('post.viewers', $post); ?>

	<?php echo $adsense->beforereplies; ?>

	<?php echo ED::renderModule('easydiscuss-before-replies'); ?>

	<?php if ($replies && $post->getCategory()->canViewReplies()) { ?>
		<div class="ed-replies-filter">
			<div class="o-dropdown" data-sorting-wrapper>
				<a href="javascript:void(0);" class="o-btn o-btn--default-o sm:t-d--block sm:t-mb--md" data-ed-toggle="dropdown">
					<span data-sorting-title>
						<?php if ($sort == 'oldest' || !$sort) { ?>
							<?php echo JText::_('COM_ED_SORT_OLDEST_FIRST');?>
						<?php } ?>

						<?php if ($sort == 'latest') { ?>
							<?php echo JText::_('COM_ED_SORT_NEWEST_FIRST');?>
						<?php } ?>
					</span>
					&nbsp;<i class="fa fa-sort"></i>
				</a>
				<ul class="o-dropdown-menu t-mt--2xs sm:t-w--100 has-active-markers">
					<li class="<?php echo ($sort == 'latest') ? 'active' : ''; ?>" data-ed-sorting="latest">
						<a href="<?php echo EDR::_( 'index.php?option=com_easydiscuss&view=post&id='. $post->id . '&sort=latest'); ?>" class="o-dropdown__item">
							<?php echo JText::_('COM_ED_SORT_NEWEST_FIRST');?>
						</a>
					</li>
					<li class="<?php echo ($sort == 'oldest' || !$sort) ? 'active' : ''; ?>" data-ed-sorting="oldest">
						<a href="<?php echo EDR::_( 'index.php?option=com_easydiscuss&view=post&id='. $post->id . '&sort=oldest'); ?>" class="o-dropdown__item">
							<?php echo JText::_('COM_ED_SORT_OLDEST_FIRST');?>
						</a>
					</li>
				</ul>
			</div>
		</div>
	<?php } ?>

	<?php if (ED::isModerator($post->category_id) || $post->canReply() || $post->getCategory()->canViewReplies()) { ?>
		<div class="ed-replies-list-wrapper <?php echo !$replies && !$onlyAcceptedReply ? ' is-empty' : '';?>" data-ed-post-replies-wrapper>

			<div class="ed-replies-list ed-timeline" data-ed-post-replies>

				<div class="ed-timeline__item" data-ed-post-answer-wrapper>
					<?php if ($answer) { ?>
						<?php if ($answer === true) { ?>
							<div class="ed-post-answer t-lg-mb--lg">
								<div class="ed-reply-item is-empty">
									<div class="o-empty o-empty--bordered o-empty--bg-shade">
										<div class="o-empty__content">
											<i class="o-empty__icon fa fa-ban"></i>
											<?php if (!$onlyAcceptedReply) { ?>
												<div class="o-empty__text"><?php echo JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_VIEW_ACCEPTED_ANSWER'); ?></div>
											<?php } else { ?>
												<div class="o-empty__text"><?php echo JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_VIEW_ACCEPTED_ANSWER_AND_REPLIES'); ?></div>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
						<?php } else { ?>
							<?php echo $this->output('site/post/replies/item', [
								'post' => $answer,
								'poll' => $answer->getPoll(),
								'fromAnswer' => true
							]); ?>
						<?php } ?>
					<?php } ?>
				</div>

				<?php if ($post->getCategory()->canViewReplies() && $replies) { ?>
					<?php foreach ($replies as $reply) { ?>
						<?php if (!isset($reply->isActivity) || !$reply->isActivity) { ?>
							<?php echo $this->output('site/post/replies/item', [
								'post' => $reply,
								'poll' > $reply->getPoll(),
								'fromAnswer' => false
							]); ?>
						<?php } ?>

						<?php if (isset($reply->isActivity) && $reply->isActivity) { ?>
							<?php echo $this->output('site/post/activities/item', [
								'log' => $reply
							]); ?>
						<?php } ?>

						<?php echo ED::renderModule('easydiscuss-between-replies'); ?>
					<?php } ?>
				<?php } ?>
			</div>

			<?php if ($replies && $pagination) { ?>
				<div class="ed-pagination">
					<?php
						$pageLinkOptions = array('id' => $post->id);
						if ($sort && ED::getDefaultRepliesSorting() != $sort) {
							$pageLinkOptions['sort'] = $sort;
						}
					?>
					<?php echo $pagination->getPagesLinks('post', $pageLinkOptions, true);?>
				</div>
			<?php } ?>

			<?php echo $this->html('card.emptyCard', 'fa fa-comments', $emptyMessage, false); ?>
		</div>

		<?php echo ED::renderModule('easydiscuss-after-replies'); ?>
	<?php } ?>

	<?php echo ED::renderModule('easydiscuss-before-replyform'); ?>

	<?php if ($post->isLocked()) { ?>
	<div class="o-card o-card--ed-locked-section">
		<div class="o-card__body">
			<div class="t-text--center">
				<div class="">
					<i class="o-empty__icon t-text--info fas fa-lock"></i>
				</div>
				<?php if (ED::isModerator($post->category_id)) { ?>
					<?php echo JText::_('COM_EASYDISCUSS_POST_IS_CURRENTLY_LOCKED_BUT_MODERATOR'); ?>
				<?php } else { ?>
					<?php echo JText::_('COM_EASYDISCUSS_POST_IS_CURRENTLY_LOCKED'); ?>
				<?php } ?>
			</div>

		</div>
	</div>

	<?php } ?>

	<div class="ed-post-reply-form" data-ed-post-reply-form>

		<div class="discuss-user-reply <?php echo !$post->isLocked() || ED::isModerator($post->category_id) ? '' : 't-hidden'; ?>" data-ed-post-reply-composer>
			<a name="respond" id="respond"></a>

			<?php if ($access->canReply() && !$post->isUserBanned() && $post->canReply()) { ?>
				<?php echo $composer->getComposer($post->category_id); ?>
			<?php } else { ?>
				<?php if (!$this->my->id) { ?>
					<?php echo ED::getLoginForm('COM_EASYDISCUSS_PLEASE_LOGIN_TO_REPLY', base64_encode('index.php?option=com_easydiscuss&view=post&id=' . $post->id)); ?>
				<?php } elseif ($post->isUserBanned()) { ?>
					<div class="o-alert o-alert--warning"><?php echo JText::_('COM_EASYDISCUSS_NOT_ALLOWED_YOU_REPLY_BECAUSE_GET_BANNED');?></div>
				<?php } ?>
			<?php } ?>
		</div>
	</div>

	<?php echo ED::renderModule('easydiscuss-after-replyform'); ?>

	<?php echo $adsense->footer; ?>
</div>

<?php echo $this->html('post.schema', $post, $answer, $tags); ?>

<?php if ($this->config->get('layout_post_liveupdates') && $this->my->id) { ?>
	<?php echo $this->output('site/post/item/live.updates', ['post' => $post]); ?>
<?php } ?>
