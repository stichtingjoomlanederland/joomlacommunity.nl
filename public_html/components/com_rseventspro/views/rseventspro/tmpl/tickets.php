<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JText::script('COM_RSEVENTSPRO_TICKETS'); 
JText::script('COM_RSEVENTSPRO_SEATS'); 
JText::script('COM_RSEVENTSPRO_SELECT_TICKETS');
$modal = $this->config->modal == 1 || $this->config->modal == 2; ?>

<script type="text/javascript">
	<?php foreach ($this->tickets as $ticket) { ?>
var ticket_limit_<?php echo $ticket->id; ?> = <?php echo (int) rseventsproHelper::getAvailable($this->event->id, $ticket->id); ?>;
	<?php } ?>
	
	if (window.dialogArguments) {
		var thedocument = window.dialogArguments;
	} else {
		var thedocument = window.opener || window.parent;
	}
	
	jQuery(document).ready(function() {
		jQuery('#rsepro_wrapper').css('height', jQuery(window).height() - 100);
		
		thedocument.jQuery('<?php echo $this->event->form == 0 ? '#rsepro-cart-details input' : '#rsepro_selected_tickets input'; ?>').each(function () {
			if (jQuery(this).prop('name').indexOf('unlimited') != -1) {
				ticketid = jQuery(this).prop('name').replace('unlimited[','').replace(']','');
				jQuery('#rsepro_unlimited_'+ticketid).val(jQuery(this).val());
			} else {
				ticketid = jQuery(this).prop('id').replace('ticket','');
				jQuery('#rsepro_seat_'+ticketid).addClass('rsepro_selected');
			}
		});
	});
	
	function rsepro_close() {
		<?php if ($modal) { ?>thedocument.<?php echo $this->event->form == 0 ? 'rsepro_multi_seats_total()' : 'rsepro_update_total()'; ?>;window.close();<?php } else { ?>window.parent.SqueezeBox.close();<?php } ?>
	}
	
	function rsepro_seats_select(id, place, name, price) {
		<?php if ($this->event->form == 0) { ?>rsepro_add_ticket_seats(id, place);<?php } else { ?>rsepro_add_ticket(id, place, name, price);<?php } ?>
	}
	
	function rsepro_seats_reset(text) {
		<?php if ($this->event->form == 0) { ?>rsepro_reset_tickets_seats();<?php } else { ?>rsepro_reset_tickets(text);<?php } ?>
	}
	
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
				<?php echo $ticket->name; ?> - 
				<?php echo $price; ?>
				<?php if (!empty($ticket->description)) { ?>
				<i class="fa fa-info-circle <?php echo rseventsproHelper::tooltipClass(); ?>" title="<?php echo rseventsproHelper::tooltipText($ticket->description); ?>"></i>
				<?php } ?>
			</div>
		</div>
		<div id="rsepro_ticket_seats<?php echo $ticket->id; ?>" class="rsepro_ticket_seats">
			<?php if (!$ticket->seats) { ?>
			<div class="rsepro_ticket_unlimited">
				<input type="text" id="rsepro_unlimited_<?php echo $ticket->id; ?>" name="tickets[<?php echo $ticket->id; ?>][]" value="" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');rsepro_seats_select('<?php echo $ticket->id; ?>', 0, '<?php echo rawurlencode($ticket->name); ?>', '<?php echo $price; ?>');" class="input-mini rsepro_ticket_center" size="5" />
			</div>
			<?php } else { ?>
			<?php for($i=1; $i <= $ticket->seats; $i++) { ?>
			<?php $disabled = in_array($i,$selected); ?>
			<div class="rsepro_ticket_seat<?php echo $disabled ? ' rsepro_disabled' : ''; ?>" id="rsepro_seat_<?php echo $ticket->id.$i; ?>">
				<?php if ($disabled) { ?>
				<?php echo $i; ?>
				<?php } else { ?>
				<a href="javascript:void(0);" onclick="rsepro_seats_select('<?php echo $ticket->id; ?>','<?php echo $i; ?>', '<?php echo rawurlencode($ticket->name); ?>', '<?php echo $price; ?>');"><?php echo $i; ?></a>
				<?php } ?>
			</div>
			<?php } ?>
			<?php } ?>
		</div>
	</div>
	<?php $left += 270; ?>
	<?php } ?>
</div>

<div style="text-align: center;">
	<button type="button" class="btn btn-success" onclick="rsepro_close();"><?php echo JText::_('COM_RSEVENTSPRO_CLOSE_TICKETS'); ?></button>
	<button type="button" class="btn btn-primary" onclick="rsepro_seats_reset('<?php echo JText::_('COM_RSEVENTSPRO_SELECT_TICKETS'); ?>');"><?php echo JText::_('COM_RSEVENTSPRO_RESET'); ?></button>
</div>