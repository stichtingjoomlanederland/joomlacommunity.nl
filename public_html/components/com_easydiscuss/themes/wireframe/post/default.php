<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-entry
	<?php echo $post->isLocked() ? ' is-locked' : '';?>
	<?php echo $post->isFeatured() ? ' is-featured' : '';?>
	<?php echo $post->isResolved() ? ' is-resolved' : '';?>
	<?php echo $post->isPrivate() ? ' is-private' : '';?>
	"
	data-ed-post-wrapper
>
	<div data-ed-post-notifications></div>

	<?php echo $adsense->header; ?>

	<div class="ed-entry-action-bar t-lg-mb--lg">

		<?php if (ED::isModerator($post->category_id)) { ?>
			<div class="o-col" data-ed-post-assign data-ed-post-category-id="<?php echo $post->category_id;?>">
				<?php echo $this->output('site/post/post.assignment', array('post' => $post)); ?>
			</div>
		<?php } ?>

		<div class="o-col o-col--4">
			<div class="ed-entry-action-bar__btn-group">
				<?php if ((!$post->isLocked() || ED::isModerator($post->category_id)) && $post->canReply()) { ?>
				<a href="<?php echo JRequest::getURI();?>#respond" class="btn btn-default btn-xs t-mr--sm">
					<?php echo JText::_('COM_EASYDISCUSS_ADD_A_REPLY');?>
				</a>
				<?php } ?>

				<?php if ((!$post->isLocked() || ED::isModerator($post->category_id)) && $post->getCategory()->canViewReplies()) { ?>
				<a href="<?php echo JRequest::getURI();?>#replies" class="btn btn-default btn-xs">
					<?php echo JText::_('COM_EASYDISCUSS_VIEW_REPLIES');?> (<span data-ed-post-reply-counter><?php echo $post->getTotalReplies(); ?></span>)
				</a>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="ed-post-item has-body">

		<div class="ed-post-item__hd">

			<div class="o-grid-sm o-grid-sm--center">
				<div class="o-grid-sm__cell">
					<h2 class="ed-post-item__title t-lg-mb--md">

						<?php if ($this->config->get('post_priority') && $post->getPriority()) { ?>
						<i class="fa fa-file t-lg-mr--sm"
							style="<?php echo $post->getPriority() ? 'color:' . $post->getPriority()->color : '';?>"
							data-ed-provide="tooltip"
							data-original-title="<?php echo JText::_($post->getPriority()->title);?>"
						></i>
						<?php } ?>

						<a href="<?php echo $post->getPermalink();?>">
							<div class="ed-post-item__status">
								<i class="fa fa-star ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_FEATURED_DESC');?>"></i>

								<i class="fa fa-lock ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_LOCKED_DESC');?>"></i>

								<i class="fa fa-key ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PROTECTED_DESC');?>"></i>

								<i class="fa fa-eye ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PRIVATE_DESC');?>"></i>
							</div>

							<?php if ($this->config->get('main_qna')) { ?>
								<span class="o-label o-label--success-o ed-state-resolved"><?php echo JText::_('COM_EASYDISCUSS_RESOLVED');?></span>
							<?php } ?>

							<?php echo $post->getTitle();?>
						</a>
					</h2>

					<?php if ($post->hasStatus() || $post->getPostType() || $post->isStillNew()) { ?>

						<ol class="g-list-inline ed-post-item__post-meta t-mt--sm">

							<?php if ($post->hasStatus()) { ?>
							<li>
								<span class="o-label o-label--info-o"><?php echo $post->getStatusMessage(); ?></span>
							</li>
							<?php } else if ($post->isStillNew()) { ?>
							<li>
								<span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_NEW');?></span>
							</li>
							<?php } ?>

							<?php if ($post->getPostType()) { ?>
							<li>
								<span class="o-label o-label--clean-o <?php echo $post->getPostTypeSuffix(); ?>"><?php echo $post->getPostType(); ?></span>
							</li>
							<?php } ?>

						</ol>
					<?php } ?>
				</div>

				<div class="o-grid-sm__cell o-grid-sm__cell--auto-size o-grid-sm__cell--top">
					<?php echo $this->output('site/post/default.vote', array('post' => $post)); ?>
				</div>
			</div>
		</div>

		<div class="ed-post-item__sub-hd">
			<ol class="g-list-inline g-list-inline--dashed">
				<li>
					<?php if ($post->isAnonymous()) { ?>

						<?php if ($this->config->get('layout_avatar_in_post')) { ?>
							<span class="o-avatar o-avatar--xs t-lg-mr--sm">
							   <img src="<?php echo ED::getDefaultAvatar();?>" width="24" height="24" />
							</span>
						<?php } ?>

						<?php if ($post->canAccessAnonymousPost()) { ?>
							<?php echo $this->html('user.username', $post->getOwner(), array('isAnonymous' => true, 'canViewAnonymousUsername' => $post->canAccessAnonymousPost(), 'posterName' => $post->poster_name)); ?>
						<?php } else { ?>
							<span class="ed-user-name"><?php echo JText::_('COM_EASYDISCUSS_ANONYMOUS_USER');?></span>
						<?php } ?>
					<?php } ?>

					<?php if (!$post->isAnonymous()) { ?>
						<?php if ($this->config->get('layout_avatar_in_post')) { ?>
							<span class="o-avatar o-avatar--xs t-lg-mr--sm">
							   <?php echo $this->html('user.avatar', $post->getOwner(), array('rank' => false, 'status' => false, 'size' => 'xs')); ?>
							</span>
						<?php } ?>

						<?php echo $this->html('user.username', $post->getOwner(), array('posterName' => $post->poster_name)); ?>
					<?php } ?>
				</li>

				<?php if (ED::badges()->isEnabled() && $post->getOwner()->hasUserBadges()) { ?>
				<li>
					<?php echo ED::badges()->getPostHtml($post->getOwner()->id); ?>
				</li>
				<?php } ?>

				<li>
					<a href="<?php echo $post->getCategory()->getPermalink();?>"><?php echo $post->getCategory()->getTitle();?></a>
				</li>
				<li>
					<span><?php echo $post->date;?></span>
				</li>

				<?php if ($this->config->get('main_postsubscription')) { ?>
				<li>
					<?php echo ED::subscription()->html($this->my->id, $post->id, 'post'); ?>
				</li>
				<?php } ?>

			</ol>
		</div>

		<div class="ed-post-item__bd <?php echo $poll && $poll->isLocked() ? ' is-lockpoll' : '';?>" data-ed-post-item>

			<?php echo $this->output('site/post/default.actions', array('post' => $post)); ?>

			<div class="ed-post-content t-lg-pt--lg t-lg-pb--lg">
				<?php echo $post->getContent(); ?>
			</div>

			<div class="ed-post-widget-group t-lg-mb--lg">

				<?php if ($post->hasPolls() && $this->config->get('main_polls')) { ?>
					<?php echo $this->output('site/post/default.polls', array('post' => $post)); ?>
				<?php } ?>

				<?php if ($post->hasAttachments()) { ?>
					<?php echo $this->output('site/post/default.attachments', array('post' => $post)); ?>
				<?php } ?>

				<?php echo $this->output('site/post/default.fields', array('post' => $post)); ?>

				<?php echo $this->output('site/post/default.references', array('post' => $post, 'composer' => $composer)); ?>

				<?php echo $this->output('site/post/default.site.detail', array('post' => $post, 'composer' => $composer)); ?>
			</div>

			<?php if ($post->getTags()) { ?>
			<ol class="g-list-inline ed-post-meta-tag t-lg-mb--md">
				<?php foreach ($post->getTags() as $tag) { ?>
					<li>
						<a href="<?php echo EDR::getTagRoute($tag->id); ?>"><i class="fa fa-tag"></i> <?php echo $tag->title; ?></a>
					</li>
				<?php } ?>
			</ol>
			<?php } ?>

			<?php echo ED::likes()->button($post); ?>

			<?php echo ED::favourite()->button($post); ?>

			<?php if ($this->config->get('main_ratings')) { ?>
				<?php echo ED::ratings()->html($post); ?>
			<?php } ?>

			<?php echo $socialbuttons; ?>

			<?php echo $this->output('site/post/default.signature', array('post' => $post)); ?>

			<?php echo $this->output('site/post/default.navigation', array('navigation' => $navigation)); ?>
		</div>

		<?php echo $this->output('site/post/default.location', array('post' => $post)); ?>

		<?php if ($this->config->get('main_commentpost')) { ?>
		<div class="ed-post-item__ft">
			<?php echo $this->output('site/comments/default', array('post' => $post)); ?>
		</div>
		<?php } ?>
	</div>

	<?php echo ED::renderModule('easydiscuss-after-postcontent'); ?>

	<div class="ed-post-who-view t-lg-mt--lg t-lg-mb--lg">
		<?php echo ED::getWhosOnline();?>
	</div>

	<?php echo $adsense->beforereplies; ?>

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
			<div class="ed-post-answer t-lg-mb--lg">
				<div class="ed-post-reply-bar">
					<div class="o-row t-lg-mb--lg">
						<div class="o-col">
							<div class="ed-post-reply-bar__title">
								<?php echo JText::_('COM_EASYDISCUSS_ENTRY_ACCEPTED_ANSWER'); ?>
							</div>
						</div>
					</div>
				</div>

				<?php echo $this->output('site/post/default.reply.item', array('post' => $answer, 'poll' => $answer->getPoll())); ?>
			</div>
		<?php } ?>
	<?php } ?>

	<?php if ((!$post->isLocked() || ED::isModerator($post->category_id)) && ($post->canReply() || $post->getCategory()->canViewReplies())) { ?>
	<div class="ed-post-replies-wrapper <?php echo !$replies && !$onlyAcceptedReply ? ' is-empty' : '';?>"
		data-ed-post-replies-wrapper
	>
		<div class="ed-post-reply-bar">
			<div class="o-row t-lg-mb--lg">
				<div class="o-col">
					<div class="ed-post-reply-bar__title">
						<?php echo JText::_('COM_EASYDISCUSS_ENTRY_RESPONSES'); ?> (<span data-ed-post-reply-counter><?php echo $post->getTotalReplies(); ?></span>)
						<a name="replies"></a>
					</div>
				</div>

				<?php echo $this->output('site/post/default.replies.filters'); ?>
			</div>
		</div>

		<?php echo ED::renderModule('easydiscuss-before-replies'); ?>

		<?php if ($post->getCategory()->canViewReplies()) { ?>
		<div class="ed-post-replies" data-ed-post-replies>
		<?php if ($replies) { ?>
			<?php foreach ($replies as $reply) { ?>
				<?php echo $this->output('site/post/default.reply.item', array('post' => $reply, 'poll' => $reply->getPoll())); ?>
			<?php } ?>
		<?php } ?>
		</div>
		<?php } ?>

		<?php if ($replies && $pagination) { ?>
			<div class="ed-pagination">
				<?php
					if (ED::getDefaultRepliesSorting() == $sort) {
						$sort = '';
					}
				?>
				<?php echo $pagination->getPagesLinks('post', array('id' => $post->id, 'sort' => $sort), true);?>
			</div>
		<?php } ?>

		<div class="o-empty ed-post-replies__empty">
			<div class="o-empty__content">
				<i class="o-empty__icon fa fa-comments"></i><br /><br />
				<div class="o-empty__text"><?php echo $emptyMessage;?></div>
			</div>
		</div>

		<?php echo ED::renderModule('easydiscuss-after-replies'); ?>
	</div>
	<?php } ?>

	<?php echo ED::renderModule('easydiscuss-before-replyform'); ?>

	<?php echo $this->output('site/post/default.reply.form'); ?>

	<?php echo ED::renderModule('easydiscuss-after-replyform'); ?>

	<?php echo $adsense->footer; ?>
</div>
