<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<fieldset class="options-form">
	<legend><?php echo JText::_($this->fieldsets['yahoo']->label); ?></legend>
	<div class="alert alert-info"><?php echo JText::_($this->fieldsets['yahoo']->description); ?></div>
	<?php echo $this->form->renderFieldset('yahoo'); ?>
</fieldset>