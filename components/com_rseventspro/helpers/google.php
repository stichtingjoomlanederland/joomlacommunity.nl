<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_SITE.'/components/com_rseventspro/helpers/Google/autoload.php';

class RSEPROGoogle
{
	/*
	*	Google client ID
	*/
	protected $_clientID;
	
	/*
	*	Google secret
	*/
	protected $_secret;
	
	/*
	*	Constructor
	*/
	
	public function __construct() {
		date_default_timezone_set('UTC');
		$this->_clientID = rseventsproHelper::getConfig('google_client_id');
		$this->_secret	 = rseventsproHelper::getConfig('google_secret');
	}
	
	/*
	*	Insert events in database
	*/
	
	public function parse() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();
		$events	= $this->getEvents();
		$jform	= JFactory::getApplication()->input->get('jform',array(),'array');
		$idcat	= isset($jform['google_category']) ? $jform['google_category'] : rseventsproHelper::getConfig('google_category','int');
		
		if (empty($idcat)) {
			$query->clear()
				->insert($db->qn('#__rseventspro_categories'))
				->set($db->qn('name').' = '.$db->q('Google Calendar'));
			
			$db->setQuery($query);
			$db->execute();
			$idcat = $db->insertid();
		}
		
		$i = 0;
		if (empty($events)) 
			return;
		
		foreach ($events as $event) {
			$start = new DateTime($event->start, new DateTimeZone($event->timezone));
			$start->setTimezone(new DateTimeZone('UTC'));
			$start = $start->format('Y-m-d H:i:s');
			
			if ($event->allday) {
				$end = $event->end;
			} else {
				$end = new DateTime($event->end, new DateTimeZone($event->timezone));
				$end->setTimezone(new DateTimeZone('UTC'));
				$end = $end->format('Y-m-d H:i:s');
			}
			
			$idlocation = isset($jform['google_location']) ? $jform['google_location'] : rseventsproHelper::getConfig('google_location','int');
			
			//check if the current event was already added
			$query->clear()
				->select('COUNT(id)')
				->from($db->qn('#__rseventspro_sync'))
				->where($db->qn('id').' = '.$db->q($event->id))
				->where($db->qn('from').' = '.$db->q('gcalendar'));
				
			$db->setQuery($query);
			$indb = $db->loadResult();
			
			if(!empty($indb)) 
				continue;
			
			if (empty($idlocation)) {
				$location = !empty($event->location) ? $event->location : 'Google calendar location';
				
				$query->clear()
					->insert($db->qn('#__rseventspro_locations'))
					->set($db->qn('name').' = '.$db->q($location))
					->set($db->qn('address').' = '.$db->q($location));
				
				$db->setQuery($query);
				$db->execute();
				$idlocation = $db->insertid();
			}
			
			$query->clear()
				->insert($db->qn('#__rseventspro_events'))
				->set($db->qn('location').' = '.$db->q($idlocation))
				->set($db->qn('owner').' = '.$db->q($user->get('id')))
				->set($db->qn('name').' = '.$db->q($event->name))
				->set($db->qn('description').' = '.$db->q($event->description))
				->set($db->qn('start').' = '.$db->q($start))
				->set($db->qn('end').' = '.$db->q($end))
				->set($db->qn('allday').' = '.$db->q((int) $event->allday))
				->set($db->qn('timezone').' = '.$db->q($event->timezone))
				->set($db->qn('options').' = '.$db->q(rseventsproHelper::getDefaultOptions()))
				->set($db->qn('completed').' = '.$db->q(1))
				->set($db->qn('published').' = '.$db->q(1));
			
			$db->setQuery($query);
			$db->execute();
			$idevent = $db->insertid();
			
			$query->clear()
				->insert($db->qn('#__rseventspro_taxonomy'))
				->set($db->qn('ide').' = '.$db->q($idevent))
				->set($db->qn('id').' = '.$db->q($idcat))
				->set($db->qn('type').' = '.$db->q('category'));
			
			$db->setQuery($query);
			$db->execute();
			
			$query->clear()
				->insert($db->qn('#__rseventspro_sync'))
				->set($db->qn('id').' = '.$db->q($event->id))
				->set($db->qn('ide').' = '.$db->q($idevent))
				->set($db->qn('from').' = '.$db->q('gcalendar'));
			
			$db->setQuery($query);
			$db->execute();
			
			$i++;
		}
		
