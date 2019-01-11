<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_RSVP_OPTIONS'); ?></legend>

<div class="control-group">
	<div class="control-label">
		<label for="jform_start_registration"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_FROM'); ?></label>
	</div>
	<div class="controls">
		<?php echo JHTML::_('rseventspro.rscalendar', 'jform[rsvp_start]', $this->item->rsvp_start); ?>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_end_registration"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TO'); ?></label>
	</div>
	<div class="controls">
		<?php echo JHTML::_('rseventspro.rscalendar', 'jform[rsvp_end]', $this->item->rsvp_end); ?>
	</div>
</div>

<div class="control-group" id="rsepro-rsvp-going">
	<label class="checkbox">
		<?php $rsvpgoingchecked = $this->item->rsvp_going ? 'checked="checked"' : ''; ?>
		<input type="checkbox" id="jform_rsvp_going" name="jform[rsvp_going]" value="1" <?php echo $rsvpgoingchecked; ?> /> <?php echo JText::_('COM_RSEVENTSPRO_RSVP_EVENT_RSVP_GOING_EMAIL'); ?>
	</label>
</div>

<div class="control-group" id="rsepro-rsvp-interested">
	<label class="checkbox">
		<?php $rsvpinterestedchecked = $this->item->rsvp_interested ? 'checked="checked"' : ''; ?>
		<input type="checkbox" id="jform_rsvp_interested" name="jform[rsvp_interested]" value="1" <?php echo $rsvpinterestedchecked; ?> /> <?php echo JText::_('COM_RSEVENTSPRO_RSVP_EVENT_RSVP_INTERESTED_EMAIL'); ?>
	</label>
</div>

<div class="control-group" id="rsepro-rsvp-notgoing">
	<label class="checkbox">
		<?php $rsvpnotgoingchecked = $this->item->rsvp_notgoing ? 'checked="checked"' : ''; ?>
		<input type="checkbox" id="jform_rsvp_notgoing" name="jform[rsvp_notgoing]" value="1" <?php echo $rsvpnotgoingchecked; ?> /> <?php echo JText::_('COM_RSEVENTSPRO_RSVP_EVENT_RSVP_NOTGOING_EMAIL'); ?>
	</label>
</div>

<div class="control-group" id="rsepro-rsvp-guests">
	<label class="checkbox" id="jform_rsvp_guests_label">
		<?php $rsvpguestschecked = $this->item->rsvp_guests ? 'checked="checked"' : ''; ?>
		<input type="checkbox" id="jform_rsvp_guests" name="jform[rsvp_guests]" value="1" <?php echo $rsvpguestschecked; ?> /> <?php echo JText::_('COM_RSEVENTSPRO_RSVP_EVENT_GUESTS'); ?>
	</label>
</div>

<div class="control-group" id="rsepro-rsvp-quota">
	<div class="control-label">
		<label for="jform_rsvp_quota" class="<?php echo rseventsproHelper::tooltipClass(); ?>" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_RSVP_QUOTA_DESC')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_RSVP_QUOTA'); ?></label>
	</div>
	<div class="controls">
		<input type="text" name="jform[rsvp_quota]" id="jform_rsvp_quota" class="span1" value="<?php echo $this->escape($this->item->rsvp_quota); ?>" />
	</div>
</div>


<div class="form-actions">
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
</div>