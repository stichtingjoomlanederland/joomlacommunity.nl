<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.keepalive'); 
JText::script('COM_RSEVENTSPRO_SUBSCRIBER_PLEASE_SELECT_TICKET_FROM_EVENT'); ?>

<?php if ($this->type) { ?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#rsepro_wrapper').css('height', jQuery(window).height() - 100);
	
	window.parent.jQuery('#rsepro_selected_tickets input').each(function (i, el) {
		if (jQuery(el).prop('name').indexOf('unlimited') != -1) {
			ticketid = jQuery(el).prop('name').replace('unlimited[','').replace(']','');
			jQuery('#rsepro_unlimited_'+ticketid).val(jQuery(el).val());
		} else {
			ticketid = jQuery(el).prop('id').replace('ticket','');
			jQuery('#rsepro_seat_'+ticketid).addClass('rsepro_selected');
		}
	});
});
</script>

<div id="rsepro_wrapper">
	<?php $left = 10; $top = 10; ?>
	<?php foreach ($this->tickets as $ticket) { ?>
	<?php $style = empty($ticket->position) ? 'top: '.$top.'px; left: '.$left.'px;' : rseventsproHelper::parseStyle($ticket->position); ?>
	<?php $price = $ticket->price ? rseventsproHelper::currency($ticket->price) : JText::_('COM_RSEVENTSPRO_GLOBAL_FREE'); ?>
	<?php $selected = rseventsproHelper::getSelectedSeats($ticket->id); ?>
	<div id="draggable<?php echo $ticket->id; ?>" class="draggable rsepro_front ui-widget-content" style="<?php echo $style; ?>">
		<div class="rsepro_ticket_container">
			<div class="rsepro_ticket_name">
				<?php echo $this->escape($ticket->name.' - '.$price); ?>
			</div>
		</div>
		<div id="rsepro_ticket_seats<?php echo $ticket->id; ?>" class="rsepro_ticket_seats">
			<?php if (!$ticket->seats) { ?>
			<div class="rsepro_ticket_unlimited">
				<input type="text" id="rsepro_unlimited_<?php echo $ticket->id; ?>" name="tickets[<?php echo $ticket->id; ?>][]" value="" onchange="rsepro_add_ticket('<?php echo $ticket->id; ?>',0, '<?php echo addcslashes($ticket->name, "'"); ?>', '<?php echo $price; ?>');" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');rsepro_add_ticket('<?php echo $ticket->id; ?>',0, '<?php echo addcslashes($ticket->name, "'"); ?>', '<?php echo $price; ?>');" class="input-mini rsepro_ticket_center" size="5" />
			</div>
			<?php } else { ?>
			<?php for($i=1; $i <= $ticket->seats; $i++) { ?>
			<?php $disabled = in_array($i,$selected); ?>
			<div class="rsepro_ticket_seat<?php echo $disabled ? ' rsepro_disabled' : ''; ?>" id="rsepro_seat_<?php echo $ticket->id.$i; ?>">
				<?php if ($disabled) { ?>
				<?php echo $i; ?>
				<?php } else { ?>
				<a href="javascript:void(0);" onclick="rsepro_add_ticket('<?php echo $ticket->id; ?>','<?php echo $i; ?>', '<?php echo addcslashes($ticket->name, "'"); ?>', '<?php echo $price; ?>');"><?php echo $i; ?></a>
				<?php } ?>
			</div>
			<?php } ?>
			<?php } ?>
		</div>
	</div>
	<?php $left += 270; ?>
	<?php } ?>
</div>

<?php } else { ?>
	<div id="rsepro_simple" class="rsepro_select_tickets">
		<script type="text/javascript">
		jQuery(document).ready(function() {
			window.parent.jQuery('#rsepro_simple_tickets input').each(function (i, el) {
				jQuery(jQuery(el).prop('id')).val(jQuery(el).val());
			});
		});
		</script>
		
		<?php if (!empty($this->tickets)) { ?>
		<table align="center" cellpadding="5">
		<?php foreach ($this->tickets as $ticket) { ?>
		<?php $price = $ticket->price ? rseventsproHelper::currency($ticket->price) : JText::_('COM_RSEVENTSPRO_GLOBAL_FREE'); ?>
		<tr>
			<td>
				<input type="text" name="simple_tickets[]" id="ticket<?php echo $ticket->id; ?>" value="" size="5" class="input-mini" onchange="rsepro_add_simple_ticket('<?php echo $ticket->id; ?>', '<?php echo addcslashes($ticket->name, "'"); ?>', '<?php echo $price; ?>', this.value);" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');rsepro_add_simple_ticket('<?php echo $ticket->id; ?>', '<?php echo addcslashes($ticket->name, "'"); ?>', '<?php echo $price; ?>', this.value);" /> 
			</td>
			<td>
				<?php echo $this->escape($ticket->name.' - '.$price); ?>
			</td>
		</tr>
		<?php } ?>
		</table>
		<?php } else { ?>
		<input type="text" name="simple_tickets[]" id="ticket0" value="" size="5" class="input-mini" onchange="rsepro_add_simple_ticket(0, '<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_FREE_ENTRANCE',true); ?>', 0, this.value);" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');rsepro_add_simple_ticket(0, '<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_FREE_ENTRANCE',true); ?>', 0, this.value);" /> x <?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_FREE_ENTRANCE'); ?>
		<?php } ?>
	</div>
<?php } ?>

<div style="text-align: center;">
	<button type="button" class="btn btn-success" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('COM_RSEVENTSPRO_CLOSE_TICKETS'); ?></button>
	<button type="button" class="btn btn-primary" onclick="rsepro_reset_tickets();"><?php echo JText::_('COM_RSEVENTSPRO_RESET'); ?></button>
</div>
<span id="eventID" style="display:none;"><?php echo $this->id; ?></span>