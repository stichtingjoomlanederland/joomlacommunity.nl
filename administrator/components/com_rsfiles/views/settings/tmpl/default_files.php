<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

$fieldsets = array('files');
foreach ($fieldsets as $fieldset) {
	echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets[$fieldset]->label));
	
	echo JHtml::_('rsfieldset.element', '<label>'.JText::_('COM_RSFILES_CONF_DOWNLOAD_FOLDER').'</label>', '<span class="rs_extra"><a href="index.php?option=com_rsfiles&view=settings&layout=select&type=download&tmpl=component" class="rsf_settings_btn modal" rel="{handler:\'iframe\'}">'.JText::_('COM_RSFILES_CONF_SET_DOWNLOAD_FOLDER').'</a></span>');
	echo JHtml::_('rsfieldset.element', '<label for="rsfl_htaccess">'.JText::_('COM_RSFILES_CONF_SECURE_DOWNLOAD_FOLDER').'</label>', '<input type="checkbox" id="rsfl_htaccess" name="rsfl_htaccess" value="1" class="'.rsfilesHelper::tooltipClass().'" title="'.rsfilesHelper::tooltipText(JText::_('COM_RSFILES_CONF_HTACCESS')).'" /> <span class="rs_extra">'.(file_exists($this->config->download_folder.'/.htaccess') ? JText::_('COM_RSFILES_HTACCESS_OK_DOWNLOAD') : JText::_('COM_RSFILES_HTACCESS_NOTOK_DOWNLOAD')).'</span>');
	
	echo JHtml::_('rsfieldset.element', $this->form->getLabel('download_cancreate'), $this->form->getInput('download_cancreate'));
	echo JHtml::_('rsfieldset.element', $this->form->getLabel('download_canupload'), $this->form->getInput('download_canupload'));
	echo JHtml::_('rsfieldset.element', $this->form->getLabel('download_description'), '<span class="rs_extra">'.$this->form->getInput('download_description').'</span>');
	
	echo JHtml::_('rsfieldset.element', '<label>'.JText::_('COM_RSFILES_CONF_BRIEFCASE_FOLDER').'</label>', '<span class="rs_extra"><a href="index.php?option=com_rsfiles&view=settings&layout=select&type=briefcase&tmpl=component" class="rsf_settings_btn modal" rel="{handler:\'iframe\'}">'.JText::_('COM_RSFILES_CONF_SET_BRIEFCASE_FOLDER').'</a></span>');
	echo JHtml::_('rsfieldset.element', '<label for="rsfl_htaccess_briefcase">'.JText::_('COM_RSFILES_CONF_SECURE_BRIEFCASE_FOLDER').'</label>', '<input type="checkbox" id="rsfl_htaccess_briefcase" name="rsfl_htaccess_briefcase" value="1" class="'.rsfilesHelper::tooltipClass().'" title="'.rsfilesHelper::tooltipText(JText::_('COM_RSFILES_CONF_HTACCESS')).'" /> <span class="rs_extra">'.(file_exists($this->config->briefcase_folder.'/.htaccess') ? JText::_('COM_RSFILES_HTACCESS_OK_BRIEFCASE') : JText::_('COM_RSFILES_HTACCESS_NOTOK_BRIEFCASE')).'</span>');
	
	foreach ($this->form->getFieldset($fieldset) as $field) {
		if ($field->fieldname == 'download_cancreate' || $field->fieldname == 'download_canupload' || $field->fieldname == 'download_description')
			continue;
		
		echo JHtml::_('rsfieldset.element', $field->label, $field->input);
	}
	
	echo JHtml::_('rsfieldset.element', '<label>&nbsp;</label>', '<span class="rs_extra">'.JText::sprintf('COM_RSFILES_CONF_MAX_SIZE',ini_get('upload_max_filesize'),ini_get('post_max_size')).'</span>');
	echo JHtml::_('rsfieldset.end');
}