<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<legend><?php echo JText::_('COM_RSEVENTSPRO_RECURRING_EVENT'); ?></legend>

<div class="alert alert-info">
	<?php echo JText::sprintf('COM_RSEVENTSPRO_EVENT_RECURING_TIMES','<span id="rs_repeating_total">'.$this->eventClass->getChild().'</span>'); ?>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_repeat_interval"><?php echo JText::_('COM_RSEVENTSPRO_REAPEAT_EVERY'); ?></label>
	</div>
	<div class="controls">
		<input type="text" value="<?php echo $this->escape($this->item->repeat_interval); ?>" id="jform_repeat_interval" name="jform[repeat_interval]" class="input-mini" size="3" /> 
		<select class="input-small" name="jform[repeat_type]" id="jform_repeat_type">
			<?php echo JHtml::_('select.options', $this->eventClass->repeatType(),'value','text',$this->item->repeat_type); ?>
		</select>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_repeat_end"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_END_REPEAT'); ?></label>
	</div>
	<div class="controls">
		<?php echo JHTML::_('rseventspro.rscalendar', 'jform[repeat_end]', $this->item->repeat_end, true, false, 'RSEventsPro.Event.repeats()'); ?>
	</div>
</div>

<div class="control-group" id="rsepro-repeat-days">
	<div class="control-label">
		<label for="repeat_days"><?php echo JText::_('COM_RSEVENTSPRO_REAPEAT_ON'); ?></label>
	</div>
	<div class="controls">
		<select class="rsepro-chosen" name="repeat_days[]" id="repeat_days" multiple="multiple">
			<?php echo JHtml::_('select.options', $this->eventClass->repeatDays(), 'value','text',$this->eventClass->repeatEventDays()); ?>
		</select>
	</div>
</div>

<div class="control-group" id="rsepro-repeat-interval">
	<div class="control-label">
		<label for="jform_repeat_on_type"><?php echo JText::_('COM_RSEVENTSPRO_REAPEAT_ON'); ?></label>
	</div>
	<div class="controls">
		<select class="input-large" name="jform[repeat_on_type]" id="jform_repeat_on_type" size="1">
			<?php echo JHtml::_('select.options', $this->eventClass->repeatOn(), 'value','text', $this->item->repeat_on_type); ?>
		</select>
		
		<?php $repeat_on_day = empty($this->item->repeat_on_day) ? rseventsproHelper::showdate($this->item->start,'d') : $this->item->repeat_on_day; ?>
		<input type="text" name="jform[repeat_on_day]" id="jform_repeat_on_day" value="<?php echo (int) $repeat_on_day; ?>" class="input-mini center" size="3" />
		
		<span id="repeat_on_day_order_container">
			<select class="input-small" name="jform[repeat_on_day_order]" id="jform_repeat_on_day_order" size="1">
				<?php echo JHtml::_('select.options', $this->eventClass->repeatOnOrder(), 'value','text', $this->item->repeat_on_day_order); ?>
			</select>
		</span>
		<span id="repeat_on_day_type_container">
			<select class="input-medium" name="jform[repeat_on_day_type]" id="jform_repeat_on_day_type" size="1">
				<?php echo JHtml::_('select.options', $this->eventClass->repeatDays(), 'value','text', $this->item->repeat_on_day_type); ?>
			</select>
		</span>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_repeatalso"><?php echo JText::_('COM_RSEVENTSPRO_REAPEAT_ALSO_ON'); ?></label>
	</div>
	<div class="controls">
		<?php echo JHTML::_('rseventspro.rscalendar', 'repeat_date', '', true, false, 'RSEventsPro.Event.addRecurringDate()'); ?>
		<br />
		<select name="jform[repeat_also][]" id="jform_repeatalso" multiple="multiple">
			<?php echo JHtml::_('select.options', $this->eventClass->repeatAlso()); ?>
		</select>
		<br />
		<button type="button" class="btn" id="rsepro-remove-repeat-dates"><?php echo JText::_('COM_RSEVENTSPRO_REMOVE_SELECTED'); ?></button>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_excludedates"><?php echo JText::_('COM_RSEVENTSPRO_REAPEAT_EXCLUDE_DATES'); ?></label>
	</div>
	<div class="controls">
		<?php echo JHTML::_('rseventspro.rscalendar', 'exclude_date', '', true, false, 'RSEventsPro.Event.addExcludeDate()'); ?>
		<br />
		<select name="jform[exclude_dates][]" id="jform_excludedates" multiple="multiple">
			<?php echo JHtml::_('select.options', $this->eventClass->excludeDates()); ?>
		</select>
		<br />
		<button type="button" class="btn" id="rsepro-remove-exclude-dates"><?php echo JText::_('COM_RSEVENTSPRO_REMOVE_SELECTED'); ?></button>
	</div>
</div>

<div class="control-group">
	<label class="checkbox">
		<input id="apply_changes" name="apply_changes" type="checkbox" value="1" /> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_APPLY_CHANGES'); ?>
	</label>
</div>

<div class="alert alert-info" id="apply_to_all_info">
	<?php echo JText::_('COM_RSEVENTSPRO_EVENT_RECURING_INFO'); ?>
</div>

<?php if ($this->repeats) { ?>
<div class="control-group">
	<div class="control-label">
		<label id="rsepro-recurring-events-show" class="btn">
			<strong><?php echo JText::_('COM_RSEVENTSPRO_EVENT_REPEATED_EVENTS'); ?></strong> 
			<i class="fa fa-arrow-down"></i>
		</label>
	</div>
	<div class="controls">
		<ul class="unstyled" id="rsepro-recurring-events" style="display:none;">
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