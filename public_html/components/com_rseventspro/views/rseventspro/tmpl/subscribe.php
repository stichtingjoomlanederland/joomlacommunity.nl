<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
JText::script('COM_RSEVENTSPRO_TICKETS'); 
JText::script('COM_RSEVENTSPRO_SEATS');
JHtml::_('behavior.modal','.rs_modal');
$modal = $this->config->modal == 1 || $this->config->modal == 2; ?>

<script type="text/javascript">
	<?php if ($this->event->max_tickets) { ?>var maxtickets = parseInt(<?php echo $this->event->max_tickets_amount; ?>);<?php echo "\n"; } ?>
	<?php if ($this->event->max_tickets) { ?>var usedtickets = parseInt(<?php echo rseventsproHelper::getUsedTickets($this->event->id); ?>);<?php echo "\n"; } ?>
	var multitickets = <?php echo rseventsproHelper::getConfig('multi_tickets','int').";\n"; ?>
	var smessage = new Array();
	smessage[0] = '<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_MESSAGE_NAME',true); ?>';
	smessage[1] = '<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_MESSAGE_EMAIL',true); ?>';
	smessage[2] = '<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_REMOVE_TICKET',true); ?>';
	smessage[3] = '<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_NO_TICKETS_SELECTED',true); ?>';
	smessage[4] = '<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_INVALID_EMAIL_ADDRESS',true); ?>';
	smessage[5] = '<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_NO_MORE_TICKETS',true); ?>';
	smessage[6] = '<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_NO_MORE_TICKETS_ALLOWED',true); ?>';
	smessage[7] = '<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_SINGLE_TICKET',true); ?>';
	
	function RSopenModal() {
		var dialogHeight = <?php echo rseventsproHelper::getConfig('seats_height','int','800'); ?>;
		var dialogWidth  = <?php echo rseventsproHelper::getConfig('seats_width','int','1280'); ?>;
		
		<?php if ($modal) { ?>
		if (window.showModalDialog) {
			window.showModalDialog('<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=tickets&tmpl=component&id='.rseventsproHelper::sef($this->event->id,$this->event->name)); ?>', window, "dialogHeight:"+dialogHeight+"px; dialogWidth:"+dialogWidth+"px;");
		} else {
			window.open('<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=tickets&tmpl=component&id='.rseventsproHelper::sef($this->event->id,$this->event->name)); ?>', 'seatswindow','status=0,toolbar=0,width='+dialogWidth+',height='+dialogHeight);
		}
		<?php } else { ?>
		SqueezeBox.open('<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=tickets&tmpl=component&id='.rseventsproHelper::sef($this->event->id,$this->event->name)); ?>', {
			handler: 'iframe',
			size: {
				x:dialogWidth,
				y:dialogHeight
			},
			onClose: function() {
				<?php echo $this->event->form == 0 ? 'rsepro_multi_seats_total();' : 'rsepro_update_total();'; ?>
			}
		});
		<?php } ?>
	}
</script>

<?php if ($modal) { ?>
<style type="text/css">
.rs_subscribe { margin-left: 50px; margin-top: 50px; }
</style>
<?php } ?>

