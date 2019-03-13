<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$hasFB = !empty($this->config->facebook_appid) && !empty($this->config->facebook_secret) && !empty($this->config->facebook_token);
$fieldsets = array('google','fb','facebook');
$redirectURI = JRoute::_('index.php?option=com_rseventspro&task=settings.savetoken', false, true); ?>

<div class="alert alert-info"><?php echo JText::_('COM_RSEVENTSPRO_CONF_CRON_INFO'); ?></div>

<?php 
foreach ($fieldsets as $fieldset) {
	echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets[$fieldset]->label));
	
	if ($fieldset == 'fb') {
		echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', '<span style="float:left;margin-top: 4px;">'.JText::_('COM_RSEVENTSPRO_CONF_FB_APP').'</span>');
	}
	
	if ($fieldset == 'facebook') {
		echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', '<a href="'.$this->login.'" class="btn btn-info"><i class="fa fa-facebook-official fa-fw"></i> '.JText::_('COM_RSEVENTSPRO_CONF_FB_BTN').'</a>');
		echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', '<span style="float:left;margin-top: 4px;">'.JText::_('COM_RSEVENTSPRO_CONF_FB_INFO').'</span>');
	}
	
	foreach ($this->form->getFieldset($fieldset) as $field) {
		if (!$hasFB && $fieldset == 'facebook')
			continue;
		
		echo JHtml::_('rsfieldset.element', $field->label, $field->input);
		
		if ($fieldset == 'fb' && $field->fieldname == 'facebook_secret') {
			echo JHtml::_('rsfieldset.element', '<label>'.JText::_('COM_RSEVENTSPRO_CONF_FACEBOOK_REDIRECT_URI').'</label>', '<span style="float:left;margin-top: 4px;font-weight:bold;">'.$redirectURI.'</span>');
		}
	}
	
	if ($fieldset == 'google') {
		if (!empty($this->config->google_client_id) && !empty($this->config->google_secret)) {
			echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', '<a href="'.$this->auth.'" class="btn btn-info button">'.JText::_('COM_RSEVENTSPRO_CONF_SYNC_BTN').'</a> <button type="button" class="btn btn-info button" onclick="jQuery(\'#rseproGoogleLog\').modal(\'show\')">'.JText::_('COM_RSEVENTSPRO_CONF_SYNC_LOG_BTN').'</button>');
		} else {
			echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', JText::_('COM_RSEVENTSPRO_CONF_SYNC_SAVE_FIRST'));
		}
	}
	
	if ($hasFB && $fieldset == 'facebook') {
		echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', '<button type="button" class="btn btn-info button" onclick="Joomla.submitbutton(\'settings.facebook\')">'.JText::_('COM_RSEVENTSPRO_CONF_SYNC_BTN').'</button> <button type="button" class="btn btn-info button" onclick="jQuery(\'#rseproFacebookLog\').modal(\'show\')">'.JText::_('COM_RSEVENTSPRO_CONF_SYNC_LOG_BTN').'</button>');
	}
	
	echo JHtml::_('rsfieldset.end');
}

echo $this->form->getInput('facebook_token');