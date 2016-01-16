<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.tooltip');
JText::script('COM_RSEVENTSPRO_TICKETS'); 
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
	
	window.addEvent('domready', function() {
		$('rsepro_wrapper').setStyle('height', window.getSize().y - 100);
		
		thedocument.$$('#rsepro_selected_tickets input').each(function (el) {
			if (el.name.indexOf('unlimited') != -1) {
				ticketid = el.name.replace('unlimited[','').replace(']','');
				$('rsepro_unlimited_'+ticketid).value = el.value;
			} else {
				ticketid = el.id.replace('ticket','');
				$('rsepro_seat_'+ticketid).addClass('rsepro_selected');
			}
		});
	});
	
	function rsepro_close() {
		<?php if ($modal) { ?>thedocument.rsepro_update_total();window.close();<?php } else { ?>window.parent.SqueezeBox.close();<?php } ?>
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
				<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/information.png" alt="" class="hasTip" title="<?php echo $ticket->description; ?>::" />
				<?php } ?>
			</div>
		</div>
		<div id="rsepro_ticket_seats<?php echo $ticket->id; ?>" class="rsepro_ticket_seats">
			<?php if (!$ticket->seats) { ?>
			<div class="rsepro_ticket_unlimited">
				<input type="text" id="rsepro_unlimited_<?php echo $ticket->id; ?>" name="tickets[<?php echo $ticket->id; ?>][]" value="" onchange="rsepro_add_ticket('<?php echo $ticket->id; ?>',0, '<?php echo $ticket->name; ?>', '<?php echo $price; ?>');" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');" class="input-mini rsepro_ticket_center" size="5" />
			</div>
			<?php } else { ?>
			<?php for($i=1; $i <= $ticket->seats; $i++) { ?>
			<?php $disabled = in_array($i,$selected); ?>
			<div class="rsepro_ticket_seat<?php echo $disabled ? ' rsepro_disabled' : ''; ?>" id="rsepro_seat_<?php echo $ticket->id.$i; ?>">
				<?php if ($disabled) { ?>
				<?php echo $i; ?>
				<?php } else { ?>
				<a href="javascript:void(0);" onclick="rsepro_add_ticket('<?php echo $ticket->id; ?>','<?php echo $i; ?>', '<?php echo $ticket->name; ?>', '<?php echo $price; ?>','<?php echo (int) $modal; ?>');"><?php echo $i; ?></a>
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
	<button type="button" class="btn btn-primary" onclick="rsepro_reset_tickets('<?php echo JText::_('COM_RSEVENTSPRO_SELECT_TICKETS'); ?>');"><?php echo JText::_('COM_RSEVENTSPRO_RESET'); ?></button>
</div>