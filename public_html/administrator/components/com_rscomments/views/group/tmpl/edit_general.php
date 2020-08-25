<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); ?>

<div class="<?php echo RSCommentsAdapterGrid::row(); ?>">
	<div class="<?php echo RSCommentsAdapterGrid::column(12); ?>">
		<?php echo $this->form->renderField('GroupName'); ?>
		<?php echo $this->form->renderField('gid'); ?>
	</div>
</div>

<div class="<?php echo RSCommentsAdapterGrid::row(); ?>">
	<div class="<?php echo RSCommentsAdapterGrid::column(4); ?>">
		<fieldset class="options-form">
			<legend><?php echo JText::_($this->fieldsets['commenting']->label); ?></legend>
			<?php foreach ($this->form->getFieldset('commenting') as $field) { ?>
			<?php echo $field->renderField(); ?>
			<?php } ?>
		</fieldset>
	</div>
	<div class="<?php echo RSCommentsAdapterGrid::column(4); ?>">
		<fieldset class="options-form">
			<legend><?php echo JText::_($this->fieldsets['security']->label); ?></legend>
			<?php foreach ($this->form->getFieldset('security') as $field) { ?>
			<?php echo $field->renderField(); ?>
			<?php } ?>
		</fieldset>
	</div>
	<div class="<?php echo RSCommentsAdapterGrid::column(4); ?>">
		<fieldset class="options-form">
			<legend><?php echo JText::_($this->fieldsets['publishing']->label); ?></legend>
			<?php foreach ($this->form->getFieldset('publishing') as $field) { ?>
			<?php echo $field->renderField(); ?>
			<?php } ?>
		</fieldset>
	</div>
</div>