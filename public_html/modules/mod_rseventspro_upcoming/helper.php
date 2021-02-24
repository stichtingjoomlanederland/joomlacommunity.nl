<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class modRseventsProUpcoming {

	public static function getEvents($params) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$subquery	= $db->getQuery(true);
		$categories	= $params->get('categories','');
		$locations	= $params->get('locations','');
		$speakers	= $params->get('speakers','');
		$sponsors	= $params->get('sponsors','');
		$tags		= $params->get('tags','');
		$order		= $params->get('ordering','start');
		$direction	= $params->get('order','DESC');
		$events		= (int) $params->get('events',0);
		$archived	= (int) $params->get('archived',0);
		$repeating	= (int) $params->get('repeating',0);
		$canceled	= (int) $params->get('canceled',1);
		$limit		= (int) $params->get('limit',4);
		$full		= (int) $params->get('full',1);
		$state		= $canceled ? ',3' : '';
		
		$todayDate = JFactory::getDate();
		$todayDate->setTimezone(new DateTimeZone(rseventsproHelper::getTimezone()));
		$todayDate->setTime(0,0,0);
		$today = $todayDate->toSql();
		$todayDate->modify('+1 days');
		$tomorrow = $todayDate->toSql();
		
		$query->clear()
			->select($db->qn('e.id'))
			->from($db->qn('#__rseventspro_events','e'))
			->where($db->qn('e.completed').' = 1');
		
		if ($archived)
			$query->where($db->qn('e.published').' IN (1,2'.$state.')');
		else 
			$query->where($db->qn('e.published').' IN (1'.$state.')');
		
		if (!$repeating)
			$query->where($db->qn('e.parent').' = 0');
		
		$AllDayEvents = modRseventsProUpcoming::_getAllDayEvents();
		
		if ($AllDayEvents) {
			$active_today = '(((('.$db->qn('e.start').' <= '.$db->q($today).' AND '.$db->qn('e.end').' >= '.$db->q($today).') OR ('.$db->qn('e.start').' >= '.$db->q($today).' AND '.$db->qn('e.start').' < '.$db->q($tomorrow).')) AND '.$db->qn('e.end').' <> '.$db->q($db->getNullDate()).') OR '.$db->qn('e.id').' IN ('.implode(',',$AllDayEvents).'))';
		} else {
			$active_today = '(('.$db->qn('e.start').' <= '.$db->q($today).' AND '.$db->qn('e.end').' >= '.$db->q($today).') OR ('.$db->qn('e.start').' >= '.$db->q($today).' AND '.$db->qn('e.start').' < '.$db->q($tomorrow).'))';
		}
		
		$upcoming		= $db->qn('e.start').' >= '.$db->q(JFactory::getDate()->toSql());
		
		if ($events == 0) // active today + upcoming
			$query->where('('.$active_today.' OR ('.$upcoming.'))');
		elseif ($events == 2) // upcoming
			$query->where($upcoming);
		elseif ($events == 1) // active today
			$query->where($active_today);
		
		if (!empty($categories)) {
			$categories = array_map('intval',$categories);
			
			$subquery->clear()
				->select($db->qn('tx.ide'))
				->from($db->qn('#__rseventspro_taxonomy','tx'))
				->join('left', $db->qn('#__categories','c').' ON '.$db->qn('c.id').' = '.$db->qn('tx.id'))
				->where($db->qn('c.id').' IN ('.implode(',',$categories).')')
				->where($db->qn('tx.type').' = '.$db->q('category'))
				->where($db->qn('c.extension').' = '.$db->q('com_rseventspro'));
			
			if (JLanguageMultilang::isEnabled()) {
				$subquery->where('c.language IN ('.$db->q(JFactory::getLanguage()->getTag()).','.$db->q('*').')');
			}
			
			$user	= JFactory::getUser();
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$subquery->where('c.access IN ('.$groups.')');
			
			$query->where($db->qn('e.id').' IN ('.$subquery.')');
		}
		
		if (!empty($tags)) {
			$tags = modRseventsProUpcoming::getTagsIds($tags);
			$tags = array_map('intval',$tags);
			
			$subquery->clear()
				->select($db->qn('tx.ide'))
				->from($db->qn('#__rseventspro_taxonomy','tx'))
				->join('left', $db->qn('#__rseventspro_tags','t').' ON '.$db->qn('t.id').' = '.$db->qn('tx.id'))
				->where($db->qn('t.id').' IN ('.implode(',',$tags).')')
				->where($db->qn('tx.type').' = '.$db->q('tag'));
			
			$query->where($db->qn('e.id').' IN ('.$subquery.')');
		}
		
		if (!empty($locations)) {
			$locations = array_map('intval',$locations);
			
			$query->where($db->qn('e.location').' IN ('.implode(',',$locations).')');
		}
		
		if (!empty($speakers)) {
			$speakers = array_map('intval',$speakers);
			
			$subquery->clear()
				->select($db->qn('tx.ide'))
				->from($db->qn('#__rseventspro_taxonomy','tx'))
				->join('left', $db->qn('#__rseventspro_speakers','s').' ON '.$db->qn('s.id').' = '.$db->qn('tx.id'))
				->where($db->qn('s.id').' IN ('.implode(',',$speakers).')')
				->where($db->qn('tx.type').' = '.$db->q('speaker'));
			
			$query->where($db->qn('e.id').' IN ('.$subquery.')');
		}
		
		if (!empty($sponsors)) {
			$sponsors = array_map('intval',$sponsors);
			
			$subquery->clear()
				->select($db->qn('tx.ide'))
				->from($db->qn('#__rseventspro_taxonomy','tx'))
				->join('left', $db->qn('#__rseventspro_sponsors','s').' ON '.$db->qn('s.id').' = '.$db->qn('tx.id'))
				->where($db->qn('s.id').' IN ('.implode(',',$sponsors).')')
				->where($db->qn('tx.type').' = '.$db->q('sponsor'));
			
			$query->where($db->qn('e.id').' IN ('.$subquery.')');
		}
		
		$exclude = modRseventsProUpcoming::excludeEvents();
		
		if (!empty($exclude))
			$query->where($db->qn('e.id').' NOT IN ('.implode(',',$exclude).')');
		
		$query->order($db->qn('e.'.$order).' '.$db->escape($direction));
		
		
		if (!$full) {
			$db->setQuery($query);
			$events = $db->loadColumn();
			
			foreach ($events as $i => $event) {
				if (rseventsproHelper::eventisfull($event)) unset($events[$i]);
			}
			
			$events = array_slice($events, 0, $limit);
		} else {
			$db->setQuery($query,0,$limit);
			$events = $db->loadColumn();
		}
		
		return $events;
	}
	
	protected static function excludeEvents() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser(); 
		$ids	= array();
		
		$query->clear()
			->select($db->qn('ide'))
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('type').' = '.$db->q('groups'));
		
		$db->setQuery($query);
		$eventids = $db->loadColumn();
		
		if (!empty($eventids)) {
			foreach ($eventids as $id) {
				$query->clear()
					->select($db->qn('owner'))
					->from($db->qn('#__rseventspro_events'))
					->where($db->qn('id').' = '.(int) $id);
				
				$db->setQuery($query);
				$owner = (int) $db->loadResult();
				
				if (!rseventsproHelper::canview($id) && $owner != $user->get('id'))
					$ids[] = $id;
			}
			
			if (!empty($ids)) {
				$ids = array_map('intval',$ids);
				$ids = array_unique($ids);
			}
		}
		
		return $ids;
	}
	
	protected static function _getAllDayEvents() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$todayUTC = JFactory::getDate();
		$todayUTC->setTime(0,0,0);
		$todayUTC = $todayUTC->format('Y-m-d H:i:s');
		
		$today = JFactory::getDate();
		$today->setTimezone(new DateTimeZone(rseventsproHelper::getTimezone()));
		$today->setTime(0,0,0);
		$today = $today->format('Y-m-d H:i:s');
		
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('allday').' = 1')
			->where('('.$db->qn('start').' = '.$db->q($today).' OR '.$db->qn('start').' = '.$db->q($todayUTC).')');
		
		$db->setQuery($query);
		if ($events = $db->loadColumn()) {
			$events = array_map('intval',$events);
			return $events;
		}
		
		return false;
	}
	
	protected static function getTagsIds($tags) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rseventspro_tags'))
			->where($db->qn('name').' IN ('.rseventsproHelper::quoteImplode($tags).')');
		$db->setQuery($query);
		return $db->loadColumn();
	}
}