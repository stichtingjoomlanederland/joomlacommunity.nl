<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

JText::script('COM_RSEVENTSPRO_FACEBOOK_NO_EVENTS');
JText::script('COM_RSEVENTSPRO_FACEBOOK_IMPORT_SUCCESS');

$hasFB = !empty($this->config->facebook_appid) && !empty($this->config->facebook_secret) && !empty($this->config->facebook_token);
$fieldsets = array('google','fb','facebook');
$redirectURI = JRoute::_('index.php?option=com_rseventspro&task=settings.savetoken', false, 1); ?>

<div class="alert alert-info"><?php echo JText::_('COM_RSEVENTSPRO_CONF_CRON_INFO'); ?></div>

<?php 
foreach ($fieldsets as $fieldset) {
	echo '<fieldset class="options-form">';
	echo '<legend>'.JText::_($this->fieldsets[$fieldset]->label).'</legend>';
	
	if ($fieldset == 'fb') {
		echo RSEventsproAdapterGrid::renderField('&nbsp;', '<div class="alert alert-info">'.JText::_('COM_RSEVENTSPRO_CONF_FB_APP').'</div>');
	}
	
	if ($fieldset == 'facebook') {
		echo RSEventsproAdapterGrid::renderField('&nbsp;', '<a href="'.$this->login.'" class="btn btn-info"><i class="fa fa-facebook-official fa-fw"></i> '.JText::_('COM_RSEVENTSPRO_CONF_FB_BTN').'</a>');
		echo RSEventsproAdapterGrid::renderField('&nbsp;', '<span style="float:left;margin-top: 4px;">'.JText::_('COM_RSEVENTSPRO_CONF_FB_INFO').'</span>');
	}
	
	foreach ($this->form->getFieldset($fieldset) as $field) {
		if (!$hasFB && $fieldset == 'facebook') {
			continue;
		}
		
		echo $field->renderField();
		
		if ($fieldset == 'fb' && $field->fieldname == 'facebook_secret') {
			echo RSEventsproAdapterGrid::renderField(JText::_('COM_RSEVENTSPRO_CONF_FACEBOOK_REDIRECT_URI'), '<span style="float:left;margin-top: 4px;font-weight:bold;">'.$redirectURI.'</span>');
		}
	}
	
	if ($fieldset == 'google') {
		if (!empty($this->config->google_client_id) && !empty($this->config->google_secret)) {
			$sync = $this->config->google_access_token ? '<a href="'.JRoute::_('index.php?option=com_rseventspro&task=settings.gimport').'" class="btn btn-info">'.JText::_('COM_RSEVENTSPRO_CONF_SYNC_BTN').'</a>' : '';
			echo RSEventsproAdapterGrid::renderField('&nbsp;', '<a href="'.$this->auth.'" class="btn btn-info">'.JText::_('COM_RSEVENTSPRO_CONF_AUTH_BTN').'</a> '.$sync.' <button type="button" class="btn btn-info" onclick="jQuery(\'#rseproGoogleLog\').modal(\'show\')">'.JText::_('COM_RSEVENTSPRO_CONF_SYNC_LOG_BTN').'</button>');
		} else {
			echo RSEventsproAdapterGrid::renderField('&nbsp;', '<div class="alert alert-info">'.JText::_('COM_RSEVENTSPRO_CONF_SYNC_SAVE_FIRST').'</div>');
		}
	}
	
	if ($hasFB && $fieldset == 'facebook') {
		echo RSEventsproAdapterGrid::renderField('&nbsp;', '<button id="fbBtn" type="button" class="btn btn-info" onclick="rsepro_import_facebook()">'.JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'rsepro-facebook-loader', 'style' => 'display: none;'), true).' '.JText::_('COM_RSEVENTSPRO_CONF_SYNC_BTN').'</button> <button type="button" class="btn btn-info" onclick="jQuery(\'#rseproFacebookLog\').modal(\'show\')">'.JText::_('COM_RSEVENTSPRO_CONF_SYNC_LOG_BTN').'</button>');
		echo RSEventsproAdapterGrid::renderField('&nbsp;', '<div id="fbMessage" class="alert alert-info span5" style="display:none;"></div>');
	}
	
	echo '</fieldset>';
}

echo $this->form->getInput('facebook_token');
echo $this->form->getInput('google_access_token');