<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproModelCalendar extends JModelLegacy
{
	protected $_query		= null;
	protected $_db 			= null;
	protected $_app 		= null;
	
	/**
	 *	Main constructor
	 *
	 */
	public function __construct() {
		parent::__construct();
		
		$config				= JFactory::getConfig();
		$this->_db			= JFactory::getDBO();
		$this->_app			= JFactory::getApplication();
		$this->_filters		= $this->getFilters();
		$this->_query		= $this->_buildQuery();
		
		if ($this->_app->input->get('layout') == 'day' || $this->_app->input->get('layout') == 'week' || $this->_app->input->get('tpl') == 'day' || $this->_app->input->get('tpl') == 'week') {
			$thelimit	= $this->_app->input->get('format','') == 'feed' ? $config->get('feed_limit') : $config->get('list_limit');
			$limit		= $this->_app->getUserStateFromRequest('com_rseventspro.limit', 'limit', $thelimit, 'int');
			$limitstart	= $this->_app->input->getInt('limitstart', 0);
			
			// In case limit has been changed, adjust it
			$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

			$this->setState('com_rseventspro.limit', $limit);
			$this->setState('com_rseventspro.limitstart', $limitstart);
		}
	}
	
	/**
	 *	Method to build the events query
	 *
	 *	@return SQL query
	 */
	protected function _buildQuery() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/query.php';
		
		$mid		= $this->_app->input->getInt('mid',0);
		$params		= $mid ? $this->getModuleParams() : rseventsproHelper::getParams();
		$date		= $this->_app->input->getString('date');
		$layout 	= $this->_app->input->get('layout');
		$tpl		= $this->_app->input->get('tpl');
		$query		= RSEventsProQuery::getInstance($params);
		
		$query->select(array('e.id', 'e.name', 'e.start', 'e.end', 'e.allday'));
		
		if ($layout == '' || $layout == 'default') {
			list($start, $end) = $this->getStartEndCurrentMonth($params);
		} else if (($layout == 'day' || $tpl == 'day') && !empty($date)) {
			list($start, $end) = $this->getStartEndDay($date);
		} else if (($layout == 'week' || $tpl == 'week') && !empty($date)) {
			list($start, $end) = $this->getStartEndWeek($date);
		}
		
		$where = $query->betweenQuery($start, $end, true);
		$where = substr_replace($where,'',0,5);
		
		$query->where($where);
		$query->userevents(false);
		$query->group(array('e.id', 'e.name', 'e.start', 'e.end', 'e.allday'));
		
		return $query->toString();
	}
	
	/**
	 *	Method to get calendar events
	 */
	public function getEvents() {
		if ($this->_app->input->get('layout') == 'day' || $this->_app->input->get('layout') == 'week' || $this->_app->input->get('tpl') == 'day' || $this->_app->input->get('tpl') == 'week') {
			if ($this->_app->input->get('type','') == 'ical') {
				$this->_db->setQuery($this->_query);
				return $this->_db->loadObjectList();
			} else {
				$this->_db->setQuery($this->_query,$this->getState('com_rseventspro.limitstart'), $this->getState('com_rseventspro.limit'));
				return $this->_db->loadObjectList();
			}
		} else {
			$this->_db->setQuery($this->_query);
			return $this->_db->loadObjectList();
		}
	}
	
	/**
	 *	Method to get the total number of events
	 */
	public function getTotal() {
		$this->_db->setQuery($this->_query);
		$this->_db->execute();

		return $this->_db->getNumRows();
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
	
	public function getColors() {
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
				$categories = array_map('intval',$categories);
				$query->where($this->_db->qn('id').' IN ('.implode(',',$categories).')');	
			}
			
			$query->order($this->_db->qn($order).' '.$this->_db->escape($direction));
			
			$this->_db->setQuery($query);
			if ($data = $this->_db->loadObjectList()) {
				foreach ($data as $i => $category) {
					try {
						$registry = new JRegistry;
						$registry->loadString($category->params);
						$data[$i]->color = $colors ? $registry->get('color','') : '';
					} catch (Exception $e) {
						$data[$i]->color = '';
					}
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
	
	protected function getStartEndCurrentMonth($params) {
		$input		= JFactory::getApplication()->input;
		$now		= JFactory::getDate();
		$month		= $input->getInt('month',	0);
		$year		= $input->getInt('year',	0);
		$weekstart	= $params->get('startday',1);
		
		if (!$month) {
			$paramsMonth = (int) $params->get('startmonth',0);
			if ($paramsMonth == 0) {
				$month = (int) $now->format('n');
			} else {
				$month = $paramsMonth;
			}
		}
		
		if (!$year) {
			$paramsYear = (int) $params->get('startyear',0);
			if (empty($paramsYear)) {
				$year = (int) $now->format('Y');
			} else {
				$year = $paramsYear;
			}
		}
		
		if (strlen($month) == 1) {
			$month = '0'.$month;
		}
		
		$startMonth			= JFactory::getDate($year.'-'.$month.'-01 00:00:00');
		$month_start_day	= $startMonth->format('w');
		$weekdays			= $this->getWeekdays($weekstart);
		
		$prevDays = 0;
		if ($month_start_day != $weekstart) {
			foreach ($weekdays as $position) {
				if ($position == $month_start_day) {
					break;
				} else {
					$prevDays++;
				}
			}
		}
		
		if ($prevDays) {
			$startMonth->modify('-'.$prevDays.' days');
		}
		
		$endofmonth = JFactory::getDate($year.'-'.$month.'-01 00:00:00')->format($year.'-'.$month.'-t H:i:s');
		$endMonth	= JFactory::getDate($endofmonth, rseventsproHelper::getTimezone());
		$endMonthDT = new DateTime($endofmonth);
		$weekend	= $this->getWeekdays($weekstart,true);
		$day		= $endMonthDT->format('w');
		
		$k = 1;
		$nextDays = 0;
		if ($day != $weekend) {
			while($day != $weekend) {
				$nextmonth = $month + 1 > 12 ? ($month + 1) - 12 : $month + 1;
				$nextyear  = $month + 1 > 12 ? $year + 1 : $year;
				
				if (strlen($nextmonth) == 1) {
					$nextmonth = '0'.$nextmonth;
				}
				
				$cday = $k;
				if (strlen($cday) == 1) {
					$cday = '0'.$cday;
				}
				
				$day = JFactory::getDate($nextyear.'-'.$nextmonth.'-'.$cday.' 00:00:00')->format('w');
				
				$k++;
				$nextDays++;
			}
		}
		
		if ($weekstart == 0) {
			$nextDays++;
		}
		
		if ($nextDays) {
			$endMonth->modify('+'.$nextDays.' days');
		}
		
		$endMonth->modify('+86399 seconds');
		
		$startMonth = new DateTime($startMonth->toSql(), new DateTimezone(rseventsproHelper::getTimezone()));
		$startMonth->setTimezone(new DateTimezone('UTC'));
		
		return array($startMonth->format('Y-m-d H:i:s'), $endMonth->toSql());
	}
	
	protected function getStartEndDay($date) {
		$tzoffset		= rseventsproHelper::getTimezone();
		$date			= str_replace(array('-',':'),'/',$date);
		list($m,$d,$y)	= explode('/',$date,3);
		
		$start	= JFactory::getDate($y.'-'.$m.'-'.$d.' 00:00:00', $tzoffset);
		$end	= JFactory::getDate($y.'-'.$m.'-'.$d.' 23:59:59', $tzoffset);
		
		return array($start->toSql(), $end->toSql());
	}
	
	protected function getStartEndWeek($date) {
		$tzoffset		= rseventsproHelper::getTimezone();
		$date			= str_replace(array('-',':'),'/',$date);
		list($m,$d,$y)	= explode('/',$date,3);
		
		$start	= JFactory::getDate($y.'-'.$m.'-'.$d.' 00:00:00', $tzoffset);
		$end	= JFactory::getDate($y.'-'.$m.'-'.$d.' 23:59:59', $tzoffset);
		$end->modify('+6 days');
		
		return array($start->toSql(), $end->toSql());
	}
	
	protected function getWeekdays($i, $weekend = false) {
		if ($i == 0) {
			return $weekend ? 6 : array(0,1,2,3,4,5,6);
		} elseif ($i == 1) {
			return $weekend ? 0 : array(1,2,3,4,5,6,0);
		} else if ($i == 6) {
			return $weekend ? 5 : array(6,0,1,2,3,4,5);
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
		
		try {
			$registry = new JRegistry;
			$registry->loadString($string);
			return $registry;
		} catch (Exception $e) {
			return new JRegistry;
		}
	}
	
	// Get extra filters
	public function getExtraFilters() {
		$itemid 	= $this->_app->input->getInt('Itemid');
		
		$status		= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_status'.$itemid,		'filter_status',	array(), 'array');
		$featured	= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_featured'.$itemid,	'filter_featured',	array(), 'array');
		$childs		= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_child'.$itemid, 		'filter_child',		array(), 'array');
		$start		= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_start'.$itemid, 		'filter_start',		array(), 'array');
		$end		= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_end'.$itemid, 		'filter_end',		array(), 'array');
		$price		= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_price'.$itemid, 		'filter_price',		array(), 'array');
		
		$status		= isset($status[0])		? ($status[0] 	== '' ? null : $status) : null;
		$featured	= isset($featured[0])	? ($featured[0] == '' ? null : $featured[0]) : null;
		$childs		= isset($childs[0])		? ($childs[0] 	== '' ? null : $childs[0]) : null;
		$start		= isset($start[0])		? ($start[0] 	== '' ? null : $start[0]) : null;
		$end		= isset($end[0])		? ($end[0] 		== '' ? null : $end[0]) : null;
		$price		= isset($price[0])		? ($price[0] 	== '' ? null : $price[0]) : null;
		
		if (is_array($status)) {
			$status = array_unique($status);
			
			foreach ($status as $key => $option) {
				if ($option == '') unset($status[$key]);
			}	
		}
		
		return array('status' => $status, 'featured' => $featured, 'childs' => $childs, 'start' => $start, 'end' => $end, 'price' => $price);
	}
	
	public function getConditions() {
		$filters	= $this->getFilters();
		$other		= $this->getExtraFilters();
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
		
		if (!is_null($other['price'])) {
			$count++;
		}
		
		return $count;
	}
	
	public function getMaxPrice() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/query.php';
		
		$db			= JFactory::getDbo();
		$mid		= $this->_app->input->getInt('mid',0);
		$params		= $mid ? $this->getModuleParams() : rseventsproHelper::getParams();
		$query		= RSEventsProQuery::getInstance($params);
		
		$query->select(array('e.id'));
		
		list($start, $end) = $this->getStartEndCurrentMonth($params);
		$where = $query->betweenQuery($start, $end, true);
		$where = substr_replace($where,'',0,5);
		
		$query->where($where);
		$query->userevents(false);
		$query->price(false);
		
		$db->setQuery('SELECT MAX('.$db->qn('price').') FROM '.$db->qn('#__rseventspro_tickets').' WHERE '.$db->qn('ide').' IN ('.$query->toString().')');
		return round($db->loadResult());
	}
	
	public function getOperator() {
		$itemid 	= $this->_app->input->getInt('Itemid');
		$valid		= array('AND', 'OR');
		$operator	= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_operator'.$itemid, 'filter_operator', 'AND');
		
		return !in_array($operator, $valid) ? 'AND' : $operator;		
	}
	
	public function getFilterOptions() { 
		return array(JHTML::_('select.option', 'events', JText::_('COM_RSEVENTSPRO_FILTER_NAME')), JHTML::_('select.option', 'description', JText::_('COM_RSEVENTSPRO_FILTER_DESCRIPTION')), 
			JHTML::_('select.option', 'locations', JText::_('COM_RSEVENTSPRO_FILTER_LOCATION')) ,JHTML::_('select.option', 'categories', JText::_('COM_RSEVENTSPRO_FILTER_CATEGORY')),
			JHTML::_('select.option', 'tags', JText::_('COM_RSEVENTSPRO_FILTER_TAG')), JHTML::_('select.option', 'featured', JText::_('COM_RSEVENTSPRO_FILTER_FEATURED')), 
			JHTML::_('select.option', 'price', JText::_('COM_RSEVENTSPRO_FILTER_PRICE'))
		);
	}
	
	public function getFilterConditions() {
		return array(JHTML::_('select.option', 'is', JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_IS')), JHTML::_('select.option', 'isnot', JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_ISNOT')),
			JHTML::_('select.option', 'contains', JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_CONTAINS')),JHTML::_('select.option', 'notcontain', JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_NOTCONTAINS'))
		);
	}
}