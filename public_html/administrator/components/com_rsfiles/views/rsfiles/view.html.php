<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesViewRsfiles extends JViewLegacy
{
	public function display($tpl = null) {
		$this->code		= rsfilesHelper::getConfig('license_code');
		$this->download	= rsfilesHelper::getConfig('download_folder');
		$this->briefcase= rsfilesHelper::getConfig('briefcase_folder');
		$this->version	= (string) new RSFilesVersion();
		$this->stats	= $this->get('Stats');
		$this->from		= JFactory::getDate()->modify('-7 days')->format('Y-m-d');
		$this->to		= JFactory::getDate()->format('Y-m-d');
		$this->hits		= $this->get('Hits');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSFILES_DASHBOARD'),'rsfiles');
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rsfiles'))
			JToolBarHelper::preferences('com_rsfiles');
		
		JToolBarHelper::custom('rsfiles','rsfiles32','rsfiles32',JText::_('COM_RSFILES'),false);
		
		JFactory::getDocument()->addScript('https://www.google.com/jsapi');
	}
}