		return $i;
	}
	
	/*
	*	Get auth URL
	*/
	public function getAuthURL() {
		$client = new Google_Client();
		$client->setClientId($this->_clientID);
		$client->setClientSecret($this->_secret);
		$client->setRedirectUri(JUri::root().'administrator/index.php?option=com_rseventspro&task=settings.google');
		$client->addScope('https://www.googleapis.com/auth/calendar');
		
		return $client->createAuthUrl();
	}
	
	/*
	*	Get and parse events
	*/
	
	protected function getEvents() {
		$eventList = array();
		$return = array();
		
		$client = new Google_Client();
		$client->setClientId($this->_clientID);
		$client->setClientSecret($this->_secret);
		$client->setRedirectUri(JUri::root().'administrator/index.php?option=com_rseventspro&task=settings.google');
		$client->addScope('https://www.googleapis.com/auth/calendar');
		
		if (isset($_GET['code'])) {
			$client->authenticate($_GET['code']);
			$client->setAccessToken($client->getAccessToken());
			
			$service = new Google_Service_Calendar($client);
		
			if ($client->getAccessToken()) {
				$calendarIDs = array();
				$calendarList = $service->calendarList->listCalendarList();

				while(true) {
					foreach ($calendarList->getItems() as $calendarListEntry) {
						$calendarIDs[$calendarListEntry->id] = $calendarListEntry->getSummary();
					}
					
					if ($pageToken = $calendarList->getNextPageToken()) {
						$optParams = array('pageToken' => $pageToken);
						$calendarList = $service->calendarList->listCalendarList($optParams);
					} else {
						break;
					}
				}

				foreach ($calendarIDs as $id => $name) {
					$events = $service->events->listEvents($id);

					while(true) {
						foreach ($events->getItems() as $event) {
							$eventList[] = $event;
						}
						
						if ($pageToken = $events->getNextPageToken()) {
							$optParams = array('pageToken' => $pageToken);
							$events = $service->events->listEvents($id, $optParams);
						} else {
							break;
						}
					}
				}
			}
		}
		
		if (!empty($eventList)) {
			foreach ($eventList as $item) {
				$timezone			= rseventsproHelper::getTimezone();
				$event				= new stdClass();
				$event->id			= $item->id;
				$event->name		= $item->summary;
				$event->description = $item->description;
				$event->location	= isset($item->location) ? $item->location : '';
				$allday				= false;
				$recurringEvents	= false;
				
				if (isset($item->start)) {
					if (isset($item->start->date)) {
						$start	= new DateTime($item->start->date, new DateTimeZone('UTC'));
						$start	= $start->format('Y-m-d H:i:s');
						
						if (isset($item->end->date)) {
							$end	= new DateTime($item->end->date, new DateTimeZone('UTC'));
							$end->modify('-1 second');
							$end	= $end->format('Y-m-d H:i:s');
						} else {
							$allday	= true;
							$end = JFactory::getDBO()->getNullDate();
						}
					} elseif(isset($item->start->dateTime)) {
						$start	= new DateTime($item->start->dateTime, new DateTimeZone('UTC'));
						$start	= $start->format('Y-m-d H:i:s');
						$end	= new DateTime($item->end->dateTime, new DateTimeZone('UTC'));
						$end	= $end->format('Y-m-d H:i:s');
						
						if (isset($item->start->timeZone)) {
							$timezone = $item->start->timeZone;
						} elseif (isset($item->end->timeZone)) {
							$timezone = $item->end->timeZone;
						}
					} else {
						// There is no start date
						continue;
					}
					
					$event->start = $start;
					$event->end = $end;
					$event->timezone = $timezone;
				} else {
					// No start date at all, skip this event
					continue;
				}
				
				$event->allday = $allday;
				
				if (isset($item->recurrence)) {
					$recurrenceRules = $item->recurrence[0];
					$recurrenceRules = str_replace('RRULE:','', $recurrenceRules);
					if ($recurrenceRules = explode(';', $recurrenceRules)) {
						$rule = new stdClass();
						foreach ($recurrenceRules as $recurrenceRule) {
							list($key, $value) = explode('=',$recurrenceRule);
							$rule->$key = $value;
						}
						
						if ($rule) {
							$recurring = new RSEPROGoogleCalendarRecurrence($event, $rule);
							$recurringEvents = $recurring->events();
						}
					}
				}
				
				if ($recurringEvents) {
					foreach ($recurringEvents as $recurringEvent) {
						$return[] = $recurringEvent;
					}
				} else {
					$return[] = $event;
				}
			}
		}
		
		return $return;
	}
}

