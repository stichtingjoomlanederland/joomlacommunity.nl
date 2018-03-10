<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewImports extends JViewLegacy
{
	protected $items;
	protected $offsets;
	protected $sidebar;
	protected $locations;
	
	public function display($tpl = null) {		
		$this->items		= $this->get('Items');
		$this->offsets		= $this->get('Offsets');
		$this->locations	= $this->get('Locations');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_IMPORT_EVENTS'),'rseventspro48');
		
		JHtml::_('rseventspro.chosen','select');
	}
}