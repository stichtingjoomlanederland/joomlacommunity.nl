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

<div class="control-group">
	<div class="control-label">
		<label for="jform_rsm_enable"><?php echo JText::_('COM_RSEVENTSPRO_RSMAIL_ENABLE'); ?></label>
	</div>
	<div class="controls">
		<select name="jform[rsm_enable]" id="jform_rsm_enable" class="span2">
			<?php echo JHtml::_('select.options', $this->eventClass->yesno(), 'value', 'text', $this->item->rsm_enable, true); ?>
		</select>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_rsm_when"><?php echo JText::_('COM_RSEVENTSPRO_RSMAIL_WHEN'); ?></label>
	</div>
	<div class="controls">
		<select name="jform[rsm_when]" id="jform_rsm_when" class="span2">
			<option value="0"<?php echo $this->item->rsm_when == 0 ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_RSEVENTSPRO_RSMAIL_WHEN_ON_REGISTRATION'); ?></option>
			<option value="1"<?php echo $this->item->rsm_when == 1 ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_RSEVENTSPRO_RSMAIL_WHEN_ON_CONFIRMATION'); ?></option>
		</select>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_rsm_lists"><?php echo JText::_('COM_RSEVENTSPRO_RSMAIL_LISTS'); ?></label>
	</div>
	<div class="controls">
		<select class="rsepro-chosen" multiple name="jform[rsm_lists][]" id="jform_rsm_lists">
			<?php echo JHtml::_('select.options', rseventsproHelper::getRsmailLists(), 'value','text',$this->item->rsm_lists); ?>
		</select>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="statuses"><?php echo JText::_('COM_RSEVENTSPRO_RSMAIL_AUTOSUBSCRIBE'); ?></label>
	</div>
	<div class="controls">
		<select class="rsepro-chosen" multiple name="statuses[]" id="statuses">
			<option value="0"><?php echo JText::_('COM_RSEVENTSPRO_RULE_STATUS_INCOMPLETE'); ?></option>
			<option value="1"><?php echo JText::_('COM_RSEVENTSPRO_RULE_STATUS_COMPLETE'); ?></option>
			<option value="2"><?php echo JText::_('COM_RSEVENTSPRO_RULE_STATUS_DENIED'); ?></option>
		</select>
		
		<button type="button" class="btn" onclick="RSEventsPro.Event.subscribeUsers()">
			<?php echo JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'rsepro-subscribe-users-loader', 'style' => 'display: none;'), true); ?> 
			<?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBE_TO_NEWSLETTER'); ?>
		</button>
	</div>
</div>

<div class="form-actions">
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
</div>