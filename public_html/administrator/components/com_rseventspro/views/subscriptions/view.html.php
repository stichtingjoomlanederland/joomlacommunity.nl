<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewSubscriptions extends JViewLegacy
{
	public function display($tpl = null) {		
		$this->layout		= $this->getLayout();
		
		if ($this->layout == 'scan') {
			JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_EVENT_SCAN_TICKET'),'rseventspro48');
			JToolBar::getInstance('toolbar')->appendButton( 'Link', 'back', JText::_('COM_RSEVENTSPRO_GLOBAL_BACK_BTN'), JRoute::_('index.php?option=com_rseventspro&view=subscriptions'));
			
			$this->scan			 = rseventsproHelper::getScan();
		} else {
			$this->items 		 = $this->get('Items');
			$this->pagination 	 = $this->get('Pagination');
			$this->state 		 = $this->get('State');
			$this->filterForm    = $this->get('FilterForm');
			$this->activeFilters = $this->get('ActiveFilters');
			
			if (!$this->state->get('filter.event')) {
				$ticketXml = new SimpleXMLElement('<field name="ticket" type="hidden" default="" />');
				$this->filterForm->setField($ticketXml, 'filter', true);
			}
			
			$this->addToolBar();
		}
		
		JFactory::getApplication()->triggerEvent('rsepro_adminSubscriptionsDisplayLayout', array(array('view' => &$this)));

		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_LIST_SUBSCRIPTIONS'),'rseventspro48');
		JToolBarHelper::addNew('subscription.add');
		JToolBarHelper::editList('subscription.edit');
		JToolBarHelper::deleteList('','subscriptions.delete');
		JToolBarHelper::custom('subscriptions.complete','save','save',JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_APPROVE'));
		JToolBarHelper::custom('subscriptions.incomplete','pending','pending',JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_PENDING'));
		JToolBarHelper::custom('subscriptions.denied','cancel-circle','cancel-circle',JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_DENY'));
		
		if ($event = $this->state->get('filter.event')) {
			if (rseventsproHelper::pdf())
				JToolBar::getInstance('toolbar')->appendButton( 'Link', 'list', JText::_('COM_RSEVENTSPRO_SUBSCRIBERS_LIST'), JRoute::_('index.php?option=com_rseventspro&view=pdf&eid='.$event));
			
			JToolBar::getInstance('toolbar')->appendButton( 'Link', 'arrow-down', JText::_('COM_RSEVENTSPRO_EXPORT_SUBSCRIBERS'), JRoute::_('index.php?option=com_rseventspro&task=subscriptions.export&id='.$event));
		}
		
		JToolBar::getInstance('toolbar')->appendButton( 'Link', 'lamp', JText::_('COM_RSEVENTSPRO_EVENT_SCAN_TICKET'), JRoute::_('index.php?option=com_rseventspro&view=subscriptions&layout=scan'));
		
		JHtml::_('rseventspro.chosen','select');
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