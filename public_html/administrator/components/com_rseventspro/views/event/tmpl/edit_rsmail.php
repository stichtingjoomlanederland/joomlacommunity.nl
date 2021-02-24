<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
JText::script('COM_RSEVENTSPRO_RSMAIL_NO_LIST_SELECTED');
JText::script('COM_RSEVENTSPRO_RSMAIL_IMPORTED_OK');
JText::script('COM_RSEVENTSPRO_RSMAIL_NO_STATUS_SELECTED'); ?>

<fieldset class="options-form">
	<?php echo $this->form->renderField('rsm_enable'); ?>
	<?php echo $this->form->renderField('rsm_when'); ?>
	<?php echo $this->form->renderField('rsm_lists'); ?>
	<?php $button = '<button type="button" class="btn btn-primary" onclick="RSEventsPro.Event.subscribeUsers()">'.JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'rsepro-subscribe-users-loader', 'style' => 'display: none;'), true).' '.JText::_('COM_RSEVENTSPRO_SUBSCRIBE_TO_NEWSLETTER').'</button>'; ?>
	<?php echo RSEventsproAdapterGrid::renderField($this->dependencies->getLabel('statuses'), RSEventsproAdapterGrid::inputGroup($this->dependencies->getInput('statuses'), null, $button)); ?>
	
	<div class="form-actions">
		<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
	</div>
</fieldset>