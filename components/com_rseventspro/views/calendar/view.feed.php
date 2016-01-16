<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewCalendar extends JViewLegacy
{	
	//Creates the Event Feed
	public function display() {
		$doc		= JFactory::getDocument();
		$jinput		= JFactory::getApplication()->input;
		$params		= rseventsproHelper::getParams();
		
		// Get events
		$rows = $this->get('Events');
		
		// If the option to not show full events is enabled , then remove them from our events list
		if (!$params->get('full',1)) {
			foreach ($rows as $i => $event) {
				if (rseventsproHelper::eventisfull($event->id)) {
					unset($rows[$i]);
				}
			}
		}
		
		foreach ($rows as $row ) {
			
			if (!rseventsproHelper::canview($row->id)) 
				continue;
			
			// Get event details
			$event = $this->getEvent($row->id);
			
			// Strip html from feed item title
			$title = $this->escape($event->name);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');		

			// Url link to event
			$link = rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($row->id,$event->name),false,rseventsproHelper::itemid($row->id));
			
			// feed item description text
			$description = JText::_('COM_RSEVENTSPRO_FEED_LOCATION').': <strong>'.$this->escape($event->locationname).'</strong> - '.$this->escape($event->address).'<br />';
			$description .= JText::_('COM_RSEVENTSPRO_FEED_DATE').': '.rseventsproHelper::showdate($event->start);
			if (!$event->allday) $description .= ' - '.rseventsproHelper::showdate($event->end);
			$description .= '<br />';
			
			if (!empty($event->description)) $description .= $event->description;
			
			@$created =  ($event->created == JFactory::getDbo()->getNullDate()) ? date( 'r', strtotime($event->start)) : date( 'r', strtotime($event->created));

			// load individual item creator class
			$item = new JFeedItem();
			$item->title 		= $title;
			$item->link 		= $link;
			$item->description 	= $description;
			$item->date			= @$created;
			
			// loads item info into rss array
			$doc->addItem( $item );
		}
	}
	
	protected function getEvent($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('e.name'))->select($db->qn('e.start'))->select($db->qn('e.end'))->select($db->qn('e.allday'))
			->select($db->qn('e.description'))->select($db->qn('e.created'))->select($db->qn('l.name','locationname'))
			->select($db->qn('l.address'))
			->from($db->qn('#__rseventspro_events','e'))
			->join('left', $db->qn('#__rseventspro_locations','l').' ON '.$db->qn('e.location').' = '.$db->qn('l.id'))
			->where($db->qn('e.id').' = '.(int) $id);
		
		$db->setQuery($query);
		return $db->loadObject();
	}
}