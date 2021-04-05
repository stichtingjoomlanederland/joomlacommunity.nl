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
<div id="ed" class="ed-mod ed-mod--most-hit <?php echo $lib->getModuleWrapperClass();?>">
	<div class="ed-mod-card">
		<div class="ed-mod-card__body">
			<?php foreach ($posts as $post) { ?>
			<div class="o-card t-bg--100 <?php echo $post->getWrapperClass();?>">
				<div class="o-card__body l-stack">
					<div class="o-meta">
						<?php if (
							($params->get('showpoststate', 1) && $post->isFeatured()) 
							|| ($params->get('showpoststatus', 1) && ($post->isLocked() || $post->isResolved())) 
							|| ($post->hasPriority()) 
							|| ($post->isStillNew())
							|| ($post->hasLabel() || $post->getPostTypeObject())) { 
						?>
						<div class="ed-post-status-bar l-stack">
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

							<?php if ($post->hasLabel() || $post->getPostTypeObject()) { ?>
							<div class="l-spaces--sm">
								<div class="o-label-group t-text--truncate">
									<?php if ($post->hasLabel()) { ?>
										<?php echo $lib->html('post.label', $post->getCurrentLabel()); ?>
									<?php } ?>

									<?php if ($post->getPostTypeObject()) { ?>
										<?php echo $lib->html('post.type', $post); ?>
									<?php } ?>
								</div>
							</div>
							<?php } ?>
						</div>
						<?php } ?>
					</div>
					<?php echo $lib->html('post.title', $post, ['customClass' => 'o-title si-link t-d--inline-block l-spaces--sm']); ?>
					<div class="o-meta t-flex-grow--1 l-cluster">
						<div class="">
							<?php if ($params->get('showtotalvotes', 1) || $params->get('showreplycount', 1)|| $params->get('showhits', 1)) { ?>
							<div class="">
								<?php if ($params->get('showtotalvotes', 1)) { ?>
									<?php echo JText::sprintf('MOD_EASYDISCUSS_MOST_HIT_VOTES_COUNT', $post->getTotalVotes()); ?>
								<?php } ?>

								<?php if ($params->get('showreplycount', 1)) { ?>
									<?php echo JText::sprintf('MOD_EASYDISCUSS_MOST_HIT_REPLIES', $post->getTotalReplies()); ?>
								<?php } ?>

								<?php if ($params->get('showhits', 1)) { ?>
									<?php echo JText::sprintf('MOD_EASYDISCUSS_MOST_HIT_HITS_COUNT', $post->getHits()); ?>
								<?php } ?>
							</div>
							<?php } ?>
							
							<?php if ($params->get('showauthor', 1)) { ?>
							<div class="t-min-width--0 t-d--flex t-align-items--c" data-user-avatar="" data-isanonymous="0">
								<?php if ($post->isAnonymous()) { ?>
									<?php echo $lib->html('user.anonymous', $post->user, []); ?>
									&nbsp;
									<?php echo $lib->html('user.username', $post->user, ['posterName' => $post->user->getName(), 'isAnonymous' => true, 'canViewAnonymousUsername' => $post->canAccessAnonymousPost()]); ?>
								<?php } else { ?>
									<?php echo $lib->html('user.avatar', $post->user, []); ?>
									&nbsp;
									<?php echo $lib->html('user.username', $post->user, []);?>
								<?php } ?>
							</div>
							<?php } ?>

							<?php if ($params->get('showcategory', 1)) { ?>
							<div class="">
								<?php echo $lib->html('post.category', $post->getCategory(), []);?>
							</div>
							<?php } ?>

							<?php if ($params->get('showdate', 1)) { ?>
							<div class="">
								<?php echo JText::sprintf('MOD_EASYDISCUSS_MOST_HIT_POSTED_ON', ED::date($post->created)->format(JText::_('DATE_FORMAT_LC1'))); ?>
							</div>
							<?php } ?>
						</div>

						<?php if ($post->getTags() && $params->get('showtags', 1)) { ?>
							<div class="">
								<?php echo $lib->html('post.tags', $post);?>
							</div>
						<?php } ?>

						<?php if ($params->get('showlastreply', 1) && $post->lastReply) { ?>
							<div class="">
								<a class="ml-5" href="<?php echo $post->getLastReplyPermalink($post->lastReply->id);?>" title="<?php echo JText::_('MOD_EASYDISCUSS_VIEW_LAST_REPLY'); ?>"><?php echo JText::_('MOD_EASYDISCUSS_VIEW_LAST_REPLY');?></a>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>