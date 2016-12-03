<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE.'/components/com_rseventspro/helpers/ical/iCalcreator.class.php';

class RSEventsProiCal {
	
	protected static $ids = array();
	protected static $events = array();
	protected static $instances = array();
	
	public function __construct($ids) {
		JArrayHelper::toInteger($ids);
		
		self::$ids = $ids;
	}
	
	public static function getInstance($ids) {
		$hash = serialize($ids);
		
		if (!isset(self::$instances[$hash])) {
			self::$instances[$hash] = new RSEventsProiCal($ids);
		}
		
		return self::$instances[$hash];
	}
	
	public static function toIcal() {
		// Load events
		self::loadEvents();
		
		// Get the filename
		$filename	= self::getFilename();
		
		// Create a new instance of the ical calendar
		$v = new vcalendar(array('unique_id' => JURI::root(), 'filename' => $filename.'.ics'));
		$v->setProperty('method', 'PUBLISH');
		
		$base = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		
		if (!empty(self::$events)) {
			foreach (self::$events as $event) {
				$url = $base.rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id));
				$url = str_replace('/administrator/', '/', $url);
				
				$description = strip_tags($event->description);
				$description = str_replace("\n",'',$description);
				$description .= ' '.$url;
				
				$start	= JFactory::getDate($event->start);
				$end	= JFactory::getDate($event->end);
				$vevent = $v->newComponent('vevent');
				
				if ($event->allday) {
					$vevent->setProperty('dtstart', rseventsproHelper::showdate($event->start,'Ymd'), array("VALUE" => "DATE"));
				} else {
					$vevent->setProperty('dtstart', array($start->format('Y'), $start->format('m'), $start->format('d'), $start->format('H'), $start->format('i'), $start->format('s'), 'tz' => 'Z'));
					$vevent->setProperty('dtend', array($end->format('Y'), $end->format('m'), $end->format('d'), $end->format('H'), $end->format('i'), $end->format('s'), 'tz' => 'Z'));
				}
				
				$vevent->setProperty('LOCATION', $event->locationname. ' (' .$event->address . ')' );
				$vevent->setProperty('summary', $event->name ); 
				$vevent->setProperty('description', $description);
				$vevent->setProperty('URL', $url);
			}
			
			$v->returnCalendar();
		}
		
		JFactory::getApplication()->close();
	}
	
	protected static function loadEvents() {
		if ($ids = self::$ids) {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			$query->clear()
				->select($db->qn('e.id'))->select($db->qn('e.name'))->select($db->qn('e.start'))
				->select($db->qn('e.end'))->select($db->qn('e.description'))
				->select($db->qn('l.name','locationname'))->select($db->qn('l.address'))->select($db->qn('e.allday'))
				->from($db->qn('#__rseventspro_events','e'))
				->join('left', $db->qn('#__rseventspro_locations','l').' ON '.$db->qn('l.id').' = '.$db->qn('e.location'))
				->where($db->qn('e.id').' IN ('.implode(',',$ids).')');
			$db->setQuery($query);
			self::$events = $db->loadObjectList();
		}
	}
	
	protected static function getFilename() {
		if (count(self::$ids) > 1) {
			return 'Events';
		} elseif (count(self::$ids) == 1) {
			if (isset(self::$events[0])) {
				return self::$events[0]->name;
			}
		} else {
			return 'Event';
		}
	}
}