<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesViewUpdates extends JViewLegacy
{
	public function display($tpl=null) 	{
		$jversion = new JVersion();
		$this->jversion = $jversion->getShortVersion();
		$this->version	= (string) new RSFilesVersion();
		$this->code		= rsfilesHelper::genKeyCode();
		$this->sidebar	= rsfilesHelper::isJ3() ? JHtmlSidebar::render() : '';
		
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolBarHelper::title(JText::_('RSFiles!'),'rsfiles');
	}
}