<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="ed" class="ed-mod m-recent-discussions">

	<?php if ($posts) { ?>

	<div class="ed-list--vertical has-dividers--bottom-space">
		<?php foreach ($posts as $post) {

			$title = ($params->get('max_title', 0)) ? JString::substr(ED::string()->escape($post->title), 0, $params->get('max_title')) : ED::string()->escape($post->title);

			$postcontent = $post->getContent();
			$postcontent = ($params->get('max_content', 0)) ? JString::substr(strip_tags($postcontent), 0, $params->get('max_content')) . '...' : $postcontent;
		?>
		<div class="ed-list__item">
			<div class="">
				<?php if ($params->get('show_avatar', 1)) { ?>
					<div class="o-flag__img t-lg-mr--md">
						<div class="ed-mod__section">

							<div class="o-col-sm">
								<div class="o-flag">
									<div class="o-flag__image o-flag--top">
										<?php echo ED::themes()->html('user.avatar', $post->getOwner(), array('rank' => true, 'status' => true, 'size' => 'md')); ?>
									</div>
									<div class="o-flag__body">
										<?php echo ED::themes()->html('user.username', $post->getOwner()); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				<div class="">
					<div class="m-your-discussions__content
					<?php echo $post->isSeen(ED::user()->id) ? ' is-read' : '';?>
					<?php echo $post->isFeatured() ? ' is-featured' : '';?>
					<?php echo $post->isLocked() ? ' is-locked' : '';?>
					<?php echo $post->isProtected() ? ' is-protected' : '';?>
					<?php echo $post->isPrivate() ? ' is-private' : '';?>
					">
						<div class="m-post-title">
							<?php if ($params->get('showpoststate', 1) && ($post->isFeatured() || $post->isLocked() || $post->isProtected() || $post->isPrivate())) { ?>
								<span class="m-post-status t-lg-mr--sm">
									<i class="fa fa-star t-icon--warning ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_FEATURED_DESC');?>"></i>

									<i class="fa fa-lock ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_LOCKED_DESC');?>"></i>

									<i class="fa fa-key ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PROTECTED_DESC');?>"></i>

									<i class="fa fa-eye ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PRIVATE_DESC');?>"></i>
								</span>
							<?php } ?>
							<a href="<?php echo $post->getPermalink(); ?>" class="m-post-title__link">
								<?php echo $title; ?>
							</a>
						</div>

						<?php if ($params->get('show_content')) { ?>
						<div class="m-post-content">
							<?php if (!$post->isProtected()) { ?>
								<?php echo $postcontent; ?>
							<?php } else { ?>
								<i><?php echo JText::_('MOD_EASYDISCUSS_RECENTDISCUSSIONS_PASSWORD_PROTECTED'); ?></i>
							<?php } ?>
						</div>
						<?php } ?>


						<div class="m-list--inline m-list--has-divider t-lg-mb-sm">
							<?php if ($params->get('showhits', 1)) { ?>
								<div class="m-list__item t-fs--sm">
									<div class="">
										<?php echo JText::sprintf('MOD_EASYDISCUSS_RECENTDISCUSSIONS_HITS_COUNT', $post->getHits()); ?>
									</div>
								</div>
							<?php } ?>

							<?php if ($params->get('showtotalvotes', 1)) { ?>
								<div class="m-list__item t-fs--sm">
									<div class="">
										<?php echo JText::sprintf('MOD_EASYDISCUSS_RECENTDISCUSSIONS_VOTES_COUNT', $post->getTotalVotes()); ?>
									</div>
								</div>
							<?php } ?>

							<?php if ($params->get('showreplycount')) { ?>
							<div class="m-list__item t-fs--sm">
								<div class="">
									<?php echo JText::sprintf('MOD_EASYDISCUSS_RECENTDISCUSSIONS_REPLIES', $post->getTotalReplies()); ?>
								</div>
							</div>
							<?php } ?>
						</div>

						<div class="m-list--inline m-list--has-divider t-lg-mb-sm">

							<?php if ($params->get('show_category', 1)) { ?>
								<div class="m-list__item t-fs--sm">
									<?php echo JText::sprintf('MOD_EASYDISCUSS_RECENTDISCUSSIONS_POSTED_IN_CATEGORY', $post->getCategory()->getPermalink(), $post->getCategory()->getTitle()); ?>
								</div>
							<?php } ?>
							<?php if ($params->get('show_date', 1)) { ?>
								<div class="m-list__item t-fs--sm">
									<?php echo JText::sprintf('MOD_EASYDISCUSS_RECENTDISCUSSIONS_POSTED_ON', $post->date); ?>
								</div>
							<?php } ?>
						</div>

						<?php if ($params->get('showpoststatus', 1)) { ?>
							<div class="">
								<?php if ($post->isResolved()) { ?>
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
							</div>
						<?php } ?>
						<?php if ($post->getTags() && $params->get('showtags')) { ?>
							<ul class="o-nav">
							<?php foreach ($post->getTags() as $tag) { ?>
								<li class="t-lg-mr--md">
									<span class="o-label o-label--default-o">#<?php echo $tag->title; ?></span>
								</li>
							<?php } ?>
							</ul>
						<?php } ?>


					</div>
				</div>
			</div>

		</div>
		<?php } ?>
	</div>

	<?php } else { ?>

		<div><?php echo JText::_('MOD_EASYDISCUSS_RECENTDISCUSSIONS_NO_ENTRIES'); ?></div>

	<?php } ?>
</div>
