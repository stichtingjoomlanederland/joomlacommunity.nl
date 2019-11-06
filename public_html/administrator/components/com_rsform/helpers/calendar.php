<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

class RSFormProCalendar
{
	protected $calendar;
	protected $className;
	protected $type;

	public function __construct($type = 'YUICalendar') {
		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/calendars/'.strtolower($type).'.php';

		$this->className = 'RSFormPro'.ucfirst($type);
		$this->calendar = new $this->className();
		$this->type = $type;
	}
	
	public static function getInstance($type = 'YUICalendar') {
		static $calendar = array();
		if (!isset($calendar[$type])) {
			$calendar[$type] = new RSFormProCalendar($type);
		}
		
		return $calendar[$type];
	}
	
	public function setCalendar($config) {
		$this->calendar->setCalendarOptions($config);
	}
	
	public function printInlineScript($formId)
	{
		// load the files necessary for the calendar
		$this->calendar->loadFiles();

		$calendarOptions = $this->calendar->getCalendarOptions();

        $script = '';
		
		if (isset($calendarOptions[$formId]))
		{
			foreach ($calendarOptions[$formId] as $calendarId => $calendarConfigs)
			{
				$configs = array();
				foreach ($calendarConfigs as $type => $value)
				{
					if ($type == 'extra')
					{
						$configs[] = "extra: {".implode(',', $value)."}";
					}
					else
					{
						$configs[] = json_encode($type).':'.json_encode($value);
					}
				}
				$configs = implode(', ',$configs);

				$script .= "RSFormPro.{$this->type}.setCalendar({$formId}, '{$calendarId}', {{$configs}});\n";
			}

			$script .= $this->calendar->printInlineScript($formId);
		}
		return $script;
	}
	
	// DateTime::createFromFormat() doesn't support locale so we need to workaround
	public static function fixValue($value, $format)
	{
		$english = JLanguage::getInstance('en-GB');
		$english->load('com_rsform', JPATH_SITE);
		
		// l (lowercase 'L') 	A full textual representation of the day of the week 	Sunday through Saturday
		if (strpos($format, 'l') !== false)
		{
			for ($i = 0; $i <= 6; $i++)
			{
				$from 	= JText::_('RSFP_CALENDAR_WEEKDAYS_LONG_' . $i);
				$to 	= $english->_('RSFP_CALENDAR_WEEKDAYS_LONG_' . $i);
				
				if ($from !== $to)
				{
					$value = preg_replace('/\b' . preg_quote($from) . '\b/u', $to, $value);
				}
			}
		}

		// D 	A textual representation of a day, three letters 	Mon through Sun
		if (strpos($format, 'D') !== false)
		{
			for ($i = 0; $i <= 6; $i++)
			{
				$from 	= JText::_('RSFP_CALENDAR_WEEKDAYS_MEDIUM_' . $i);
				$to 	= $english->_('RSFP_CALENDAR_WEEKDAYS_MEDIUM_' . $i);
				
				if ($from !== $to)
				{
					$value = preg_replace('/\b' . preg_quote($from) . '\b/u', $to, $value);
				}
			}
		}
		
		// F 	A full textual representation of a month, such as January or March 	January through December
		if (strpos($format, 'F') !== false)
		{
			for ($i = 1; $i <= 12; $i++)
			{
				$from 	= JText::_('RSFP_CALENDAR_MONTHS_LONG_' . $i);
				$to 	= $english->_('RSFP_CALENDAR_MONTHS_LONG_' . $i);
				
				if ($from !== $to)
				{
					$value = preg_replace('/\b' . preg_quote($from) . '\b/u', $to, $value);
				}
			}
		}
		
		// M 	A short textual representation of a month, three letters 	Jan through Dec
		if (strpos($format, 'M') !== false)
		{
			for ($i = 1; $i <= 12; $i++)
			{
				$from 	= JText::_('RSFP_CALENDAR_MONTHS_SHORT_' . $i);
				$to 	= $english->_('RSFP_CALENDAR_MONTHS_SHORT_' . $i);
				
				if ($from !== $to)
				{
					$value = preg_replace('/\b' . preg_quote($from) . '\b/u', $to, $value);
				}
			}
		}
		
		return $value;
	}

	public function getPosition($formId, $componentId)
	{
		return $this->calendar->getPosition($formId, $componentId);
	}
}