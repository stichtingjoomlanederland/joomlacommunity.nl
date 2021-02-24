<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('category');

class JFormFieldRSCategories extends JFormFieldCategory
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'RSCategories';
	
	public function __construct() {
		if (!class_exists('rseventsproHelper')) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
		}
		
		if (rseventsproHelper::isJ4()) {
			JText::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');
			JText::script('JGLOBAL_SELECT_PRESS_TO_SELECT');

			JFactory::getDocument()->getWebAssetManager()->usePreset('choicesjs')->useScript('webcomponent.field-fancy-select');
		}
	}
	
	public function getInput() {
		if (rseventsproHelper::isJ4()) {
			$attr		= '';
			$attr2		= '';
			$options	= $this->getOptions();

			$attr .= !empty($this->element['size']) ? ' size="' . $this->element['size'] . '"' : '';
			$attr .= $this->element['multiple'] ? ' multiple' : '';
			
			$attr2  = '';
			$attr2 .= !empty($this->element['class']) ? ' class="' . $this->element['class'] . '"' : '';
			$attr2 .= ' placeholder="' . JText::_('JGLOBAL_TYPE_OR_SELECT_SOME_OPTIONS',true) . '" ';
			$attr2 .= ' allow-custom';
			
			return '<joomla-field-fancy-select '.trim($attr2).'>'.JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id).'</joomla-field-fancy-select>';
		} else {
			return parent::getInput();
		}
	}
}