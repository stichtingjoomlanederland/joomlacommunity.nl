<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSCommentsViewUpdates extends JViewLegacy
{
	public function display($tpl=null) 	{
		$version = new JVersion();
		$this->jversion = $version->getShortVersion();
		$this->version	= (string) new RSCommentsVersion();
		$this->code		= RSCommentsHelper::genKeyCode();
		
		$this->addToolbar();
		$this->sidebar 		= RSCommentsHelper::isJ3() ? JHtmlSidebar::render() : '';
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolBarHelper::title(JText::_('RSComments!'),'rscomment');
		JToolBarHelper::preferences('com_rscomments');

		// add Menu in sidebar
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSCommentsToolbarHelper::addToolbar('updates');
	}
}