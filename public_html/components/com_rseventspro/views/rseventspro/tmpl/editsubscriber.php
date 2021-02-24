<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$total		= 0;
$subscriber = $this->data['data'];
$tickets	= $this->data['tickets'];
$event		= $this->data['event']; ?>

<h1><?php echo JText::_('COM_RSEVENTSPRO_EDIT_SUBSCRIBER'); ?></h1>

<script type="text/javascript">
function rs_validate_subscr() {
	var ret = true;
	var msg = new Array();
	
	if (jQuery('#jform_name').val().length == 0) {
		jQuery('#jform_name').addClass('invalid');
		msg.push('<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_ADD_NAME', true); ?>');
		ret = false;
	} else {
		jQuery('#jform_name').removeClass('invalid');
	}
	
	if (jQuery('#jform_email').val().length == 0) {
		jQuery('#jform_email').addClass('invalid');
		msg.push('<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_ADD_EMAIL', true); ?>');
		ret = false;
	} else {
		jQuery('#jform_email').removeClass('invalid');
	}
	
	if (ret) {
		return true;
	} else {
		alert(msg.join("\n"));
		return false;
	}
}
</script>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editsubscriber'); ?>" method="post" name="adminForm" id="adminForm" onsubmit="return rs_validate_subscr();">

<div style="text-align:right;">
	<button type="submit" class="button btn btn-primary" onclick="return rs_validate_subscr();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?>
	<?php if (!$this->rlink) { ?>
	<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribers&id='.rseventsproHelper::sef($event->id,$event->name)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></a>
	<?php } else { ?>
	<a href="<?php echo $this->rlink; ?>"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></a>
	<?php } ?>
</div>

<fieldset class="options-form form-horizontal rsepro-horizontal">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_INFO'); ?></legend>
	
	<?php echo $this->form->renderField('name'); ?>
	<?php echo $this->form->renderField('email'); ?>
	<?php if (!$this->user) echo $this->form->renderField('state'); ?>
	<?php if ($this->user) echo RSEventsproAdapterGrid::renderField($this->form->getLabel('state'), $this->getStatus($subscriber->state), true); ?>
</fieldset>

