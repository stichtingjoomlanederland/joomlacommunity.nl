<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

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
		
		if (rseventsproHelper::isJ3()) {
			JHtml::_('rseventspro.chosen','select');
		}
	}
}