<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.application.component.view');

class rseventsproViewImports extends JViewLegacy
{
	protected $items;
	protected $offsets;
	protected $sidebar;
	protected $locations;
	
	public function display($tpl = null) {		
		$this->items		= $this->get('Items');
		$this->offsets		= $this->get('Offsets');
		$this->sidebar		= $this->get('Sidebar');
		$this->locations	= $this->get('Locations');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_IMPORT_EVENTS'),'rseventspro48');
		JToolBarHelper::custom('rseventspro','rseventspro32','rseventspro32',JText::_('COM_RSEVENTSPRO_GLOBAL_NAME'),false);
	}
}