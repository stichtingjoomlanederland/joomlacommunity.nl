<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
JText::script('COM_RSEVENTSPRO_TICKETS'); 
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
		var dialogHeight = 800;
		var dialogWidth  = 1280;
		
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
				rsepro_update_total();
			}
		});
		<?php } ?>
	}
</script>
<span id="eventID" style="display:none;"><?php echo $this->event->id; ?></span>

<?php if ($modal) { ?>
<style type="text/css">
.rs_subscribe { margin-left: 50px; margin-top: 50px; }
</style>
<?php } ?>

<?php if ($this->event->form != 0 && $this->form) { ?>
<div class="rs_subscribe">
	<span style="clear:both;display:block;"></span>
	<?php echo rseventsproHelper::loadRSForm($this->event->form); ?>
	<?php if (!empty($this->tickets) && !$this->thankyou) { ?><script type="text/javascript"><?php if ($this->event->ticketsconfig) { ?>rsepro_update_total();<?php } else { ?>rs_get_ticket($('RSEProTickets').value);<?php } ?></script><?php } ?>
</div>
<?php } else { ?>
<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribe'); ?>" method="post" name="adminForm">
<div class="rs_subscribe">
	<h1><?php echo JText::sprintf('COM_RSEVENTSPRO_SUBSCRIBER_JOIN',$this->event->name); ?></h1>
	<p>
		<label for="name" class="rs_subscribe_label"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_NAME'); ?></label>
		<input type="text" name="name" id="name" value="<?php echo rseventsproHelper::getUser($this->user->get('id')); ?>" size="40" class="rs_edit_inp_small" />
	</p>
	<div class="rs_clear"></div>
	<p>
		<label for="email" class="rs_subscribe_label"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_EMAIL'); ?></label>
		<input type="text" name="email" id="email" value="<?php echo $this->user->get('email'); ?>" size="40" class="rs_edit_inp_small" />
	</p>
	<div class="rs_clear"></div>
	<?php if (!empty($this->tickets)) { ?>
	
	<?php if ($this->event->ticketsconfig) { ?>
	<p>
		<label class="rs_subscribe_label">&nbsp;</label>
		<a href="javascript:void(0);" onclick="RSopenModal();">
			<i class="icon-cart"></i> <span id="rsepro_cart"><?php echo JText::_('COM_RSEVENTSPRO_SELECT_TICKETS'); ?></span>
		</a>
	</p>
	<div class="rs_clear"></div>
	<p>
		<label class="rs_subscribe_label"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKETS'); ?></label>
		<span id="rsepro_selected_tickets_view" style="float:left;"></span>
		<span id="rsepro_selected_tickets"></span>
	</p>
	<div class="rs_clear"></div>
	
	<?php } else { ?>
		<p>
			<label for="ticket" class="rs_subscribe_label"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_SELECT_TICKETS'); ?></label>
			<input type="text" id="numberinp" name="numberinp" value="1" size="3" style="display: none;" onkeyup="this.value=this.value.replace(/[^0-9\.\,]/g, '');rse_calculatetotal();" class="rs_edit_inp_small" />
			<select name="number" id="number" class="rs_edit_sel_small" onchange="rse_calculatetotal();"><option value="1">1</option></select>
			<?php echo $this->lists['tickets']; ?> 
			<img id="rs_loader" src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loader.gif" alt="" style="vertical-align: middle; display: none;" />
			<?php if (rseventsproHelper::getConfig('multi_tickets','int')) { ?>
			<a href="javascript:void(0);" onclick="rs_add_ticket();"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_ADD_TICKET'); ?></a>
			<?php } ?>
		</p>
		<div class="rs_clear"></div>
		<p>
			<label for="tdescription" class="rs_subscribe_label"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKET_DESC'); ?></label>
			<span id="tdescription"></span>
		</p>
		<div class="rs_clear"></div>
		<?php if (rseventsproHelper::getConfig('multi_tickets','int')) { ?>
		<p>
			<label class="rs_subscribe_label"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TICKETS'); ?></label>
			<span id="tickets" style="float:left;"></span>
			<span id="hiddentickets"></span>
		</p>
		<?php } ?>
		<div class="rs_clear"></div>
	<?php } ?>
	
	
	<?php if ($this->payment && $this->payments) { ?>
	<div id="rsepro_payment">
		<label for="payment" class="rs_subscribe_label"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_PAYMENT_METHOD'); ?></label>
		<?php echo $this->lists['payments']; ?>
	</div>
	<div class="rs_clear"></div>
	<?php if ($this->event->discounts) { ?>
	<p>
		<label for="coupon" class="rs_subscribe_label"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_PAYMENT_COUPON'); ?></label>
		<input type="text" name="coupon" id="coupon" value="" size="40" class="rs_edit_inp_small" onkeyup="<?php echo $this->updatefunction; ?>" />
		<a href="javascript:void(0)" onclick="rse_verify_coupon(<?php echo $this->event->id; ?>,$('coupon').value)">
			<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/coupon.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_COUPON_VERIFY'); ?>" style="vertical-align:middle" />
		</a>
	</p>
	<?php } ?>
	<?php } ?>
	<hr />
	<p id="grandtotalcontainer" style="display:none;">
		<label class="rs_subscribe_label"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TOTAL'); ?></label>
		<span id="grandtotal"></span>
	</p>
	<div class="rs_clear"></div>
	<p id="paymentinfocontainer" style="display:none;">
		<label class="rs_subscribe_label">&nbsp;</label>
		<span id="paymentinfo"></span>
	</p>
	<div class="rs_clear"></div>
	<p>&nbsp;</p>
	<?php } ?>
	<p>
		<label class="rs_subscribe_label">&nbsp;</label>
		<button type="submit" class="button btn btn-primary" onclick="return svalidation();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?> 
		<?php echo rseventsproHelper::redirect(false,JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'),rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name))); ?>
	</p>
	<div class="rs_clear"></div>
</div>

	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="rseventspro.subscribe" />
	<input type="hidden" name="from" id="from" value="" />
	<input type="hidden" name="id" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="tmpl" value="component" />
</form>
<?php if (!empty($this->tickets)) { ?>
<script type="text/javascript"><?php if ($this->event->ticketsconfig) { ?>rsepro_update_total();<?php } else { ?>rs_get_ticket($('ticket').value);<?php } ?></script>
<?php } ?>
<?php } ?>