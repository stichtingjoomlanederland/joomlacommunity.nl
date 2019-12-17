<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

class RSFormProJQueryCalendar
{
	protected $calendarOptions = array(); // store the javascript settings for each calendar
	
	protected $translationTable = array
	(
		'd' => 'DD',
		'j' => 'D',
		'D' => 'ddd',
		'l' => 'dddd',
		'N' => 'e',
		'S' => 'o',
		'z' => 'DDDD',
		'F' => 'MMMM',
		'm' => 'MM',
		'M' => 'MMM',
		'n' => 'M',
		'Y' => 'YYYY',
		'y' => 'YY',
		
		'a' => 'a',
		'A' => 'A',
		'g' => 'h',
		'G' => 'H',
		'h' => 'hh',
		'H' => 'HH',
		'i' => 'mm',
		's' => 'ss',
	);
	
	public function loadFiles()
	{
		static $done;

		if ($done)
		{
			return;
		}

		// load the jQuery framework 
		JHtml::_('jquery.framework', true);
		
		RSFormProAssets::addScript(JHtml::script('com_rsform/jquerycalendar/jquery.datetimepicker.js', array('pathOnly' => true, 'relative' => true)));
		RSFormProAssets::addScript(JHtml::script('com_rsform/jquerycalendar/moment.js', array('pathOnly' => true, 'relative' => true)));
		RSFormProAssets::addScript(JHtml::script('com_rsform/jquerycalendar/script.js', array('pathOnly' => true, 'relative' => true)));
		RSFormProAssets::addStyleSheet(JHtml::stylesheet('com_rsform/jquerycalendar/jquery.datetimepicker.css', array('pathOnly' => true, 'relative' => true)));
		
		$out = "\n";
		
		$m_short = $m_long = array();
		for ($i=1; $i<=12; $i++)
		{
			$m_short[] = '"'.JText::_('RSFP_CALENDAR_MONTHS_SHORT_'.$i, true).'"';
			$m_long[] = '"'.JText::_('RSFP_CALENDAR_MONTHS_LONG_'.$i, true).'"';
		}
		$w_short = $w_med = $w_long = array();
		for ($i=0; $i<=6; $i++)
		{
			$w_short[] = '"'.JText::_('RSFP_CALENDAR_WEEKDAYS_SHORT_'.$i, true).'"';
			$w_med[] = '"'.JText::_('RSFP_CALENDAR_WEEKDAYS_MEDIUM_'.$i, true).'"';
			$w_long[] = '"'.JText::_('RSFP_CALENDAR_WEEKDAYS_LONG_'.$i, true).'"';
		}
		
		$out .= 'RSFormPro.jQueryCalendar.settings.MONTHS_SHORT 	 = ['.implode(',', $m_short).'];'."\n";
		$out .= 'RSFormPro.jQueryCalendar.settings.MONTHS_LONG 	 = ['.implode(',', $m_long).'];'."\n";
		$out .= 'RSFormPro.jQueryCalendar.settings.WEEKDAYS_SHORT  = ['.implode(',', $w_short).'];'."\n";
		$out .= 'RSFormPro.jQueryCalendar.settings.WEEKDAYS_MEDIUM = ['.implode(',', $w_med).'];'."\n";
		$out .= 'RSFormPro.jQueryCalendar.settings.WEEKDAYS_LONG 	 = ['.implode(',', $w_long).'];'."\n";
		$out .= 'RSFormPro.jQueryCalendar.settings.START_WEEKDAY 	 = '.JText::_('RSFP_CALENDAR_START_WEEKDAY').';'."\n";

		$out .= "jQuery(document).ready(function(){ RSFormPro.jQueryCalendar.renderCalendars(); });\n";
		
		RSFormProAssets::addScriptDeclaration($out);

		$done = true;
	}
	
	protected function processDateFormat($dateFormat) {
		$newFormat = '';
		
		for ($i = 0; $i < strlen($dateFormat); $i++)
		{
			$current = $dateFormat[$i];
			
			if (isset($this->translationTable[$current]))
			{
				$newFormat .= $this->translationTable[$current];
			}
			else
			{
				$newFormat .= $current;
			}
		}
		
		return $newFormat;
	}
	
	public function setCalendarOptions($config) {
		extract($config);
		
		$this->calendarOptions[$formId][$customId]['inline'] = $inline;
		$this->calendarOptions[$formId][$customId]['format'] = $this->processDateFormat($dateFormat);
		$this->calendarOptions[$formId][$customId]['value'] = $value;
		$this->calendarOptions[$formId][$customId]['timepicker'] = $timepicker;
		$this->calendarOptions[$formId][$customId]['theme'] = $theme;
		if ($timepicker) {
			// in case the user leaves the input empty and save the settings
			$timepickerformat = trim($timepickerformat);
			if (empty($timepickerformat)) {
				$timepickerformat = 'H:i';
			}
			$this->calendarOptions[$formId][$customId]['timepickerformat'] = $this->processDateFormat($timepickerformat);
		}
		
		$extras = array();

		// Set the min and max dates
		if (!empty($minDate)) {
			$extras['minDate'] = $minDate;
		}
		if (!empty($maxDate)) {
			$extras['maxDate'] = $maxDate;
		}

		// Set the min and max time
		if (!empty($minTime)) {
			$extras['minTime'] = $minTime;
		}
		if (!empty($maxTime)) {
			$extras['maxTime'] = $maxTime;
		}

		if (!empty($allowDates)) {
			$allowDates = str_replace("\r\n", "\n", $allowDates);
			$allowDates = explode("\n", $allowDates);

			$extras['allowDates'] = $allowDates;
		}

		if (!empty($allowDateRe)) {
			$extras['allowDateRe'] = $allowDateRe;
		}

		// Set the time step (Ex: 5, 10, 15, 30 minutes)
		if (!empty($timeStep)) {
			$extras['step'] = $timeStep;
		}
		
		if (!empty($validationCalendar)) {
			list($rule, $otherCalendar) = explode(' ', $validationCalendar);
			$otherCalendarData = RSFormProHelper::getComponentProperties($otherCalendar);

			$extras['rule'] = $rule.'|'.$otherCalendarData['NAME'];

			if (isset($offset) && $offset != 1)
			{
				$extras['rule'] .= '|' . (int) $offset;
			}
		}

		$extras = $this->parseJSProperties($extras);

		$this->calendarOptions[$formId][$customId]['extra'] = $extras;
	}

	protected function parseJSProperties($extras) {
		$properties = array();
		if (count($extras)) {
			foreach ($extras as $key => $value) {
				$properties[] = json_encode($key).': '.json_encode($value);
			}
		}

		return $properties;
	}
	
	public function getCalendarOptions() {
		return $this->calendarOptions;
	}
	
	public function getPosition($formId, $componentId)
	{
		static $calendars = array();

		if (!isset($calendars[$formId]))
		{
			$calendars[$formId] = RSFormProHelper::componentExists($formId, RSFORM_FIELD_JQUERY_CALENDAR);
		}

		$position = (int) array_search($componentId, $calendars[$formId]);

		return $position;
	}

	public function printInlineScript($formId)
	{
		return "RSFormPro.callbacks.addCallback({$formId}, 'changePage', [RSFormPro.jQueryCalendar.hideAllPopupCalendars, {$formId}]);";
	}
}