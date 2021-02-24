<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">

	<div class="<?php echo RSEventsproAdapterGrid::column(4); ?>">
		<fieldset class="options-form">
			<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_OPTIONS_DETAIL'); ?></legend>
			
			<label class="checkbox">
				<input type="checkbox" name="check1" value="1" onclick="RSEventsPro.Event.checkAll(this, 'rsepro-block-1');" />
				<strong><?php echo JText::_('COM_RSEVENTSPRO_CHECK_ALL'); ?></strong>
			</label>
			
			<div id="rsepro-block-1" class="control-group">
				<?php echo $this->form->renderFieldset('options1'); ?>
			</div>
		</fieldset>
	</div>
	
	<div class="<?php echo RSEventsproAdapterGrid::column(4); ?>">
		<fieldset class="options-form">
			<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_OPTIONS_LISTINGS'); ?></legend>
			
			<label class="checkbox">
				<input type="checkbox" name="check1" value="1" onclick="RSEventsPro.Event.checkAll(this, 'rsepro-block-2');" />
				<strong><?php echo JText::_('COM_RSEVENTSPRO_CHECK_ALL'); ?></strong>
			</label>
			
			<div id="rsepro-block-2" class="control-group">
				<?php echo $this->form->renderFieldset('options2'); ?>
			</div>
		</fieldset>
	</div>
	
	<div class="<?php echo RSEventsproAdapterGrid::column(4); ?>">
		<fieldset class="options-form">
			<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_MESSAGES'); ?></legend>
			<?php echo $this->form->renderFieldset('options3'); ?>
		</fieldset>
	</div>
</div>

<div class="form-actions">
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
</div>