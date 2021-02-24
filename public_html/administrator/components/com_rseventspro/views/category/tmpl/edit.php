<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=category&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?> mb-3">
		<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
			<?php echo $this->form->renderField('title'); ?>
		</div>
		<div class="<?php echo RSEventsproAdapterGrid::column(6); ?>">
			<?php echo $this->form->renderField('alias'); ?>
		</div>
	</div>
	
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(9); ?>">
			<?php echo $this->form->getInput('description'); ?>
		</div>
		<div class="<?php echo RSEventsproAdapterGrid::column(3); ?>">
			<fieldset class="options-form">
				<legend><?php echo JText::_('COM_RSEVENTSPRO_CATEGORY_TAB_GENERAL'); ?></legend>
				<?php echo $this->form->renderField('parent_id'); ?>
				<?php echo $this->form->renderField('published'); ?>
				<?php echo $this->form->renderField('access'); ?>
				<?php echo $this->form->renderField('language'); ?>
				<?php echo $this->form->renderField('image','params'); ?>
				<?php echo $this->form->renderField('color','params'); ?>
			</fieldset>
			
			<fieldset class="options-form">
				<legend><?php echo JText::_('COM_RSEVENTSPRO_CATEGORY_TAB_METADATA'); ?></legend>
				<?php echo $this->form->renderField('metadesc'); ?>
				<?php echo $this->form->renderField('metakey'); ?>
				<?php  foreach ($this->form->getFieldset('jmetadata') as $field) echo $field->renderField(); ?>
			</fieldset>
		</div>
	</div>
		
	<div>
		<?php echo $this->form->getInput('id'); ?>
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_('form.token') . "\n"; ?>
	</div>
	
</form>