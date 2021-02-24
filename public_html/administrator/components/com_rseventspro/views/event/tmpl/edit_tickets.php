<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php $tab = rseventsproHelper::isJ4() ? 'uitab' : 'bootstrap'; ?>
<?php if ($this->tickets) { ?>
<?php foreach ($this->tickets as $ticket) { ?>

<div class="tab-pane" id="rsepro-edit-ticket<?php echo $ticket->id; ?>">
	
	<fieldset class="options-form">
		<legend><?php echo $ticket->name; ?></legend>
		
		<?php echo JHtml::_($tab.'.startTabSet', 'ticket'.$ticket->id, array('active' => 'general'.$ticket->id)); ?>
		
		<?php echo JHtml::_($tab.'.addTab', 'ticket'.$ticket->id, 'general'.$ticket->id, JText::_('COM_RSEVENTSPRO_CONF_TAB_GENERAL')); ?>
			
			<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
				<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
					<?php echo $this->ticketsform[$ticket->id]->renderField('tickets['.$ticket->id.'][name]'); ?>
				</div>
				<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
					<?php echo $this->ticketsform[$ticket->id]->renderField('tickets['.$ticket->id.'][price]'); ?>
				</div>
			</div>

			<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
				<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
					<?php echo $this->ticketsform[$ticket->id]->renderField('tickets['.$ticket->id.'][seats]'); ?>
				</div>
				<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
					<?php echo $this->ticketsform[$ticket->id]->renderField('tickets['.$ticket->id.'][user_seats]'); ?>
				</div>
			</div>
			
			<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
				<div class="<?php echo RSEventsproAdapterGrid::column(12); ?>">
					<?php echo $this->ticketsform[$ticket->id]->renderField('tickets['.$ticket->id.'][groups]'); ?>
				</div>
			</div>

			<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
				<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
					<?php echo $this->ticketsform[$ticket->id]->renderField('tickets['.$ticket->id.'][from]'); ?>
				</div>
				
				<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
					<?php echo $this->ticketsform[$ticket->id]->renderField('tickets['.$ticket->id.'][to]'); ?>
				</div>
			</div>

			<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
				<div class="<?php echo RSEventsproAdapterGrid::column(12); ?>">
					<?php echo $this->ticketsform[$ticket->id]->renderField('tickets['.$ticket->id.'][description]'); ?>
				</div>
			</div>
		<?php echo JHtml::_($tab.'.endTab'); ?>
		
		<?php JFactory::getApplication()->triggerEvent('onrsepro_eventTicketFields', array(array('view' => &$this, 'id' => $ticket->id, 'ticket' => $ticket))); ?>
		
		<?php if (rseventsproHelper::pdf()) { ?>
		<?php echo JHtml::_($tab.'.addTab', 'ticket'.$ticket->id, 'layout'.$ticket->id, JText::_('COM_RSEVENTSPRO_TICKET_PDF')); ?>
		<?php echo $this->ticketsform[$ticket->id]->renderField('tickets['.$ticket->id.'][attach]'); ?>
		<?php echo $this->ticketsform[$ticket->id]->getInput('tickets['.$ticket->id.'][layout]'); ?>
		<button type="button" onclick="window.open('<?php echo JRoute::_('index.php?option=com_rseventspro&view=placeholders&type=pdf&tmpl=component', false); ?>', 'placeholdersWindow', 'toolbar=no, scrollbars=yes, resizable=yes, top=200, left=500, width=600, height=700');" class="btn btn-primary button"><?php echo JText::_('COM_RSEVENTSPRO_EMAIL_PLACEHOLDERS'); ?></button>
		<?php echo JHtml::_($tab.'.endTab'); ?>
		<?php } ?>
		
		<?php echo JHtml::_($tab.'.endTabSet'); ?>
		
		<div class="form-actions">
			<button class="btn btn-danger rsepro-remove-ticket" type="button" data-id="<?php echo $ticket->id; ?>"><span class="fa fa-times"></span> <?php echo JText::_('COM_RSEVENTSPRO_REMOVE_TICKET'); ?></button>
			<button class="btn btn-primary rsepro-ticket-duplicate" type="button" data-id="<?php echo $ticket->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_DUPLICATE_TICKET'); ?></button>
			<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
			<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
		</div>
	</fieldset>
</div>

<?php if (JFactory::getApplication()->input->get('format') == 'raw' && !rseventsproHelper::isJ4()) { ?>
<script type="text/javascript">
var tab = jQuery('<li class="active"><a href="#general<?php echo $ticket->id; ?>" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_CONF_TAB_GENERAL',true); ?></a></li>');
jQuery('#ticket<?php echo $ticket->id; ?>Tabs').append(tab);
<?php JFactory::getApplication()->triggerEvent('onrsepro_eventTicketFieldsTab', array(array('id' => $ticket->id))); ?>
<?php if (rseventsproHelper::pdf()) { ?>
var tab = jQuery('<li class=""><a href="#layout<?php echo $ticket->id; ?>" data-toggle="tab" data-bs-toggle="tab"><?php echo JText::_('COM_RSEVENTSPRO_TICKET_PDF',true); ?></a></li>');
jQuery('#ticket<?php echo $ticket->id; ?>Tabs').append(tab);
<?php } ?>
</script>
<?php } ?>
<?php }} ?>