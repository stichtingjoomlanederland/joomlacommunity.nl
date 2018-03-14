<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewLocations extends JViewLegacy
{	
	public function display($tpl = null) {
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state 		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_LIST_LOCATIONS'),'rseventspro48');
		JToolBarHelper::addNew('location.add');
		JToolBarHelper::editList('location.edit');
		JToolBarHelper::deleteList('','locations.delete');
		JToolBarHelper::publishList('locations.publish');
		JToolBarHelper::unpublishList('locations.unpublish');
		
		JHtml::_('rseventspro.chosen','select');
	}
}