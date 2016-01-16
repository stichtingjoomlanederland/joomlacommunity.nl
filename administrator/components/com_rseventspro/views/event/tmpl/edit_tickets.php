<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php if ($this->tickets) { ?>
<?php foreach ($this->tickets as $ticket) { ?>

<!-- Start Ticket '<?php echo $ticket->name;?>' tab -->
<div class="tab-pane" id="rsepro-edit-ticket<?php echo $ticket->id; ?>">
	
	<legend><?php echo $ticket->name; ?></legend>
	
	<div class="control-group">
		<div class="control-label">
			<label for="ticket_name<?php echo $ticket->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_NAME'); ?></label>
		</div>
		<div class="controls">
			<input type="text" value="<?php echo $this->escape($ticket->name); ?>" class="span10" name="tickets[<?php echo $ticket->id; ?>][name]" id="ticket_name<?php echo $ticket->id; ?>" />
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label for="ticket_price<?php echo $ticket->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_PRICE'); ?></label>
		</div>
		<div class="controls">
			<input type="text" value="<?php echo $this->escape($ticket->price); ?>" class="span10" name="tickets[<?php echo $ticket->id; ?>][price]" id="ticket_price<?php echo $ticket->id; ?>" onkeyup="this.value=this.value.replace(/[^0-9\.\,]/g, '');" />
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label for="ticket_seats<?php echo $ticket->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_SEATS'); ?></label>
		</div>
		<div class="controls">
			<input type="text" value="<?php echo empty($ticket->seats) ? JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED') : $this->escape($ticket->seats); ?>" onfocus="if (this.value=='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>') this.value=''" onblur="if (this.value=='') this.value='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>'" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" class="span10" name="tickets[<?php echo $ticket->id; ?>][seats]" id="ticket_seats<?php echo $ticket->id; ?>" />
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label for="ticket_user_seats<?php echo $ticket->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_USER_SEATS'); ?></label>
		</div>
		<div class="controls">
			<input type="text"  value="<?php echo empty($ticket->user_seats) ? JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED') : $this->escape($ticket->user_seats); ?>" onfocus="if (this.value=='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>') this.value=''" onblur="if (this.value=='') this.value='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>'" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" class="span10" name="tickets[<?php echo $ticket->id; ?>][user_seats]" id="ticket_user_seats<?php echo $ticket->id; ?>" />
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label for="ticket_groups<?php echo $ticket->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_TICKET_GROUPS_INFO'); ?></label>
		</div>
		<div class="controls">
			<select class="rsepro-chosen" name="tickets[<?php echo $ticket->id; ?>][groups][]" id="ticket_groups<?php echo $ticket->id; ?>" multiple="multiple">
				<?php echo JHtml::_('select.options', $this->eventClass->groups(),'value','text', $ticket->groups); ?>
			</select>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label for="ticket_description<?php echo $ticket->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_DESCRIPTION'); ?></label>
		</div>
		<div class="controls">
			<textarea class="span10" name="tickets[<?php echo $ticket->id; ?>][description]" id="ticket_description<?php echo $ticket->id; ?>" rows="5"><?php echo $ticket->description; ?></textarea>
		</div>
	</div>
	
	<div class="form-actions">
		<button class="btn btn-danger rsepro-remove-ticket" type="button" data-id="<?php echo $ticket->id; ?>"><span class="fa fa-times"></span> <?php echo JText::_('COM_RSEVENTSPRO_REMOVE_TICKET'); ?></button>
		<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
	</div>
	
</div>
<!-- End Ticket '<?php echo $ticket->name;?>' tab -->

<?php }} ?>