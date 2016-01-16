<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.application.component.view');

class rseventsproViewEvent extends JViewLegacy
{	
	protected $item;
	protected $config;
	protected $layout;
	protected $tab;
	protected $eventClass;
	
	public function display($tpl = null) {
		$this->config		= rseventsproHelper::getConfig();
		$this->layout		= $this->getLayout();
		$this->item			= $this->get('Item');
		
		if ($this->layout == 'edit') {
			$this->tab		= JFactory::getApplication()->input->getInt('tab');
			
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/events.php';
			$this->eventClass = RSEvent::getInstance($this->item->id);
			$this->states	= array('published' => true, 'unpublished' => true, 'archived' => true, 'trash' => false, 'all' => false);
			
			$this->addToolBar();
		} elseif ($this->layout == 'crop') {
			// Load scripts
			rseventsproHelper::loadEditEvent(false,true);
		} elseif ($this->layout == 'file') {
			$this->row = $this->get('File');
		} elseif ($this->layout == 'tickets') {
			
			if (!rseventsproHelper::isJ3()) {
				JFactory::getDocument()->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/jquery/jquery-1.9.1.js');
				JFactory::getDocument()->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/jquery/jquery.noconflict.js');
			}
			
			JFactory::getDocument()->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/jquery/jquery-ui-1.10.3.custom.js');
			JFactory::getDocument()->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/js/jquery/jquery-ui-1.10.3.custom.css');
			JFactory::getDocument()->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/css/tickets.css');
			
			$this->tickets = rseventsproHelper::getTickets(JFactory::getApplication()->input->getInt('id',0));
		}
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		$this->item->name ? JToolBarHelper::title(JText::sprintf('COM_RSEVENTSPRO_EDIT_EVENT',$this->item->name),'rseventspro48') : JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_ADD_EVENT'),'rseventspro48');
		JToolBarHelper::apply('event.apply');
		JToolBarHelper::save('event.save');
		JToolBarHelper::custom('preview','preview','preview',JText::_('COM_RSEVENTSPRO_PREVIEW_EVENT'),false);
		JToolBarHelper::cancel('event.cancel');
		
		rseventsproHelper::chosen();
		
		// Load scripts
		rseventsproHelper::loadEditEvent();
		
		// Load RSEvents!Pro plugins
		rseventsproHelper::loadPlugins();
		
		// Load custom scripts
		JFactory::getApplication()->triggerEvent('rsepro_addCustomScripts');
		
		if ($this->config->enable_google_maps)
			JFactory::getDocument()->addScript('https://maps.google.com/maps/api/js?sensor=false');
	}
}