<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$fieldsets = array('google','facebook'); 
foreach ($fieldsets as $fieldset) {
	echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets[$fieldset]->label));
	
	if ($fieldset == 'facebook') {
		echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', '<button type="button" class="btn btn-info button" id="fbconnect" onclick="fconnect()">'.JText::_('COM_RSEVENTSPRO_CONF_FB_BTN').'</button>');
		echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', '<span style="float:left;margin-top: 4px;">'.JText::_('COM_RSEVENTSPRO_CONF_FB_INFO').'</span>');
	}
	
	foreach ($this->form->getFieldset($fieldset) as $field) {
		if (empty($this->config->facebook_token) && $fieldset == 'facebook')
			continue;
		
		echo JHtml::_('rsfieldset.element', $field->label, $field->input);
	}
	
	if ($fieldset == 'google') {
		if (!empty($this->config->google_client_id) && !empty($this->config->google_secret)) {
			echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', '<a href="'.$this->auth.'" class="btn btn-info button">'.JText::_('COM_RSEVENTSPRO_CONF_SYNC_BTN').'</a>');
		} else {
			echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', JText::_('COM_RSEVENTSPRO_CONF_SYNC_SAVE_FIRTS'));
		}
	}
	
	if (!empty($this->config->facebook_token) && $fieldset == 'facebook') {
		echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', '<button type="button" class="btn btn-info button" onclick="Joomla.submitbutton(\'settings.facebook\')">'.JText::_('COM_RSEVENTSPRO_CONF_SYNC_BTN').'</button>');
	}
	
	echo JHtml::_('rsfieldset.end');
}