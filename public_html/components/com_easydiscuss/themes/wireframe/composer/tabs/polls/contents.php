<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$poll = $post->getPoll();
$choices = array();

if ($poll && $poll->id) {
	$choices = $poll->getChoices();
}

if (!$poll && isset($post->sessiondata) && $post->sessiondata) {
	$dataPoll = $post->getInternalData('pollquestion');
	$poll = ED::poll($dataPoll);
	$choices = $post->getInternalData('polls');
}

?>
<div id="polls-<?php echo $editorId;?>" class="ed-editor-tab__content tab-pane">

	<div class="ed-editor-tab__content-note">
		<?php echo JText::_('COM_EASYDISCUSS_POLLS_DESC'); ?>
	</div>
		
	<?php if ($this->config->get('main_polls_multiple')) { ?>
	<div class="o-form-check t-mb--md ed-editor-tab__content-note">
		<input type="checkbox" id="multiple-polls" 
			value="1" 
			name="multiplePolls" 
			class="o-form-check-input"
			<?php echo $poll && $poll->isMultiple() ? ' checked="checked"' : '';?>
		/>
		<label for="multiple-polls" class="o-form-check-label">
			 <?php echo JText::_('COM_EASYDISCUSS_ALLOW_MULTIPLE_POLL_VOTES'); ?>
		</label>
	</div>
	<?php } ?>

	<input type="text" class="o-form-control" name="poll_question" 
		placeholder="<?php echo $this->html('string.escape', JText::_('COM_EASYDISCUSS_POLL_QUESTION_PLACEHOLDER'));?>" 
		<?php if ($poll) { ?>
		value="<?php echo $this->html('string.escape', $poll->title);?>" 
		<?php } ?>
	/>

	<div class="ed-editor-tab__content-note t-lg-mt--xl">
		<b><?php echo JText::_('COM_EASYDISCUSS_POLLS_VOTE_OPTIONS'); ?></b>
	</div>

	<div class="ed-editor-input-list l-stack l-spaces--xs" data-ed-polls-list>
		<?php if ($choices) { ?>
			<?php foreach ($choices as $choice) { ?>
				<div class="o-input-group">
					<input type="text" name="pollitems[]" class="o-form-control" 
						placeholder="<?php echo JText::_('COM_EASYDISCUSS_POLLS_ANSWER_PLACEHOLDER', true);?>" 
						data-ed-polls-input
						value="<?php echo $this->html('string.escape', $choice->value);?>"
						data-id="<?php echo $choice->id;?>"
					/>
					<input type="hidden" name="pollitemsOri[]" value="<?php echo $this->escape($choice->value); ?>" />
					<button class="o-btn o-btn--danger o-btn--ed-input-del" type="button" data-ed-polls-remove>×</button>
				</div>
			<?php } ?>
		<?php } else { ?>
			<div class="o-input-group">
				<input type="text" name="pollitems[]" class="o-form-control" 
					placeholder="<?php echo JText::_('COM_EASYDISCUSS_POLLS_ANSWER_PLACEHOLDER', true);?>" 
					data-ed-polls-input
				/>
				<button class="o-btn o-btn--danger o-btn--ed-input-del" type="button" data-ed-polls-remove>×</button>
			</div>
		<?php } ?>
	</div>

	<div class="ed-editor-tab__content-note">
		<a href="javascript:void(0);" class="si-link" data-ed-polls-insert><?php echo JText::_('COM_EASYDISCUSS_ADD_POLL_ITEM');?></a>
	</div>

	<input type="hidden" name="pollsremove" data-ed-polls-removed-items />
</div>