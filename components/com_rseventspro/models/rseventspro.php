<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.model' );

class rseventsproModelRseventspro extends JModelLegacy
{	
	var $_query				= null;
	var $_locationquery		= null;
	var $_categoriesquery	= null;
	var $_subscrquery		= null;
	var $_searchquery		= null;
	var $_formsquery		= null;
	
	var $_total				= 0;
	var $_locationtotal		= 0;
	var $_categoriestotal	= 0;
	var $_subscrtotal		= 0;
	var $_searchtotal		= 0;
	var $_formstotal		= 0;
	
	var $_data				= null;
	var $_locationdata		= null;
	var $_categoriesdata	= null;
	var $_subscrdata		= null;
	var $_searchdata		= null;
	var $_formsdata			= null;
	
	var $_db				= null;
	var $_id				= 0;
	var $_app				= null;
	var $_user				= null;
	var $_name				= null;
	var $_join				= null;
	var $_where				= null;
	var $_exclude			= null;
	var $_pagination		= null;
	var $permissions		= null;
	
	/**
	 *	Main constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->_db			= JFactory::getDBO();
		$this->_app			= JFactory::getApplication();
		$this->_user		= JFactory::getUser();
		$this->permissions	= rseventsproHelper::permissions();
		$layout				= $this->_app->input->get('layout','');
		$task				= $this->_app->input->get('task');
		$config				= JFactory::getConfig();
		
		if (in_array($layout, array('','items','default','locations','categories'))) {
			if ($category = $this->_app->input->getInt('category',0)) {
				$this->setFilter('categories',$this->getNameType('category',$category));
			}
			
			if ($tag = $this->_app->input->getInt('tag',0)) {
				$this->setFilter('tags',$this->getNameType('tag', $tag));
			}
				
			if ($location = $this->_app->input->getInt('location',0)) {
				$this->setFilter('locations',$this->getNameType('location', $location));
			}
			
			$this->_filters		= $this->getFilters();
			$this->_where		= $this->_buildWhere();
			$this->_join		= $this->_buildJoin();
			$this->_exclude		= rseventsproHelper::excludeEvents();
			$this->_query		= $this->_buildQuery();
		}
		
		if ($layout == 'locations' || $layout == 'items') {
			$this->_locationquery = $this->_buildLocationQuery();
		}
		
		if ($layout == 'categories' || $layout == 'items') {
			$this->_categoriesquery = $this->_buildCategoriesQuery();
		}
		
		if ($layout == 'subscribers' || $layout == 'items' || $task == 'exportguests') {
			$this->_subscrquery = $this->_buildSubscribersQuery();
		}
		
		if ($layout == 'search' || $layout == 'items') {
			$this->_searchquery = $this->_buildSearchQuery();
		}
		
		if ($layout == 'forms') {
			$this->_formsquery = $this->getFormsQuery();
		}
		
		// Get pagination request variables
		$limit = $this->_app->getUserStateFromRequest('com_rseventspro.limit', 'limit', $config->get('list_limit'), 'int');
		$limitstart	= $this->_app->input->getInt('limitstart', 0);
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('com_rseventspro.limit', $limit);
		$this->setState('com_rseventspro.limitstart', $limitstart);
	}
	
	/**
	 *	Method to get All day events
	 *
	 *	@return array
	 */
	protected function _getAllDayEvents($type) {
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
		
		// Start search query params
		$startsearch = $this->_app->getUserStateFromRequest('rsepro.search.start', 'rsstart');
		$endsearch = $this->_app->getUserStateFromRequest('rsepro.search.end', 'rsend');
		
		if (strlen(trim($startsearch)) <= 10)
			$startsearch .= ' 00:00:00';
		
		$startsearch = rseventsproHelper::date($startsearch,null,false,true);
		$startsearch->setTZByID($startsearch->getTZID());
		$startsearch->convertTZ(new RSDate_Timezone('GMT'));
		$startsearch = $startsearch->formatLikeDate('Y-m-d H:i:s');
		
		if (strlen(trim($endsearch)) <= 10)
			$endsearch .= ' 00:00:00';
		
		$endsearch = rseventsproHelper::date($endsearch,null,false,true);
		$endsearch->setTZByID($endsearch->getTZID());
		$endsearch->convertTZ(new RSDate_Timezone('GMT'));
		$endsearch = $endsearch->formatLikeDate('Y-m-d H:i:s');
		
		
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
		} elseif ($type == 'searchfrom') {
			$query->where($this->_db->qn('start').' >= '.$this->_db->q($startsearch));
		} elseif ($type == 'searchto') {
			$query->where($this->_db->qn('start').' <= '.$this->_db->q($endsearch));
		} elseif ($type == 'searchfromto') {
			$query->where($this->_db->qn('start').' >= '.$this->_db->q($startsearch));
			$query->where($this->_db->qn('start').' <= '.$this->_db->q($endsearch));
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
		$params 	= rseventsproHelper::getParams();
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
		$counter	= (int) $params->get('repeatcounter',1);	
		$parent		= $this->_app->input->getInt('parent',0);
		$where		= array();
		
		// Start Legacy 
		$future		= (int) $params->get('future',0);
		$uevents	= (int) $params->get('userevents',0);
		
		if ($future) $list = 'future';
		if ($uevents) $list = 'user';
		// End Legacy
		
		// Start query
		$query = 'SELECT '.$this->_db->qn('e.id').' FROM '.$this->_db->qn('#__rseventspro_events','e').' ';
		
		// Join over location table
		if (!empty($this->_join)) 
			$query .= $this->_join;
		
		$query .= ' WHERE ';
		
		// Exclude unwanted events
		if (!empty($this->_exclude))
			$query .= $this->_db->qn('e.id').' NOT IN ('.implode(',',$this->_exclude).') AND ';
		else 
			$query .= '1 AND ';
		
		$query .= '( ';
		
		// Select only completed events
		$query .= $this->_db->qn('e.completed').' = 1';
		
		if ($parent && $counter) {
			$where[] = ' AND '.$this->_db->qn('e.parent').' = '.$this->_db->q($parent).' AND '.$this->_db->qn('e.published').' = 1 ';
		} else {
			// Show repeated events
			if (!$repeat) {
				$where[] = ' AND '.$this->_db->qn('e.parent').' = 0 ';
			}
			
			// Get the list type
			// Get all events
			if ($list == 'all') {
				$query .= $archived ? ' AND '.$this->_db->qn('e.published').' IN (1,2) ' : ' AND '.$this->_db->qn('e.published').' = 1 ';
			}
			// Get Featured events
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
				} else 
				// Select today events
				{
					$start = rseventsproHelper::date('now',null,false,true);
					$start->setTZByID($start->getTZID());
					$start->convertTZ(new RSDate_Timezone('GMT'));
					
					$end = rseventsproHelper::date('now',null,false,true);
					$end->setHourMinuteSecond(23,59,59);
					$end->setTZByID($end->getTZID());
					$end->convertTZ(new RSDate_Timezone('GMT'));
					$end->addSeconds(1);
					
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
			else {
				if ($this->_user->get('id') > 0)
					$where[] = ' AND '.$this->_db->qn('e.owner').' = '.(int) $this->_user->get('id').' ';
				
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
			
			// Select events in the specific interval
			if (empty($from) && !empty($to)) {
				$includeTo = $this->_getAllDayEvents('to');
				
				if (!empty($includeTo)) {
					$where[] = ' AND ( ('.$this->_db->qn('e.end').' <= '.$this->_db->q($to).' AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).') OR '.$this->_db->qn('e.id').' IN ('.implode(',',$includeTo).')) ';
				} else {
					$where[] = ' AND '.$this->_db->qn('e.end').' <= '.$this->_db->q($to).' AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).' ';
				}
				
			} elseif (!empty($from) && empty($to)) {
				$includeFrom = $this->_getAllDayEvents('from');
				
				if (!empty($includeFrom)) {
					$where[] = ' AND ( ('.$this->_db->qn('e.start').' >= '.$this->_db->q($from).' AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).') OR '.$this->_db->qn('e.id').' IN ('.implode(',',$includeFrom).')) ';
				} else {
					$where[] = ' AND '.$this->_db->qn('e.start').' >= '.$this->_db->q($from).' AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).' ';
				}
			} elseif (!empty($from) && !empty($to)) {
				$includeFromTo = $this->_getAllDayEvents('fromto');
				
				if (!empty($includeFromTo)) {
					$where[] = ' AND (((('.$this->_db->qn('e.start').' <= '.$this->_db->q($from).' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($to).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($from).' AND '.$this->_db->qn('e.end').' <= '.$this->_db->q($to).')) AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).' ) OR '.$this->_db->qn('e.id').' IN ('.implode(',',$includeFromTo).')) ';
				} else {
					$where[] = ' AND ((('.$this->_db->qn('e.start').' <= '.$this->_db->q($from).' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($to).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($from).' AND '.$this->_db->qn('e.end').' <= '.$this->_db->q($to).')) AND '.$this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()).') ';
				}
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
		}
		
		if (!empty($where)) {
			$query .= implode('',$where);
		}
		
		if (!empty($this->_where)) {
			$query .= $this->_where;
		}
		
		$query .= ") ";
		
		$filters = empty($this->_filters[0]) ? 0 : 1;
		
		// Select users unpublished / incompleted events
		if ($this->_app->input->getInt('location',0) == 0 && $this->_app->input->getString('tag') == '' && $this->_app->input->getString('category') == '' && empty($filters)) {
			$subquery = ' OR ('.$this->_db->qn('e.id').' IN (SELECT '.$this->_db->qn('e.id').' FROM '.$this->_db->qn('#__rseventspro_events','e').' ';
			
			if (!empty($this->_join)) 
				$subquery .= $this->_join;
			
			$subquery .= " WHERE 1 ";
			
			if (!empty($where))
				$subquery .= implode('',$where);
			
			if (!empty($this->_where))
				$subquery .= $this->_where;
			
			if ($this->_user->get('id') > 0) {
				$subquery .= ' AND '.$this->_db->qn('e.owner').' = '.(int) $this->_user->get('id').' AND '.$this->_db->qn('e.published').' IN (0,1) AND '.$this->_db->qn('e.completed').' IN (0,1))) ';
			} else {
				$sid = JFactory::getSession()->getId();
				$subquery .= ' AND '.$this->_db->qn('e.sid').' = '.$this->_db->q($sid).' AND '.$this->_db->qn('e.published').' IN (0,1) AND '.$this->_db->qn('e.completed').' IN (0,1))) ';
			}
			
			$query .= $subquery;
		}
		
		if ($order == 'title')
			$order = 'name';
		
		if ($order == 'lft')
			$order = 'start';
		
		$featured_condition = rseventsproHelper::getConfig('featured','int') ? $this->_db->qn('e.featured').' DESC, ' : '';
		$query .= ' ORDER BY '.$featured_condition.' '.$this->_db->qn('e.'.$order).' '.$this->_db->escape($direction).' ';
		
		return $query;
	}
	
	/**
	 *	Method to build the locations query
	 *
	 *	@return SQL query
	 */
	protected function _buildLocationQuery() {
		$query	= $this->_db->getQuery(true);
		$params	= rseventsproHelper::getParams();
		$order	= $params->get('order','ASC');
		
		$query->clear()
			->select($this->_db->qn('l.id'))->select($this->_db->qn('l.name'))->select($this->_db->qn('l.description'))
			->from($this->_db->qn('#__rseventspro_locations','l'))
			->where($this->_db->qn('l.published').' = 1')
			->order($this->_db->qn('l.name').' '.$this->_db->escape($order));
		
		if ($params->get('empty',0)) {
			$query->join('right',$this->_db->qn('#__rseventspro_events','e').' ON '.$this->_db->qn('e.location').' = '.$this->_db->qn('l.id'));
			$query->group($this->_db->qn('l.id'));
		}
		
		return $query;
	}
	
	/**
	 *	Method to build the locations query
	 *
	 *	@return SQL query
	 */
	protected function _buildCategoriesQuery() {
		$query	= $this->_db->getQuery(true);
		$params	= rseventsproHelper::getParams();
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		
		$ordering	= $params->get('ordering','title');
		$direction	= $params->get('order','ASC');
		
		$query->clear()
			->select($this->_db->qn('id'))->select($this->_db->qn('title'))
			->select($this->_db->qn('description'))->select($this->_db->qn('level'))
			->from($this->_db->qn('#__categories'))
			->where($this->_db->qn('extension').' = '.$this->_db->q('com_rseventspro'))
			->where($this->_db->qn('published').' = 1')
			->order($this->_db->qn($ordering).' '.$this->_db->escape($direction));
		
		if (JLanguageMultilang::isEnabled()) {
			$query->where('language IN ('.$this->_db->q(JFactory::getLanguage()->getTag()).','.$this->_db->q('*').')');
		}
		
		$query->where('access IN ('.$groups.')');
		
		return $query;
	}
	
	/**
	 *	Method to build the subscribers query
	 *
	 *	@return SQL query
	 */
	protected function _buildSubscribersQuery() {
		$query	= $this->_db->getQuery(true);
		$id		= $this->_app->input->getInt('id');
		$ticket = $this->_app->input->get('ticket',$this->_app->getUserState('com_rseventspro.subscriptions.ticket.frontend'));
		$search = $this->_app->input->getString('search',$this->_app->getUserState('com_rseventspro.subscriptions.search_frontend'));
		$state	= $this->_app->input->getString('state',$this->_app->getUserState('com_rseventspro.subscriptions.state.frontend'));
		
		$this->_app->setUserState('com_rseventspro.subscriptions.search_frontend',$search);
		$this->_app->setUserState('com_rseventspro.subscriptions.state.frontend',$state);
		$this->_app->setUserState('com_rseventspro.subscriptions.ticket.frontend',$ticket);
		
		$query->clear()
			->select($this->_db->qn('e.name','event'))->select($this->_db->qn('u.id'))->select($this->_db->qn('u.ide'))
			->select($this->_db->qn('u.idu'))->select($this->_db->qn('u.name'))->select($this->_db->qn('u.email'))
			->select($this->_db->qn('u.date'))->select($this->_db->qn('u.state'))->select($this->_db->qn('u.ip'))
			->select($this->_db->qn('u.gateway'))->select($this->_db->qn('u.SubmissionId'))->select($this->_db->qn('u.discount'))
			->select($this->_db->qn('u.early_fee'))->select($this->_db->qn('u.late_fee'))->select($this->_db->qn('u.tax'))
			->from($this->_db->qn('#__rseventspro_users','u'))
			->join('left',$this->_db->qn('#__rseventspro_events','e').' ON '.$this->_db->qn('e.id').' = '.$this->_db->qn('u.ide'))
			->where($this->_db->qn('u.ide').' = '.$id);
		
		if ($ticket != '-' && !empty($ticket))
			$query->join('left',$this->_db->qn('#__rseventspro_user_tickets','ut').' ON '.$this->_db->qn('ut.ids').' = '.$this->_db->qn('u.id'));

		if (!empty($search)) {
			$search = $this->_db->Quote('%'.$this->_db->escape($search, true).'%');
			$query->where('('.$this->_db->qn('e.name').' LIKE '.$search.' OR '.$this->_db->qn('u.name').' LIKE '.$search.' OR '.$this->_db->qn('u.email').' LIKE '.$search.')');
		}
		
		if ($state != '-' && !is_null($state))
			$query->where($this->_db->qn('u.state').' = '.(int) $state);
		
		if ($ticket != '-' && !empty($ticket))
			$query->where($this->_db->qn('ut.idt').' = '.(int) $ticket);
		
		$query->order($this->_db->qn('u.date').' DESC');
		return $query;
	}
	
	/**
	 *	Method to build the search query
	 *
	 *	@return SQL query
	 */
	protected function _buildSearchQuery() {
		$query			= $this->_db->getQuery(true);
		$params			= rseventsproHelper::getParams();
		$enablestart	= $this->_app->input->getInt('enablestart');
		$enableend		= $this->_app->input->getInt('enableend');
		$order			= $params->get('ordering','start');
		$direction		= $params->get('order','ASC');
		
		if ($this->_app->input->get('format') != 'raw') {
			$this->_app->setUserState('rsepro.search.estart',$enablestart);
			$this->_app->setUserState('rsepro.search.eend',$enableend);
		}
		
		$categories	= $this->_app->getUserStateFromRequest('rsepro.search.categories', 'rscategories');
		$locations	= $this->_app->getUserStateFromRequest('rsepro.search.locations', 'rslocations');
		$estart		= $this->_app->getUserStateFromRequest('rsepro.search.estart', 'enablestart');
		$eend		= $this->_app->getUserStateFromRequest('rsepro.search.eend', 'enableend');
		$start		= $this->_app->getUserStateFromRequest('rsepro.search.start', 'rsstart');
		$end		= $this->_app->getUserStateFromRequest('rsepro.search.end', 'rsend');
		$archive	= $this->_app->getUserStateFromRequest('rsepro.search.archive', 'rsarchive');
		$search		= $this->_app->getUserStateFromRequest('rsepro.search.search', 'rskeyword');
		$exclude	= rseventsproHelper::excludeEvents();
		$where		= array();
		
		$query->clear()
			->select($this->_db->qn('e.id'))
			->from($this->_db->qn('#__rseventspro_events','e'))
			->join('left',$this->_db->qn('#__rseventspro_locations','l').' ON '.$this->_db->qn('l.id').' = '.$this->_db->qn('e.location'))
			->join('left',$this->_db->qn('#__rseventspro_taxonomy','tx').' ON '.$this->_db->qn('tx.ide').' = '.$this->_db->qn('e.id'))
			->join('left',$this->_db->qn('#__categories','c').' ON '.$this->_db->qn('c.id').' = '.$this->_db->qn('tx.id'))
			->where($this->_db->qn('e.completed').' = 1')
			->where($this->_db->qn('c.extension').' = '.$this->_db->q('com_rseventspro'))
			->group($this->_db->qn('e.id'));
		
		if ($archive) {
			$query->where($this->_db->qn('e.published').' IN (1,2)');
		} else {
			$query->where($this->_db->qn('e.published').' = 1');
		}
		
		if (!empty($categories)) {
			JArrayHelper::toInteger($categories);
			$addcategorywhere = true;
			
			if (count($categories) == 1 && $categories[0] == 0) {
				$addcategorywhere = false;
			}
			
			if ($addcategorywhere) {
				$subquery = $this->_db->getQuery(true);
				$subquery->clear()
					->select($this->_db->qn('tx.ide'))
					->from($this->_db->qn('#__rseventspro_taxonomy','tx'))
					->join('left',$this->_db->qn('#__categories','c').' ON '.$this->_db->qn('c.id').' = '.$this->_db->qn('tx.id'))
					->where($this->_db->qn('c.id').' IN ('.implode(',',$categories).')')
					->where($this->_db->qn('tx.type').' = '.$this->_db->q('category'))
					->where($this->_db->qn('c.extension').' = '.$this->_db->q('com_rseventspro'));
				
				if (JLanguageMultilang::isEnabled()) {
					$subquery->where('c.language IN ('.$this->_db->q(JFactory::getLanguage()->getTag()).','.$this->_db->q('*').')');
				}
				
				$user	= JFactory::getUser();
				$groups	= implode(',', $user->getAuthorisedViewLevels());
				$subquery->where('c.access IN ('.$groups.')');
				
				$query->where($this->_db->qn('e.id').' IN ('.$subquery.')');
			}
		}
		
		if (!empty($locations)) {
			JArrayHelper::toInteger($locations);
			$addlocationwhere = true;
			
			if (count($locations) == 1 && $locations[0] == 0)
				$addlocationwhere = false;
			
			if ($addlocationwhere)
				$query->where($this->_db->qn('e.location').' IN ('.implode(',',$locations).')');
		}
		
		$isstart	= false;
		$isend		= false;
		
		if ($estart && !empty($start)) {
			if (strlen(trim($start)) <= 10)
				$start .= ' 00:00:00';
			
			$start = rseventsproHelper::date($start,null,false,true);
			$start->setTZByID($start->getTZID());
			$start->convertTZ(new RSDate_Timezone('GMT'));
			$start = $start->formatLikeDate('Y-m-d H:i:s');
			
			$isstart = true;
		}
		
		if ($eend && !empty($end)) {
			if (strlen(trim($end)) <= 10)
				$end .= ' 00:00:00';
			
			$end = rseventsproHelper::date($end,null,false,true);
			$end->setTZByID($end->getTZID());
			$end->convertTZ(new RSDate_Timezone('GMT'));
			$end = $end->formatLikeDate('Y-m-d H:i:s');
			
			$isend = true;
		}
		
		if ($isstart && !$isend) {
			$includeFrom = $this->_getAllDayEvents('searchfrom');
			$query->where($this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			
			if (!empty($includeFrom)) {
				$query->where($this->_db->qn('e.start').' >= '.$this->_db->q($start).' OR '.$this->_db->qn('e.id').' IN ('.implode(',',$includeFrom).')');
			} else {
				$query->where($this->_db->qn('e.start').' >= '.$this->_db->q($start));
			}
		} else if (!$isstart && $isend) {
			$includeTo = $this->_getAllDayEvents('searchto');
			$query->where($this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			
			if (!empty($includeTo)) {
				$query->where($this->_db->qn('e.end').' <= '.$this->_db->q($end).' OR '.$this->_db->qn('e.id').' IN ('.implode(',',$includeTo).')');
			} else {
				$query->where($this->_db->qn('e.end').' <= '.$this->_db->q($end));
			}
		} else if ($isstart && $isend) {
			$includeFromTo = $this->_getAllDayEvents('searchfromto');
			$query->where($this->_db->qn('e.end').' <> '.$this->_db->q($this->_db->getNullDate()));
			
			if (!empty($includeFromTo)) {
				$query->where('(('.$this->_db->qn('e.start').' <= '.$this->_db->q($start).' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($start).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($start).' AND '.$this->_db->qn('e.start').' <= '.$this->_db->q($end).'))'.' OR '.$this->_db->qn('e.id').' IN ('.implode(',',$includeFromTo).')');
			} else {
				$query->where('(('.$this->_db->qn('e.start').' <= '.$this->_db->q($start).' AND '.$this->_db->qn('e.end').' >= '.$this->_db->q($start).') OR ('.$this->_db->qn('e.start').' >= '.$this->_db->q($start).' AND '.$this->_db->qn('e.start').' <= '.$this->_db->q($end).'))');
			}
		}
		
		if (!empty($search)) {
			$where	= '';
			$words	= explode(' ', $search);
			$search = $this->_db->quote('%' . $this->_db->escape($search, true) . '%', false);
			$wheres = array();
			
			$wheres1 = array();
			$wheres1[] = $this->_db->qn('e.name').' LIKE ' . $search;
			$wheres1[] = $this->_db->qn('e.description').' LIKE ' . $search;
			$wheres1[] = $this->_db->qn('l.name').' LIKE ' . $search;
			$wheres1[] = $this->_db->qn('l.description').' LIKE ' . $search;
			$wheres1[] = $this->_db->qn('l.address').' LIKE ' . $search;
			$wheres1[] = $this->_db->qn('c.title').' LIKE ' . $search;
			$wheres1[] = $this->_db->qn('c.description').' LIKE ' . $search;
			$wheres[] = implode(' OR ', $wheres1);
			
			if (count($words) > 1) {
				foreach ($words as $word) {
					$word = $this->_db->quote('%' . $this->_db->escape($word, true) . '%', false);
					$wheres2 = array();
					$wheres2[] = $this->_db->qn('e.name').' LIKE ' . $word;
					$wheres2[] = $this->_db->qn('e.description').' LIKE ' . $word;
					$wheres2[] = $this->_db->qn('l.name').' LIKE ' . $word;
					$wheres2[] = $this->_db->qn('l.description').' LIKE ' . $word;
					$wheres2[] = $this->_db->qn('l.address').' LIKE ' . $word;
					$wheres2[] = $this->_db->qn('c.title').' LIKE ' . $word;
					$wheres2[] = $this->_db->qn('c.description').' LIKE ' . $word;
					$wheres[] = implode(' OR ', $wheres2);
				}
			}
			
			$where = '(' . implode(') OR (', $wheres) . ')';
			$query->where('('.$where.')');
		}

		if (!empty($exclude))
			$query->where($this->_db->qn('e.id').' NOT IN ('.implode(',',$exclude).')');
		
		if (rseventsproHelper::getConfig('featured','int'))
			$query->order($this->_db->qn('e.featured').' DESC, '.$this->_db->qn($order).' '.$this->_db->escape($direction));
		else
			$query->order($this->_db->qn($order).' '.$this->_db->escape($direction));
		
		return $query;
	}
	
	/**
	 *	Method to build the RSForm! Pro forms query
	 *
	 *	@return SQL query
	 */
	protected function getFormsQuery() {
		$query	= $this->_db->getQuery(true);
		
		$query->clear()
			->select('DISTINCT '.$this->_db->qn('f.FormId'))->select($this->_db->qn('f.FormName'))
			->from($this->_db->qn('#__rsform_forms','f'))
			->join('left',$this->_db->qn('#__rsform_components','c').' ON '.$this->_db->qn('c.FormId').' = '.$this->_db->qn('f.FormId'))
			->where($this->_db->qn('f.Published').' = 1')
			->where($this->_db->qn('c.Published').' = 1')
			->where($this->_db->qn('c.ComponentTypeId').' IN (30,31)')
			->order($this->_db->qn('f.FormId').' ASC');
		
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
					if ($eventids = $this->_db->loadColumn()) {
						JArrayHelper::toInteger($eventids);
						$where[] = 'AND '.$this->_db->qn('e.id').' IN ('.implode(',',$eventids).')';
					}
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
	 *	Method to get events
	 */
	public function getEvents() {
		if (empty($this->_data)) {
			$this->_db->setQuery($this->_query, $this->getState('com_rseventspro.limitstart'), $this->getState('com_rseventspro.limit'));
			$this->_data = $this->_db->loadObjectList();
		}
		return $this->_data;
	}
	
	/**
	 *	Method to get locations
	 */
	public function getLocations() {
		if (empty($this->_locationdata)) {
			$this->_db->setQuery($this->_locationquery, $this->getState('com_rseventspro.limitstart'), $this->getState('com_rseventspro.limit'));
			$this->_locationdata = $this->_db->loadObjectList();
		}
		return $this->_locationdata;
	}
	
	/**
	 *	Method to get categories
	 */
	public function getCategories() {
		if (empty($this->_categoriesdata)) {
			$this->_db->setQuery($this->_categoriesquery,$this->getState('com_rseventspro.limitstart'), $this->getState('com_rseventspro.limit'));
			$this->_categoriesdata = $this->_db->loadObjectList();
		}
		return $this->_categoriesdata;
	}
	
	/**
	 *	Method to get subscribers
	 */
	public function getSubscribers() {
		if (empty($this->_subscrdata)) {
			$this->_db->setQuery($this->_subscrquery,$this->getState('com_rseventspro.limitstart'), $this->getState('com_rseventspro.limit'));
			$this->_subscrdata = $this->_db->loadObjectList();
		}
		return $this->_subscrdata;
	}
	
	/**
	 *	Method to get search results
	 */
	public function getResults() {
		if (empty($this->_searchdata)) {
			$this->_db->setQuery($this->_searchquery,$this->getState('com_rseventspro.limitstart'), $this->getState('com_rseventspro.limit'));
			$this->_searchdata = $this->_db->loadObjectList();
		}
		return $this->_searchdata;
	}
	
	/**
	 *	Method to get RSForm! Pro forms
	 */
	public function getForms() {
		if (!file_exists(JPATH_SITE.'/components/com_rsform/rsform.php'))
			return array();
		
		if (empty($this->_formsdata)) {
			$this->_db->setQuery($this->_formsquery,$this->getState('com_rseventspro.limitstart'),$this->getState('com_rseventspro.limit'));
			$this->_formsdata = $this->_db->loadObjectList();
		}
		return $this->_formsdata;
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
		if (empty($this->_total)) {
			$this->_total = $this->getCount($this->_query);
		}
		return $this->_total;
	}
	
	/**
	 *	Method to get the total number of locations
	 */
	public function getTotalLocations() {
		if (empty($this->_locationtotal))
			$this->_locationtotal = $this->getCount($this->_locationquery);
		return $this->_locationtotal;
	}
	
	/**
	 *	Method to get the total number of categories
	 */
	public function getTotalCategories() {
		if (empty($this->_categoriestotal))
			$this->_categoriestotal = $this->getCount($this->_categoriesquery);
		return $this->_categoriestotal;
	}
	
	/**
	 *	Method to get the total number of categories
	 */
	public function getTotalSubscribers() {
		if (empty($this->_subscrtotal))
			$this->_subscrtotal = $this->getCount($this->_subscrquery);
		return $this->_subscrtotal;
	}
	
	/**
	 *	Method to get the total number of search results
	 */
	public function getTotalResults() {
		if (empty($this->_searchtotal))
			$this->_searchtotal = $this->getCount($this->_searchquery);
		return $this->_searchtotal;
	}
	
	/**
	 *	Method to get the total number of forms
	 */
	public function getFormsTotal() {
		if (!file_exists(JPATH_SITE.'/components/com_rsform/rsform.php')) 
			return 1;
		
		if (empty($this->_formstotal))
			$this->_formstotal = $this->getCount($this->_formsquery); 
		
		return $this->_formstotal;
	}
	
	/**
	 *	Method to get pagination
	 */
	public function getPagination() {
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('com_rseventspro.limitstart'), $this->getState('com_rseventspro.limit'));
		}
		return $this->_pagination;
	}
	
	/**
	 *	Method to get forms pagination
	 */
	public function getFormsPagination() {
		if (empty($this->_formspagination)) {
			jimport('joomla.html.pagination');
			$this->_formspagination = new JPagination($this->getFormsTotal(), $this->getState('com_rseventspro.limitstart'), $this->getState('com_rseventspro.limit'));
		}
		return $this->_formspagination;
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
	
	public function getUser() {
		if ($this->_user->get('id') > 0) {
			return $this->_user->get('id');
		} else {
			return JFactory::getSession()->getId();
		}
	}
	
	// Get current subscriber details
	public function getSubscriber() {
		$id		= $this->_app->input->getInt('id',0);
		$query	= $this->_db->getQuery(true);
		
		// Get subscriber details
		$query->clear()
			->select('*')
			->from($this->_db->qn('#__rseventspro_users'))
			->where($this->_db->qn('id').' = '.$id);
		
		$this->_db->setQuery($query);
		$subscription = $this->_db->loadObject();
		
		// Get user tickets
		$query->clear()
			->select($this->_db->qn('ut.quantity'))->select($this->_db->qn('t.name'))->select($this->_db->qn('t.price'))
			->from($this->_db->qn('#__rseventspro_user_tickets','ut'))
			->join('left',$this->_db->qn('#__rseventspro_tickets','t').' ON '.$this->_db->qn('t.id').' = '.$this->_db->qn('ut.idt'))
			->where($this->_db->qn('ut.ids').' = '.$id);
		
		$this->_db->setQuery($query);
		$tickets = $this->_db->loadObjectList();
		
		// Get event details
		$query->clear()
			->select($this->_db->qn('id'))->select($this->_db->qn('name'))->select($this->_db->qn('owner'))
			->select($this->_db->qn('ticketsconfig'))->select($this->_db->qn('ticket_pdf'))->select($this->_db->qn('ticket_pdf_layout'))
			->from($this->_db->qn('#__rseventspro_events'))
			->where($this->_db->qn('id').' = '.(int) $subscription->ide);
		
		$this->_db->setQuery($query);
		$event = $this->_db->loadObject();
		
		return array('data' => $subscription, 'tickets' => $tickets, 'event' => $event);
	}
	
	// Get payment info
	public function getPayment() {
		$id		= $this->_app->input->getInt('pid',0);
		$query	= $this->_db->getQuery(true);
		
		$query->clear()
			->select($this->_db->qn('name'))->select($this->_db->qn('details'))->select($this->_db->qn('redirect'))
			->from($this->_db->qn('#__rseventspro_payments'))
			->where($this->_db->qn('id').' = '.$id);
		
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}
	
	// Check if the user is subscribed to this event
	public function getIsSubscribed() {
		$id = $this->_app->input->getInt('id');
		$query	= $this->_db->getQuery(true);
		
		$query->clear()
			->select('COUNT('.$this->_db->qn('id').')')
			->from($this->_db->qn('#__rseventspro_users'))
			->where($this->_db->qn('ide').' = '.$id)
			->where($this->_db->qn('idu').' = '.$this->_user->get('id'));
		
		$this->_db->setQuery($query);
		$issubscribed = $this->_db->loadResult();
		
		if ($this->_user->get('id') > 0 && !empty($this->permissions['can_unsubscribe'])) {
			return $issubscribed;
		} else return 0;
	}
	
	// Get user subscriptions
	public function getUserSubscriptions() {
		$id = $this->_app->input->getInt('id');
		$query	= $this->_db->getQuery(true);
		
		$query->clear()
			->select($this->_db->qn('id'))->select($this->_db->qn('name'))
			->select($this->_db->qn('date'))->select($this->_db->qn('state'))
			->from($this->_db->qn('#__rseventspro_users'))
			->where($this->_db->qn('ide').' = '.$id)
			->where($this->_db->qn('idu').' = '.$this->_user->get('id'));
		
		$this->_db->setQuery($query);
		$subscriptions = $this->_db->loadObjectList();
		
		if ($this->_user->get('id') > 0 && !empty($this->permissions['can_unsubscribe']))
			return $subscriptions;
		
		return false;
	}
	
	// Get user subscriptions
	public function getSubscriptions() {
		$query	= $this->_db->getQuery(true);
		
		$query->clear()
			->select($this->_db->qn('u.state'))->select($this->_db->qn('u.URL'))->select($this->_db->qn('u.date','subscribe_date'))->select($this->_db->qn('u.id','ids'))
			->select($this->_db->qn('u.name','iname'))->select($this->_db->qn('e.id'))->select($this->_db->qn('e.name'))
			->select($this->_db->qn('e.ticket_pdf'))->select($this->_db->qn('e.ticket_pdf_layout')) 
			->from($this->_db->qn('#__rseventspro_users','u'))
			->join('left',$this->_db->qn('#__rseventspro_events','e').' ON '.$this->_db->qn('e.id').' = '.$this->_db->qn('u.ide'))
			->where($this->_db->qn('u.idu').' = '.(int) $this->_user->get('id'));
		
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	// Get global statuses
	public function getStatuses() {
		return array(JHTML::_('select.option', 0, JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_INCOMPLETE')), 
			JHTML::_('select.option', 1, JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_COMPLETED')), 
			JHTML::_('select.option', 2, JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_DENIED'))
		);
	}
	
	// Get a list of tickets that belong to a specific event
	public function getTicketsFromEvent() {
		$id		= $this->_app->input->getInt('id');
		$query	= $this->_db->getQuery(true);
		$return = array();
		$return[] = JHTML::_('select.option', '-', '-= '.JText::_('COM_RSEVENTSPRO_GLOBAL_SELECT_TICKET').' =-');
		
		if (!empty($id)) {
			$query->clear()
				->select($this->_db->qn('id'))->select($this->_db->qn('name'))->select($this->_db->qn('price'))
				->from($this->_db->qn('#__rseventspro_tickets'))
				->where($this->_db->qn('ide').' = '.$id);
			
			$this->_db->setQuery($query);
			$tickets = $this->_db->loadObjectList();
			
			if (!empty($tickets)) {
				foreach ($tickets as $ticket) {
					if ($ticket->price > 0) {
						$return[] = JHTML::_('select.option', $ticket->id, $ticket->name . ' (' . rseventsproHelper::currency($ticket->price).')');
					} else {
						$return[] = JHTML::_('select.option', $ticket->id, $ticket->name . ' (' .JText::_('COM_RSEVENTSPRO_GLOBAL_FREE').')');
					}
				}
			}
		}
		
		return $return;
	}
	
	// Check if the current user can subscribe
	public function getCanSubscribe() {
		$id = $this->_app->input->getInt('id');
		$query	= $this->_db->getQuery(true);
		
		// Get the event details
		$query->clear()
			->select($this->_db->qn('end'))->select($this->_db->qn('registration'))->select($this->_db->qn('start_registration'))
			->select($this->_db->qn('end_registration'))->select($this->_db->qn('max_tickets'))->select($this->_db->qn('max_tickets_amount'))
			->select($this->_db->qn('form'))->select($this->_db->qn('allday'))->select($this->_db->qn('start'))
			->from($this->_db->qn('#__rseventspro_events'))
			->where($this->_db->qn('id').' = '.$id);
		
		$this->_db->setQuery($query);
		$event = $this->_db->loadObject();
		
		// Get the total number of tickets
		$query->clear()
			->select('COUNT('.$this->_db->qn('id').')')
			->from($this->_db->qn('#__rseventspro_tickets'))
			->where($this->_db->qn('ide').' = '.$id);
		
		$this->_db->setQuery($query);
		$tickets = $this->_db->loadResult();
		
		// If we are using RSForm!Pro and we have multiple registration off we return true;
		if ($event->form != 0 && $this->_app->input->get('layout') == 'subscribe' && !rseventsproHelper::getConfig('multi_registration','int'))
			return array('status' => true);
		
		// If the event does't have registration
		if (empty($event->registration)) 
			return array('status' => false, 'err' => JText::_('COM_RSEVENTSPRO_REGISTRATION_ERROR1'));
		
		$nowunix = JFactory::getDate()->toUnix();
		$endunix = JFactory::getDate($event->end)->toUnix();
		
		// If the event has ended
		if ($event->allday) {
			$date = rseventsproHelper::date($event->start,null,false,true);
			$date->addSeconds(86400);
			$date->setTZByID($date->getTZID());
			$date->convertTZ(new RSDate_Timezone('GMT'));
			$endunix = $date->getDate(RSDATE_FORMAT_UNIXTIME);
			
			if ($nowunix > $endunix) 
				return array('status' => false, 'err' => JText::_('COM_RSEVENTSPRO_REGISTRATION_ERROR2'));
			
		} else {
			if ($nowunix > $endunix) 
				return array('status' => false, 'err' => JText::_('COM_RSEVENTSPRO_REGISTRATION_ERROR2'));
		}
		
		// There are no tickets
		$eventtickets = $this->getEventTickets();		
		if (!empty($tickets) && empty($eventtickets)) {
			if ($this->_isThankYou($event->form)) {
				return array('status' => true);
			} else {
				return array('status' => false, 'err' => JText::_('COM_RSEVENTSPRO_REGISTRATION_ERROR6'));
			}
		}
		
		if ($event->max_tickets && $event->max_tickets_amount > 0) {
			$query->clear()
				->select('COUNT('.$this->_db->qn('id').')')
				->from($this->_db->qn('#__rseventspro_users'))
				->where($this->_db->qn('ide').' = '.$id)
				->where($this->_db->qn('state').' IN (0,1)');
			
			$this->_db->setQuery($query);
			$all_tickets_purchased = $this->_db->loadResult();
			
			if ($all_tickets_purchased >= (int) $event->max_tickets_amount)
				return array('status' => false, 'err' => JText::_('COM_RSEVENTSPRO_REGISTRATION_ERROR6'));
		}
		
		// Check the registration time
		$show = true;
		if ($event->start_registration == $this->_db->getNullDate()) $event->start_registration = '';
		if ($event->end_registration == $this->_db->getNullDate()) $event->end_registration = '';
		
		if (empty($event->start_registration))
			$start_registration = false;
		else
			$start_registration = JFactory::getDate($event->start_registration)->toUnix();
		
		if (empty($event->end_registration))
			$end_registration = false;
		else
			$end_registration = JFactory::getDate($event->end_registration)->toUnix();
		
		if (!empty($start_registration) && !empty($end_registration))
		{
			if ($start_registration <= $nowunix && $end_registration >= $nowunix || $start_registration >= $nowunix && $end_registration <= $nowunix)
				$show = true;
			else $show = false;
			
		} elseif (empty($start_registration) && !empty($end_registration))
		{
			if ($end_registration >= $nowunix)
				$show = true;
			else $show = false;
			
		} elseif (!empty($start_registration) && empty($end_registration))
		{
			if ($start_registration <= $nowunix)
				$show = true;
			else $show = false;
			
		} elseif (empty($start_registration) && empty($end_registration))
		{
			$show = true;
		}
		
		if (!$show) 	
			return array('status' => false, 'err' => JText::_('COM_RSEVENTSPRO_REGISTRATION_ERROR3'));
		
		// Check for permission
		if (empty($this->permissions['can_register']) && !rseventsproHelper::admin()) 
			return array('status' => false, 'err' => JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		
		// If the Multiple registration option is off we check to see if the user already registered
		if (!rseventsproHelper::getConfig('multi_registration','int')) {
			if ($this->_user->get('id') > 0) {
				$query->clear()
					->select($this->_db->qn('id'))
					->from($this->_db->qn('#__rseventspro_users'))
					->where($this->_db->qn('ide').' = '.$id)
					->where($this->_db->qn('idu').' = '.(int) $this->_user->get('id'));
				
				
				$this->_db->setQuery($query);
				if ($this->_db->loadResult())
					return array('status' => false, 'err' => JText::_('COM_RSEVENTSPRO_REGISTRATION_ERROR5'));
			}
		}
		
		return array('status' => true);
	}
	
	// Check for thankyou message
	protected function _isThankYou($form) {
		$thankyou	= false;
		$formparams = JFactory::getSession()->get('com_rsform.formparams.'.$form);		
		
		if (isset($formparams->formProcessed)) 
			$thankyou = true;
		
		return $thankyou;
	}
	
	// Get event tickets
	public function getTickets() {
		$id			= $this->_app->input->getInt('id');
		$query		= $this->_db->getQuery(true);
		$user		= JFactory::getUser();
		$usergroups	= rseventsproHelper::getUserGroups();
		$joomgroups	= JAccess::getGroupsByUser($user->get('id'));
		
		// If the user is logged in remove the Public group
		if (!$user->get('guest')) {
			foreach ($joomgroups as $key => $value) {
				if ($value == 1) {
					unset($joomgroups[$key]);
				}
			}
		}
		
		$query->clear()
			->select('*')
			->from($this->_db->qn('#__rseventspro_tickets'))
			->where($this->_db->qn('ide').' = '.$id)
			->order($this->_db->qn('name').' ASC');
		
		$this->_db->setQuery($query);
		if ($tickets = $this->_db->loadObjectList()) {
			foreach($tickets as $i => $ticket) {
				$skip = false;
				if (!empty($ticket->groups)) {
					$registry = new JRegistry;
					$registry->loadString($ticket->groups);
					$groups = $registry->toArray();
					
					// Check first in the RSEvents!Pro groups
					if (!empty($usergroups)) {
						if (!empty($groups)) {
							foreach ($usergroups as $usergroup) {
								if (!in_array($usergroup,$groups)) {
									$skip = true;
								}
							}
						}
					}
					
					// Check in the Joomla! groups section
					if (!empty($joomgroups) && empty($usergroups)) {
						if (!empty($groups)) {
							$query->clear()
								->select($this->_db->qn('jgroups'))->select($this->_db->qn('jusers'))
								->from($this->_db->qn('#__rseventspro_groups'))
								->where($this->_db->qn('id').' IN ('.implode(',',$groups).')');
							$this->_db->setQuery($query);
							if ($rseproGroups = $this->_db->loadObjectList()) {
								foreach ($rseproGroups as $rseproGroup) {
									// Check group users
									if (!empty($rseproGroup->jusers)) {
										$registry = new JRegistry;
										$registry->loadString($rseproGroup->jusers);
										$jusers = $registry->toArray();
										
										if (!empty($jusers)) {
											$userid = $user->get('id');
											
											if (!in_array($userid,$jusers)) {
												$skip = true;
											}
										}
									}
									
									// Check group joomla acl
									if (!empty($rseproGroup->jgroups)) {
										$registry = new JRegistry;
										$registry->loadString($rseproGroup->jgroups);
										$jgroups = $registry->toArray();
										
										if (!empty($jgroups)) {
											foreach ($joomgroups as $gr) {
												if (!in_array($gr,$jgroups)) {
													$skip = true;
												}
											}
										}
									}
								}
							}
						}
					}
				}
				
				if ($skip)
					unset($tickets[$i]);
			}
			
			return $tickets;
		}
		
		return array();
	}
	
	// Get event tickets
	public function getEventTickets() {
		$return   = array();
		$tickets  = $this->getTickets();
		
		if (!empty($tickets)) {
			foreach ($tickets as $ticket) {				
				$checkticket = rseventsproHelper::checkticket($ticket->id);				
				if ($checkticket == -1) 
					continue;
				
				$price = $ticket->price > 0 ? ' - '.rseventsproHelper::currency($ticket->price) : ' - '.JText::_('COM_RSEVENTSPRO_GLOBAL_FREE');
				$return[] = JHTML::_('select.option', $ticket->id, $ticket->name.$price);
			}
		}
		
		return $return;
	}
	
	function getTicketPayment() {
		$tickets	= $this->getTickets();
		$return		= false;
		
		if (!empty($tickets)) {
			foreach ($tickets as $ticket) {
				if ($ticket->price > 0) 
					$return = true;
			}
		}
		
		return $return;
	}
	
	// Get registered users
	public function getPeople() {
		$id		= $this->_app->input->getInt('id');
		$query	= $this->_db->getQuery(true);
		$return = array();
		
		$query->clear()
			->select($this->_db->qn('id'))->select($this->_db->qn('name'))->select($this->_db->qn('email'))
			->from($this->_db->qn('#__rseventspro_users'))
			->where($this->_db->qn('ide').' = '.$id)
			->where($this->_db->qn('state').' = 0');
		
		$this->_db->setQuery($query);
		$pending = $this->_db->loadObjectList();
		
		$query->clear()
			->select($this->_db->qn('id'))->select($this->_db->qn('name'))->select($this->_db->qn('email'))
			->from($this->_db->qn('#__rseventspro_users'))
			->where($this->_db->qn('ide').' = '.$id)
			->where($this->_db->qn('state').' = 1');
		
		$this->_db->setQuery($query);
		$accepted = $this->_db->loadObjectList();
		
		$query->clear()
			->select($this->_db->qn('id'))->select($this->_db->qn('name'))->select($this->_db->qn('email'))
			->from($this->_db->qn('#__rseventspro_users'))
			->where($this->_db->qn('ide').' = '.$id)
			->where($this->_db->qn('state').' = 2');
			
		$this->_db->setQuery($query);
		$denied = $this->_db->loadObjectList();
		
		if (!empty($pending)) {
			$pendingobjstart = new stdClass();
			$pendingobjstart->value = '<OPTGROUP>';
			$pendingobjstart->text = JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_PENDING');
			$return[] = $pendingobjstart;
			
			foreach ($pending as $subscriber)
				$return[] = JHTML::_('select.option' , $subscriber->id, $subscriber->name . ' (' .$subscriber->email.')');
			
			$pendingobjend = new stdClass();
			$pendingobjend->value = '</OPTGROUP>';
			$pendingobjend->text = JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_PENDING');
			$return[] = $pendingobjend;
		}
		
		if (!empty($accepted))
		{
			$acceptedobjstart = new stdClass();
			$acceptedobjstart->value = '<OPTGROUP>';
			$acceptedobjstart->text = JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_ACCEPTED');
			$return[] = $acceptedobjstart;
			
			foreach ($accepted as $subscriber)
				$return[] = JHTML::_('select.option' , $subscriber->id, $subscriber->name . ' (' .$subscriber->email.')');
			
			$acceptedobjend = new stdClass();
			$acceptedobjend->value = '</OPTGROUP>';
			$acceptedobjend->text = JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_ACCEPTED');
			$return[] = $acceptedobjend;
		}
		
		if (!empty($denied))
		{
			$deniedobjstart = new stdClass();
			$deniedobjstart->value = '<OPTGROUP>';
			$deniedobjstart->text = JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_DENIED');
			$return[] = $deniedobjstart;
			
			foreach ($denied as $subscriber)
				$return[] = JHTML::_('select.option' , $subscriber->id, $subscriber->name . ' (' .$subscriber->email.')');
			
			$deniedobjend = new stdClass();
			$deniedobjend->value = '</OPTGROUP>';
			$deniedobjend->text = JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_DENIED');
			$return[] = $deniedobjend;
		}		
		return $return;
	}
	
	// Get events map
	public function getEventsMap() {
		$params		= rseventsproHelper::getParams();
		$query		= $this->_db->getQuery(true);
		$subquery	= $this->_db->getQuery(true);
		$categories	= $params->get('categories','');
		$locations	= $params->get('locations','');
		$tags		= $params->get('tags','');
		$return		= array();
		
		$this->_filters = $this->getFilters();
		$where		= $this->_buildWhere();
		
		$query->clear()
			->select($this->_db->qn('e.id'))->select($this->_db->qn('e.name'))->select($this->_db->qn('e.start'))->select($this->_db->qn('e.owner'))->select($this->_db->qn('e.end'))->select($this->_db->qn('e.allday'))
			->select($this->_db->qn('l.id','lid'))->select($this->_db->qn('l.name','lname'))->select($this->_db->qn('l.address'))->select($this->_db->qn('l.coordinates'))
			->from($this->_db->qn('#__rseventspro_events','e'))
			->join('left', $this->_db->qn('#__rseventspro_locations','l').' ON '.$this->_db->qn('e.location').' = '.$this->_db->qn('l.id'))
			->where($this->_db->qn('e.published').' = 1')
			->where($this->_db->qn('e.completed').' = 1');
		
		if (!empty($categories)) {
			JArrayHelper::toInteger($categories);
			
			$subquery->clear()
				->select($this->_db->qn('tx.ide'))
				->from($this->_db->qn('#__rseventspro_taxonomy','tx'))
				->join('left', $this->_db->qn('#__categories','c').' ON '.$this->_db->qn('c.id').' = '.$this->_db->qn('tx.id'))
				->where($this->_db->qn('c.id').' IN ('.implode(',',$categories).')')
				->where($this->_db->qn('tx.type').' = '.$this->_db->q('category'))
				->where($this->_db->qn('c.extension').' = '.$this->_db->q('com_rseventspro'));
			
			if (JLanguageMultilang::isEnabled()) {
				$subquery->where('c.language IN ('.$this->_db->q(JFactory::getLanguage()->getTag()).','.$this->_db->q('*').')');
			}
			
			$user	= JFactory::getUser();
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$subquery->where('c.access IN ('.$groups.')');
			
			$query->where($this->_db->qn('e.id').' IN ('.$subquery.')');
		}
		
		if (!empty($tags)) {
			JArrayHelper::toInteger($tags);
			
			$subquery->clear()
				->select($this->_db->qn('tx.ide'))
				->from($this->_db->qn('#__rseventspro_taxonomy','tx'))
				->join('left', $this->_db->qn('#__rseventspro_tags','t').' ON '.$this->_db->qn('t.id').' = '.$this->_db->qn('tx.id'))
				->where($this->_db->qn('t.id').' IN ('.implode(',',$tags).')')
				->where($this->_db->qn('tx.type').' = '.$this->_db->q('tag'));
			
			$query->where($this->_db->qn('e.id').' IN ('.$subquery.')');
		}
		
		if (!empty($locations)) {
			JArrayHelper::toInteger($locations);
			
			$query->where($this->_db->qn('e.location').' IN ('.implode(',',$locations).')');
		}
		
		if (!empty($where)) {
			$query = (string) $query;
			$query .= ' '.$where;
		}
		
		$this->_db->setQuery($query);
		$events = $this->_db->loadObjectList();
		
		if (!empty($events)) {
			foreach ($events as $event) {
				if (!rseventsproHelper::canview($event->id) && $event->owner != $this->_user->get('id')) 
					continue;
				
				$return[$event->lid][] = $event;
			}
		}
		
		return $return;
	}
	
	// Get location details
	public function getLocation() {
		$id = $this->_app->input->getInt('id');
		$row = JTable::getInstance('Location','rseventsproTable');
		$row->load($id);
		
		$registry = new JRegistry();
		$registry->loadString($row->gallery_tags);
		$row->gallery_tags = $registry->toArray();
		
		return $row;
	}
	
	// Get event details
	public function getEvent() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/events.php';
		
		$id		= $this->_app->input->getInt('id');
		$jform	= $this->_app->input->get('jform',array(),'array');
		$task	= $this->_app->input->get('task');
		$query	= $this->_db->getQuery(true);
		$tasks	= array('approve','pending','denied','savesubscriber','removesubscriber');
		
		if (in_array($task,$tasks)) {
			$theid = $id;
			
			if ($task == 'savesubscriber')
				$theid = $jform['id'];
			
			$query->clear()
				->select($this->_db->qn('ide'))
				->from($this->_db->qn('#__rseventspro_users'))
				->where($this->_db->qn('id').' = '.$theid);
				
			$this->_db->setQuery($query);
			$id = (int) $this->_db->loadResult();
		} elseif ($task == 'message') {
			$id = (int) $jform['id'];
		} elseif ($task == 'saveticket' || $task == 'savecoupon') {
			$id = (int) $jform['ide'];
		}
		
		$event = RSEvent::getInstance($id);
		return $event->getEvent();
	}
	
	// Get owner
	public function getOwner() {
		$jinput = $this->_app->input;
		$query	= $this->_db->getQuery(true);
		$id		= $jinput->getInt('id');
		
		if (empty($id)) {
			$event = $jinput->get('jform',array(),'array');
			$id = isset($event['id']) ? (int) $event['id'] : 0;
		}
		
		// Get id from file
		if ($jinput->get('from') == 'file') {
			$file = $jinput->getInt('id');
			
			$query->clear()
				->select($this->_db->qn('ide'))
				->from($this->_db->qn('#__rseventspro_files'))
				->where($this->_db->qn('id').' = '.$file);
			
			$this->_db->setQuery($query);
			$id = (int) $this->_db->loadResult();
		}
		
		// Get id from ticket
		if ($jinput->get('from') == 'ticket') {
			$ticket = $jinput->getInt('id');
			
			$query->clear()
				->select($this->_db->qn('ide'))
				->from($this->_db->qn('#__rseventspro_tickets'))
				->where($this->_db->qn('id').' = '.$ticket);
			
			$this->_db->setQuery($query);
			$id = (int) $this->_db->loadResult();
		}
		
		// Get id from coupon
		if ($jinput->get('from') == 'coupon') {
			$coupon = $jinput->getInt('id');
			
			$query->clear()
				->select($this->_db->qn('ide'))
				->from($this->_db->qn('#__rseventspro_coupons'))
				->where($this->_db->qn('id').' = '.$coupon);
			
			$this->_db->setQuery($query);
			$id = (int) $this->_db->loadResult();
		}
		
		$query->clear();
		if ($this->_user->get('guest')) {
			$query->select($this->_db->qn('sid'));
		} else {
			$query->select($this->_db->qn('owner'));
		}
		$query->from($this->_db->qn('#__rseventspro_events'))
			->where($this->_db->qn('id').' = '.(int) $id);
		
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	
	// Get RSForm!Pro data
	public function getFields() {
		$id = $this->_app->input->getInt('id');
		return rseventsproHelper::getRSFormData($id);
	}
	
	// Remove event
	public function remove() {
		$id = $this->_app->input->getInt('id');
		rseventsproHelper::remove($id);
		return true;
	}
	
	// Get filters
	public function getFilters() {
		$itemid 	= $this->_app->input->getInt('Itemid');
		$parent		= $this->_app->input->getInt('parent');
		
		$columns 	= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_columns'.$itemid.$parent, 	'filter_from',		array(), 'array');
		$operators 	= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_operators'.$itemid.$parent,	'filter_condition',	array(), 'array');
		$values 	= $this->_app->getUserStateFromRequest('com_rseventspro.events.filter_values'.$itemid.$parent,		'search',			array(), 'array');
		
		if ($columns && $columns[0] == '')
			$columns = $operators = $values = array();
		
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
			$this->_app->setUserState('com_rseventspro.events.filter_columns'.$itemid.$parent,array_merge($columns));
			$this->_app->setUserState('com_rseventspro.events.filter_operators'.$itemid.$parent,array_merge($operators));
			$this->_app->setUserState('com_rseventspro.events.filter_values'.$itemid.$parent,array_merge($values));
		}
		
		if ($this->_app->input->getInt('rs_clear',0)) {
			$this->_app->setUserState('com_rseventspro.events.filter_columns'.$itemid.$parent,array());
			$this->_app->setUserState('com_rseventspro.events.filter_operators'.$itemid.$parent,array());
			$this->_app->setUserState('com_rseventspro.events.filter_values'.$itemid.$parent,array());
			$columns = $operators = $values = array();
		}
		
		return array(array_merge($columns), array_merge($operators), array_merge($values));
	}
	
	// Set filter
	public function setFilter($type,$value) {
		$itemid 	= $this->_app->input->getInt('Itemid');
		$parent		= $this->_app->input->getInt('parent');
		
		$this->_app->setUserState('com_rseventspro.events.filter_columns'.$itemid.$parent,array($type));
		$this->_app->setUserState('com_rseventspro.events.filter_operators'.$itemid.$parent,array('is'));
		$this->_app->setUserState('com_rseventspro.events.filter_values'.$itemid.$parent,array($value));
		
		return true;
	}
	
	// Get name of category, tag or location
	protected function getNameType($type, $value) {
		$query	= $this->_db->getQuery(true);
		
		if ($type == 'category') {
			$query->clear()
				->select($this->_db->qn('title'))
				->from($this->_db->qn('#__categories'))
				->where($this->_db->qn('extension').' = '.$this->_db->q('com_rseventspro'))
				->where($this->_db->qn('id').' = '.(int) $value);
			
			$this->_db->setQuery($query);
			return $this->_db->loadResult();
		} else if ($type == 'location') {
			$query->clear()
				->select($this->_db->qn('name'))
				->from($this->_db->qn('#__rseventspro_locations'))
				->where($this->_db->qn('id').' = '.(int) $value);
			
			$this->_db->setQuery($query);
			return $this->_db->loadResult();
		} else if ($type == 'tag') {
			$query->clear()
				->select($this->_db->qn('name'))
				->from($this->_db->qn('#__rseventspro_tags'))
				->where($this->_db->qn('id').' = '.(int) $value);
			
			$this->_db->setQuery($query);
			return $this->_db->loadResult();
		} else return '';
	}
	
	// Get category details
	public function getEventCategory() {
		$doc		= JFactory::getDocument();
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
						->where($this->_db->qn('extension').' = '.$this->_db->q('com_rseventspro'))
						->where($this->_db->qn('title').' = '.$this->_db->q($value));
					
					$this->_db->setQuery($query);
					$category = (int) $this->_db->loadResult();
				}
				$count++;
			}
		}
		
		// Search the category within the params
		if (empty($count) && empty($category)) {
			$params 	= rseventsproHelper::getParams();
			if ($pcategories = $params->get('categories','')) {
				foreach ($pcategories as $cat) {
					$category = (int) $cat;
					$count++;
				}
			}
		}
		
		// Get Category details
		if ($count == 1 && $category > 0) {
			jimport('joomla.application.categories');
			$categories = JCategories::getInstance('Rseventspro');
			$item = $categories->get($category);
			
			// Check whether category access level allows access.
			$user	= JFactory::getUser();
			$groups	= $user->getAuthorisedViewLevels();
			if (!is_null($item) && !in_array($item->access, $groups)) {
				return JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			}
			
			if ($item) {
				// Set Meta Description
				if ($item->metadesc) {
					$doc->setDescription($item->metadesc);
				}
				
				// Set Meta Keywords
				if ($item->metakey) {
					$doc->setMetadata('keywords', $item->metakey);
				}
				
				// Set Author
				if (JFactory::getApplication()->getCfg('MetaAuthor') == '1') {
					$doc->setMetaData('author', $item->getMetadata()->get('author'));
				}
				
				// Set Robots
				$robots = $item->getMetadata()->get('robots');
				if ($robots) {
					$doc->setMetadata('robots', $robots);
				}
			}
			
			return $item;
		}
		
		return false;
	}
	
	// Export subscribers
	public function exportguests() {
		$query = $this->_subscrquery;
		rseventsproHelper::exportSubscribersCSV($query);
	}
	
	// Change subscriber status
	public function status($pk, $value) {
		$query = $this->_db->getQuery(true);
		
		$query->clear()
			->select($this->_db->qn('state'))
			->from($this->_db->qn('#__rseventspro_users'))
			->where($this->_db->qn('id').' = '.$pk);
		
		$this->_db->setQuery($query);
		$oldstate = $this->_db->loadResult();
		
		$query->clear()
			->update($this->_db->qn('#__rseventspro_users'))
			->set($this->_db->qn('state').' = '.(int) $value)
			->where($this->_db->qn('id').' = '.$pk);
		
		$this->_db->setQuery($query);
		$this->_db->execute();
		
		// Send activation email
		if ($oldstate != 1 && $value == 1) {
			rseventsproHelper::confirm($pk);
		}
		
		// Send denied email
		if ($oldstate != 2 && $value == 2) {
			rseventsproHelper::denied($pk);
		}
		
		return true;
	}
	
	// Save subscriber
	public function savesubscriber() {
		$table	= JTable::getInstance('Subscription','rseventsproTable');
		$data	= $this->_app->input->get('jform',array(),'array');
		$query	= $this->_db->getQuery(true);
		
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}
		
		// Get old state
		$query->clear()
			->select($this->_db->qn('state'))
			->from($this->_db->qn('#__rseventspro_users'))
			->where($this->_db->qn('id').' = '.$table->id);
		
		$this->_db->setQuery($query);
		$state = $this->_db->loadResult();
		
		if ($table->store()) {
			// Send activation email
			if ($state != 1 && $data['state'] == 1)
				rseventsproHelper::confirm($table->id);
			
			// Send denied email
			if ($state != 2 && $data['state'] == 2)
				rseventsproHelper::denied($table->id);
			
			return true;
		} else {
			$this->setError($table->getError());
			return false;
		}
	}
	
	// Remove subscriber
	public function removesubscriber() {
		$table	= JTable::getInstance('Subscription','rseventsproTable');
		$id		= $this->_app->input->getInt('id');
		
		if (!$table->delete($id)) {
			$this->setError($table->getError());
			return false;
		}
		
		return true;
	}
	
	// Send message to guests
	public function message() {
		$send	= array();
		$jform	= $this->_app->input->get('jform',array(),'array');
		$people	= $jform['subscribers'];
		$query	= $this->_db->getQuery(true);
		
		if (isset($jform['pending']) && $jform['pending'] == 1) $send[] = 0;
		if (isset($jform['accepted']) && $jform['accepted'] == 1) $send[] = 1;
		if (isset($jform['denied']) && $jform['denied'] == 1) $send[] = 2;		
		
		if (!empty($send) || !empty($people))
		{
			if (!empty($people))
				JArrayHelper::toInteger($people);
				
			$query->clear()
				->select($this->_db->qn('email'))->select($this->_db->qn('name'))->select($this->_db->qn('ide'))
				->from($this->_db->qn('#__rseventspro_users'))
				->where($this->_db->qn('ide').' = '.(int) $jform['id']);
			
			if (empty($people) && !empty($send)) {
				$query->where($this->_db->qn('state').' IN ('.implode(',',$send).')');
			} elseif (empty($send) && !empty($people)) {
				$query->where($this->_db->qn('id').' IN ('.implode(',',$people).')');
			} elseif (!empty($send) && !empty($people)) {
				$query->where('('.$this->_db->qn('state').' IN ('.implode(',',$send).') OR '.$this->_db->qn('id').' IN ('.implode(',',$people).'))');
			}
			
			$this->_db->setQuery($query);
			$subscribers = $this->_db->loadObjectList();
			
			if (!empty($subscribers)) {
				$subject = $jform['subject'];
				$message = $jform['message'];
				
				foreach ($subscribers as $subscriber)
					rseventsproEmails::guests($subscriber->email,$subscriber->ide,$subscriber->name,$subject,$message);
			}
		}
		
		return true;
	}
	
	// Invite people to event
	public function invite() {
		jimport('joomla.mail.helper');
		
		$lang		= JFactory::getLanguage();
		$jform		= $this->_app->input->get('jform',array(),'array');
		$from		= $jform['from'];
		$fromname	= $jform['from_name'];
		$emails		= $jform['emails'];
		$ide		= $this->_app->input->getInt('id');
		
		$from		= !empty($from) ? $from : rseventsproHelper::getConfig('email_from');
		$fromname	= !empty($fromname) ? $fromname : rseventsproHelper::getConfig('email_fromname');
		
		if (!empty($emails)) {
			$emails = str_replace("\r",'',$emails);
			$emails = explode("\n",$emails);
			
			if (!empty($emails)) {
				foreach ($emails as $email) {
					if (JMailHelper::isEmailAddress($email))
						rseventsproEmails::invite($from,$fromname,$email,$ide, $lang->getTag());
				}
			}
		}
		
		return true;
	}
	
	// Export event 
	public function export() {
		$query	= $this->_db->getQuery(true);
		$id		= $this->_app->input->getInt('id');
		
		$query->clear()
			->select($this->_db->qn('e.name'))->select($this->_db->qn('e.start'))->select($this->_db->qn('e.end'))->select($this->_db->qn('e.description'))
			->select($this->_db->qn('l.name','locationname'))->select($this->_db->qn('l.address'))->select($this->_db->qn('e.allday'))
			->from($this->_db->qn('#__rseventspro_events','e'))
			->join('left', $this->_db->qn('#__rseventspro_locations','l').' ON '.$this->_db->qn('l.id').' = '.$this->_db->qn('e.location'))
			->where($this->_db->qn('e.id').' = '.(int) $id);
		
		$this->_db->setQuery($query);
		if ($event = $this->_db->loadObject()) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/ical/iCalcreator.class.php';
			
			$config = array("unique_id" => JURI::root(), 'filename' => JFilterOutput::stringURLSafe($event->name).'.ics');
			$v = new vcalendar( $config );
			$v->setProperty( "method", "PUBLISH" );
			
			$description = strip_tags($event->description);
			$description = str_replace("\n",'',$description);
			
			$start	= rseventsproHelper::date($event->start,null,false,true);
			$end	= rseventsproHelper::date($event->end,null,false,true);
			
			$start->setTZByID($start->getTZID());
			$start->convertTZ(new RSDate_Timezone('GMT'));
			$end->setTZByID($end->getTZID());
			$end->convertTZ(new RSDate_Timezone('GMT'));
			
			$vevent = &$v->newComponent( "vevent" );
			$vevent->setProperty( "dtstart", array($start->getYear(), $start->getMonth(), $start->getDay(), $start->getHour(), $start->getMinute(), $start->getSecond(), 'tz' => 'Z'));
			if (!$event->allday) $vevent->setProperty( "dtend", array($end->getYear(), $end->getMonth(), $end->getDay(), $end->getHour(), $end->getMinute(), $end->getSecond(), 'tz' => 'Z'));
			$vevent->setProperty( "LOCATION", $event->locationname. ' (' .$event->address . ')' );
			$vevent->setProperty( "summary", $event->name ); 
			$vevent->setProperty( "description", $description);
			$v->returnCalendar();
			
			JFactory::getApplication()->close();
		}
		
		return false;
	}
	
	// Rate event
	public function rate() {
		$id		= $this->_app->input->getInt('id',0);
		$vote	= $this->_app->input->getInt('feedback',0);
		$ip		= $_SERVER['REMOTE_ADDR'];
		$query	= $this->_db->getQuery(true);
		
		//check for the id of the event and for the number of votes
		if ($id == 0 || $vote == 0) {
			return '0|'.JText::_('COM_RSEVENTSPRO_INVALID_EVENT_OR_BLANK_VOTE');
		}
		
		//check for vote number
		if ($vote > 5){
			return '0|'.JText::_('COM_RSEVENTSPRO_INVALID_VOTE');
		}
		
		//check if the user or the ip has already voted
		$query->clear()
			->select($this->_db->qn('id'))
			->from($this->_db->qn('#__rseventspro_taxonomy'))
			->where($this->_db->qn('extra').' = '.$this->_db->q($ip))
			->where($this->_db->qn('ide').' = '.$id)
			->where($this->_db->qn('type').' = '.$this->_db->q('rating'));
			
		$this->_db->setQuery($query,0,1);
		$voted = $this->_db->loadResult();
		
		//if the user voted do nothing
		if ($voted) {
			return '0|'.JText::_('COM_RSEVENTSPRO_ALREADY_VOTED');
		}
		
		//insert the vote
		$query->clear()
			->insert($this->_db->qn('#__rseventspro_taxonomy'))
			->set($this->_db->qn('extra').' = '.$this->_db->q($ip))
			->set($this->_db->qn('ide').' = '.$id)
			->set($this->_db->qn('id').' = '.$this->_db->q($vote))
			->set($this->_db->qn('type').' = '.$this->_db->q('rating'));
		
		$this->_db->setQuery($query);
		$this->_db->execute();
		
		//get the total votes
		$query->clear()
			->select('CEIL(IFNULL(SUM(id)/COUNT(id),0))')
			->from($this->_db->qn('#__rseventspro_taxonomy'))
			->where($this->_db->qn('ide').' = '.$id)
			->where($this->_db->qn('type').' = '.$this->_db->q('rating'));
		
		
		$this->_db->setQuery($query);
		$rating = (int) $this->_db->loadResult();
		
		return $rating.'|'.JText::_('COM_RSEVENTSPRO_VOTE_ADDED');
	}
	
	// Save location
	public function savelocation() {
		$table	= JTable::getInstance('Location','rseventsproTable');
		$data	= $this->_app->input->get('jform',array(),'array');
		
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}
		
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}
		
		if (!empty($this->permissions['event_moderation']) && !rseventsproHelper::admin()) 
			$table->published = 0;
		
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}
		
		$this->setState($this->getName().'.lid',$table->id);
		
		return true;
	}
	
	// Save category
	public function savecategory() {
		$table	= JTable::getInstance('Category','rseventsproTable');
		$data	= $this->_app->input->get('jform',array(),'array');
		
		$data['extension'] = 'com_rseventspro';
		$data['language'] = '*';
		$table = JTable::getInstance('Category', 'rseventsproTable');
		$table->setLocation($data['parent_id'], 'last-child');
		$table->save($data);
		$table->rebuildPath($table->id);
		$table->rebuild($table->id, $table->lft, $table->level, $table->path);
		
		$this->setState($this->getName().'.cid',$table->id);
		return true;
	}
	
	// Subscribe user
	public function subscribe($idsubmission = null) {
		jimport('joomla.mail.helper');
		
		$now			= JFactory::getDate();
		$query			= $this->_db->getQuery(true);
		$lang			= JFactory::getLanguage();
		$nowunix		= $now->toUnix();
		$jinput			= $this->_app->input;
		$id				= $jinput->getInt('id');
		$name			= $jinput->getString('name');
		$email			= $jinput->getString('email');
		$payment		= $jinput->getString('payment');
		$form			= $jinput->get('form',array(),'array');
		$from			= $jinput->getInt('from');
		$total			= 0;
		$discount		= 0;
		$info			= '';
		$cansubscribe	= $this->getCanSubscribe();
		$couponid		= 0;
		$tickets		= array();
		$seats			= array();
		$state			= 0;
		$tax			= 0;
		
		// RSForm!Pro mapping
		if (!empty($form['RSEProName']) && $jinput->get('option') == 'com_rseventspro')	{
			$id			= $jinput->getInt('id');
			$name		= @$form['RSEProName'];
			$email		= @$form['RSEProEmail'];
			$payment	= is_array($form['RSEProPayment']) ? $form['RSEProPayment'][0] : @$form['RSEProPayment'];
		}
		
		// Get event name
		$query->clear()
			->select($this->_db->qn('name'))->select($this->_db->qn('discounts'))->select($this->_db->qn('early_fee'))->select($this->_db->qn('early_fee_type'))
			->select($this->_db->qn('early_fee_end'))->select($this->_db->qn('late_fee'))->select($this->_db->qn('late_fee_type'))->select($this->_db->qn('late_fee_start'))
			->select($this->_db->qn('automatically_approve'))->select($this->_db->qn('notify_me'))->select($this->_db->qn('owner'))->select($this->_db->qn('ticketsconfig'))
			->from($this->_db->qn('#__rseventspro_events'))
			->where($this->_db->qn('id').' = '.$id);
		
		$this->_db->setQuery($query);
		$event = $this->_db->loadObject();
		
		if (!JMailHelper::isEmailAddress($email) || empty($name))
			return array('status' => false, 'url' => rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribe&id='.rseventsproHelper::sef($id,$event->name),false) , 'message' => JText::_('COM_RSEVENTSPRO_INVALID_SUBSCRIBE_FORM'));
		
		if (!$cansubscribe['status'])
			return array('status' => false, 'id' => $id, 'name' => $event->name, 'message' => $cansubscribe['err']);
		
		// Set tickets
		if ($event->ticketsconfig) {
			$tickets	= array();
			$thetickets	= $jinput->get('tickets',array(),'array');
			$unlimited	= $jinput->get('unlimited',array(),'array');
			
			foreach ($thetickets as $tid => $theticket) {
				$tickets[$tid] = count($theticket);
			}
			
			if (!empty($unlimited)) {
				JArrayHelper::toInteger($unlimited);
				foreach ($unlimited as $unlimitedid => $quantity)
					$tickets[$unlimitedid] = $quantity;
			}
			
			$seats = $thetickets;
		} else {
			if (rseventsproHelper::getConfig('multi_tickets','int')) {
				$tickets = $jinput->get('tickets',array(),'array');
				
				if (empty($tickets) && !empty($form['RSEProTickets']) && $jinput->get('option') == 'com_rseventspro') {
					if ($from == 1) {
						$tickets = array($form['RSEProTickets'] => $jinput->getInt('number'));
					} else  {
						$tickets = array($form['RSEProTickets'] => $jinput->getInt('numberinp'));
					}
				}
			} else {
				$ticket = (!empty($form['RSEProTickets']) && $jinput->get('option') == 'com_rseventspro') ? $form['RSEProTickets'] : $jinput->get('ticket');
				
				if (!empty($ticket)) {
					if ($from == 1) {
						$tickets = array($ticket => $jinput->getInt('number'));
					} else {
						$tickets = array($ticket => $jinput->getInt('numberinp'));
					}
				}
			}
		}
		
		// Set the verification string
		$verification = md5(time().$id.$name);
		
		// Get the user id
		$uid = 0;
		$juid = $this->_user->get('id');
		if (empty($juid) && rseventsproHelper::getConfig('create_user','int')) {
			$uid = rseventsproHelper::returnUser($email,$name);
		} else $uid = $juid;
		
		$idsubmission = !is_null($idsubmission) ? $idsubmission : 0;
		
		// Trigger before the user subscribes.
		$this->_app->triggerEvent('rsepro_beforeSubscribe',array(array('name'=>&$name, 'email'=>&$email)));
		
		$query->clear()
			->insert($this->_db->qn('#__rseventspro_users'))
			->set($this->_db->qn('ide').' = '.(int) $id)
			->set($this->_db->qn('idu').' = '.(int) $uid)
			->set($this->_db->qn('name').' = '.$this->_db->q($name))
			->set($this->_db->qn('email').' = '.$this->_db->q($email))
			->set($this->_db->qn('date').' = '.$this->_db->q($now->toSql()))
			->set($this->_db->qn('state').' = 0')
			->set($this->_db->qn('SubmissionId').' = '.(int) $idsubmission)
			->set($this->_db->qn('verification').' = '.$this->_db->q($verification))
			->set($this->_db->qn('gateway').' = '.$this->_db->q($payment))
			->set($this->_db->qn('ip').' = '.$this->_db->q($_SERVER['REMOTE_ADDR']))
			->set($this->_db->qn('lang').' = '.$this->_db->q($lang->getTag()));
		
		// Add the method that iDeal is using
		if (rseventsproHelper::ideal() && $payment == 'ideal') {
			$iDealMethod = rseventsproHelper::getConfig('ideal_account');
			$query->set($this->_db->qn('ideal').' = '.$this->_db->q($iDealMethod));
		}
		
		$this->_db->setQuery($query);
		$this->_db->execute();
		$ids = (int) $this->_db->insertid();
		
		if (!empty($tickets)) {
			foreach ($tickets as $tid => $quantity) {
				$checkticket = rseventsproHelper::checkticket($tid);
				if ($checkticket == RSEPRO_TICKETS_NOT_AVAILABLE) continue;
				
				$query->clear()
					->select($this->_db->qn('name'))->select($this->_db->qn('price'))->select($this->_db->qn('seats'))
					->from($this->_db->qn('#__rseventspro_tickets'))
					->where($this->_db->qn('id').' = '.(int) $tid);
				
				$this->_db->setQuery($query);
				$ticket = $this->_db->loadObject();
				
				if ($checkticket > RSEPRO_TICKETS_UNLIMITED && $quantity > $checkticket) 
					$quantity = $checkticket;
				
				// Calculate the total
				if ($ticket->price > 0) {
					$price = $ticket->price * $quantity;
					if ($event->discounts) {
						$eventdiscount = rseventsproHelper::discount($id,$ticket->price);
						if (is_array($eventdiscount)) {
							
							$query->clear()
								->select($this->_db->qn('c.action'))->select($this->_db->qn('c.type'))
								->from($this->_db->qn('#__rseventspro_coupons','c'))
								->join('left',$this->_db->qn('#__rseventspro_coupon_codes','cc').' ON '.$this->_db->qn('cc.idc').' = '.$this->_db->qn('c.id'))
								->where($this->_db->qn('cc.id').' = '.(int) $eventdiscount['id']);
							
							$this->_db->setQuery($query);
							$thecoupon = $this->_db->loadObject();
							
							if ($thecoupon->action == 0) {
								if ($thecoupon->type == 0)
									$discount += $eventdiscount['discount'] * $quantity;
								else
									$discount += $eventdiscount['discount'];
							}
							$couponid = $eventdiscount['id'];
						}
					}
					$total += $price;
				}
				
				// Get purchased tickets
				if ($ticket->price > 0) {
					$info .= $quantity . ' x ' .$ticket->name.' ('.rseventsproHelper::currency($ticket->price).') '.rseventsproHelper::getSeats($ids,$tid).' <br />';
				} else {
					$info .= $quantity . ' x ' .$ticket->name.' ('.JText::_('COM_RSEVENTSPRO_GLOBAL_FREE').')<br />';
				}
				
				// Insert tickets into database
				$query->clear()
					->insert($this->_db->qn('#__rseventspro_user_tickets'))
					->set($this->_db->qn('ids').' = '.(int) $ids)
					->set($this->_db->qn('idt').' = '.(int) $tid)
					->set($this->_db->qn('quantity').' = '.(int) $quantity);
				
				$this->_db->setQuery($query);
				$this->_db->execute();
				
				// Add seats
				if (isset($seats[$tid]) && !empty($seats[$tid])) {
					$theseats = $quantity < count($seats[$tid]) ? array_slice($seats[$tid],0,$quantity) : $seats[$tid];
					
					if (!empty($theseats)) {
						foreach ($theseats as $seat) {
							$query->clear()
								->insert($this->_db->qn('#__rseventspro_user_seats'))
								->set($this->_db->qn('ids').' = '.(int) $ids)
								->set($this->_db->qn('idt').' = '.(int) $tid)
								->set($this->_db->qn('seat').' = '.(int) $seat);
							
							$this->_db->setQuery($query);
							$this->_db->execute();
						}
					}
				}
			}
		}
		
		if ($event->discounts) {
			$eventdiscount = rseventsproHelper::discount($id,$total);
			if (is_array($eventdiscount)) {
				$query->clear()
					->select($this->_db->qn('c.action'))
					->from($this->_db->qn('#__rseventspro_coupons','c'))
					->join('left',$this->_db->qn('#__rseventspro_coupon_codes','cc').' ON '.$this->_db->qn('cc.idc').' = '.$this->_db->qn('c.id'))
					->where($this->_db->qn('cc.id').' = '.(int) $eventdiscount['id']);
				
				$this->_db->setQuery($query);
				$couponaction = $this->_db->loadResult();
				
				if ($couponaction == 1)
					$discount += $eventdiscount['discount'];
				$couponid = $eventdiscount['id'];
			}
		}
		
		// Update the use of the coupon and add the coupon code to the users table
		if ($couponid) {
			$query->clear()
				->update($this->_db->qn('#__rseventspro_coupon_codes'))
				->set($this->_db->qn('used').' = '.$this->_db->qn('used').' + 1')
				->where($this->_db->qn('id').' = '.(int) $couponid);
			
			$this->_db->setQuery($query);
			$this->_db->execute();
			
			$query->clear()
				->select($this->_db->qn('code'))
				->from($this->_db->qn('#__rseventspro_coupon_codes'))
				->where($this->_db->qn('id').' = '.(int) $couponid);
			
			$this->_db->setQuery($query);
			if ($couponcode = $this->_db->loadResult()) {
				$query->clear()
					->update($this->_db->qn('#__rseventspro_users'))
					->set($this->_db->qn('coupon').' = '.$this->_db->q($couponcode))
					->where($this->_db->qn('id').' = '.(int) $ids);
				
				$this->_db->setQuery($query);
				$this->_db->execute();
			}
		}
		
		// Update the total after the discount
		$total = $total - $discount;
		
		// If this is a free ticket subscription automatically approve the subscription
		if ($total == 0 && $event->automatically_approve) {
			$query->clear()
				->update($this->_db->qn('#__rseventspro_users'))
				->set($this->_db->qn('state').' = 1')
				->where($this->_db->qn('id').' = '.(int) $ids);
			
			$this->_db->setQuery($query);
			$this->_db->execute();
			$state = 1;
		}
		
		// Check for late and early fees
		$early = 0;
		if ($total > 0) {
			if (!empty($event->early_fee_end) && $event->early_fee_end != $this->_db->getNullDate()) {
				$early_fee_unix = JFactory::getDate($event->early_fee_end)->toUnix();
				if ($early_fee_unix > $nowunix) {
					$early = rseventsproHelper::setTax($total,$event->early_fee_type,$event->early_fee);
					$total = $total - $early;
				}
			}
		}

		$late = 0;
		if ($total > 0) {
			if (!empty($event->late_fee_start) && $event->late_fee_start != $this->_db->getNullDate()) {
				$late_fee_unix = JFactory::getDate($event->late_fee_start)->toUnix();
				if ($late_fee_unix < $nowunix) {
					$late = rseventsproHelper::setTax($total,$event->late_fee_type,$event->late_fee);
					$total = $total + $late;
				}
			}
		}
		
		// Check to see if the selected payment type is a wire payment
		$query->clear()
			->select($this->_db->qn('id'))->select($this->_db->qn('name'))
			->select($this->_db->qn('tax_type'))->select($this->_db->qn('tax_value'))
			->from($this->_db->qn('#__rseventspro_payments'))
			->where($this->_db->qn('id').' = '.(int) $payment);
		
		$this->_db->setQuery($query);
		$wire = $this->_db->loadObject();
		
		// Add payment tax
		if ($total > 0) {
			if (!empty($wire)) {
				$tax = rseventsproHelper::setTax($total,$wire->tax_type,$wire->tax_value);
				$total = $total + $tax;
			} else {
				$plugintaxes = $this->_app->triggerEvent('rsepro_tax',array(array('method'=>&$payment, 'total'=>$total)));
				
				if (!empty($plugintaxes)) {
					foreach ($plugintaxes as $plugintax) {
						if (!empty($plugintax)) $tax = $plugintax;
					}
				}
				
				$total = $total + $tax;
			}
		}
		
		$query->clear()
			->select($this->_db->qn('coupon'))
			->from($this->_db->qn('#__rseventspro_users'))
			->where($this->_db->qn('id').' = '.(int) $ids);
		$this->_db->setQuery($query);
		$thecouponcode = $this->_db->loadResult();
		
		$ticketstotal		= !empty($total) ? rseventsproHelper::currency($total) : '';
		$ticketsdiscount	= !empty($total) && !empty($discount) ? rseventsproHelper::currency($discount) : '';
		$subscriptionTax	= !empty($total) && !empty($tax) ? rseventsproHelper::currency($tax) : '';
		$lateFee			= !empty($total) && !empty($late) ? rseventsproHelper::currency($late) : '';
		$earlyDiscount		= !empty($total) && !empty($early) ? rseventsproHelper::currency($early) : '';
		$gateway			= rseventsproHelper::getPayment($payment);
		$IP					= $_SERVER['REMOTE_ADDR'];
		$coupon				= !empty($thecouponcode) ? $thecouponcode : '';
		$optionals			= array($info, $ticketstotal, $ticketsdiscount, $subscriptionTax, $lateFee, $earlyDiscount, $gateway, $IP, $coupon);
		
		// Trigger after the user subscribes.
		$this->_app->triggerEvent('rsepro_afterSubscribe',array(array('ids'=>$ids, 'name'=>&$name, 'email'=>&$email, 'discount'=>&$discount, 'early'=>&$early, 'late'=>&$late, 'tax'=>&$tax, 'total'=>$total, 'optionals'=>&$optionals)));
		
		// Update the subscription with the late , early and discount fees
		$query->clear()
			->update($this->_db->qn('#__rseventspro_users'))
			->set($this->_db->qn('discount').' = '.$this->_db->q($discount))
			->set($this->_db->qn('early_fee').' = '.$this->_db->q($early))
			->set($this->_db->qn('late_fee').' = '.$this->_db->q($late))
			->set($this->_db->qn('tax').' = '.$this->_db->q($tax))
			->where($this->_db->qn('id').' = '.(int) $ids);
		
		$this->_db->setQuery($query);
		$this->_db->execute();
		
		// Notify the owner of a new subscription
		if ($ids && $event->notify_me) {
			$theuser =& JFactory::getUser($event->owner); 			
			$additional_data = array(
				'{SubscriberUsername}' => $uid ? JFactory::getUser($uid)->get('username') : '',
				'{SubscriberName}' => $name,
				'{SubscriberEmail}' => $email,
				'{SubscribeDate}' => rseventsproHelper::date($now->toSql(),null,true),
				'{PaymentGateway}' => rseventsproHelper::getPayment($payment),
				'{SubscriberIP}' => $_SERVER['REMOTE_ADDR'],
				'{TicketsInfo}' => $info,
				'{TicketsTotal}' => $ticketstotal,
				'{TicketsDiscount}' => $ticketsdiscount
			);
			
			rseventsproEmails::notify_me($theuser->get('email'), $id, $additional_data, $lang->getTag());
		}
		
		// Send registration email
		rseventsproEmails::registration($email, $id, $name, $optionals);
		
		// Send activation email
		if ($state)
			rseventsproEmails::activation($email, $id, $name, $optionals);
		
		if ($total > 0 && !empty($payment)) {
			if (!empty($wire)) {
				$url = rseventsproHelper::route('index.php?option=com_rseventspro&layout=wire&id='.$ids.'&pid='.rseventsproHelper::sef($wire->id,$wire->name),false);
				$query->clear()
					->update($this->_db->qn('#__rseventspro_users'))
					->set($this->_db->qn('URL').' = '.$this->_db->q(base64_encode($url)))
					->where($this->_db->qn('id').' = '.(int) $ids);
				
				$this->_db->setQuery($query);
				$this->_db->execute();
				
				return array('status' => true, 'url' => $url, 'message' => JText::_('COM_RSEVENTSPRO_REGISTRATION_COMPLETE'));
			} else {
				// Payment plugins
				$url = rseventsproHelper::route('index.php?option=com_rseventspro&task=payment&method='.$payment.'&hash='.md5($ids.$name.$email),false);
				$query->clear()
					->update($this->_db->qn('#__rseventspro_users'))
					->set($this->_db->qn('URL').' = '.$this->_db->q(base64_encode($url)))
					->where($this->_db->qn('id').' = '.(int) $ids);
				
				$this->_db->setQuery($query);
				$this->_db->execute();
				
				return array('status' => true, 'url' => $url, 'message' => JText::_('COM_RSEVENTSPRO_REGISTRATION_COMPLETE'));
			}
		}
		
		$url = rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($id,$event->name),false);
		return array('status' => true, 'url' => $url, 'message' => JText::_('COM_RSEVENTSPRO_REGISTRATION_COMPLETE'));
	}
	
	// Unsubscribe user from the unsubscribe layout
	public function unsubscribeuser() {
		$id		= $this->_app->input->getInt('id');
		$now	= JFactory::getDate()->toUnix();
		$query	= $this->_db->getQuery(true);
		$config	= rseventsproHelper::getConfig();
		
		$query->clear()
			->select($this->_db->qn('e.id'))->select($this->_db->qn('e.name'))
			->select($this->_db->qn('e.unsubscribe_date'))->select($this->_db->qn('e.sync'))
			->select($this->_db->qn('e.notify_me_unsubscribe'))->select($this->_db->qn('e.owner'))
			->from($this->_db->qn('#__rseventspro_events','e'))
			->join('left',$this->_db->qn('#__rseventspro_users','u').' ON '.$this->_db->qn('u.ide').' = '.$this->_db->qn('e.id'))
			->where($this->_db->qn('u.id').' = '.(int) $id);
		
		$this->_db->setQuery($query);
		$event = $this->_db->loadObject();
		
		$URL = rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false);
		
		if (!empty($event->unsubscribe_date) && $event->unsubscribe_date != $this->_db->getNullDate()) {
			$unsubscribe_unix = JFactory::getDate($event->unsubscribe_date)->toUnix();
			if ($now > $unsubscribe_unix) {
				$URL = rseventsproHelper::route('index.php?option=com_rseventspro&layout=unsubscribe&id='.rseventsproHelper::sef($event->id,$event->name).'&tmpl=component',false);
				return array('status' => false, 'url' => $URL, 'message' => JText::_('COM_RSEVENTSPRO_USER_UNSUBSCRIBED_ERROR'));
			}
		}
		
		if (!empty($this->permissions['can_unsubscribe'])) {
			$query->clear()
				->select($this->_db->qn('name'))->select($this->_db->qn('email'))->select($this->_db->qn('SubmissionId'))
				->from($this->_db->qn('#__rseventspro_users'))
				->where($this->_db->qn('id').' = '.(int) $id)
				->where($this->_db->qn('idu').' = '.(int) $this->_user->get('id'));
			
			$this->_db->setQuery($query);
			$subscription = $this->_db->loadObject();
			
			if (!empty($subscription)) {
				$query->clear()
					->select($this->_db->qn('lang'))
					->from($this->_db->qn('#__rseventspro_users'))
					->where($this->_db->qn('id').' = '.$id);
				
				$this->_db->setQuery($query);
				$lang = $this->_db->loadResult();
				
				$query->clear()
					->delete()
					->from($this->_db->qn('#__rseventspro_users'))
					->where($this->_db->qn('id').' = '.$id);
					
				$this->_db->setQuery($query);
				$this->_db->execute();
				
				$query->clear()
					->delete()
					->from($this->_db->qn('#__rseventspro_user_tickets'))
					->where($this->_db->qn('ids').' = '.$id);
					
				$this->_db->setQuery($query);
				$this->_db->execute();
				
				$query->clear()
					->delete()
					->from($this->_db->qn('#__rseventspro_user_seats'))
					->where($this->_db->qn('ids').' = '.$id);
					
				$this->_db->setQuery($query);
				$this->_db->execute();
				
				// Delete RSForm!Pro submission
				if (file_exists(JPATH_SITE.'/components/com_rsform/rsform.php') && $event->sync) {
					$query->clear()
						->delete()
						->from($this->_db->qn('#__rsform_submission_values'))
						->where($this->_db->qn('SubmissionId').' = '.(int) $subscription->SubmissionId);
					
					$this->_db->setQuery($query);
					$this->_db->execute();
					
					$query->clear()
						->delete()
						->from($this->_db->qn('#__rsform_submissions'))
						->where($this->_db->qn('SubmissionId').' = '.(int) $subscription->SubmissionId);
					
					$this->_db->setQuery($query);
					$this->_db->execute();
				}
				
				rseventsproEmails::unsubscribe($subscription->email,$event->id,$subscription->name,$lang);
				
				// Notify the owner
				if ($event->notify_me_unsubscribe) {
					if ($event->owner) {
						$ownerEmail = JFactory::getUser($event->owner)->get('email');
						$ownerName  = rseventsproHelper::getUser($event->owner);
						$from		= $config->email_from;
						$fromName	= $config->email_fromname;
						$subject	= JText::sprintf('COM_RSEVENTSPRO_UNSUBSCRIBE_EMAIL_SUBJECT', $subscription->name, $event->name);
						$body		= JText::sprintf('COM_RSEVENTSPRO_UNSUBSCRIBE_EMAIL_BODY', $ownerName, $subscription->name, $event->name);
						
						$mailer	= JFactory::getMailer();
						$mailer->sendMail($from , $fromName , $ownerEmail , $subject , $body , 0);
					}
				}
				
			}
			return array('status' => true, 'url' => $URL, 'message' => JText::_('COM_RSEVENTSPRO_USER_UNSUBSCRIBED'));
		}
		
		$URL = rseventsproHelper::route('index.php?option=com_rseventspro&layout=unsubscribe&id='.rseventsproHelper::sef($event->id,$event->name).'&tmpl=component',false);
		return array('status' => false, 'url' => $URL, 'message' => JText::_('COM_RSEVENTSPRO_USER_UNSUBSCRIBED_ERROR'));
	}
	
	// Unsubscribe user
	public function unsubscribe() {
		$id		= $this->_app->input->getInt('id');
		$now	= JFactory::getDate()->toUnix();
		$query	= $this->_db->getQuery(true);
		$config	= rseventsproHelper::getConfig();
		
		$query->clear()
			->select($this->_db->qn('id'))->select($this->_db->qn('name'))->select($this->_db->qn('owner'))
			->select($this->_db->qn('sync'))->select($this->_db->qn('notify_me_unsubscribe'))
			->from($this->_db->qn('#__rseventspro_events'))
			->where($this->_db->qn('id').' = '.$id);
		
		$this->_db->setQuery($query);
		$event = $this->_db->loadObject();
		
		$query->clear()
			->select($this->_db->qn('id'))->select($this->_db->qn('name'))
			->select($this->_db->qn('email'))->select($this->_db->qn('SubmissionId'))
			->from($this->_db->qn('#__rseventspro_users'))
			->where($this->_db->qn('ide').' = '.(int) $id)
			->where($this->_db->qn('idu').' = '.(int) $this->_user->get('id'));
		
		$this->_db->setQuery($query);
		$subscription = $this->_db->loadObject();
		
		
		$can_unsubscribe = $this->getCanUnsubscribe();
		if (!$can_unsubscribe) 
			return array('id' => $event->id, 'name' => $event->name, 'message' => JText::_('COM_RSEVENTSPRO_USER_UNSUBSCRIBED_ERROR'));
		
		if (!empty($this->permissions['can_unsubscribe'])) {
			$query->clear()
				->select($this->_db->qn('lang'))
				->from($this->_db->qn('#__rseventspro_users'))
				->where($this->_db->qn('id').' = '.$subscription->id);
			
			$this->_db->setQuery($query);
			$lang = $this->_db->loadResult();
			
			$query->clear()
				->delete()
				->from($this->_db->qn('#__rseventspro_users'))
				->where($this->_db->qn('id').' = '.$subscription->id);
				
			$this->_db->setQuery($query);
			$this->_db->execute();
			
			$query->clear()
				->delete()
				->from($this->_db->qn('#__rseventspro_user_tickets'))
				->where($this->_db->qn('ids').' = '.$subscription->id);
				
			$this->_db->setQuery($query);
			$this->_db->execute();
			
			$query->clear()
				->delete()
				->from($this->_db->qn('#__rseventspro_user_seats'))
				->where($this->_db->qn('ids').' = '.$subscription->id);
				
			$this->_db->setQuery($query);
			$this->_db->execute();
			
			// Delete RSForm!Pro submission
			if (file_exists(JPATH_SITE.'/components/com_rsform/rsform.php') && $event->sync) {
				$query->clear()
					->delete()
					->from($this->_db->qn('#__rsform_submission_values'))
					->where($this->_db->qn('SubmissionId').' = '.(int) $subscription->SubmissionId);
				
				$this->_db->setQuery($query);
				$this->_db->execute();
				
				$query->clear()
					->delete()
					->from($this->_db->qn('#__rsform_submissions'))
					->where($this->_db->qn('SubmissionId').' = '.(int) $subscription->SubmissionId);
				
				$this->_db->setQuery($query);
				$this->_db->execute();
			}
			
			rseventsproEmails::unsubscribe($subscription->email,$id,$subscription->name,$lang);
			
			// Notify the owner
			if ($event->notify_me_unsubscribe) {
				if ($event->owner) {
					$ownerEmail = JFactory::getUser($event->owner)->get('email');
					$ownerName  = rseventsproHelper::getUser($event->owner);
					$from		= $config->email_from;
					$fromName	= $config->email_fromname;
					$subject	= JText::sprintf('COM_RSEVENTSPRO_UNSUBSCRIBE_EMAIL_SUBJECT', $subscription->name, $event->name);
					$body		= JText::sprintf('COM_RSEVENTSPRO_UNSUBSCRIBE_EMAIL_BODY', $ownerName, $subscription->name, $event->name);
					
					$mailer	= JFactory::getMailer();
					$mailer->sendMail($from , $fromName , $ownerEmail , $subject , $body , 0);
				}
			}
			
			return array('id' => $event->id, 'name' => $event->name, 'message' => JText::_('COM_RSEVENTSPRO_USER_UNSUBSCRIBED'));
		}		
		
		return array('id' => $event->id, 'name' => $event->name, 'message' => JText::_('COM_RSEVENTSPRO_USER_UNSUBSCRIBED_ERROR'));
	}
	
	// Check if the user can unsubscribe
	public function getCanUnsubscribe() {
		$id		= $this->_app->input->getInt('id');
		$now	= JFactory::getDate()->toUnix();
		$query	= $this->_db->getQuery(true);
		
		$query->clear()
			->select($this->_db->qn('unsubscribe_date'))
			->from($this->_db->qn('#__rseventspro_events'))
			->where($this->_db->qn('id').' = '.$id);
		
		$this->_db->setQuery($query);
		$unsubscribe_date = $this->_db->loadResult();
		
		if (!empty($unsubscribe_date) && $unsubscribe_date != $this->_db->getNullDate()) {
			$unsubscribeunix = JFactory::getDate($unsubscribe_date)->toUnix();
			if ($now > $unsubscribeunix) return false;
		}
		
		return true;
	}
	
	// Save event ticket
	public function saveticket() {
		$data = $this->_app->input->get('jform',array(),'array');
		$data = (object) $data;
		$groups = $this->_app->input->get('groups',array(),'array');
		if (!empty($groups)) {
			$registry = new JRegistry;
			$registry->loadArray($groups);
			$data->groups = $registry->toString();
		}
		
		$this->_db->insertObject('#__rseventspro_tickets', $data, 'id');
		return $data->id;
	}
	
	// Remove ticket
	public function removeticket() {
		$query	= $this->_db->getQuery(true);
		$id		= $this->_app->input->getInt('id');
		
		if ($id) {
			$query->clear()
				->delete()
				->from($this->_db->qn('#__rseventspro_tickets'))
				->where($this->_db->qn('id').' = '.$id);
			
			$this->_db->setQuery($query);
			if ($this->_db->execute())
				return true;
		}
		
		return false;
	}
	
	// Save event coupon
	public function savecoupon() {
		$query	= $this->_db->getQuery(true);
		$data = $this->_app->input->get('jform',array(),'array');
		$data = (object) $data;
		$groups = $this->_app->input->get('groups',array(),'array');
		if (!empty($groups)) {
			$registry = new JRegistry;
			$registry->loadArray($groups);
			$data->groups = $registry->toString();
		}
		
		if (!empty($data->from) && $data->from != $this->_db->getNullDate()) {
			$start	= new RSDate($data->from);
			$start->setTZByID(rseventsproHelper::getTimezone());
			$start->convertTZ(new RSDate_Timezone('GMT'));
			$data->from = $start->formatLikeDate('Y-m-d H:i:s');
		}
		
		if (!empty($data->to) && $data->to != $this->_db->getNullDate()) {
			$end	= new RSDate($data->to);
			$end->setTZByID(rseventsproHelper::getTimezone());
			$end->convertTZ(new RSDate_Timezone('GMT'));
			$data->to = $end->formatLikeDate('Y-m-d H:i:s');
		}
		
		$this->_db->insertObject('#__rseventspro_coupons', $data, 'id');
		
		if ($codes = JFactory::getApplication()->input->getString('codes')) {
			$codes = explode("\n",$codes);
			if (!empty($codes)) {
				foreach ($codes as $code) {
					$code = trim($code);
					$query->clear()
						->insert($this->_db->qn('#__rseventspro_coupon_codes'))
						->set($this->_db->qn('idc').' = '.(int) $data->id)
						->set($this->_db->qn('code').' = '.$this->_db->q($code));
					
					$this->_db->setQuery($query);
					$this->_db->execute();
				}
			}
		}
		
		return $data->id;
	}
	
	// Remove coupon
	public function removecoupon() {
		$query	= $this->_db->getQuery(true);
		$id		= $this->_app->input->getInt('id');
		
		if ($id) {
			$query->clear()
				->delete()
				->from($this->_db->qn('#__rseventspro_coupons'))
				->where($this->_db->qn('id').' = '.$id);
			
			$this->_db->setQuery($query);
			if ($this->_db->execute()) {
				$query->clear()
					->delete()
					->from($this->_db->qn('#__rseventspro_coupon_codes'))
					->where($this->_db->qn('idc').' = '.$id);
				
				$this->_db->setQuery($query);
				$this->_db->execute();
				return true;
			}
		}
		return false;
	}
	
	// Get file details
	public function getFile() {
		$query	= $this->_db->getQuery(true);
		$id		= $this->_app->input->getInt('id');
		
		$query->clear()
			->select($this->_db->qn('id'))->select($this->_db->qn('name'))->select($this->_db->qn('permissions'))
			->from($this->_db->qn('#__rseventspro_files'))
			->where($this->_db->qn('id').' = '.$id);
		
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}
	
	// Save event file details
	public function savefile() {
		$query	= $this->_db->getQuery(true);
		$jinput	= $this->_app->input->post;
		$id		= $jinput->getInt('id');
		$permissions = '';
		
		$fp0 = $jinput->get('fp0');
		$fp1 = $jinput->get('fp1');
		$fp2 = $jinput->get('fp2');
		$fp3 = $jinput->get('fp3');
		$fp4 = $jinput->get('fp4');
		$fp5 = $jinput->get('fp5');
		
		if (isset($fp0) && $fp0 == 1) $permissions .= '1'; else $permissions .= '0';
		if (isset($fp1) && $fp1 == 1) $permissions .= '1'; else $permissions .= '0';
		if (isset($fp2) && $fp2 == 1) $permissions .= '1'; else $permissions .= '0';
		if (isset($fp3) && $fp3 == 1) $permissions .= '1'; else $permissions .= '0';
		if (isset($fp4) && $fp4 == 1) $permissions .= '1'; else $permissions .= '0';
		if (isset($fp5) && $fp5 == 1) $permissions .= '1'; else $permissions .= '0';
		
		$query->clear()
			->update($this->_db->qn('#__rseventspro_files'))
			->set($this->_db->qn('name').' = '.$this->_db->q($jinput->getString('name')))
			->set($this->_db->qn('permissions').' = '.$this->_db->q($permissions))
			->where($this->_db->qn('id').' = '.$this->_db->q($id));
		
		$this->_db->setQuery($query);
		$this->_db->execute();
		
		$this->setState('com_rseventspro.file.id',$id);
		$this->setState('com_rseventspro.file.name',$jinput->getString('name'));
		
		return true;
	}
	
	// Remove file
	public function removefile() {
		jimport('joomla.filesystem.file');
		
		$id = $this->_app->input->getInt('id');
		$query = $this->_db->getQuery(true);
		
		$query->clear()
			->select($this->_db->qn('location'))
			->from($this->_db->qn('#__rseventspro_files'))
			->where($this->_db->qn('id').' = '.$id);
		
		$this->_db->setQuery($query);
		if ($file = $this->_db->loadResult()) {
			$thefile = JPATH_SITE.'/components/com_rseventspro/assets/images/files/'.$file;
			if (JFile::exists($thefile)) {
				if (JFile::delete($thefile)) {
					$query->clear()
						->delete()
						->from($this->_db->qn('#__rseventspro_files'))
						->where($this->_db->qn('id').' = '.$id);
					
					$this->_db->setQuery($query);
					$this->_db->execute();
					
					return true;
				}
			}
		}
		
		return false;
	}
	
	// Get icon details
	public function getIcon() {
		$id = $this->_app->input->getInt('id');
		$query = $this->_db->getQuery(true);
		
		$query->clear()
			->select($this->_db->qn('icon'))
			->from($this->_db->qn('#__rseventspro_events'))
			->where($this->_db->qn('id').' = '.$id);
		
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	
	// Delete event icon
	public function deleteicon() {
		jimport('joomla.filesystem.file');
		
		$id = $this->_app->input->getInt('id');
		$query = $this->_db->getQuery(true);
		
		$query->clear()
			->select($this->_db->qn('icon'))
			->from($this->_db->qn('#__rseventspro_events'))
			->where($this->_db->qn('id').' = '.$id);
		
		$this->_db->setQuery($query);
		if ($icon = $this->_db->loadResult()) {
			if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$icon))
				JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$icon);
				
			if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/e_'.$icon))
				JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/e_'.$icon);
			
			if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/s_'.$icon))
				JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/s_'.$icon);
			
			if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/b_'.$icon))
				JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/b_'.$icon);
			
			$query->clear()
				->update($this->_db->qn('#__rseventspro_events'))
				->set($this->_db->qn('icon').' = '.$this->_db->q(''))
				->set($this->_db->qn('properties').' = '.$this->_db->q(''))
				->where($this->_db->qn('id').' = '.$id);
			
			$this->_db->setQuery($query);
			$this->_db->execute();
		}
		return true;
	}
	
	// Upload event icon
	public function upload() {
		jimport('joomla.filesystem.file');
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/phpthumb/phpthumb.class.php';
		
		$icon	= $this->_app->input->files->get('icon',array(),'array');
		$path	= JPATH_SITE.'/components/com_rseventspro/assets/images/events/';
		$id		= $this->_app->input->getInt('id');
		$query	= $this->_db->getQuery(true);
		
		if (!empty($icon)) {
			$ext = JFile::getExt($icon['name']);
			if (in_array(strtolower($ext),array('jpg','png','jpeg'))) {
				if ($icon['error'] == 0) {
					$query->clear()
						->select($this->_db->qn('icon'))
						->from($this->_db->qn('#__rseventspro_events'))
						->where($this->_db->qn('id').' = '.$id);
					
					$this->_db->setQuery($query);
					if ($eventicon = $this->_db->loadResult()) {
						if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$eventicon))
							JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$eventicon);
						if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/e_'.$eventicon))
							JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/e_'.$eventicon);
						if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/s_'.$eventicon))
							JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/s_'.$eventicon);
						if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/b_'.$eventicon))
							JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/b_'.$eventicon);
					}
					
					$file		= JFile::makeSafe($icon['name']);
					$filename	= JFile::getName(JFile::stripExt($file));
					
					while(JFile::exists($path.$filename.'.'.$ext))
						$filename .= rand(1,999);
					
					if (JFile::upload($icon['tmp_name'],$path.$filename.'.'.$ext)) {
						$query->clear()
							->update($this->_db->qn('#__rseventspro_events'))
							->set($this->_db->qn('icon').' = '.$this->_db->q($filename.'.'.$ext))
							->set($this->_db->qn('properties').' = '.$this->_db->q(''))
							->where($this->_db->qn('id').' = '.$id);
						
						$this->_db->setQuery($query);
						$this->_db->execute();
						
						$this->setState('com_rseventspro.edit.icon', $filename.'.'.$ext);
						
						rseventsproHelper::resize($path.$filename.'.'.$ext,rseventsproHelper::getConfig('icon_big_width'),$path.'thumbs/b_'.$filename.'.'.$ext);
						rseventsproHelper::resize($path.$filename.'.'.$ext,rseventsproHelper::getConfig('icon_small_width'),$path.'thumbs/s_'.$filename.'.'.$ext);
						rseventsproHelper::resize($path.$filename.'.'.$ext,188,$path.'thumbs/e_'.$filename.'.'.$ext);
						
						$this->setState('rseventspro.icon',$filename.'.'.$ext);
						$this->setState('rseventspro.eid',$id);
						
					} else return JText::_('COM_RSEVENTSPRO_UPLOAD_ERROR',true);
				} else return JText::_('COM_RSEVENTSPRO_FILE_ERROR',true);
			} else return JText::_('COM_RSEVENTSPRO_WRONG_FILE_TYPE',true);
		} else return JText::_('COM_RSEVENTSPRO_NO_FILE_SELECTED',true);
		
		return true;
	}
	
	// Get image properties
	public function getProperties($public = true) {
		$id = $this->_app->input->getInt('id');
		$query = $this->_db->getQuery(true);
		
		$query->clear()
			->select($this->_db->qn('properties'))
			->from($this->_db->qn('#__rseventspro_events'))
			->where($this->_db->qn('id').' = '.$id);
		
		$this->_db->setQuery($query);
		if ($properties = $this->_db->loadResult()) {
			$registry = new JRegistry;
			$registry->loadString($properties);
			return $registry->toArray();
		}
		
		return false;
	}
	
	// Crop event image
	public function crop() {
		$id		= $this->_app->input->getInt('id');
		$query	= $this->_db->getQuery(true);
		$path	= JPATH_SITE.'/components/com_rseventspro/assets/images/events/';
		
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/phpthumb/phpthumb.class.php';
		
		$query->clear()
			->select($this->_db->qn('icon'))
			->from($this->_db->qn('#__rseventspro_events'))
			->where($this->_db->qn('id').' = '.$id);
		
		$this->_db->setQuery($query);
		$icon = $this->_db->loadResult();
		
		$this->setState('rseventspro.crop.icon', $icon);
		
		$left	= $this->_app->input->getInt('left');
		$top	= $this->_app->input->getInt('top');
		$width	= $this->_app->input->getInt('width');
		$height	= $this->_app->input->getInt('height');
		
		$properties = array('left' => $left, 'top' => $top, 'width' => $width, 'height' => $height);
		$registry = new JRegistry;
		$registry->loadArray($properties);
		$properties = $registry->toString();
		
		$query->clear()
			->update($this->_db->qn('#__rseventspro_events'))
			->set($this->_db->qn('properties').' = '.$this->_db->q($properties))
			->where($this->_db->qn('id').' = '.$id);
		
		$this->_db->setQuery($query);
		$this->_db->execute();
		
		rseventsproHelper::crop($path.$icon,rseventsproHelper::getConfig('icon_big_width'),$path.'thumbs/b_'.$icon);
		rseventsproHelper::crop($path.$icon,rseventsproHelper::getConfig('icon_small_width'),$path.'thumbs/s_'.$icon);
		rseventsproHelper::crop($path.$icon,188,$path.'thumbs/e_'.$icon);
		return true;
	}
	
	// Get event guests
	public function getGuests() {
		$id		= $this->_app->input->getInt('id');
		$query	= $this->_db->getQuery(true);
		$return	= array();
		
		$query->clear()
			->select('DISTINCT(email)')->select($this->_db->qn('idu'))->select($this->_db->qn('name'))
			->from($this->_db->qn('#__rseventspro_users'))
			->where($this->_db->qn('ide').' = '.$id)
			->where($this->_db->qn('state').' IN (0,1)');
		
		$this->_db->setQuery($query);
		if ($guests = $this->_db->loadObjectList()) {
			foreach ($guests as $guest) {
				$object = new stdClass();
				$object->name	= !empty($guest->idu) ? rseventsproHelper::getUser($guest->idu,'guest') : $guest->name;
				$object->url	= !empty($guest->idu) ? rseventsproHelper::getProfile('guests', $guest->idu) : '';
				$object->avatar = rseventsproHelper::getAvatar($guest->idu,$guest->email);
				$return[] = $object;
			}
		}
		
		return $return;
	}
	
	// Get card details
	public function getCard() {
		$id		= $this->_app->input->getInt('id');
		
		return  rseventsproHelper::getCardDetails($id);
	}
	
	// Save event
	public function save() {
		$lang	= JFactory::getLanguage();
		$data	= $this->_app->input->get('jform', array(), 'array');
		$new	= $this->_app->input->getInt('new',0);
		$admin	= rseventsproHelper::admin();
		$query	= $this->_db->getQuery(true);
		
		if (!empty($this->permissions['event_moderation']) && $new && !$admin) 
			$data['published'] = 0;
		
		jimport( 'joomla.application.component.modeladmin' );
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_rseventspro/models');
		$model = JModelLegacy::getInstance('Event','rseventsproModel',  array('ignore_request' => true));
		
		if ($model->save($data)) {
			$this->setState('eventid', $model->getState('event.id'));
			$this->setState('eventname', $model->getState('event.name'));
			
			$query->clear()
				->select($this->_db->qn('owner'))
				->from($this->_db->qn('#__rseventspro_events'))
				->where($this->_db->qn('id').' = '.(int) $model->getState('event.id'));
			
			$this->_db->setQuery($query);
			$owner = (int) $this->_db->loadResult();
			
			if ((!empty($this->permissions['event_moderation']) && !$admin) && $owner == JFactory::getUser()->get('id')) {
				$query->clear()
					->select($this->_db->qn('completed'))->select($this->_db->qn('approved'))
					->from($this->_db->qn('#__rseventspro_events'))
					->where($this->_db->qn('id').' = '.(int) $model->getState('event.id'));
					
				$this->_db->setQuery($query);
				$event = $this->_db->loadObject();
				
				if ($event->completed && !$event->approved) {
					$emails = rseventsproHelper::getConfig('event_moderation_emails');
					$emails = !empty($emails) ? explode(',',$emails) : '';
					
					if (!empty($emails))
						foreach ($emails as $email)
							rseventsproEmails::moderation(trim($email), $model->getState('event.id'), $lang->getTag());
							
					$query->clear()
						->update($this->_db->qn('#__rseventspro_events'))
						->set($this->_db->qn('published').' = 0')
						->set($this->_db->qn('approved').' = 1')
						->where($this->_db->qn('id').' = '.(int) $model->getState('event.id'));
				
					$this->_db->setQuery($query);
					$this->_db->execute();
				}
			}
			
			return true;
		}
		return false;
	}
	
	/**
	 * Method to get save tickets configuration
	 *
	 * @return	array
	 */
	public function tickets() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$input		= JFactory::getApplication()->input;
		$params		= $input->get('params',array(),'array');
		
		if (!empty($params)) {
			foreach ($params as $i => $param) {
				$registry = new JRegistry;
				$registry->loadArray($param);
				$position = $registry->toString();
				
				$query->clear()
					->update($db->qn('#__rseventspro_tickets'))
					->set($db->qn('position').' = '.$db->q($position))
					->where($db->qn('id').' = '.(int) $i);
				
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
	
	/**
	 * Method to save the report
	 *
	 * @return	void
	 */
	public function report() {
		$db					= JFactory::getDbo();
		$query				= $db->getQuery(true);
		$jform				= JFactory::getApplication()->input->get('jform',array(),'array');
		$lang				= JFactory::getLanguage();
		$user				= JFactory::getUser();
		$config				= rseventsproHelper::getConfig();
		$additional_data	= array();
		$to					= array();
		
		$query->clear()
			->insert($db->qn('#__rseventspro_reports'))
			->set($db->qn('ide').' = '.(int) $jform['id'])
			->set($db->qn('idu').' = '.(int) $user->get('id'))
			->set($db->qn('ip').' = '.$db->q($_SERVER['REMOTE_ADDR']))
			->set($db->qn('text').' = '.$db->q($jform['report']));
		$db->setQuery($query);
		$db->execute();
		
		$additional_data = array(
				'{ReportUser}' => $user->get('guest') ? JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST') : $user->get('name'),
				'{ReportIP}' => $_SERVER['REMOTE_ADDR'],
				'{ReportMessage}' => $jform['report']
			);
		
		if ($config->report_to_owner) {
			$query->clear()
				->select($db->qn('u.email'))
				->from($db->qn('#__users','u'))
				->join('left',$db->qn('#__rseventspro_events','e').' ON '.$db->qn('u.id').' = '.$db->qn('e.owner'))
				->where($db->qn('e.id').' = '.(int) $jform['id']);
			$db->setQuery($query);
			if ($email = $db->loadResult()) {
				$to = array_merge($to,(array) $email);
			}
		}
		
		if ($config->report_to) {
			$report_to = explode(',',$config->report_to);
			$to = array_merge($to,$report_to);
		}
		
		// Send email
		rseventsproEmails::report($to, (int) $jform['id'], $additional_data, $lang->getTag());
	}
	
	// Can we report events ?
	public function getCanreport() {
		$config = rseventsproHelper::getConfig();
		$user	= JFactory::getUser();
		
		if ($config->reports) {
			if ($user->get('guest')) {
				if ($config->reports_guests)
					return true;
				else
					return false;
			}
			
			return true;
		}
		
		return false;
	}
	/**
	 * Method to delete the reports.
	 */
	public function deletereports($pks) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->delete()
			->from($db->qn('#__rseventspro_reports'))
			->where($db->qn('id').' IN ('.implode(',',$pks).')');
		$db->setQuery($query);
		$db->execute();
	}
}