<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_NEWCOUPON'); ?></legend>
	
	<?php $generate = RSEventsproAdapterGrid::inputGroup($this->dependencies->getInput('coupon_times'), '<button type="button" class="btn btn-primary rsepro-coupon-generate" data-id="">'.JText::_('COM_RSEVENTSPRO_GENERATE').'</button>',JText::_('COM_RSEVENTSPRO_COUPONS')); ?>
	<?php $coupon = RSEventsproAdapterGrid::inputGroup($this->dependencies->getInput('coupon_discount'), null, $this->dependencies->getInput('coupon_type').$this->dependencies->getInput('coupon_action')); ?>
	<?php echo $this->dependencies->renderField('coupon_name'); ?>
	<?php echo $this->dependencies->renderField('coupon_code'); ?>
	<?php echo RSEventsproAdapterGrid::renderField('', $generate); ?>
	<?php echo $this->dependencies->renderField('coupon_start'); ?>
	<?php echo $this->dependencies->renderField('coupon_end'); ?>
	<?php echo $this->dependencies->renderField('coupon_usage'); ?>
	<?php echo RSEventsproAdapterGrid::renderField($this->dependencies->getLabel('coupon_discount'), $coupon); ?>
	<?php echo $this->dependencies->renderField('coupon_groups'); ?>

	<div class="form-actions">
		<?php echo JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'rsepro-add-coupon-loader', 'style' => 'display: none;'), true); ?> 
		<button class="btn btn-primary rsepro-event-add-coupon" type="button"><span class="fa fa-plus"></span> <?php echo JText::_('COM_RSEVENTSPRO_ADD_COUPON'); ?></button>
		<button class="btn btn-success rsepro-event-save" type="button"><?php echo JText::_('COM_RSEVENTSPRO_SAVE_EVENT'); ?></button>
		<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
	</div>
</fieldset>