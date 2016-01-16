<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.application.component.view');

class rseventsproViewCalendar extends JViewLegacy
{
	public function display($tpl = null) {
		JHTML::_('behavior.modal');
		
		$doc		= JFactory::getDocument();
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$layout		= $this->getLayout();
		$pathway	= $app->getPathWay();
		$menus		= $app->getMenu();
		$menu		= $menus->getActive();
		
		// Get menu parameters , user permission etc.
		$this->user			= $user->get('id');
		$this->admin		= rseventsproHelper::admin();
		$this->params		= rseventsproHelper::getParams();
		$this->permissions	= rseventsproHelper::permissions();
		$this->config		= rseventsproHelper::getConfig();
		
		// Add Joomla! menu metadata
		if ($this->params->get('menu-meta_description'))
			$doc->setDescription($this->params->get('menu-meta_description'));

		if ($this->params->get('menu-meta_keywords'))
			$doc->setMetadata('keywords', $this->params->get('menu-meta_keywords'));

		if ($this->params->get('robots'))
			$doc->setMetadata('robots', $this->params->get('robots'));
		
		// Add custom scripts
		$doc->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/scripts.js');
		$doc->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/dom.js');
		$doc->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/css/calendar.css');
		
		// Get events
		$events = $this->get('Events');
		
		// If the option to not show full events is enabled , then remove them from our events list
		if (!$this->params->get('full',1)) {
			foreach ($events as $i => $event) {
				if (rseventsproHelper::eventisfull($event->id)) {
					unset($events[$i]);
				}
			}
		}
		
		$this->events	= $events;
		$this->total	= $this->get('total');
		
		if ($layout == 'default') {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/calendar.php';
			$lists		= array();
			$doc->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/FloatingTips.js');
			
			// Get colors
			$this->legend = $this->get('colors');
			
			if (!empty($this->legend)) {
				$doc->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/fancyselect/fancyselect.js?v='.RSEPRO_RS_REVISION);
				$doc->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/js/fancyselect/fancyselect.css?v='.RSEPRO_RS_REVISION);
				
				$this->selected = $this->get('selected');
			}
			
			// Add search bar
			if ($this->params->get('search',1)) {
				$doc->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/select.js');
				$doc->addCustomTag('<!--[if IE 7]>
				<style type="text/css">
					.elSelect { width: 200px; }
					.optionsContainer { margin-top: -30px; }
					.selectedOption { width: 165px !important; }
				</style>
				<![endif]-->');
			
				
				$lists['filter_from']		= JHTML::_('select.genericlist', $this->get('FilterOptions'), 'filter_from[]', 'size="1"','value','text');
				$lists['filter_condition']	= JHTML::_('select.genericlist', $this->get('FilterConditions'), 'filter_condition[]', 'size="1"','value','text');
				
				$filters			= $this->get('filters');
				$this->columns		= $filters[0];
				$this->operators	= $filters[1];
				$this->values		= $filters[2];
			}
			
			//set the pathway
			if (!$menu) 
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_CALENDAR'));
			
			$cmonth	= $app->input->getInt('month', '0');
			$cyear	= $app->input->getInt('year', '0');
			
			// Get a new instance of the calendar
			$calendar = new RSEPROCalendar($this->events,$this->params);
			
			if ($cmonth && $cyear)
				$calendar->setDate($cmonth, $cyear);
			
			$this->calendar = $calendar;
			$months = array();
			$years = array();
			
			if (!empty($this->calendar->months))
			foreach ($this->calendar->months as $i => $month) 
				$months[] = JHTML::_('select.option', $i, $month);
			
			for($j = 2008; $j <= 2023; $j++)
				$years[] = JHTML::_('select.option', $j, $j);
			
			$lists['month'] = JHTML::_('select.genericlist', $months, 'month', 'size="1" class="rs_cal_select" onchange="document.adminForm.submit()"','value','text',$this->calendar->cmonth);
			$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'size="1" class="rs_cal_select" onchange="document.adminForm.submit()"','value','text',$this->calendar->cyear);
			
			$this->lists  = $lists ;
		} elseif ($layout == 'day') {
			$date = $app->input->getString('date');
			$date = str_replace(array('-',':'),'/',$date);
			list($m,$d,$y) = explode('/',$date,3);
			
			$start = rseventsproHelper::date($y.'-'.$m.'-'.$d.' 00:00:00',null,false,true);
			$start->setTZByID($start->getTZID());
			$start->convertTZ(new RSDate_Timezone('GMT'));
			
			$this->date = rseventsproHelper::translatedate($start->formatLikeDate(rseventsproHelper::getConfig('global_date')));
		} elseif ($layout == 'week') {
			$date = $app->input->getString('date');
			$date	= str_replace(array('-',':'),'/',$date);
			list($m,$d,$y) = explode('/',$date,3);
			
			$start = rseventsproHelper::date($y.'-'.$m.'-'.$d.' 00:00:00',null,false,true);
			$start->setTZByID($start->getTZID());
			$start->convertTZ(new RSDate_Timezone('GMT'));
			$from = $start->formatLikeDate(rseventsproHelper::getConfig('global_date'));
			$start->addDays(6);
			$to	  = $start->formatLikeDate(rseventsproHelper::getConfig('global_date'));
			
			$this->from	= rseventsproHelper::translatedate($from);
			$this->to	= rseventsproHelper::translatedate($to);
		}
		
		parent::display($tpl);
	}
	
