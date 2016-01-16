<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_DISCOUNTS'); ?></legend>

<div class="control-group">
	<div class="controls">
		
		<label for="jform_early_fee">
			<?php echo JText::_('COM_RSEVENTSPRO_APPLY_DISCOUNT'); ?>
		</label>
		
		<input type="text" value="<?php echo $this->escape($this->item->early_fee); ?>" class="input-mini" name="jform[early_fee]" id="jform_early_fee" onkeyup="this.value=this.value.replace(/[^0-9\.\,]/g, '');" />
		
		<select class="input-mini" name="jform[early_fee_type]" id="jform_early_fee_type">
			<?php echo JHtml::_('select.options', $this->eventClass->getDiscountTypes(),'value','text',$this->item->early_fee_type); ?>
		</select>
		
		<label for="jform_early_fee_end">
			<?php echo JText::_('COM_RSEVENTSPRO_BOOKINGS_MADE_UNTIL'); ?>
		</label>
		
		<?php echo JHTML::_('rseventspro.rscalendar', 'jform[early_fee_end]', $this->item->early_fee_end); ?>
		
	</div>
</div>

<div class="control-group">
	<div class="controls">
		
		<label for="jform_late_fee">
			<?php echo JText::_('COM_RSEVENTSPRO_APPLY_FEE'); ?>
		</label>
		
		<input type="text" value="<?php echo $this->escape($this->item->late_fee); ?>" class="input-mini" name="jform[late_fee]" id="jform_late_fee" onkeyup="this.value=this.value.replace(/[^0-9\.\,]/g, '');" />
		
		<select class="input-mini" name="jform[late_fee_type]" id="jform_late_fee_type">
			<?php echo JHtml::_('select.options', $this->eventClass->getDiscountTypes(),'value','text',$this->item->late_fee_type); ?>
		</select>
		
		<label for="jform_late_fee_start">
			<?php echo JText::_('COM_RSEVENTSPRO_BOOKINGS_MADE_AFTER'); ?>
		</label>
		
		<?php echo JHTML::_('rseventspro.rscalendar', 'jform[late_fee_start]', $this->item->late_fee_start); ?>
		
	</div>
</div>

<div class="form-actions">
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
</div>