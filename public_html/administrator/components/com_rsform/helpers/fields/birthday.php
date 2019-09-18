<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/


defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/selectlist.php';

class RSFormProFieldBirthDay extends RSFormProFieldSelectList
{
	protected $processing;
	
	// backend preview
	public function getPreviewInput()
	{
		$ordering 	= $this->getProperty('DATEORDERING');
		$separator	= $this->getProperty('DATESEPARATOR');
		$day   		= strpos($ordering, 'D');
		$month 		= strpos($ordering, 'M');
		$year  		= strpos($ordering, 'Y');
		
		$showDay 			= $this->getProperty('SHOWDAY', 'YES');
		$showMonth 			= $this->getProperty('SHOWMONTH', 'YES');
		$showYear 			= $this->getProperty('SHOWYEAR', 'YES');
		$this->hasAllFields = $showDay && $showMonth && $showYear;
		
		$items = array();
		
		// Make invalid compatible with the 3 select lists
		$invalid = $this->invalid;
		$this->invalid = array(
			'd' => $invalid && empty($this->value['d']),
			'm' => $invalid && empty($this->value['m']),
			'y' => $invalid && empty($this->value['y'])
		);
		
		// Show days
		if ($showDay) {
			// Set processing to days
			$this->processing = 'd';
			$items[$day] = parent::getPreviewInput();
		}
		
		// Show months
		if ($showMonth) {
			// Set processing to months
			$this->processing = 'm';
			$items[$month] = parent::getPreviewInput();
		}
		
		if ($showYear) {
			// Set processing to years
			$this->processing = 'y';
			$items[$year] = parent::getPreviewInput();
		}
		
		ksort($items);
		
		return implode($separator, $items);
	}
	
	// functions used for rendering in front view
	public function getFormInput() {
		$ordering 	= $this->getProperty('DATEORDERING');
		$separator	= $this->getProperty('DATESEPARATOR');
		$day   		= strpos($ordering, 'D');
		$month 		= strpos($ordering, 'M');
		$year  		= strpos($ordering, 'Y');
		
		$showDay 			= $this->getProperty('SHOWDAY', 'YES');
		$showMonth 			= $this->getProperty('SHOWMONTH', 'YES');
		$showYear 			= $this->getProperty('SHOWYEAR', 'YES');
		$this->hasAllFields = $showDay && $showMonth && $showYear;
		
		$items = array();
		
		// Make invalid compatible with the 3 select lists
		$invalid = $this->invalid;
		$this->invalid = array(
			'd' => $invalid && empty($this->value['d']),
			'm' => $invalid && empty($this->value['m']),
			'y' => $invalid && empty($this->value['y'])
		);
		
		// Show days
		if ($showDay) {
			// Set processing to days
			$this->processing = 'd';
			$items[$day] = parent::getFormInput();
		}
		
		// Show months
		if ($showMonth) {
			// Set processing to months
			$this->processing = 'm';
			$items[$month] = parent::getFormInput();
		}
		
		if ($showYear) {
			// Set processing to years
			$this->processing = 'y';
			$items[$year] = parent::getFormInput();
		}
		
		ksort($items);
		return implode($separator, $items);
	}
	
	// @desc Gets the field
	public function getId() {
		return $this->name.$this->processing;
	}
	
	// @desc Overriden because <select> lists have an additional [item]
	public function getName() {
		return $this->namespace.'['.$this->name.']['.$this->processing.']';
	}
	
