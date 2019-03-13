<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldJqueryCalendar extends RSFormProField
{
	protected $customId;
	
	// backend preview
	public function getPreviewInput() {
		$layout  	= $this->getProperty('CALENDARLAYOUT', 'FLAT');
		$caption 	= $this->getProperty('CAPTION','');
		$codeIcon	= RSFormProHelper::getIcon('jQueryCalendar');
		
		$html = '<td>'.$caption.'</td><td>'.$codeIcon.' '.JText::_('RSFP_COMP_FVALUE_'.$layout).'</td>';
		
		return $html;
	}
	
	// functions used for rendering in front view
	
	public function getFormInput() {
		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/calendar.php';
		$calendar = RSFormProCalendar::getInstance('jQueryCalendar');
		
		$value 		= (string) $this->getValue();
		$name		= $this->getName();
		$id			= $this->getId();
		$layout  	= $this->getProperty('CALENDARLAYOUT', 'FLAT');
		$format 	= $this->getProperty('DATEFORMAT');
		$readonly	= $this->getProperty('READONLY', 'NO');
		$attr		= $this->getAttributes('input');
		$type		= 'text';
		$additional = '';
		$zIndex	  	= $this->getZIndex();
		$position	= $this->getPosition();
		// Create a unique ID for this calendar.
		$this->customId = $this->formId.'_'.$position;
		
		$hiddenValue = '';
        $hidden = JFactory::getApplication()->input->get('hidden', array(), 'array');
		$hiddenName = $this->formId.'_'.$this->getProperty('NAME', '');
		
		if (!empty($hidden[$hiddenName])) {
			$hiddenValue = preg_replace('#[^0-9\/\s\:]+#i', '', $hidden[$hiddenName]);
		} else {
			if (!empty($value)) {
				// Let's allow the 'now' keyword
				if (strtolower($value) == 'now')
				{
					$value = JFactory::getDate('now')->format($format);
				}
				if (JFactory::getLanguage()->getTag() != 'en-GB')
				{
					require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/calendar.php';

					$value = RSFormProCalendar::fixValue($value, $format);
				}
				// Try to create a date to see if it's valid
				$date = JFactory::getDate()->createFromFormat($format, $value);
				if ($date !== false)
				{
					$hiddenDateFormat = 'm/d/Y';
					if ($this->getProperty('TIMEPICKER', 'NO')) {
						// in case the user leaves the input empty and save the settings
						$timepickerformat = trim($this->getProperty('TIMEPICKERFORMAT', 'H:i'));
						if (empty($timepickerformat)) {
							$timepickerformat = 'H:i';
						}
						$hiddenDateFormat .= ' '.$timepickerformat;
					}
					$hiddenValue = $date->format($hiddenDateFormat);
				}
				else
				{
					$value = '';
					$hiddenValue = '';
				}
			}
		}
		
		// set the calendar script
		$config = array(
			'inline' 	 		 => $layout=='FLAT',
			'timepicker' 	 	 => $this->getProperty('TIMEPICKER', 'NO'),
			'timepickerformat' 	 => $this->getProperty('TIMEPICKERFORMAT', 'H:i'),
			'theme' 	 		 => strtolower($this->getProperty('THEME', 'DEFAULT')),
			'dateFormat' 		 => $format,
			'value' 	 		 => $hiddenValue,
			'minDate' 	 		 => $this->isCode($this->getProperty('MINDATEJQ', '')),
			'maxDate' 			 => $this->isCode($this->getProperty('MAXDATEJQ', '')),
			'minTime' 	 		 => $this->isCode($this->getProperty('MINTIMEJQ', '')),
			'maxTime' 			 => $this->isCode($this->getProperty('MAXTIMEJQ', '')),
			'timeStep' 			 => $this->getProperty('TIMESTEP', ''),
			'validationCalendar' => $this->getProperty('VALIDATIONCALENDAR', ''),
			'formId' 			 => $this->formId,
			'customId' 			 => $this->customId
		);
		$calendar->setCalendar($config);
		
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
		$input = '<input'.
				 ' id="txtjQcal'.$this->customId.'"'.
				 ' name="'.$this->escape($name).'"'.
				 ' type="'.$this->escape($type).'"';
		// Is it read only?
		if ($readonly) {
			$input .= ' readonly="readonly"';
		}
		// Add the value
		$input .= ' value="'.$this->escape($value).'"';
		// Additional HTML
		$input .= $additional;
		// Close the tag
		$input .= ' />';
		
		// Flat layouts need a <br /> so that the calendar is shown below the textbox
		if ($layout == 'FLAT') {
			// Let's just substitute the "button" with a <br /> tag
			$button = '<br />';
		} elseif ($layout == 'POPUP') {
			$attr  		= $this->getAttributes('button');
			$label 		= $this->getProperty('POPUPLABEL', '...');
			$additional = '';
			
			// Parse Additional Attributes for the button
			if ($attr) {
				foreach ($attr as $key => $values) {
					$additional .= $this->attributeToHtml($key, $values);
				}
			}
			
			// Create the popup button
			$button = '<input'.
					  ' id="btnjQ'.$this->customId.'"'.
					  ' type="button"'.
					  ' value="'.$this->escape($label).'"'.
					  $additional.
					  ' />';
		}
		
		// This is the calendar HTML container
		$hide = '';
		// If this is a popup layout, hide the container so that it's only shown when the button is clicked
		if ($layout == 'POPUP') {
			$hide = 'clear:both;display:none;position:absolute;';
		}
		$container = '<div'.
					' id="caljQ'.$this->customId.'Container"'.
					' style="'.$hide.'z-index:'.$zIndex.'">'.
					'</div>';
		
		// This is the hidden field of the calendar
		$hidden = '<input'.
				  ' id="hiddenjQcal'.$this->customId.'"'.
				  ' type="hidden"'.
				  ' name="hidden['.$this->formId.'_'.$id.']"'.
				  ' value="' . $this->escape($hiddenValue) .'"'.
				  ' />';
		
		$html = $this->setFieldOutput($input, $button, $container, $hidden, $layout);
		
		return $html;
	}
	
	// set the field output - function needed for overwriting in the layout classes
	protected function setFieldOutput($input, $button, $container, $hidden, $layout) {
		return $input.$button.$container.$hidden;
	}
	
	// @desc Gets the position of this calendar in the current form (eg. if it's the only calendar in the form, the position is 0,
	//	if it's the second calendar the position is 1 and so on).
	protected function getPosition() {
		$componentTypeId = $this->getProperty('componentTypeId', RSFORM_FIELD_JQUERY_CALENDAR);
		$calendars 	 = RSFormProHelper::componentExists($this->formId, $componentTypeId);
		$componentId = $this->getProperty('componentId');
		$position 	 = 0;
		foreach ($calendars as $position => $calendar) {
			if ($calendar == $componentId) {
				break;
			}
		}
		return $position;
	}
	
	protected function getZIndex() {
		return (9999 - (int) $this->getProperty('Order'));
	}
	
	// @desc All calendars should have a 'rsform-calendar-box' class for easy styling
	//		 Since the calendar is composed of multiple items, we need to differentiate the attributes through the $type parameter
	public function getAttributes($type='input') {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		
		if ($type == 'input') {
			$attr['class'] .= 'rsform-calendar-box';
			$layout	= $this->getProperty('CALENDARLAYOUT', 'FLAT');
			if ($layout == 'FLAT') {
				$attr['class'] .= ' txtCal';
			}
		} elseif ($type == 'button') {
			$attr['class'] .= 'btnCal rsform-calendar-button';
			if (!empty($attr['onclick'])) {
				$attr['onclick'] .= ' ';
			} else {
				$attr['onclick'] = '';
			}
			
			$attr['onclick'] .= "RSFormPro.jQueryCalendar.showCalendar('".$this->customId."');";
		}
		
		return $attr;
	}
}