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
<div class="ed-post-item<?php echo $post->getHeaderClass();?>" <?php echo $post->getPriority() ? 'style="border-left: 3px solid ' . $post->getPriority()->color . ';"' : '';?>>
	<div class="ed-post-item__hd">

		<div class="o-grid">
			<div class="o-grid__cell t-lg-pr--lg t-xs-pr--no">

				<h2 class="ed-post-item__title t-lg-mb--md">

					<a href="<?php echo $post->getPermalink();?>">
						<?php if ($post->isFeatured() || $post->isLocked() || $post->isProtected() || $post->isPrivate()) { ?>
						<div class="ed-post-item__status pull-left t-lg-mr--md">
							<i class="fa fa-star ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_FEATURED_DESC');?>"></i>

							<i class="fa fa-lock ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_LOCKED_DESC');?>"></i>

							<i class="fa fa-key ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PROTECTED_DESC');?>"></i>

							<i class="fa fa-eye ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PRIVATE_DESC');?>"></i>

						</div>
						<?php } ?>
						<?php echo $post->getTitle();?>
					</a>

				</h2>

				<?php if ($post->isResolved() || $post->isPostRejected() || $post->isPostOnhold() || $post->isPostAccepted() || $post->isPostWorkingOn() || $post->getTypeTitle() || $post->isStillNew()) { ?>
				<ol class="g-list-inline ed-post-item__post-meta">

					<?php if ($post->isResolved() && $this->config->get('main_qna')) { ?>
						<li><span class="o-label o-label--success-o"><?php echo JText::_('COM_EASYDISCUSS_RESOLVED');?></span></li>
					<?php } ?>

					<?php if ($post->isStillNew()) { ?>
						<li><span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_NEW');?></span></li>
					<?php } ?>

					<!-- post status here: accepted, onhold, working rejected -->
					<?php if ($post->isPostRejected()) { ?>
						<li><span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_REJECT');?></span></li>
					<?php } ?>
					<?php if ($post->isPostOnhold()) { ?>
						<li><span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_ON_HOLD');?></span></li>
					<?php } ?>
					<?php if ($post->isPostAccepted()) { ?>
						<li><span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_ACCEPTED');?></span></li>
					<?php } ?>
					<?php if ($post->isPostWorkingOn()) { ?>
						<li><span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_WORKING_ON');?></span></li>
					<?php } ?>

					<!-- post type here -->
					<?php if ($post->getTypeTitle()) { ?>
						<li><span class="o-label o-label--clean-o <?php echo $post->getTypeSuffix(); ?>"><?php echo $post->getTypeTitle(); ?></span></li>
					<?php } ?>
				</ol>
				<?php } ?>

				<ol class="g-list-inline g-list-inline--delimited ed-post-meta-reply t-lg-mt--md">
					<li><?php echo JText::sprintf('COM_EASYDISCUSS_LAST_ACTIVITY_TIMELAPSE', ED::date()->toLapsed($post->modified)); ?></li>
					<?php if ($post->getLastReplier() && $post->lastReply) { ?>
						<li data-breadcrumb="·">
							<?php if (!$post->isLastReplyAnonymous()) { ?>
								<a href="<?php echo $post->getLastReplyPermalink($post->lastReply->id); ?>">
									<i class="fa fa-reply"></i> <?php echo JText::sprintf('COM_EASYDISCUSS_LAST_REPLIED_BY', $post->getLastReplier()->getName(), ED::date()->toLapsed($post->lastupdate)); ?>
								</a>
							<?php } else { ?>
								<a href="<?php echo $post->getLastReplyPermalink($post->lastReply->id); ?>">
									<i class="fa fa-reply"></i> <?php echo JText::sprintf('COM_EASYDISCUSS_LAST_REPLIED_BY', JText::_('COM_EASYDISCUSS_ANONYMOUS_USER'), ED::date()->toLapsed($post->lastupdate)); ?>
								</a>
							<?php } ?>
						</li>
					<?php } ?>
				</ol>

			</div>

			<div class="o-grid__cell o-grid__cell--auto-size">
				<div class="ed-statistic pull-right">
					<div class="ed-statistic__item">
						<a href="<?php echo $post->getPermalink();?>">
							<span class="ed-statistic__item-count"><?php echo $post->getTotalReplies();?></span>
							<span>
								<?php echo $this->getNouns('COM_EASYDISCUSS_REPLIES', $post->getTotalReplies()); ?>
							</span>
						</a>
					</div>
					<div class="ed-statistic__item">
						<a href="<?php echo $post->getPermalink();?>">
							<span class="ed-statistic__item-count"><?php echo $post->getHits();?></span>
							<span>
								<?php echo $this->getNouns('COM_EASYDISCUSS_VIEWS', $post->getHits()); ?>
							</span>
						</a>
					</div>

					<?php if ($this->config->get('main_allowquestionvote')) { ?>
					<div class="ed-statistic__item">
						<a href="<?php echo $post->getPermalink();?>">
							<span class="ed-statistic__item-count"><?php echo $post->getTotalVotes();?></span>
							<span>
								<?php echo $this->getNouns('COM_EASYDISCUSS_VOTES_STRING', $post->getTotalVotes()); ?>
							</span>
						</a>
					</div>
					<?php } ?>

					<?php if ($this->config->get('main_likes_discussions')) { ?>
					<div class="ed-statistic__item">
						<a href="<?php echo $post->getPermalink();?>">
							<span class="ed-statistic__item-count"><?php echo $post->getTotalLikes();?></span>
							<span>
								<?php echo $this->getNouns('COM_EASYDISCUSS_LIKES_STRING', $post->getTotalLikes()); ?>
							</span>
						</a>
					</div>
					<?php } ?>
				</div>
			</div>

		</div>
	</div>

	<?php if ($this->config->get('layout_enableintrotext') || ($post->getTags() && $this->config->get('layout_showtags'))) { ?>
	<div class="ed-post-item__bd">
		<?php if ($this->config->get('layout_enableintrotext')) { ?>
		<div class="ed-post-content">
			<?php echo $post->getIntro();?>
		</div>
		<?php } ?>

		<?php if ($this->config->get('main_master_tags') && $this->config->get('layout_showtags')) { ?>
			<?php if ($post->getTags()) { ?>
			<ol class="g-list-inline ed-post-meta-tag">
				<?php foreach ($post->getTags() as $tag) { ?>
				<li>
					<a href="<?php echo EDR::_('view=tags&id=' . $tag->id);?>">
						<i class="fa fa-tag"></i>&nbsp; <?php echo $tag->title;?>
					</a>
				</li>
				<?php } ?>
			</ol>
			<?php } ?>
		<?php } ?>

	</div>
	<?php } ?>

	<div class="ed-post-item__ft">

		<div class="o-row">
			<?php if (!$post->isAnonymous()) { ?>
				<div class="o-col-sm">
					<div class="o-grid">
						<div class="o-grid__cell o-grid__cell--auto-size t-lg-pr--md t-xs-pb--md">
							<div class="o-flag">
								<div class="o-flag__image o-flag--top">
									<?php echo $this->html('user.avatar', $post->getOwner(), array('rank' => true, 'status' => true)); ?>
								</div>
								<div class="o-flag__body">
									<div class="">
										<?php echo $this->html('user.username', $post->getOwner(), array('posterName' => $post->poster_name)); ?>
										<?php if ($this->config->get('layout_profile_roles') && $post->getOwner()->getRole()) { ?>
											<span class="ed-user-role-label o-label o-label--<?php echo $post->getOwner()->getRoleLabelClassname()?>"><?php echo $post->getOwner()->getRole(); ?></span>
										<?php } ?>
									</div>

									<?php if( $this->config->get('main_ranking')){ ?>
									<div class="ed-user-rank"><?php echo $this->escape(ED::getUserRanks($post->getOwner()->id)); ?></div>
									<?php } ?>
								</div>
							</div>
						</div>

						<?php if (ED::badges()->isEnabled() && $post->getOwner()->hasUserBadges()) { ?>
						<div class="o-grid__cell o-grid__cell--auto-size o-grid__cell--center t-lg-pr--md">
							<?php echo ED::badges()->getPostHtml($post->getOwner()->id); ?>
						</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			
			<?php if ($post->isAnonymous()) { ?>
				<div class="o-col-sm">
					<div class="o-flag">
						<div class="o-flag__image o-flag--top">
							<?php echo $this->html('user.anonymous', $post->getOwner(), $post->isAnonymous()); ?>
						</div>
						<div class="o-flag__body">
							<div class="">
								<?php echo $this->html('user.username', $post->getOwner(), array('isAnonymous' => true, 'canViewAnonymousUsername' => $post->canAccessAnonymousPost())); ?>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
			<div class="o-col-sm">
				<ol class="g-list-inline g-list-inline--dashed pull-right ed-post-meta-cat">
					<li><a href="<?php echo $post->getCategory()->getPermalink();?>" class=""><?php echo JText::_($post->getCategory()->title);?></a></li>

					<?php if ($post->hasAttachments()) { ?>
					<li>
						<i class="fa fa-file" data-ed-provide="tooltip" data-title="<?php echo JText::_('COM_ED_HAS_ATTACHMENTS');?>"></i>
					</li>
					<?php } ?>

					<?php if ($post->hasPolls() && $this->config->get('main_polls')) { ?>
					<li>
						<i class="fa fa-pie-chart" data-ed-provide="tooltip" data-title="<?php echo JText::_('COM_ED_HAS_POLL');?>"></i>
					</li>
					<?php } ?>
				</ol>
			</div>
		</div>

	</div>
</div>
