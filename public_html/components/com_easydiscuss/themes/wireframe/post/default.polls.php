<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$poll = $post->getPoll();

if (!$poll) {
    return;
}

$choices = $poll->getChoices();

if (!$choices) {
    return;
}
?>
<div class="ed-polls t-lg-mt--lg t-lg-mb--lg" data-ed-polls data-post-id="<?php echo $post->id;?>">

    <div class="ed-polls__hd">
        <div class="clearfix">
            <div class="pull-left">

                <span class="poll-locked">
                    <i class="fa fa-lock" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_POLL_IS_LOCKED', true);?>"></i>&nbsp;
                </span>

                <?php echo $poll->getTitle();?>
            </div>

            <div class="pull-right">
                <?php if ($post->canLockPolls()) { ?>
                    <a href="javascript:void(0);" class="btn btn-default btn-sm ed-btn-lockpoll" data-ed-post-poll-lock>
                        <?php echo JText::_('COM_EASYDISCUSS_ENTRY_LOCK_POLL'); ?>
                    </a>
                    <a href="javascript:void(0);" class="btn btn-default btn-sm ed-btn-unlockpoll" data-ed-post-poll-unlock>
                        <?php echo JText::_('COM_EASYDISCUSS_ENTRY_UNLOCK_POLL'); ?>
                    </a>
                <?php } ?>
                <span data-ed-post-poll-total-votes>
                    <?php echo JText::sprintf('COM_EASYDISCUSS_POLLS_TOTAL_VOTES', $poll->getTotalVotes()); ?>
                </span>
            </div>
        </div>
    </div>

    <div class="ed-polls__bd">
        <div class="ed-polls__ques-list" data-ed-polls-choices>
            <?php foreach ($choices as $choice) { ?>
            <div class="ed-polls__item">
                <div class="o-<?php echo $poll->isMultiple() ? 'checkbox' : 'radio';?>" data-ed-poll-choice-item data-id="<?php echo $choice->id;?>">
                    <?php if ($this->config->get('main_polls_guests') || $this->my->id) { ?>
                    <input
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

                    <label for="poll-choice-<?php echo $choice->id;?>">
                        <div>
                            <?php echo $choice->getTitle();?>
                        </div>

                        <div class="ed-polls__progress progress">
                            <div class="progress-bar progress-bar-primary" style="width: <?php echo $choice->getPercentage();?>%;" data-ed-poll-choice-percentage></div>
                        </div>

                        <div class="ed-polls__voters t-hidden" data-ed-poll-choice-voters>
                        </div>

                        <a href="javascript:void(0);" class="ed-polls__count" data-ed-poll-choice-show-voters data-count="<?php echo $choice->getVoteCount();?>">
                            <?php echo JText::sprintf('COM_EASYDISCUSS_POLLS_VOTE_COUNT', '<span data-ed-poll-choice-counter>' . $choice->getVoteCount() . '</span>'); ?>
                        </a>
                    </label>

                    <div class="o-loading">
                        <div class="o-loading__content">
                            <i class="fa fa-spinner fa-pulse"></i>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

        </div>
    </div>
</div>
