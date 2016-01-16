<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport('joomla.application.component.view');

class rseventsproViewSettings extends JViewLegacy
{
	protected $form;
	protected $fieldsets;
	protected $tabs;
	protected $layouts;
	protected $config;
	protected $social;
	
	public function display($tpl = null) {
		$this->form			= $this->get('Form');
		$this->tabs			= $this->get('Tabs');
		$this->layouts		= $this->get('Layouts');
		$this->config		= $this->get('Config');
		$this->social		= $this->get('Social');
		$this->fieldsets	= $this->form->getFieldsets();
		$this->sidebar		= rseventsproHelper::isJ3() ? JHtmlSidebar::render() : '';
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_CONF_SETTINGS'), 'rseventspro48');
		
		JFactory::getDocument()->addScript('https://maps.google.com/maps/api/js?sensor=false');
		
		JToolBarHelper::apply('settings.apply');
		JToolBarHelper::save('settings.save');
		JToolBarHelper::cancel('settings.cancel');
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rseventspro'))
			JToolBarHelper::preferences('com_rseventspro');
		
		JToolBarHelper::custom('rseventspro','rseventspro32','rseventspro32',JText::_('COM_RSEVENTSPRO_GLOBAL_NAME'),false);
	}
}