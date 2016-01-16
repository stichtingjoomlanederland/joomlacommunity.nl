<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

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
		
		if (rseventsproHelper::isJ3()) {
			JHtml::_('rseventspro.chosen','select');
		}
		
		if ($this->config->enable_google_maps) {
			JFactory::getDocument()->addScript('https://maps.google.com/maps/api/js?sensor=false');
			JFactory::getDocument()->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/jquery.map.js?v='.RSEPRO_RS_REVISION);
		}
	}
}