<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$poll = $post->getPoll(false);

if (!$poll) {
	return;
}

$choices = $poll->getChoices(true);

if (!$choices) {
	return;
}
?>
<div class="o-card o-card--ed-post-widget">
	<div class="o-card__body l-stack">
		<div class="ed-polls  <?php echo $poll && $poll->isLocked() ? ' is-lockpoll' : '';?>" data-ed-polls data-post-id="<?php echo $post->id;?>">
			<div class="ed-polls__hd">
				<div class="t-d--flex">
					<div class="t-flex-grow--1 t-d--flex t-align-items--cx t-min-width--0">

						<span class="ed-poll-locked">
							<i class="fa fa-lock t-mr--sm" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_POLL_IS_LOCKED', true);?>"></i>&nbsp;&nbsp;
						</span>
						<span class="o-title-01 t-text--truncate t-pr--md">
							<div class="t-text--wrap">
								<?php echo $poll->getTitle();?>
							</div>
						</span>
					</div>

					<div class="t-flex-shrink--0">
						<?php if ($post->canLockPolls()) { ?>
							<a href="javascript:void(0);" class="o-btn o-btn--default-o o-btn--sm ed-btn-lockpoll" data-ed-polls-lock data-task="lock">
								<?php echo JText::_('COM_EASYDISCUSS_ENTRY_LOCK_POLL'); ?>
							</a>
							<a href="javascript:void(0);" class="o-btn o-btn--default-o o-btn--sm ed-btn-unlockpoll" data-ed-polls-lock data-task="unlock">
								<?php echo JText::_('COM_EASYDISCUSS_ENTRY_UNLOCK_POLL'); ?>
							</a>
						<?php } ?>
						<span class="o-title-01" data-ed-post-poll-total-votes>
							<?php echo JText::sprintf('COM_EASYDISCUSS_POLLS_TOTAL_VOTES', $poll->getTotalVotes(false)); ?>
						</span>
					</div>
				</div>
			</div>
			
			<div class="ed-polls__bd">
				<div class="ed-polls__ques-list" data-ed-polls-choices>
					<?php foreach ($choices as $choice) { ?>
					<div class="ed-polls__item">
						<div class="o-form-check <?php echo $poll->isMultiple() ? 'checkbox' : 'radio';?>" data-ed-poll-choice-item data-id="<?php echo $choice->id;?>">
							<?php if ($this->config->get('main_polls_guests') || $this->my->id) { ?>
							<input
								class="o-form-check-input"
								name="poll"
								type="<?php echo $poll->isMultiple() ? 'checkbox' : 'radio';?>"
								id="poll-choice-<?php echo $choice->id;?>"
								data-id="<?php echo $choice->id;?>"
								data-ed-poll-choice-checkbox
								<?php if ($poll->isLocked()) { ?>
								disabled="disabled"
								<?php } ?>
								<?php if ($choice->hasVoted()) { ?>
								checked="true"
								<?php } ?>
							/>
							<?php } ?>

							<label for="poll-choice-<?php echo $choice->id;?>" class="t-d--block">
								<div class="ed-polls-choice__title">
									<?php echo $choice->getTitle();?>
								</div>

								<div class="ed-polls__progress o-progress">
									<div class="o-progress-bar o-progress-bar--primary" style="width: <?php echo $choice->getPercentage();?>%;" data-ed-poll-choice-percentage></div>
								</div>

								<div class="ed-polls__voters t-hidden" data-ed-poll-choice-voters>
								</div>

								<a href="javascript:void(0);" class="ed-polls__count" data-ed-poll-choice-show-voters data-count="<?php echo $choice->getVoteCount();?>">
									<?php echo JText::sprintf('COM_EASYDISCUSS_POLLS_VOTE_COUNT', '<span data-ed-poll-choice-counter>' . $choice->getVoteCount() . '</span>'); ?>
								</a>
							</label>

							<div class="o-loader o-loader--sm o-loader--inline"></div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
			
		</div>
	</div>
	
</div>

