<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive'); ?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'discount.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=discount&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span6 rsfltlft rswidth-50">
			<?php $generate = '<button type="button" class="btn" onclick="rsepro_generate_string()">'.JText::_('COM_RSEVENTSPRO_DISCOUNT_GENERATE').'</button>'; ?>
			<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('name'), $this->form->getInput('name')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('code'), $this->form->getInput('code').' '.$generate); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('from'), $this->form->getInput('from')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('to'), $this->form->getInput('to')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('usage'), $this->form->getInput('usage')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('value'), $this->form->getInput('value').' '.$this->form->getInput('type')); ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
		</div>
		<div class="span6 rsfltlft rswidth-50">
			<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('apply_to'), $this->form->getInput('apply_to')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('events'), $this->form->getInput('events'),'events'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('groups'), $this->form->getInput('groups')); ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
		</div>
	</div>
	<div class="clr"></div>
	<div class="row-fluid">
		<h3><?php echo JText::_('COM_RSEVENTSPRO_DISCOUNT_OPTIONS'); ?></h3>
		<table class="table adminlist">
			<tr>
				<td>
					<input type="radio" name="jform[discounttype]" value="0" id="jform_discounttype_0" <?php echo $this->item->discounttype == 0 ? 'checked="checked"' : ''; ?> /> 
					<label for="jform_discounttype_0" class="inline radio"><?php echo JText::_('COM_RSEVENTSPRO_DISCOUNT_APPLY_SAME_1'); ?></label> 
					<?php echo $this->form->getInput('same_tickets'); ?>
					<label for="jform_discounttype_0" class="inline radio"><?php echo JText::_('COM_RSEVENTSPRO_DISCOUNT_APPLY_SAME_2'); ?></label>
				</td>
			</tr>
			<tr>
				<td>
					<input type="radio" name="jform[discounttype]" value="1" id="jform_discounttype_1" <?php echo $this->item->discounttype == 1 ? 'checked="checked"' : ''; ?> /> 
					<label for="jform_discounttype_1" class="inline radio"><?php echo JText::_('COM_RSEVENTSPRO_DISCOUNT_APPLY_DIFFERENT_1'); ?></label> 
					<?php echo $this->form->getInput('different_tickets'); ?>
					<label for="jform_discounttype_1" class="inline radio"><?php echo JText::_('COM_RSEVENTSPRO_DISCOUNT_APPLY_DIFFERENT_2'); ?></label>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $this->form->getInput('total'); ?>
					<label for="jform_total" class="inline checkbox"><?php echo JText::_('COM_RSEVENTSPRO_DISCOUNT_TOTAL_GREATER'); ?></label> 
					<?php echo $this->form->getInput('totalvalue'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $this->form->getInput('payment'); ?>
					<label for="jform_payment" class="inline checkbox"><?php echo JText::_('COM_RSEVENTSPRO_DISCOUNT_PAYMENT'); ?></label> 
					<?php echo $this->form->getInput('paymentvalue'); ?>
				</td>
			</tr>
		</table>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>
<script type="text/javascript">rsepro_discount_assignment();</script>