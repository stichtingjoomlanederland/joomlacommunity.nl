<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewSubscriptions extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $sidebar;
	protected $filterbar;
	protected $total;
	
	public function display($tpl = null) {		
		$this->layout		= $this->getLayout();
		$this->sidebar		= $this->get('Sidebar');
		
		if ($this->layout == 'scan') {
			JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_EVENT_SCAN_TICKET'),'rseventspro48');
			
			$this->scan			= rseventsproHelper::getScan();
			$this->event		= $this->get('Event');
		} else {
			$this->items 		= $this->get('Items');
			$this->pagination 	= $this->get('Pagination');
			$this->filterbar	= $this->get('Filterbar');	
			$this->total 		= $this->get('Total');
			$this->state 		= $this->get('State');
			
			$this->addToolBar();
		}

		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_LIST_SUBSCRIPTIONS'),'rseventspro48');
		JToolBarHelper::addNew('subscription.add');
		JToolBarHelper::editList('subscription.edit');
		JToolBarHelper::deleteList('','subscriptions.delete');
		JToolBarHelper::custom('subscriptions.complete','approve','approve',JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_APPROVE'));
		JToolBarHelper::custom('subscriptions.incomplete','pending','pending',JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_PENDING'));
		JToolBarHelper::custom('subscriptions.denied','denied','denied',JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_DENY'));
		
		if ($event = $this->state->get('filter.event')) {
			if (rseventsproHelper::pdf())
				JToolBar::getInstance('toolbar')->appendButton( 'Link', 'list', JText::_('COM_RSEVENTSPRO_SUBSCRIBERS_LIST'), JRoute::_('index.php?option=com_rseventspro&view=pdf&eid='.$event));
			
			JToolBar::getInstance('toolbar')->appendButton( 'Link', 'export', JText::_('COM_RSEVENTSPRO_EXPORT_SUBSCRIBERS'), JRoute::_('index.php?option=com_rseventspro&task=subscriptions.export&id='.$event));
		}
		
		JToolBar::getInstance('toolbar')->appendButton( 'Link', 'lamp', JText::_('COM_RSEVENTSPRO_EVENT_SCAN_TICKET'), JRoute::_('index.php?option=com_rseventspro&view=subscriptions&layout=scan'));
		JToolBarHelper::custom('subscriptions.confirmsubscriber','approve','approve',JText::_('COM_RSEVENTSPRO_SUBSCRIBERS_CONFIRM'));
		JToolBarHelper::custom('rseventspro','rseventspro32','rseventspro32',JText::_('COM_RSEVENTSPRO_GLOBAL_NAME'),false);
		
		if (rseventsproHelper::isJ3()) {
			JHtml::_('rseventspro.chosen','select');
		}
	}
	
	protected function getUser($id) {
		if ($id > 0)
			return JFactory::getUser($id)->get('username');
		
		return JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST');
	}
	
	protected function getStatus($state) {
		if ($state == 0) {
			return '<font color="blue">'.JText::_('COM_RSEVENTSPRO_RULE_STATUS_INCOMPLETE').'</font>';
		} else if ($state == 1) {
			return '<font color="green">'.JText::_('COM_RSEVENTSPRO_RULE_STATUS_COMPLETE').'</font>';
		} else if ($state == 2) {
			return '<font color="red">'.JText::_('COM_RSEVENTSPRO_RULE_STATUS_DENIED').'</font>';
		}
	}
}