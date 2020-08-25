<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_INVOICE'); ?></legend>

<div class="control-group" id="rsepro-invoice-enable">
	<label class="checkbox">
		<?php $invoice = $this->item->invoice ? 'checked="checked"' : ''; ?>
		<input type="checkbox" id="jform_invoice" name="jform[invoice]" value="1" <?php echo $invoice; ?> /> <?php echo JText::_('COM_RSEVENTSPRO_ENABLE_INVOICE'); ?>
	</label>
</div>

<div class="control-group" id="rsepro-invoice-attach">
	<label class="checkbox">
		<?php $invoiceActivation = $this->item->invoice_attach ? 'checked="checked"' : ''; ?>
		<input type="checkbox" id="jform_invoice_attach" name="jform[invoice_attach]" value="1" <?php echo $invoiceActivation; ?> /> <?php echo JText::_('COM_RSEVENTSPRO_INVOICE_ATTACH'); ?>
	</label>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_invoice_type"><?php echo JText::_('COM_RSEVENTSPRO_INVOICE_TYPE'); ?></label>
	</div>
	<div class="controls">
		<select name="jform[invoice_type]" id="jform_invoice_type" class="span2">
			<option value="1" <?php echo $this->item->invoice_type == 1 ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_RSEVENTSPRO_INVOICE_TYPE_GLOBAL'); ?></option>
			<option value="2" <?php echo $this->item->invoice_type == 2 ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_RSEVENTSPRO_INVOICE_TYPE_CUSTOM'); ?></option>
		</select>
	</div>
</div>

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