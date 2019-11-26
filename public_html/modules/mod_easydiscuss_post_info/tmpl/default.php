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
<div id="ed" class="ed-mod m-post-info <?php echo $params->get('moduleclass_sfx');?>">
<div class="ed-mod__section">
	<div class="m-post-info__content
			<?php echo $post->isSeen(ED::user()->id) ? ' is-read' : '';?>
			<?php echo $post->isFeatured() ? ' is-featured' : '';?>
			<?php echo $post->isLocked() ? ' is-locked' : '';?>
			<?php echo $post->isProtected() ? ' is-protected' : '';?>
			<?php echo $post->isPrivate() ? ' is-private' : '';?>">
			<?php if ($params->get('showpoststate', 1) && ($post->isFeatured() || $post->isLocked() || $post->isProtected() || $post->isPrivate())) { ?>
				<div class="m-post-info__status">
					<i class="fa fa-star ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_FEATURED_DESC');?>"></i>

					<i class="fa fa-lock ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_LOCKED_DESC');?>"></i>

					<i class="fa fa-key ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PROTECTED_DESC');?>"></i>

					<i class="fa fa-eye ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PRIVATE_DESC');?>"></i>
				</div>
			<?php } ?>
		<div class="ed-list--vertical has-dividers--bottom-space">
			<div class="ed-list__item">
				<div class="o-flag">
					<div class="o-avatar-status<?php echo ($post->getOwner()->isOnline()) ? ' is-online': ' is-offline'; ?>">
						<div class="o-flag__image">
							<?php echo ED::themes()->html('user.avatar', $post->getOwner(), array('rank' => true, 'status' => true, 'size' => 'md')); ?>
						</div>
					</div>
					<div class="o-flag__body">
						<?php echo ED::themes()->html('user.username', $post->getOwner()); ?>
							<div class="t-fs--sm">( <?php echo ED::ranks()->getRank($post->getOwner()->id); ?> )</div>
					</div>
				</div>
			</div>

			<div class="ed-list__item">
				<?php echo JText::_('MOD_POST_INFO_POSTED'); ?> <b><?php echo $post->created; ?></b>
				<?php echo JText::_('MOD_POST_INFO_IN'); ?> <b><?php echo $post->getCategory()->getTitle(); ?></b>
			</div>


			<?php if ($params->get('showpoststatus') && ($post->hasStatus() || $post->isStillNew())) { ?>
			<div class="ed-list__item">

				<?php if ($post->isStillNew()) { ?>
					<span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_NEW');?></span>
				<?php } ?>

				<!-- post status here: accepted, onhold, working rejected -->
				<?php if ($post->isPostRejected()) { ?>
					<span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_REJECT');?></span>
				<?php } ?>
				<?php if ($post->isPostOnhold()) { ?>
					<span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_ON_HOLD');?></span>
				<?php } ?>
				<?php if ($post->isPostAccepted()) { ?>
					<span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_ACCEPTED');?></span>
				<?php } ?>
				<?php if ($post->isPostWorkingOn()) { ?>
					<span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_WORKING_ON');?></span>
				<?php } ?>
			</div>
			<?php } ?>

			<?php if ($params->get('showposttype') && $post->getPostType()) { ?>
			<div class="ed-list__item">
				<!-- post type here -->
				<span class="o-label o-label--clean-o <?php echo $post->getTypeSuffix(); ?>"><?php echo $post->getPostType(); ?></span>
			</div>
			<?php } ?>

			<?php if ($post->getTags() && $params->get('showtags')) { ?>
			<div class="ed-list__item">
				<div>
					<b><?php echo JText::_('MOD_POST_INFO_TAGS'); ?></b>
				</div>
				<ul class="o-nav">
				<?php foreach ($post->getTags() as $tag) { ?>
					<li class="t-lg-mr--md">
						<span class="o-label o-label--default-o">#<?php echo $tag->title; ?></span>
					</li>
				<?php } ?>
				</ul>
			</div>
			<?php } ?>
			<?php if ($post->getAttachments() && $params->get('showattachment')) { ?>
			<div class="ed-list__item">
				<div>
					<b><?php echo JText::_('MOD_POST_INFO_ATTACHMENTS'); ?></b>
				</div>
				<ul class="o-nav o-nav  o-nav--stacked">
					<?php foreach ($post->getAttachments() as $attachment) { ?>
						<li><?php echo $attachment->html(); ?></li>
					<?php } ?>
				</ul>
			</div>
			<?php } ?>
			<?php if ($post->getParticipants() && $params->get('showparticipants')) { ?>
			<div class="ed-list__item">
				<div>
					<b><?php echo JText::_('MOD_POST_INFO_PARTICIPANTS'); ?></b>
				</div>
				<div class="o-avatar-list">
				<?php foreach ($post->getParticipants() as $participant) { ?>
					<div class="o-avatar-list__item">
						<?php echo ED::themes()->html('user.avatar', $participant, array('rank' => true, 'status' => true, 'size' => 'md')); ?>
					</div>
				<?php } ?>
				</div>
			</div>
			<?php } ?>
			<?php if ($params->get('showreplycount')) { ?>
			<div class="ed-list__item">
				<i class="fa fa-comment"></i> <?php echo JText::sprintf('MOD_POST_INFO_REPLIES', $post->getTotalReplies()); ?>
			</div>
			<?php } ?>

		</div>
	</div>
</div>
</div>

