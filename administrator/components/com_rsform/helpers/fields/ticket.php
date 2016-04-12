<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/hidden.php';

class RSFormProFieldTicket extends RSFormProFieldHidden
{
	// backend preview
	public function getPreviewInput() {
		$txt	 	= JText::_('RSFP_HIDDEN_FIELD_PLACEHOLDER');
		$codeIcon   = RSFormProHelper::getIcon('support');
		$length 	= $this->getProperty('LENGTH', 8);
		$characters = $this->getProperty('CHARACTERS', 'ALPHANUMERIC');
		
		$html = '<td>&nbsp;</td><td>'.$codeIcon.RSFormProHelper::generateString($length, $characters).'</td>';
		return $html;
	}
	
	// @desc Overridden here because this field generates a value based on its settings
	public function getValue() {
		$length 	= $this->getProperty('LENGTH', 8);
		$characters = $this->getProperty('CHARACTERS', 'ALPHANUMERIC');
		
		return RSFormProHelper::generateString($length, $characters);
	}
}