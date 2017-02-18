<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

class RSFormProCalendar
{
	protected $className;
	protected $type;
	protected static $renderYUI = false;
	protected static $renderJQ = false;
	
	public function __construct($type = 'YUICalendar') {
		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/calendars/'.strtolower($type).'.php';
		$this->className = 'RSFormPro'.ucfirst($type);
		$this->type = $type;
		// load the files necessary for the calendar
		call_user_func(array($this->className, 'loadFiles'));
	}
	
	public static function getInstance($type = 'YUICalendar') {
		static $calendar = array();
		if (!isset($calendar[$type])) {
			$calendar[$type] = new RSFormProCalendar($type);
		}
		
		return $calendar[$type];
	}
	
	public function setCalendar($config) {
		call_user_func(array($this->className, 'setCalendarOptions'), $config);
	}
	
	public function printInlineScript($formId) {
		$className = $this->className;
		$calendarOptions = call_user_func(array($className, 'getCalendarOptions'));
		
		if (isset($calendarOptions[$formId])) {
			// the form calendar fields
			$script = '';
			$calendarsIds = array();
			foreach ($calendarOptions[$formId] as $calendarId => $calendarConfigs) {
				$configs = array();
				foreach ($calendarConfigs as $type=>$value) {
					if ($type == 'extra') {
						$configs[] = "extra: {".implode(',', $value)."}";
					} else {
						$configs[] = json_encode($type).':'.json_encode($value);
					}
				}
				$script .= "RSFormPro.".$this->type.".setCalendar(".$formId.", '".$calendarId."', {".implode(', ',$configs)."});\n";
				if ($this->type == 'YUICalendar') {
					$calendarsIds[] = $calendarId;
				}
			}
			if (!RSFormProCalendar::$renderYUI && $this->type == 'YUICalendar') {
				$script .= 'rsf_CALENDAR.util.Event.addListener(window, "load",RSFormPro.YUICalendar.renderCalendars);'."\n";
				$script .= 'RSFormPro.callbacks.addCallback('.$formId.', \'changePage\', [RSFormPro.YUICalendar.hideAllPopupCalendars, '.$formId.', '.json_encode($calendarsIds).']);';
				RSFormProCalendar::$renderYUI = true;
			}
			
			if (!RSFormProCalendar::$renderJQ && $this->type == 'jQueryCalendar') {
				$script .= "jQuery(document).ready(function(){\n\t RSFormPro.jQueryCalendar.renderCalendars(); });\n";
				$script .= 'RSFormPro.callbacks.addCallback('.$formId.', \'changePage\', [RSFormPro.jQueryCalendar.hideAllPopupCalendars, '.$formId.']);';
				RSFormProCalendar::$renderJQ = true;
			}
		}
		return $script;
	}
}