<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('JPATH_PLATFORM') or die;

class JFormFieldRSFolders extends JFormField
{
	public $type = 'RSFolders';

	protected function getInput() {
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
		$script[] = '	function jSelectFolder(path) {';
		$script[] = '		document.id("'.$this->id.'_id").value = path;';
		$script[] = '		document.id("'.$this->id.'_name").value = path;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';
		
		$script[] = '	function jDeselectFolder() {';
		$script[] = '		document.id("'.$this->id.'_id").value = "";';
		$script[] = '		document.id("'.$this->id.'_name").value = "'.JText::_('COM_RSFILES_DOWNLOAD_ROOT',true).'";';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		
		// Setup variables for display.
		$html	= array();
		$link	= 'index.php?option=com_rsfiles&amp;view=files&amp;layout=modal&amp;tmpl=component';
		
		$title = $this->value;

		if (empty($title)) {
			$title = JText::_('COM_RSFILES_DOWNLOAD_ROOT');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
		
		require_once JPATH_SITE.'/components/com_rsfiles/helpers/rsfiles.php';
		
		// The current user display field.
		if (rsfilesHelper::isJ3()) {
			$html[] = '<span class="input-append">';
			$html[] = '<input type="text" class="input-large" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" /><a class="modal btn" title="'.JText::_('COM_RSFILES_CHANGE_DOWNLOAD_ROOT').'"  href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.JText::_('JSELECT').'</a> <a class="btn" title="'.JText::_('COM_RSFILES_CLEAR').'"  href="javascript:void(0)" onclick="jDeselectFolder();"><i class="icon-remove"></i></a>';
			$html[] = '</span>';
		} else {
			$html[] = '<input type="text" class="input-large" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
			$html[] = '<div class="button2-left">';
			$html[] = '<div class="blank">';
			$html[] = '<a class="modal btn" title="'.JText::_('COM_RSFILES_CHANGE_DOWNLOAD_ROOT').'"  href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.JText::_('JSELECT').'</a> ';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '<div class="button2-left">';
			$html[] = '<div class="blank">';
			$html[] = '<a class="btn" title="'.JText::_('COM_RSFILES_CLEAR').'"  href="javascript:void(0)" onclick="jDeselectFolder();">'.JText::_('COM_RSFILES_CLEAR').'</a>';
			$html[] = '</div>';
			$html[] = '</div>';
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$this->value.'" />';
		return implode("\n", $html);
	}
}