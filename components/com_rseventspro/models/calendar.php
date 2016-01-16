<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.model' );

class rseventsproModelCalendar extends JModelLegacy
{	
	var $_query = null;
	var $_data = null;
	var $_total = null;
	var $_db = null;
	var $_app = null;
	var $_user = null;
	var $_where = null;
	var $_join = null;
	var $_exclude = null;
	
	/**
	 *	Main constructor
	 *
	 */
	public function __construct() {
		parent::__construct();
		
		$config				= JFactory::getConfig();
		$this->_db			= JFactory::getDBO();
		$this->_app			= JFactory::getApplication();
		$this->_user		= JFactory::getUser();
		$this->_filters		= $this->getFilters();
		$this->_where		= $this->_buildWhere();
		$this->_join		= $this->_buildJoin();
		$this->_exclude		= rseventsproHelper::excludeEvents();
		$this->_query		= $this->_buildQuery();
		
		if ($this->_app->input->get('layout') == 'day' || $this->_app->input->get('layout') == 'week' || $this->_app->input->get('tpl') == 'day' || $this->_app->input->get('tpl') == 'week') {
			// Get pagination request variables
			$limit = $this->_app->getUserStateFromRequest('com_rseventspro.limit', 'limit', $config->get('list_limit'), 'int');
			$limitstart	= $this->_app->input->getInt('limitstart', 0);
			
			// In case limit has been changed, adjust it
			$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

			$this->setState('com_rseventspro.limit', $limit);
			$this->setState('com_rseventspro.limitstart', $limitstart);
		}
	}
	
	/**
	 *	Method to get All day events
	 *
	 *	@return array
	 */
	protected function _getAllDayEvents($type, $sdate = null, $edate = null) {
		$query 		= $this->_db->getQuery(true);
		$params 	= rseventsproHelper::getParams();
		
		// Parameters
		$list	= $params->get('list','all');
		$days	= (int) $params->get('days',0);
		$from	= $params->get('from','');
		$to		= $params->get('to','');
		
		// Start default query params
		if (!empty($from)) {
			if (strtolower($from) == 'today') {
				$from = rseventsproHelper::date('now',null,false,true);
				$from->setTZByID($from->getTZID());
				$from->convertTZ(new RSDate_Timezone('GMT'));
				$from->setHourMinuteSecond(0,0,0);
				$from = $from->formatLikeDate('Y-m-d H:i:s');
			} else {
				$from = rseventsproHelper::date($from,null,false,true);
				$from->setTZByID($from->getTZID());
				$from->convertTZ(new RSDate_Timezone('GMT'));
				$from = $from->formatLikeDate('Y-m-d H:i:s');
			}
		}
		
		if (!empty($to)) {
			$to = rseventsproHelper::date($to,null,false,true);
			$to->setTZByID($to->getTZID());
			$to->convertTZ(new RSDate_Timezone('GMT'));
			$to = $to->formatLikeDate('Y-m-d H:i:s');
		}
		
		$query->clear()
			->select($this->_db->qn('id'))
			->from($this->_db->qn('#__rseventspro_events'))
			->where($this->_db->qn('allday').' = 1');
		
		if ($type == 'future') {
			if ($list == 'future') {
				if ($days > 0) {
					$start = rseventsproHelper::date('now',null,false,true);
					$start->setTZByID($start->getTZID());
					$start->convertTZ(new RSDate_Timezone('GMT'));
					$start->addSeconds($days * 86400);
					
					$start	= $start->formatLikeDate('Y-m-d H:i:s');
					
					$query->where($this->_db->qn('start').' >= '.$this->_db->q($start));
				} else {
					$start = rseventsproHelper::date('now',null,false,true);
					$start->setHourMinuteSecond(0,0,0);
					$start->setTZByID($start->getTZID());
					$start->convertTZ(new RSDate_Timezone('GMT'));
					
					$end = rseventsproHelper::date('now',null,false,true);
					$end->setHourMinuteSecond(23,59,59);
					$end->setTZByID($end->getTZID());
					$end->convertTZ(new RSDate_Timezone('GMT'));
					$end->addSeconds(1);
					
					$start	= $start->formatLikeDate('Y-m-d H:i:s');
					$end	= $end->formatLikeDate('Y-m-d H:i:s');
					
					$query->where($this->_db->qn('start').' >= '.$this->_db->q($start));
					$query->where($this->_db->qn('start').' < '.$this->_db->q($end));
				}
			}
		} elseif ($type == 'from') {
			$query->where($this->_db->qn('start').' >= '.$this->_db->q($from));
		} elseif ($type == 'to') {
			$query->where($this->_db->qn('start').' <= '.$this->_db->q($to));
		} elseif ($type == 'fromto') {
			$query->where($this->_db->qn('start').' >= '.$this->_db->q($from));
			$query->where($this->_db->qn('start').' <= '.$this->_db->q($to));
		} elseif ($type == 'calendar') {
			$query->where($this->_db->qn('start').' >= '.$this->_db->q($sdate));
			$query->where($this->_db->qn('start').' <= '.$this->_db->q($edate));
		} elseif ($type == 'day') {
			$query->where($this->_db->qn('start').' >= '.$this->_db->q($sdate));
			$query->where($this->_db->qn('start').' <= '.$this->_db->q($edate));
		} elseif ($type == 'week') {
			$query->where($this->_db->qn('start').' >= '.$this->_db->q($sdate));
			$query->where($this->_db->qn('start').' <= '.$this->_db->q($edate));
		}
		
		$this->_db->setQuery($query);
		if ($events = $this->_db->loadColumn()) {
			JArrayHelper::toInteger($events);
			return $events;
		}
		
		return false;
	}
	
