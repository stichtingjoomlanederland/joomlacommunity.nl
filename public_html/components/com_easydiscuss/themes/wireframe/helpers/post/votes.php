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
<div class="ed-entry-actions-group sm:t-justify-content--c" role="group" data-id="<?php echo $post->id;?>" data-ed-votes>
	<div class="o-btn o-btn--sm ed-vote">

		<div 
			class="ed-vote__points" data-vote-counter
			data-ed-popbox="ajax://site/views/popbox/voters"
			data-ed-popbox-position="bottom-center"
			data-ed-popbox-toggle="click"
			data-ed-popbox-offset="6"
			data-ed-popbox-type="ed-voters"
			data-ed-popbox-component="o-popbox--avatar-list"
			data-ed-popbox-cache="1"
			data-args-id="<?php echo $post->id; ?>"
		>
			<?php echo $post->getTotalVotes();?>
			<div class="ed-vote__label"><?php echo JText::_('COM_ED_VOTES');?></div>
		</div>
		

		<a href="javascript:void(0)" class="ed-vote__undo t-d--none" data-vote-undo>
			<?php echo JText::_('COM_EASYDISCUSS_ENTRY_VOTES_UNDO_BUTTON'); ?>
		</a>

		<?php if ($post->canVote()) { ?>
		<a href="javascript:void(0);" class="ed-vote__up" data-vote-button data-direction="up">
			<i class="fa fa-chevron-up"></i>
		</a>
	
		<a href="javascript:void(0);" class="ed-vote__down" data-vote-button data-direction="down">
			<i class="fa fa-chevron-down"></i>
		</a>
		<?php } ?>
	</div>
</div>