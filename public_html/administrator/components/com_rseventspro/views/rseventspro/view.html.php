<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewRseventspro extends JViewLegacy
{
	protected $jversion;
	protected $code;
	protected $config;
	protected $events;
	protected $subscribers;
	protected $comments;
	
	public function display($tpl = null) {		
		$this->version		= (string) new RSEventsProVersion();	
		$this->config		= rseventsproHelper::getConfig();
		$this->code			= $this->config->global_code;
		$this->events		= $this->get('Events');
		$this->subscribers	= $this->get('Subscribers');
		$this->comments		= $this->get('Comments');
		$this->buttons		= $this->get('Buttons');
		$this->statistics	= $this->get('Statistics');
		$this->total		= $this->get('Totals');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_GLOBAL_NAME'),'rseventspro48');
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rseventspro'))
			JToolBarHelper::preferences('com_rseventspro');
	}
}