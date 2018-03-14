<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldRSResolution extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'RSResolution';

	public function __construct($parent = null) {
		static $added;
		parent::__construct($parent);
		
		if (!$added) {
			// add javascript
			JFactory::getDocument()->addScriptDeclaration("function rsepro_change_other(id, value) {
				var value = value == 'h' ? '".JText::_('COM_RSEVENTSPRO_GALLERY_PARAM_WIDTH', true)."' : '".JText::_('COM_RSEVENTSPRO_GALLERY_PARAM_HEIGHT', true)."';
				document.getElementById('rsepro_other_' + id).innerHTML = value;
			}");
			
			$added = true;
		}
	}
	
	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	public function getInput() {
		$html 		= array();
		
		$name  		= $this->name;
		$fieldname 	= $this->fieldname;
		$value 		= $this->value;
		$node  		= $this->element;
		
		$size 		= isset($this->element['size']) ? 'size="'.$this->element['size'].'"' : '';
		$value		= is_array($value) ? $value : explode(',', $value);		
		if (!isset($value[1])) $value[1] = '0';
		
		$options = array(
			JHtml::_('select.option', 'w', JText::_('COM_RSEVENTSPRO_GALLERY_PARAM_WIDTH')),
			JHtml::_('select.option', 'h', JText::_('COM_RSEVENTSPRO_GALLERY_PARAM_HEIGHT'))
		);
		
		$select = JHtml::_('select.genericlist', $options, $name.'[]', 'onchange="rsepro_change_other(\''.addslashes($fieldname).'\', this.value)"', 'value', 'text', $value[0], $fieldname.'_w');
		$input	= '<input style="text-align: center;" type="text" name="'.$name.'[]" id="'.$fieldname.'_res" value="'.(int) $value[1].'" '.$size.' />';
		$other	= $value[0] == 'h' ? JText::_('COM_RSEVENTSPRO_GALLERY_PARAM_WIDTH') : JText::_('COM_RSEVENTSPRO_GALLERY_PARAM_HEIGHT');
		
		// because Joomla! 2.5 doesn't behave quite right with params and we don't want to keep this whole HTML code in the language files, we need to make this ugly workaround
		$words = explode(' ', JText::_('COM_RSEVENTSPRO_GALLERY_PARAM_SIZE_ADJUST'));
		
		foreach ($words as $word) {
			// found replacement, wrap it in a <td>
			if ($word[0] == '%') {
				if (strstr($word, '%3$s'))
					$html[] = '<span'.(strstr($word, '%3$s') ? ' id="rsepro_other_'.$fieldname.'"' : '').' class="rsextra">'.$word.'</span>';
				else
					$html[] = $word;
			}
			else
				$html[] = '<span class="rsextra">'.$word.'</span>';
		}
		
		return sprintf(implode("\r\n", $html), $select, $input, $other);
	}
}