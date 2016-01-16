<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewMessages extends JViewLegacy
{	
	public function display($tpl = null) {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_LIST_EMAILS'),'rseventspro48');
		JToolBarHelper::custom('rseventspro','rseventspro32','rseventspro32',JText::_('COM_RSEVENTSPRO_GLOBAL_NAME'),false);
		
		$this->sidebar = rseventsproHelper::isJ3() ? JHtmlSidebar::render() : '';
		
		parent::display($tpl);
	}
}