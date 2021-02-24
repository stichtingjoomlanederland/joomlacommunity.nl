<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_NEWTICKET'); ?></legend>
	
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
			<?php echo $this->dependencies->renderField('ticket_name'); ?>
		</div>
		<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
			<?php echo $this->dependencies->renderField('ticket_price'); ?>
		</div>
	</div>

	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
			<?php echo $this->dependencies->renderField('ticket_seats'); ?>
		</div>
		<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
			<?php echo $this->dependencies->renderField('ticket_user_seats'); ?>
		</div>
	</div>
	
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(12); ?>">
			<?php echo $this->dependencies->renderField('ticket_groups'); ?>
		</div>
	</div>

	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
			<?php echo $this->dependencies->renderField('ticket_from'); ?>
		</div>
		
		<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
			<?php echo $this->dependencies->renderField('ticket_to'); ?>
		</div>
	</div>

	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(12); ?>">
			<?php echo $this->dependencies->renderField('ticket_description'); ?>
		</div>
	</div>

	<div class="form-actions">
		<?php echo JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'rsepro-add-ticket-loader', 'style' => 'display: none;'), true); ?> 
		<button class="btn btn-success rsepro-add-ticket" type="button"><span class="fa fa-plus"></span> <?php echo JText::_('COM_RSEVENTSPRO_ADD_TICKET'); ?></button>
		<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
	</div>
</fieldset>