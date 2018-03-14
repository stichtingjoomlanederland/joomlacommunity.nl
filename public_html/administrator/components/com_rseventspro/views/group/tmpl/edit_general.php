<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<div class="span6 rswidth-50 rsfltlft">
	<?php echo JHtml::_('rsfieldset.start', 'adminform', JText::_('COM_RSEVENTSPRO_GROUP_EVENT_PERMISSIONS')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('can_edit_events'), $this->form->getInput('can_edit_events')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('can_post_events'), $this->form->getInput('can_post_events')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('can_repeat_events'), $this->form->getInput('can_repeat_events')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('event_moderation'), $this->form->getInput('event_moderation')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('can_delete_events'), $this->form->getInput('can_delete_events')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('can_register'), $this->form->getInput('can_register')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('can_unsubscribe'), $this->form->getInput('can_unsubscribe')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('can_download'), $this->form->getInput('can_download')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('can_upload'), $this->form->getInput('can_upload')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('can_change_options'), $this->form->getInput('can_change_options')); ?>
	<?php echo JHtml::_('rsfieldset.end'); ?>
</div>
<div class="span6 rswidth-50 rsfltlft">
	<?php echo JHtml::_('rsfieldset.start', 'adminform', JText::_('COM_RSEVENTSPRO_GROUP_CATEGORY_PERMISSIONS')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('can_create_categories'), $this->form->getInput('can_create_categories')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('restricted_categories'), $this->form->getInput('restricted_categories')); ?>
	<?php echo JHtml::_('rsfieldset.end'); ?>
	
	<?php echo JHtml::_('rsfieldset.start', 'adminform', JText::_('COM_RSEVENTSPRO_GROUP_TAG_PERMISSIONS')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('tag_moderation'), $this->form->getInput('tag_moderation')); ?>
	<?php echo JHtml::_('rsfieldset.end'); ?>
	
	<?php echo JHtml::_('rsfieldset.start', 'adminform', JText::_('COM_RSEVENTSPRO_GROUP_LOCATION_PERMISSIONS')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('can_add_locations'), $this->form->getInput('can_add_locations')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('can_edit_locations'), $this->form->getInput('can_edit_locations')); ?>
	<?php echo JHtml::_('rsfieldset.end'); ?>
	
	<?php echo JHtml::_('rsfieldset.start', 'adminform', JText::_('COM_RSEVENTSPRO_GROUP_APPROVAL_PERMISSIONS')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('can_approve_events'), $this->form->getInput('can_approve_events')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('can_approve_tags'), $this->form->getInput('can_approve_tags')); ?>
	<?php echo JHtml::_('rsfieldset.end'); ?>
</div>
<div class="clr"></div>