class RSEPROGoogleCalendarRecurrence {
	
	protected $event;
	protected $start;
	protected $length;
	
	protected $interval = 1;
	protected $frequency;
	protected $count;
	protected $end;
	
	protected $dayName;
	protected $weekNumber;
	protected $daysOfWeek;
	
	public function __construct($event = null, $rule = null) {
		if (is_null($event) || is_null($rule)) {
			return false;
		}
		
		$this->event 	= $event;
		$this->start 	= $event->start;
		$this->nullDate	= JFactory::getDbo()->getNullDate();
		
		if ($event->allday) {
			$this->length = 0;
		} else {
			$end = new DateTime($event->end, new DateTimeZone('UTC'));
			$start = new DateTime($event->start, new DateTimeZone('UTC'));
			$this->length = $end->format('U') - $start->format('U');
		}
		
		// Parse the rules
		$this->parseRules($rule);
	}
	
	// Get events based on the given rule
	public function events() {
		$events = array();
		
		if ($dates = $this->getReccuringDates()) {
			foreach ($dates as $date) {
				$event	= new stdClass();
				$event->id 			= md5($this->event->id.$date['start'].$date['end']);			
				$event->name 		= $this->event->name;		
				$event->description = $this->event->description; 
				$event->location 	= $this->event->location;
				$event->allday	 	= $this->event->allday;
				$event->timezone 	= $this->event->timezone;
				$event->start 		= $date['start'];
				$event->end			= $date['end'];
				
				$events[] = $event;
			}
		}
		
		return $events;
	}
	
	// Parse the recurring rules
	protected function parseRules($rule) {
		// Set the interval
		if (isset($rule->INTERVAL)) {
			$this->interval = (int) $rule->INTERVAL;
		}
		
		// Set the repeats counter
		if (isset($rule->COUNT)) {
			$this->count = (int) $rule->COUNT;
		}
		
		// Set repeat UNTIL date
		if (isset($rule->UNTIL)) {
			$until =  $rule->UNTIL;
			
			$lastDate = new DateTime($until, new DateTimeZone('UTC'));
			$this->end = $lastDate->format('Y-m-d H:i:s');
		}
		
		if (!isset($rule->COUNT) && !isset($rule->UNTIL)) {
			$this->count = 10;
		}
		
		if (strtoupper($rule->FREQ) == 'MONTHLY') {
			$start			= new DateTime($this->start, new DateTimeZone('UTC'));
			$this->dayName	= $start->format('l');
			$day 			= strtoupper(substr($this->dayName, 0 , 2));
			
			if (isset($rule->BYDAY)) {
				$this->weekNumber = (int) str_replace($day, '', $rule->BYDAY);
			}
		}
		
		if (strtoupper($rule->FREQ) == 'WEEKLY') {
			if (isset($rule->BYDAY)) {
				$daysOfWeek  = explode(',', $rule->BYDAY);
				$this->daysOfWeek  = $this->arrangeDays($daysOfWeek);
			}
		}
		
		$this->frequency = strtoupper($rule->FREQ);
	}
	
