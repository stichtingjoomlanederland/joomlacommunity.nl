<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.application.component.view');

class rseventsproViewLocation extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $config;
	
	public function display($tpl = null) {
		$this->form 		= $this->get('Form');
		$this->item 		= $this->get('Item');
		$this->config		= rseventsproHelper::getConfig();
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_ADD_EDIT_LOCATION'),'rseventspro48');
		JToolBarHelper::apply('location.apply');
		JToolBarHelper::save('location.save');
		JToolBarHelper::save2new('location.save2new');
		JToolBarHelper::cancel('location.cancel');
		
		rseventsproHelper::chosen();
		
		if ($this->config->enable_google_maps)
			JFactory::getDocument()->addScript('https://maps.google.com/maps/api/js?sensor=false');
	}
}