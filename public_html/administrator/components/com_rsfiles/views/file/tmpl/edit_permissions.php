<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

$fieldsets = array('permissions');
foreach ($fieldsets as $fieldset) {
	echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets[$fieldset]->label));
	foreach ($this->form->getFieldset($fieldset) as $field) {
		$extra = '';
		
		if ($this->type == 'folder') {
			if ($field->fieldname == 'CanCreate')
				$extra = ' <span class="rs_extra rsextend"><input type="checkbox" id="extendCanCreate" name="extendCanCreate" value="1" /> <label for="extendCanCreate" class="rsf_inline">'.JText::_('COM_RSFILES_FILE_EXTEND').'</label></span>';
			
			if ($field->fieldname == 'CanUpload')
				$extra = ' <span class="rs_extra rsextend"><input type="checkbox" id="extendCanUpload" name="extendCanUpload" value="1" /> <label for="extendCanUpload" class="rsf_inline">'.JText::_('COM_RSFILES_FILE_EXTEND').'</label></span>';
			
			if ($field->fieldname == 'CanDelete')
				$extra = ' <span class="rs_extra rsextend"><input type="checkbox" id="extendCanDelete" name="extendCanDelete" value="1" /> <label for="extendCanDelete" class="rsf_inline">'.JText::_('COM_RSFILES_FILE_EXTEND').'</label></span>';
			
			if ($field->fieldname == 'CanEdit')
				$extra = ' <span class="rs_extra rsextend"><input type="checkbox" id="extendCanEdit" name="extendCanEdit" value="1" /> <label for="extendCanEdit" class="rsf_inline">'.JText::_('COM_RSFILES_FILE_EXTEND').'</label></span>';
			
			if ($field->fieldname == 'CanView')
				$extra = ' <span class="rs_extra rsextend"><input type="checkbox" id="extendView" name="extendView" value="1" /> <label for="extendView" class="rsf_inline">'.JText::_('COM_RSFILES_FILE_EXTEND').'</label></span>';
			
			if ($field->fieldname == 'CanDownload')
				$extra = ' <span class="rs_extra rsextend"><input type="checkbox" id="extendDownload" name="extendDownload" value="1" /> <label for="extendDownload" class="rsf_inline">'.JText::_('COM_RSFILES_FILE_EXTEND').'</label></span>';
		} else {
			if ($field->fieldname == 'CanCreate' || $field->fieldname == 'CanUpload') {
				continue;
			}
		}
		
		echo JHtml::_('rsfieldset.element', $field->label, $field->input.$extra);
	}
	echo JHtml::_('rsfieldset.end');
}