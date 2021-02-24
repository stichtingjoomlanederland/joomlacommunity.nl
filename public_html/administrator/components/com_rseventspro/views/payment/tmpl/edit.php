<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=payment&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(8); ?>">
			<?php echo $this->form->renderField('published'); ?>
			<?php echo $this->form->renderField('name'); ?>
			<?php echo $this->form->renderField('redirect'); ?>
			<?php echo $this->form->renderField('tax_type'); ?>
			<?php echo $this->form->renderField('tax_value'); ?>
			<?php echo $this->form->getInput('details'); ?>
		</div>
		<div class="<?php echo RSEventsproAdapterGrid::column(4); ?>">
			<?php if ($this->placeholders) { ?>
			<fieldset class="options-form">
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