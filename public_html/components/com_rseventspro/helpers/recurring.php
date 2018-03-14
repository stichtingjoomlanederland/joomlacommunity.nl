<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSEventsProRecurring {
	
	protected static $data = array();
	protected static $instances = array();

	public function __construct($data) {
		self::$data = $data;
	}
	
	public static function getInstance($data) {
		$hash = md5(serialize($data));
		
		if (!isset(self::$instances[$hash])) {
			self::$instances[$hash] = new RSEventsProRecurring($data);
		}
		
		return self::$instances[$hash];
	}
	
	public static function getDates($onlystart = false) {
		$dates	= array();
		$repeat	= self::$data->get('interval',0);
		$type	= self::$data->get('type',0);
		$start	= self::$data->get('start','');
		$endd	= self::$data->get('endd','');
		$end	= self::$data->get('end','');
		$days	= self::$data->get('days',array());
		$also	= self::$data->get('also',array());
		$exclude= self::$data->get('exclude',array());
		
		$repeat_on_type 		= self::$data->get('repeat_on_type',0);
		$repeat_on_day			= self::$data->get('repeat_on_day',0);
		$repeat_on_day_order	= self::$data->get('repeat_on_day_order',0);
		$repeat_on_day_type		= self::$data->get('repeat_on_day_type',0);
		
		// Check if the hide seconds option is enabled
		if (strlen($start) != 19 && strlen($start) != 10) {
			$start = $start.':00';
		}
		
		if (JFactory::getApplication()->input->get('task') == 'repeats') {
			$start = JFactory::getDate($start, rseventsproHelper::getTimezone())->toSql();
		}
		
		list($repeat_end,$repeat_end_time) = explode(' ',$end,2);
		
		$start	= new DateTime($start, new DateTimezone('UTC'));
		$endd	= new DateTime($endd, new DateTimezone('UTC'));
		$stop	= new DateTime($repeat_end.' 23:59:59', new DateTimezone('UTC'));
		$diff	= $endd->format('U') - $start->format('U');
		
		$start->setTimezone(new DateTimezone(rseventsproHelper::getTimezone()));
		$stop->setTimezone(new DateTimezone(rseventsproHelper::getTimezone()));
		
		list($h, $m, $s) = explode(':', $start->format('H:i:s'), 3);
		$seconds = ($h * 3600) + ($m * 60) + $s;
		
		// Create repeating dates
		if ($repeat > 0) {
			switch($type) {
				
				//Days
				case 1:
					while ($start <= $stop) {
						$start->modify('+'.$repeat.' days');
						
						if (!in_array($start->format('w'),$days)) continue;
						if ($start > $stop) break;
						
						$dates[] = $start->format('Y-m-d H:i:s');
					}
				break;
				
				//Weeks
				case 2:
					while ($start <= $stop) {
						$start->modify('+'.($repeat * 7).' days');
						
						if ($start > $stop) break;
						
						$clone = clone($start);
						$clone->modify('this week monday');
						$clone->setTime(0,0,0);
						$clone->modify('+'.$seconds.' seconds');
						$from = clone($clone);
						
						$clone = clone($start);
						$clone->modify('this week sunday');
						$clone->setTime(0,0,0);
						$clone->modify('+'.$seconds.' seconds');
						$to = clone($clone);
						
						if ($to > $stop) {
							$to = clone($stop);
							$to->setTime(0,0,0);
							$to->modify('+'.$seconds.' seconds');
						}
						
						if (in_array($from->format('w'),$days)) {
							$dates[] = $from->format('Y-m-d H:i:s');
						}
						
						if (in_array($to->format('w'),$days)) {
							$dates[] = $to->format('Y-m-d H:i:s');
						}
						
						while ($from <= $to) {
							$from->modify('+1 days');
							
							if ($from > $to) break;
							
							if (in_array($from->format('w'),$days)) {
								$dates[] = $from->format('Y-m-d H:i:s');
							}
						}
						
						if (!in_array($start->format('w'),$days)) continue;
						
						$dates[] = $start->format('Y-m-d H:i:s');
					}
				break;
				
				//Months
				case 3:
					while ($start <= $stop) {
						//$nextMonthStart = (int) $start->format('n') + 1;
						
						$start->modify('+'.$repeat.' months');
						
						/* if ($nextMonthStart > 1 && $nextMonthStart < 12) {
							while ($start->format('n') != $nextMonthStart) {
								$start->modify('-1 days');
							}
						} */
						
						if ($start > $stop) break;
						
						if ($repeat_on_type == 0) {
							$dates[] = $start->format('Y-m-d H:i:s');
						} else {
							$clone = clone($start);
							$clone->modify('first day of this month');
							$clone->setTime(0,0,0);
							$clone->modify('+'.$seconds.' seconds');
							$from = clone($clone);
							
							$clone = clone($start);
							$clone->modify('last day of this month');
							$clone->setTime(0,0,0);
							$clone->modify('+'.$seconds.' seconds');
							$to = clone($clone);
							
							if ($to > $stop) {
								$to = clone($stop);
								$to->setTime(0,0,0);
								$to->modify('+'.$seconds.' seconds');
							}
							
							if ($repeat_on_type == 1) {
								// Repeat the event on this specific day
								if ($repeat_on_day) {
									if ($from->format('d') == $repeat_on_day) {
										$dates[] = $from->format('Y-m-d H:i:s');
									}
									
									if ($to->format('d') == $repeat_on_day) {
										$dates[] = $to->format('Y-m-d H:i:s');
									}
									
									while ($from <= $to) {
										$from->modify('+1 days');
										
										if($from > $to) break;
										
										if ($from->format('d') == $repeat_on_day) {
											$dates[] = $from->format('Y-m-d H:i:s');
										}
									}
								} else {
									$dates[] = $start->format('Y-m-d H:i:s');
								}
							} elseif ($repeat_on_type == 2) {
								// Repeat the event based on the selected scenario
								$clone = clone($start);
								$clone->modify(self::createRepeatScenario($repeat_on_day_order, $repeat_on_day_type).' of '.$start->format('F').' '.$start->format('Y'));
								$clone->setTime(0,0,0);
								$clone->modify('+'.$seconds.' seconds');
								$dates[] = $clone->format('Y-m-d H:i:s');
							}
						}
					}
				break;
				
				//Years
				case 4:
					while ($start <= $stop) {
						$start->modify('+'.$repeat.' years');
						
						if($start > $stop) break;
						
						$dates[] = $start->format('Y-m-d H:i:s');
					}
				break;
			}
		}
		
		// Remove duplicates
		$dates = array_unique($dates);
		$ends  = array();
		
		foreach ($dates as $date) {
			$cDate = new DateTime($date, new DateTimezone(rseventsproHelper::getTimezone()));
			$cDate->modify('+'.$diff.' seconds');
			$cDate->setTimezone(new DateTimezone('UTC'));
			$ends[] = $cDate->format('Y-m-d H:i:s');
		}
		
		// Convert dates to UTC timezone
		if ($dates) {
			foreach ($dates as $i => $date) {
				$tmpDate = new DateTime($date,new DateTimezone(rseventsproHelper::getTimezone()));
				$tmpDate->setTimezone(new DateTimezone('UTC'));
				$dates[$i] = $tmpDate->format('Y-m-d H:i:s');
			}
		}
		
		// Add "repeat also on" dates
		if (!empty($also)) {
			foreach ($also as $i => $day) {
				$date = new DateTime($day, new DateTimezone(rseventsproHelper::getTimezone()));
				$date->setTime(0,0,0);
				$date->modify('+'.$seconds.' seconds');
				$date->setTimezone(new DateTimezone('UTC'));
				$also[$i] = $date->format('Y-m-d H:i:s');
			}
			
			foreach ($also as $alsoDate) {
				if (!in_array($alsoDate,$dates)) {
					$dates[]	= $alsoDate;
					
					$endAlso = new DateTime($alsoDate, new DateTimezone('UTC'));
					$endAlso->modify('+'.$diff.' seconds');
					$ends[]	= $endAlso->format('Y-m-d H:i:s');
				}
			}
		}
		
		$dates	= array_unique($dates);
		$dates	= array_merge(array(),$dates);
		$ends	= array_unique($ends);
		$ends	= array_merge(array(),$ends);
		
		// Exclude dates
		if (!empty($exclude)) {
			foreach ($exclude as $j => $exclude_date) {
				$exclude_date = new DateTime($exclude_date, new DateTimezone(rseventsproHelper::getTimezone()));
				$exclude_date->setTime(0,0,0);
				$exclude_date->modify('+'.$seconds.' seconds');
				$exclude_date->setTimezone(new DateTimezone('UTC'));
				$exclude[$j] = $exclude_date->format('Y-m-d H:i:s');
			}
			
			foreach ($dates as $d => $day) {
				if (in_array($day,  $exclude)) {
					unset($dates[$d]);
					unset($ends[$d]);
				}
			}
		}
		
		$dates	= array_unique($dates);
		$dates	= array_merge(array(),$dates);
		$ends	= array_unique($ends);
		$ends	= array_merge(array(),$ends);
		
		foreach ($dates as $x => $date) {
			$ends[$date] = $ends[$x];
			unset($ends[$x]);
		}
		
		return $onlystart ? $dates : array('start' => $dates , 'end' => $ends);
	}
	
	protected static function createRepeatScenario($order, $type) {
		$string = '';
		
		if ($order == 1) {
			$string .= 'First';
		} elseif ($order == 2) {
			$string .= 'Second';
		} elseif ($order == 3) {
			$string .= 'Third';
		} elseif ($order == 4) {
			$string .= 'Fourth';
		} else {
			$string .= 'Last';
		}
		
		if ($type == 0) {
			$string .= ' Sunday';
		} elseif ($type == 1) {
			$string .= ' Monday';
		} elseif ($type == 2) {
			$string .= ' Tuesday';
		} elseif ($type == 3) {
			$string .= ' Wednesday';
		} elseif ($type == 4) {
			$string .= ' Thursday';
		} elseif ($type == 5) {
			$string .= ' Friday';
		} elseif ($type == 6) {
			$string .= ' Saturday';
		}
		
		return $string;
	}
}