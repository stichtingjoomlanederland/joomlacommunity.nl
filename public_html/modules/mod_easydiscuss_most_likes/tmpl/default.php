<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div id="ed" class="ed-mod m-most-likes">
	<div class="ed-list--vertical has-dividers--bottom-space">
		<?php foreach ($posts as $post) { ?>
		<div class="ed-list__item">
			<div class="m-most-likes__content
			<?php echo $post->isSeen(ED::user()->id) ? ' is-read' : '';?>
			<?php echo $post->isFeatured() ? ' is-featured' : '';?>
			<?php echo $post->isLocked() ? ' is-locked' : '';?>
			<?php echo $post->isProtected() ? ' is-protected' : '';?>
			<?php echo $post->isPrivate() ? ' is-private' : '';?>
			"
		>
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
						<?php echo ED::string()->escape($post->title); ?>
					</a>
				</div>
				<?php if ($params->get('showauthor', 1)) { ?>
					<div class="m-list--inline m-list--has-divider t-lg-mb-sm">
						<div class="o-flag t-lg-mb--md">
							<div class="o-flag__image">
								<?php echo ED::themes()->html('user.avatar', $post->user, array('rank' => true, 'status' => true, 'size' => 'md')); ?>
							</div>
							<div class="o-flag__body">
								<?php echo ED::themes()->html('user.username', $post->user); ?>
								<div class="ed-user-rank t-lg-mb--sm"><?php echo ED::getUserRanks($post->user->id); ?></div>
							</div>
						</div>
					</div>
				<?php } ?>
				<div class="m-list--inline m-list--has-divider t-lg-mb-sm">
					<?php if ($params->get('showlikes', 1)) { ?>
						<div class="m-list__item">
							<div class="">
								<?php echo JText::sprintf('MOD_EASYDISCUSS_MOST_LIKES_LIKES_COUNT', $post->getTotalLikes()); ?>
							</div>
						</div>
					<?php } ?>

					<?php if ($params->get('showtotalvotes', 1)) { ?>
						<div class="m-list__item">
							<div class="">
								<?php echo JText::sprintf('MOD_EASYDISCUSS_MOST_LIKES_VOTES_COUNT', $post->getTotalVotes()); ?>
							</div>
						</div>
					<?php } ?>

					<?php if ($params->get('showreplycount', 1)) { ?>
					<div class="m-list__item">
						<div class="">
							<?php echo JText::sprintf('MOD_EASYDISCUSS_MOST_LIKES_REPLIES', $post->getTotalReplies()); ?>
						</div>
					</div>
					<?php } ?>
				</div>

				<div class="m-list--inline m-list--has-divider t-lg-mb-sm">
					<?php if ($params->get('showcategory', 1)) { ?>
						<div class="m-list__item">
							<?php echo JText::sprintf('MOD_EASYDISCUSS_MOST_LIKES_POSTED_IN_CATEGORY', $post->getCategory()->getPermalink(), $post->getCategory()->getTitle()); ?>
						</div>
					<?php } ?>
					<?php if ($params->get('showdate', 1)) { ?>
						<div class="m-list__item">
							<?php echo JText::sprintf('MOD_EASYDISCUSS_MOST_LIKES_POSTED_ON', ED::date($post->created)->format(JText::_('DATE_FORMAT_LC1'))); ?>
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
				<?php if ($post->getTags() && $params->get('showtags', 1)) { ?>
					<ul class="o-nav">
					<?php foreach ($post->getTags() as $tag) { ?>
						<li class="t-lg-mr--md">
							<span class="o-label o-label--default-o">#<?php echo $tag->title; ?></span>
						</li>
					<?php } ?>
					</ul>
				<?php } ?>

				<?php if ($params->get('showlastreply', 1) && $post->getLastReplier()) { ?>
				<?php $lastReply = ED::model('Posts')->getLastReply($post->id);  ?>
					<div class="ed-mod__section">
						<?php if ($post->getLastReplier()->id) { ?>
							<?php if ($config->get('layout_avatar')) { ?>
								<a href="<?php echo $post->getLastReplier()->getPermalink();?>" class="o-avatar o-avatar--sm" >
									<img src="<?php echo $post->getLastReplier()->getAvatar();?>" alt="<?php echo ED::string()->escape($post->getLastReplier()->getName());?>"/>
								</a>
							<?php } ?>
						<?php } else { ?>
							<?php echo $post->getLastReplier()->poster_name; ?>
						<?php } ?>
						<a class="ml-5" href="<?php echo EDR::getPostRoute($post->id) . '#' . JText::_('MOD_EASYDISCUSS_REPLY_PERMALINK') . '-' . $lastReply->id;?>" title="<?php echo JText::_('MOD_EASYDISCUSS_VIEW_LAST_REPLY'); ?>"><?php echo JText::_('MOD_EASYDISCUSS_VIEW_LAST_REPLY');?></a>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
