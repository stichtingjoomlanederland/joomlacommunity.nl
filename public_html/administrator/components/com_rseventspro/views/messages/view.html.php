<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewMessages extends JViewLegacy
{	
	public function display($tpl = null) {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_LIST_EMAILS'),'rseventspro48');
		
		parent::display($tpl);
	}
}