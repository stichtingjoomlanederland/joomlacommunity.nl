<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.keepalive'); ?>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#rsepro_wrapper').css('height', jQuery(window).height() - 100);
});
</script>

<div id="rsepro_wrapper">
	<?php $left = 10; $top = 10; ?>
	<?php foreach ($this->tickets as $ticket) { ?>
	<?php $style = empty($ticket->position) ? 'top: '.$top.'px; left: '.$left.'px;' : rseventsproHelper::parseStyle($ticket->position); ?>
	<?php $price = $ticket->price ? rseventsproHelper::currency($ticket->price) : JText::_('COM_RSEVENTSPRO_GLOBAL_FREE'); ?>
	<?php $selected = rseventsproHelper::getSelectedSeats($ticket->id,$this->id); ?>
	<?php $disabled = rseventsproHelper::getSelectedSeats($ticket->id); ?>
	<div id="draggable<?php echo $ticket->id; ?>" class="draggable rsepro_front ui-widget-content" style="<?php echo $style; ?>">
		<div class="rsepro_ticket_container">
			<div class="rsepro_ticket_name">
				<?php echo $ticket->name; ?> - 
				<?php echo $price; ?>
			</div>
		</div>
		<div id="rsepro_ticket_seats<?php echo $ticket->id; ?>" class="rsepro_ticket_seats">
			<?php if (!$ticket->seats) { ?>
			<div class="rsepro_ticket_unlimited">
				<?php echo rseventsproHelper::getTotalUnlimited($ticket->id,$this->id); ?> <?php echo JText::_('COM_RSEVENTSPRO_UNLIMITED_TICKETS'); ?>
			</div>
			<?php } else { ?>
			<?php for($i=1; $i <= $ticket->seats; $i++) { ?>
			<?php 
				$class = '';
				if (in_array($i,$disabled)) $class .= ' rsepro_disabled';
				if (in_array($i,$selected)) $class .= ' rsepro_user_selected';
			?>
			<div class="rsepro_ticket_seat<?php echo $class; ?>" id="rsepro_seat_<?php echo $ticket->id.$i; ?>">
				<?php echo $i; ?>
			</div>
			<?php } ?>
			<?php } ?>
		</div>
	</div>
	<?php $left += 270; ?>
	<?php } ?>
	<div class="rsepro_legend">
		<div class="rsepro_ticket_seat rsepro_user_selected">&nbsp;</div> &nbsp; <?php echo JText::_('COM_RSEVENTSPRO_USER_SELECTED_SEATS'); ?>
		<div class="rsepro_clear"></div>
		<div class="rsepro_ticket_seat rsepro_disabled">&nbsp;</div> &nbsp; <?php echo JText::_('COM_RSEVENTSPRO_DISABLED_SEATS'); ?>
	</div>
</div>