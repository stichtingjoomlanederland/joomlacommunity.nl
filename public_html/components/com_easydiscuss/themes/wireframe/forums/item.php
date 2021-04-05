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
<div class="ed-forums-item t-p--md t-bg--100 <?php echo $post->getHeaderClass(); ?>">
	<div class="lg:t-d--flex t-align-items--c">
		<div class="t-flex-grow--1 sm:t-mb--lg">
			<h2 class="o-title t-mt--no">
				<a href="<?php echo $post->getPermalink();?>" class="si-link">
					<?php echo $post->getTitle(); ?>
				</a>

				<?php if ($post->isFeatured()) { ?>
					<?php echo $this->html('post.featured'); ?>
				<?php } ?>
			</h2>

			<div class="lg:t-d--flex">
				<?php if ($post->isStillNew() || $post->isResolved() || $post->getPostType()) { ?>
				<div>
					<div class="o-label-group t-mr--md">
						<?php if ($post->isStillNew()) { ?>
							<?php echo $this->html('post.newLabel'); ?>
						<?php } ?>

						<?php if ($post->hasLabel()) { ?>
							<?php echo $this->html('post.postLabel', $post->getCurrentLabel()); ?>
						<?php } ?>

						<?php if ($post->isResolved()) { ?>
							<?php echo $this->html('post.resolved'); ?>
						<?php } ?>

						<?php if ($post->getPostTypeObject()) { ?>
							<?php echo $this->html('post.type', $post); ?>
						<?php } ?>
					</div>
				</div>
				<?php } ?>

				<div class="o-meta">
					<?php echo $this->html('post.lastReplied', $post); ?>

					<?php echo $this->html('post.replies', $post); ?>

					<?php echo $this->html('post.locked', $post); ?>
					<?php echo $this->html('post.protected', $post); ?>

					<?php if ($post->isLocked()) { ?>
						<?php echo $this->html('post.locked', $post); ?>
					<?php } ?>

					<?php if ($post->isProtected()) { ?>
						<?php echo $this->html('post.protected', $post); ?>
					<?php } ?>
				</div>
			</div>
		</div>

		<div>
			<div class="l-cluster l-spaces--negative">
				<?php echo $this->html('post.participants', $post->getParticipants(5)); ?>
			</div>
		</div>
		
	</div>
</div>