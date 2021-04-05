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
<div class="l-stack <?php echo !$posts ? 'is-empty' : '';?>">
	<?php if ($posts) { ?>
		<?php foreach ($posts as $post) { ?>
			<div class="o-card o-card--ed-dashboard-item" data-ed-pending data-id="<?php echo $post->id; ?>">
				<div class="o-card__body l-stack">
					<div class="t-d--flex">
						<div class="t-flex-grow--1">
							<h2 class="o-title t-my--no">
								<?php if ($post->isQuestion()) { ?>
									<a href="<?php echo EDR::_('view=ask&id=' . $post->id); ?>" class="si-link"><?php echo $post->title; ?></a>
								<?php } else { ?>
									<?php echo JText::sprintf('COM_ED_VIEW_THIS_REPLY', $post->getPermalink(), $post->getParent()->getPermalink(), $post->getParent()->title) ?>
								<?php } ?>
							</h2>
						</div>
						<div>
						</div>
					</div>

					<div class="o-meta l-cluster">
						<div>
							<div>
								<?php echo $this->html('user.avatar', $post->getOwner()); ?>&nbsp; <?php echo $this->html('user.username', $post->getOwner()); ?>
							</div>
							<div class="t-font-size--03 t-font-weight--bold">·</div>
							<div>
								<?php echo ED::date()->toLapsed($post->modified);?>
							</div>
						</div>
					</div>
				</div>

				<div class="o-card__footer l-stack">
					<div class="t-d--flex sm:t-flex-direction--c ">
						<div class="t-flex-grow--1">
							<span class="o-label t-bg--dark">
							<?php if ($post->isReply()) { ?>
								<?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_MANAGE_POST_TYPE_REPLY');?>
							<?php } else { ?>
								<?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_MANAGE_POST_TYPE_QUESTION');?>
							<?php } ?>
							</span>
						</div>
						<div class="t-text--right">
							<div class="l-cluster l-spaces--sm">
								<div>
									<div>
										<button type="button" class="o-btn o-btn--default-o o-btn--sm" data-ed-moderate="confirmRejectPost">
											<?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_MANAGE_POST_REJECT');?>
										</button>
									</div>
									<div>
										<button type="button" class="o-btn o-btn--primary o-btn--sm" data-ed-moderate="confirmApprovePost">
											<?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_MANAGE_POST_APPROVE');?>
										</button>
									</div>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>

	<?php echo $this->html('card.emptyCard', 'fa fa-newspaper', 'COM_EASYDISCUSS_DASHBOARD_NO_POST_TO_MANAGE'); ?>
</div>
