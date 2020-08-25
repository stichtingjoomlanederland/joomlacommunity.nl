<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewUnsubscribers extends JViewLegacy
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
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_UNSUBSCRIBERS'),'rseventspro48');
		
		JToolBar::getInstance('toolbar')->appendButton( 'Link', 'arrow-down', JText::_('COM_RSEVENTSPRO_EXPORT_CSV'), JRoute::_('index.php?option=com_rseventspro&task=unsubscribers.export&id='.JFactory::getApplication()->input->getInt('id',0)));
		JToolBarHelper::deleteList('','unsubscribers.delete');
		
		JHtml::_('rseventspro.chosen','select');
	}
}