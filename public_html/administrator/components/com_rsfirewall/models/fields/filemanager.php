<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('JPATH_PLATFORM') or die;

class JFormFieldFileManager extends JFormField
{
	public $type = 'FileManager';

	protected function getInput() {
		$html  = '';
		
		// textarea
		$columns = $this->element['cols'] ? ' cols="' . (int) $this->element['cols'] . '"' : '';
		$rows = $this->element['rows'] ? ' rows="' . (int) $this->element['rows'] . '"' : '';
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		
		// file manager
		$allowfolders 	= !empty($this->element['allowfolders']) ? '&allowfolders=1' : '';
		$allowfiles 	= !empty($this->element['allowfiles']) ? '&allowfiles=1' : '';
		
		$href = JRoute::_('index.php?option=com_rsfirewall&view=folders&tmpl=component&name='.$this->fieldname.$allowfolders.$allowfiles);
		$open = "window.open('".addslashes($href)."', 'com_rsfirewall_fileman', 'width=520, height=480,scrollbars=1');";
		
		$html .= '<div class="com-rsfirewall-file-manager-box">'."\n";
		$html .= '<button type="button" onclick="'.$open.'" class="btn btn-secondary">'.JText::_($this->element['button']).'</button>'."\n";
		$html .= '<span class="com-rsfirewall-clear"></span>';
		$html .= '<textarea name="'.$this->name.'" id="'.$this->id.'"'.$columns.$rows.$class.$disabled.'>'.$this->escape($this->value).'</textarea>'."\n";
		$html .= '</div>'."\n";
		
		return $html;
	}
	
	protected function escape($string) {
		return htmlentities($string, ENT_COMPAT, 'utf-8');
	}
}