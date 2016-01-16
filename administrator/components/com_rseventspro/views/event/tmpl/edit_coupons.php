<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php if ($this->coupons) { ?>
<?php foreach ($this->coupons as $coupon) { ?>

<!-- Start Coupon '<?php echo $coupon->name; ?>' tab -->
<div class="tab-pane" id="rsepro-edit-coupon<?php echo $coupon->id; ?>">
	
	<legend><?php echo $coupon->name; ?></legend>
	
	<div class="control-group">
		<div class="control-label">
			<label for="coupon_name<?php echo $coupon->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_COUPON_NAME'); ?></label>
		</div>
		<div class="controls">
			<input type="text" value="<?php echo $this->escape($coupon->name); ?>" class="span10" name="coupons[<?php echo $coupon->id; ?>][name]" id="coupon_name<?php echo $coupon->id; ?>" />
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label for="coupon_code<?php echo $coupon->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_COUPON_CODE'); ?></label>
		</div>
		<div class="controls">
			<textarea rows="3" class="span2" name="coupons[<?php echo $coupon->id; ?>][code]" id="coupon_code<?php echo $coupon->id; ?>"><?php echo $coupon->code; ?></textarea>
			
			<div class="input-prepend input-append">
				<button type="button" class="btn rsepro-coupon-generate" data-id="<?php echo $coupon->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_GENERATE'); ?></button>
				<input type="text" value="3" class="input-mini center" name="coupon_times" id="coupon_times<?php echo $coupon->id; ?>" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" />
				<span class="add-on"><?php echo JText::_('COM_RSEVENTSPRO_COUPONS'); ?></span>
			</div>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label><?php echo JText::_('COM_RSEVENTSPRO_COUPON_AVAILABILITY'); ?></label>
		</div>
		<div class="controls">
			<?php echo JHTML::_('rseventspro.rscalendar', 'coupons['.$coupon->id.'][from]', $coupon->from); ?> 
			<?php echo JText::_('COM_RSEVENTSPRO_TO_LOWERCASE'); ?> 
			<?php echo JHTML::_('rseventspro.rscalendar', 'coupons['.$coupon->id.'][to]', $coupon->to); ?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label for="coupon_usage<?php echo $coupon->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_MAX_USAGE'); ?></label>
		</div>
		<div class="controls">
			<input type="text"  value="<?php echo empty($coupon->usage) ? JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED') : $this->escape($coupon->usage); ?>" onfocus="if (this.value=='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>') this.value=''" onblur="if (this.value=='') this.value='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>'" class="span10" name="coupons[<?php echo $coupon->id; ?>][usage]" id="coupon_usage<?php echo $coupon->id; ?>" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" />
		</div>
	</div>

	<div class="control-group">
		<label class="rsepro-inline" for="coupon_discount<?php echo $coupon->id; ?>">
			<?php echo JText::_('COM_RSEVENTSPRO_APPLY_DISCOUNT'); ?>
		</label>
		
		<input type="text" value="<?php echo $this->escape($coupon->discount); ?>" class="input-mini" name="coupons[<?php echo $coupon->id; ?>][discount]" id="coupon_discount<?php echo $coupon->id; ?>" />
		
		<select class="input-mini" name="coupons[<?php echo $coupon->id; ?>][type]" id="coupon_type<?php echo $coupon->id; ?>">
			<?php echo JHtml::_('select.options', $this->eventClass->getDiscountTypes(), 'value', 'text', $coupon->type); ?>
		</select>
		
		<select class="input-medium" name="coupons[<?php echo $coupon->id; ?>][action]" id="coupon_action<?php echo $coupon->id; ?>">
			<?php echo JHtml::_('select.options', $this->eventClass->getDiscountActions(), 'value', 'text', $coupon->action); ?>
		</select>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label for="coupon_groups<?php echo $coupon->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_INSTANT_DISCOUNT'); ?></label>
		</div>
		<div class="controls">
			<select class="rsepro-chosen" name="coupons[<?php echo $coupon->id; ?>][groups][]" id="coupon_groups<?php echo $coupon->id; ?>" multiple="multiple">
				<?php echo JHtml::_('select.options', $this->eventClass->groups(),'value','text', $coupon->groups); ?>
			</select>
		</div>
	</div>

	<div class="form-actions">
		<button class="btn btn-danger rsepro-event-remove-coupon" type="button" data-id="<?php echo $coupon->id; ?>"><span class="fa fa-times"></span> <?php echo JText::_('COM_RSEVENTSPRO_REMOVE_COUPON'); ?></button>
		<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
	</div>
		
</div>
<!-- End Coupon '<?php echo $coupon->name; ?>' tab -->
<?php }} ?>