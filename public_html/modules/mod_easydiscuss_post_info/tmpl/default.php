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
<div id="ed" class="ed-mod ed-mod--welcome <?php echo $lib->getModuleWrapperClass();?>">
	<div class="ed-mod-card">
		<div class="ed-mod-card__body">
			<div class="o-card t-bg--100 <?php echo $post->getWrapperClass();?>">
				<?php if ($params->get('showauthor', 1)) { ?>
					<div class="o-card__body l-stack">
						<div class="o-media o-media--top">
							<div class="o-media__image t-text--truncate">
								<?php if ($post->isAnonymous()) { ?>
									<?php echo $lib->html('user.anonymous', $post->getOwner(), ['rank' => true, 'status' => true, 'size' => 'md']); ?>
								<?php } else { ?>
									<?php echo $lib->html('user.avatar', $post->getOwner(), ['rank' => true, 'status' => true, 'size' => 'md']); ?>
								<?php } ?>
							</div>
						</div>
						<div class="l-spaces--sm">
							<?php if ($post->isAnonymous()) { ?>
								<?php echo $lib->html('user.username', $post->getOwner(), ['posterName' => $post->getOwner()->getName(), 'isAnonymous' => true, 'canViewAnonymousUsername' => $post->canAccessAnonymousPost()]); ?>
							<?php } else { ?>
								<?php echo $lib->html('user.username', $post->getOwner(), []);?>
							<?php } ?>

							<?php if (!$post->isAnonymous()) { ?>
							<div class="o-meta l-spaces--sm">
								<?php echo ED::ranks()->getRank($post->getOwner()->id); ?>
							</div>
							<?php } ?>
						</div>
					</div>

					<hr>
				<?php } ?>

				<div class="o-card__body l-stack">
					<div class="o-title-01"><?php echo JText::_('MOD_POST_INFO_POSTED'); ?>:</div>
					<div class="o-body">
						<?php echo $post->created; ?>
							
						<?php if ($params->get('showcategory', 1)) { ?>
							<div class="t-d--flex">
								<?php echo JText::_('MOD_POST_INFO_IN'); ?>
								&nbsp;
								<?php echo $lib->html('category.title', $post->getCategory(), []);?>
							</div>
						<?php } ?>
					</div>
					
				</div>

				<?php if ($params->get('showpoststate', 1) && ($post->isStillNew() || $post->hasLabel() || $post->getPostTypeObject())) { ?>
					<hr>
					<div class="o-card__body l-stack">
						<div class="o-title-01"><?php echo JText::_('COM_ED_FILTERS_POST_LABELS'); ?>:</div>
						<div class="o-body ed-post-status-bar l-stack">
							<div class="l-cluster">
								<div class="">
									<?php if ($params->get('showpoststate', 1) && $post->isFeatured()) { ?>
										<?php echo $lib->html('post.featured'); ?>
									<?php } ?>

									<?php if ($params->get('showpoststatus', 1)) { ?>
										<?php echo $lib->html('post.resolved'); ?>

										<?php echo $lib->html('post.locked'); ?>
									<?php } ?>

									<?php echo $lib->html('post.priority', $post);?>
									
									<?php if ($post->isStillNew()) { ?>
										<?php echo $lib->html('post.newLabel'); ?>
									<?php } ?>
								</div>
							</div>

							<div class="l-spaces--sm">
								<div class="o-label-group t-text--truncate">
									<?php if ($post->hasLabel()) { ?>
										<?php echo $lib->html('post.label', $post->getCurrentLabel()); ?>
									<?php } ?>

									<?php if ($post->getPostTypeObject() && $params->get('showposttype')) { ?>
										<?php echo $lib->html('post.type', $post); ?>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<?php if ($post->getTags() && $params->get('showtags', 1)) { ?>
				<div class="o-card__body l-stack">
					<div class="o-title-01"><?php echo JText::_('COM_EASYDISCUSS_TAGS'); ?>:</div>
					<div class="o-meta l-cluster">
						<?php echo $lib->html('post.tags', $post);?>
					</div>
				</div>
				<?php } ?>


				<?php if ($post->getAttachments() && $params->get('showattachment')) { ?>
				<div class="o-card__body l-stack">
					<div class="o-title-01"><?php echo JText::_('MOD_POST_INFO_ATTACHMENTS'); ?>:</div>
					<div class="">
						<?php echo $lib->html('post.attachments', $post);?>
					</div>
				</div>
				<?php } ?>

				<?php if ($post->getParticipants() && $params->get('showparticipants')) { ?>
				<div class="o-card__body l-stack">
					<div class="o-title-01"><?php echo JText::_('MOD_POST_INFO_PARTICIPANTS'); ?>:</div>
					<div class="">
						<?php foreach ($post->getParticipants() as $participant) { ?>
							<?php echo $lib->html('user.avatar', $participant, ['status' => true, 'size' => 'md']); ?>
						<?php } ?>
					</div>
				</div>
				<?php } ?>

				<?php if ($params->get('showreplycount')) { ?>
				<div class="o-card__body l-stack">
					<div class="o-title-01"><?php echo JText::_('COM_EASYDISCUSS_USER_REPLIES'); ?>:</div>
					<div class="">
						<?php echo $lib->html('post.replies', $post); ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
