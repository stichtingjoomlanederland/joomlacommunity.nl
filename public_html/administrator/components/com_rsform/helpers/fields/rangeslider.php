<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';
require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rangeslider.php';

class RSFormProFieldRangeSlider extends RSFormProField
{
	protected $customId;
	
	// backend preview
	public function getPreviewInput()
	{
		return RSFormProHelper::getIcon('rangeSlider') . ' ' . JText::_('RSFP_COMP_FVALUE_' . $this->getProperty('SLIDERTYPE', 'SINGLE'));
	}
	
	// functions used for rendering in front view
	
	public function getFormInput() {
		$slider = RSFormProRangeSlider::getInstance();
		
		$value 		= (string) $this->getValue();
		$name		= $this->getName();
		$readonly	= $this->getProperty('READONLY', 'NO');
		$values  	= $this->getProperty('VALUES', '');
		if (!empty($values)) {
			if ($this->hasCode($values)) {
				$values = $this->isCode($values);
			}
		}
		
		$attr		= $this->getAttributes('input');
		$additional = '';
		// Create a unique ID for this slider.
		$this->customId = $this->formId . '_' . $this->getPosition();
		
		// set the slider script
		$config = array(
			'type' 	 			 => $this->getProperty('SLIDERTYPE', 'SINGLE'),
			'skin' 	 			 => $this->getProperty('SKIN', 'FLAT'),
			'min' 	 	 		 => $this->getProperty('MINVALUE', 0),
			'max' 	 			 => $this->getProperty('MAXVALUE', 100),
			'grid' 	 			 => $this->getProperty('GRID', 'YES'),
			'grid_snap' 	 	 => $this->getProperty('GRIDSNAP', 'NO'),
			'step' 	 	 		 => $this->getProperty('GRIDSTEP', 10),
			'force_edges' 	 	 => $this->getProperty('FORCEEDGES', 'YES'),
			'from_fixed' 	 	 => $this->getProperty('FROMFIXED', 'NO'),
			'to_fixed' 	 	     => $this->getProperty('TOFIXED', 'NO'),
			'keyboard' 	 	     => $this->getProperty('KEYBOARD', 'NO'),
			'disable' 	 	     => $readonly,
			'use_values' 	 	 => $this->getProperty('USEVALUES', 'NO'),
			'values' 	 		 => $values,
			'formId' 			 => $this->formId,
			'customId' 			 => $this->customId
		);
		$slider->setSlider($config);
		
		// Parse Additional Attributes for the input textbox
		if ($attr) {
			foreach ($attr as $key => $values) {
				// @new feature - Some HTML attributes (type, size, maxlength) can be overwritten
				// directly from the Additional Attributes area
				if ($key == 'type' && strlen($values)) {
					${$key} = $values;
					continue;
				}
				$additional .= $this->attributeToHtml($key, $values);
			}
		}
		
		// This is the textbox used to display the date
		$html = '<input'.
				 ' id="rs-range-slider'.$this->customId.'"'.
				 ' name="'.$this->escape($name).'"'.
				 ' type="text"';
		// Is it read only?
		if ($readonly) {
			$html .= ' readonly="readonly"';
		}
		// Add the value
		$html .= ' value="'.$this->escape($value).'"';
		// Additional HTML
		$html .= $additional;
		// Close the tag
		$html .= ' />';
		
		return $html;
	}
	
	// @desc Gets the position of this slider in the current form (eg. if it's the only slider in the form, the position is 0,
	//	if it's the second slider the position is 1 and so on).
	protected function getPosition() {
		return RSFormProRangeSlider::getInstance()->getPosition($this->formId, $this->componentId);
	}

	public function processValidation($validationType = 'form', $submissionId = 0)
	{
		$required 	= $this->isRequired();
		$value 		= trim($this->getValue());

		if ($required && empty($value))
		{
			return false;
		}

		return true;
	}
}