	// @desc Items are based on current processing item
	public function getItems() {
		$items = array();
		
		if ($this->processing == 'd') {
			$please = $this->getProperty('SHOWDAYPLEASE', '');
			$type 	= $this->getProperty('SHOWDAYTYPE', 'DAY_TYPE_1');
			
			// Show the please select item
			if (strlen($please)) {
				$items[] = '|'.$please;
			}
			
			// Add the days to the list
			for ($i=1; $i<=31; $i++) {
				$label = '';
				if ($type == 'DAY_TYPE_1') {
					$label = $i;
				} elseif ($type == 'DAY_TYPE_01') {
					$label = str_pad($i, 2, '0', STR_PAD_LEFT);
				}
				$items[] = $i.'|'.$label;
			}
		} elseif ($this->processing == 'm') {
			$please = $this->getProperty('SHOWMONTHPLEASE', '');
			$type 	= $this->getProperty('SHOWMONTHTYPE', 'MONTH_TYPE_1');
			
			// Show the please select item
			if (strlen($please)) {
				$items[] = '|'.$please;
			}
			
			// Add the months to the list
			for ($i=1; $i<=12; $i++) {
				$label = '';
				
				if ($type == 'MONTH_TYPE_1') {
					$label = $i;
				} elseif ($type == 'MONTH_TYPE_01') {
					$label = str_pad($i, 2, '0', STR_PAD_LEFT);
				} elseif ($type == 'MONTH_TYPE_TEXT_SHORT') {
					$label = JText::_('RSFP_CALENDAR_MONTHS_SHORT_'.$i);
				} elseif ($type == 'MONTH_TYPE_TEXT_LONG') {
					$label = JText::_('RSFP_CALENDAR_MONTHS_LONG_'.$i);
				}
				
				$items[] = $i.'|'.$label;
			}
		} elseif ($this->processing == 'y') {
			$please = $this->getProperty('SHOWYEARPLEASE', '');
			
			// Show the please select item
			if (strlen($please)) {
				$items[] = '|'.$please;
			}
			
			$start 	= (int) $this->getProperty('STARTYEAR', 1970);
			$end 	= (int) $this->getProperty('ENDYEAR', 2050);
			
			if ($start < $end) {
				for ($i=$start; $i<=$end; $i++) {
					$items[] = $i;
				}
			} else {
				for ($i=$start; $i>=$end; $i--) {
					$items[] = $i;
				}
			}
		}
		
		return $items;
	}
	
	// @desc All birthday select lists should have a 'rsform-select-box-small' class for easy styling
	public function getAttributes() {
		$attr = array();
		if ($attrs = $this->getProperty('ADDITIONALATTRIBUTES')) {
			$attr = $this->parseAttributes($attrs);
		}
		if (!isset($attr['class'])) {
			$attr['class'] = '';
		} else {
			$attr['class'] .= ' ';
		}
		$attr['class'] .= 'rsform-select-box-small';
		
		// Check for invalid here so that we can add 'rsform-error'
		if ($this->invalid[$this->processing]) {
			$attr['class'] .= ' ' . $this->fieldErrorClass;
		}
		
		// Must add an onchange event when we don't allow incorrect dates eg. 31 feb
		if (($this->processing == 'm' || $this->processing == 'y') && ($this->hasAllFields && !$this->getProperty('VALIDATION_ALLOW_INCORRECT_DATE', 'YES'))) {
			if (!isset($attr['onchange'])) {
				$attr['onchange'] = '';
			} else {
				$attr['onchange'] .= ' ';
			}
			$attr['onchange'] .= "RSFormPro.disableInvalidDates('".$this->name."');";
		}
		
		return $attr;
	}

	// process the field value after validation
	public function processBeforeStore($submissionId, &$post, &$files) {
		if (!isset($post[$this->name]))
		{
			return false;
		}

		$dateOrdering = $this->getProperty('DATEORDERING', '');
		$showDay = $this->getProperty('SHOWDAY', 'YES');
		$showMonth = $this->getProperty('SHOWMONTH', 'YES');
		$showYear = $this->getProperty('SHOWYEAR', 'YES');
		$storeLeadingZero = $this->getProperty('STORELEADINGZERO', 'NO');
		$dateSeparator = $this->getProperty('DATESEPARATOR', '/');

		$day   = strpos($dateOrdering, 'D');
		$month = strpos($dateOrdering, 'M');
		$year  = strpos($dateOrdering, 'Y');

		$value = $post[$this->name];
		$items = array();
		if ($showDay)
		{
			if ($storeLeadingZero)
			{
				$value['d'] = str_pad($value['d'], 2, '0', STR_PAD_LEFT);
			}
			$items[$day] = $value['d'];
		}
		if ($showMonth)
		{
			if ($storeLeadingZero)
			{
				$value['m'] = str_pad($value['m'], 2, '0', STR_PAD_LEFT);
			}
			$items[$month] = $value['m'];
		}
		if ($showYear)
		{
			$items[$year] = $value['y'];
		}
		ksort($items);

		$hasValues = false;
		foreach ($items as $item)
		{
			if (!empty($item))
			{
				$hasValues = true;
				break;
			}
		}
		if (!$hasValues)
		{
			$value = '';
		}
		else
		{
			$value = implode($dateSeparator, $items);
		}

		$post[$this->name] = $value;
	}

