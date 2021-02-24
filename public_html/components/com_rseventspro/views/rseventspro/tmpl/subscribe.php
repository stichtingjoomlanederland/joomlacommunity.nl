<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
JText::script('COM_RSEVENTSPRO_TICKETS'); 
JText::script('COM_RSEVENTSPRO_SEATS');
JText::script('ERROR'); ?>

<script type="text/javascript">
	var rseproTypingTimer;
	
	jQuery(document).ready(function() {
		jQuery('#coupon').keyup(function(){
			clearTimeout(rseproTypingTimer);
			
			if (jQuery('#coupon').val()) {
				rseproTypingTimer = setTimeout(function() {
				<?php echo $this->updatefunction; ?>
				}, 500);
			}
		});
	});
	
	<?php if ($this->event->tickets_amount) { ?>var ticketsamount = parseInt(<?php echo $this->event->tickets_amount; ?>);<?php echo "\n"; } ?>
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
	smessage[8] = '<?php echo JText::_('COM_RSEVENTSPRO_CONSENT_INFO',true); ?>';
	smessage[9] = '<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_MAX_TICKETS',true); ?>';
	smessage[10] = '<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_CAPTCHA_ERROR',true); ?>';
	
	function RSopenModal() {
		var dialogHeight = <?php echo rseventsproHelper::getConfig('seats_height','int','800'); ?>;
		var dialogWidth  = <?php echo rseventsproHelper::getConfig('seats_width','int','1280'); ?>;
		
		<?php if ($this->config->modal == 1) { ?>
		if (window.showModalDialog) {
			window.showModalDialog('<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=tickets&tmpl=component&id='.rseventsproHelper::sef($this->event->id,$this->event->name)); ?>', window, "dialogHeight:"+dialogHeight+"px; dialogWidth:"+dialogWidth+"px;");
		} else {
			window.open('<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=tickets&tmpl=component&id='.rseventsproHelper::sef($this->event->id,$this->event->name)); ?>', 'seatswindow','status=0,toolbar=0,width='+dialogWidth+',height='+dialogHeight);
		}
		<?php } else { ?>
		<?php if ($this->config->modaltype == 1) { ?>
		jQuery('#rseTicketsModal').on('hide.bs.modal', function () {
			<?php echo $this->event->form == 0 ? 'rsepro_multi_seats_total();' : 'rsepro_update_total();'; ?>
		});
		jQuery('#rseTicketsModal').modal('show');
		<?php } else { ?>
		jQuery.colorbox({href: '<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=tickets&tmpl=component&id='.rseventsproHelper::sef($this->event->id,$this->event->name)); ?>', iframe:true, innerWidth: '<?php echo rseventsproHelper::getConfig('seats_width','int','1280'); ?>', innerHeight: '<?php echo rseventsproHelper::getConfig('seats_height','int','800'); ?>', maxWidth:'95%', maxHeight:'95%', onClosed: function() { <?php echo $this->event->form == 0 ? 'rsepro_multi_seats_total();' : 'rsepro_update_total();'; ?> }});
		<?php } ?>
		<?php } ?>
	}
</script>

<?php if ($this->config->modal == 1) { ?><style type="text/css">.rs_subscribe { margin-left: 50px; margin-top: 50px; }</style><?php } ?>

<?php if ($this->event->form != 0 && $this->form) { ?>
<div class="rs_subscribe">
	<span style="clear:both;display:block;"></span>
	<?php echo rseventsproHelper::loadRSForm($this->event->form); ?>
	<?php if (!empty($this->tickets) && !$this->thankyou) { ?><script type="text/javascript"><?php if ($this->event->ticketsconfig) { ?>rsepro_update_total();<?php } else { ?>rs_get_ticket(jQuery('#RSEProTickets').val());<?php } ?></script><?php } ?>
</div>
<?php } else { ?>
<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribe'); ?>" method="post" name="subscribeForm" id="subscribeForm" class="rsepro-horizontal form-horizontal" autocomplete="off">
<div class="rs_subscribe">
	<h1><?php echo JText::sprintf('COM_RSEVENTSPRO_SUBSCRIBER_JOIN',$this->event->name); ?></h1>
	
	<?php echo RSEventsproAdapterGrid::renderField('<label for="name">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_NAME').'</label>', '<input type="text" name="name" id="name" value="'.rseventsproHelper::getUser($this->user->get('id')).'" size="40" class="input-large form-control" />'); ?>
	<?php echo RSEventsproAdapterGrid::renderField('<label for="email">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_EMAIL').'</label>', '<input type="text" name="email" id="email" value="'.$this->user->get('email').'" size="40" class="input-large form-control" />'); ?>
	
	<?php if (!empty($this->tickets)) { ?>
	
	<?php if ($this->event->ticketsconfig) { ?>
	<?php echo RSEventsproAdapterGrid::renderField('', '<a href="javascript:void(0);" onclick="RSopenModal();"><i class="fa fa-shopping-cart"></i> <span id="rsepro_cart">'.JText::_('COM_RSEVENTSPRO_SELECT_TICKETS').'</span></a>'); ?>
	<?php } else { ?>
	<?php $input = '<select name="ticket" id="ticket" onchange="'.$this->js.'" size="1" class="input-large custom-select">'.JHtml::_('select.options', $this->tickets).'</select>'; ?>
	<?php $prepend = '<input type="text" id="numberinp" name="numberinp" value="1" size="3" style="display: none;" onkeyup="'.$this->js.'" class="input-mini form-control" /><select name="number" id="number" class="input-mini custom-select" onchange="'.$this->js.'"><option value="1">1</option></select>'; ?>
	<?php $append = $this->config->multi_tickets ? '<a href="javascript:void(0);" onclick="rsepro_add_multiple_tickets();">'.JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'rs_loader', 'style' => 'vertical-align: middle; display: none;'), true).JText::_('COM_RSEVENTSPRO_SUBSCRIBER_ADD_TICKET').'</a>' : null; ?>
	<?php echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_SUBSCRIBER_SELECT_TICKETS'), RSEventsproAdapterGrid::inputGroup($input, $prepend, $append)); ?>
	<?php } ?>
	
	<?php if ($this->payment && $this->payments) { ?>
	<?php echo RSEventsproAdapterGrid::renderField('<label for="payment">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_PAYMENT_METHOD').'</label>', $this->lists['payments']); ?>
	<?php if ($this->event->discounts) { ?>
	<?php echo RSEventsproAdapterGrid::renderField('<label for="coupon">'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_PAYMENT_COUPON').'</label>', RSEventsproAdapterGrid::inputGroup('<input type="text" name="coupon" id="coupon" value="" size="40" class="input-large form-control" />', null, '<a href="javascript:void(0)" onclick="rse_verify_coupon('.$this->event->id.',document.getElementById(\'coupon\').value)"><i class="fa fa-refresh"></i></a>')); ?>
	<?php } ?>
	<?php } ?>
	
	<?php if (in_array(2,$this->captcha_use)) { ?>
	<div class="control-group" id="rsepro_subscribe_captcha_block">
		<div class="control-label">&nbsp;</div>
		<?php if ($this->config->captcha == 1) { ?>
		<div class="controls">
			<img id="rsepro_subscribe_captcha" src="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=captcha&tmpl=component&rand='.rand(),false); ?>" onclick="reloadSubscribeCaptcha()" />
			<span class="explain">
				<?php echo JText::_('COM_RSEVENTSPRO_CAPTCHA_TEXT'); ?> <?php echo JText::_('COM_RSEVENTSPRO_CAPTCHA_RELOAD'); ?>
			</span>
			<input type="text" id="rsepro_subscribe_secret" name="secret" value="" class="input-large form-control" />
		</div>
		<?php } elseif ($this->config->captcha == 2) { ?>
		<div class="controls">
			<div id="rse-g-recaptcha"></div>
		</div>
		<?php } elseif ($this->config->captcha == 3) { ?>
		<div class="controls">
			<div id="h-captcha-subscribe"></div>
		</div>
		<?php } ?>
	</div>
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
	
	<?php } else { ?>
	<?php if (in_array(2,$this->captcha_use)) { ?>
	<div class="control-group" id="rsepro_subscribe_captcha_block">
		<div class="control-label">&nbsp;</div>
		<?php if ($this->config->captcha == 1) { ?>
		<div class="controls">
			<img id="rsepro_subscribe_captcha" src="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=captcha&tmpl=component&rand='.rand(),false); ?>" onclick="reloadSubscribeCaptcha()" />
			<span class="explain">
				<?php echo JText::_('COM_RSEVENTSPRO_CAPTCHA_TEXT'); ?> <?php echo JText::_('COM_RSEVENTSPRO_CAPTCHA_RELOAD'); ?>
			</span>
			<input type="text" id="rsepro_subscribe_secret" name="secret" value="" class="input-large form-control" />
		</div>
		<?php } elseif ($this->config->captcha == 2) { ?>
		<div class="controls">
			<div id="rse-g-recaptcha"></div>
		</div>
		<?php } elseif ($this->config->captcha == 3) { ?>
		<div class="controls">
			<div id="h-captcha-subscribe"></div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>
	<?php } ?>	
	
	<?php if (rseventsproHelper::getConfig('consent','int','1')) { ?>
	<div class="control-group">
		<div class="controls">
			<label class="checkbox inline">
				<input type="checkbox" name="consent" id="consent" value="1" /> <?php echo JText::_('COM_RSEVENTSPRO_CONSENT'); ?>
			</label>
		</div>
	</div>
	<?php } ?>

	<hr />
	
	<div class="control-group">
		<div class="controls">
			<button id="subscribeBtn" type="submit" class="button btn btn-primary" onclick="return rsepro_validate_subscription();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?> 
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
<?php if ($this->config->modaltype == 1) echo JHtml::_('bootstrap.renderModal', 'rseTicketsModal', array('title' => JText::_('COM_RSEVENTSPRO_SELECT_TICKETS'), 'url' => rseventsproHelper::route('index.php?option=com_rseventspro&layout=tickets&tmpl=component&id='.rseventsproHelper::sef($this->event->id,$this->event->name)), 'bodyHeight' => 70, 'width' => rseventsproHelper::getConfig('seats_width','int','1280'), 'height' => rseventsproHelper::getConfig('seats_height','int','800') )); ?>