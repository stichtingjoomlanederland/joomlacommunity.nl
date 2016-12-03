<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

//keep session alive while editing
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive'); ?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'email.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=email&layout=edit&tmpl=component&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="rsfltrgt pull-right">
			<button type="button" onclick="window.open('<?php echo JRoute::_('index.php?option=com_rseventspro&view=placeholders&type=rule&tmpl=component', false); ?>', 'placeholdersWindow', 'toolbar=no, scrollbars=yes, resizable=yes, top=200, left=500, width=600, height=700');" class="btn btn-primary button"><?php echo JText::_('COM_RSEVENTSPRO_EMAIL_PLACEHOLDERS'); ?></button>
			<button type="button" onclick="Joomla.submitbutton('email.save');" class="btn btn-primary button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE_BTN'); ?></button>
			<button type="button" onclick="document.location = '<?php echo JRoute::_('index.php?option=com_rseventspro&view=emails&tmpl=component'); ?>'" class="btn button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
		</div>
		<div class="clr"></div>
		<div class="span11">
			<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('lang'), $this->form->getInput('lang')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('subject'), $this->form->getInput('subject')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('mode'), $this->form->getInput('mode')); ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
			
			<?php echo $this->form->getInput('message'); ?>
		</div>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="tmpl" value="component" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo $this->form->getInput('parent'); ?>
	<?php echo $this->form->getInput('type'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>