	public function processValidation($validationType = 'form', $submissionId = 0)
	{
		$value 		= $this->getValue();
		$showDay 	= $this->getProperty('SHOWDAY', false);
		$showMonth 	= $this->getProperty('SHOWMONTH', false);
		$showYear 	= $this->getProperty('SHOWYEAR', false);

		// flag to check if we need to run the validation functions
		$runValidations = false;

		if ($validationType == 'directory')
		{
			// Split the field...
			$dateParts = explode($this->getProperty('DATESEPARATOR'), $value);
			$dateOrdering = $this->getProperty('DATEORDERING');

			if (!$showDay)
			{
				$dateOrdering = str_replace('D', '', $dateOrdering);
			}

			if (!$showMonth)
			{
				$dateOrdering = str_replace('M', '', $dateOrdering);
			}

			if (!$showYear)
			{
				$dateOrdering = str_replace('Y', '', $dateOrdering);
			}

			$day   = strpos($dateOrdering, 'D');
			$month = strpos($dateOrdering, 'M');
			$year  = strpos($dateOrdering, 'Y');

			$value = array();

			if ($showDay && isset($dateParts[$day]))
			{
				$value['d'] = $dateParts[$day];
			}

			if ($showMonth && isset($dateParts[$month]))
			{
				$value['m'] = $dateParts[$month];
			}

			if ($showYear && isset($dateParts[$year]))
			{
				$value['y'] = $dateParts[$year];
			}
		}

		if ($this->getProperty('REQUIRED', false))
		{
			if (($showDay && empty($value['d'])) || ($showMonth && empty($value['m'])) || ($showYear && empty($value['y'])))
			{
				return false;
			}

			$runValidations = true;
		}
		else
		{
			// the field is not required, but if a selection is made it needs to be valid
			$selections = array();

			if ($showDay)
			{
				$selections[] = !empty($value['d']) ? $value['d'] : '';
			}

			if ($showMonth)
			{
				$selections[] = !empty($value['m']) ? $value['m'] : '';
			}

			if ($showYear)
			{
				$selections[] = !empty($value['y']) ? $value['y'] : '';
			}

			$selectionsFiltered = array_filter($selections);

			// If we've selected something
			if ($selectionsFiltered)
			{
				// We've found empty values, fail validation
				if (count($selections) !== count($selectionsFiltered))
				{
					return false;
				}

				$runValidations = true;
			}
		}

		// We have all the info we need, validations only work when all fields are selected
		if ($runValidations && $showDay && $showMonth && $showYear)
		{
			if ($validationRule = $this->getProperty('VALIDATIONRULE_DATE'))
			{
				$day 	= $value['d'];
				$month 	= $value['m'];
				$year 	= $value['y'];

				// load validation rules
				$dateValidations 	 = array_flip(RSFormProHelper::getDateValidationRules(true));
				$dateValidationClass = RSFormProHelper::getDateValidationClass();

				// start checking validation rules
				if (isset($dateValidations[$validationRule]) && !call_user_func(array($dateValidationClass, $validationRule), $day, $month, $year, $this->data))
				{
					return false;
				}
			}
		}

		return true;
	}
}