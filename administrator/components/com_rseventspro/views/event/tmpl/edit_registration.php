<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.modal', 'a.rsepro-event-form');
JHtml::_('behavior.modal', 'a.rsepro-tickets-config'); ?>
<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_REGISTRATION'); ?></legend>

<div class="control-group">
	<div class="control-label">
		<label for="jform_start_registration"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_FROM'); ?></label>
	</div>
	<div class="controls">
		<?php echo JHTML::_('rseventspro.rscalendar', 'jform[start_registration]', $this->item->start_registration); ?>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_end_registration"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TO'); ?></label>
	</div>
	<div class="controls">
		<?php echo JHTML::_('rseventspro.rscalendar', 'jform[end_registration]', $this->item->end_registration); ?>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_unsubscribe_date" class="<?php echo rseventsproHelper::tooltipClass(); ?>" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_UNSUBSCRIPTION_DATE_DESC')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIPTION_DATE'); ?></label>
	</div>
	<div class="controls">
		<?php echo JHTML::_('rseventspro.rscalendar', 'jform[unsubscribe_date]', $this->item->unsubscribe_date); ?>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_payments"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_PAYMENTS'); ?></label>
	</div>
	<div class="controls">
		<select class="rsepro-chosen" name="jform[payments][]" id="jform_payments" multiple="multiple">
			<?php echo JHtml::_('select.options', rseventsproHelper::getPayments(),'value','text',$this->eventClass->getPayments()); ?>
		</select>
	</div>
</div>

<div class="control-group">
	<label class="checkbox">
		<input id="jform_overbooking" name="jform[overbooking]" type="checkbox" value="1" <?php echo $this->item->overbooking ? 'checked="checked"' : ''; ?> />
		<?php echo JText::_('COM_RSEVENTSPRO_EVENT_OVERBOOKING'); ?>
	</label>
</div>

<div class="control-group" id="rsepro-overbooking-amount" style="display: none;">
	<div class="control-label">
		<label for="jform_overbooking_amount" class="<?php echo rseventsproHelper::tooltipClass(); ?>" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_EVENT_OVERBOOKING_AMOUNT_DESC')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_OVERBOOKING_AMOUNT'); ?></label>
	</div>
	<div class="controls">
		<input type="text" name="jform[overbooking_amount]" id="jform_overbooking_amount" class="span1" value="<?php echo $this->escape($this->item->overbooking_amount); ?>" />
	</div>
</div>

<div class="control-group">
	<label class="checkbox">
		<input id="jform_max_tickets" name="jform[max_tickets]" type="checkbox" value="1" <?php echo $this->item->max_tickets ? 'checked="checked"' : ''; ?> />
		<?php echo JText::_('COM_RSEVENTSPRO_EVENT_MAX_TICKETS'); ?>
	</label>
</div>

<div class="control-group" id="rsepro-max-tickets-amount" style="display: none;">
	<div class="control-label">
		<label for="jform_max_tickets_amount" class="<?php echo rseventsproHelper::tooltipClass(); ?>" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_MAX_TICKETS_AMOUNT_DESC')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_MAX_TICKETS_AMOUNT'); ?></label>
	</div>
	<div class="controls">
		<input type="text" name="jform[max_tickets_amount]" id="jform_max_tickets_amount" class="span1" value="<?php echo $this->escape($this->item->max_tickets_amount); ?>" />
	</div>
</div>

