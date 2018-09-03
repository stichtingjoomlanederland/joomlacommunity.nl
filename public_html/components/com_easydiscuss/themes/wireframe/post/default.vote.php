<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php if ($post->isVoteEnabled()) { ?>
<div class="ed-vote pull-right t-lg-mt--md t-lg-mb--md" data-id="<?php echo $post->id;?>" data-ed-post-vote>
	<div class="ed-vote__points" data-ed-vote-counter>
		<?php echo $post->getTotalVotes(); ?>
	</div>
	<div class="ed-vote__text">
		<?php echo JText::_("COM_EASYDISCUSS_ENTRY_VOTES");?>
	</div>

	<a href="javascript:void(0)" class="ed-vote__undo t-hidden" data-ed-vote-undo><?php echo JText::_('COM_EASYDISCUSS_ENTRY_VOTES_UNDO_BUTTON'); ?></a>

	<?php if ($post->canVote()){ ?>
		<a href="javascript:void(0);" class="ed-vote__up" data-ed-vote-button data-direction="up">
			<i class="fa fa-chevron-up"></i>
		</a>
	<?php } ?>

	<?php if ($post->canVote()) { ?>
		<a href="javascript:void(0);" class="ed-vote__down" data-ed-vote-button data-direction="down">
			<i class="fa fa-chevron-down"></i>
		</a>
	<?php } ?>
</div>
<?php } ?>