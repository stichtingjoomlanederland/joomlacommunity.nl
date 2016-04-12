<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class RSFormViewRsform extends JViewLegacy
{
	protected $buttons;
	// version info
	protected $code;
	protected $version;
	protected $isJ30;
	
	function display($tpl = null)
	{
		$this->addToolbar();
		
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rsform/assets/css/dashboard.css');
		
		$this->isJ30 	= RSFormProHelper::isJ('3.0');
		$this->buttons  = $this->get('Buttons');
		$this->code		= $this->get('code');
		$this->version	= (string) new RSFormProVersion();
		
		$this->sidebar	= $this->get('SideBar');
		
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		if (JFactory::getUser()->authorise('core.admin', 'com_rsform'))
			JToolBarHelper::preferences('com_rsform');
		
		// set title
		JToolBarHelper::title('RSForm! Pro', 'rsform');
		
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSFormProToolbarHelper::addToolbar('rsform');
	}
}