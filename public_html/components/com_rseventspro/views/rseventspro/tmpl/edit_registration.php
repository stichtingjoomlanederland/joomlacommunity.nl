<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_REGISTRATION'); ?></legend>

	<?php echo $this->form->renderField('start_registration'); ?>
	<?php echo $this->form->renderField('end_registration'); ?>
	<?php echo $this->form->renderField('unsubscribe_date'); ?>
	<?php echo $this->form->renderField('payments'); ?>
	<?php echo $this->form->renderField('tickets_amount'); ?>
	<?php echo $this->form->renderField('overbooking'); ?>
	<div id="rsepro-overbooking-amount" style="display: none;"><?php echo $this->form->renderField('overbooking_amount'); ?></div>
	<?php echo $this->form->renderField('max_tickets'); ?>
	<div id="rsepro-max-tickets-amount" style="display: none;"><?php echo $this->form->renderField('max_tickets_amount'); ?></div>
	<?php echo $this->form->renderField('waitinglist'); ?>
	<div id="rsepro-waitinglist-limit" style="display: none;"><?php echo $this->form->renderField('waitinglist_limit'); ?></div>
	<div id="rsepro-waitinglist-time" style="display: none;"><?php echo $this->form->renderField('waitinglist_time'); ?></div>
	<div id="rsepro-waitinglist-emails" style="display: none;"><?php echo $this->form->renderField('waitinglist_user'); ?><?php echo $this->form->renderField('waitinglist_admin'); ?></div>
	<?php echo $this->form->renderField('notify_me'); ?>
	<?php echo $this->form->renderField('notify_me_paid'); ?>
	<?php echo $this->form->renderField('notify_me_unsubscribe'); ?>
	<?php echo $this->form->renderField('show_registered'); ?>
	<?php echo $this->form->renderField('automatically_approve'); ?>
	<?php echo $this->form->renderField('ticketsconfig'); ?>
	<?php echo $this->form->renderField('discounts'); ?>
	<div id="rsepro-tickets-configuration" style="display: none;"><?php echo RSEventsproAdapterGrid::renderField('','<a class="btn btn-primary" href="javascript:void(0)" onclick="jQuery(\'#rseTicketsModal\').modal(\'show\');">'.JText::_('COM_RSEVENTSPRO_TICKETS_CONFIGURATION').'</a>'); ?></div>
	<?php if (!rseventsproHelper::isCart()) { ?>
	<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_EVENT_REGISTRATION_FORM'), '<a class="btn btn-primary rsepro-event-form" onclick="jQuery(\'#rseFromModal\').modal(\'show\');" href="javascript:void(0)">'.$this->eventClass->getForm().'</a> &mdash; <a href="http://www.rsjoomla.com/joomla-extensions/joomla-form.html" target="_blank">'.JText::_('COM_RSEVENTSPRO_RSFORMPRO').'</a>'); ?>
	<?php if (file_exists(JPATH_SITE.'/components/com_rsform/rsform.php') && rseventsproHelper::rsform()) echo $this->form->renderField('sync'); ?>
	<?php if (rseventsproHelper::paypal() && $this->config->payment_paypal) echo $this->form->renderField('paypal_email'); ?>
	<?php } ?>
	
	<div class="form-actions">
		<button class="btn btn-success rsepro-event-save" type="button"><?php echo JText::_('COM_RSEVENTSPRO_SAVE_EVENT'); ?></button>
		<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
	</div>
</fieldset>

<?php if ($this->config->modaltype == 1) echo JHtml::_('bootstrap.renderModal', 'rseFromModal', array('title' => JText::_('COM_RSEVENTSPRO_SELECT_FORM'), 'url' => JRoute::_('index.php?option=com_rseventspro&layout=forms&tmpl=component&id='.rseventsproHelper::sef($this->item->id,$this->item->name), false), 'bodyHeight' => 70, 'modalWidth' => 70)); ?>
<?php if ($this->config->modaltype == 1) echo JHtml::_('bootstrap.renderModal', 'rseTicketsModal', array('title' => '&nbsp;', 'url' => JRoute::_('index.php?option=com_rseventspro&layout=seats&tmpl=component&id='.rseventsproHelper::sef($this->item->id,$this->item->name),false), 'bodyHeight' => 70, 'modalWidth' => 70, 'width' => rseventsproHelper::getConfig('seats_width','int','1280'), 'height' => rseventsproHelper::getConfig('seats_height','int','800'))); ?>