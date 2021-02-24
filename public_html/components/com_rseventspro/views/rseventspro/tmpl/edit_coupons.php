<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php if ($this->coupons) { ?>
<?php foreach ($this->coupons as $coupon) { ?>

<div class="tab-pane" id="rsepro-edit-coupon<?php echo $coupon->id; ?>">
	<fieldset class="options-form">
		<legend><?php echo $coupon->name; ?></legend>
		
		<?php $generate = RSEventsproAdapterGrid::inputGroup($this->couponsform[$coupon->id]->getInput('coupons['.$coupon->id.'][times]'), '<button type="button" class="btn btn-primary rsepro-coupon-generate" data-id="'.$coupon->id.'">'.JText::_('COM_RSEVENTSPRO_GENERATE').'</button>',JText::_('COM_RSEVENTSPRO_COUPONS')); ?>
		<?php $couponArea = RSEventsproAdapterGrid::inputGroup($this->couponsform[$coupon->id]->getInput('coupons['.$coupon->id.'][discount]'), null, $this->couponsform[$coupon->id]->getInput('coupons['.$coupon->id.'][type]').$this->couponsform[$coupon->id]->getInput('coupons['.$coupon->id.'][action]')); ?>
		<?php echo $this->couponsform[$coupon->id]->renderField('coupons['.$coupon->id.'][name]'); ?>
		<?php echo $this->couponsform[$coupon->id]->renderField('coupons['.$coupon->id.'][code]'); ?>
		<?php echo RSEventsproAdapterGrid::renderField('', $generate); ?>
		<?php echo $this->couponsform[$coupon->id]->renderField('coupons['.$coupon->id.'][from]'); ?>
		<?php echo $this->couponsform[$coupon->id]->renderField('coupons['.$coupon->id.'][to]'); ?>
		<?php echo $this->couponsform[$coupon->id]->renderField('coupons['.$coupon->id.'][usage]'); ?>
		<?php echo RSEventsproAdapterGrid::renderField($this->couponsform[$coupon->id]->getLabel('coupons['.$coupon->id.'][discount]'), $couponArea); ?>
		<?php echo $this->couponsform[$coupon->id]->renderField('coupons['.$coupon->id.'][groups]'); ?>

		<div class="form-actions">
			<button class="btn btn-danger rsepro-event-remove-coupon" type="button" data-id="<?php echo $coupon->id; ?>"><span class="fa fa-times"></span> <?php echo JText::_('COM_RSEVENTSPRO_REMOVE_COUPON'); ?></button>
			<button class="btn btn-success rsepro-event-save" type="button"><?php echo JText::_('COM_RSEVENTSPRO_SAVE_EVENT'); ?></button>
			<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
			<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
		</div>
	</fieldset>
</div>
<?php }} ?>