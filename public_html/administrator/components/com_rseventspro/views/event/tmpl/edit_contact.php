<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_CONTACT'); ?></legend>
	
	<?php echo $this->form->renderFieldset('contact'); ?>
	
	<div class="form-actions">
		<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
	</div>
</fieldset>