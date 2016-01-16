<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.model' );

class rseventsproModelEvents extends JModelLegacy
{
	var $_pastquery			= null;
	var $_ongoingquery		= null;
	var $_thisweekquery		= null;
	var $_thismonthquery	= null;
	var $_nextmonthquery	= null;
	var $_upcomingquery		= null;
	var $_formsquery		= null;
	
	var $_pastdata			= null;
	var $_ongoingdata		= null;
	var $_thisweekdata		= null;
	var $_thismonthdata		= null;
	var $_nextmonthdata		= null;
	var $_upcomingdata		= null;
	var $_formsdata			= null;
	
	var $_pasttotal			= 0;
	var $_ongoingtotal		= 0;
	var $_thisweektotal		= 0;
	var $_thismonthtotal	= 0;
	var $_nextmonthtotal	= 0;
	var $_upcomingtotal		= 0;
	var $_formstotal		= 0;
	
	var $_id				= 0;
	var $_db				= null;
	var $_app				= null;
	var $_input				= null;
	var $_join				= null;
	var $_where				= null;
	var $_filters			= null;
	var $_other				= null;
	var $_pagination		= null;
	var $_formspagination	= null;
	
	/**
	 *	Main constructor
	 */
	public function __construct() {
		parent::__construct();
		
		$this->_db	= JFactory::getDBO();
		$this->_app = JFactory::getApplication();
		$this->_input = $this->_app->input;
		$config = JFactory::getConfig();
		
		// Get pagination request variables
		$limit = $this->_app->getUserStateFromRequest('com_rseventspro.events.limit', 'limit', $config->get('list_limit'), 'int');
		$limitstart = $this->_input->getInt('lstart', 0);
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('com_rseventspro.events.limit', $limit);
		$this->setState('com_rseventspro.events.limitstart', $limitstart);
		
		$layout = $this->_input->get('layout');
		
		if ($layout == 'default' || $layout == '' || $layout == 'items' || $layout == 'menu') {
			$this->_filters		= $this->getFilters();
			$this->_other		= $this->getOtherFilters();
			$this->_where		= $this->_buildWhere();
			$this->_join		= $this->_buildJoin();
			
			$this->_pastquery		= $this->getPastEventsQuery();
			$this->_ongoingquery	= $this->getOngoingEventsQuery();
			$this->_thisweekquery	= $this->getThisWeekEventsQuery();
			$this->_thismonthquery	= $this->getThisMonthEventsQuery();
			$this->_nextmonthquery	= $this->getNextMonthEventsQuery();
			$this->_upcomingquery	= $this->getUpcomingEventsQuery();
		}
		
		if ($layout == 'forms') {
			$this->_formsquery = $this->getFormsQuery();
		}
	}
	
