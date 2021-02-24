<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_CATEGORIES'); ?></legend>
	<?php $append = !empty($this->permissions['can_create_categories']) || $this->admin ? '<a rel="rs_category" '.($this->config->modaltype == 1 ? 'href="#rsepro-add-new-categ" data-toggle="modal" data-bs-toggle="modal"' : '').' class="btn btn-primary" type="button">'.JText::_('COM_RSEVENTSPRO_EVENT_ADD_CATEGORY').'</a>' : ''; ?>
	<?php echo RSEventsproAdapterGrid::inputGroup($this->dependencies->getInput('categories'), null, $append); ?>
</fieldset>

<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAGS'); ?></legend>
	<?php echo $this->dependencies->getInput('tags'); ?>
</fieldset>

<div class="form-actions">
	<button class="btn btn-success rsepro-event-save" type="button"><?php echo JText::_('COM_RSEVENTSPRO_SAVE_EVENT'); ?></button>
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
</div>