<?php if ($this->event->form != 0 && $this->form) { ?>
<div class="rs_subscribe">
	<span style="clear:both;display:block;"></span>
	<?php echo rseventsproHelper::loadRSForm($this->event->form); ?>
	<?php if (!empty($this->tickets) && !$this->thankyou) { ?><script type="text/javascript"><?php if ($this->event->ticketsconfig) { ?>rsepro_update_total();<?php } else { ?>rs_get_ticket(jQuery('#RSEProTickets').val());<?php } ?></script><?php } ?>
</div>
<?php } else { ?>
<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribe'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
<div class="rs_subscribe">
	<h1><?php echo JText::sprintf('COM_RSEVENTSPRO_SUBSCRIBER_JOIN',$this->event->name); ?></h1>
	
	<div class="control-group">
		<div class="control-label">
			<label for="name"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_NAME'); ?></label>
		</div>
		<div class="controls">
			<input type="text" name="name" id="name" value="<?php echo rseventsproHelper::getUser($this->user->get('id')); ?>" size="40" class="input-large" />
		</div>
	</div>
	
	<div class="control-group">
		<div class="control-label">
			<label for="email"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_EMAIL'); ?></label>
		</div>
		<div class="controls">
			<input type="text" name="email" id="email" value="<?php echo $this->user->get('email'); ?>" size="40" class="input-large" />
		</div>
	</div>
	
	<?php if (!empty($this->tickets)) { ?>
	
	<?php if ($this->event->ticketsconfig) { ?>
	<div class="control-group">
		<div class="control-label">
			<label>&nbsp;</label>
		</div>
		<div class="controls">
			<a href="javascript:void(0);" onclick="RSopenModal();">
				<i class="fa fa-shopping-cart"></i> <span id="rsepro_cart"><?php echo JText::_('COM_RSEVENTSPRO_SELECT_TICKETS'); ?></span>
			</a>
		</div>
	</div>
	
	<?php } else { ?>
	
	<div class="control-group">
		<div class="control-label">
			<label for="ticket"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_SELECT_TICKETS'); ?></label>
		</div>
		<div class="controls">
			<input type="text" id="numberinp" name="numberinp" value="1" size="3" style="display: none;" onkeyup="<?php echo $this->js; ?>" class="input-mini" />
			<select name="number" id="number" class="input-mini" onchange="<?php echo $this->js; ?>"><option value="1">1</option></select>
			<select name="ticket" id="ticket" onchange="<?php echo $this->js; ?>" size="1" class="input-large">
				<?php echo JHtml::_('select.options', $this->tickets); ?>
			</select>
			<img id="rs_loader" src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loader.gif" alt="" style="vertical-align: middle; display: none;" />
			<?php if ($this->config->multi_tickets) { ?>
			<a href="javascript:void(0);" onclick="rsepro_add_multiple_tickets();"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_ADD_TICKET'); ?></a>
			<?php } ?>
		</div>
	</div>
	
	<?php } ?>
	
	<?php if ($this->payment && $this->payments) { ?>
	
	<div class="control-group" id="rsepro_payment">
		<div class="control-label">
			<label for="payment"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_PAYMENT_METHOD'); ?></label>
		</div>
		<div class="controls">
			<?php echo $this->lists['payments']; ?>
		</div>
	</div>
	
	<?php if ($this->event->discounts) { ?>
	
	<div class="control-group">
		<div class="control-label">
			<label for="coupon"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_PAYMENT_COUPON'); ?></label>
		</div>
		<div class="controls">
			<input type="text" name="coupon" id="coupon" value="" size="40" class="input-large" onkeyup="<?php echo $this->updatefunction; ?>" />
			<a href="javascript:void(0)" onclick="rse_verify_coupon(<?php echo $this->event->id; ?>,document.getElementById('coupon').value)">
				<i class="fa fa-refresh"></i>
			</a>
		</div>
	</div>
	
	<?php } ?>
	
	<?php } ?>
	
	<table class="table table-striped table-condensed" id="rsepro-cart-details">
		
		<tr class="rsepro-cart-options" id="rsepro-cart-discount" style="display: none;">
			<td>
				<strong><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DISCOUNT'); ?></strong>
				<span></span>
			</td>
			<td></td>
			<td>&nbsp;</td>
		</tr>
		<tr class="rsepro-cart-options" id="rsepro-cart-latefee" style="display: none;">
			<td>
				<strong><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LATE_FEE'); ?></strong>
			</td>
			<td></td>
			<td>&nbsp;</td>
		</tr>
		<tr class="rsepro-cart-options" id="rsepro-cart-earlybooking" style="display: none;">
			<td>
				<strong><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_EARLY_FEE'); ?></strong>
			</td>
			<td></td>
			<td>&nbsp;</td>
		</tr>
		<tr class="rsepro-cart-options" id="rsepro-cart-tax" style="display: none;">
			<td>
				<strong><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TAX'); ?></strong>
			</td>
			<td></td>
			<td>&nbsp;</td>
		</tr>
		<tr class="rsepro-cart-options" id="rsepro-cart-total">
			<td>
				<strong><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TOTAL'); ?></strong>
			</td>
			<td><?php echo rseventsproHelper::currency(0); ?></td>
			<td>&nbsp;</td>
		</tr>
	</table>
	
	<?php } ?>	

	<hr />
	
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="button btn btn-primary" onclick="return rsepro_validate_subscription();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?> 
			<?php echo rseventsproHelper::redirect(false,JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'),rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id))); ?>
		</div>
	</div>

</div>

	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="rseventspro.subscribe" />
	<input type="hidden" name="from" id="from" value="" />
	<input type="hidden" name="id" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="tmpl" value="component" />
</form>
<?php if (!empty($this->tickets) && !$this->event->ticketsconfig) { ?>
<script type="text/javascript">
jQuery(document).ready(function() {
	<?php echo $this->js; ?>
});
</script>
<?php } ?>
<?php } ?>

<span id="eventID" style="display:none;"><?php echo $this->event->id; ?></span>