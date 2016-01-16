<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewSettings extends JViewLegacy
{
	protected $form;
	protected $fieldsets;
	protected $tabs;
	protected $layouts;
	protected $config;
	protected $social;
	
	public function display($tpl = null) {
		$this->app			= JFactory::getApplication();
		$this->form			= $this->get('Form');
		$this->tabs			= $this->get('Tabs');
		$this->layouts		= $this->get('Layouts');
		$this->config		= $this->get('Config');
		$this->social		= $this->get('Social');
		$this->fieldsets	= $this->form->getFieldsets();
		$this->sidebar		= rseventsproHelper::isJ3() ? JHtmlSidebar::render() : '';
		
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/google.php';
		$google = new RSEPROGoogle;
		$this->auth = $google->getAuthURL();
		
		if ($this->app->input->getInt('fb',0)) {
			$this->app->enqueueMessage(JText::_('COM_RSEVENTSPRO_FACEBOOK_CONNECTION_OK'), 'message');
		}
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_CONF_SETTINGS'), 'rseventspro48');
		
		if (rseventsproHelper::isJ3()) {
			JHtml::_('rseventspro.chosen','select');
		}
		
		JFactory::getDocument()->addScript('https://maps.google.com/maps/api/js?sensor=false');
		JFactory::getDocument()->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/jquery.map.js?v='.RSEPRO_RS_REVISION);
		
		JToolBarHelper::apply('settings.apply');
		JToolBarHelper::save('settings.save');
		JToolBarHelper::cancel('settings.cancel');
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rseventspro'))
			JToolBarHelper::preferences('com_rseventspro');
		
		JToolBarHelper::custom('rseventspro','rseventspro32','rseventspro32',JText::_('COM_RSEVENTSPRO_GLOBAL_NAME'),false);
	}
}