<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
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
	 * Database
	 * @var db
	*/
	public $_db = null;
	
	
	
	/**
	* Initializes the calendar based on today's date
	*/
	public function __construct($events, $params, $module = false) {
		JFactory::getLanguage()->load('com_rseventspro.dates');
		
		$this->_db			= JFactory::getDbo();
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
		$this->setWeekStart($this->weekstart);
		$this->_setMonths();
		
		$now = rseventsproHelper::date('now',null,false,true);
		$now->setTZByID($now->getTZID());
		$now->convertTZ(new RSDate_Timezone('GMT'));
		
		if ($month == 0) {
			$month = JFactory::getApplication()->input->getInt('month',0);
			if (!$month)
				$month = (int) $now->formatLikeDate('n');
		} else $month = (int) $month;
		
		if ($year == 0) {
			$year = JFactory::getApplication()->input->getInt('year',0);
			if (!$year)
				$year = (int) $now->formatLikeDate('Y');
		} else $year = (int) $year;
		
		if (is_numeric($year) && $year >=1970)
			$this->cyear = (int) $year;
			
		if (is_numeric($month) && $month >= 1 && $month <= 12) {			
			$this->cmonth = (int) $month;
			$this->_setDate();
			$cmonth_days = rseventsproHelper::date($this->unixdate,null,false,true);
			$cmonth_days->setTZByID($cmonth_days->getTZID());
			$cmonth_days->convertTZ(new RSDate_Timezone('GMT'));
			$this->cmonth_days = $cmonth_days->formatLikeDate('t');
			
			$month_start_unixdate = rseventsproHelper::date($this->unixdate,null,false,true);
			$month_start_unixdate->setTZByID($month_start_unixdate->getTZID());
			$month_start_unixdate->convertTZ(new RSDate_Timezone('GMT'));
			
			$this->month_start_unixdate = $month_start_unixdate->formatLikeDate('Y-m-d H:i:s');
			$this->month_start_day = $month_start_unixdate->formatLikeDate('w');
			$this->_createMonthObject();
		}
		return true;
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
				} else  {
					$this->weekdays = array(
						6 => JText::_('COM_RSEVENTSPRO_SATURDAY'),
						0 => JText::_('COM_RSEVENTSPRO_SUNDAY'),
						1 => JText::_('COM_RSEVENTSPRO_MONDAY'),
						2 => JText::_('COM_RSEVENTSPRO_TUESDAY'),
						3 => JText::_('COM_RSEVENTSPRO_WEDNESDAY'),
						4 => JText::_('COM_RSEVENTSPRO_THURSDAY'),
						5 => JText::_('COM_RSEVENTSPRO_FRIDAY')
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
		
		$firstdayofweek = rseventsproHelper::date($this->cyear.'-'.$this->cmonth.'-01 00:00:00',null,false,true);
		$firstdayofweek->setTZByID($firstdayofweek->getTZID());
		$firstdayofweek->convertTZ(new RSDate_Timezone('GMT'));
		
		$this->unixdate = $firstdayofweek->formatLikeDate('Y-m-d H:i:s');
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
		$now = rseventsproHelper::date('now',null,false,true);
		$now->setTZByID($now->getTZID());
		$now->convertTZ(new RSDate_Timezone('GMT'));
		
		// Days in previous month
		if ($this->month_start_day != $this->weekstart) {
			$day = new stdClass();
			$day->day = $this->weekstart;
		
			$i = 0;
			foreach ($this->weekdays as $position => $weekday)
				if ($position == $this->month_start_day)
					break;
				else
					$i++;
			
			for ($i; $i>0; $i--) {
				$day = new stdClass();
				$lmunixdate = rseventsproHelper::date($this->month_start_unixdate,null,false,true);
				$lmunixdate->setTZByID($lmunixdate->getTZID());
				$lmunixdate->convertTZ(new RSDate_Timezone('GMT'));
				$lmunixdate->subtractSeconds($i * 86400);				
				
				$day->unixdate = $lmunixdate->formatLikeDate('Y-m-d H:i:s');
				$day->day = $lmunixdate->formatLikeDate('w');
				$day->week = $lmunixdate->formatLikeDate('W');
				$day->class = 'prev-month';
				$day->events = false;
				if (!empty($this->event_dates[$lmunixdate->formatLikeDate('d.m.Y')]))
					$day->events = $this->event_dates[$lmunixdate->formatLikeDate('d.m.Y')];
				if (!empty($day->events))
					$day->class .= ' has-events';
				
				$month->days[] = $day;
			}
		}
		
		// Days in current month
		for ($j=1; $j<=$month->nr_days; $j++) {
			$day = new stdClass();
			
			$cmonth = $this->cmonth;
			if (strlen($cmonth) == 1)
				$cmonth = '0'.$cmonth;
			
			$cday = $j;
			if (strlen($cday) == 1)
				$cday = '0'.$cday;
			
			$cmunixdate = rseventsproHelper::date($this->cyear.'-'.$cmonth.'-'.$cday.' 00:00:00',null,false,true);
			$cmunixdate->setTZByID($cmunixdate->getTZID());
			$cmunixdate->convertTZ(new RSDate_Timezone('GMT'));
			
			$day->unixdate = $cmunixdate->formatLikeDate('Y-m-d H:i:s');
			$day->day = $cmunixdate->formatLikeDate('w');
			$day->week = $cmunixdate->formatLikeDate('W');
			$day->class = 'curr-month';
			if ($cmunixdate->formatLikeDate('d.m.Y') == $now->formatLikeDate('d.m.Y'))
				$day->class .= ' curr-day';
			$day->events = false;
				if (!empty($this->event_dates[$cmunixdate->formatLikeDate('d.m.Y')]))
					$day->events = $this->event_dates[$cmunixdate->formatLikeDate('d.m.Y')];
			if (!empty($day->events))
				$day->class .= ' has-events';
			
			$month->days[] = $day;
		}
		
		// Days in next month		
		$k = 1;
		if ($day->day != $this->weekend)
			while($day->day != $this->weekend) {
				$day = new stdClass();
				$nextmonth = $this->cmonth+1 > 12 ? ($this->cmonth+1)-12 : $this->cmonth+1;
				$nextyear  = $this->cmonth+1 > 12 ? $this->cyear+1 : $this->cyear;
				
				if (strlen($nextmonth) == 1)
					$nextmonth = '0'.$nextmonth;
				
				$cday = $k;
				if (strlen($cday) == 1)
					$cday = '0'.$cday;
				
				$nmunixdate = rseventsproHelper::date($nextyear.'-'.$nextmonth.'-'.$cday.' 00:00:00',null,false,true);
				$nmunixdate->setTZByID($nmunixdate->getTZID());
				$nmunixdate->convertTZ(new RSDate_Timezone('GMT'));
				
				$day->unixdate = $nmunixdate->formatLikeDate('Y-m-d H:i:s');
				$day->day = $nmunixdate->formatLikeDate('w');
				$day->week = $nmunixdate->formatLikeDate('W');
				$day->class = 'next-month';
				$day->events = false;
				if (!empty($this->event_dates[$nmunixdate->formatLikeDate('d.m.Y')]))
					$day->events = $this->event_dates[$nmunixdate->formatLikeDate('d.m.Y')];
				if (!empty($day->events))
					$day->class .= ' has-events';
				$k++;
				
				$month->days[] = $day;
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
		
		$nowgmt = new RSDate();
		$nowgmt->setTZByID($nowgmt->getTZID());
		$nowgmt->convertTZ(new RSDate_Timezone('GMT'));
		$nowtimezone = new RSDate();
		$nowtimezone->setTZByID($nowtimezone->getTZID());
		$nowtimezone->convertTZ(new RSDate_Timezone(rseventsproHelper::getTimezone()));
		
		$diff = ($nowtimezone->dateDiff($nowgmt) * 86400);
		
		if (!empty($events)) {
			$Calc	= new RSDate_Calc();
			$endofmonth = $Calc->endOfMonth($this->cmonth, $this->cyear);
			$date = new RSDate($endofmonth);
			$date->addSeconds(691199);
			$endofmonth = $date->getDate(RSDATE_FORMAT_UNIXTIME);
			
			$date = rseventsproHelper::date('now',null,false,true);
			$start = rseventsproHelper::date('now',null,false,true);
			$end = rseventsproHelper::date('now',null,false,true);
			$date->setTZByID($date->getTZID());
			$date->convertTZ(new RSDate_Timezone('GMT'));
			$start->setTZByID($start->getTZID());
			$start->convertTZ(new RSDate_Timezone('GMT'));
			$end->setTZByID($end->getTZID());
			$end->convertTZ(new RSDate_Timezone('GMT'));
			
			$tz = new RSDate_Timezone(rseventsproHelper::getTimezone());
			
			foreach ($events as $event) {
				$this->events[$event->id] = $event;
				
				// Set the start and end dates
				$start->setDate($event->start);
				$end->setDate($event->end);
				
				$start->addSeconds($diff);
				$end->addSeconds($diff);
				
				$startplus = $tz->inDaylightTime($start) ? 3600 : 0;
				$endplus = $tz->inDaylightTime($end) ? 3600 : 0;
				
				$start->addSeconds($startplus);
				$end->addSeconds($endplus);
				
				$startFormat = $start->formatLikeDate('d.m.Y');
				
				// Event start date
				$this->event_dates[$startFormat][$event->id] = $event->id;
				
				if ($event->end == '0000-00-00 00:00:00' || $event->allday) {
					continue;
				}
				$endFormat = $end->formatLikeDate('d.m.Y');
				
				// Event occuring dates
				if ($display == 0) {
					$start->setHourMinuteSecond(0,0,0);
					$unixstartdate = $start->getDate(RSDATE_FORMAT_UNIXTIME);
					$end->setHourMinuteSecond(0,0,0);
					$unixendate = $end->getDate(RSDATE_FORMAT_UNIXTIME);
					
					if ($unixendate > $endofmonth) {
						$unixendate = $endofmonth;
					}
					
					for ($i = $unixstartdate; $i <= $unixendate; $i += 86400) {
						$this->event_dates[gmdate('d.m.Y',$i )][$event->id] = $event->id;
					}
				}
				
				// Event end date
				if ($display == 0 || $display == 2)
					$this->event_dates[$endFormat][$event->id] = $event->id;
			}
		}
	}
}