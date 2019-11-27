<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div id="ed" class="ed-mod m-recent-replies">
	<div class="ed-list--vertical has-dividers--bottom-space">
		<?php foreach ($posts as $post) { ?>
		<div class="ed-mod__section">
			<div class="m-post-info__content
			<?php echo $post->isSeen(ED::user()->id) ? ' is-read' : '';?>
			<?php echo $post->isFeatured() ? ' is-featured' : '';?>
			<?php echo $post->isLocked() ? ' is-locked' : '';?>
			<?php echo $post->isProtected() ? ' is-protected' : '';?>
			<?php echo $post->isPrivate() ? ' is-private' : '';?>
			"
		>
				<?php if ($params->get('showpoststate', 1) && ($post->isFeatured() || $post->isLocked() || $post->isProtected() || $post->isPrivate())) { ?>
					<div class="m-post-info__status">
						<i class="fa fa-star ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_FEATURED_DESC');?>"></i>

						<i class="fa fa-lock ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_LOCKED_DESC');?>"></i>

						<i class="fa fa-key ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PROTECTED_DESC');?>"></i>

						<i class="fa fa-eye ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PRIVATE_DESC');?>"></i>
					</div>
				<?php } ?>
				<?php if ($params->get('showauthor', 1)) { ?>
					<div class="o-flag t-lg-mb--md">
						<div class="o-flag__image">
               				<?php echo ED::themes()->html('user.avatar', $post->user, array('rank' => true, 'status' => true, 'size' => 'md')); ?>
						</div>
						<div class="o-flag__body">
							<?php echo ED::themes()->html('user.username', $post->user, array('posterName' => $post->poster_name)); ?>
							<div class="ed-user-rank t-lg-mb--sm"><?php echo ED::getUserRanks($post->user->id); ?></div>
						</div>
					</div>
				<?php } ?>
				<div class="">
					<a href="<?php echo $post->getLastReplyPermalink($post->last_reply_id); ?>" class="m-post-title">
						<?php echo ED::string()->escape($post->title); ?>
					</a>
				</div>
				<?php if ($params->get('showreplycontent', 1)) { ?>
					<div class="">
						<?php echo $post->content; ?>
					</div>
				<?php } ?>

				<?php if ($params->get('showcategory', 1)) { ?>
					<div class="t-fs--sm">
						<?php echo JText::sprintf('MOD_EASYDISCUSS_RECENT_REPLIES_POSTED_IN_CATEGORY', $post->getCategory()->getPermalink(), $post->getCategory()->getTitle()); ?>
					</div>
				<?php } ?>
				<?php if ($params->get('showdate', 1)) { ?>
					<div class="t-fs--sm">
						<?php echo JText::sprintf('MOD_EASYDISCUSS_RECENT_REPLIES_POSTED_ON', ED::date($post->replied)->format(JText::_('DATE_FORMAT_LC1'))); ?>
					</div>
				<?php } ?>

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
				<?php if ($params->get('showreplycount', 1)) { ?>
					<div class="ed-mod__section">
						<i class="fa fa-comment"></i> <?php echo JText::sprintf('MOD_RECENT_REPLIES_REPLIES', $post->getTotalReplies()); ?>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