	/**
	 *	Method to get the WHERE clasue
	 *
	 *	return array;
	 */
	protected function _buildWhere() {
		list($columns, $operators, $values) = $this->_filters;
		$where = array();
		$query = $this->_db->getQuery(true);
		$query->clear();
		
		for ($i=0; $i<count($columns); $i++) {
			$column 	= $columns[$i];			
			$operator 	= $operators[$i];
			$value 		= $values[$i];
			$extrac		= 0;
			$extrat		= 0;
			
			switch ($column) {
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
				$subquery = $this->_db->getQuery(true);
				$subquery->clear();
				if ($operator == '<>') {
					$subquery->select($this->_db->qn('tx.ide'))->select("CONCAT(',', GROUP_CONCAT(".$this->_db->qn('c.title')."), ',') categs")
						->from($this->_db->qn('#__rseventspro_taxonomy','tx'))
						->join('left', $this->_db->qn('#__categories','c').' ON '.$this->_db->qn('c.id').' = '.$this->_db->qn('tx.id'))
						->where($this->_db->qn('tx.type').' = '.$this->_db->q('category'))
						->where($this->_db->qn('c.extension').' = '.$this->_db->q('com_rseventspro'))
						->group($this->_db->qn('tx.ide'))
						->having('categs NOT LIKE '.$this->_db->q('%'.$value.'%'));
					$this->_db->setQuery($subquery);
					if ($eventids = $this->_db->loadColumn()) {
						JArrayHelper::toInteger($eventids);
						$where[] = $this->_db->qn('e.id').' IN ('.implode(',',$eventids).')';
					}
				} else {
					$subquery->select($this->_db->qn('tx.ide'))
						->from($this->_db->qn('#__rseventspro_taxonomy','tx'))
						->join('left', $this->_db->qn('#__categories','c').' ON '.$this->_db->qn('c.id').' = '.$this->_db->qn('tx.id'))
						->where($this->_db->qn($column).' '.$operator.' '.$this->_db->q($value))
						->where($this->_db->qn('tx.type').' = '.$this->_db->q('category'))
						->where($this->_db->qn('c.extension').' = '.$this->_db->q('com_rseventspro'));
					$where[] = $this->_db->qn('e.id').' IN ('.$subquery.')';
				}
			} else if ($extrat) {
				$subquery = $this->_db->getQuery(true);
				$subquery->clear();
				
				if ($operator == '<>') {
					$subquery->select($this->_db->qn('tx.ide'))->select("CONCAT(',', GROUP_CONCAT(".$this->_db->qn('t.name')."), ',') tags")
						->from($this->_db->qn('#__rseventspro_taxonomy','tx'))
						->join('left', $this->_db->qn('#__rseventspro_tags','t').' ON '.$this->_db->qn('t.id').' = '.$this->_db->qn('tx.id'))
						->where($this->_db->qn('tx.type').' = '.$this->_db->q('tag'))
						->group($this->_db->qn('tx.ide'))
						->having('tags NOT LIKE '.$this->_db->q('%'.$value.'%'));
					$this->_db->setQuery($subquery);
					if ($eventids = $this->_db->loadColumn()) {
						JArrayHelper::toInteger($eventids);
						$where[] = $this->_db->qn('e.id').' IN ('.implode(',',$eventids).')';
					}
				} else {
					$subquery->select($this->_db->qn('tx.ide'))
						->from($this->_db->qn('#__rseventspro_taxonomy','tx'))
						->join('left', $this->_db->qn('#__rseventspro_tags','t').' ON '.$this->_db->qn('t.id').' = '.$this->_db->qn('tx.id'))
						->where($this->_db->qn($column).' '.$operator.' '.$this->_db->q($value))
						->where($this->_db->qn('tx.type').' = '.$this->_db->q('tag'));
					$where[] = $this->_db->qn('e.id').' IN ('.$subquery.')';
				}
			} else {
				$where[] = $this->_db->qn($column).' '.$operator.' '.$this->_db->q($value);
			}
		}
		
		if (!is_null($archived = $this->_other['archived'])) {
			if ($archived == 1)
				$where[] = $this->_db->qn('e.published').' IN (0,1,2)';
			elseif ($archived == 0)
				$where[] = $this->_db->qn('e.published').' IN (0,1)';
			elseif ($archived == 2)
				$where[] = $this->_db->qn('e.published').' = 2';
		}
		
		if (!is_null($featured = $this->_other['featured'])) {
			if ($featured == 1)
				$where[] = $this->_db->qn('e.featured').' = 1';
			elseif ($featured == 0)
				$where[] = $this->_db->qn('e.featured').' = 0';
		}
		
		if (!is_null($childs = $this->_other['childs'])) {
			if ($childs == 0)
				$where[] = $this->_db->qn('e.parent').' = 0';
		}
		
		$from	= $this->_other['start'];
		$to		= $this->_other['end'];
		
		if (!is_null($from)) {
			$from = rseventsproHelper::date($from,null,false,true);
			$from->setTZByID($from->getTZID());
			$from->convertTZ(new RSDate_Timezone('GMT'));
			$from = $from->formatLikeDate('Y-m-d H:i:s');
		}
		
		if (!is_null($to)) {
			$to = rseventsproHelper::date($to,null,false,true);
			$to->setTZByID($to->getTZID());
			$to->convertTZ(new RSDate_Timezone('GMT'));
			$to = $to->formatLikeDate('Y-m-d H:i:s');
		}
		
		if (is_null($from) && !is_null($to)) {
			$where[] = $this->_db->qn('e.end').' <= '.$this->_db->q($to);
		} elseif (!is_null($from) && is_null($to)) {
			$where[] = $this->_db->qn('e.start').' >= '.$this->_db->q($from);
		} elseif (!is_null($from) && !is_null($to)) {
			$where[] = '(('.$this->_db->qn('e.start').' <= '.$this->_db->q($from).' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($to).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($from).' AND '.$this->_db->qn('e.end').' <= '.$this->_db->q($to).') )';
		}
		
		return $where;
	}
	
	/**
	 *	Method to load the JOIN clasuse
	 *
	 *	return boolean;
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
		
		return $join;
	}
	
	/**
	 *	Method to get All day events
	 *
	 *	return boolean;
	 */
	protected function _getAllDayEvents($type) {
		$query = $this->_db->getQuery(true);
		
		$query->clear()
			->select($this->_db->qn('id'))
			->from($this->_db->qn('#__rseventspro_events'))
			->where($this->_db->qn('allday').' = 1');
		
		$nowUTC = new RSDate();
		$nowUTC->setTZByID($nowUTC->getTZID());
		$nowUTC->convertTZ(new RSDate_Timezone('GMT'));
		$nowUTC->setHourMinuteSecond(0,0,0);
		$nowUTC = $nowUTC->formatLikeDate('Y-m-d H:i:s');
		
		if ($type == 'past') {
			$now   = new RSDate();
			$now->setHourMinuteSecond(0,0,0);
			$now->setTZByID($now->getTZID());
			$now->convertTZ(new RSDate_Timezone('GMT'));
			$now = $now->formatLikeDate('Y-m-d H:i:s');
			
			$query->where('('.$this->_db->qn('start').' < '.$this->_db->q($now).' AND '.$this->_db->qn('start').' < '.$this->_db->q($nowUTC).')');
		} elseif ($type == 'ongoing') {
			$now   = new RSDate();
			$now->setHourMinuteSecond(0,0,0);
			$now->setTZByID($now->getTZID());
			$now->convertTZ(new RSDate_Timezone('GMT'));
			$now = $now->formatLikeDate('Y-m-d H:i:s');
		
			$query->where('('.$this->_db->qn('start').' = '.$this->_db->q($now).' OR '.$this->_db->qn('start').' = '.$this->_db->q($nowUTC).')');
		} elseif ($type == 'thisweek') {
			$Calc	= new RSDate_Calc();
			$endofweek = $Calc->endOfWeek();
			$date = new RSDate($endofweek);
			$date->addSeconds(86399);
			$end_of_week = new RSDate($date);
			$end_of_week = $end_of_week->formatLikeDate('Y-m-d H:i:s');
			
			$now   = new RSDate();
			$now->addSeconds(86400);
			$now->setHourMinuteSecond(0,0,0);
			$now->setTZByID($now->getTZID());
			$now->convertTZ(new RSDate_Timezone('GMT'));
			$now = $now->formatLikeDate('Y-m-d H:i:s');
			
			$query->where($this->_db->qn('start').' >= '.$this->_db->q($now));
			$query->where($this->_db->qn('start').' <= '.$this->_db->q($end_of_week));
		} elseif ($type == "thismonth") {
			$Calc	= new RSDate_Calc();
			$endofweek = $Calc->endOfWeek();
			$date = new RSDate($endofweek);
			$date->addSeconds(86400);
			$end_of_week = new RSDate($date);
			$end_of_week->setTZByID($end_of_week->getTZID());
			$end_of_week->convertTZ(new RSDate_Timezone('GMT'));
			$end_of_week = $end_of_week->formatLikeDate('Y-m-d H:i:s');
			
			$Calc	= new RSDate_Calc();
			$endofmonth = $Calc->endOfMonth();
			$date = new RSDate($endofmonth);
			$date->addSeconds(86399);
			$end_of_month = new RSDate($date);
			$end_of_month = $end_of_month->formatLikeDate('Y-m-d H:i:s');
			
			
			$query->where($this->_db->qn('start').' >= '.$this->_db->q($end_of_week));
			$query->where($this->_db->qn('start').' <= '.$this->_db->q($end_of_month));
		} elseif ($type == 'nextmonth') {
			$Calc	= new RSDate_Calc();
			$start	= new RSDate($Calc->beginOfNextMonth());
			$start	= $start->formatLikeDate('Y-m-d H:i:s');
			
			$end	= $Calc->endOfNextMonth();
			$end	= new RSDate($end);
			$end->addSeconds(86399);
			$end	= new RSDate($end);
			$end	= $end->formatLikeDate('Y-m-d H:i:s');
			
			
			$query->where($this->_db->qn('start').' >= '.$this->_db->q($start));
			$query->where($this->_db->qn('start').' <= '.$this->_db->q($end));
		}
		
		$this->_db->setQuery($query);
		if ($events = $this->_db->loadColumn()) {
			JArrayHelper::toInteger($events);
			return $events;
		}
		
		return false;
	}
	
	/**
	 *	Method to get past events
	 *
	 *	return JDatabaseQuery object;
	 */
	public function getPastEventsQuery() {
		$now   = new RSDate();
		$now->setTZByID($now->getTZID());
		$now->convertTZ(new RSDate_Timezone('GMT'));
		$now = $now->formatLikeDate('Y-m-d H:i:s');
		
		$include = $this->_getAllDayEvents('past');
		
		$query = $this->_db->getQuery(true);
		$query->clear()
			->select($this->_db->qn('e.id'))
			->from($this->_db->qn('#__rseventspro_events','e'));
		
		if ($this->_join)
			$query->join('left', $this->_db->qn('#__rseventspro_locations','l').' ON '.$this->_db->qn('l.id').' = '.$this->_db->qn('e.location'));
		
		if (!empty($include)) {
			$query->where('(('.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where($this->_db->qn('e.end').' <= '.$this->_db->q($now).') OR '.$this->_db->qn('e.id').' IN ('.implode(',',$include).'))');
		} else {
			$query->where($this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where($this->_db->qn('e.end').' <= '.$this->_db->q($now));
		}
		
		if (!empty($this->_where)) {
			foreach ($this->_where as $where)
			$query->where($where);
		}
		
		$sortColumn = $this->getSortColumn();
		$sortOrder 	= $this->getSortOrder();
		
		$query->order($this->_db->qn($sortColumn).' '.$this->_db->escape($sortOrder));
		
		return $query;
	}
	
	/**
	 *	Method to get ongoing events
	 *
	 *	return JDatabaseQuery object;
	 */
	public function getOngoingEventsQuery() {
		$now   = new RSDate();
		$now->setTZByID($now->getTZID());
		$now->convertTZ(new RSDate_Timezone('GMT'));
		$now = $now->formatLikeDate('Y-m-d H:i:s');
		
		$include = $this->_getAllDayEvents('ongoing');
		
		$query = $this->_db->getQuery(true);
		$query->clear()
			->select($this->_db->qn('e.id'))
			->from($this->_db->qn('#__rseventspro_events','e'));
		
		if ($this->_join)
			$query->join('left', $this->_db->qn('#__rseventspro_locations','l').' ON '.$this->_db->qn('l.id').' = '.$this->_db->qn('e.location'));
		
		
		if (!empty($include)) {
			$query->where('(('.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where($this->_db->qn('e.start').' <= '.$this->_db->q($now));
			$query->where($this->_db->qn('e.end').' >= '.$this->_db->q($now).') OR '.$this->_db->qn('e.id').' IN ('.implode(',',$include).'))');
		} else {
			$query->where($this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where($this->_db->qn('e.start').' <= '.$this->_db->q($now));
			$query->where($this->_db->qn('e.end').' >= '.$this->_db->q($now));
		}
		
		if (!empty($this->_where)) {
			foreach ($this->_where as $where)
			$query->where($where);
		}
		
		$sortColumn = $this->getSortColumn();
		$sortOrder 	= $this->getSortOrder();
		
		$query->order($this->_db->qn($sortColumn).' '.$this->_db->escape($sortOrder));
		
		return $query;
	}
	
	/**
	 *	Method to get this week events
	 *
	 *	return JDatabaseQuery object;
	 */
	public function getThisWeekEventsQuery() {
		$now   = new RSDate();
		$now->setTZByID($now->getTZID());
		$now->convertTZ(new RSDate_Timezone('GMT'));
		$now = $now->formatLikeDate('Y-m-d H:i:s');
		
		$include = $this->_getAllDayEvents('thisweek');
		
		$Calc	= new RSDate_Calc();
		$endofweek = $Calc->endOfWeek();
		$date = new RSDate($endofweek);
		$date->addSeconds(86399);
		$end_of_week = new RSDate($date);
		$end_of_week = $end_of_week->formatLikeDate('Y-m-d H:i:s');
		
		$exclude = array();
		$this->_db->setQuery($this->_ongoingquery);
		$ongoing = $this->_db->loadColumn();
		if (!empty($ongoing)) {
			$exclude = array_merge($ongoing,array());
			JArrayHelper::toInteger($exclude);
		}
		
		$query = $this->_db->getQuery(true);
		$query->clear()
			->select($this->_db->qn('e.id'))
			->from($this->_db->qn('#__rseventspro_events','e'));
		
		if ($this->_join)
			$query->join('left', $this->_db->qn('#__rseventspro_locations','l').' ON '.$this->_db->qn('l.id').' = '.$this->_db->qn('e.location'));
		
		if (!empty($exclude))
			$query->where($this->_db->qn('e.id').' NOT IN ('.implode(',',$exclude).')');
		
		if (!empty($include)) {
			$query->where('(('.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where('(('.$this->_db->qn('e.start').' <= '.$this->_db->q($now). ' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($now).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($now).' AND '.$this->_db->qn('e.start').' <= '.$this->_db->q($end_of_week).'))) OR '.$this->_db->qn('e.id').' IN ('.implode(',',$include).'))');
		} else {
			$query->where($this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where('(('.$this->_db->qn('e.start').' <= '.$this->_db->q($now). ' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($now).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($now).' AND '.$this->_db->qn('e.start').' <= '.$this->_db->q($end_of_week).'))');
		}
		
		if (!empty($this->_where)) {
			foreach ($this->_where as $where)
			$query->where($where);
		}
		
		$sortColumn = $this->getSortColumn();
		$sortOrder 	= $this->getSortOrder();
		
		$query->order($this->_db->qn($sortColumn).' '.$this->_db->escape($sortOrder));
		
		return $query;
	}
	
	/**
	 *	Method to get this month events events
	 *
	 *	return JDatabaseQuery object;
	 */
	public function getThisMonthEventsQuery() {
		$now   = new RSDate();
		$now->setTZByID($now->getTZID());
		$now->convertTZ(new RSDate_Timezone('GMT'));
		$now = $now->formatLikeDate('Y-m-d H:i:s');
		$Calc	= new RSDate_Calc();
		$endofmonth = $Calc->endOfMonth();
		$date = new RSDate($endofmonth);
		$date->addSeconds(86399);
		$end_of_month = new RSDate($date);
		$end_of_month = $end_of_month->formatLikeDate('Y-m-d H:i:s');
		
		$include = $this->_getAllDayEvents('thismonth');
		
		$exclude = array();
		$this->_db->setQuery($this->_ongoingquery);
		$ongoing = $this->_db->loadColumn();
		$this->_db->setQuery($this->_thisweekquery);
		$thisweek = $this->_db->loadColumn();
		if (!empty($ongoing)) {
			$exclude = array_merge($ongoing,array());
			JArrayHelper::toInteger($exclude);
		}
		
		if (!empty($thisweek)) {
			$exclude = array_merge($exclude,$thisweek);
			JArrayHelper::toInteger($exclude);
		}
		
		$query = $this->_db->getQuery(true);
		$query->clear()
			->select($this->_db->qn('e.id'))
			->from($this->_db->qn('#__rseventspro_events','e'));
		
		if ($this->_join)
			$query->join('left', $this->_db->qn('#__rseventspro_locations','l').' ON '.$this->_db->qn('l.id').' = '.$this->_db->qn('e.location'));
		
		if (!empty($exclude))
			$query->where($this->_db->qn('e.id').' NOT IN ('.implode(',',$exclude).')');
		
		if (!empty($include)) {
			$query->where('(('.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where('(('.$this->_db->qn('e.start').' <= '.$this->_db->q($now). ' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($now).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($now).' AND '.$this->_db->qn('e.start').' <= '.$this->_db->q($end_of_month).'))) OR '.$this->_db->qn('e.id').' IN ('.implode(',',$include).'))');
		} else {
			$query->where($this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where('(('.$this->_db->qn('e.start').' <= '.$this->_db->q($now). ' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($now).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($now).' AND '.$this->_db->qn('e.start').' <= '.$this->_db->q($end_of_month).'))');
		}
		
		if (!empty($this->_where)) {
			foreach ($this->_where as $where)
			$query->where($where);
		}
		
		$sortColumn = $this->getSortColumn();
		$sortOrder 	= $this->getSortOrder();
		
		$query->order($this->_db->qn($sortColumn).' '.$this->_db->escape($sortOrder));
		
		return $query;
	}
	
	/**
	 *	Method to get next month events events
	 *
	 *	return JDatabaseQuery object;
	 */
	public function getNextMonthEventsQuery() {
		$Calc	= new RSDate_Calc();
		$start	= new RSDate($Calc->beginOfNextMonth());
		$start	= $start->formatLikeDate('Y-m-d H:i:s');
		
		$end	= $Calc->endOfNextMonth();
		$end	= new RSDate($end);
		$end->addSeconds(86399);
		$end	= new RSDate($end);
		$end	= $end->formatLikeDate('Y-m-d H:i:s');
		
		$include = $this->_getAllDayEvents('nextmonth');
		
		$exclude = array();
		$this->_db->setQuery($this->_ongoingquery);
		$ongoing = $this->_db->loadColumn();
		$this->_db->setQuery($this->_thisweekquery);
		$thisweek = $this->_db->loadColumn();
		$this->_db->setQuery($this->_thismonthquery);
		$thismonth = $this->_db->loadColumn();
		if (!empty($ongoing)) {
			$exclude = array_merge($ongoing,array());
			JArrayHelper::toInteger($exclude);
		}
		
		if (!empty($thisweek)) {
			$exclude = array_merge($exclude,$thisweek);
			JArrayHelper::toInteger($exclude);
		}
		
		if (!empty($thismonth)) {
			$exclude = array_merge($exclude,$thismonth);
			JArrayHelper::toInteger($exclude);
		}
		
		$query = $this->_db->getQuery(true);
		$query->clear()
			->select($this->_db->qn('e.id'))
			->from($this->_db->qn('#__rseventspro_events','e'));
		
		if ($this->_join)
			$query->join('left', $this->_db->qn('#__rseventspro_locations','l').' ON '.$this->_db->qn('l.id').' = '.$this->_db->qn('e.location'));
		
		if (!empty($exclude))
			$query->where($this->_db->qn('e.id').' NOT IN ('.implode(',',$exclude).')');
		
		if (!empty($include)) {
			$query->where('(('.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where('(('.$this->_db->qn('e.start').' <= '.$this->_db->q($start). ' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($start).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($start).' AND '.$this->_db->qn('e.start').' <= '.$this->_db->q($end).'))) OR '.$this->_db->qn('e.id').' IN ('.implode(',',$include).'))');
		} else {
			$query->where($this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where('(('.$this->_db->qn('e.start').' <= '.$this->_db->q($start). ' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($start).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($start).' AND '.$this->_db->qn('e.start').' <= '.$this->_db->q($end).'))');
		}
		
		if (!empty($this->_where)) {
			foreach ($this->_where as $where)
			$query->where($where);
		}
		
		$sortColumn = $this->getSortColumn();
		$sortOrder 	= $this->getSortOrder();
		
		$query->order($this->_db->qn($sortColumn).' '.$this->_db->escape($sortOrder));
		
		return $query;
	}
	
	/**
	 *	Method to get upcoming events events
	 *
	 *	return JDatabaseQuery object;
	 */
	public function getUpcomingEventsQuery() {
		$Calc	= new RSDate_Calc();		
		$now   	= new RSDate();
		$now->addMonths(2);
		$start	= new RSDate($Calc->beginOfMonth($now->formatLikeDate('m'),$now->formatLikeDate('Y')));
		$start	= $start->formatLikeDate('Y-m-d H:i:s');
		
		$query = $this->_db->getQuery(true);
		$query->clear()
			->select($this->_db->qn('e.id'))
			->from($this->_db->qn('#__rseventspro_events','e'));
		
		if ($this->_join)
			$query->join('left', $this->_db->qn('#__rseventspro_locations','l').' ON '.$this->_db->qn('l.id').' = '.$this->_db->qn('e.location'));
		
		$query->where($this->_db->qn('e.start').' >= '.$this->_db->q($start));
		
		if (!empty($this->_where)) {
			foreach ($this->_where as $where)
			$query->where($where);
		}
		
		$sortColumn = $this->getSortColumn();
		$sortOrder 	= $this->getSortOrder();
		
		$query->order($this->_db->qn($sortColumn).' '.$this->_db->escape($sortOrder));
		
		return $query;
	}
	
	/**
	 *	Method to get RSForm!Pro registration forms
	 *
	 *	return JDatabaseQuery object;
	 */
	public function getFormsQuery() {
		$query = $this->_db->getQuery(true);
		$query->clear()
			->select($this->_db->qn('FormId'))->select($this->_db->qn('FormName'))
			->from($this->_db->qn('#__rsform_forms'))
			->where($this->_db->qn('Published').' = 1')
			->order($this->_db->qn('FormId').' ASC');
		
		return $query;
	}
	
	/**
	 *	Method to get past events data
	 *
	 *	return array;
	 */
	public function getPastEvents() {
		if (empty($this->_pastdata)) {
			$this->_db->setQuery($this->_pastquery, $this->getState('com_rseventspro.events.limitstart'), $this->getState('com_rseventspro.events.limit'));
			$this->_pastdata = $this->_db->loadColumn();
		}		
		return $this->_pastdata;
	}
	
	/**
	 *	Method to get ongoing events data
	 *
	 *	return array;
	 */
	public function getOngoingEvents() {
		if (empty($this->_ongoingdata)) {
			$this->_db->setQuery($this->_ongoingquery,$this->getState('com_rseventspro.events.limitstart'),$this->getState('com_rseventspro.events.limit'));
			$this->_ongoingdata = $this->_db->loadColumn();
		}
		return $this->_ongoingdata;
	}
	
	/**
	 *	Method to get this week events data
	 *
	 *	return array;
	 */
	public function getThisWeekEvents() {
		if (empty($this->_thisweekdata)) {
			$this->_db->setQuery($this->_thisweekquery,$this->getState('com_rseventspro.events.limitstart'),$this->getState('com_rseventspro.events.limit'));
			$this->_thisweekdata = $this->_db->loadColumn();
		}
		return $this->_thisweekdata;
	}
	
	/**
	 *	Method to get this month events data
	 *
	 *	return array;
	 */
	public function getThisMonthEvents() {
		if (empty($this->_thismonthdata)) {
			$this->_db->setQuery($this->_thismonthquery,$this->getState('com_rseventspro.events.limitstart'),$this->getState('com_rseventspro.events.limit'));
			$this->_thismonthdata = $this->_db->loadColumn();
		}
		return $this->_thismonthdata;
	}
	
	/**
	 *	Method to get next month events data
	 *
	 *	return array;
	 */
	public function getNextMonthEvents() {
		if (empty($this->_nextmonthdata)) {
			$this->_db->setQuery($this->_nextmonthquery,$this->getState('com_rseventspro.events.limitstart'),$this->getState('com_rseventspro.events.limit'));
			$this->_nextmonthdata = $this->_db->loadColumn();
		}
		return $this->_nextmonthdata;
	}
	
	/**
	 *	Method to get upcoming events data
	 *
	 *	return array;
	 */
	public function getUpcomingEvents() {
		if (empty($this->_upcomingdata)) {
			$this->_db->setQuery($this->_upcomingquery,$this->getState('com_rseventspro.events.limitstart'),$this->getState('com_rseventspro.events.limit'));
			$this->_upcomingdata = $this->_db->loadColumn();
		}
		return $this->_upcomingdata;
	}
	
	/**
	 *	Method to get RSForm!Pro forms data
	 *
	 *	return array;
	 */
	public function getForms() {
		if (!file_exists(JPATH_SITE.'/components/com_rsform/rsform.php')) {
			return array();
		}
		
		if (empty($this->_formsdata)) {
			$this->_db->setQuery($this->_formsquery,$this->getState('com_rseventspro.events.limitstart'),$this->getState('com_rseventspro.events.limit'));
			$this->_formsdata = $this->_db->loadObjectList();
		}
		return $this->_formsdata;
	}
	
	
	protected function getCount($query) {
		$this->_db->setQuery($query);
		$this->_db->execute();

		return $this->_db->getNumRows();
	}
	
	public function getPastTotal() {
		if (empty($this->_pasttotal)) {
			$this->_pasttotal = $this->getCount($this->_pastquery); 
		}
		
		return $this->_pasttotal;
	}
	
	public function getOngoingTotal() {
		if (empty($this->_ongoingtotal)) {
			$this->_ongoingtotal = $this->getCount($this->_ongoingquery); 
		}
		
		return $this->_ongoingtotal;
	}
	
	public function getThisWeekTotal() {
		if (empty($this->_thisweektotal)) {
			$this->_thisweektotal = $this->getCount($this->_thisweekquery);
		}
		
		return $this->_thisweektotal;
	}
	
	public function getThisMonthTotal() {
		if (empty($this->_thismonthtotal)) {
			$this->_thismonthtotal = $this->getCount($this->_thismonthquery);
		}
		
		return $this->_thismonthtotal;
	}
	
	public function getNextMonthTotal() {
		if (empty($this->_nextmonthtotal)) {
			$this->_nextmonthtotal = $this->getCount($this->_nextmonthquery);
		}
		
		return $this->_nextmonthtotal;
	}
	
	public function getUpcomingTotal() {
		if (empty($this->_upcomingtotal)) {
			$this->_upcomingtotal = $this->getCount($this->_upcomingquery);
		}
		
		return $this->_upcomingtotal;
	}
	
	public function getFormsTotal() {
		if (!file_exists(JPATH_SITE.'/components/com_rsform/rsform.php')) 
			return 1;
		
		if (empty($this->_formstotal)) {
			$this->_formstotal = $this->getCount($this->_formsquery);
		}
		
		return $this->_formstotal;
	}
	
	public function getPagination() {
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination(1, $this->getState('com_rseventspro.events.limitstart'), $this->getState('com_rseventspro.events.limit'));
		}
		return $this->_pagination;
	}
	
	public function getFormsPagination() {
		if (empty($this->_formspagination)) {
			jimport('joomla.html.pagination');
			$this->_formspagination = new JPagination($this->getFormsTotal(), $this->getState('com_rseventspro.events.limitstart'), $this->getState('com_rseventspro.events.limit'));
		}
		return $this->_formspagination;
	}
	
	public function getFilters() {
		$columns 	= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_columns', 	'filter_from', 		array(), 'array');
		$operators 	= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_operators',	'filter_condition', array(), 'array');
		$values 	= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_values',		'search', 			array(), 'array');
		
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
		
		$columns = array_merge($columns);
		$operators = array_merge($operators);
		$values = array_merge($values);
		
		if ($this->_input->get('rs_remove') != '') {
			unset($columns[$this->_input->get('rs_remove')]);
			unset($operators[$this->_input->get('rs_remove')]);
			unset($values[$this->_input->get('rs_remove')]);
			$this->_app->setUserState('com_rseventspro.events.filter_columns',		array_merge($columns));
			$this->_app->setUserState('com_rseventspro.events.filter_operators',	array_merge($operators));
			$this->_app->setUserState('com_rseventspro.events.filter_values',		array_merge($values));
		}
		
		if ($this->_input->getInt('rs_clear',0)) {
			$this->_app->setUserState('com_rseventspro.events.filter_columns',		array());
			$this->_app->setUserState('com_rseventspro.events.filter_operators',	array());
			$this->_app->setUserState('com_rseventspro.events.filter_values',		array());
			$columns = $operators = $values = array();
		}
		
		return array(array_merge($columns), array_merge($operators), array_merge($values));
	}
	
	public function getOtherFilters() {
		$archived	= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_archived',	'filter_archived',	null);
		$featured	= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_featured',	'filter_featured',	null);
		$childs		= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_child', 		'filter_child',		null);
		$start		= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_start', 		'filter_start',		null);
		$end		= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_end', 		'filter_end',		null);
		
		if ($this->_input->getInt('rs_clear',0)) {
			$this->_app->setUserState('com_rseventspro.events.filter_archived',	null);
			$this->_app->setUserState('com_rseventspro.events.filter_featured',	null);
			$this->_app->setUserState('com_rseventspro.events.filter_child',	null);
			$this->_app->setUserState('com_rseventspro.events.filter_start',	null);
			$this->_app->setUserState('com_rseventspro.events.filter_end',		null);
			return array('archived' => null, 'featured' => null, 'childs' => null, 'start' => null, 'end' => null);
		}
		
		if ($this->_input->get('rs_remove') == 'archive') {
			$this->_app->setUserState('com_rseventspro.events.filter_archived',	null);
			return array('archived' => null, 'childs' => $childs, 'start' => $start, 'end' => $end);
		} elseif ($this->_input->get('rs_remove') == 'child') {
			$this->_app->setUserState('com_rseventspro.events.filter_child',	null);
			return array('archived' => $archived, 'childs' => null, 'start' => $start, 'end' => $end);
		} elseif ($this->_input->get('rs_remove') == 'start') {
			$this->_app->setUserState('com_rseventspro.events.filter_start',	null);
			return array('archived' => $archived, 'childs' => $childs, 'start' => null, 'end' => $end);
		} elseif ($this->_input->get('rs_remove') == 'end') {
			$this->_app->setUserState('com_rseventspro.events.filter_end',	null);
			return array('archived' => $archived, 'childs' => $childs, 'start' => $start, 'end' => null);
		} elseif ($this->_input->get('rs_remove') == 'featured') {
			$this->_app->setUserState('com_rseventspro.events.filter_featured',	null);
			return array('archived' => $archived, 'featured' => null, 'childs' => $childs, 'start' => $start, 'end' => $end);
		}
		
		return array('archived' => $archived, 'featured' => $featured, 'childs' => $childs, 'start' => $start, 'end' => $end);
	}
	
	public function getSortColumn() {
		return $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_order', 'filter_order', 'e.start');
	}
	
	public function getSortOrder() {
		return $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_order_Dir', 'filter_order_Dir', 'DESC');
	}
	
	public function getFilterOptions() { 
		return array(JHTML::_('select.option', 'events', JText::_('COM_RSEVENTSPRO_FILTER_NAME')), JHTML::_('select.option', 'description', JText::_('COM_RSEVENTSPRO_FILTER_DESCRIPTION')), 
			JHTML::_('select.option', 'locations', JText::_('COM_RSEVENTSPRO_FILTER_LOCATION')) ,JHTML::_('select.option', 'categories', JText::_('COM_RSEVENTSPRO_FILTER_CATEGORY')),
			JHTML::_('select.option', 'tags', JText::_('COM_RSEVENTSPRO_FILTER_TAG')), JHTML::_('select.option', 'featured', JText::_('COM_RSEVENTSPRO_FILTER_FEATURED')),
			JHTML::_('select.option', 'archived', JText::_('COM_RSEVENTSPRO_FILTER_ARCHIVED')), JHTML::_('select.option', 'child', JText::_('COM_RSEVENTSPRO_FILTER_CHILD')), 
			JHTML::_('select.option', 'start', JText::_('COM_RSEVENTSPRO_FILTER_FROM')), JHTML::_('select.option', 'end', JText::_('COM_RSEVENTSPRO_FILTER_TO'))
		);
	}
	
	public function getFilterConditions() {
		return array(JHTML::_('select.option', 'is', JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_IS')), JHTML::_('select.option', 'isnot', JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_ISNOT')),
			JHTML::_('select.option', 'contains', JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_CONTAINS')),JHTML::_('select.option', 'notcontain', JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_NOTCONTAINS'))
		);
	}
	
	public function getOrdering() {
		return array(JHTML::_('select.option', 'e.start', JText::_('COM_RSEVENTSPRO_ORDERING_START_DATE')), JHTML::_('select.option', 'e.end', JText::_('COM_RSEVENTSPRO_ORDERING_END_DATE')),
			JHTML::_('select.option', 'e.name', JText::_('COM_RSEVENTSPRO_ORDERING_NAME')), JHTML::_('select.option', 'e.owner', JText::_('COM_RSEVENTSPRO_ORDERING_OWNER')), 
			JHTML::_('select.option', 'e.location', JText::_('COM_RSEVENTSPRO_ORDERING_LOCATION'))
		);
	}
	
	public function getOrder() {
		return array(JHTML::_('select.option', 'ASC', JText::_('COM_RSEVENTSPRO_GLOBAL_ASCENDING')), 
			JHTML::_('select.option', 'DESC', JText::_('COM_RSEVENTSPRO_GLOBAL_DESCENDING'))
		);
	}
	
	/**
	 * Method to set the side bar.
	 */
	public function getSidebar() {
		if (rseventsproHelper::isJ3()) {
			return JHtmlSidebar::render();
		}
		
		return;
	}
}