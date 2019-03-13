<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

class RSFormProJQueryCalendar
{
	static $calendarOptions = array(); // store the javascript settings for each calendar
	
	static $translationTable = array
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
	
	public static function loadFiles() {
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
		
		RSFormProAssets::addScriptDeclaration($out);
	}
	
	public static function processDateFormat($dateFormat) {
		$newFormat = '';
		
		for ($i = 0; $i < strlen($dateFormat); $i++)
		{
			$current = $dateFormat[$i];
			
			if (isset(self::$translationTable[$current]))
			{
				$newFormat .= self::$translationTable[$current];
			}
			else
			{
				$newFormat .= $current;
			}
		}
		
		return $newFormat;
	}
	
	public static function setCalendarOptions($config) {
		extract($config);
		
		self::$calendarOptions[$formId][$customId]['inline'] = $inline;
		self::$calendarOptions[$formId][$customId]['format'] = self::processDateFormat($dateFormat);
		self::$calendarOptions[$formId][$customId]['value'] = $value;
		self::$calendarOptions[$formId][$customId]['timepicker'] = $timepicker;
		self::$calendarOptions[$formId][$customId]['theme'] = $theme;
		if ($timepicker) {
			// in case the user leaves the input empty and save the settings
			$timepickerformat = trim($timepickerformat);
			if (empty($timepickerformat)) {
				$timepickerformat = 'H:i';
			}
			self::$calendarOptions[$formId][$customId]['timepickerformat'] = self::processDateFormat($timepickerformat);
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

		// Set the time step (Ex: 5, 10, 15, 30 minutes)
		if (!empty($timeStep)) {
			$extras['step'] = $timeStep;
		}
		
		if (!empty($validationCalendar)) {
			list($rule, $otherCalendar) = explode(' ', $validationCalendar);
			$otherCalendarData = RSFormProHelper::getComponentProperties($otherCalendar);

			$extras['rule'] = $rule.'|'.$otherCalendarData['NAME'];
		}

		$extras = self::parseJSProperties($extras);

		self::$calendarOptions[$formId][$customId]['extra'] = $extras;
	}

	protected static function parseJSProperties($extras) {
		$properties = array();
		if (count($extras)) {
			foreach ($extras as $key => $value) {
				$properties[] = json_encode($key).': '.json_encode($value);
			}
		}

		return $properties;
	}
	
	public static function getCalendarOptions() {
		return self::$calendarOptions;
	}
}