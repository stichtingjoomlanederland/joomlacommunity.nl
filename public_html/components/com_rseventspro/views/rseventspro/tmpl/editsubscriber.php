<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
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
	
	// do field validation
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

<fieldset class="rs_fieldset form-horizontal">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_INFO'); ?></legend>
	
	<div class="control-group">
		<div class="control-label">
			<label for="jform_name"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_NAME'); ?></label>
		</div>
		<div class="controls">
			<input type="text" name="jform[name]" value="<?php echo $this->escape($subscriber->name); ?>" id="jform_name" size="60" class="input-xlarge" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<label for="jform_email"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_EMAIL'); ?></label>
		</div>
		<div class="controls">
			<input type="text" name="jform[email]" value="<?php echo $this->escape($subscriber->email); ?>" id="jform_email" size="60" class="input-xlarge" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<label for="jform_state"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS'); ?></label>
		</div>
		<div class="controls">
			<?php if (!$this->user) { ?>
			<?php echo $this->lists['status']; ?>
			<?php } else { ?>
			<div class="rsepro-text"><?php echo $this->getStatus($subscriber->state); ?></div>
			<?php } ?>
		</div>
	</div>
</fieldset>

<div class="rs_clear"></div>

<fieldset class="rs_fieldset">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_DETAILS'); ?></legend>
	<table cellspacing="0" cellpadding="3" border="0" class="rs_table">
		<tr>
			<td width="160"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_DATE'); ?></td>
			<td><?php echo rseventsproHelper::showdate($subscriber->date); ?></td>
		</tr>
		<tr>
			<td width="160"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_IP'); ?></td>
			<td><?php echo $subscriber->ip; ?></td>
		</tr>
		<?php if (!empty($subscriber->gateway)) { ?>
		<tr>
			<td width="160"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_PAYMENT'); ?></td>
			<td><?php echo rseventsproHelper::getPayment($subscriber->gateway); ?></td>
		</tr>
		<?php } ?>
		<?php if (!empty($tickets)) { ?>
		<tr>
			<td width="160"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKETS'); ?></td>
			<td>
				<?php $purchasedtickets = ''; ?>
				<?php if ($tickets) {
						$purchasedtickets .= '<table class="table">';
						$purchasedtickets .= '<thead>';
						$purchasedtickets .= '<tr>';
						$purchasedtickets .= '<th>'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET').'</th>';
						if (rseventsproHelper::pdf() && $subscriber->state == 1) {
							$purchasedtickets .= '<th align="center" class="center">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET_PDF').'</th>';
							$purchasedtickets .= '<th align="center" class="center">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET_PDF_CODE').'</th>';
							
							if (!$this->user) {
								$purchasedtickets .= '<th align="center" class="center">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET_PDF_CONFIRMED').'</th>';
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
									
									$purchasedtickets .= '<td align="center" class="center">'.($hasLayout ? '<a class="rsextra" href="'.JRoute::_('index.php?option=com_rseventspro&layout=ticket&from=subscriber&format=raw&id='.$subscriber->id.'&ide='.$subscriber->ide.'&tid='.$ticket->id.'&position='.$j.$scode).'"><i class="fa fa-file-pdf-o"></i> '.$ticket->name.'</a>' : '-').'</td>';
									$purchasedtickets .= '<td align="center" class="center">'.($ticket->id ? $code : '-').'</td>';

									if (!$this->user) {
										$purchasedtickets .= '<td align="center" class="center">';
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
			</td>
		</tr>
		<?php } ?>
		<?php if ($subscriber->discount) { ?>
		<tr>
			<td width="160"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DISCOUNT'); ?></td>
			<td><?php echo rseventsproHelper::currency($subscriber->discount); ?></td>
		</tr>
		<tr>
			<td width="160"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DISCOUNT_CODE'); ?></td>
			<td><?php echo $subscriber->coupon; ?></td>
		</tr>
		<?php $total = $total - $subscriber->discount; ?>
		<?php } ?>
		<?php if ($subscriber->early_fee) { ?>
		<tr>
			<td width="160"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_EARLY_FEE'); ?></td>
			<td><?php echo rseventsproHelper::currency($subscriber->early_fee); ?></td>
		</tr>
		<?php $total = $total - $subscriber->early_fee; ?>
		<?php } ?>
		<?php if ($subscriber->late_fee) { ?>
		<tr>
			<td width="160"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LATE_FEE'); ?></td>
			<td><?php echo rseventsproHelper::currency($subscriber->late_fee); ?></td>
		</tr>
		<?php $total = $total + $subscriber->late_fee; ?>
		<?php } ?>
		<?php if ($subscriber->tax) { ?>
		<tr>
			<td width="160"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TAX'); ?></td>
			<td><?php echo rseventsproHelper::currency($subscriber->tax); ?></td>
		</tr>
		<?php $total = $total + $subscriber->tax; ?>
		<?php } ?>
		
		<?php if ($event->ticketsconfig && rseventsproHelper::hasSeats($subscriber->id) && !$this->user) { ?>
		<tr>
			<td width="160">&nbsp;</td>
			<td><a class="btn" onclick="jQuery('#rseModal').modal('show');" href="javascript:void(0);"><?php echo JText::_('COM_RSEVENTSPRO_SEATS_CONFIGURATION'); ?></a></td>
		</tr>
		<?php } ?>
		<tr>
			<td width="160"><b><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TOTAL'); ?></b></td>
			<td><span id="total"><?php echo rseventsproHelper::currency($total); ?></span></td>
		</tr>
	</table>
</fieldset>

<div class="rs_clear"></div>

<?php if (!empty($subscriber->log)) { ?>
<fieldset class="rs_fieldset">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_LOG'); ?></legend>
	<table cellspacing="0" cellpadding="3" border="0" class="rs_table">
		<tr>
			<td><?php echo $subscriber->log; ?></td>
		</tr>
	</table>
</fieldset>
<?php } ?>

<div class="rs_clear"></div>
<?php JFactory::getApplication()->triggerEvent('rsepro_info',array(array('method'=>&$subscriber->gateway, 'data' => $this->tparams))); ?>
<div class="rs_clear"></div>

<?php if (!empty($subscriber->SubmissionId) && !empty($this->fields)) { ?>
<fieldset class="rs_fieldset">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_RSFORM'); ?></legend>
	<table cellspacing="0" cellpadding="3" border="0" class="rs_table">
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

<?php echo JHtml::_('bootstrap.renderModal', 'rseModal', array('title' => '&nbsp;', 'url' => rseventsproHelper::route('index.php?option=com_rseventspro&layout=userseats&tmpl=component&id='.rseventsproHelper::sef($subscriber->id,$subscriber->name)), 'bodyHeight' => 70, 'width' => rseventsproHelper::getConfig('seats_width','int','1280'), 'height' => rseventsproHelper::getConfig('seats_height','int','800'))); ?>