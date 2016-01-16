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
		if (task == 'category.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=category&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	
	<div class="form-inline form-inline-header">
		<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
		<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('title'), $this->form->getInput('title')); ?>
		<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('alias'), $this->form->getInput('alias')); ?>
		<?php echo JHtml::_('rsfieldset.end'); ?>
	</div>
	
	<div class="row-fluid">
		<div class="span9 rswidth-50 rsfltlft">
			<?php echo $this->form->getLabel('description'); ?>
			<?php echo $this->form->getInput('description'); ?>
		</div>
		<div class="span3 rsfltrgt rswidth-50">
			<?php echo JHtml::_('rsfieldset.start', 'adminform form-vertical', JText::_('COM_RSEVENTSPRO_CATEGORY_TAB_GENERAL')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('parent_id'), $this->form->getInput('parent_id')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('published'), $this->form->getInput('published')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('access'), $this->form->getInput('access')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('language'), $this->form->getInput('language')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('image','params'), $this->form->getInput('image','params')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('color','params'), $this->form->getInput('color','params')); ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
			
			<?php echo JHtml::_('rsfieldset.start', 'adminform form-vertical',JText::_('COM_RSEVENTSPRO_CATEGORY_TAB_METADATA')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('metadesc'), $this->form->getInput('metadesc')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('metakey'), $this->form->getInput('metakey')); ?>
			<?php foreach ($this->form->getFieldset('jmetadata') as $field) { ?>
			<?php echo JHtml::_('rsfieldset.element', $field->label, $field->input); ?>
			<?php } ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
		</div>
	</div>
		
	<div>
		<?php echo $this->form->getInput('id'); ?>
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_('form.token') . "\n"; ?>
	</div>
	
</form>