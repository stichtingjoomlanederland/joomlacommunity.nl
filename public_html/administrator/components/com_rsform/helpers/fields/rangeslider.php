<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldRangeSlider extends RSFormProField
{
	protected $customId;
	
	// backend preview
	public function getPreviewInput() {
		$type  	= $this->getProperty('SLIDERTYPE', 'SINGLE');
		$caption 	= $this->getProperty('CAPTION','');
		$codeIcon	= RSFormProHelper::getIcon('rangeSlider');
		
		$html = '<td>'.$caption.'</td><td>'.$codeIcon.' '.JText::_('RSFP_COMP_FVALUE_'.$type).'</td>';
		
		return $html;
	}
	
	// functions used for rendering in front view
	
	public function getFormInput() {
		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rangeslider.php';
		$slider = RSFormProRangeSlider::getInstance();
		
		$value 		= (string) $this->getValue();
		$name		= $this->getName();
		$id			= $this->getId();
		$readonly	= $this->getProperty('READONLY', 'NO');
		$values  	= $this->getProperty('VALUES', '');
		if (!empty($values)) {
			if ($this->hasCode($values)) {
				$values = $this->isCode($values);
			}
		}
		
		$attr		= $this->getAttributes('input');
		$additional = '';
		$position	= $this->getPosition();
		// Create a unique ID for this slider.
		$this->customId = $this->formId.'_'.$position;
		
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
		$componentTypeId = $this->getProperty('componentTypeId', RSFORM_FIELD_RANGE_SLIDER);
		$sliders 	 = RSFormProHelper::componentExists($this->formId, $componentTypeId);
		$componentId = $this->getProperty('componentId');
		$position 	 = 0;
		foreach ($sliders as $position => $slider) {
			if ($slider == $componentId) {
				break;
			}
		}
		return $position;
	}
}