<fieldset class="options-form form-horizontal rsepro-horizontal">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_DETAILS'); ?></legend>
	
	<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_DATE'), rseventsproHelper::showdate($subscriber->date), true); ?>
	<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_IP'), $subscriber->ip, true); ?>
	<?php if (!empty($subscriber->gateway)) echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_PAYMENT'), rseventsproHelper::getPayment($subscriber->gateway), true); ?>
	<?php 
		$purchasedtickets = '';
		 
		if ($tickets) {
			$purchasedtickets .= '<table class="table">';
			$purchasedtickets .= '<thead>';
			$purchasedtickets .= '<tr>';
			$purchasedtickets .= '<th>'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET').'</th>';
			if (rseventsproHelper::pdf() && $subscriber->state == 1) {
				$purchasedtickets .= '<th class="'.RSEventsproAdapterGrid::styles(array('center')).'">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET_PDF').'</th>';
				$purchasedtickets .= '<th class="'.RSEventsproAdapterGrid::styles(array('center')).'">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET_PDF_CODE').'</th>';
				
				if (!$this->user) {
					$purchasedtickets .= '<th class="'.RSEventsproAdapterGrid::styles(array('center')).'">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET_PDF_CONFIRMED').'</th>';
				}
			}
			$purchasedtickets .= '</tr>';
			$purchasedtickets .= '</thead>';
			
			foreach ($tickets as $ticket) {
				$total += (int) $ticket->quantity * $ticket->price;
				for ($j = 1; $j <= $ticket->quantity; $j++) {
					$purchasedtickets .= '<tr>';
					$purchasedtickets .= '<td>'.$ticket->name.' ('.($ticket->price > 0 ?rseventsproHelper::currency($ticket->price) : JText::_('COM_RSEVENTSPRO_GLOBAL_FREE')).')'.'</td>';
					if (rseventsproHelper::pdf() && $subscriber->state == 1) {
						$code	= md5($subscriber->id.$ticket->id.$j);
						$code	= substr($code,0,4).substr($code,-4);
						$code	= rseventsproHelper::getBarcodeOptions('barcode_prefix', 'RST-').$subscriber->id.'-'.$code;
						$code	= in_array(rseventsproHelper::getBarcodeOptions('barcode', 'C39'), array('C39', 'C93')) ? strtoupper($code) : $code;
						$confirmed	= rseventsproHelper::confirmed($subscriber->id, $code);
						$hasLayout	= rseventsproHelper::hasPDFLayout($ticket->layout,$subscriber->SubmissionId);
						$scode		= JFactory::getApplication()->input->getString('code');
						$scode		= $scode ? '&code='.$scode : '';
						
						$purchasedtickets .= '<td class="'.RSEventsproAdapterGrid::styles(array('center')).'">'.($hasLayout ? '<a class="rsextra" href="'.JRoute::_('index.php?option=com_rseventspro&layout=ticket&from=subscriber&format=raw&id='.$subscriber->id.'&ide='.$subscriber->ide.'&tid='.$ticket->id.'&position='.$j.$scode).'"><i class="fa fa-file-pdf-o"></i> '.$ticket->name.'</a>' : '-').'</td>';
						$purchasedtickets .= '<td class="'.RSEventsproAdapterGrid::styles(array('center')).'">'.($ticket->id ? $code : '-').'</td>';

						if (!$this->user) {
							$purchasedtickets .= '<td class="'.RSEventsproAdapterGrid::styles(array('center')).'">';
							$purchasedtickets .= $ticket->id ? ($confirmed ? '<span class="label label-success">'.JText::_('JYES').'</span>' : '<span><a href="javascript:void(0)" class="label '.rseventsproHelper::tooltipClass().'" title="'.rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET_PDF_CONFIRMED_DESC')).'" onclick="rsepro_confirm_ticket(\''.$subscriber->id.'\',\''.$code.'\', this)">'.JText::_('JNO').'</a></span>') : '-';
							$purchasedtickets .= '</td>';
						}
					}
					$purchasedtickets .= '</tr>';
				}
			}
			$purchasedtickets .= '</table>';
		}
		echo $purchasedtickets;
	?>
	<?php if ($subscriber->discount) echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_GLOBAL_DISCOUNT'), rseventsproHelper::currency($subscriber->discount), true); ?>
	<?php if ($subscriber->discount) echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_GLOBAL_DISCOUNT_CODE'), $subscriber->coupon, true); ?>
	<?php if ($subscriber->discount) $total = $total - $subscriber->discount; ?>
	<?php if ($subscriber->early_fee) echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_GLOBAL_EARLY_FEE'), rseventsproHelper::currency($subscriber->early_fee), true); ?>
	<?php if ($subscriber->early_fee) $total = $total - $subscriber->early_fee; ?>
	<?php if ($subscriber->late_fee) echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_GLOBAL_LATE_FEE'), rseventsproHelper::currency($subscriber->late_fee), true); ?>
	<?php if ($subscriber->late_fee) $total = $total + $subscriber->late_fee; ?>
	<?php if ($subscriber->tax) echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_GLOBAL_TAX'), rseventsproHelper::currency($subscriber->tax), true); ?>
	<?php if ($subscriber->tax) $total = $total + $subscriber->tax; ?>
	<?php if ($event->ticketsconfig && rseventsproHelper::hasSeats($subscriber->id) && !$this->user) echo RSEventsproAdapterGrid::renderField('', '<a class="btn btn-primary" rel="rs_seats" '.($this->config->modaltype == 1 ? 'onclick="jQuery(\'#rseModal\').modal(\'show\');" href="javascript:void(0);"' : 'href="'.rseventsproHelper::route('index.php?option=com_rseventspro&layout=userseats&tmpl=component&id='.rseventsproHelper::sef($subscriber->id,$subscriber->name)).'"').'>'.JText::_('COM_RSEVENTSPRO_SEATS_CONFIGURATION').'</a>', true); ?>
	<?php $total = $total > 0 ? $total : 0; ?>
	<?php echo RSEventsproAdapterGrid::renderField('<strong>'.JText::_('COM_RSEVENTSPRO_GLOBAL_TOTAL').'</strong>', rseventsproHelper::currency($total), true); ?>
</fieldset>

<?php if (!empty($subscriber->log)) { ?>
<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_LOG'); ?></legend>
	<pre><?php echo $subscriber->log; ?></pre>
</fieldset>
<?php } ?>

<?php JFactory::getApplication()->triggerEvent('onrsepro_info',array(array('method'=>&$subscriber->gateway, 'data' => $this->tparams))); ?>

<?php if (!empty($subscriber->SubmissionId) && !empty($this->fields)) { ?>
<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_RSFORM'); ?></legend>
	<table cellspacing="0" cellpadding="3" border="0" class="table">
	<?php foreach ($this->fields as $field) { ?>
	<?php $name = @$field['name']; ?>
	<?php $value = @$field['value']; ?>
		<tr> 
			<td width="160"><?php echo $name; ?></td> 
			<td><?php echo strpos($value,'http://') !== false || strpos($value,'https://') !== false ? '<a href="'.$value.'" target="_blank">'.$value.'</a>' : $value; ?></td>
		</tr>
	<?php } ?>
	</table>
</fieldset>
<?php } ?>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="rseventspro.savesubscriber" />
	<input type="hidden" name="jform[id]" value="<?php echo $subscriber->id; ?>" />
	<input type="hidden" name="ide" value="<?php echo $event->id; ?>" />
	<input type="hidden" name="isuser" value="<?php echo (int) $this->user; ?>" />
	<input type="hidden" name="code" value="<?php echo JFactory::getApplication()->input->getString('code'); ?>" />
</form>

<?php if (rseventsproHelper::getConfig('modaltype','int') == 1) echo JHtml::_('bootstrap.renderModal', 'rseModal', array('title' => '&nbsp;', 'url' => rseventsproHelper::route('index.php?option=com_rseventspro&layout=userseats&tmpl=component&id='.rseventsproHelper::sef($subscriber->id,$subscriber->name)), 'bodyHeight' => 70, 'width' => rseventsproHelper::getConfig('seats_width','int','1280'), 'height' => rseventsproHelper::getConfig('seats_height','int','800'))); ?>