	/**
	 *	Method to build the events query
	 *
	 *	@return SQL query
	 */
	protected function _buildQuery() {
		$mid		= $this->_app->input->getInt('mid',0);
		$params		= $mid ? $this->getModuleParams() : rseventsproHelper::getParams();
		$categories	= $params->get('categories','');
		$locations	= $params->get('locations','');
		$tags		= $params->get('tags','');
		$order		= $params->get('ordering','start');
		$direction	= $params->get('order','DESC');
		$archived	= (int) $params->get('archived',0);
		$list		= $params->get('list','all');
		$from		= $params->get('from','');
		$to			= $params->get('to','');
		$days		= (int) $params->get('days',0);
		$repeat		= (int) $params->get('repeat',1);
		$date		= $this->_app->input->getString('date');
		$where		= array();
		
		// Start Legacy
		$uevents	= (int) $params->get('userevents',0);
		if ($uevents) $list = 'user';
		// End Legacy
		
		// Create main query
		$query = 'SELECT '.$this->_db->qn('e.id').', '.$this->_db->qn('e.name').', '.$this->_db->qn('e.start').', '.$this->_db->qn('e.end').', '.$this->_db->qn('e.allday').'  FROM '.$this->_db->qn('#__rseventspro_events','e').' ';		
		
		if (!empty($this->_join)) 
			$query .= $this->_join;
		
		$query .= " WHERE ";
		
		// Select only completed events
		$query .= ' '.$this->_db->qn('e.completed').' = 1 ';
		
		// Get the start and end date of the current month days
		if ($this->_app->input->get('layout') == '' || $this->_app->input->get('layout') == 'default') {
			$now = rseventsproHelper::date('now',null,false,true);
			$now->setTZByID($now->getTZID());
			$now->convertTZ(new RSDate_Timezone('GMT'));
			
			$month		= $this->_app->input->getInt('month',(int) $now->formatLikeDate('m'));
			$year		= $this->_app->input->getInt('year',(int) $now->formatLikeDate('Y'));
			
			if (strlen($month) == 1) 
				$month = '0'.$month;
			
			$Calc		= new RSDate_Calc();
			$startMonth	= rseventsproHelper::date($year.'-'.$month.'-01 00:00:00',null,false,true);
			$startMonth->setTZByID($startMonth->getTZID());
			$startMonth->convertTZ(new RSDate_Timezone('GMT'));
			
			$month_start_day	= $startMonth->formatLikeDate('w');
			$weekstart			= $params->get('startday',1);
			$weekdays			= $this->getWeekdays($weekstart);
			
			$prevDays = 0;
			if ($month_start_day != $weekstart) {
				foreach ($weekdays as $position)
					if ($position == $month_start_day)
						break;
					else
						$prevDays++;
			}
			
			if ($prevDays)
				$startMonth->subtractSeconds($prevDays * 86400);
			
			$endofmonth = $Calc->endOfMonth($month,$year);
			$endMonth	= rseventsproHelper::date($endofmonth,null,false,true);
			$endMonth->setTZByID($endMonth->getTZID());
			$endMonth->convertTZ(new RSDate_Timezone('GMT'));
			
			$weekend	= $this->getWeekdays($weekstart,true);
			$day		= $endMonth->formatLikeDate('w');
			
			$k = 1;
			$nextDays = 0;
			if ($day != $weekend) {
				while($day != $weekend) {
					$nextmonth = $month+1 > 12 ? ($month+1)-12 : $month+1;
					$nextyear  = $month+1 > 12 ? $year+1 : $year;
					
					if (strlen($nextmonth) == 1)
						$nextmonth = '0'.$nextmonth;
					
					$cday = $k;
					if (strlen($cday) == 1)
						$cday = '0'.$cday;
					
					$nmunixdate = rseventsproHelper::date($nextyear.'-'.$nextmonth.'-'.$cday.' 00:00:00',null,false,true);
					$nmunixdate->setTZByID($nmunixdate->getTZID());
					$nmunixdate->convertTZ(new RSDate_Timezone('GMT'));
					
					$day = $nmunixdate->formatLikeDate('w');
					
					$k++;
					$nextDays++;
				}
			}
			
			if ($nextDays)
				$endMonth->addSeconds($nextDays * 86400);
			$endMonth->addSeconds(86399);
			
			$startMonth = $startMonth->formatLikeDate('Y-m-d H:i:s');
			$endMonth = $endMonth->formatLikeDate('Y-m-d H:i:s');
			
			$includeCalendar = $this->_getAllDayEvents('calendar', $startMonth, $endMonth);
			
			if (!empty($includeCalendar)) {
				$query .= ' AND (('.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).' AND (('.$this->_db->qn('e.start').' <= '.$this->_db->q($startMonth).' AND ('.$this->_db->qn('e.end').' <= '.$this->_db->q($endMonth).' OR '.$this->_db->qn('e.end').' >= '.$this->_db->q($endMonth).')) OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($startMonth).' AND '.$this->_db->qn('e.start').' <= '.$this->_db->q($endMonth).'))) OR '.$this->_db->qn('e.id').' IN ('.implode(',',$includeCalendar).'))';
			} else {
				$query .= ' AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).' AND (('.$this->_db->qn('e.start').' <= '.$this->_db->q($startMonth).' AND ('.$this->_db->qn('e.end').' <= '.$this->_db->q($endMonth).' OR '.$this->_db->qn('e.end').' >= '.$this->_db->q($endMonth).')) OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($startMonth).' AND '.$this->_db->qn('e.start').' <= '.$this->_db->q($endMonth).')) ';
			}
		}
		
		// Show repeated events
		if (!$repeat) 
			$where[] = ' AND '.$this->_db->qn('e.parent').' = 0 ';
		
		// Get the list type
		// Get all events
		if ($list == 'all') {
			$query .= $archived ? ' AND '.$this->_db->qn('e.published').' IN (1,2) ' : ' AND '.$this->_db->qn('e.published').' = 1 ';
		} 
		// Get featured events
		else if ($list == 'featured') {
			$query .= $archived ? ' AND '.$this->_db->qn('e.published').' IN (1,2) ' : ' AND '.$this->_db->qn('e.published').' = 1 ';
			$where[] = ' AND '.$this->_db->qn('e.featured').' = 1 ';
		}
		// Get archived events
		else if ($list == 'archived') {
			$query .= ' AND '.$this->_db->qn('e.published').' = 2 ';
		} 
		// Get future events
		else if ($list == 'future') {
			$where[] = ' AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).' ';
			
			$includeFuture = $this->_getAllDayEvents('future');
		
			// Select future events
			if ($days > 0) {
				$start = rseventsproHelper::date('now',null,false,true);
				$start->setTZByID($start->getTZID());
				$start->convertTZ(new RSDate_Timezone('GMT'));
				$start->addSeconds($days * 86400);
				
				$start	= $start->formatLikeDate('Y-m-d H:i:s');
				
				if (!empty($includeFuture)) {
					$where[] = ' AND ('.$this->_db->qn('e.start').' >= '.$this->_db->q($start).' OR '.$this->_db->qn('e.id').' IN ('.implode(',',$includeFuture).')) ';
				} else {
					$where[] = ' AND '.$this->_db->qn('e.start').' >= '.$this->_db->q($start).' ';
				}
			}
			// Select today events
			else {
				$start = rseventsproHelper::date('now',null,false,true);
				$start->setTZByID($start->getTZID());
				$start->convertTZ(new RSDate_Timezone('GMT'));
				
				$end = rseventsproHelper::date('now',null,false,true);
				$end->setTZByID($end->getTZID());
				$end->convertTZ(new RSDate_Timezone('GMT'));
				$end->addSeconds(86399);
				
				$start	= $start->formatLikeDate('Y-m-d H:i:s');
				$end	= $end->formatLikeDate('Y-m-d H:i:s');
				
				if (!empty($includeFuture)) {
					$where[] = ' AND ((('.$this->_db->qn('e.start').' <= '.$this->_db->q($start).' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($start).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($start).' AND '.$this->_db->qn('e.end').' <= '.$this->_db->q($end).')) OR '.$this->_db->qn('e.id').' IN ('.implode(',',$includeFuture).')) ';
				} else {
					$where[] = ' AND (('.$this->_db->qn('e.start').' <= '.$this->_db->q($start).' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($start).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($start).' AND '.$this->_db->qn('e.end').' <= '.$this->_db->q($end).')) ';
				}
			}
			
			$query .= ' AND '.$this->_db->qn('e.published').' = 1 ';
		}
		// Get user events
		else 
		{
			if ($this->_user->get('id') > 0) {
				$where[] = ' AND '.$this->_db->qn('e.owner').' = '.(int) $this->_user->get('id').' ';
			}
			
			$query .= ' AND '.$this->_db->qn('e.published').' = 1 ';
		}
		
		if (!empty($from)) {
			if (strtolower($from) == 'today') {
				$from = rseventsproHelper::date('now',null,false,true);
				$from->setTZByID($from->getTZID());
				$from->convertTZ(new RSDate_Timezone('GMT'));
				$from->setHourMinuteSecond(0,0,0);
				$from = $from->formatLikeDate('Y-m-d H:i:s');
			} else {
				$from = rseventsproHelper::date($from,null,false,true);
				$from->setTZByID($from->getTZID());
				$from->convertTZ(new RSDate_Timezone('GMT'));
				$from = $from->formatLikeDate('Y-m-d H:i:s');
			}
		}
		
		if (!empty($to)) {
			$to = rseventsproHelper::date($to,null,false,true);
			$to->setTZByID($to->getTZID());
			$to->convertTZ(new RSDate_Timezone('GMT'));
			$to = $to->formatLikeDate('Y-m-d H:i:s');
		}
		
		if (empty($from) && !empty($to)) {
			$includeTo = $this->_getAllDayEvents('to');
		
			if (!empty($includeTo)) {
				$query .= ' AND ( ('.$this->_db->qn('e.end').' <= '.$this->_db->q($to).' AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).') OR '.$this->_db->qn('e.id').' IN ('.implode(',',$includeTo).')) ';
			} else {
				$query .= ' AND '.$this->_db->qn('e.end').' <= '.$this->_db->q($to).' AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).' ';
			}
		} elseif (!empty($from) && empty($to)) {
			$includeFrom = $this->_getAllDayEvents('from');
			
			if (!empty($includeFrom)) {
				$query .= ' AND ( ('.$this->_db->qn('e.start').' >= '.$this->_db->q($from).' AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).') OR '.$this->_db->qn('e.id').' IN ('.implode(',',$includeFrom).')) ';
			} else {
				$query .= ' AND '.$this->_db->qn('e.start').' >= '.$this->_db->q($from).' AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).' ';
			}
		} elseif (!empty($from) && !empty($to)) {
			$includeFromTo = $this->_getAllDayEvents('fromto');
			
			if (!empty($includeFromTo)) {
				$query .= ' AND ((('.$this->_db->qn('e.start').' <= '.$this->_db->q($from).' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($to).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($from).' AND '.$this->_db->qn('e.end').' <= '.$this->_db->q($to).')) OR '.$this->_db->qn('e.id').' IN ('.implode(',',$includeFromTo).')) ';
			} else {
				$query .= ' AND ((('.$this->_db->qn('e.start').' <= '.$this->_db->q($from).' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($to).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($from).' AND '.$this->_db->qn('e.end').' <= '.$this->_db->q($to).')) AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).') ';
			}
		}
		
		if (($this->_app->input->get('layout') == 'day' || $this->_app->input->get('tpl') == 'day') && !empty($date)) {
			$date = str_replace(array('-',':'),'/',$date);
			list($m,$d,$y) = explode('/',$date,3);
			
			$sdate = rseventsproHelper::date($y.'-'.$m.'-'.$d.' 00:00:00',null,false,true);
			$sdate->setTZByID($sdate->getTZID());
			$sdate->convertTZ(new RSDate_Timezone('GMT'));
			
			$edate = rseventsproHelper::date($y.'-'.$m.'-'.$d.' 00:00:00',null,false,true);
			$edate->setTZByID($edate->getTZID());
			$edate->convertTZ(new RSDate_Timezone('GMT'));
			$edate->addSeconds(86399);
		
			$start = new RSDate($sdate);
			$start->setTZByID(rseventsproHelper::getTimezone());
			$start->convertTZ(new RSDate_Timezone('GMT'));
			
			$end = new RSDate($edate);
			$end->setTZByID(rseventsproHelper::getTimezone());
			$end->convertTZ(new RSDate_Timezone('GMT'));
			
			$start = $start->formatLikeDate('Y-m-d H:i:s');
			$end = $end->formatLikeDate('Y-m-d H:i:s');
			
			$includeDay = $this->_getAllDayEvents('day',$start,$end);
			
			if (!empty($includeDay)) {
				$query .= ' AND (((('.$this->_db->qn('e.start').' <= '.$this->_db->q($start).' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($start).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($start).' AND '.$this->_db->qn('e.start').' <= '.$this->_db->q($end).')) AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).') OR '.$this->_db->qn('e.id').' IN ('.implode(',',$includeDay).')) ';
			} else {
				$query .= ' AND (('.$this->_db->qn('e.start').' <= '.$this->_db->q($start).' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($start).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($start).' AND '.$this->_db->qn('e.start').' <= '.$this->_db->q($end).')) ';
			}
		}
		
		if (($this->_app->input->get('layout') == 'week' || $this->_app->input->get('tpl') == 'week') && !empty($date)) {
			$date = str_replace(array('-',':'),'/',$date);
			list($m,$d,$y) = explode('/',$date,3);
			
			$sdate = rseventsproHelper::date($y.'-'.$m.'-'.$d.' 00:00:00',null,false,true);
			$sdate->setTZByID($sdate->getTZID());
			$sdate->convertTZ(new RSDate_Timezone('GMT'));
			
			$edate = rseventsproHelper::date($y.'-'.$m.'-'.$d.' 00:00:00',null,false,true);
			$edate->setTZByID($edate->getTZID());
			$edate->convertTZ(new RSDate_Timezone('GMT'));
			$edate->addSeconds(6*86400+86399);
			
			$start = new RSDate($sdate);
			$start->setTZByID(rseventsproHelper::getTimezone());
			$start->convertTZ(new RSDate_Timezone('GMT'));
			
			$end = new RSDate($edate);
			$end->setTZByID(rseventsproHelper::getTimezone());
			$end->convertTZ(new RSDate_Timezone('GMT'));
			
			$start = $start->formatLikeDate('Y-m-d H:i:s');
			$end = $end->formatLikeDate('Y-m-d H:i:s');
			
			$includeWeek = $this->_getAllDayEvents('week',$start, $end);
			
			if (!empty($includeWeek)) {
				$query .= ' AND (((('.$this->_db->qn('e.start').' <= '.$this->_db->q($start).' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($start).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($start).' AND '.$this->_db->qn('e.start').' <= '.$this->_db->q($end).')) AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).') OR '.$this->_db->qn('e.id').' IN ('.implode(',',$includeWeek).')) ';
			} else {
				$query .= ' AND (('.$this->_db->qn('e.start').' <= '.$this->_db->q($start).' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($start).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($start).' AND '.$this->_db->qn('e.start').' <= '.$this->_db->q($end).')) ';
			}
		}
		
		if ($category = $this->_app->input->getInt('category',0)) {
			$categories = (array) $category;
		}
		
		if ($tag = $this->_app->input->getInt('tag',0)) {
			$tags = (array) $tag;
		}
			
		if ($location = $this->_app->input->getInt('location',0)) {
			$locations = (array) $location;
		}
		
		// Select events with this specific categories
		if (!empty($categories)) {
			$categoryquery = '';
			if (JLanguageMultilang::isEnabled()) {
				$categoryquery .= ' AND c.language IN ('.$this->_db->q(JFactory::getLanguage()->getTag()).','.$this->_db->q('*').') ';
			}
			
			$user	= JFactory::getUser();
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$categoryquery .= ' AND c.access IN ('.$groups.') ';
			
			JArrayHelper::toInteger($categories);
			$where[] = ' AND '.$this->_db->qn('e.id').' IN (SELECT '.$this->_db->qn('tx.ide').' FROM '.$this->_db->qn('#__rseventspro_taxonomy','tx').' LEFT JOIN '.$this->_db->qn('#__categories','c').' ON '.$this->_db->qn('c.id').' = '.$this->_db->qn('tx.id').' WHERE '.$this->_db->qn('c.id').' IN ('.implode(',',$categories).') AND '.$this->_db->qn('tx.type').' = '.$this->_db->q('category').' AND '.$this->_db->qn('c.extension').' = '.$this->_db->q('com_rseventspro').' '.$categoryquery.' )';
		}
		
		// Select events with this specific tags
		if (!empty($tags)) {
			JArrayHelper::toInteger($tags);
			$where[] = ' AND '.$this->_db->qn('e.id').' IN (SELECT '.$this->_db->qn('tx.ide').' FROM '.$this->_db->qn('#__rseventspro_taxonomy','tx').' LEFT JOIN '.$this->_db->qn('#__rseventspro_tags','t').' ON '.$this->_db->qn('t.id').' = '.$this->_db->qn('tx.id').' WHERE '.$this->_db->qn('t.id').' IN ('.implode(',',$tags).') AND '.$this->_db->qn('tx.type').' = '.$this->_db->q('tag').') ';
		}
		
		// Select events with this specific location
		if (!empty($locations)) {
			JArrayHelper::toInteger($locations);
			$where[] = ' AND '.$this->_db->qn('e.location').' IN ('.implode(',',$locations).') ';
		}
		
		if (!empty($where)) {
			$query .= implode('',$where);
		}
		
		if (!empty($this->_where)) {
			$query .= $this->_where;
		}
		
		if (!empty($this->_exclude))
			$query .= ' AND '.$this->_db->qn('e.id').' NOT IN ('.implode(',',$this->_exclude).') ';
		
		$featured_condition = rseventsproHelper::getConfig('featured','int') ? $this->_db->qn('e.featured').' DESC, ' : '';
		$query .= ' ORDER BY '.$featured_condition.' '.$this->_db->qn('e.'.$order).' '.$this->_db->escape($direction).' ';
		
		return $query;
	}
	
	/**
	 *	Method to build the where query
	 *
	 *	@return SQL query
	 */
	protected function _buildWhere() {
		list($columns, $operators, $values) = $this->_filters;
		$where 	= array();
		
		for ($i=0; $i<count($columns); $i++) {
			$column 	= $columns[$i];			
			$operator 	= $operators[$i];
			$value 		= $values[$i];
			$extrac		= 0;
			$extrat		= 0;
			
			switch ($column)
			{
				case 'locations':
					$column = 'l.name';
				break;
				
				case 'categories':
					$column = 'c.title';
					$extrac = 1;
				break;
				
				case 'tags':
					$column = 't.name';
					$extrat = 1;
				break;
				
				case 'events':
					$column = 'e.name';
				break;
				
				default:
					$column = 'e.'.$column;
				break;
			}
			
			switch ($operator) {
				case 'contains':
					$operator = 'LIKE';
					$value	  = '%'.str_replace('%', '\%', $value).'%';
				break;
				
				case 'notcontain':
					$operator = 'NOT LIKE';
					$value	  = '%'.str_replace('%', '\%', $value).'%';
				break;
				
				case 'is':
					$operator = '=';
				break;
				
				case 'isnot':
					$operator = '<>';
				break;
			}
			
			if ($extrac) {
				$categoryquery = '';
				if (JLanguageMultilang::isEnabled()) {
					$categoryquery .= ' AND c.language IN ('.$this->_db->q(JFactory::getLanguage()->getTag()).','.$this->_db->q('*').') ';
				}
				
				$user	= JFactory::getUser();
				$groups	= implode(',', $user->getAuthorisedViewLevels());
				$categoryquery .= ' AND c.access IN ('.$groups.') ';
				
				if ($operator == '<>') {
					$this->_db->setQuery('SELECT '.$this->_db->qn('tx.ide').', CONCAT(\',\', GROUP_CONCAT('.$this->_db->qn('c.title').'), \',\') categs FROM '.$this->_db->qn('#__rseventspro_taxonomy','tx').' LEFT JOIN '.$this->_db->qn('#__categories','c').' ON '.$this->_db->qn('c.id').' = '.$this->_db->qn('tx.id').' WHERE '.$this->_db->qn('tx.type').' = '.$this->_db->q('category').' AND '.$this->_db->qn('c.extension').' = '.$this->_db->q('com_rseventspro').' '.$categoryquery.' GROUP BY '.$this->_db->qn('tx.ide').' HAVING categs NOT LIKE '.$this->_db->q('%'.$value.'%'));
					if ($eventids = $this->_db->loadColumn()) {
						JArrayHelper::toInteger($eventids);
						$where[] = 'AND '.$this->_db->qn('e.id').' IN ('.implode(',',$eventids).')';
					}
				} else {
					$where[] = 'AND '.$this->_db->qn('e.id').' IN (SELECT '.$this->_db->qn('tx.ide').' FROM '.$this->_db->qn('#__rseventspro_taxonomy','tx').' LEFT JOIN '.$this->_db->qn('#__categories','c').' ON '.$this->_db->qn('c.id').' = '.$this->_db->qn('tx.id').' WHERE '.$this->_db->qn($column).' '.$operator.' '.$this->_db->q($value).' AND '.$this->_db->qn('tx.type').' = '.$this->_db->q('category').' AND '.$this->_db->qn('c.extension').' = '.$this->_db->q('com_rseventspro').' '.$categoryquery.' )';
				}
			} elseif ($extrat) {
				if ($operator == '<>') {
					$this->_db->setQuery('SELECT '.$this->_db->qn('tx.ide').', CONCAT(\',\', GROUP_CONCAT('.$this->_db->qn('t.name').'), \',\') tags FROM '.$this->_db->qn('#__rseventspro_taxonomy','tx').' LEFT JOIN '.$this->_db->qn('#__rseventspro_tags','t').' ON '.$this->_db->qn('t.id').' = '.$this->_db->qn('tx.id').' WHERE '.$this->_db->qn('tx.type').' = '.$this->_db->q('tag').' GROUP BY '.$this->_db->qn('tx.ide').' HAVING tags NOT LIKE '.$this->_db->q('%'.$value.'%'));
					$eventids = $this->_db->loadColumn();
					JArrayHelper::toInteger($eventids);
					
					$where[] = 'AND '.$this->_db->qn('e.id').' IN ('.implode(',',$eventids).')';
				} else {
					$where[] = 'AND '.$this->_db->qn('e.id').' IN (SELECT '.$this->_db->qn('tx.ide').' FROM '.$this->_db->qn('#__rseventspro_taxonomy','tx').' LEFT JOIN '.$this->_db->qn('#__rseventspro_tags','t').' ON '.$this->_db->qn('t.id').' = '.$this->_db->qn('tx.id').' WHERE '.$this->_db->qn($column).' '.$operator.' '.$this->_db->q($value).' AND '.$this->_db->qn('tx.type').' = '.$this->_db->q('tag').')';
				}
			} else {
				$where[] = 'AND ('.$this->_db->qn($column).' '.$operator.' '.$this->_db->q($value).')';
			}
		}
		
		return !empty($where) ? implode(' ',$where) : '';
	}
	
	/**
	 *	Method to build the JOIN query
	 *
	 *	@return SQL query
	 */
	protected function _buildJoin() {
		list($columns, $operators, $values) = $this->_filters;
		$join = false;
		
		for ($i=0; $i<count($columns); $i++) {
			$column 	= $columns[$i];
			switch ($column) {
				case 'locations':
					$join = true;
				break;
			}
		}
		
		return $join ? 'LEFT JOIN '.$this->_db->qn('#__rseventspro_locations','l').' ON '.$this->_db->qn('l.id').' = '.$this->_db->qn('e.location').'' : '';
	}
	
	/**
	 *	Method to get calendar events
	 */
	public function getEvents() {
		if (empty($this->_data)) {
			if ($this->_app->input->get('layout') == 'day' || $this->_app->input->get('layout') == 'week' || $this->_app->input->get('tpl') == 'day' || $this->_app->input->get('tpl') == 'week') {
				$this->_db->setQuery($this->_query,$this->getState('com_rseventspro.limitstart'), $this->getState('com_rseventspro.limit'));
				$this->_data = $this->_db->loadObjectList();
			} else {
				$this->_db->setQuery($this->_query);
				$this->_data = $this->_db->loadObjectList();
			}
		}
		return $this->_data;
	}
	
	protected function getCount($query) {
		$this->_db->setQuery($query);
		$this->_db->execute();

		return $this->_db->getNumRows();
	}
	
	/**
	 *	Method to get the total number of events
	 */
	public function getTotal() {
		if (empty($this->_total))
			$this->_total = $this->getCount($this->_query);
		return $this->_total;
	}
	
	/**
	 *	Method to get calendar filters
	 */
	public function getFilters() {
		$itemid 	= $this->_app->input->getInt('Itemid');
		$columns 	= $this->_app->getUserStateFromRequest('com_rseventspro.calendar.filter_columns'.$itemid, 	'filter_from', 	array(), 'array');
		$operators 	= $this->_app->getUserStateFromRequest('com_rseventspro.calendar.filter_operators'.$itemid, 'filter_condition', array(), 'array');
		$values 	= $this->_app->getUserStateFromRequest('com_rseventspro.calendar.filter_values'.$itemid, 	'search', 	array(), 'array');
		
		if ($columns && $columns[0] == '') {
			$columns = $operators = $values = array();
		}
		
		if (!empty($values)) {
			foreach ($values as $i => $value) {
				if (empty($value)) {
					if (isset($columns[$i])) unset($columns[$i]);
					if (isset($operators[$i])) unset($operators[$i]);
					if (isset($values[$i])) unset($values[$i]);
				}
			}
		}
		
		$columns	= array_merge($columns);
		$operators	= array_merge($operators);
		$values		= array_merge($values);
		
		if ($this->_app->input->getString('rs_remove') != '') {
			unset($columns[$this->_app->input->getInt('rs_remove')]);
			unset($operators[$this->_app->input->getInt('rs_remove')]);
			unset($values[$this->_app->input->getInt('rs_remove')]);
			$this->_app->setUserState('com_rseventspro.calendar.filter_columns'.$itemid,	array_merge($columns));
			$this->_app->setUserState('com_rseventspro.calendar.filter_operators'.$itemid,	array_merge($operators));
			$this->_app->setUserState('com_rseventspro.calendar.filter_values'.$itemid,		array_merge($values));
		}
		
		if ($this->_app->input->getInt('rs_clear',0)) {
			$this->_app->setUserState('com_rseventspro.calendar.filter_columns'.$itemid,	array());
			$this->_app->setUserState('com_rseventspro.calendar.filter_operators'.$itemid,	array());
			$this->_app->setUserState('com_rseventspro.calendar.filter_values'.$itemid,		array());
			$columns = $operators = $values = array();
		}
		
		return array(array_merge($columns), array_merge($operators), array_merge($values));
	}
	
	public function getColors() {
		// Get params
		$params		= rseventsproHelper::getParams();
		$colors		= $params->get('colors',0);
		$legend		= $params->get('legend',0);
		$categories = $params->get('categories',0);
		$order		= $params->get('legendordering','title');
		$direction	= $params->get('legenddirection','DESC');
		$query		= $this->_db->getQuery(true);
		$data		= array();
		
		if ($legend) {
			$query->clear()
				->select($this->_db->qn('id'))->select($this->_db->qn('title'))->select($this->_db->qn('params'))
				->from($this->_db->qn('#__categories'))
				->where($this->_db->qn('extension').' = '.$this->_db->q('com_rseventspro'))
				->where($this->_db->qn('published').' = 1');
			
			if (JLanguageMultilang::isEnabled()) {
				$query->where('language IN ('.$this->_db->q(JFactory::getLanguage()->getTag()).','.$this->_db->q('*').')');
			}
			
			$user	= JFactory::getUser();
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('access IN ('.$groups.')');

			if (!empty($categories)) {
				JArrayHelper::toInteger($categories);
				$query->where($this->_db->qn('id').' IN ('.implode(',',$categories).')');	
			}
			
			$query->order($this->_db->qn($order).' '.$this->_db->escape($direction));
			
			$this->_db->setQuery($query);
			if ($data = $this->_db->loadObjectList()) {
				foreach ($data as $i => $category) {
					$registry = new JRegistry;
					$registry->loadString($category->params);
					$data[$i]->color = $colors ? $registry->get('color','') : '';
				}
				
				$object = new stdClass();
				$object->id		= '';
				$object->title	= JText::_('COM_RSEVENTSPRO_SHOW_ALL_CATEGORIES');
				$object->color	= '';
				$data = array_merge(array($object),$data);
			}
			
			return $data;
		}
		
		return false;
	}
	
	public function getSelected() {
		$query		= $this->_db->getQuery(true);
		$category	= 0;
		$count		= 0;
		
		list($columns, $operators, $values) = $this->_filters;
		
		for ($i=0; $i<count($columns); $i++) {
			$column 	= $columns[$i];
			$operator	= $operators[$i];
			$value 		= $values[$i];
			
			if ($column == 'categories') {
				if ($operator == 'is') {
					$query->clear()
						->select($this->_db->qn('id'))
						->from($this->_db->qn('#__categories'))
						->where($this->_db->qn('title').' = '.$this->_db->q($value));
					
					$this->_db->setQuery($query);
					$category = (int) $this->_db->loadResult();
				}
				$count++;
			}
		}
		
		// Get Category details
		if ($count == 1) {
			return $category;
		}
		
		return false;
	}
	
	protected function getWeekdays($i, $weekend = false) {
		switch($i) {
			case 0:
				if ($weekend)
					return 6;
				else
					return array(0,1,2,3,4,5,6);
			break;
			
			case 1:
				if ($weekend)
					return 0;
				else
					return array(1,2,3,4,5,6,0);
			break;
			
			case 6:
				if ($weekend)
					return 5;
				else
					return array(6,0,1,2,3,4,5);
			break;
		}
	}
	
	/**
	 *	Method to get module params
	 *
	 *	@return array
	 */
	public function getModuleParams() {
		$query = $this->_db->getQuery(true);
		
		$query->clear()
			->select($this->_db->qn('params'))
			->from($this->_db->qn('#__modules'))
			->where($this->_db->qn('id').' = '.$this->_app->input->getInt('mid',0));
		
		$this->_db->setQuery($query);
		$string = $this->_db->loadResult();
		
		$registry = new JRegistry;
		$registry->loadString($string);
		return $registry;
	}
	
	public function getFilterOptions() { 
		return array(JHTML::_('select.option', 'events', JText::_('COM_RSEVENTSPRO_FILTER_NAME')), JHTML::_('select.option', 'description', JText::_('COM_RSEVENTSPRO_FILTER_DESCRIPTION')), 
			JHTML::_('select.option', 'locations', JText::_('COM_RSEVENTSPRO_FILTER_LOCATION')) ,JHTML::_('select.option', 'categories', JText::_('COM_RSEVENTSPRO_FILTER_CATEGORY')),
			JHTML::_('select.option', 'tags', JText::_('COM_RSEVENTSPRO_FILTER_TAG'))
		);
	}
	
	public function getFilterConditions() {
		return array(JHTML::_('select.option', 'is', JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_IS')), JHTML::_('select.option', 'isnot', JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_ISNOT')),
			JHTML::_('select.option', 'contains', JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_CONTAINS')),JHTML::_('select.option', 'notcontain', JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_NOTCONTAINS'))
		);
	}
}