<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JText::script('COM_RSEVENTSPRO_SUBSCRIBER_PLEASE_SELECT_TICKET');
JText::script('COM_RSEVENTSPRO_SUBSCRIBER_PLEASE_SELECT_TICKET_FROM_EVENT'); ?>

<script type="text/javascript">
	<?php if (empty($this->item->id)) { ?>
	document.addEventListener('DOMContentLoaded', function() {
		Joomla.submitbutton = function(task) {
			if (task == 'subscription.cancel') {
				Joomla.submitform(task, document.getElementById('adminForm'));
				return;
			}
			
			if (document.formvalidator.isValid(document.getElementById('adminForm'))) {
				if ((document.getElementById('rsepro_selected_tickets').innerHTML != '' || document.getElementById('rsepro_simple_tickets').innerHTML != '') && task != 'subscription.cancel') {
					Joomla.submitform(task, document.getElementById('adminForm'));
				} else {
					alert('<?php echo $this->escape(JText::_('COM_RSEVENTSPRO_PLEASE_SELECT_TICKET',true));?>');
				}
			}
		}
	});
	<?php } ?>
	
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
	
	function rsepro_show_add_tickets() {
		sel = jQuery('#event option:selected').val();
		
		if (sel == 0) {
			jQuery('#eventtickets').css('display','none');
		} else {
			jQuery('#eventtickets').css('display','');
		}
	}
	
	function rsepro_show_tickets() {
		sel = jQuery('#event option:selected').val();
		
		if (sel != 0) {
			jQuery('#rseTicketModal').on('show.bs.modal', function() {
				jQuery(this).find('iframe').prop('src','index.php?option=com_rseventspro&view=subscription&layout=tickets&tmpl=component&id=' + sel);
			}).on('hide.bs.modal', function () {
				rsepro_update_total();
			});
			jQuery('#rseTicketModal').modal('show');
		}
	}
	
	document.addEventListener('DOMContentLoaded', function() {
		jQuery('#jform_idu_id').on('change', function() {
			rsepro_get_user_details(jQuery(this).val());
		});
	});
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscription&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
			<fieldset class="options-form">
				<legend><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_INFO'); ?></legend>
				<?php echo $this->form->renderField('idu'); ?>
				<?php echo $this->form->renderField('name'); ?>
				<?php echo $this->form->renderField('email'); ?>
				<?php echo $this->form->renderField('state'); ?>
				<?php if ($this->item->state == 1) { ?>
				<?php $extra = '<a href="'.JRoute::_('index.php?option=com_rseventspro&task=subscription.activation&id='.$this->item->id).'" class="btn btn-primary">'.JText::_('COM_RSEVENTSPRO_SEND_ACTIVATION_EMAIL').'</a>'; ?>
				<?php echo RSEventsproAdapterGrid::renderField('',$extra); ?>
				<?php } ?>
				<?php if (empty($this->item->id)) { ?>
				<?php echo RSEventsproAdapterGrid::renderField('', '<label for="registration" class="checkbox"><input type="checkbox" id="registration" name="registration" value="1"> '.JText::_('COM_RSEVENTSPRO_SEND_REGISTRATION_EMAIL').'</label>'); ?>
				<?php } ?>
			</fieldset>
			
			<fieldset class="options-form">
				<legend><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_DETAILS'); ?></legend>
				<?php if ($this->item->id) { ?>
				<?php $event = $this->getEvent($this->item->ide); ?>
				<?php 
					$tickets = rseventsproHelper::getUserTickets($this->item->id);
					$purchasedtickets = '';
					if ($tickets) {
						$purchasedtickets .= '<table class="table">';
						$purchasedtickets .= '<thead>';
						$purchasedtickets .= '<tr>';
						$purchasedtickets .= '<th>'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET').'</th>';
						if (rseventsproHelper::pdf() && $this->item->state == 1) {
							$purchasedtickets .= '<th class="'.RSEventsproAdapterGrid::styles(array('center')).'">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET_PDF').'</th>';
							$purchasedtickets .= '<th class="'.RSEventsproAdapterGrid::styles(array('center')).'">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET_PDF_CODE').'</th>';
							$purchasedtickets .= '<th class="'.RSEventsproAdapterGrid::styles(array('center')).'">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET_PDF_CONFIRMED').'</th>';
						}
						$purchasedtickets .= '</tr>';
						$purchasedtickets .= '</thead>';
						foreach ($tickets as $ticket) {
							for ($j = 1; $j <= $ticket->quantity; $j++) {
								$purchasedtickets .= '<tr>';
								$purchasedtickets .= '<td>'.$ticket->name.' ('.($ticket->price > 0 ?rseventsproHelper::currency($ticket->price) : JText::_('COM_RSEVENTSPRO_GLOBAL_FREE')).')'.'</td>';
								if (rseventsproHelper::pdf() && $this->item->state == 1) {
									$code	= md5($this->item->id.$ticket->id.$j);
									$code	= substr($code,0,4).substr($code,-4);
									$code	= rseventsproHelper::getBarcodeOptions('barcode_prefix', 'RST-').$this->item->id.'-'.$code;
									$code   = in_array(rseventsproHelper::getBarcodeOptions('barcode', 'C39'), array('C39', 'C93')) ? strtoupper($code) : $code;
									$confirmed = rseventsproHelper::confirmed($this->item->id, $code);
									$hasLayout = rseventsproHelper::hasPDFLayout($ticket->layout,$this->item->SubmissionId);
									
									$purchasedtickets .= '<td class="'.RSEventsproAdapterGrid::styles(array('center')).'">'.($hasLayout ? '<a class="rsextra" href="'.JRoute::_('index.php?option=com_rseventspro&view=pdf&id='.$this->item->id.'&ide='.$this->item->ide.'&tid='.$ticket->id.'&position='.$j).'"><i class="fa fa-file-pdf-o"></i> '.$ticket->name.'</a>' : '-').'</td>';
									$purchasedtickets .= '<td class="'.RSEventsproAdapterGrid::styles(array('center')).'">'.($ticket->id ? $code : '-').'</td>';
									$purchasedtickets .= '<td class="'.RSEventsproAdapterGrid::styles(array('center')).'">';
									$purchasedtickets .= $ticket->id ? ($confirmed ? '<span class="label label-success">'.JText::_('JYES').'</span>' : '<span><a href="javascript:void(0)" class="label '.rseventsproHelper::tooltipClass().'" title="'.rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET_PDF_CONFIRMED_DESC')).'" onclick="rsepro_confirm_ticket(\''.$this->item->id.'\',\''.$code.'\', this)">'.JText::_('JNO').'</a></span>') : '-';
									$purchasedtickets .= '</td>';
								}
								$purchasedtickets .= '</tr>';
							}
						}
						$purchasedtickets .= '</table>';
					}
				?>
				
				<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_DATE'), rseventsproHelper::showdate($this->item->date,null,true), true); ?>
				<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_IP'), $this->item->ip, true); ?>
				<?php if ($event) { ?>
				<?php $date = $event->allday ? rseventsproHelper::showdate($event->start, rseventsproHelper::getConfig('global_date')) : rseventsproHelper::showdate($event->start).' - '.rseventsproHelper::showdate($event->end); ?>
				<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_EVENT'), '<a href="'.JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$event->id).'">'.$event->name.'</a> ('.$date.')', true); ?>
				<?php } ?>
				<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_PAYMENT'), rseventsproHelper::getPayment($this->item->gateway), true); ?>
				<?php echo $purchasedtickets; ?>
				
				<?php if ($this->item->discount) { ?>
				<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_DISCOUNT'), rseventsproHelper::currency($this->item->discount), true); ?>
				<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_DISCOUNT_CODE'), $this->item->coupon, true); ?>
				<?php } ?>
				
				<?php if ($this->item->early_fee) echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_EARLY_FEE'), rseventsproHelper::currency($this->item->early_fee), true); ?>
				<?php if ($this->item->late_fee) echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_LATE_FEE'), rseventsproHelper::currency($this->item->late_fee), true); ?>
				<?php if ($this->item->tax) echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TAX'), rseventsproHelper::currency($this->item->tax), true); ?>
				
				<?php if ($event && $event->ticketsconfig && rseventsproHelper::hasSeats($this->item->id)) echo RSEventsproAdapterGrid::renderField('', '<a class="btn btn-primary" href="javascript:void(0)" onclick="jQuery(\'#rseModal\').modal(\'show\');">'.JText::_('COM_RSEVENTSPRO_SEATS_CONFIGURATION').'</a>', true); ?>
				
				<?php $total = rseventsproHelper::total($this->item->id); ?>
				<?php $total = $total > 0 ? $total : 0; ?>
				<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TOTAL'), rseventsproHelper::currency($total), true); ?>
				
				<?php } else { ?>
				
				<?php $selectevent = ' <select name="event" class="custom-select" id="event" onchange="rsepro_show_add_tickets();">'; ?>
				<?php $selectevent .= JHtml::_('select.options', $this->events); ?>
				<?php $selectevent .= '</select>'; ?>
				
				<?php echo RSEventsproAdapterGrid::renderField('', $selectevent); ?>
				<?php echo RSEventsproAdapterGrid::renderField('', '<a id="eventtickets" style="vertical-align: top; display:none;" class="btn btn-primary" onclick="rsepro_show_tickets()" href="javascript:void(0);">'.JText::_('COM_RSEVENTSPRO_SELECT_TICKETS').'</a>'); ?>
				<?php echo RSEventsproAdapterGrid::renderField('', '<span id="rsepro_selected_tickets_view"></span><span id="rsepro_selected_tickets"></span><span id="rsepro_simple_tickets"></span>'); ?>
				<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TOTAL'), '<span id="grandtotal">'.rseventsproHelper::currency(0).'</span>' , true); ?>
				<?php } ?>
				
			</fieldset>
		</div>
		
		<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
			<?php if ($this->item->log) { ?>
			<fieldset class="options-form">
				<legend><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_LOG'); ?></legend>
				<pre class="rslog"><?php echo $this->item->log; ?></pre>
			</fieldset>
			<?php } ?>
			
			<?php JFactory::getApplication()->triggerEvent('onrsepro_info',array(array('method'=>&$this->item->gateway, 'data' => $this->params))); ?>
			
			<?php if (!empty($this->item->SubmissionId) && !empty($this->fields)) { ?>
			<fieldset class="options-form">
				<legend><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_RSFORM'); ?></legend>
					<?php foreach ($this->fields as $field) { ?>
					<?php $name = @$field['name']; ?>
					<?php $value = @$field['value']; ?>
					<?php $value = (strpos($value,'http://') !== false || strpos($value,'https://') !== false) ? '<a href="'.$value.'" target="_blank">'.$value.'</a>' : $value; ?>
					<?php echo RSEventsproAdapterGrid::renderField($name, $value, true); ?>
					<?php } ?>
			</fieldset>
			<?php } ?>
		</div>
		
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo $this->form->getInput('ide'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>

<?php if (isset($event) && $event->ticketsconfig && rseventsproHelper::hasSeats($this->item->id)) echo JHtml::_('bootstrap.renderModal', 'rseModal', array('title' => '&nbsp;', 'url' => JRoute::_('index.php?option=com_rseventspro&view=subscription&layout=seats&tmpl=component&id='.$this->item->id, false), 'bodyHeight' => 70, 'modalWidth' => 70, 'width' => rseventsproHelper::getConfig('seats_width','int','1280'), 'height' => rseventsproHelper::getConfig('seats_height','int','800'))); ?>

<?php echo JHtml::_('bootstrap.renderModal', 'rseTicketModal', array('title' => '&nbsp;', 'url' => '#', 'bodyHeight' => 70, 'modalWidth' => 70)); ?>