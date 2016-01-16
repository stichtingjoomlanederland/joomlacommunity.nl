<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_NEWCOUPON'); ?></legend>

<div class="control-group">
	<div class="control-label">
		<label for="coupon_name"><?php echo JText::_('COM_RSEVENTSPRO_COUPON_NAME'); ?></label>
	</div>
	<div class="controls">
		<input type="text" value="" class="span10" name="coupon_name" id="coupon_name" />
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="coupon_code"><?php echo JText::_('COM_RSEVENTSPRO_COUPON_CODE'); ?></label>
	</div>
	<div class="controls">
		<textarea rows="3" class="span2" name="coupon_code" id="coupon_code"></textarea>
		
		<div class="input-prepend input-append">
			<button type="button" class="btn rsepro-coupon-generate" data-id=""><?php echo JText::_('COM_RSEVENTSPRO_GENERATE'); ?></button>
			<input type="text" value="3" class="input-mini center" name="coupon_times" id="coupon_times" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" />
			<span class="add-on"><?php echo JText::_('COM_RSEVENTSPRO_COUPONS'); ?></span>
		</div>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label><?php echo JText::_('COM_RSEVENTSPRO_COUPON_AVAILABILITY'); ?></label>
	</div>
	<div class="controls">
		<?php echo JHTML::_('rseventspro.rscalendar', 'coupon_start'); ?> 
		<?php echo JText::_('COM_RSEVENTSPRO_TO_LOWERCASE'); ?> 
		<?php echo JHTML::_('rseventspro.rscalendar', 'coupon_end'); ?>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="coupon_usage"><?php echo JText::_('COM_RSEVENTSPRO_MAX_USAGE'); ?></label>
	</div>
	<div class="controls">
		<input type="text"  value="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED'); ?>" onfocus="if (this.value=='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>') this.value=''" onblur="if (this.value=='') this.value='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>'" class="span10" name="coupon_usage" id="coupon_usage" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" />
	</div>
</div>

<div class="control-group">
	<label class="rsepro-inline" for="coupon_discount">
		<?php echo JText::_('COM_RSEVENTSPRO_APPLY_DISCOUNT'); ?>
	</label>
	
	<input type="text" value="" class="input-mini" name="coupon_discount" id="coupon_discount" />
	
	<select class="input-mini" name="coupon_type" id="coupon_type">
		<?php echo JHtml::_('select.options', $this->eventClass->getDiscountTypes()); ?>
	</select>
	
	<select class="input-medium" name="coupon_action" id="coupon_action">
		<?php echo JHtml::_('select.options', $this->eventClass->getDiscountActions()); ?>
	</select>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="coupon_groups"><?php echo JText::_('COM_RSEVENTSPRO_INSTANT_DISCOUNT'); ?></label>
	</div>
	<div class="controls">
		<select class="rsepro-chosen" name="coupon_groups[]" id="coupon_groups" multiple="multiple">
			<?php echo JHtml::_('select.options', $this->eventClass->groups()); ?>
		</select>
	</div>
</div>

<div class="form-actions">
	<img id="rsepro-add-coupon-loader" src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loader.gif" alt="" style="display: none;" />	
	<button class="btn rsepro-event-add-coupon" type="button"><span class="fa fa-plus"></span> <?php echo JText::_('COM_RSEVENTSPRO_ADD_COUPON'); ?></button>
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
</div>