<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewRsvp extends JViewLegacy
{	
	public function display($tpl = null) {
		$this->state 		= $this->get('State');
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->filterForm   = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_RSVP_GUESTS'),'rseventspro48');
		
		JToolBarHelper::custom('rsvp.going','save','save',JText::_('COM_RSEVENTSPRO_RSVP_GOING'));
		JToolBarHelper::custom('rsvp.interested','pending','pending',JText::_('COM_RSEVENTSPRO_RSVP_INTERESTED'));
		JToolBarHelper::custom('rsvp.notgoing','cancel-circle','cancel-circle',JText::_('COM_RSEVENTSPRO_RSVP_NOT_GOING'));
		JToolBar::getInstance('toolbar')->appendButton( 'Link', 'arrow-down', JText::_('COM_RSEVENTSPRO_EXPORT_CSV'), JRoute::_('index.php?option=com_rseventspro&task=rsvp.export&id='.JFactory::getApplication()->input->getInt('id',0)));
		JToolBarHelper::deleteList('','rsvp.delete');
		
		JHtml::_('rseventspro.chosen','select');
	}
}