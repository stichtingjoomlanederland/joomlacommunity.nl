<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class RSEPROCalendar
{
	/**
	 * Array containing the order of week days
	 * @var array
	 */
	public $weekdays = array();
	
	/**
	 * Array containing the order of short week days
	 * @var array
	 */
	public $shortweekdays = array();
	
	/**
	 * Week starts on this day
	 * @var int 
	 * @val 0,1,6
	 */
	public $weekstart = 1;
	
	/**
	 * Week ends on this day
	 * @var int 
	 * @val 6,0,5
	 */
	public $weekend = 0;
	
	/**
	 * Array containing all months
	 * @var array
	 */
	public $months = array();
	
	/**
	 * Current month
	 * @var int 
	 * @val 1-12
	 */
	public $cmonth;
	
	/**
	 * Number of days in current month
	 * @var int 
	 * @val 28-31
	 */
	public $cmonth_days;
	
	/**
	 * The first day of the month in unix format
	 * @var int 
	 */
	public $month_start_unixdate;
	
	/**
	 * The first day of the month in week format
	 * @var int 
	 * @val 1-7
	 */
	public $month_start_day;
	
	/**
	 * Current year
	 * @var int 
	 */
	public $cyear;
		
	/**
	 * Unix date used in calculations
	 * @var int 
	 */
	public $unixdate;
		
	/**
	 * If US format, starts with Sunday instead of Monday
	 * @var boolean
	 */
	public $is_us_format = false;
	
	/**
	 * If is module, shows the small calendar
	 * @var boolean
	 */
	public $is_module = false;
	
	/**
	 * If is module, set the class suffix
	 * @var string
	 */
	public $class_suffix ;
	
	/**
	 * Array of events
	 * @var array
	*/
	public $events = array();
	
	/**
	 * Array of dates that contain events
	 * @var array
	*/
	public $event_dates = array();
	
	/**
	 * Array of days
	 * @var array
	*/
	public $days = array();
	
	/**
	 * optional Itemid
	 * @var integer
	*/
	public $itemid = null;
	
	/**
	 * events container
	 * @var array
	*/
	public $_container = array();
	
	/**
	 * parameters
	 * @var prams
	*/
	public $params = null;
	
	/**
	* Initializes the calendar based on today's date
	*/
	public function __construct($events, $params, $module = false) {
		JFactory::getLanguage()->load('com_rseventspro.dates');
		
		$this->is_module	= $module;
		$this->params		= $params;
		$this->weekstart	= (int) $this->params->get('startday',1);
		$this->_container	= $events;
		
		$this->setDate();
	}
	
	/**
	* Sets the date
	* @access public
	* @return true if successful
	*/
	public function setDate($month = 0, $year = 0) {
		$now = JFactory::getDate();
		
		$this->setWeekStart($this->weekstart);
		$this->_setMonths();
		
		if ($this->is_module) {
			if ($month == 0) {
				$paramsMonth = (int) $this->params->get('startmonth',0);
				if ($paramsMonth == 0) {
					$month = (int) $now->format('n');
				} else {
					$month = $paramsMonth;
				}
			}
			
			if ($year == 0) {
				$paramsYear = (int) $this->params->get('startyear',0);
				if (empty($paramsYear)) {
					$year = (int) $now->format('Y');
				} else {
					$year = $paramsYear;
				}
			}
		} else {
			if ($month == 0) {
				$month = JFactory::getApplication()->input->getInt('month',0);
				if (!$month) {
					$paramsMonth = (int) $this->params->get('startmonth',0);
					if ($paramsMonth == 0) {
						$month = (int) $now->format('n');
					} else {
						$month = $paramsMonth;
					}
				}
			} else {
				$month = (int) $month;
			}
			
			if ($year == 0) {
				$year = JFactory::getApplication()->input->getInt('year',0);
				if (!$year) {
					$paramsYear = (int) $this->params->get('startyear',0);
					if (empty($paramsYear)) {
						$year = (int) $now->format('Y');
					} else {
						$year = $paramsYear;
					}
				}
			} else {
				$year = (int) $year;
			}
		}
		
		if (is_numeric($year) && $year >= 1970) {
			$this->cyear = (int) $year;
		} else {
			$this->cyear = (int) $now->format('Y');
		}
		
		if (is_numeric($month) && $month >= 1 && $month <= 12) {			
			$this->cmonth = (int) $month;
			$this->_setDate();
			$cmonth_days = JFactory::getDate($this->unixdate);
			$this->cmonth_days = $cmonth_days->format('t');
			
			$month_start_unixdate = JFactory::getDate($this->unixdate);
			$this->month_start_unixdate = $month_start_unixdate->format('Y-m-d H:i:s');
			$this->month_start_day = $month_start_unixdate->format('w');
			$this->_createMonthObject();
		}
		return true;
	}
	
	public function getNextMonth() {
		$date = JFactory::getDate($this->unixdate);
		$date->modify('+1 months');
		return $date->format('m');
	}
	
	public function getNextYear() {
		$date = JFactory::getDate($this->unixdate);
		$date->modify('+1 months');
		return $date->format('Y');
	}
	
	public function getPrevMonth() {
		$date = JFactory::getDate($this->unixdate);
		$date->modify('-1 months');
		return $date->format('m');
	}
	
	public function getPrevYear() {
		$date = JFactory::getDate($this->unixdate);
		$date->modify('-1 months');
		return $date->format('Y');
	}
	
	protected function setWeekStart($i) {
		switch ($i) {
			case 0:			
				if($this->is_module) {
					$this->weekdays = array(
						0 => JText::_('COM_RSEVENTSPRO_SU'),
						1 => JText::_('COM_RSEVENTSPRO_MO'),
						2 => JText::_('COM_RSEVENTSPRO_TU'),
						3 => JText::_('COM_RSEVENTSPRO_WE'),
						4 => JText::_('COM_RSEVENTSPRO_TH'),
						5 => JText::_('COM_RSEVENTSPRO_FR'),
						6 => JText::_('COM_RSEVENTSPRO_SA')
					);
				} else {
					$this->weekdays = array(
						0 => JText::_('COM_RSEVENTSPRO_SUNDAY'),
						1 => JText::_('COM_RSEVENTSPRO_MONDAY'),
						2 => JText::_('COM_RSEVENTSPRO_TUESDAY'),
						3 => JText::_('COM_RSEVENTSPRO_WEDNESDAY'),
						4 => JText::_('COM_RSEVENTSPRO_THURSDAY'),
						5 => JText::_('COM_RSEVENTSPRO_FRIDAY'),
						6 => JText::_('COM_RSEVENTSPRO_SATURDAY')
					);
					
					$this->shortweekdays = array(
						0 => JText::_('COM_RSEVENTSPRO_SU_SHORT'),
						1 => JText::_('COM_RSEVENTSPRO_MO_SHORT'),
						2 => JText::_('COM_RSEVENTSPRO_TU_SHORT'),
						3 => JText::_('COM_RSEVENTSPRO_WE_SHORT'),
						4 => JText::_('COM_RSEVENTSPRO_TH_SHORT'),
						5 => JText::_('COM_RSEVENTSPRO_FR_SHORT'),
						6 => JText::_('COM_RSEVENTSPRO_SA_SHORT')
					);
				}
				
				$this->weekstart = 0;
				$this->weekend = 6;
			break;
			
			case 1:				
				if($this->is_module) {
					$this->weekdays = array(
						1 => JText::_('COM_RSEVENTSPRO_MO'),
						2 => JText::_('COM_RSEVENTSPRO_TU'),
						3 => JText::_('COM_RSEVENTSPRO_WE'),
						4 => JText::_('COM_RSEVENTSPRO_TH'),
						5 => JText::_('COM_RSEVENTSPRO_FR'),
						6 => JText::_('COM_RSEVENTSPRO_SA'),
						0 => JText::_('COM_RSEVENTSPRO_SU')
					);
				} else {
					$this->weekdays = array(
						1 => JText::_('COM_RSEVENTSPRO_MONDAY'),
						2 => JText::_('COM_RSEVENTSPRO_TUESDAY'),
						3 => JText::_('COM_RSEVENTSPRO_WEDNESDAY'),
						4 => JText::_('COM_RSEVENTSPRO_THURSDAY'),
						5 => JText::_('COM_RSEVENTSPRO_FRIDAY'),
						6 => JText::_('COM_RSEVENTSPRO_SATURDAY'),
						0 => JText::_('COM_RSEVENTSPRO_SUNDAY')
					);
					
					$this->shortweekdays = array(
						1 => JText::_('COM_RSEVENTSPRO_MO_SHORT'),
						2 => JText::_('COM_RSEVENTSPRO_TU_SHORT'),
						3 => JText::_('COM_RSEVENTSPRO_WE_SHORT'),
						4 => JText::_('COM_RSEVENTSPRO_TH_SHORT'),
						5 => JText::_('COM_RSEVENTSPRO_FR_SHORT'),
						6 => JText::_('COM_RSEVENTSPRO_SA_SHORT'),
						0 => JText::_('COM_RSEVENTSPRO_SU_SHORT')
					);
				}
				
				$this->weekstart = 1;
				$this->weekend = 0;
			break;
			
			case 6:			
				if($this->is_module) {
					$this->weekdays = array(
						6 => JText::_('COM_RSEVENTSPRO_SA'),
						0 => JText::_('COM_RSEVENTSPRO_SU'),
						1 => JText::_('COM_RSEVENTSPRO_MO'),
						2 => JText::_('COM_RSEVENTSPRO_TU'),
						3 => JText::_('COM_RSEVENTSPRO_WE'),
						4 => JText::_('COM_RSEVENTSPRO_TH'),
						5 => JText::_('COM_RSEVENTSPRO_FR')
					);
				} else {
					$this->weekdays = array(
						6 => JText::_('COM_RSEVENTSPRO_SATURDAY'),
						0 => JText::_('COM_RSEVENTSPRO_SUNDAY'),
						1 => JText::_('COM_RSEVENTSPRO_MONDAY'),
						2 => JText::_('COM_RSEVENTSPRO_TUESDAY'),
						3 => JText::_('COM_RSEVENTSPRO_WEDNESDAY'),
						4 => JText::_('COM_RSEVENTSPRO_THURSDAY'),
						5 => JText::_('COM_RSEVENTSPRO_FRIDAY')
					);
					
					$this->shortweekdays = array(
						6 => JText::_('COM_RSEVENTSPRO_SA_SHORT'),
						0 => JText::_('COM_RSEVENTSPRO_SU_SHORT'),
						1 => JText::_('COM_RSEVENTSPRO_MO_SHORT'),
						2 => JText::_('COM_RSEVENTSPRO_TU_SHORT'),
						3 => JText::_('COM_RSEVENTSPRO_WE_SHORT'),
						4 => JText::_('COM_RSEVENTSPRO_TH_SHORT'),
						5 => JText::_('COM_RSEVENTSPRO_FR_SHORT')
					);
				}
			
				$this->weekstart = 6;
				$this->weekend = 5;
			break;
		}
	}
	
	protected function _setMonths() {
		$this->months = array(
			'1' => JText::_('COM_RSEVENTSPRO_JANUARY'),
			'2' => JText::_('COM_RSEVENTSPRO_FEBRUARY'),
			'3' => JText::_('COM_RSEVENTSPRO_MARCH'),
			'4' => JText::_('COM_RSEVENTSPRO_APRIL'),
			'5' => JText::_('COM_RSEVENTSPRO_MAY'),
			'6' => JText::_('COM_RSEVENTSPRO_JUNE'),
			'7' => JText::_('COM_RSEVENTSPRO_JULY'),
			'8' => JText::_('COM_RSEVENTSPRO_AUGUST'),
			'9' => JText::_('COM_RSEVENTSPRO_SEPTEMBER'),
			'10' => JText::_('COM_RSEVENTSPRO_OCTOBER'),
			'11' => JText::_('COM_RSEVENTSPRO_NOVEMBER'),
			'12' => JText::_('COM_RSEVENTSPRO_DECEMBER')
		);
	}
	
	/**
	* Sets the unix date used in calculations
	* @access private
	*/
	protected function _setDate() {
		if (strlen($this->cmonth) == 1) {
			$this->cmonth = '0'.$this->cmonth;
		}
		
		$firstdayofweek = JFactory::getDate($this->cyear.'-'.$this->cmonth.'-01 00:00:00');
		$this->unixdate = $firstdayofweek->format('Y-m-d H:i:s');
	}
	
	protected function _createMonthObject() {
		$this->_getEvents();
		
		$month = new stdClass();
		// Days in order
		$month->weekdays = $this->weekdays;
		// Number of days in month
		$month->nr_days = $this->cmonth_days;
		// Days
		$month->days = array();
		// Get now
		$nowTZ = rseventsproHelper::showdate('now','d.m.Y');
		
		// Days in previous month
		if ($this->month_start_day != $this->weekstart) {
			$day = new stdClass();
			$day->day = $this->weekstart;
		
			$i = 0;
			foreach ($this->weekdays as $position => $weekday)
				if ($position == $this->month_start_day) {
					break;
				} else {
					$i++;
				}
			
			for ($i; $i>0; $i--) {
				$day = new stdClass();

				$lmunixdate = JFactory::getDate($this->month_start_unixdate);
				$lmunixdate->modify('-'.$i.' days');
				
				$day->unixdate	= $lmunixdate->format('Y-m-d H:i:s');
				$day->day		= $lmunixdate->format('w');
				$day->week		= $lmunixdate->format('W');
				$day->class		= 'prev-month';
				$day->events	= false;
				
				if (!empty($this->event_dates[$lmunixdate->format('d.m.Y')])) {
					$day->events = $this->event_dates[$lmunixdate->format('d.m.Y')];
				}
				
				if (!empty($day->events)) {
					$day->class .= ' has-events';
				}
				
				$month->days[] = $day;
			}
		}
		
		// Days in current month
		for ($j=1; $j<=$month->nr_days; $j++) {
			$day = new stdClass();
			
			$cmonth = $this->cmonth;
			
			if (strlen($cmonth) == 1) {
				$cmonth = '0'.$cmonth;
			}
			
			$cday = $j;
			
			if (strlen($cday) == 1) {
				$cday = '0'.$cday;
			}
			
			$cmunixdate		= JFactory::getDate($this->cyear.'-'.$cmonth.'-'.$cday.' 00:00:00');
			$day->unixdate	= $cmunixdate->format('Y-m-d H:i:s');
			$day->day		= $cmunixdate->format('w');
			$day->week		= $cmunixdate->format('W');
			$day->class		= 'curr-month';
			
			if ($cmunixdate->format('d.m.Y') == $nowTZ) {
				$day->class .= ' curr-day';
			}
			
			$day->events = false;
			
			if (!empty($this->event_dates[$cmunixdate->format('d.m.Y')])) {
				$day->events = $this->event_dates[$cmunixdate->format('d.m.Y')];
			}
			
			if (!empty($day->events)) {
				$day->class .= ' has-events';
			}
			
			$month->days[] = $day;
		}
		
		// Days in next month		
		$k = 1;
		if ($day->day != $this->weekend) {
			while($day->day != $this->weekend) {
				$day = new stdClass();
				$nextmonth = $this->cmonth+1 > 12 ? ($this->cmonth+1)-12 : $this->cmonth+1;
				$nextyear  = $this->cmonth+1 > 12 ? $this->cyear+1 : $this->cyear;
				
				if (strlen($nextmonth) == 1) {
					$nextmonth = '0'.$nextmonth;
				}
				
				$cday = $k;
				
				if (strlen($cday) == 1) {
					$cday = '0'.$cday;
				}
				
				$nmunixdate		= JFactory::getDate($nextyear.'-'.$nextmonth.'-'.$cday.' 00:00:00');
				$day->unixdate	= $nmunixdate->format('Y-m-d H:i:s');
				$day->day		= $nmunixdate->format('w');
				$day->week		= $nmunixdate->format('W');
				$day->class		= 'next-month';
				$day->events	= false;
				
				if (!empty($this->event_dates[$nmunixdate->format('d.m.Y')])) {
					$day->events = $this->event_dates[$nmunixdate->format('d.m.Y')];
				}
				
				if (!empty($day->events)) {
					$day->class .= ' has-events';
				}
				
				$k++;
				
				$month->days[] = $day;
			}
		}
		
		$this->days = $month;
	}
	
	/**
	* Get the events
	* @access private
	*/
	protected function _getEvents() {
		$events		= $this->_container;
		$display	= $this->params->get('display',0);
		
		if (!empty($events)) {
			$date = JFactory::getDate($this->cyear.'-'.$this->cmonth.'-01 00:00:00');
			$end_of_month = $date->format('Y-m-t H:i:s');
			$endofmonth = JFactory::getDate($end_of_month);
			$endofmonth->modify('+691199 seconds');
			$endofmonth = $endofmonth->toUnix();
			
			$tz			= new DateTimezone(rseventsproHelper::getTimezone());
			$utc		= new DateTimezone('UTC');
			$limit 		= (int) $this->params->get('limit',3);
			
			$timezone = date_default_timezone_get();
			date_default_timezone_set('UTC');
			
			foreach ($events as $event) {
				$this->events[$event->id] = $event;
				
				$date = new DateTime($event->start, $utc);
				$date->setTimezone($tz);
				$start = $date->format('d.m.Y');
				
				// Event start date
				$this->event_dates[$start][$event->id] = $event->id;
				
				if ($event->end == '0000-00-00 00:00:00' || $event->allday) {
					continue;
				}
				
				$date = new DateTime($event->end, $utc);
				$date->setTimezone($tz);
				$end = $date->format('d.m.Y');
				
				// Event occuring dates
				if ($display == 0) {
					$unixstartdate = new DateTime($event->start, new DateTimezone('UTC'));
					$unixstartdate->setTimezone(new DateTimezone(rseventsproHelper::getTimezone()));
					$unixstartdate = $unixstartdate->format('U');
					
					$unixendate = new DateTime($event->end, new DateTimezone('UTC'));
					$unixendate->setTimezone(new DateTimezone(rseventsproHelper::getTimezone()));
					$unixendate = $unixendate->format('U');
					
					if ($unixendate > $endofmonth) {
						$unixendate = $endofmonth;
					}
					
					for ($i = $unixstartdate; $i < $unixendate; $i += 86400) {
						$date = new DateTime(date('Y-m-d H:i:s', $i), $utc);
						$date->setTimezone($tz);
						
						if (!isset($this->event_dates[$date->format('d.m.Y')])) {
							$this->event_dates[$date->format('d.m.Y')] = array();
						}
						
						if (!$this->is_module) {
							if ($limit > 0 && count($this->event_dates[$date->format('d.m.Y')]) >= $limit) {
								break;
							}
						}
						
						$this->event_dates[$date->format('d.m.Y')][$event->id] = $event->id;
					}
				}
				
				// Event end date
				if ($display == 0 || $display == 2) {
					$this->event_dates[$end][$event->id] = $event->id;
				}
			}
			
			date_default_timezone_set($timezone);
		}
	}
}