<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewEvents extends JViewLegacy
{	
	public function display($tpl = null) {
		$this->layout	= $this->getLayout();
		$this->app		= JFactory::getApplication();
		
		if ($this->layout == 'items') {
			$jinput					= $this->app->input;
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
			$this->eventID			= $this->app->input->getInt('id');
		} elseif ($this->layout == 'report') {
			$this->reports			= rseventsproHelper::getReports($this->app->input->getInt('id'));
			
			$this->addToolBarReport();
		} else {
			$this->tpl				= rseventsproHelper::getConfig('backendlist','int',0) ? 'general' : 'timeline';
			
			if (rseventsproHelper::checkTimezone()) {
				$this->app->enqueueMessage(JText::_('COM_RSEVENTSPRO_TIMEZONE_HAS_CHANGED'),'notice');
			}
			
			if ($this->tpl == 'general') {
				$this->events			= $this->get('events');
				$this->pagination		= $this->get('pagination');
			} else {			
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
			}
			
			$this->sortColumn		= $this->get('sortColumn');
			$this->sortColumnText	= $this->get('OrderingText');
			$this->sortOrder		= $this->get('sortOrder');
			$this->sortOrderText	= $this->get('OrderText');
			
			$filters				= $this->get('filters');
			$this->columns			= $filters[0];
			$this->operators		= $filters[1];
			$this->values			= $filters[2];
			$this->other			= $this->get('OtherFilters');
			$this->operator			= $this->get('Operator');
			$this->showCondition	= $this->get('ConditionsNr');
			$this->tabs				= $this->get('Tabs');
			$this->form				= $this->get('BatchForm');
			$this->version			= rseventsproHelper::isJ4() ? 'j4' : 'j3';
			
			$this->addToolBar();
		}
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_LIST_EVENTS'),'rseventspro48');
		
		$toolbar = JToolbar::getInstance('toolbar');
		JToolBarHelper::addNew('event.add');
		
		if (rseventsproHelper::isJ4()) {
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('fas fa-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();
			$childBar->edit('page.edit')->listCheck(true);
			
			$childBar->appendButton(
				'Custom', '<joomla-toolbar-button><button onclick="rsepro_show_preview()" '
				. 'class="button-preivew dropdown-item"><span class="fas fa-search-plus" aria-hidden="true"></span>'
				. JText::_('COM_RSEVENTSPRO_PREVIEW_EVENT') . '</button></joomla-toolbar-button>', 'preivew'
			);
			
			$childBar->delete('events.delete')
				->text('JTOOLBAR_DELETE')
				->message('COM_RSEVENTSPRO_REMOVE_EVENTS')
				->icon('icon-trash')
				->listCheck(true);
			
			$childBar->appendButton(
				'Custom', '<joomla-toolbar-button><button onclick="Joomla.submitbutton(\'events.copy\')" '
				. 'class="button-copy dropdown-item"><span class="fas fa-copy" aria-hidden="true"></span>'
				. JText::_('COM_RSEVENTSPRO_COPY_EVENT') . '</button></joomla-toolbar-button>', 'copy'
			);
			
			$childBar->publish('events.publish')->listCheck(true);
			$childBar->unpublish('events.unpublish')->listCheck(true);
			$childBar->archive('events.archive')->listCheck(true);
			
			$childBar->appendButton(
				'Custom', '<joomla-toolbar-button><button onclick="Joomla.submitbutton(\'events.cancel\')" '
				. 'class="button-cancel dropdown-item"><span class="fas fa-times" aria-hidden="true"></span>'
				. JText::_('COM_RSEVENTSPRO_CANCEL_EVENT') . '</button></joomla-toolbar-button>', 'cancel'
			);
			
			$childBar->appendButton(
				'Custom', '<joomla-toolbar-button><button onclick="Joomla.submitbutton(\'events.featured\')" '
				. 'class="button-featured dropdown-item"><span class="fas fa-star" aria-hidden="true"></span>'
				. JText::_('JFEATURED') . '</button></joomla-toolbar-button>', 'featured'
			);
			
			$childBar->appendButton(
				'Custom', '<joomla-toolbar-button><button onclick="Joomla.submitbutton(\'events.exportical\')" '
				. 'class="button-exportical dropdown-item"><span class="fas fa-arrow-down" aria-hidden="true"></span>'
				. JText::_('COM_RSEVENTSPRO_EXPORT_ICAL') . '</button></joomla-toolbar-button>', 'exportical'
			);
			
			$childBar->appendButton(
				'Custom', '<joomla-toolbar-button><button onclick="Joomla.submitbutton(\'events.exportcsv\')" '
				. 'class="button-exportcsv dropdown-item"><span class="fas fa-arrow-down" aria-hidden="true"></span>'
				. JText::_('COM_RSEVENTSPRO_EXPORT_CSV') . '</button></joomla-toolbar-button>', 'exportcsv'
			);
			
			$childBar->appendButton(
				'Custom', '<joomla-toolbar-button><button onclick="Joomla.submitbutton(\'events.rating\')" '
				. 'class="button-rating dropdown-item"><span class="fas fa-trash" aria-hidden="true"></span>'
				. JText::_('COM_RSEVENTSPRO_CLEAR_RATING') . '</button></joomla-toolbar-button>', 'rating'
			);
			
		} else {
			JToolBarHelper::editList('event.edit');
			
			$layout = new JLayoutFile('joomla.toolbar.standard');
			$dhtml = $layout->render(array('text' => JText::_('COM_RSEVENTSPRO_PREVIEW_EVENT'), 'btnClass' => 'btn', 'id' => '', 'htmlAttributes' => '', 'onclick' => 'rsepro_show_preview()', 'class' => 'icon-preview', 'doTask' => 'rsepro_show_preview()'));
			$toolbar->appendButton('Custom', $dhtml, 'preview', false);
			
			JToolBarHelper::divider();
			JToolBarHelper::deleteList(JText::_('COM_RSEVENTSPRO_REMOVE_EVENTS'),'events.delete');
			JToolBarHelper::custom('events.copy', 'copy.png', 'copy_f2.png', 'COM_RSEVENTSPRO_COPY_EVENT' );
			JToolBarHelper::publishList('events.publish');
			JToolBarHelper::unpublishList('events.unpublish');
			JToolBarHelper::archiveList('events.archive');
			JToolbarHelper::custom('events.cancel', 'cancel', 'cancel', JText::_('COM_RSEVENTSPRO_CANCEL_EVENT'), true);
			JToolbarHelper::custom('events.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
			JToolBarHelper::custom('events.exportical','arrow-down','arrow-down',JText::_('COM_RSEVENTSPRO_EXPORT_ICAL'));
			JToolBarHelper::custom('events.exportcsv','arrow-down','arrow-down',JText::_('COM_RSEVENTSPRO_EXPORT_CSV'));
			JToolBarHelper::divider();
			JToolBarHelper::custom('events.rating','trash','trash',JText::_('COM_RSEVENTSPRO_CLEAR_RATING'));
			JToolBarHelper::divider();
			
			JHtml::_('formbehavior.chosen','#modal-batchevents select');
		}
		
		JToolBarHelper::custom('events.sync','refresh','refresh',JText::_('COM_RSEVENTSPRO_SYNC'),false);
		
		$layout = new JLayoutFile('joomla.toolbar.popup');
		$dhtml = $layout->render(array('text' => JText::_('JTOOLBAR_BATCH'), 'htmlAttributes' => '', 'btnClass' => 'btn', 'class' => 'icon-checkbox-partial', 'name' => 'batchevents', 'selector' => 'batchevents', 'doTask' => ''));
		$toolbar->appendButton('Custom', $dhtml, 'batch');
		
		JHtml::script('com_rseventspro/jquery.filter.'.$this->version.'.js', array('relative' => true, 'version' => 'auto'));
	}
	
	protected function addToolBarReport() {
		JToolBarHelper::title(JText::sprintf('COM_RSEVENTSPRO_REPORTS_FOR', @$this->reports['name']),'rseventspro48');
		JToolBarHelper::deleteList('','events.deletereports');
		
		$layout = new JLayoutFile('joomla.toolbar.link');
		$dhtml = $layout->render(array('text' => JText::_('COM_RSEVENTSPRO_GLOBAL_BACK_BTN'), 'btnClass' => 'btn btn-primary', 'id' => '', 'class' => 'icon-arrow-left', 'htmlAttributes' => '', 'url' => 'index.php?option=com_rseventspro&view=events', 'doTask' => 'index.php?option=com_rseventspro&view=events'));
		JToolbar::getInstance('toolbar')->appendButton('Custom', $dhtml, 'back');
	}
	
	protected function getDetails($id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('e.id'))->select($db->qn('e.name'))->select($db->qn('e.start'))->select($db->qn('e.end'))
			->select($db->qn('e.registration'))->select($db->qn('e.rsvp'))
			->select($db->qn('e.parent'))->select($db->qn('e.icon'))->select($db->qn('e.published'))
			->select($db->qn('e.owner'))->select($db->qn('e.featured'))->select($db->qn('e.completed'))->select($db->qn('l.id','lid'))
			->select($db->qn('l.name','lname'))->select($db->qn('u.name','uname'))->select($db->qn('e.allday'))->select($db->qn('e.hits'))
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
			->select('COUNT('.$db->qn('u.id').')')
			->from($db->qn('#__rseventspro_users','u'))
			->where($db->qn('u.ide').' = '.(int) $id);
		
		JFactory::getApplication()->triggerEvent('onrsepro_subscriptionsQuery', array(array('query' => &$query, 'rule' => 'u.ide')));
		
		$db->setQuery($query);
		return (int) $db->loadResult();
	}
	
	protected function getWaitingList($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rseventspro_waitinglist'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		return (int) $db->loadResult();
	}
	
	protected function getUnsubscribers($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rseventspro_unsubscribers'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		return (int) $db->loadResult();
	}
}