	// Get recurring dates
	protected function getReccuringDates() {
		if ($this->frequency == 'DAILY') {
			return $this->buildDatesDay($this->start);
		} elseif ($this->frequency == 'WEEKLY') {
			if (!is_null($this->count)) {
				return $this->buildDatesWeekCount($this->start, 0, 0);
			} else {
				return $this->buildDatesWeekEndDate($this->start, 0);
			}
		} elseif ($this->frequency == 'MONTHLY') {
			return $this->buildDatesMonth($this->start);
		} elseif ($this->frequency == 'YEARLY') {
			return $this->buildDatesYear($this->start);
		}
		
		return false;
	}
	
	protected function buildDatesDay($start) {
		$dates = array();
		
		if (!is_null($this->count)) {
			$count = 0;
			
			while($count < $this->count) {
				$dates[] = $this->createReccuringEventDates($start);
				$start	 = $this->getNextAvailableDayDate($start);
				$count++;
			}
		} else {
			$start = new DateTime($start, new DateTimeZone('UTC'));
			$end   = new DateTime($this->end, new DateTimeZone('UTC'));
			
			while($start <= $end) {
				$dates[] 	= $this->createReccuringEventDates($start->format('Y-m-d H:i:s'));
				$nextstart	= $this->getNextAvailableDayDate($start->format('Y-m-d H:i:s'));
				$start	 	= new DateTime($nextstart, new DateTimeZone('UTC'));
			}
		}
		
		return $dates;
	}
	
	protected function buildDatesWeekCount($start, $countInterval = 0, $countEvents = 0) {
		$dates = array();
		
		$startDate		= new DateTime($start, new DateTimeZone('UTC'));
		$firstDayStart 	= substr($startDate->format('D'), 0, 2);
		$firstDayStart 	= strtoupper($firstDayStart);
		
		if ($this->daysOfWeek) {
			foreach ($this->daysOfWeek as $day) {
				if ($countEvents < $this->count) {
					if ($firstDayStart == $day) {
						if ($countInterval % $this->interval == 0) {
							$dates[] = $this->createReccuringEventDates($start);
							$countEvents++;
						}
					} else {
						if ($countInterval % $this->interval == 0) {
							$dayDifference = $this->getDayDifference($firstDayStart, $day);
							$newEventStartDate = new DateTime($start, new DateTimeZone('UTC'));
							$newEventStartDate->modify('+'.$dayDifference.' days');
							$newEventStartDate = $newEventStartDate->format('Y-m-d H:i:s');
							
							if ($newEventStartDate >= $start){
								$dates[] = $this->createReccuringEventDates($newEventStartDate);
								$countEvents++;
							}
						}
					}
				}
			}
		}
		
		if ($countEvents < $this->count) {
			$mondayDiff = 7 - $this->getDayDifference('MO', $firstDayStart);
			$nextWeekMondayDate = new DateTime($start, new DateTimeZone('UTC'));
			$nextWeekMondayDate->modify('+'.$mondayDiff.' days');
			$nextWeekMondayDate = $nextWeekMondayDate->format('Y-m-d H:i:s');
			$countInterval++;
			
			return array_merge($dates, $this->buildDatesWeekCount($nextWeekMondayDate, $countInterval, $countEvents));
		} else {
			return $dates;
		}
	}
	
