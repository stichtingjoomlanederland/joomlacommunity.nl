<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_INVOICE'); ?></legend>
	
	<?php echo $this->form->renderField('invoice'); ?>
	<?php echo $this->form->renderField('invoice_attach'); ?>
	<?php echo $this->form->renderField('invoice_type'); ?>

	<div id="rsepro-invoice-custom" style="display:none;">
		<?php echo $this->form->renderField('invoice_font'); ?>
		<?php echo $this->form->renderField('invoice_orientation'); ?>
		<?php echo $this->form->renderField('invoice_padding'); ?>
		<?php echo $this->form->renderField('invoice_prefix'); ?>
		<?php echo $this->form->renderField('invoice_title'); ?>
		<?php echo $this->form->renderField('invoice_layout'); ?>
		<div class="clearfix"></div>
		<div class="alert alert-info"><?php echo JText::_('COM_RSEVENTSPRO_INVOICE_PLACEHOLDERS'); ?></div>
	</div>

	<div class="form-actions">
		<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
	</div>
</fieldset>