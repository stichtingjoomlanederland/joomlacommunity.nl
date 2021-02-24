<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=discount&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
			<?php $generate = '<button type="button" class="btn btn-secondary" onclick="rsepro_generate_string()">'.JText::_('COM_RSEVENTSPRO_DISCOUNT_GENERATE').'</button>'; ?>
			<?php echo $this->form->renderField('name'); ?>
			<?php echo RSEventsproAdapterGrid::renderField($this->form->getLabel('code'), RSEventsproAdapterGrid::inputGroup($this->form->getInput('code'), null, $generate)); ?>
			<?php echo $this->form->renderField('from'); ?>
			<?php echo $this->form->renderField('to'); ?>
			<?php echo $this->form->renderField('usage'); ?>
			<?php echo RSEventsproAdapterGrid::renderField($this->form->getLabel('value'), RSEventsproAdapterGrid::inputGroup($this->form->getInput('value'), null, $this->form->getInput('type'))); ?>
		</div>
		<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
			<?php echo $this->form->renderField('apply_to'); ?>
			<?php echo $this->form->renderField('events'); ?>
			<?php echo $this->form->renderField('groups'); ?>
		</div>
	</div>
	
	<h3><?php echo JText::_('COM_RSEVENTSPRO_DISCOUNT_OPTIONS'); ?></h3>
	<table id="rseproDiscountTable" class="table">
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
				<?php $text = rseventsproHelper::isCart('1.1.9') ? 'COM_RSEVENTSPRO_CART_APPLY_CART' : 'COM_RSEVENTSPRO_DISCOUNT_NUMBER'; ?>
				<input type="radio" name="jform[discounttype]" value="2" id="jform_discounttype_2" <?php echo $this->item->discounttype == 2 ? 'checked="checked"' : ''; ?> /> 
				<label for="jform_discounttype_2" class="inline radio"><?php echo JText::_($text.'_1'); ?></label> 
				<?php echo $this->form->getInput('cart_tickets'); ?>
				<label for="jform_discounttype_2" class="inline radio"><?php echo JText::_($text.'_2'); ?></label>
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

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>