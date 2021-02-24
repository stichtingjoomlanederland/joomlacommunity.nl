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
<div class="ed-timeline__item <?php echo $post->isAnswer() && !$fromAnswer ? 't-d--none' : '';?>"
	data-ed-post-item
	data-ed-reply-item
	data-id="<?php echo $post->id;?>"
>
	<div>
		<div class="o-card o-card--ed-reply-item
		<?php echo $post->isPending() ? 'ed-post-pending' : ''; ?> 
		<?php echo $post->isAnswer() && $fromAnswer ? 'is-answer' : '';?>
		" data-ed-reply-card-wrapper>
			<div class="o-card__body l-stack">
				<div class="lg:t-d--flex t-align-items--c sm:t-flex-direction--c">
					<div class="t-flex-grow--1 t-min-width--0 lg:t-pr--lg sm:t-mb--md">
						<a id="<?php echo JText::_('COM_EASYDISCUSS_REPLY_PERMALINK');?>-<?php echo $post->id;?>"></a>

						<div class="o-media">
							<div class="o-media__image">
								<?php echo $this->html('user.avatar', $post->getOwner(), [
									'rank' => false, 
									'status' => false
								], $post->isAnonymous()); ?>
							</div>
							<div class="o-media__body t-text--truncate t-font-size--01">
								<?php echo $this->html('user.username', $post->getOwner(), [
									'isAnonymous' => $post->isAnonymous(),
								]); ?>
								
								<div class="">
								</div>
							</div>
						</div>
					</div>
					<div class="lg:t-ml--auto">
						<div class="ed-entry-actions" data-ed-post-actions-bar data-id="<?php echo $post->id;?>">
							<?php if ($post->isPending()) { ?>
							<div class="ed-entry-actions-group" role="group">
								<button type="button" class="o-btn sm:t-flex-grow--1 t-bg--warning t-text--100 t-text--nowrap">
									<?php echo JText::_('COM_EASYDISCUSS_PENDING_MODERATION'); ?>
								</button>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>

				<div class="o-body is-editor-markup" data-ed-post-content>
					<?php echo $post->getContent(true); ?>
				</div>
			</div>
		</div>
	</div>
</div>