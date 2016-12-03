<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

class RSFormProYUICalendar
{
	static $calendarOptions = array(); // store the javascript settings for each calendar
	
	static $translationTable = array
	(
		'd' => 'dd',
		'j' => 'd',
		'D' => 'ddd',
		'l' => 'dddd',
		'F' => 'mmmm',
		'm' => 'mm',
		'M' => 'mmm',
		'n' => 'm',
		'Y' => 'yyyy',
		'y' => 'yy',
		'a' => 'tt',
		'A' => 'TT',
		'g' => 'h',
		'G' => 'H',
		'h' => 'hh',
		'H' => 'HH',
		'i' => 'MM',
		's' => 'ss',
	);
	
	public static function loadFiles() {
		RSFormProAssets::addScript(JHtml::script('com_rsform/calendar/calendar.js', false, true, true));
		RSFormProAssets::addScript(JHtml::script('com_rsform/calendar/script.js', false, true, true));
		RSFormProAssets::addStyleSheet(JHtml::stylesheet('com_rsform/calendar/calendar.css', array(), true, true));
		if (JFactory::getDocument()->direction == 'rtl') {
			RSFormProAssets::addStyleSheet(JHtml::stylesheet('com_rsform/calendar/calendar-rtl.css', array(), true, true));
		}
		
		$out = "\n";
		
		$m_short = $m_long = array();
		for ($i=1; $i<=12; $i++)
		{
			$m_short[] = '"'.JText::_('RSFP_CALENDAR_MONTHS_SHORT_'.$i, true).'"';
			$m_long[] = '"'.JText::_('RSFP_CALENDAR_MONTHS_LONG_'.$i, true).'"';
		}
		$w_1 = $w_short = $w_med = $w_long = array();
		for ($i=0; $i<=6; $i++)
		{
			$w_1[] = '"'.JText::_('RSFP_CALENDAR_WEEKDAYS_1CHAR_'.$i, true).'"';
			$w_short[] = '"'.JText::_('RSFP_CALENDAR_WEEKDAYS_SHORT_'.$i, true).'"';
			$w_med[] = '"'.JText::_('RSFP_CALENDAR_WEEKDAYS_MEDIUM_'.$i, true).'"';
			$w_long[] = '"'.JText::_('RSFP_CALENDAR_WEEKDAYS_LONG_'.$i, true).'"';
		}
		
		$out .= 'RSFormPro.YUICalendar.settings.MONTHS_SHORT 	 = ['.implode(',', $m_short).'];'."\n";
		$out .= 'RSFormPro.YUICalendar.settings.MONTHS_LONG 	 = ['.implode(',', $m_long).'];'."\n";
		$out .= 'RSFormPro.YUICalendar.settings.WEEKDAYS_1CHAR  = ['.implode(',', $w_1).'];'."\n";
		$out .= 'RSFormPro.YUICalendar.settings.WEEKDAYS_SHORT  = ['.implode(',', $w_short).'];'."\n";
		$out .= 'RSFormPro.YUICalendar.settings.WEEKDAYS_MEDIUM = ['.implode(',', $w_med).'];'."\n";
		$out .= 'RSFormPro.YUICalendar.settings.WEEKDAYS_LONG 	 = ['.implode(',', $w_long).'];'."\n";
		$out .= 'RSFormPro.YUICalendar.settings.START_WEEKDAY 	 = '.JText::_('RSFP_CALENDAR_START_WEEKDAY').';'."\n";
		
		$lang = JFactory::getLanguage();
		if ($lang->hasKey('COM_RSFORM_CALENDAR_CHOOSE_MONTH')) {
			$out .= 'RSFormPro.YUICalendar.settings.navConfig = { strings : { month: "'.JText::_('COM_RSFORM_CALENDAR_CHOOSE_MONTH', true).'", year: "'.JText::_('COM_RSFORM_CALENDAR_ENTER_YEAR', true).'", submit: "'.JText::_('COM_RSFORM_CALENDAR_OK').'", cancel: "'.JText::_('COM_RSFORM_CALENDAR_CANCEL').'", invalidYear: "'.JText::_('COM_RSFORM_CALENDAR_PLEASE_ENTER_A_VALID_YEAR', true).'" }, monthFormat: rsf_CALENDAR.widget.Calendar.LONG, initialFocus: "year" };'."\n";
		}
		
		RSFormProAssets::addScriptDeclaration($out);
	}
	
	public static function processDateFormat($dateFormat) {
		// handle the date formats
		$formats   = preg_split("/[^a-z0-9]/i", $dateFormat);
		$formats = array_filter($formats);
		$formats = array_values($formats);
		// handle the date splitters
		$splitters = preg_split("/[a-z0-9]/i", $dateFormat);
		$splitters = array_filter($splitters);
		$splitters = array_values($splitters);
		
		// rewrite the new format date format set by the user
		$newFormats = array();
		foreach ($formats as $i => $format) {
			if (isset(self::$translationTable[$format])) {
				$newFormats[] = self::$translationTable[$format];		
			} else {
				// leave this for legacy reasons if the correspondent is not found
				$newFormats[] = $format;
			}
			
			if (isset($splitters[$i])) {
				$newFormats[] = $splitters[$i];
			}		
		}
		
		if (!empty($newFormats)) {
			$dateFormat = implode('', $newFormats);
		}
		
		return trim($dateFormat);
	}
	
	public static function setCalendarOptions($config) {
		extract($config);
		
		self::$calendarOptions[$formId][$customId]['layout'] = $layout;
		self::$calendarOptions[$formId][$customId]['format'] = self::processDateFormat($dateFormat);
		self::$calendarOptions[$formId][$customId]['value'] = $value;
		
		$extras = array();
		if (!empty($minDate)) {
			$extras['mindate'] = $minDate;
		}

		if (!empty($maxDate)) {
			$extras['maxdate'] = $maxDate;
		}
		if (!empty($validationCalendar)) {
			list($rule, $otherCalendar) = explode(' ', $validationCalendar);
			$otherCalendarData =  RSFormProHelper::getComponentProperties($otherCalendar);

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