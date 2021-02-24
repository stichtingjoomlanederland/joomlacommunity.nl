<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_CATEGORIES'); ?></legend>
	<?php echo RSEventsproAdapterGrid::inputGroup($this->dependencies->getInput('categories'), null, '<a href="#rsepro-add-new-categ" data-toggle="modal" data-bs-toggle="modal" class="btn btn-primary" type="button">'.JText::_('COM_RSEVENTSPRO_EVENT_ADD_CATEGORY').'</a>'); ?>
</fieldset>

<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAGS'); ?></legend>
	<?php echo $this->dependencies->getInput('tags'); ?>
</fieldset>

<div class="form-actions">
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
</div>