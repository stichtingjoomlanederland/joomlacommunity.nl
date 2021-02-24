<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

//keep session alive while editing
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=email&layout=edit&tmpl=component&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="<?php echo RSEventsproAdapterGrid::styles(array('pull-right')); ?>">
		<button type="button" onclick="window.open('<?php echo JRoute::_('index.php?option=com_rseventspro&view=placeholders&type=rule&tmpl=component', false); ?>', 'placeholdersWindow', 'toolbar=no, scrollbars=yes, resizable=yes, top=200, left=500, width=600, height=700');" class="btn btn-primary"><?php echo JText::_('COM_RSEVENTSPRO_EMAIL_PLACEHOLDERS'); ?></button>
		<button type="button" onclick="Joomla.submitbutton('email.save');" class="btn btn-primary"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE_BTN'); ?></button>
		<button type="button" onclick="document.location = '<?php echo JRoute::_('index.php?option=com_rseventspro&view=emails&tmpl=component'); ?>'" class="btn btn-danger"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
	</div>
	<div class="clearfix"></div>
	
	<div class="mt-3">
		<?php echo $this->form->renderField('lang'); ?>
		<?php echo $this->form->renderField('subject'); ?>
		<?php echo $this->form->renderField('mode'); ?>
		<?php echo $this->form->getInput('message'); ?>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="tmpl" value="component" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo $this->form->getInput('parent'); ?>
	<?php echo $this->form->getInput('type'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>