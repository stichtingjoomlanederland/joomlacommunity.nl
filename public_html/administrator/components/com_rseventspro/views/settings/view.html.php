<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewSettings extends JViewLegacy
{
	protected $form;
	protected $fieldsets;
	protected $tabs;
	protected $layouts;
	protected $config;
	protected $social;
	
	public function display($tpl = null) {
		if ($this->getLayout() == 'log') {
			$this->logs			= $this->get('Logs');
			$this->pagination	= $this->get('Pagination');
		} else {
			$this->app			= JFactory::getApplication();
			$this->form			= $this->get('Form');
			$this->tabs			= $this->get('Tabs');
			$this->layouts		= $this->get('Layouts');
			$this->config		= $this->get('Config');
			$this->social		= $this->get('Social');
			$this->fieldsets	= $this->form->getFieldsets();
			$this->login		= $this->get('Login');
			
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/google.php';
			$google = new RSEPROGoogle;
			$this->auth = $google->getAuthURL();
			
			if ($this->app->input->getInt('fb',0)) {
				$this->app->enqueueMessage(JText::_('COM_RSEVENTSPRO_FACEBOOK_CONNECTION_OK'), 'message');
			}
			
			$this->addToolBar();
		}
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_CONF_SETTINGS'), 'rseventspro48');
		
		JHtml::_('rseventspro.chosen','select');
		
		JFactory::getDocument()->addScript('https://maps.google.com/maps/api/js?language='.JFactory::getLanguage()->getTag().($this->config->google_map_api ? '&key='.$this->config->google_map_api : ''));
		JHtml::script('com_rseventspro/jquery.map.js', array('relative' => true, 'version' => 'auto'));
		
		JToolBarHelper::apply('settings.apply');
		JToolBarHelper::save('settings.save');
		JToolBarHelper::cancel('settings.cancel');
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rseventspro'))
			JToolBarHelper::preferences('com_rseventspro');
	}
}