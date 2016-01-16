<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.application.component.view');

class rseventsproViewEvents extends JViewLegacy
{
	protected $sidebar;
	
	public function display($tpl = null) {
		$this->layout			= $this->getLayout();
		
		if ($this->layout == 'items') {
			$jinput					= JFactory::getApplication()->input;
			$type					= $jinput->get('type');
			$this->total			= $jinput->getInt('total',0);
			
			if ($type == 'past') {
				$this->data = $this->get('pastevents');
			} elseif ($type == 'ongoing') {
				$this->data = $this->get('ongoingevents');
			} elseif ($type == 'thisweek') {
				$this->get('ongoingevents');
				$this->data = $this->get('thisweekevents');
			} elseif ($type == 'thismonth') {
				$this->get('ongoingevents');
				$this->get('thisweekevents');
				$this->data = $this->get('thismonthevents');
			} elseif ($type == 'nextmonth') {
				$this->get('ongoingevents');
				$this->get('thisweekevents');
				$this->get('thismonthevents');
				$this->data = $this->get('nextmonthevents');
			} elseif ($type == 'upcoming') {
				$this->data = $this->get('upcomingevents');
			} else {
				$this->data = array();
			}
		} elseif ($this->layout == 'forms') {
			$this->forms			= $this->get('Forms');
			$this->fpagination		= $this->get('FormsPagination');
			$this->eventID			= JFactory::getApplication()->input->getInt('id');
		} elseif ($this->layout == 'report') {
			$this->sidebar			= $this->get('Sidebar');
			$this->reports			= rseventsproHelper::getReports(JFactory::getApplication()->input->getInt('id'));
			
			$this->addToolBarReport();
		} else {
			$this->sidebar			= $this->get('Sidebar');
			
			$this->past				= $this->get('pastevents');
			$this->ongoing			= $this->get('ongoingevents');
			$this->thisweek			= $this->get('thisweekevents');
			$this->thismonth		= $this->get('thismonthevents');
			$this->nextmonth		= $this->get('nextmonthevents');
			$this->upcoming			= $this->get('upcomingevents');
			
			$this->total_past		= $this->get('pasttotal');
			$this->total_ongoing	= $this->get('ongoingtotal');
			$this->total_thisweek	= $this->get('thisweektotal');
			$this->total_thismonth	= $this->get('thismonthtotal');
			$this->total_nextmonth	= $this->get('nextmonthtotal');
			$this->total_upcoming	= $this->get('upcomingtotal');
			
			
			$this->sortColumn		= $this->get('sortColumn');
			$this->sortOrder		= $this->get('sortOrder');
			
			$filters				= $this->get('filters');
			$this->columns			= $filters[0];
			$this->operators		= $filters[1];
			$this->values			= $filters[2];
			$this->other			= $this->get('OtherFilters');
			
			$this->addToolBar();
		}
		
		$this->pagination = $this->get('pagination');
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_LIST_EVENTS'),'rseventspro48');
		JToolBarHelper::addNew('event.add');
		JToolBarHelper::editList('event.edit');
		JToolBarHelper::custom('preview','preview','preview',JText::_('COM_RSEVENTSPRO_PREVIEW_EVENT'));
		JToolBarHelper::divider();
		JToolBarHelper::deleteList(JText::_('COM_RSEVENTSPRO_REMOVE_EVENTS'),'events.delete');
		JToolBarHelper::custom('events.copy', 'copy.png', 'copy_f2.png', 'COM_RSEVENTSPRO_COPY_EVENT' );
		JToolBarHelper::archiveList('events.archive');
		JToolBarHelper::publishList('events.publish');
		JToolBarHelper::unpublishList('events.unpublish');
		JToolbarHelper::custom('events.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
		JToolBarHelper::custom('events.exportical','export','export',JText::_('COM_RSEVENTSPRO_EXPORT_ICAL'));
		JToolBarHelper::custom('events.exportcsv','export','export',JText::_('COM_RSEVENTSPRO_EXPORT_CSV'));
		JToolBarHelper::divider();
		JToolBarHelper::custom('events.rating','trash','trash',JText::_('COM_RSEVENTSPRO_CLEAR_RATING'));
		JToolBarHelper::divider();
		JToolBarHelper::custom('rseventspro','rseventspro32','rseventspro32',JText::_('COM_RSEVENTSPRO_GLOBAL_NAME'),false);
		
		JFactory::getDocument()->addScript(JURI::root().'components/com_rseventspro/assets/js/dom.js');
		JFactory::getDocument()->addScript(JURI::root().'components/com_rseventspro/assets/js/select.js');
		
		JFactory::getDocument()->addCustomTag('<!--[if IE 7]>
				<style type="text/css">
					.elSelect { width: 200px; }
					.optionsContainer { margin-top: -30px; }
					.selectedOption { width: 165px !important; }
				</style>
				<![endif]-->');
	}
	
	protected function addToolBarReport() {
		JToolBarHelper::title(JText::sprintf('COM_RSEVENTSPRO_REPORTS_FOR', @$this->reports['name']),'rseventspro48');
		JToolBarHelper::deleteList('','events.deletereports');
		JToolBarHelper::custom('back','back','back',JText::_('COM_RSEVENTSPRO_GLOBAL_BACK_BTN'),false);
	}
	
	protected function getDetails($id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('e.id'))->select($db->qn('e.name'))->select($db->qn('e.start'))->select($db->qn('e.end'))
			->select($db->qn('e.parent'))->select($db->qn('e.icon'))->select($db->qn('e.published'))->select($db->qn('e.owner'))->select($db->qn('e.featured'))
			->select($db->qn('e.completed'))->select($db->qn('l.id','lid'))->select($db->qn('l.name','lname'))->select($db->qn('u.name','uname'))->select($db->qn('e.allday'))
			->from($db->qn('#__rseventspro_events','e'))
			->join('left', $db->qn('#__rseventspro_locations','l').' ON '.$db->qn('e.location').' = '.$db->qn('l.id'))
			->join('left', $db->qn('#__users','u').' ON '.$db->qn('u.id').' = '.$db->qn('e.owner'))
			->where($db->qn('e.id').' = '.(int) $id);
		
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	protected function getTickets($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$array	= array();
		
		$query->clear()
			->select($db->qn('id'))->select($db->qn('name'))->select($db->qn('seats'))
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query,0,3);
		$tickets = $db->loadObjectList();
		
		if (!empty($tickets)) {
			foreach ($tickets as $ticket) {
				$query->clear()
					->select('SUM(ut.quantity)')
					->from($db->qn('#__rseventspro_user_tickets','ut'))
					->join('left', $db->qn('#__rseventspro_users','u').' ON '.$db->qn('u.id').' = '.$db->qn('ut.ids'))
					->where($db->qn('u.state').' IN (0,1)')
					->where($db->qn('ut.idt').' = '.(int) $ticket->id);
				
				$db->setQuery($query);
				$purchased = $db->loadResult();
				
				if ($ticket->seats == 0) {
					$array[] = JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED').' '.'<em>'.$ticket->name.'</em>';
				} else {
					$available = $ticket->seats - $purchased;
					if ($available <= 0) continue;
					$array[] = $available. ' x '. '<em>'.$ticket->name.'</em>';
				}
			}
		}
		
		return !empty($array) ? implode(' , ',$array) : '';
	}
	
	protected function getSubscribers($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rseventspro_users'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		return (int) $db->loadResult();
	}
}