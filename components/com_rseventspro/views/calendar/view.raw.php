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
		$layout = $this->getLayout();
		$jinput	= JFactory::getApplication()->input;
		$this->config	= rseventsproHelper::getConfig();
		
		if ($layout == 'module') {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/calendar.php';
			require_once JPATH_SITE.'/modules/mod_rseventspro_calendar/helper.php';
			
			$month	= $jinput->getInt('month');
			$year	= $jinput->getInt('year');
			$module	= $jinput->getInt('mid');
			$params = $this->get('ModuleParams');
			
			// Get events
			$events = modRseventsProCalendar::getEvents($params);
			
			if (!$params->get('full',1)) {
				foreach ($events as $i => $event)
					if (rseventsproHelper::eventisfull($event->id)) unset($events[$i]);
			}
			
			$calendar = new RSEPROCalendar($events,$params,true);
			$calendar->class_suffix = $params->get('moduleclass_sfx','');
			$calendar->setDate($month, $year);
			
			$itemid = $params->get('itemid');
			$itemid = !empty($itemid) ? $itemid : RseventsproHelperRoute::getCalendarItemid();
			
			$this->calendar	= $calendar;
			$this->itemid	= $itemid;
			$this->module	= $module;
		} else {
			$jinput->set('limitstart', $jinput->getInt('limitstart'));
			
			$this->user			= JFactory::getUser()->get('id');
			$this->admin		= rseventsproHelper::admin();
			$this->params		= rseventsproHelper::getParams();
			$this->permissions	= rseventsproHelper::permissions();
			
			// Get events
			$events = $this->get('Events');
			
			if (!$this->params->get('full',1)) {
				foreach ($events as $i => $event) {
					if (rseventsproHelper::eventisfull($event->id)) {
						unset($events[$i]);
					}
				}
			}
			
			$this->events = $events;
		}
		
		parent::display($tpl);
	}
}