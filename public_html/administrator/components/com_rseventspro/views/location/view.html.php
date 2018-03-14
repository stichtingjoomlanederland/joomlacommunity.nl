<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewLocation extends JViewLegacy
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
		
		JHtml::_('rseventspro.chosen','select');
		
		if ($this->config->enable_google_maps) {
			JFactory::getDocument()->addScript('https://maps.google.com/maps/api/js?language='.JFactory::getLanguage()->getTag().($this->config->google_map_api ? '&key='.$this->config->google_map_api : ''));
			JHtml::script('com_rseventspro/jquery.map.js', array('relative' => true, 'version' => 'auto'));
		}
	}
}