	protected function buildDatesWeekEndDate($start, $countInterval) {
		$startDate		= new DateTime($start, new DateTimeZone('UTC'));
		$firstDayStart 	= substr($startDate->format('D'), 0, 2);
		$firstDayStart 	= strtoupper($firstDayStart);
		
		$dates = array();
		$goUntil = true;
		
		if ($this->daysOfWeek) {
			foreach ($this->daysOfWeek as $day) {
				if ($firstDayStart == $day) {
					if ($countInterval % $this->interval == 0) {
						$startDateObj = new DateTime($start, new DateTimeZone('UTC'));
						$endDateObj = new DateTime($this->end, new DateTimeZone('UTC'));
						
						if ($startDateObj <= $endDateObj) {
							$dates[] = $this->createReccuringEventDates($start);
						} else {
							$goUntil = false;
						}
					}
				} else {
					if ($countInterval % $this->interval == 0) {
						$dayDifference = $this->getDayDifference($firstDayStart, $day);
						$newEventStartDate = new DateTime($start, new DateTimeZone('UTC'));
						$newEventStartDate->modify('+'.$dayDifference.' days');
						$startDateObj = new DateTime($start, new DateTimeZone('UTC'));
						$endDateObj = new DateTime($this->end, new DateTimeZone('UTC'));
						
						if ($newEventStartDate >= $startDateObj){
							if ($newEventStartDate <= $endDateObj) {
								$dates[] = $this->createReccuringEventDates($newEventStartDate->format('Y-m-d H:i:s'));
							} else {
								$goUntil = false;
							}
						}
					}
				}
			}
		}
		
		if ($goUntil) {
			$mondayDiff = 7 - $this->getDayDifference('MO', $firstDayStart);
			$nextWeekMondayDate = new DateTime($start, new DateTimeZone('UTC'));
			$nextWeekMondayDate->modify('+'.$mondayDiff.' days');
			$nextWeekMondayDate = $nextWeekMondayDate->format('Y-m-d H:i:s');
			$countInterval++;
			
			return array_merge($dates, $this->buildDatesWeekEndDate($nextWeekMondayDate, $countInterval));
		} else {
			return $dates;
		}
	}
	
	protected function buildDatesMonth($start) {
		$dates = array();
		
		if (!is_null($this->count)) {
			$countEvents = 0;
			while($countEvents < $this->count) {
				$dates[] = $this->createReccuringEventDates($start);
				
				if (is_null($this->weekNumber)) {
					$start = $this->getNextAvailableMonthDate($start);
				} else {
					$start = $this->getNextWeekNumberDate($start);
				}
				
				$countEvents++;
			}
		} else {
			$start	= new DateTime($start, new DateTimeZone('UTC'));
			$end	= new DateTime($this->end, new DateTimeZone('UTC'));
			$end->setTime($start->format('H'), $start->format('i'), $start->format('s'));
			
			while($start <= $end) {
				$dates[] = $this->createReccuringEventDates($start->format('Y-m-d H:i:s'));
				
				if (is_null($this->weekNumber)) {
					$nextstart = $this->getNextAvailableMonthDate($start->format('Y-m-d H:i:s'));
				} else {
					$nextstart = $this->getNextWeekNumberDate($start->format('Y-m-d H:i:s'));
				}
				
				$start = new DateTime($nextstart, new DateTimeZone('UTC'));
			}
		}
		
		return $dates;
	}
	
	protected function buildDatesYear($start) {
		$dates = array();
		
		if (!is_null($this->count)) {
			$countEvents = 0;
			
			while($countEvents < $this->count) {
				$dates[] = $this->createReccuringEventDates($start);
				$start = $this->getNextAvailableYearDate($start);
				$countEvents++;
			}
		} else {
			$start	= new DateTime($start, new DateTimeZone('UTC'));
			$end	= new DateTime($this->end, new DateTimeZone('UTC'));
			
			while($start <= $end) {
				$dates[] = $this->createReccuringEventDates($start->format('Y-m-d H:i:s'));
				$nextstart = $this->getNextAvailableYearDate($start->format('Y-m-d H:i:s'));
				$start = new DateTime($nextstart, new DateTimeZone('UTC'));
			}
		}
		
		return $dates;
	}
	
	protected function getNextAvailableYearDate($date) {
		$splitDate	= explode(' ', $date);
		$onlyDate	= explode('-', $splitDate[0]);
		
		$year	= (int) $onlyDate[0];
		$month	= (int) $onlyDate[1];
		$day	= (int) $onlyDate[2];
		
		$newYear = $year + $this->interval;
		$newDate = $newYear.'-'.$month.'-'.$day.' '.$splitDate[1]; 
		
		if (checkdate($month, $day, $newYear)) {
			$dateObj = new DateTime($newDate, new DateTimeZone('UTC'));
			return $dateObj->format('Y-m-d H:i:s');
		} else {
			return $this->getNextAvailableYearDate($newDate);
		}
	}
	
