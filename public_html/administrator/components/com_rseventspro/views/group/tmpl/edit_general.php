<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
	<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
		<fieldset class="options-form">
			<legend><?php echo JText::_('COM_RSEVENTSPRO_GROUP_EVENT_PERMISSIONS'); ?></legend>
			<?php echo $this->form->renderField('can_edit_events'); ?>
			<?php echo $this->form->renderField('can_post_events'); ?>
			<?php echo $this->form->renderField('can_repeat_events'); ?>
			<?php echo $this->form->renderField('limit_events'); ?>
			<?php echo $this->form->renderField('event_moderation'); ?>
			<?php echo $this->form->renderField('can_delete_events'); ?>
			<?php echo $this->form->renderField('can_register'); ?>
			<?php echo $this->form->renderField('can_unsubscribe'); ?>
			<?php echo $this->form->renderField('can_download'); ?>
			<?php echo $this->form->renderField('can_upload'); ?>
			<?php echo $this->form->renderField('can_change_options'); ?>
			<?php echo $this->form->renderField('can_select_speakers'); ?>
			<?php echo $this->form->renderField('can_add_speaker'); ?>
			<?php echo $this->form->renderField('can_select_sponsors'); ?>
			<?php echo $this->form->renderField('can_add_sponsor'); ?>
		</fieldset>
	</div>
	<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
		<fieldset class="options-form">
			<legend><?php echo JText::_('COM_RSEVENTSPRO_GROUP_CATEGORY_PERMISSIONS'); ?></legend>
			<?php echo $this->form->renderField('can_create_categories'); ?>
			<?php echo $this->form->renderField('restricted_categories'); ?>
		</fieldset>
		
		<fieldset class="options-form">
			<legend><?php echo JText::_('COM_RSEVENTSPRO_GROUP_TAG_PERMISSIONS'); ?></legend>
			<?php echo $this->form->renderField('tag_moderation'); ?>
		</fieldset>
		
		<fieldset class="options-form">
			<legend><?php echo JText::_('COM_RSEVENTSPRO_GROUP_LOCATION_PERMISSIONS'); ?></legend>
			<?php echo $this->form->renderField('can_add_locations'); ?>
			<?php echo $this->form->renderField('can_edit_locations'); ?>
		</fieldset>
		
		<fieldset class="options-form">
			<legend><?php echo JText::_('COM_RSEVENTSPRO_GROUP_APPROVAL_PERMISSIONS'); ?></legend>
			<?php echo $this->form->renderField('can_approve_events'); ?>
			<?php echo $this->form->renderField('can_approve_tags'); ?>
			<?php echo $this->form->renderField('can_confirm_tickets'); ?>
		</fieldset>
	</div>
</div>