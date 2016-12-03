<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access'); 

$fieldsets = array('general');
foreach ($fieldsets as $fieldset) {
	echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets[$fieldset]->label));
	foreach ($this->form->getFieldset($fieldset) as $field) {
		$extra = '';
		if ($field->fieldname == 'FilePath') {
			$extra = ' <span class="rs_extra"><img src="'.JURI::root().'administrator/components/com_rsfiles/assets/images/icons/'.$this->item->type.'.png" alt="" class="'.rsfilesHelper::tooltipClass().'" title="'.rsfilesHelper::tooltipText(JText::_(strtoupper('COM_RSFILES_FILE_TYPE_'.$this->item->type))).'" /></span>';
		}
		
		if ($this->briefcase) {
			if ($this->type == 'folder') {
				if (in_array($field->fieldname, array('publish_down', 'FileStatistics', 'FileVersion', 'IdLicense', 'DownloadMethod', 'DownloadLimit')))
					continue;
			} else {
				if ($field->fieldname == 'publish_down' || $field->fieldname == 'FileStatistics' || $field->fieldname == 'DownloadMethod')
					continue;
			}
		} else {
			// Dont show specific fields if the path is a folder
			if ($this->type == 'folder' && in_array($field->fieldname, array('FileStatistics','FileVersion','IdLicense','DownloadMethod','DownloadLimit','show_preview')))
				continue;
		
			if ($this->type == 'folder' && $field->fieldname == 'publish_down')
				continue;
		}
		
		echo JHtml::_('rsfieldset.element', $field->label, $field->input.$extra);
		
		if ($field->fieldname == 'DownloadLimit') {
			if (empty($this->item->FileThumb) || !file_exists(JPATH_SITE.'/components/com_rsfiles/images/thumbs/files/'.$this->item->FileThumb)) {
				echo JHtml::_('rsfieldset.element', '<label for="thumb" class="'.rsfilesHelper::tooltipClass().'" title="'.rsfilesHelper::tooltipText(JText::_('COM_RSFILES_FILE_THUMB_DESC')).'">'.JText::_('COM_RSFILES_FILE_THUMB').'</label>', '<input type="file" id="thumb" name="thumb" size="50" />');
			} else {
				$thumb = JHTML::_('image', JURI::root().'components/com_rsfiles/images/thumbs/files/'.$this->item->FileThumb.'?sid='.rand(), '','class="rsf_thumb" style="vertical-align: middle;"');
				$thumb .= ' <a href="'.JRoute::_('index.php?option=com_rsfiles&task=file.deletethumb&id='.$this->item->IdFile).'">';
				$thumb .= JHTML::_('image', JURI::root().'administrator/components/com_rsfiles/assets/images/icons/delete.png', '');
				$thumb .= '</a>';
				
				echo JHtml::_('rsfieldset.element', '<label for="thumb" class="'.rsfilesHelper::tooltipClass().'" title="'.rsfilesHelper::tooltipText(JText::_('COM_RSFILES_FILE_THUMB_DESC')).'">'.JText::_('COM_RSFILES_FILE_THUMB').'</label>', '<span class="rs_extra">'.$thumb.'</span>');
			}
			
			if ($this->type != 'folder') {
				if (empty($this->item->preview) || !file_exists(JPATH_SITE.'/components/com_rsfiles/images/preview/'.$this->item->preview)) {
					$preview = '<input type="file" id="preview" name="preview" size="50" /> <br /><br />';
					$preview .= '<input type="checkbox" id="resize" name="resize" value="1" /> <label class="checkbox inline" for="resize">'.JText::_('COM_RSFILES_FILE_PREVIEW_RESIZE').'</label> <input type="text" value="200" class="input-mini" size="5" name="resize_width" /> px';
					echo JHtml::_('rsfieldset.element', '<label for="preview" class="'.rsfilesHelper::tooltipClass().'" title="'.rsfilesHelper::tooltipText(JText::sprintf('COM_RSFILES_FILE_PREVIEW_DESC',rsfilesHelper::previewExtensions(true))).'">'.JText::_('COM_RSFILES_FILE_PREVIEW').'</label>', '<span class="rs_extra">'.$preview.'</span>');
				} else {
					$properties = rsfilesHelper::previewProperties($this->item->IdFile);
					$preview = '<a class="modal" rel="{handler:\''.$properties['handler'].'\','.$properties['size'].'}" href="'.JRoute::_('index.php?option=com_rsfiles&task=preview&tmpl=component&id='.$this->item->IdFile,false).'">'.JText::_('COM_RSFILES_FILE_PREVIEW').'</a>';
					$preview .= ' / <a href="'.JRoute::_('index.php?option=com_rsfiles&task=file.deletepreview&id='.$this->item->IdFile).'">'.JText::_('COM_RSFILES_DELETE').'</a>';
					
					echo JHtml::_('rsfieldset.element', '<label for="preview" class="'.rsfilesHelper::tooltipClass().'" title="'.rsfilesHelper::tooltipText(JText::sprintf('COM_RSFILES_FILE_PREVIEW_DESC',rsfilesHelper::previewExtensions(true))).'">'.JText::_('COM_RSFILES_FILE_PREVIEW').'</label>', '<span class="rs_extra">'.$preview.'</span>');
				}
			}
		}	
	}
	
	echo JHtml::_('rsfieldset.end');
}