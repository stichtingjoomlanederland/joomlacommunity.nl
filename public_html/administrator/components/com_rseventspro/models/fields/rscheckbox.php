<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

class JFormFieldRSCheckbox extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'RSCheckbox';

	protected function getInput() {
		$html		= array();
		$label		= (string) $this->element['label'];
		$checked 	= $this->value ? 'checked="checked"' : '';
		$muted		= (string) $this->element['muted'];
		$disabled	= (string) $this->element['disabled'];
		$disabled	= $disabled === 'true' ? 'disabled' : '';
		
		$html[] = '<label for="'.$this->id.'" class="checkbox '.($muted === 'true' ? RSEventsproAdapterGrid::styles(array('muted')) : '').'">';
		$html[] = '<input type="checkbox" id="'.$this->id.'" name="'.$this->name.'" value="1" '.$checked.' '.$disabled.' /> '.JText::_($label);
		$html[] = '</label>';
		
		return implode("\n", $html);
	}
	
	protected function getLabel() {
		return '';
	}
}