	public function getColour($id) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$excluded	= rseventsproHelper::excludeEvents();
		
		static $cache = array();
		if (empty($cache)) {
			$query->clear()
				->select($db->qn('t.ide'))->select($db->qn('c.params'))
				->from($db->qn('#__categories','c'))
				->join('left', $db->qn('#__rseventspro_taxonomy','t').' ON '.$db->qn('t.id').' = '.$db->qn('c.id'))
				->where($db->qn('t.type').' = '.$db->q('category'))
				->where($db->qn('c.extension').' = '.$db->q('com_rseventspro'));
			
			if (JLanguageMultilang::isEnabled()) {
				$query->where('c.language IN ('.$db->q(JFactory::getLanguage()->getTag()).','.$db->q('*').')');
			}
			
			$user	= JFactory::getUser();
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('c.access IN ('.$groups.')');
			
			if ($excluded) {
				$query->where($db->qn('t.ide').' NOT IN ('.implode(',',$excluded).')');
			}
			
			$db->setQuery($query);
			$cache = $db->loadObjectList('ide');
			
			if (!empty($cache)) {
				foreach ($cache as $ide => $object) {
					$registry = new JRegistry;
					$registry->loadString($object->params);
					$cache[$ide]->color = $registry->get('color','');
				}
			}
		}
		
		return isset($cache[$id]) ? $cache[$id]->color : '';
	}
	
	public function getDetailsBig($event) {
		$details = $this->escape($event->name).'<br />';
		
		if ($event->allday) {
			$details .= '<b>'.JText::_('COM_RSEVENTSPRO_GLOBAL_ON').'</b> '.rseventsproHelper::date($event->start,rseventsproHelper::getConfig('global_date'),true).'<br />';
		} else {
			$details .= '<b>'.JText::_('COM_RSEVENTSPRO_CALENDAR_FROM').'</b> '.rseventsproHelper::date($event->start,null,true).'<br />';
			$details .= '<b>'.JText::_('COM_RSEVENTSPRO_CALENDAR_TO').'</b> '.rseventsproHelper::date($event->end,null,true).'<br />';
		}
		
		return $details;
	}
	
	public function getDetailsSmall($ids) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$details	= '';
		
		if (!empty($ids)) {
			JArrayHelper::toInteger($ids);
			$query->clear()
				->select($db->qn('name'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' IN ('.implode(',',$ids).')');
			
			$db->setQuery($query);
			$eventnames = $db->loadColumn();
			$details .= $this->escape(implode('<br />',$eventnames));
		} else {
			$details = JText::_('COM_RSEVENTSPRO_GLOBAL_NO_EVENTS').'<br />'.JText::_('COM_RSEVENTSPRO_GLOBAL_NO_EVENTS');
		}
		
		return $details;
	}
}