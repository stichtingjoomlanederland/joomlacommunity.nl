<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_DISCOUNTS'); ?></legend>
	
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(12); ?>">
			<?php echo RSEventsproAdapterGrid::renderField($this->form->getLabel('early_fee'), RSEventsproAdapterGrid::inputGroup($this->form->getInput('early_fee'), null, $this->form->getInput('early_fee_type'))); ?>
			<?php echo $this->form->renderField('early_fee_end'); ?>
			<?php echo RSEventsproAdapterGrid::renderField($this->form->getLabel('late_fee'), RSEventsproAdapterGrid::inputGroup($this->form->getInput('late_fee'), null, $this->form->getInput('late_fee_type'))); ?>
			<?php echo $this->form->renderField('late_fee_start'); ?>
		</div>
	</div>
	
	<div class="form-actions">
		<button class="btn btn-success rsepro-event-save" type="button"><?php echo JText::_('COM_RSEVENTSPRO_SAVE_EVENT'); ?></button>
		<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
	</div>
</fieldset>