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
<div class="ed-post-item">
	<div class="ed-post-item__hd">
		<h2 class="ed-post-item__title t-lg-mt--md t-lg-mb--md">
			<?php if ($post->isPending() && ED::isModerator()) { ?>
				<a href="<?php echo EDR::_('view=ask&id=' . $post->id); ?>"><?php echo $post->getTitle(); ?></a>
			<?php } else if ($post->isPending() && $post->isMine()) { ?>
				<a href="javascript:void(0);" data-es-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_FILTER_PENDING_MODERATION'); ?>"><?php echo $post->getTitle(); ?></a>
			<?php } else { ?>
				<a href="<?php echo $post->getPermalink(); ?>"><?php echo $post->getTitle(); ?></a>
			<?php } ?>
		</h2>

		<?php if ($post->getTags()) { ?>
		<ol class="g-list-inline ed-post-meta-tag t-lg-mb--md">
			<?php foreach ($post->getTags() as $tag) { ?>
				<li><a href="<?php echo EDR::getTagRoute($tag->id); ?>"><i class="fa fa-tag"></i> <?php echo $this->html('string.escape', $tag->title); ?></a></li>
			<?php } ?>
		</ol>
		<?php } ?>

		<?php if ($post->isFeatured() || $post->isLocked()) { ?>
			<div class="ed-post-item__status">
				<?php if ($post->isFeatured()) { ?>
				<i class="fa fa-star"></i>
				<?php } ?>

				<?php if ($post->isLocked()) { ?>
				<i class="fa fa-lock"></i>
				<?php } ?>
			</div>
		<?php } ?>
	</div>

	<div class="ed-post-item__ft t-bdt-no">
		<ol class="g-list-inline g-list-inline--dashed">
			<li><span class=""><?php echo $post->getDuration(); ?></span></li>
			<li><a class="" href="<?php echo EDR::getCategoryRoute($post->getCategory()->id); ?>"><?php echo JText::_($post->getCategory()->title); ?></a></li>

			<?php if ($post->getLastReplier()) { ?>
			<li class="current">
				<div class="">
					<span><?php echo JText::_('COM_EASYDISCUSS_LAST_REPLIER'); ?>: </span>
					<?php if (!$post->isLastReplyAnonymous()) { ?>
						<?php echo $this->html('user.avatar', $post->getLastReplier(), array('rank' => false, 'size' => 'sm')); ?>
					<?php } else { ?>
						<?php echo $this->html('user.anonymous', $post->getOwner(), $post->isAnonymous()); ?>
					<?php } ?>
				</div>
			</li>
			<?php } ?>
			
			<?php if ($post->isPending()) { ?>
				<li><span class="o-label o-label--danger-o label-pending"><?php echo JText::_('COM_EASYDISCUSS_FILTER_PENDING_MODERATION'); ?></span></li>
			<?php } else if (!$post->getTotalReplies() && !$post->isResolved()) { ?>
				<li><span class="o-label o-label--danger-o label-unanswered"><?php echo JText::_('COM_EASYDISCUSS_FILTER_UNANSWERED'); ?></span></li>
			<?php } ?>
		</ol>
	</div>
</div>
