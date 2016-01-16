<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
JHtml::_('behavior.modal', 'a.modal_jform_owner'); ?>
<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_CONTACT'); ?></legend>

<div class="control-group">
	<div class="control-label">
		<label for="jform_owner"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_THEOWNER'); ?></label>
	</div>
	<div class="controls">
		<div class="input-append">
			<input type="text" readonly="" value="<?php echo $this->eventClass->getOwner(); ?>" id="jform_owner_name">
			<a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_users&view=users&layout=modal&tmpl=component&field=jform_owner_name'); ?>" class="btn btn-primary modal_jform_owner">
			<i class="fa fa-user fa-inverse"></i></a>
		</div>
		<input type="hidden" value="<?php echo $this->escape($this->item->owner); ?>" name="jform[owner]" id="jform_owner">
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_URL"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_WEB'); ?></label>
	</div>
	<div class="controls">
		<input type="text" value="<?php echo $this->escape($this->item->URL); ?>" class="span10" id="jform_URL" name="jform[URL]" />
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_phone"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_PHONE'); ?></label>
	</div>
	<div class="controls">
		<input type="text" value="<?php echo $this->escape($this->item->phone); ?>" class="span10" id="jform_phone" name="jform[phone]" />
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_email"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_EMAIL'); ?></label>
	</div>
	<div class="controls">
		<input type="text" value="<?php echo $this->escape($this->item->email); ?>" class="span10" id="jform_email" name="jform[email]" />
	</div>
</div>

<div class="form-actions">
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
</div>