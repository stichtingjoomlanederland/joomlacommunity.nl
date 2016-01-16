<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.application.component.view');

class rseventsproViewGroups extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $sidebar;
	protected $filterbar;
	protected $total;
	
	public function display($tpl = null) {
		$this->filterbar	= $this->get('Filterbar');
		$this->state 		= $this->get('State');
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->total 		= $this->get('Total');
		$this->sidebar		= $this->get('Sidebar');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_LIST_GROUPS'),'rseventspro48');
		JToolBarHelper::addNew('group.add');
		JToolBarHelper::editList('group.edit');
		JToolBarHelper::deleteList('','groups.delete');
		JToolBarHelper::custom('rseventspro','rseventspro32','rseventspro32',JText::_('COM_RSEVENTSPRO_GLOBAL_NAME'),false);
		
		JFactory::getDocument()->addScript(JURI::root().'components/com_rseventspro/assets/js/dom.js');
	}
}