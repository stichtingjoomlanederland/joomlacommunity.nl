<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproModelEvents extends JModelLegacy
{
	protected $_query			= null;
	protected $_pastquery		= null;
	protected $_ongoingquery	= null;
	protected $_thisweekquery	= null;
	protected $_thismonthquery	= null;
	protected $_nextmonthquery	= null;
	protected $_upcomingquery	= null;
	protected $_formsquery		= null;
	
	protected $_data			= null;
	protected $_pastdata		= null;
	protected $_ongoingdata		= null;
	protected $_thisweekdata	= null;
	protected $_thismonthdata	= null;
	protected $_nextmonthdata	= null;
	protected $_upcomingdata	= null;
	protected $_formsdata		= null;
	
	protected $_total			= 0;
	protected $_pasttotal		= 0;
	protected $_ongoingtotal	= 0;
	protected $_thisweektotal	= 0;
	protected $_thismonthtotal	= 0;
	protected $_nextmonthtotal	= 0;
	protected $_upcomingtotal	= 0;
	protected $_formstotal		= 0;
	
	protected $_id				= 0;
	protected $_db				= null;
	protected $_app				= null;
	protected $_input			= null;
	protected $_join			= null;
	protected $offset			= null;
	protected $_where			= null;
	protected $_filters			= null;
	protected $_other			= null;
	protected $_pagination		= null;
	protected $_formspagination	= null;
	
	protected $_operator		= 'AND';
	
	/**
	 *	Main constructor
	 */
	public function __construct() {
		parent::__construct();
		
		$this->_db	= JFactory::getDBO();
		$this->_app = JFactory::getApplication();
		$this->_input = $this->_app->input;
		$config = JFactory::getConfig();
		
		$layout = $this->_input->get('layout');
		$list	= rseventsproHelper::getConfig('backendlist','int',0);
		
		// Get pagination request variables
		$limit = $this->_app->getUserStateFromRequest('com_rseventspro.events.limit', 'limit', $config->get('list_limit'), 'int');
		$limitstart = $this->_input->getInt('lstart', 0);
		
		if ($layout == 'forms' || $list) {
			$limitstart = $this->_input->getInt('limitstart', 0);
		}
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('com_rseventspro.events.limit', $limit);
		$this->setState('com_rseventspro.events.limitstart', $limitstart);		
		
		$timezone = new DateTimeZone(rseventsproHelper::getTimezone());
		$this->offset = $timezone->getOffset(new DateTime('now', new DateTimeZone('UTC')));
		
		if ($layout == 'default' || $layout == '' || $layout == 'items' || $layout == 'menu') {
			$this->_filters		= $this->getFilters();
			$this->_other		= $this->getOtherFilters();
			$this->_operator	= $this->getOperator();
			$this->_where		= $this->_buildWhere();
			$this->_join		= $this->_buildJoin();
			
			if ($list) {
				$this->_query			= $this->getEventsQuery();
			} else {			
				$this->_pastquery		= $this->getPastEventsQuery();
				$this->_ongoingquery	= $this->getOngoingEventsQuery();
				$this->_thisweekquery	= $this->getThisWeekEventsQuery();
				$this->_thismonthquery	= $this->getThisMonthEventsQuery();
				$this->_nextmonthquery	= $this->getNextMonthEventsQuery();
				$this->_upcomingquery	= $this->getUpcomingEventsQuery();
			}
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
				default:
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
		
		if (!is_null($statuses = $this->_other['status'])) {
			foreach ($statuses as $status) {
				if ($status == '') continue;
				$where[] = $this->_db->qn('e.published').' = '.(int) $status;
			}
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
			$from = JFactory::getDate($from, rseventsproHelper::getTimezone());
			$from = $from->format('Y-m-d H:i:s');
		}
		
		if (!is_null($to)) {
			$to = JFactory::getDate($to, rseventsproHelper::getTimezone());
			$to = $to->format('Y-m-d H:i:s');
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

		$todayUTC = JFactory::getDate();
		$todayUTC->setTime(0,0,0);
		$todayUTC = $todayUTC->format('Y-m-d H:i:s');
		
		$today = JFactory::getDate();
		$today->setTimezone(new DateTimeZone(rseventsproHelper::getTimezone()));
		$today->setTime(0,0,0);
		$today = $today->format('Y-m-d H:i:s');
		
		if ($type == 'past') {
			$query->where('('.$this->_db->qn('start').' < '.$this->_db->q($today).' AND '.$this->_db->qn('start').' < '.$this->_db->q($todayUTC).')');
		} elseif ($type == 'ongoing') {
			$query->where('('.$this->_db->qn('start').' = '.$this->_db->q($today).' OR '.$this->_db->qn('start').' = '.$this->_db->q($todayUTC).')');
		} elseif ($type == 'thisweek') {
			$now = JFactory::getDate();
			$currentDayOfWeek = $now->format('N');
			$daysToLastDayOfWeek = 7 - $currentDayOfWeek;
			$now->modify('+'.$daysToLastDayOfWeek.' days');
			$now->setTime(23,59,59);
			$end_of_week = $now->toSql();
			
			$tomorrow = JFactory::getDate(rseventsproHelper::showdate('now','Y-m-d H:i:s'));
			$tomorrow->modify('+1 days');
			$tomorrow->setTime(0,0,0);
			
			$query->where('DATE_ADD('.$this->_db->qn('start').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($tomorrow->toSql()));
			$query->where('DATE_ADD('.$this->_db->qn('start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($end_of_week));
		} elseif ($type == "thismonth") {
			$now = JFactory::getDate();
			$now->setTime(23,59,59);
			$end_of_month = $now->format('Y-m-t H:i:s');
			
			$now = JFactory::getDate();
			$currentDayOfWeek = $now->format('N');
			$daysToLastDayOfWeek = 7 - $currentDayOfWeek + 1;
			$now->modify('+'.$daysToLastDayOfWeek.' days');
			$now->setTime(0,0,0);
			$start = $now->toSql();
			
			$query->where('DATE_ADD('.$this->_db->qn('start').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($start));
			$query->where('DATE_ADD('.$this->_db->qn('start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($end_of_month));
		} elseif ($type == 'nextmonth') {
			$now = JFactory::getDate();
			$now->modify('first day of next month');
			$now->setTime(0,0,0);
			$start = $now->toSql();
			$now->setTime(23,59,59);
			$end = $now->format('Y-m-t H:i:s');
			
			$query->where('DATE_ADD('.$this->_db->qn('start').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($start));
			$query->where('DATE_ADD('.$this->_db->qn('start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($end));
		}
		
		$this->_db->setQuery($query);
		if ($events = $this->_db->loadColumn()) {
			JArrayHelper::toInteger($events);
			return $events;
		}
		
		return false;
	}
	
	/**
	 *	Method to get events
	 *
	 *	return JDatabaseQuery object;
	 */
	public function getEventsQuery() {
		$query = $this->_db->getQuery(true);
		$query->clear()
			->select($this->_db->qn('e.id'))
			->from($this->_db->qn('#__rseventspro_events','e'));
		
		if ($this->_join)
			$query->join('left', $this->_db->qn('#__rseventspro_locations','l').' ON '.$this->_db->qn('l.id').' = '.$this->_db->qn('e.location'));
		
		if (!empty($this->_where)) {
			$query->where('('.implode(' '.$this->_operator.' ',$this->_where).')');
		}
		
		$sortColumn = $this->getSortColumn();
		$sortOrder 	= $this->getSortOrder();
		
		$query->order($this->_db->qn($sortColumn).' '.$this->_db->escape($sortOrder));
		
		return $query;
	}
	
	
	 /**
	 *	Method to get past events
	 *
	 *	return JDatabaseQuery object;
	 */
	public function getPastEventsQuery() {
		$now	 = rseventsproHelper::showdate('now','Y-m-d H:i:s');
		$query	 = $this->_db->getQuery(true);
		$include = $this->_getAllDayEvents('past');
		
		$query->clear()
			->select($this->_db->qn('e.id'))
			->from($this->_db->qn('#__rseventspro_events','e'));
		
		if ($this->_join) {
			$query->join('left', $this->_db->qn('#__rseventspro_locations','l').' ON '.$this->_db->qn('l.id').' = '.$this->_db->qn('e.location'));
		}
		
		if (!empty($include)) {
			$query->where('(('.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where('DATE_ADD('.$this->_db->qn('e.end').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($now).') OR '.$this->_db->qn('e.id').' IN ('.implode(',',$include).'))');
		} else {
			$query->where($this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where('DATE_ADD('.$this->_db->qn('e.end').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($now));
		}
		
		if (!empty($this->_where)) {
			$query->where('('.implode(' '.$this->_operator.' ',$this->_where).')');
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
		$now	 = rseventsproHelper::showdate('now','Y-m-d H:i:s');
		$query	 = $this->_db->getQuery(true);
		$include = $this->_getAllDayEvents('ongoing');
		
		$query->clear()
			->select($this->_db->qn('e.id'))
			->from($this->_db->qn('#__rseventspro_events','e'));
		
		if ($this->_join) {
			$query->join('left', $this->_db->qn('#__rseventspro_locations','l').' ON '.$this->_db->qn('l.id').' = '.$this->_db->qn('e.location'));
		}
		
		if (!empty($include)) {
			$query->where('(('.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where('DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($now));
			$query->where('DATE_ADD('.$this->_db->qn('e.end').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($now).') OR '.$this->_db->qn('e.id').' IN ('.implode(',',$include).'))');
		} else {
			$query->where($this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where('DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($now));
			$query->where('DATE_ADD('.$this->_db->qn('e.end').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($now));
		}
		
		if (!empty($this->_where)) {
			$query->where('('.implode(' '.$this->_operator.' ',$this->_where).')');
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
		$nowDate = JFactory::getDate();
		$now	 = rseventsproHelper::showdate('now','Y-m-d H:i:s');
		$exclude = array();
		$include = $this->_getAllDayEvents('thisweek');
		
		$currentDayOfWeek = $nowDate->format('N');
		$daysToLastDayOfWeek = 7 - $currentDayOfWeek;
		$nowDate->modify('+'.$daysToLastDayOfWeek.' days');
		$nowDate->setTime(23,59,59);
		$end_of_week = $nowDate->toSql();
		
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
			$query->where('((DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($now). ' AND DATE_ADD('.$this->_db->qn('e.end').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($now).') OR (DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($now).' AND DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($end_of_week).'))) OR '.$this->_db->qn('e.id').' IN ('.implode(',',$include).'))');
		} else {
			$query->where($this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where('((DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($now). ' AND DATE_ADD('.$this->_db->qn('e.end').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($now).') OR (DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($now).' AND DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($end_of_week).'))');
		}
		
		if (!empty($this->_where)) {
			$query->where('('.implode(' '.$this->_operator.' ',$this->_where).')');
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
		$nowDate = JFactory::getDate();
		$now	 = rseventsproHelper::showdate('now','Y-m-d H:i:s');
		$nowDate->setTime(23,59,59);
		$end_of_month = $nowDate->format('Y-m-t H:i:s');
		
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
			$query->where('((DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($now). ' AND DATE_ADD('.$this->_db->qn('e.end').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($now).') OR (DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($now).' AND DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($end_of_month).'))) OR '.$this->_db->qn('e.id').' IN ('.implode(',',$include).'))');
		} else {
			$query->where($this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where('((DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($now). ' AND DATE_ADD('.$this->_db->qn('e.end').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($now).') OR (DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($now).' AND DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($end_of_month).'))');
		}
		
		if (!empty($this->_where)) {
			$query->where('('.implode(' '.$this->_operator.' ',$this->_where).')');
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
		$nowDate = JFactory::getDate();
		$nowDate->modify('first day of next month');
		$nowDate->setTime(0,0,0);
		$start = $nowDate->toSql();
		$nowDate->setTime(23,59,59);
		$end = $nowDate->format('Y-m-t H:i:s');
		
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
			$query->where('((DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($start). ' AND DATE_ADD('.$this->_db->qn('e.end').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($start).') OR (DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($start).' AND DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($end).'))) OR '.$this->_db->qn('e.id').' IN ('.implode(',',$include).'))');
		} else {
			$query->where($this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			$query->where('((DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($start). ' AND DATE_ADD('.$this->_db->qn('e.end').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($start).') OR (DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($start).' AND DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) <= '.$this->_db->q($end).'))');
		}
		
		if (!empty($this->_where)) {
			$query->where('('.implode(' '.$this->_operator.' ',$this->_where).')');
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
		$nowDate = JFactory::getDate();
		$nowDate->modify('first day of this month');
		$nowDate->modify('+2 months');
		$nowDate->setTime(0,0,0);
		$start = $nowDate->format('Y-m-d H:i:s');
		
		$query = $this->_db->getQuery(true);
		$query->clear()
			->select($this->_db->qn('e.id'))
			->from($this->_db->qn('#__rseventspro_events','e'));
		
		if ($this->_join)
			$query->join('left', $this->_db->qn('#__rseventspro_locations','l').' ON '.$this->_db->qn('l.id').' = '.$this->_db->qn('e.location'));
		
		$query->where('DATE_ADD('.$this->_db->qn('e.start').', INTERVAL '.$this->offset.' SECOND) >= '.$this->_db->q($start));
		
		if (!empty($this->_where)) {
			$query->where('('.implode(' '.$this->_operator.' ',$this->_where).')');
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
	 *	Method to get events data
	 *
	 *	return array;
	 */
	public function getEvents() {
		if (empty($this->_data)) {
			$this->_db->setQuery($this->_query, $this->getState('com_rseventspro.events.limitstart'), $this->getState('com_rseventspro.events.limit'));
			$this->_data = $this->_db->loadColumn();
		}		
		return $this->_data;
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
			$this->_db->setQuery((string) $this->_formsquery, $this->getState('com_rseventspro.events.limitstart'), $this->getState('com_rseventspro.events.limit'));
			$this->_formsdata = $this->_db->loadObjectList();
		}
		return $this->_formsdata;
	}
	
	
	protected function getCount($query) {
		$version = new JVersion();
		if ($version->isCompatible('3.0')) {
			if ($query instanceof JDatabaseQuery
				&& $query->type == 'select'
				&& $query->group === null
				&& $query->having === null)
			{
				$query = clone $query;
				$query->clear('select')->clear('order')->clear('limit')->select('COUNT(*)');

				$this->_db->setQuery($query);
				return (int) $this->_db->loadResult();
			}
		}

		// Otherwise fall back to inefficient way of counting all results.
		$this->_db->setQuery($query);
		$this->_db->execute();

		return (int) $this->_db->getNumRows();
	}
	
	public function getTotal() {
		if (empty($this->_total)) {
			$this->_total = $this->getCount($this->_query); 
		}
		
		return $this->_total;
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
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('com_rseventspro.events.limitstart'), $this->getState('com_rseventspro.events.limit'));
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
			$filter = JFilterInput::getInstance();
			foreach ($values as $i => $value) {
				if (empty($value)) {
					if (isset($columns[$i])) unset($columns[$i]);
					if (isset($operators[$i])) unset($operators[$i]);
					if (isset($values[$i])) unset($values[$i]);
				}
				
				$values[$i] = $filter->clean($value,'string');
			}
		}
		
		return array(array_merge($columns), array_merge($operators), array_merge($values));
	}
	
	public function getOtherFilters() {
		$status		= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_status',		'filter_status',	array(), 'array');
		$featured	= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_featured',	'filter_featured',	array(), 'array');
		$childs		= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_child', 		'filter_child',		array(), 'array');
		$start		= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_start', 		'filter_start',		array(), 'array');
		$end		= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_end', 		'filter_end',		array(), 'array');
		
		$status		= $status[0] == '' ? null : $status;
		$featured	= $featured[0] == '' ? null : $featured[0];
		$childs		= $childs[0] == '' ? null : $childs[0];
		$start		= $start[0] == '' ? null : $start[0];
		$end		= $end[0] == '' ? null : $end[0];
		
		if (is_array($status)) {
			$status = array_unique($status);
			
			foreach ($status as $key => $option) {
				if ($option == '') unset($status[$key]);
			}	
		}
		
		return array('status' => $status, 'featured' => $featured, 'childs' => $childs, 'start' => $start, 'end' => $end);
	}
	
	public function getConditionsNr() {
		$filters	= $this->getFilters();
		$other		= $this->getOtherFilters();
		$columns	= isset($filters[0]) ? $filters[0] : array();
		$count		= 0;
		
		foreach($columns as $column) {
			if ($column == '') continue;
			$count++;
		}
		
		if (!is_null($other['status'])) {
			foreach ($other['status'] as $status) {
				if ($status == '') continue;
				$count++;
			}
		}
			
		if (!is_null($other['featured'])) {
			$count++;
		}
		
		if (!is_null($other['childs'])) {
			$count++;
		}
		
		if (!is_null($other['start'])) {
			$count++;
		}
		
		if (!is_null($other['end'])) {
			$count++;
		}
		
		return $count;
	}
	
	public function getOperator() {
		$valid		= array('AND', 'OR');
		$operator	= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_operator', 'filter_operator', 'AND');
		
		return !in_array($operator, $valid) ? 'AND' : $operator;		
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
			JHTML::_('select.option', 'status', JText::_('COM_RSEVENTSPRO_FILTER_STATUS')), JHTML::_('select.option', 'child', JText::_('COM_RSEVENTSPRO_FILTER_CHILD')), 
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
	
	public function getOrderingText() {
		$ordering = $this->getOrdering();
		foreach ($ordering as $order) {
			if ($order->value == $this->getSortColumn()) {
				return $order->text;
			}
		}
		
		return JText::_('COM_RSEVENTSPRO_ORDERING_START_DATE');
	}
	
	public function getOrder() {
		return array(JHTML::_('select.option', 'ASC', JText::_('COM_RSEVENTSPRO_GLOBAL_ASCENDING')), 
			JHTML::_('select.option', 'DESC', JText::_('COM_RSEVENTSPRO_GLOBAL_DESCENDING'))
		);
	}
	
	public function getOrderText() {
		$order = $this->getOrder();
		foreach ($order as $direction) {
			if ($direction->value == $this->getSortOrder()) {
				return $direction->text;
			}
		}
		
		return JText::_('COM_RSEVENTSPRO_GLOBAL_DESCENDING');
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
	
	/**
	 * Method to get Tabs
	 *
	 * @return	mixed	The Joomla! Tabs.
	 * @since	1.6
	 */
	public function getTabs() {
		$tabs = new RSTabs('batch');
		return $tabs;
	}
}