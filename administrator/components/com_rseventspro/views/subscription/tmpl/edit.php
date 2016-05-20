<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.modal'); ?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'subscription.cancel') {
			Joomla.submitform(task, document.getElementById('adminForm'));
			return;
		}
		
		if (document.formvalidator.isValid(document.id('adminForm'))) {
			<?php if ($this->item->id) { ?>
			Joomla.submitform(task, document.getElementById('adminForm'));
			<?php } else { ?>
			if ((document.getElementById('rsepro_selected_tickets').innerHTML != '' || document.getElementById('rsepro_simple_tickets').innerHTML != '') && task != 'subscription.cancel') {
				Joomla.submitform(task, document.getElementById('adminForm'));
			} else {
				alert('<?php echo $this->escape(JText::_('COM_RSEVENTSPRO_PLEASE_SELECT_TICKET',true));?>');
			}
			<?php } ?>
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED',true));?>');
		}
	}
	
	function jSelectUser_jform_idu(id, title) {
		if (jQuery('#jform_idu_id').val() != id) {
			rsepro_get_user_details(id);
		}
		
		SqueezeBox.close();
	}
	
	function rsepro_get_user_details(id) {
		jQuery.ajax({
			url: 'index.php?option=com_rseventspro',
			type: 'post',
			dataType : 'json',
			data: 'task=subscription.email&id=' + id
		}).done(function(response) {
			jQuery('#jform_idu_id').val(id);
			jQuery('#jform_idu_name').val(response.name);
			jQuery('#jform_idu').val(response.name);
			jQuery('#jform_name').val(response.name);
			jQuery('#jform_email').val(response.email);
		});
	}
	
	function rsepro_select_event(id) {
		if (id == 0) {
			jQuery('#eventtickets').css('display','none');
			jQuery('#eventtickets')
		} else {
			jQuery('#eventtickets').css('display','');
			jQuery('#eventtickets').prop('href','index.php?option=com_rseventspro&view=subscription&layout=tickets&tmpl=component&id='+id);
		}
	}
	
	jQuery(document).ready(function(){
		jQuery('#jform_idu_id').on('change', function() {
			rsepro_get_user_details(jQuery(this).val());
		});
	});
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscription&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span6 rswidth-50 rsfltlft">
			<?php echo JHtml::_('rsfieldset.start', 'adminform', JText::_('COM_RSEVENTSPRO_SUBSCRIBER_INFO')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('idu'), $this->form->getInput('idu')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('name'), $this->form->getInput('name')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('email'), $this->form->getInput('email')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('state'), $this->form->getInput('state')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('confirmed'), $this->form->getInput('confirmed')); ?>
			<?php if ($this->item->state == 1) { ?>
			<?php $extra = '<a href="'.JRoute::_('index.php?option=com_rseventspro&task=subscription.activation&id='.$this->item->id).'" class="rsextra">'.JText::_('COM_RSEVENTSPRO_SEND_ACTIVATION_EMAIL').'</a>'; ?>
			<?php echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', $extra); ?>
			<?php } ?>
			<?php if (empty($this->item->id)) { ?>
			<?php echo JHtml::_('rsfieldset.element', '<label for="registration">'.JText::_('COM_RSEVENTSPRO_SEND_REGISTRATION_EMAIL').'</label>', '<input type="checkbox" name="registration" value="1" id="registration" />'); ?>
			<?php } ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
			
			
			<?php echo JHtml::_('rsfieldset.start', 'adminform', JText::_('COM_RSEVENTSPRO_SUBSCRIBER_DETAILS')); ?>
			<?php if ($this->item->id) { ?>
			
			<?php echo JHtml::_('rsfieldset.element', '<label title="'.rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_DATE_DESC')).'" class="'.rseventsproHelper::tooltipClass().'">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_DATE').'</label>', '<span class="rsextra">'.rseventsproHelper::showdate($this->item->date,null,true).'</span>'); ?>
			
			<?php echo JHtml::_('rsfieldset.element', '<label>'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_IP').'</label>', '<span class="rsextra">'.$this->item->ip.'</span>'); ?>
			
			<?php $event = $this->getEvent($this->item->ide); ?>
			<?php $date = $event->allday ? rseventsproHelper::showdate($event->start, rseventsproHelper::getConfig('global_date')) : rseventsproHelper::showdate($event->start).' - '.rseventsproHelper::showdate($event->end); ?>
			<?php echo JHtml::_('rsfieldset.element', '<label title="'.rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_EVENT_DESC')).'" class="'.rseventsproHelper::tooltipClass().'">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_EVENT').'</label>', '<span class="rsextra"><a href="'.JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$event->id).'">'.$event->name.'</a> ('.$date.')</span>'); ?>
			
			<?php echo JHtml::_('rsfieldset.element', '<label title="'.rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_PAYMENT_DESC')).'" class="'.rseventsproHelper::tooltipClass().'">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_PAYMENT').'</label>', '<span class="rsextra">'.rseventsproHelper::getPayment($this->item->gateway).'</span>'); ?>
			
			<?php $tickets = rseventsproHelper::getUserTickets($this->item->id); ?>
			<?php $purchasedtickets = ''; ?>
			<?php if ($tickets) {
					foreach ($tickets as $ticket) {
						if ($ticket->price > 0) {
							$purchasedtickets .= $ticket->quantity.' x '.$ticket->name.' ('.rseventsproHelper::currency($ticket->price).')<br />';
						} else {
							$purchasedtickets .= $ticket->quantity.' x '.$ticket->name.' ('.JText::_('COM_RSEVENTSPRO_GLOBAL_FREE').')<br />';
						}
					}
				}
			?>
			<?php echo JHtml::_('rsfieldset.element', '<label title="'.rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKETS_DESC')).'" class="'.rseventsproHelper::tooltipClass().'">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKETS').'</label>', '<span class="rsextra">'.$purchasedtickets.'</span>'); ?>
			
			<?php if ($this->item->discount) { ?>
			<?php echo JHtml::_('rsfieldset.element', '<label>'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_DISCOUNT').'</label>', '<span class="rsextra">'.rseventsproHelper::currency($this->item->discount).'</span>'); ?>
			<?php echo JHtml::_('rsfieldset.element', '<label>'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_DISCOUNT_CODE').'</label>', '<span class="rsextra">'.$this->item->coupon.'</span>'); ?>
			<?php } ?>
			
			<?php if ($this->item->early_fee) { ?>
			<?php echo JHtml::_('rsfieldset.element', '<label>'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_EARLY_FEE').'</label>', '<span class="rsextra">'.rseventsproHelper::currency($this->item->early_fee).'</span>'); ?>
			<?php } ?>
			
			<?php if ($this->item->late_fee) { ?>
			<?php echo JHtml::_('rsfieldset.element', '<label>'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_LATE_FEE').'</label>', '<span class="rsextra">'.rseventsproHelper::currency($this->item->late_fee).'</span>'); ?>
			<?php } ?>
			
			<?php if ($this->item->tax) { ?>
			<?php echo JHtml::_('rsfieldset.element', '<label>'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TAX').'</label>', '<span class="rsextra">'.rseventsproHelper::currency($this->item->tax).'</span>'); ?>
			<?php } ?>
			
			<?php if ($event->ticketsconfig && rseventsproHelper::hasSeats($this->item->id)) echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', '<span class="rsextra"><a class="modal" rel="{handler: \'iframe\', size: {x:'.rseventsproHelper::getConfig('seats_width','int','1280').',y:'.rseventsproHelper::getConfig('seats_height','int','800').'}}" href="'.JRoute::_('index.php?option=com_rseventspro&view=subscription&layout=seats&tmpl=component&id='.$this->item->id).'">'.JText::_('COM_RSEVENTSPRO_SEATS_CONFIGURATION').'</a></span>'); ?>
			
			<?php echo JHtml::_('rsfieldset.element', '<label>'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TOTAL').'</label>', '<span class="rsextra">'.rseventsproHelper::currency(rseventsproHelper::total($this->item->id)).'</span>'); ?>
			
			<?php if (rseventsproHelper::pdf() && $this->item->state == 1 && $event->ticket_pdf == 1 && !empty($event->ticket_pdf_layout)) { ?>
			<?php echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', '<a class="rsextra" href="'.JRoute::_('index.php?option=com_rseventspro&view=pdf&id='.$this->item->id).'">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET_PDF').'</a>'); ?>
			<?php } ?>
			
			<?php } else { ?>
			<?php JText::script('COM_RSEVENTSPRO_SUBSCRIBER_PLEASE_SELECT_TICKET'); ?>
			<?php JText::script('COM_RSEVENTSPRO_SUBSCRIBER_PLEASE_SELECT_TICKET_FROM_EVENT'); ?>
			
			<?php $selectevent = ' <select name="event" id="event" onchange="rsepro_select_event(this.value);">'; ?>
			<?php $selectevent .= JHtml::_('select.options', $this->events); ?>
			<?php $selectevent .= '</select>'; ?>
			<?php $selectevent .= ' <a id="eventtickets" style="vertical-align: top; display:none;" class="modal btn" rel="{handler: \'iframe\', size: {x:'.rseventsproHelper::getConfig('seats_width','int','1280').',y:'.rseventsproHelper::getConfig('seats_height','int','800').'}, onClose: function() {rsepro_update_total();}}" href="javascript:void(0);">'.JText::_('COM_RSEVENTSPRO_SELECT_TICKETS').'</a>'; ?>
			
			<?php echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', $selectevent);  ?>
			<?php echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', '<span class="rsextra" id="rsepro_selected_tickets_view"></span><span id="rsepro_selected_tickets"></span><span id="rsepro_simple_tickets"></span>'); ?>
			<?php echo JHtml::_('rsfieldset.element', '<label>'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TOTAL').'</label>', '<span class="rsextra" id="grandtotal">'.rseventsproHelper::currency(0).'</span>'); ?>
			
			<?php } ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
		</div>
		
		<div class="span6 rswidth-50 rsfltlft">
			<?php if ($this->item->log) { ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_LOG'); ?></legend>
				<pre class="rslog"><?php echo $this->item->log; ?></pre>
			</fieldset>
			<?php } ?>
			
			<?php JFactory::getApplication()->triggerEvent('rsepro_info',array(array('method'=>&$this->item->gateway, 'data' => $this->params))); ?>
			
			<?php if (!empty($this->item->SubmissionId) && !empty($this->fields)) { ?>
			<?php echo JHtml::_('rsfieldset.start', 'adminform', JText::_('COM_RSEVENTSPRO_SUBSCRIBER_RSFORM')); ?>
			<?php foreach ($this->fields as $field) { ?>
			<?php $name = @$field['name']; ?>
			<?php $value = @$field['value']; ?>
			<?php $value = (strpos($value,'http://') !== false || strpos($value,'https://') !== false) ? '<a href="'.$value.'" target="_blank">'.$value.'</a>' : $value; ?>
			<?php echo JHtml::_('rsfieldset.element', '<label>'.$name.'</label>', '<span class="rsextra">'.$value.'</span>'); ?>
			<?php } ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
			<?php } ?>
		</div>
		
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo $this->form->getInput('ide'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>