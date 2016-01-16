<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<script type="text/javascript">
	jQuery(document).ready(function(){
		if (jQuery(window).width() >= 768) {
			jQuery('#rsepro_wrapper').css('height', jQuery(window).height() - 100);
			jQuery('#rsepro_wrapper').droppable({tolerance: 'fit'});
			
			<?php foreach ($this->tickets as $ticket) { ?>
			jQuery('#draggable<?php echo $ticket->id; ?>').draggable({ 
				containment: '#rsepro_wrapper', 
				scroll: false,
				stack: '.draggable',
				revert: 'invalid',
				stop: function(event, ui) {
					if (jQuery(this).draggable('option', 'revert') == 'invalid') {
						jQuery('#left<?php echo $ticket->id; ?>').attr('value', ui.position.left);
						jQuery('#top<?php echo $ticket->id; ?>').attr('value', ui.position.top);
					}
					jQuery(this).draggable('option','revert','invalid');
				} 
			});
			
			jQuery('#draggable<?php echo $ticket->id; ?>').droppable({
				greedy: true,
				tolerance: 'touch',
				drop: function(event,ui){
					ui.draggable.draggable('option','revert',true);
				}
			});
			
			jQuery('#draggable<?php echo $ticket->id; ?>').resizable({
				minHeight: 90, 
				minWidth: 90,
				containment: 'parent',
				handles: 'e<?php if ($ticket->seats == 0) echo ',se'; ?>',
				stop: function(event, ui) {
					jQuery('#width<?php echo $ticket->id; ?>').attr('value', ui.size.width);
					jQuery('#height<?php echo $ticket->id; ?>').attr('value', ui.size.height);
				}
			});
			<?php } ?>
		} else {
			jQuery('.draggable').addClass('rsepro-draggable-off');
			jQuery('#rsepro_wrapper').css('height', 'auto');
			jQuery('#rsepro_wrapper').css('overflow', 'hidden');
		}
	});
</script>


<div id="rsepro_wrapper">
	<?php $left = 10; $top = 10; ?>
	<?php foreach ($this->tickets as $ticket) { ?>
	<?php $style = empty($ticket->position) ? 'top: '.$top.'px; left: '.$left.'px;' : rseventsproHelper::parseStyle($ticket->position); ?>
	<div id="draggable<?php echo $ticket->id; ?>" class="draggable ui-widget-content" style="<?php echo $style; ?>">
		<div class="rsepro_ticket_container">
			<div class="rsepro_ticket_name">
				<?php echo $ticket->name; ?> - <?php echo $ticket->price ? rseventsproHelper::currency($ticket->price) : JText::_('COM_RSEVENTSPRO_GLOBAL_FREE'); ?>
			</div>
		</div>
		<div id="rsepro_ticket_seats<?php echo $ticket->id; ?>" class="rsepro_ticket_seats">
			<?php if (!$ticket->seats) { ?>
			<div class="rsepro_ticket_unlimited"><?php echo JText::_('COM_RSEVENTSPRO_UNLIMITED_TICKETS'); ?></div>
			<?php } else { ?>
			<?php for($i=1; $i <= $ticket->seats; $i++) { ?>
			<div class="rsepro_ticket_seat"><?php echo $i; ?></div>
			<?php } ?>
			<?php } ?>
		</div>
	</div>
	<?php $left += 270; ?>
	<?php } ?>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro'); ?>" method="post" id="adminForm" name="adminForm">
	<div style="text-align: center;">
		<button type="submit" class="btn btn-primary"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE_BTN'); ?></button>
		<button type="button" class="btn" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
	</div>
	
	<input type="hidden" name="task" value="event.tickets" />
	<?php foreach ($this->tickets as $ticket) { ?>
	<input type="hidden" value="<?php echo @$ticket->position['left']; ?>" id="left<?php echo $ticket->id; ?>" name="params[<?php echo $ticket->id; ?>][left]" />
	<input type="hidden" value="<?php echo @$ticket->position['top']; ?>" id="top<?php echo $ticket->id; ?>" name="params[<?php echo $ticket->id; ?>][top]" />
	<input type="hidden" value="<?php echo @$ticket->position['width']; ?>" id="width<?php echo $ticket->id; ?>" name="params[<?php echo $ticket->id; ?>][width]" />
	<?php if (!$ticket->seats) { ?><input type="hidden" value="<?php echo @$ticket->position['height']; ?>" id="height<?php echo $ticket->id; ?>" name="params[<?php echo $ticket->id; ?>][height]" /><?php } ?>
	<?php } ?>
</form>