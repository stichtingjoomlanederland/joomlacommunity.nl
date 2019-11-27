<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="<?php echo $post->isPending() ? 'ed-post-pending' : ''; ?> ed-reply-item <?php echo $poll && $poll->isLocked() ? ' is-lockpoll' : '';?>" 
	data-ed-post-item 
	data-ed-reply-item 
	data-id="<?php echo $post->id;?>"
>
	<a name="<?php echo JText::_('COM_EASYDISCUSS_REPLY_PERMALINK');?>-<?php echo $post->id;?>"></a>

	<div class="ed-reply-item__hd">

		<div class="o-row">
			<div class="o-col">
				<div class="o-grid">
					<div class="o-grid__cell o-grid__cell--auto-size t-lg-pr--md t-xs-pb--md">
						<div class="o-flag">

							<?php if ($post->isAnonymous()) { ?>
								
								<?php if ($this->config->get('layout_avatar_in_post')) { ?>
								<div class="o-flag__image">
									<img src="<?php echo ED::getDefaultAvatar();?>" width="24" height="24" />
								</div>
								<?php } ?>

								<div class="o-flag__body">
									<?php if ($post->canAccessAnonymousPost()) { ?>
										<?php echo $this->html('user.username', $post->getOwner(), array('isAnonymous' => true, 'canViewAnonymousUsername' => $post->canAccessAnonymousPost(), 'lgMarginBottom' => true)); ?>
									<?php } else { ?>
										<?php echo $this->html('user.username', $post->getOwner(), array('isAnonymous' => true, 'lgMarginBottom' => true)); ?>
									<?php } ?>
								</div>
							<?php } ?>
							
							<?php if (!$post->isAnonymous()) { ?>
								<div class="o-flag__image">
									<?php echo $this->html('user.avatar', $post->getOwner(), array('rank' => false, 'status' => false)); ?>  
								</div>

								<div class="o-flag__body">
									<?php echo $this->html('user.username', $post->getOwner(), array('posterName' => $post->poster_name, 'lgMarginBottom' => true)); ?>
									
									<div class="ed-user-rank t-lg-mb--sm o-label o-label--<?php echo $post->getOwner()->getRoleLabelClassname()?>"><?php echo $post->getOwner()->getRole(); ?></div>

									<?php if ($this->config->get('main_ranking')) { ?>
										<div class="ed-rank-bar">
											<div style="width: <?php echo ED::ranks()->getScore($post->getOwner()->id, true); ?>%" class="ed-rank-bar__progress"></div>
										</div>
									<?php } ?>
								</div>
							<?php } ?>
						</div>
					</div>
					
					<?php if (ED::badges()->isEnabled() && $post->getOwner()->hasUserBadges()) { ?>
					<div class="o-grid__cell o-grid__cell--auto-size o-grid__cell--center t-lg-pr--md">
						<?php echo ED::badges()->getPostHtml($post->getOwner()->id); ?>					
					</div>
					<?php } ?>
					
					<div class="o-grid__cell">
						<span class="o-label o-label--success-o ed-state-answer"><?php echo JText::_('COM_EASYDISCUSS_ENTRY_ACCEPTED_ANSWER'); ?></span>

						<span class="o-label o-label--primary-o ed-state-pending"><?php echo JText::_('COM_EASYDISCUSS_PENDING_MODERATION'); ?></span>
					</div>
				</div>
			</div>

			<div class="o-col">
				<div class="t-lg-pull-right">
					<?php if (!$post->isPending()) { ?>
						<?php echo $this->output('site/post/default.actions', array('post' => $post)); ?>
					<?php } else { ?>
						<?php echo $this->output('site/post/default.moderator.actions', array('post' => $post)); ?>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<div class="ed-reply-item__bd">
		
		<div data-ed-reply-editor></div>

		<div class="o-row">
			<?php echo $this->output('site/post/default.vote', array('post' => $post)); ?>

			<div class="ed-reply-content">
				<?php echo $post->getContent(true); ?>
			</div>

			<div class="ed-post-widget-group t-lg-mb--lg">
				<?php if ($post->hasPolls() && $this->config->get('main_polls_replies')) { ?>
					<?php echo $this->output('site/post/default.polls', array('post' => $post)); ?>
				<?php } ?>

				<?php if ($post->hasAttachments()) { ?>
					<?php echo $this->output('site/post/default.attachments', array('post' => $post)); ?>
				<?php } ?>
				
				<?php echo $this->output('site/post/default.fields', array('post' => $post)); ?>

				<?php echo $this->output('site/post/default.references', array('post' => $post, 'composer' => $composer)); ?>

				<?php echo $this->output('site/post/default.site.detail', array('post' => $post, 'composer' => $composer)); ?>
			</div>

			<?php echo ED::likes()->button($post); ?>

			<?php echo $this->output('site/post/default.signature', array('post' => $post)); ?>
		</div>
	</div>

	<?php echo $this->output('site/post/default.location', array('post' => $post)); ?>
	
	<?php if ($this->config->get('main_comment')) { ?>
		<div class="ed-reply-item__comments-wrap">
			<?php echo $this->output('site/comments/default', array('post' => $post)); ?>
		</div>
	<?php } ?>

	<div class="ed-reply-item__ft">
		<ol class="g-list-inline g-list-inline--dashed">
			<li><span class=""><?php echo $post->getDuration(); ?></span></li>
			<li>
				<a href="<?php echo EDR::getCategoryRoute($post->getCategory()->id); ?>"><?php echo JText::_($post->getCategory()->title); ?></a>
			</li>
			<li>
				<a data-ed-post-reply-seq="<?php echo $post->seq; ?>" href="<?php echo $post->permalink; ?>">
					<?php echo JText::sprintf('COM_EASYDISCUSS_REPLY_PERMALINK_TO', $post->seq); ?>
				</a>
			</li>
			
			<?php if ($post->getLastReplier()) { ?> 
			<li class="current">
				<div class="">
					<span><?php echo JText::_('COM_EASYDISCUSS_VIEW_LAST_REPLY'); ?>: </span>
					<a href="" class="o-avatar o-avatar--sm">
						<img src="<?php echo $post->getLastReplier()->getAvatar(); ?>"/>
					</a>
				</div>
			</li>
			<?php } ?>
		</ol>
	</div>
</div>