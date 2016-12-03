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
		if (task == 'payment.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=payment&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span8">
			<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('published'), $this->form->getInput('published')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('name'), $this->form->getInput('name')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('redirect'), $this->form->getInput('redirect')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('tax_type'), $this->form->getInput('tax_type')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('tax_value'), $this->form->getInput('tax_value')); ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('details'); ?>
		</div>
		<div class="span4">
			<?php if ($this->placeholders) { ?>
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_EMAIL_PLACEHOLDERS'); ?></legend>
				<table class="table table-striped table-condensed" id="placeholdersTable">
				<?php foreach ($this->placeholders as $placeholder => $description) { ?>
				<tr>
					<td class="rsepro-placeholder"><?php echo $placeholder; ?></td>
					<td><?php echo JText::_($description); ?></td>
				</tr>
				<?php } ?>
				</table>
			</fieldset>
			<?php } ?>
		</div>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>