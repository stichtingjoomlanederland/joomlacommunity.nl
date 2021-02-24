<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_RECURRING_EVENT'); ?></legend>
	<div class="alert alert-info"><?php echo JText::sprintf('COM_RSEVENTSPRO_EVENT_RECURING_TIMES','<span id="rs_repeating_total">'.$this->eventClass->getChild().'</span>'); ?></div>
	
	<?php echo RSEventsproAdapterGrid::renderField($this->form->getLabel('repeat_interval'), RSEventsproAdapterGrid::inputGroup($this->form->getInput('repeat_interval'), null, $this->form->getInput('repeat_type'))); ?>
	<?php echo $this->form->renderField('repeat_end'); ?>
	<div id="rsepro-repeat-days"><?php echo $this->dependencies->renderField('repeat_days'); ?></div>
	<?php $append = $this->form->getInput('repeat_on_day').'<span id="repeat_on_day_order_container">'.$this->form->getInput('repeat_on_day_order').'</span><span id="repeat_on_day_type_container">'.$this->form->getInput('repeat_on_day_type').'</span>'; ?>
	<div id="rsepro-repeat-interval"><?php echo RSEventsproAdapterGrid::renderField($this->dependencies->getLabel('repeat_days'),RSEventsproAdapterGrid::inputGroup($this->form->getInput('repeat_on_type'), null, $append)); ?></div>
	<?php echo $this->dependencies->renderField('repeat_date'); ?>
	<?php echo RSEventsproAdapterGrid::renderField('',$this->form->getInput('repeat_also')); ?>
	<?php echo $this->dependencies->renderField('exclude_date'); ?>
	<?php echo RSEventsproAdapterGrid::renderField('',$this->form->getInput('exclude_dates')); ?>
	
	<?php echo $this->dependencies->renderField('apply_changes'); ?>
	
	<br>
	<div class="alert alert-info" id="apply_to_all_info"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_RECURING_INFO'); ?></div>
	<div class="alert alert-danger" id="apply_to_all"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_RECURING_WARNING'); ?></div>

	<?php if ($this->repeats) { ?>
	<div class="control-group">
		<div class="control-label">
			<label id="rsepro-recurring-events-show" class="btn">
				<strong><?php echo JText::_('COM_RSEVENTSPRO_EVENT_REPEATED_EVENTS'); ?></strong> 
				<i class="fa fa-arrow-down"></i>
			</label>
		</div>
		<div class="controls">
			<ul class="<?php echo RSEventsproAdapterGrid::styles(array('unstyled')); ?>" id="rsepro-recurring-events" style="display:none;">
				<?php foreach ($this->repeats as $event) { ?>
				<li>
					<i class="fa fa-calendar"></i> 
					<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=event.edit&id='.$event->id); ?>"><?php echo $event->name; ?></a> 
					<?php if ($event->allday) { ?>
					(<?php echo rseventsproHelper::showdate($event->start, $this->config->global_date); ?>)
					<?php } else { ?>
					(<?php echo rseventsproHelper::showdate($event->start); ?> - <?php echo rseventsproHelper::showdate($event->end); ?>)
					<?php } ?>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<?php } ?>
	
	<div class="form-actions">
		<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
	</div>
</fieldset>