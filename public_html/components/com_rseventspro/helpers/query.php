<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RSEventsProQuery
{
	/**
	 * Array to hold the object instances
	 *
	 * @var    array
	 */
	public static $instances = array();
	
	/**
	 * Parameters
	 *
	 * @var    array
	 */
	protected $params;
	
	/**
	 * Events filters
	 *
	 * @var    array
	 */
	protected $filters;
	
	/**
	 * Extra events filters
	 *
	 * @var    array
	 */
	protected $extra;
	
	/**
	 * Query selectors
	 *
	 * @var    mixed
	 */
	protected $select;
	
	/**
	 * WHERE element
	 *
	 * @var    string
	 */
	protected $where;
	
	/**
	 * Query order
	 *
	 * @var    string
	 */
	protected $order;
	
	/**
	 * Query order direction
	 *
	 * @var    string
	 */
	protected $direction;
	
	/**
	 * GROUP BY element
	 *
	 * @var    string
	 */
	protected $group;
	
	/**
	 * HAVING element
	 *
	 * @var    string
	 */
	protected $having;
	
	/**
	 * Query operator
	 *
	 * @var    string
	 */
	protected $operator = 'AND';
	
	/**
	 * Set featured
	 *
	 * @var    boolean
	 */
	protected $featured = true;
	
	/**
	 * Show/Hide users incomplete/unpublished events
	 *
	 * @var    boolean
	 */
	protected $userevents = true;
	
	/**
	 * Filter events by price
	 *
	 * @var    boolean
	 */
	protected $filterPrice = true;
	
	/**
	 * Class constructor
	 *
	 * @param   mixed  $params  Query params
	 *
	 */
	public function __construct($params) {
		$this->params	= $params;
		$this->filters	= $this->filters();
		$this->extra	= $this->extraFilters();
		$this->operator	= $this->operator();
	}
	
	/**
	 * Returns a reference to a RSEventsProQuery object
	 *
	 * @param   mixed  $params  Query params
	 *
	 * @return  RSEventsProQuery   RSEventsProQuery object
	 *
	 */
	public static function getInstance($params) {
		$hash = md5(serialize($params));
		
		if (!isset(self::$instances[$hash])) {
			$classname = 'RSEventsProQuery';
			self::$instances[$hash] = new $classname($params);
		}
		
		return self::$instances[$hash];
	}
	
	/**
	 * Method to get the events query
	 *
	 * @return   string  Events query
	 *
	 */
	public function toString() {
		$db		= JFactory::getDbo();
		$user	= JFactory::getUser();
		$input	= JFactory::getApplication()->input;
		$where	= array();
		
		// Set the list of parameters
		$tzoffset	= rseventsproHelper::getTimezone();
		$parent		= $input->getInt('parent',0);
		$listType	= $this->params->get('list','all');
		$order		= $this->params->get('ordering','start');
		$direction	= $this->params->get('order','DESC');
		$categories	= $this->params->get('categories','');
		$locations	= $this->params->get('locations','');
		$tags		= $this->params->get('tags','');
		$from		= $this->params->get('from','');
		$to			= $this->params->get('to','');
		$repeat		= (int) $this->params->get('repeat',1);
		$counter	= (int) $this->params->get('repeatcounter',1);
		$days		= (int) $this->params->get('days',0);
		
		if ($select = $this->select) {
			$select = is_array($select) ? $this->implodeSql($select) : $db->qn($select);
		} else {
			$select = $db->qn('e.id');
		}
		
		// Start the main query
		$query = 'SELECT '.$select.' FROM '.$db->qn('#__rseventspro_events','e');
		
		// Join over the locations table
		$query .= ' LEFT JOIN '.$db->qn('#__rseventspro_locations','l').' ON '.$db->qn('l.id').' = '.$db->qn('e.location');
		
		// Join over the tickets table
		$query .= ' LEFT JOIN '.$db->qn('#__rseventspro_tickets','tickets').' ON '.$db->qn('tickets.ide').' = '.$db->qn('e.id');
		
		$query .= ' WHERE 1';
		
		// Main query where
		$query .= ' AND (';
		
		// Only show completed events
		$query .= $db->qn('e.completed').' = '.$db->q(1);
		
		// The events state
		if ($listType == 'archived') {
			$state = '2';
		} else {
			if ($this->params->get('archived',0)) {
				$state = '1,2';
			} else { 
				$state = '1';
			}
		}
		
		$query .= ' AND '.$db->qn('e.published').' IN ('.$state.')';
		
		// Exclude events that the current user has no permission 
		if ($exclude = rseventsproHelper::excludeEvents()) {
			$query .= ' AND '.$db->qn('e.id').' NOT IN ('.implode(',',$exclude).')';
		}
		
		if (isset($this->where)) {
			$where[] = ' AND '.$this->where;
		}
		
		// Filter events with the menu item categories filter
		if (!empty($categories)) {
			$categories = array_map('intval',$categories);
			$groups		= implode(',', $user->getAuthorisedViewLevels());
			$catwhere	= 'AND '.$db->qn('c.access').' IN ('.$groups.')';
			
			if (JLanguageMultilang::isEnabled()) {
				$catwhere .= ' AND '.$db->qn('c.language').' IN ('.$db->q(JFactory::getLanguage()->getTag()).','.$db->q('*').') ';
			}
			
			$where[] = ' AND '.$db->qn('e.id').' IN (SELECT '.$db->qn('tx.ide').' FROM '.$db->qn('#__rseventspro_taxonomy','tx').' LEFT JOIN '.$db->qn('#__categories','c').' ON '.$db->qn('c.id').' = '.$db->qn('tx.id').' WHERE '.$db->qn('c.id').' IN ('.implode(',',$categories).') AND '.$db->qn('tx.type').' = '.$db->q('category').' AND '.$db->qn('c.extension').' = '.$db->q('com_rseventspro').' '.$catwhere.')';
		}
		
		// Filter events with the menu item tags filter
		if (!empty($tags)) {
			$tags = array_map('intval',$tags);
			
			$where[] = ' AND '.$db->qn('e.id').' IN (SELECT '.$db->qn('tx.ide').' FROM '.$db->qn('#__rseventspro_taxonomy','tx').' LEFT JOIN '.$db->qn('#__rseventspro_tags','t').' ON '.$db->qn('t.id').' = '.$db->qn('tx.id').' WHERE '.$db->qn('t.id').' IN ('.implode(',',$tags).') AND '.$db->qn('tx.type').' = '.$db->q('tag').')';
		}
		
		// Filter events with the menu item locations filter
		if (!empty($locations)) {
			$locations = array_map('intval',$locations);
			
			$where[] = ' AND '.$db->qn('e.location').' IN ('.implode(',',$locations).')';
		}
		
		// Check the "Events starting from" menu item option
		if (!empty($from)) {
			if (strtolower($from) == 'today') {
				$from = JFactory::getDate();
				$from->setTimezone(new DateTimezone($tzoffset));
				$from->setTime(0,0,0);
				$from = $from->toSql();
			} else {
				// The timezone is needed because of what we did to also have 'today' as a value
				$from = JFactory::getDate($from, $tzoffset)->toSql();
			}
		}
		
		// Check the "Events ending on" menu item option
		if (!empty($to)) {
			$to = JFactory::getDate($to)->toSql();
		}
		
		// Select events in the specific interval
		if (empty($from) && !empty($to)) {
			// Get events that end before the 'to' date
			$where[] = ' AND (('.$db->qn('e.end').' <> '.$db->q($this->getNullDate()).' AND '.$db->qn('e.end').' <= '.$db->q($to).') OR ('.$db->qn('e.end').' = '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' <= '.$db->q($to).'))';
		} elseif (!empty($from) && empty($to)) {
			// Get events that start after the 'from' date
			$where[] = ' AND '.$db->qn('e.start').' >= '.$db->q($from);
		} elseif (!empty($from) && !empty($to)) {
			$where[] = $this->betweenQuery($from, $to);
		}
		
		if ($parent && $counter) {
			// Show child events
			$where[] = ' AND '.$db->qn('e.parent').' = '.$db->q($parent);
		} else {
			// Only show parent events
			if (!$repeat) {
				$where[] = ' AND '.$db->qn('e.parent').' = 0';
			}
			
			// Events list type
			if ($listType == 'past') {
				// List past events
				$now = JFactory::getDate('now',$tzoffset)->toSql();
				
				$p1 = '('.$db->qn('e.end').' <> '.$db->q($this->getNullDate()).' AND '.$db->qn('e.end').' < '.$db->q($now).')';
				$p2 = '('.$db->qn('e.end').' = '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' < '.$db->q($now).')';
				
				$where[] = ' AND ( '.$p1.' OR  '.$p2.' )';
			} else if ($listType == 'today') {
				$date = JFactory::getDate('now', $tzoffset);
				$date->setTime(0,0,0);
				$today = $date->format('Y-m-d H:i:s');
				$date->modify('+1 days');
				$tomorrow = $date->toSql();
				
				// List today events
				$p1 = '('.$db->qn('e.end').' = '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' = '.$db->q($today).')';
				$p2 = '('.$db->qn('e.end').' <> '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' <= '.$db->q($today).' AND '.$db->qn('e.end').' >= '.$db->q($today).')';
				$p3 = '('.$db->qn('e.end').' <> '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' >= '.$db->q($today).' AND '.$db->qn('e.start').' < '.$db->q($tomorrow).')';
				
				$where[] = ' AND ( '.$p1.' OR  '.$p2.' OR '.$p3.' )';
			} else if ($listType == 'upcoming') {
				// List upcoming events
				
				$where[] = ' AND '.$db->qn('e.start').' >= '.$db->q(JFactory::getDate()->toSql());
			} else if ($listType == 'featured') {
				// List featured events
				$where[] = ' AND '.$db->qn('e.featured').' = '.$db->q(1);
			} else if ($listType == 'future') {
				// Based on the days parameter, show events that start from x days. If days == 0, then show events that start from today
				if ($days > 0) {
					$start = JFactory::getDate();
					$start->modify('+'.$days.' days');
					$start->setTimezone(new DateTimezone($tzoffset));
					$start->setTime(0,0,0);
					$start	= $start->toSql();
				} else {
					$start = JFactory::getDate();
					$start->setTimezone(new DateTimezone($tzoffset));
					$start->setTime(0,0,0);
					$start	= $start->toSql();
				}
				
				$where[] = ' AND '. $db->qn('e.start').' >= '.$db->q($start);
			} else if ($listType == 'user') {
				// List users events
				if ($userID = (int) $user->get('id')) {
					$where[] = ' AND '.$db->qn('e.owner').' = '.$db->q($userID);
				}
			} else if ($listType == 'thisweek') {
				// List this week events
				
				// Get Monday
				$startDate = JFactory::getDate('now',$tzoffset);
				$startDate->modify('this monday');
				$startDate->setTime(0,0,0);
				$start = $startDate->toSql();
				
				// Get Sunday
				$endDate = JFactory::getDate('now',$tzoffset);
				$endDate->modify('this sunday');
				$endDate->setTime(23,59,59);
				$end = $endDate->toSql();
				
				if ($startDate >= $endDate) {
					$startDate = JFactory::getDate('now', $tzoffset);
					$startDate->modify('previous monday');
					$startDate->setTime(0,0,0);
					$start = $startDate->toSql();
				}
				
				$where[] = $this->betweenQuery($start, $end);
			} else if ($listType == 'nextweek') {
				// List next week events
				
				$start = JFactory::getDate('now', $tzoffset);
				$start->modify('next monday');
				$start->setTime(0,0,0);
				$start = $start->toSql();
				
				$end = JFactory::getDate('now', $tzoffset);
				
				if ($end->format('N') == 7) {
					$end->modify('next sunday');
				} else {
					$end->modify('sunday next week');
				}
				
				$end->setTime(23,59,59);
				$end = $end->toSql();
				
				$where[] = $this->betweenQuery($start, $end);
			} else if ($listType == 'thisweekend') {
				// List events from this weekend
				
				$startDate = JFactory::getDate('now', $tzoffset);
				$startDate->modify('this saturday');
				$startDate->setTime(0,0,0);
				$start = $startDate->toSql();
				
				$endDate = JFactory::getDate('now', $tzoffset);
				$endDate->modify('this sunday');
				$endDate->setTime(23,59,59);
				$end = $endDate->toSql();
				
				if ($startDate >= $endDate) {
					$startDate = JFactory::getDate('now', $tzoffset);
					$startDate->modify('previous saturday');
					$startDate->setTime(0,0,0);
					$start = $startDate->toSql();
				}
				
				$where[] = $this->betweenQuery($start, $end);
			} else if ($listType == 'nextweekend') {
				// List events from next weekend
				
				$start = JFactory::getDate('now', $tzoffset);
				
				if ($start->format('N') == 7) {
					$start->modify('next saturday');
				} else {
					$start->modify('saturday next week');
				}
				
				$start->setTime(0,0,0);
				$start = $start->toSql();
				
				$end = JFactory::getDate('now', $tzoffset);
				if ($end->format('N') == 7) {
					$end->modify('next sunday');
				} else {
					$end->modify('sunday next week');
				}
				
				$end->setTime(23,59,59);
				$end = $end->toSql();
				
				$where[] = $this->betweenQuery($start, $end);
			} else if ($listType == 'thismonth') {
				// List events from this month
				
				$start = JFactory::getDate('now', $tzoffset);
				$start->modify('first day of this month');
				$start->setTime(0,0,0);
				$start = $start->toSql();
				
				$end = JFactory::getDate('now', $tzoffset);
				$end->modify('last day of this month');
				$end->setTime(23,59,59);
				$end = $end->toSql();
				
				$where[] = $this->betweenQuery($start, $end);
			} else if ($listType == 'nextmonth') {
				// List events from next month
				
				$start = JFactory::getDate('now', $tzoffset);
				$start->modify('first day of next month');
				$start->setTime(0,0,0);
				$start = $start->toSql();
				
				$end = JFactory::getDate('now', $tzoffset);
				$end->modify('last day of next month');
				$end->setTime(23,59,59);
				$end = $end->toSql();
				
				$where[] = $this->betweenQuery($start, $end);
			} else if ($listType == 'ongoing') {
				// List ongoing events
				
				$now = JFactory::getDate('now',$tzoffset);
				$now = $now->toSql();
				
				$today = JFactory::getDate('now',$tzoffset);
				$today->setTime(0,0,0);
				$today = $today->toSql();
				
				// Get regular events that are ongoing
				$o1 = '('.$db->qn('e.end').' <> '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' <= '.$db->q($now).' AND '.$db->qn('e.end').' >= '.$db->q($now).')';
				
				// Get all day events that are ongoing
				$o2 = '('.$db->qn('e.end').' = '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' = '.$db->q($today).')';
				
				$where[] = ' AND ( '.$o1.' OR  '.$o2.' )';
			}
		}
		
		// Add conditions based on user and menu item options
		if (!empty($where)) {
			$query .= implode(' ',$where);
		}
		
		// Add conditions from the Events filter area
		if ($filtersWhere = $this->whereFilters()) {
			$query .= $filtersWhere;
		}
		
		$query .= ' )';
		
		// Load users unpublished / incomplete events
		$filters = empty($this->filters[0]) ? 0 : 1;
		if ($input->getInt('location',0) == 0 && $input->getString('tag') == '' && $input->getString('category') == '' && empty($filters) && $this->userevents) {
			$subquery = ' OR (';
			
			$eventState = $listType == 'archived' ? '2' : '0,1';
			if ($user->get('id') > 0) {
				$subquery .= $db->qn('e.owner').' = '.(int) $user->get('id').' AND '.$db->qn('e.published').' IN ('.$eventState.') AND '.$db->qn('e.completed').' IN (0,1)';
			} else {
				$sid = JFactory::getSession()->getId();
				$subquery .= $db->qn('e.sid').' = '.$db->q($sid).' AND '.$db->qn('e.published').' IN ('.$eventState.') AND '.$db->qn('e.completed').' IN (0,1)';
			}
			
			// Add conditions based on user and menu item options
			if (!empty($where)) {
				$subquery .= implode(' ',$where);
			}
			
			// Add conditions from the Events filter area
			if ($filtersWhere = $this->whereFilters()) {
				$subquery .= $filtersWhere;
			}
			
			$subquery .= ')';
			
			$query .= $subquery;
		}
		
		// Fix ordering for when the menu item is a categories menu item
		if ($order == 'title' || $order == 'c.title')	$order = 'name';
		if ($order == 'lft' || $order == 'c.lft')		$order = 'start';
		
		$order		= isset($this->order) ? $this->order : 'e.'.$order;
		$direction	= isset($this->direction) ? $this->direction : $direction;
		
		// Set the ordering and the order
		$featured_condition = $this->featured ? (rseventsproHelper::getConfig('featured','int') ? $db->qn('e.featured').' DESC, ' : '') : '';
		
		if (isset($this->group)) {
			$groupBy = is_array($this->group) ? $this->implodeSql($this->group) : $db->qn($this->group);
			$query .= ' GROUP BY '.$groupBy;
		}
		
		if (isset($this->having)) {
			$query .= ' HAVING '.$this->having;
		}
		
		$query .= ' ORDER BY '.$featured_condition.' '.$db->qn($order).' '.$db->escape($direction).' ';
		
		return $query;
	}
	
	/**
	 * Method to set the query selectors
	 *
	 * @return   void
	 *
	 */
	public function select($select) {
		$this->select = $select;
	}
	
	/**
	 * Method to overwrite the order
	 *
	 * @return   void
	 *
	 */
	public function order($order) {
		$this->order = $order;
	}
	
	/**
	 * Method to overwrite the order direction
	 *
	 * @return   void
	 *
	 */
	public function direction($dir) {
		$this->direction = $dir;
	}
	
	/**
	 * Method to set the GRPUP BY element
	 *
	 * @return   void
	 *
	 */
	public function group($group) {
		$this->group = $group;
	}
	
	/**
	 * Method to set the HAVING element
	 *
	 * @return   void
	 *
	 */
	public function having($having) {
		$this->having = $having;
	}
	
	/**
	 * Method to set the WHERE element
	 *
	 * @return   void
	 *
	 */
	public function where($where) {
		$this->where = $where;
	}
	
	/**
	 * Method to set the featured query
	 *
	 * @return  void
	 *
	 */
	public function featured($featured) {
		$this->featured = (bool) $featured;
	}
	
	/**
	 * Method to show users incomplete/unpublished events
	 *
	 * @return  void
	 *
	 */
	public function userevents($value) {
		$this->userevents = (bool) $value;
	}
	
	/**
	 * Method to filter or not by price
	 *
	 * @return  void
	 *
	 */
	public function price($value) {
		$this->filterPrice = (bool) $value;
	}
	
	/**
	 * Method to get the events filters
	 *
	 * @return   array  Filters
	 *
	 */
	protected function filters($fromrequest = false) {
		$app	= JFactory::getApplication();
		$input	= $app->input;
		$itemid = $input->getInt('Itemid');
		$parent	= $input->getInt('parent');
		
		if ($fromrequest) {
			$columns 	= $input->get('filter_from', 		array(), 'array');
			$operators 	= $input->get('filter_condition',	array(), 'array');
			$values 	= $input->get('search',				array(), 'array');
		} else {
			$columns 	= $app->getUserStateFromRequest('com_rseventspro.events.filter_columns'.$itemid.$parent, 	'filter_from',		array(), 'array');
			$operators 	= $app->getUserStateFromRequest('com_rseventspro.events.filter_operators'.$itemid.$parent,	'filter_condition',	array(), 'array');
			$values 	= $app->getUserStateFromRequest('com_rseventspro.events.filter_values'.$itemid.$parent,		'search',			array(), 'array');
		}
		
		if ($columns && $columns[0] == '') {
			$columns = $operators = $values = array();
		}
		
		if (!empty($values)) {
			$filter = JFilterInput::getInstance();
			foreach ($values as $i => $value) {
				if (empty($value)) {
					if (isset($columns[$i]))	unset($columns[$i]);
					if (isset($operators[$i]))	unset($operators[$i]);
					if (isset($values[$i]))		unset($values[$i]);
				}
				
				$values[$i] = $filter->clean($value,'string');
			}
		}
		
		return array(array_merge($columns), array_merge($operators), array_merge($values));
	}
	
	protected function extraFilters() {
		$app	= JFactory::getApplication();
		$input	= $app->input;
		$itemid = $input->getInt('Itemid');
		$parent	= $input->getInt('parent');
		
		$status		= $app->getUserStateFromRequest('com_rseventspro.events.filter_status'.$itemid.$parent,		'filter_status',	array(), 'array');
		$featured	= $app->getUserStateFromRequest('com_rseventspro.events.filter_featured'.$itemid.$parent,	'filter_featured',	array(), 'array');
		$childs		= $app->getUserStateFromRequest('com_rseventspro.events.filter_child'.$itemid.$parent, 		'filter_child',		array(), 'array');
		$start		= $app->getUserStateFromRequest('com_rseventspro.events.filter_start'.$itemid.$parent, 		'filter_start',		array(), 'array');
		$end		= $app->getUserStateFromRequest('com_rseventspro.events.filter_end'.$itemid.$parent, 		'filter_end',		array(), 'array');
		$price		= $app->getUserStateFromRequest('com_rseventspro.events.filter_price'.$itemid.$parent, 		'filter_price',		array(), 'array');
		
		$status		= isset($status[0]) ? ($status[0] == '' ? null : $status) : null;
		$featured	= isset($featured[0]) ? ($featured[0] 	== '' ? null : $featured[0]) : null;
		$childs		= isset($childs[0]) ? ($childs[0] 	== '' ? null : $childs[0]) : null;
		$start		= isset($start[0]) ? ($start[0] 	== '' ? null : $start[0]) : null;
		$end		= isset($end[0]) ? ($end[0] 		== '' ? null : $end[0]) : null;
		$price		= isset($price[0]) ? ($price[0] 	== '' ? null : $price[0]) : null;
		
		if (is_array($status)) {
			$status = array_unique($status);
			
			foreach ($status as $key => $option) {
				if ($option == '') unset($status[$key]);
			}	
		}
		
		return array('status' => $status, 'featured' => $featured, 'childs' => $childs, 'start' => $start, 'end' => $end, 'price' => $price);
	}
	
	/**
	 * Method to get the query operator
	 *
	 * @return   string  Operator
	 *
	 */
	protected function operator() {
		$app		= JFactory::getApplication();
		$input		= $app->input;
		$itemid 	= $input->getInt('Itemid',0);
		$parent		= $input->getInt('parent',0);
		$valid		= array('AND', 'OR');
		$operator	= $app->getUserStateFromRequest('com_rseventspro.events.filter_operator'.$itemid.$parent, 'filter_operator', 'AND');
		
		return !in_array($operator, $valid) ? 'AND' : $operator;		
	}
	
	/**
	 *	Method to build the where query
	 *
	 *	@return SQL query
	 */
	protected function whereFilters() {
		$db		= JFactory::getDbo();
		$user	= JFactory::getUser();
		$where 	= array();
		
		list($columns, $operators, $values) = $this->filters;
		
		for ($i=0; $i<count($columns); $i++) {
			$column 	= $columns[$i];			
			$operator 	= $operators[$i];
			$value 		= $values[$i];
			$extrac		= 0;
			$extrat		= 0;
			$not		= '';
			
			switch ($column) {
				case 'locations':
					$column = 'l.name';
				break;
				
				case 'description':
					$column = 'e.description';
				break;
				
				case 'categories':
					$column = 'c.title';
					$extrac = 1;
				break;
				
				case 'tags':
					$column = 't.name';
					$extrat = 1;
				break;
				
				default:
				case 'events':
					$column = 'e.name';
				break;
			}
			
			switch ($operator) {
				default:
				case 'contains':
					$operator = 'LIKE';
					$value	  = '%'.str_replace('%', '\%', $value).'%';
				break;
				
				case 'notcontain':
					$operator = $column == 't.name' ? 'LIKE' : 'NOT LIKE';
					$value	  = '%'.str_replace('%', '\%', $value).'%';
					
					if ($column == 't.name') {
						$not = ' NOT';
					}
				break;
				
				case 'is':
					$operator = '=';
				break;
				
				case 'isnot':
					$operator = ($column == 't.name' || $column == 'c.title') ? '=' : '<>';
					
					if ($column == 't.name' || $column == 'c.title') {
						$not = ' NOT';
					}
				break;
			}
			
			if ($extrac) {
				$groups		= implode(',', $user->getAuthorisedViewLevels());
				$catwhere	= ' AND '.$db->qn('c.access').' IN ('.$groups.')';
				
				if (JLanguageMultilang::isEnabled()) {
					$catwhere .= ' AND '.$db->qn('c.language').' IN ('.$db->q(JFactory::getLanguage()->getTag()).','.$db->q('*').')';
				}
				
				$where[] = $db->qn('e.id').$not.' IN (SELECT '.$db->qn('tx.ide').' FROM '.$db->qn('#__rseventspro_taxonomy','tx').' LEFT JOIN '.$db->qn('#__categories','c').' ON '.$db->qn('c.id').' = '.$db->qn('tx.id').' WHERE '.$db->qn($column).' '.$operator.' '.$db->q($value).' AND '.$db->qn('tx.type').' = '.$db->q('category').' AND '.$db->qn('c.extension').' = '.$db->q('com_rseventspro').$catwhere.')';
			} elseif ($extrat) {
				$where[] = $db->qn('e.id').$not.' IN (SELECT '.$db->qn('tx.ide').' FROM '.$db->qn('#__rseventspro_taxonomy','tx').' LEFT JOIN '.$db->qn('#__rseventspro_tags','t').' ON '.$db->qn('t.id').' = '.$db->qn('tx.id').' WHERE '.$db->qn($column).' '.$operator.' '.$db->q($value).' AND '.$db->qn('tx.type').' = '.$db->q('tag').')';
			} else {
				$where[] = '('.$db->qn($column).' '.$operator.' '.$db->q($value).')';
			}
		}
		
		// Add conditions from the extra Events filter
		if (!is_null($featured = $this->extra['featured'])) {
			if ($featured == 1) {
				$where[] = '('.$db->qn('e.featured').' = 1)';
			} elseif ($featured == 0) {
				$where[] = '('.$db->qn('e.featured').' = 0)';
			}
		}
		
		if (!is_null($price = $this->extra['price']) && $this->filterPrice) {
			list($min, $max) = explode(',',$price);
			$where[] = '('.$db->qn('tickets.price').' >= '.$db->q($min).' AND '.$db->qn('tickets.price').' <= '.$db->q($max).')';
		}
		
		return !empty($where) ? ' AND ('.implode(' '.$this->operator.' ',$where).')' : '';
	}
	
	/**
	 * Method to get events between two dates
	 *
	 * @return   string  Mysql query
	 *
	 */
	public function betweenQuery($start, $end, $calendar = false) {
		$db = JFactory::getDbo();
		
		if ($calendar) {
			// Get regular events that start before the 'start' date and end after the 'start' date 
			$c1 = '('.$db->qn('e.end').' <> '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' <= '.$db->q($start).' AND '.$db->qn('e.end').' >= '.$db->q($start).')';
			
			// Get regular events between 'start' and 'end' dates
			$c2 = '('.$db->qn('e.end').' <> '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' >= '.$db->q($start).' AND '.$db->qn('e.start').' <= '.$db->q($end).')';
			
			// Get all day events between 'start' and 'end' dates
			$c3 = '('.$db->qn('e.end').' = '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' >= '.$db->q($start).' AND '.$db->qn('e.start').' <= '.$db->q($end).')';
			
			return ' AND ( '.$c1.' OR  '.$c2.' OR  '.$c3.' )';
		}
		
		// Get regular events between 'start' and 'end' dates
		$q1 = '('.$db->qn('e.end').' <> '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' >= '.$db->q($start).' AND '.$db->qn('e.end').' <= '.$db->q($end).')';
		
		// Get regular events that starts between the 'start' and 'end' dates and ends after the 'end' date
		$q2 = '('.$db->qn('e.end').' <> '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' >= '.$db->q($start).' AND '.$db->qn('e.start').' <= '.$db->q($end).' AND '.$db->qn('e.end').' >= '.$db->q($end).')';
		
		// Get regular events that start before the 'start' date and ends between the 'start' and 'end' dates
		$q3 = '('.$db->qn('e.end').' <> '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' <= '.$db->q($start).' AND '.$db->qn('e.end').' >= '.$db->q($start).' AND '.$db->qn('e.end').' <= '.$db->q($end).')';
		
		// Get regular events that start before the 'start' date and ends after the 'end' date
		$q4 = '('.$db->qn('e.end').' <> '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' <= '.$db->q($start).' AND '.$db->qn('e.end').' >= '.$db->q($end).')';
		
		// Get all day events between 'start' and 'end' dates
		$q5 = '('.$db->qn('e.end').' = '.$db->q($this->getNullDate()).' AND '.$db->qn('e.start').' >= '.$db->q($start).' AND '.$db->qn('e.start').' <= '.$db->q($end).')';
		
		return ' AND ( '.$q1.' OR  '.$q2.' OR  '.$q3.' OR  '.$q4.' OR '.$q5.' )';
	}
	
	/**
	 * Method to get null date
	 *
	 * @return   string  Database null date
	 *
	 */
	protected function getNullDate() {
		return JFactory::getDbo()->getNullDate();
	}
	
	protected function implodeSql($items) {
		$db		= JFactory::getDbo();
		$select	= array();
		
		if ($items) {
			foreach ($items as $item => $as) {
				if (is_int($item)) {
					$select[] = strpos($as,'`') !== false ? $as : $db->qn($as);
				} else {
					$select[] = $db->qn($item,$as);
				}
			}
		}
		
		return !empty($select) ? implode(', ',$select) : $db->qn('e.id');
	}
}

// Keep this class for legacy
class RSEventsQuery extends RSEventsProQuery {
	
}