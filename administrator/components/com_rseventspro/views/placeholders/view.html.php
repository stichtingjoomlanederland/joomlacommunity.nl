<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewPlaceholders extends JViewLegacy
{	
	public function display($tpl = null) {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/placeholders.php';
		
		$type 				= JFactory::getApplication()->input->getCmd('type','');
		$this->placeholders = RSEventsProPlaceholders::get($type);
		
		parent::display($tpl);
	}
}