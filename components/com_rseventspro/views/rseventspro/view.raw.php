<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.application.component.view');

class rseventsproViewRseventspro extends JViewLegacy
{
	public function display($tpl = null) {
		$db				= JFactory::getDbo();
		$query			= $db->getQuery(true);
		$layout			= $this->getLayout();
		$jinput			= JFactory::getApplication()->input;
		$this->user		= $this->get('User');
		$this->admin	= rseventsproHelper::admin();
		$this->config	= rseventsproHelper::getConfig();
		
		if ($layout == 'ticket') {
			if ($jinput->get('from') == 'subscriber') {
				$query->clear()
					->select($db->qn('e.owner'))
					->from($db->qn('#__rseventspro_events','e'))
					->join('left', $db->qn('#__rseventspro_users','u').' ON '.$db->qn('u.ide').' = '.$db->qn('e.id'))
					->where($db->qn('u.id').' = '.$jinput->getInt('id'));
				
				$db->setQuery($query);
				$userid = (int) $db->loadResult();
			} else {
				$query->clear()
					->select($db->qn('idu'))
					->from($db->qn('#__rseventspro_users'))
					->where($db->qn('id').' = '.$jinput->getInt('id'));
				
				$db->setQuery($query);
				$userid = (int) $db->loadResult();
			}
			
			if ($this->admin || $userid == $this->user) {
				if (file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/pdf.php')) {
					require_once JPATH_SITE.'/components/com_rseventspro/helpers/pdf.php';
					JFactory::getDocument()->setMimeEncoding('application/pdf');
					$pdf = RSEventsProPDF::getInstance();
					
					if ($id = $jinput->getInt('id'))
						$this->buffer 		= $pdf->ticket($id);
				} else {
					JFactory::getApplication()->close();
				}
			} else {
				JFactory::getApplication()->close();
			}
		} else {		
			$jinput->set('limitstart',$jinput->getInt('limitstart',0));
			$tmpl = $jinput->get('tpl');
			
			if ($tmpl == 'events') {
				$this->events = $this->get('Events');
			} elseif ($tmpl == 'search') {
				$this->events = $this->get('Results');
			} elseif ($tmpl == 'locations') {
				$this->locations = $this->get('Locations');
			} elseif ($tmpl == 'categories') {
				$this->categories = $this->get('Categories');
			} elseif ($tmpl == 'subscribers') {
				$this->event = $this->get('Event');
				
				if ($this->admin || $this->event->owner == $this->user) {
					$this->subscribers  = $this->get('subscribers');
				} else {
					JFactory::getApplication()->close();
				}
			}
			
			$this->tmpl			= $tmpl;
			$this->params		= rseventsproHelper::getParams();
			$this->permissions	= rseventsproHelper::permissions();
		}
		
		parent::display($tpl);
	}
	
	public function getStatus($state) {
		if ($state == 0) {
			return '<font color="blue">'.JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_INCOMPLETE').'</font>';
		} elseif ($state == 1) {
			return '<font color="green">'.JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_COMPLETED').'</font>';
		} elseif ($state == 2) {
			return '<font color="red">'.JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_DENIED').'</font>';
		}
	}
	
	public function getUser($id) {
		if ($id > 0) {
			return JFactory::getUser($id)->get('username');
		} else return JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST');
	}
	
	public function getNumberEvents($id, $type) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$events	= 0;
		
		if ($type == 'categories') {
			$query->clear()
				->select($db->qn('e.id'))
				->from($db->qn('#__rseventspro_events','e'))
				->join('left', $db->qn('#__rseventspro_taxonomy','t').' ON '.$db->qn('e.id').' = '.$db->qn('t.ide'))
				->where($db->qn('t.type').' = '.$db->q('category'))
				->where($db->qn('t.id').' = '.(int) $id);
			
			
			$db->setQuery($query);
			$eventids = $db->loadColumn();
			
			if (!empty($eventids)) {
				foreach ($eventids as $eid) {
					if (!rseventsproHelper::canview($eid)) 
						continue;
					$events++;
				}
			}
		} else if ($type == 'locations') {
			$query->clear()
				->select($db->qn('id'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('location').' = '.(int) $id);
			
			
			$db->setQuery($query);
			$eventids = $db->loadColumn();
			
			if (!empty($eventids)) {
				foreach ($eventids as $eid) {
					if (!rseventsproHelper::canview($eid)) 
						continue;
					$events++;
				}
			}
		}
		
		if (!$events) return;
		return $events.' '.JText::plural('COM_RSEVENTSPRO_CALENDAR_EVENTS',$events);
	}
}