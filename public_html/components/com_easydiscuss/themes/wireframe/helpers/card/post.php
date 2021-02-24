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
<div class="o-card <?php echo $wrapperClass;?> <?php echo $post->getWrapperClass();?>">
	<div class="o-card__body">
		<div class="l-stack">

			<?php if ($isSearch) { ?>
			<div class="lg:t-d--flex">
				<div class="o-label <?php echo $post->isQuestion() ? 't-bg--primary' : 't-bg--warning';?>">
					<?php echo $post->isQuestion() ? JText::_('COM_ED_GDPR_POSTTYPE_POST') : JText::_('COM_ED_GDPR_POSTTYPE_REPLY') ; ?>
				</div>
			</div>
			<?php } ?>

			<div class="lg:t-d--flex md:t-d--flex t-align-items--c">
				<div class="t-flex-grow--1 t-min-width--0">
					<h2 class="o-title t-my--no t-text--truncate">
						<?php if ($post->isFeatured()) { ?>
							<?php echo $this->html('post.featured'); ?>
						<?php } ?>

						<?php echo $this->html('post.new'); ?>
						
						<?php echo $this->html('post.title', $post); ?>
					</h2>
				</div>

				<?php if ($post->isFeatured() || $post->isResolved() || $post->isLocked() || $post->isProtected() || $post->isPrivate() || $post->hasPriority()) { ?>
				<div class="t-flex-shrink--0 sm:t-mt--md">
					<div class="o-body ed-post-status-bar l-cluster">
						<div>
							<?php echo $this->html('post.locked', $post); ?>
							<?php echo $this->html('post.password', $post); ?>
							<?php echo $this->html('post.hidden', $post); ?>
							<?php echo $this->html('post.resolved'); ?>
							<?php echo $this->html('post.priority', $post); ?>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
			
			<div class="lg:t-d--flex md:t-d--flex  t-align-items--c">
				<div class="t-flex-grow--1 o-meta l-cluster">
					<div class="">
						<?php echo $this->html('post.category', $post->getCategory()); ?>

						<?php echo $this->html('post.author', $post); ?>

						<?php if ($post->isQuestion()) { ?>
							<?php echo $this->html('post.lastReplied', $post); ?>
						<?php } ?>
					</div>
				</div>

				<?php if ($post->isQuestion()) { ?>
				<div class="t-flex-shrink--0 sm:t-mt--md o-meta l-cluster">
					<div>
						<div class="o-label-group" style="max-width: 280px">
							<?php if ($post->hasLabel()) { ?>
								<?php echo $this->html('post.label', $post->getCurrentLabel()); ?>
							<?php } ?>

							<?php if ($post->getPostTypeObject()) { ?>
								<?php echo $this->html('post.type', $post); ?>
							<?php } ?>
						</div>

						<?php echo $this->html('post.replies', $post); ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>