	protected function getNextAvailableMonthDate($date) {
		$globalDate	= explode(' ', $date);
		$splitDate	= explode('-', $globalDate[0]);
		$year		= (int) $splitDate[0];
		$month		= (int) $splitDate[1];
		$day		= (int) $splitDate[2];
		
		if (($month + $this->interval) > 12)	{
			$calc = ($month + $this->interval) - 12;
			if ($calc == 0) {
				$plusMonth = $this->interval % 12;
				$plusYear = 0;
			} else {
				$plusMonth = $calc % 12;
				$plusYear = floor(($month + $this->interval) / 12);
			}	
		} else {
			$plusYear = 0;
			$plusMonth = $this->interval;
		}
		
		$year		= $year + $plusYear;
		$month		= ($month + $this->interval) > 12 ? $plusMonth : ($month + $plusMonth);
		$newDate	= $year.'-'.$month.'-'.$day.' '.$globalDate[1];
		
		if (checkdate($month, $day, $year)) {
			$dateObj = new DateTime($newDate, new DateTimeZone('UTC'));
			return $dateObj->format('Y-m-d H:i:s');
		} else {
			return $this->getNextAvailableMonthDate($newDate);
		}
	}
	
	protected function getNextWeekNumberDate($start) {
		$dates		= array();
		$startObj	= new DateTime($start, new DateTimeZone('UTC'));
		$hours		= $startObj->format('H:i:s');
		$formats	= array('this', 'next', 'second', 'third', 'fourth', 'last');
		
		$nextMonthDate = new DateTime($start, new DateTimeZone('UTC'));
		$nextMonthDate->modify('+'.$this->interval.' months');
		$nextMonthDate = $nextMonthDate->format('Y-m');
		
		$lastNextMonthDate = new DateTime($start, new DateTimeZone('UTC'));
		$lastNextMonthDate->modify('+'.($this->interval + 1).' months');
		$lastNextMonthDate = $lastNextMonthDate->format('Y-m');
		
		foreach($formats as $format) {
			$dates[] = date('Y-m-d', strtotime($format.' '.$this->dayName, strtotime(($format == 'last' ? $lastNextMonthDate : $nextMonthDate))));
		}
		
		$dates = array_unique($dates);
		$dates = array_values($dates);
		
		if ($this->weekNumber > 0) {
			return $dates[($this->weekNumber -1)].' '.$hours;
		} else {
			return array_pop($dates).' '.$hours;
		}
	}
	
	protected function getNextAvailableDayDate($date) {
		$date = new DateTime($date, new DateTimeZone('UTC'));
		$date->modify('+'.$this->interval.' days');
		
		return $date->format('Y-m-d H:i:s');
	}
	
	protected function createReccuringEventDates($start) {
		if ($this->length) {
			$end = new DateTime($start, new DateTimeZone('UTC'));
			$end->modify('+ '.$this->length.' seconds');
			$end = $end->format('Y-m-d H:i:s');
		} else {
			$end = $this->nullDate;
		}
		
		return array(
			'start' => $start,
			'end'	=> $end
		);
	}
	
	protected function getDayDifference($startDay, $currentDay) {
		$startDay	= strtoupper($startDay);
		$currentDay = strtoupper($currentDay);
		$days		= array('MO' => 1, 'TU' => 2, 'WE' => 3, 'TH' => 4, 'FR' => 5, 'SA' => 6, 'SU' => 7);

		return $days[$currentDay] - $days[$startDay];
	}
	
	protected function arrangeDays($array) {
		$tmp	= array();
		$order	= array('MO','TU','WE','TH','FR','SA','SU');
		
		foreach ($order as $key) {
			if (in_array($key, $array)) {
				$tmp[] = $key;
			}
		}
		
		return $tmp;
	}
}