<div class="control-group">
	
	<label class="checkbox">
		<input id="jform_notify_me" name="jform[notify_me]" type="checkbox" value="1" <?php echo $this->item->notify_me ? 'checked="checked"' : ''; ?> />
		<?php echo JText::_('COM_RSEVENTSPRO_EVENT_SUBSCRIPTION_NOTIFICATION'); ?>
	</label>
	
	<label class="checkbox">
		<input id="jform_notify_me_unsubscribe" name="jform[notify_me_unsubscribe]" type="checkbox" value="1" <?php echo $this->item->notify_me_unsubscribe ? 'checked="checked"' : ''; ?> />
		<?php echo JText::_('COM_RSEVENTSPRO_EVENT_UNSUBSCRIBE_NOTIFICATION'); ?>
	</label>
	
	<label class="checkbox">
		<input id="jform_show_registered" name="jform[show_registered]" type="checkbox" value="1" <?php echo $this->item->show_registered ? 'checked="checked"' : ''; ?> />
		<?php echo JText::_('COM_RSEVENTSPRO_EVENT_SHOW_GUESTS'); ?></label>
	</label>
	
	<label class="checkbox">
		<input id="jform_automatically_approve" name="jform[automatically_approve]" type="checkbox" value="1" <?php echo $this->item->automatically_approve ? 'checked="checked"' : ''; ?> />
		<?php echo JText::_('COM_RSEVENTSPRO_EVENT_APPROVE'); ?>
	</label>
	
	<label class="checkbox">
		<input name="jform[ticketsconfig]" type="checkbox" value="1" id="jform_ticketsconfig" <?php echo $this->item->ticketsconfig ? 'checked="checked"' : ''; ?> />
		<?php echo JText::_('COM_RSEVENTSPRO_ENABLE_TICKETS_CONFIGURATION'); ?>
	</label>
	
	<label class="checkbox">
		<input name="jform[discounts]" type="checkbox" value="1" id="jform_discounts" <?php echo $this->item->discounts ? 'checked="checked"' : ''; ?> />
		<?php echo JText::_('COM_RSEVENTSPRO_ENABLE_DISCOUNTS'); ?>
	</label>
	
</div>

<div class="control-group" id="rsepro-tickets-configuration" style="display: none;">
	<div class="controls">
		<a class="btn rsepro-tickets-config" rel="{handler: 'iframe', size: {x: <?php echo rseventsproHelper::getConfig('seats_width','int','1280'); ?>, y:<?php echo rseventsproHelper::getConfig('seats_height','int','800'); ?>}}" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=event&layout=tickets&tmpl=component&id='.$this->item->id,false); ?>">
			<?php echo JText::_('COM_RSEVENTSPRO_TICKETS_CONFIGURATION'); ?>
		</a>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label><?php echo JText::_('COM_RSEVENTSPRO_EVENT_REGISTRATION_FORM'); ?></label>
	</div>
	<div class="controls">
		<a class="btn rsepro-event-form" rel="{handler: 'iframe', size: {x: 800, y:600}}" href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=events&layout=forms&tmpl=component&id='.$this->item->id); ?>">
			<?php echo $this->eventClass->getForm(); ?>
		</a>
		&mdash; <a href="http://www.rsjoomla.com/joomla-extensions/joomla-form.html" target="_blank"><?php echo JText::_('COM_RSEVENTSPRO_RSFORMPRO'); ?></a>
	</div>
</div>

<?php if (file_exists(JPATH_SITE.'/components/com_rsform/rsform.php') && rseventsproHelper::rsform()) { ?>
<div class="control-group">
	<label class="checkbox">
		<input name="jform[sync]" type="checkbox" value="1" id="jform_sync" <?php echo $this->item->sync ? 'checked="checked"' : ''; ?> />
		<?php echo JText::_('COM_RSEVENTSPRO_ENABLE_SYNC'); ?>
	</label>
</div>
<?php } ?>

<?php if (rseventsproHelper::paypal() && $this->config->payment_paypal) { ?>
<div class="control-group">
	<div class="control-label">
		<label for="jform_paypal_email"><?php echo JText::_('COM_RSEVENTSPRO_PAYPAL_EMAIL'); ?></label>
	</div>
	<div class="controls">
		<input type="text" id="jform_paypal_email" name="jform[paypal_email]" class="span3" value="<?php echo $this->escape($this->item->paypal_email); ?>" />
	</div>
</div>
<?php } ?>

<div class="form-actions">
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
</div>