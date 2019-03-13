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
	
	<?php echo JHtml::_('bootstrap.startTabSet', 'ticket'.$ticket->id, array('active' => 'general'.$ticket->id)); ?>
		
	<?php echo JHtml::_('bootstrap.addTab', 'ticket'.$ticket->id, 'general'.$ticket->id, JText::_('COM_RSEVENTSPRO_CONF_TAB_GENERAL')); ?>
		<div class="row-fluid">
		
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
					<input type="text" value="<?php echo $this->escape(rseventsproHelper::showprice($ticket->price)); ?>" class="span10" name="tickets[<?php echo $ticket->id; ?>][price]" id="ticket_price<?php echo $ticket->id; ?>" onkeyup="this.value=this.value.replace(/[^0-9\.\,]/g, '');" />
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
					<label for="tickets_<?php echo $ticket->id; ?>_from"><?php echo JText::_('COM_RSEVENTSPRO_TICKET_AVAILABLE_FROM'); ?></label>
				</div>
				<div class="controls">
					<?php echo JHTML::_('rseventspro.rscalendar', 'tickets['.$ticket->id.'][from]', $ticket->from); ?>
				</div>
			</div>
			
			<div class="control-group">
				<div class="control-label">
					<label for="tickets_<?php echo $ticket->id; ?>_to"><?php echo JText::_('COM_RSEVENTSPRO_TICKET_AVAILABLE_UNTIL'); ?></label>
				</div>
				<div class="controls">
					<?php echo JHTML::_('rseventspro.rscalendar', 'tickets['.$ticket->id.'][to]', $ticket->to); ?>
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
		</div>
	
	<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php JFactory::getApplication()->triggerEvent('rsepro_eventTicketFields', array(array('view' => &$this, 'id' => $ticket->id))); ?>
		
	<?php if (rseventsproHelper::pdf()) { ?>
	<?php echo JHtml::_('bootstrap.addTab', 'ticket'.$ticket->id, 'layout'.$ticket->id, JText::_('COM_RSEVENTSPRO_TICKET_PDF')); ?>
		<div class="control-group">
			<div class="control-label">
				<label for="tickets_<?php echo $ticket->id; ?>_attach"><?php echo JText::_('COM_RSEVENTSPRO_TICKET_PDF_ATTACH'); ?></label>
			</div>
			<div class="controls">
				<select name="tickets[<?php echo $ticket->id; ?>][attach]" id="tickets_<?php echo $ticket->id; ?>_attach" class="input-small">
					<?php echo JHtml::_('select.options', $this->eventClass->yesno(), 'value', 'text', $ticket->attach, true); ?>
				</select>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<?php echo JEditor::getInstance(JFactory::getConfig()->get('editor'))->display('tickets['.$ticket->id.'][layout]',$this->escape($ticket->layout),'100%', '50%', 70, 10); ?>
			</div>
		</div>
		<button type="button" onclick="window.open('<?php echo JRoute::_('index.php?option=com_rseventspro&layout=placeholders&type=pdf&tmpl=component', false); ?>', 'placeholdersWindow', 'toolbar=no, scrollbars=yes, resizable=yes, top=200, left=500, width=600, height=700');" class="btn btn-primary button"><?php echo JText::_('COM_RSEVENTSPRO_EMAIL_PLACEHOLDERS'); ?></button>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php } ?>
		
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	
	<div class="form-actions">
		<button class="btn btn-danger rsepro-remove-ticket" type="button" data-id="<?php echo $ticket->id; ?>"><span class="fa fa-times"></span> <?php echo JText::_('COM_RSEVENTSPRO_REMOVE_TICKET'); ?></button>
		<button class="btn btn-success rsepro-event-save" type="button"><?php echo JText::_('COM_RSEVENTSPRO_SAVE_EVENT'); ?></button>
		<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
	</div>
	
</div>
<!-- End Ticket '<?php echo $ticket->name;?>' tab -->
<?php if (JFactory::getApplication()->input->get('format') == 'raw') { ?>
<script type="text/javascript">
var tab = jQuery('<li class="active"><a href="#general<?php echo $ticket->id; ?>" data-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_CONF_TAB_GENERAL',true); ?></a></li>');
jQuery('#ticket<?php echo $ticket->id; ?>Tabs').append(tab);
<?php JFactory::getApplication()->triggerEvent('rsepro_eventTicketFieldsTab', array(array('id' => $ticket->id))); ?>
<?php if (rseventsproHelper::pdf()) { ?>
var tab = jQuery('<li class=""><a href="#layout<?php echo $ticket->id; ?>" data-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_TICKET_PDF',true); ?></a></li>');
jQuery('#ticket<?php echo $ticket->id; ?>Tabs').append(tab);
<?php } ?>
</script>
<?php } ?>
<?php }} ?>