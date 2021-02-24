<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');
class JFormFieldRSLocations extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'RSLocations';
	
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

	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getOptions() {
		$options	= parent::getOptions();
		$default	= array((object) array('value' => 0, 'text' => JText::_('COM_RSEVENTSPRO_CONF_GOOGLE_NEW_LOCATION')));
		$locations	= rseventsproHelper::getLocations();
		
		if (!isset($this->element['show_root']))
			return array_merge($default, $locations);
		
		$locations = array_merge($options, $locations);
		
		return $locations;
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