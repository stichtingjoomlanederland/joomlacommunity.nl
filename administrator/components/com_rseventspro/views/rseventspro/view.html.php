<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewRseventspro extends JViewLegacy
{
	protected $jversion;
	protected $code;
	protected $config;
	protected $middle;
	protected $events;
	protected $subscribers;
	protected $comments;
	
	public function display($tpl = null) {		
		$layout = $this->getLayout();	
		
		$this->version = (string) new RSEventsProVersion();
		
		if ($layout == 'update') {
			$this->sidebar = rseventsproHelper::isJ3() ? JHtmlSidebar::render() : '';
			$jversion = new JVersion();
			$this->jversion = $jversion->getShortVersion();
		} else {
			$this->config		= rseventsproHelper::getConfig();
			$this->code			= $this->config->global_code;
			$this->middle		= $this->config->dashboard_upcoming || $this->config->dashboard_subscribers || ($this->config->dashboard_comments && !in_array($this->config->event_comment,array(0,1)));
			$this->events		= $this->get('Events');
			$this->subscribers	= $this->get('Subscribers');
			$this->comments		= $this->get('Comments');
			$this->buttons		= $this->get('Buttons');
		}
		
		$this->addToolBar($layout);
		parent::display($tpl);
	}
	
	protected function addToolBar($layout) {
		if ($layout == 'update') {
			JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_UPDATE'),'rseventspro48');
		} else {
			JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_GLOBAL_NAME'),'rseventspro48');
		}
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rseventspro'))
			JToolBarHelper::preferences('com_rseventspro');
		
		JToolBarHelper::custom('rseventspro','rseventspro32','rseventspro32',JText::_('COM_RSEVENTSPRO_GLOBAL_NAME'),false);
	}
}