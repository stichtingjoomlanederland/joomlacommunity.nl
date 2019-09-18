<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldFreeText extends RSFormProField
{
	// backend preview
	public function getPreviewInput()
	{
		$value		= $this->getProperty('TEXT', '');
		$codeIcon 	= '';
		
		if ($this->hasCode($value)) {
			$value 		= JText::_('RSFP_PHP_CODE_PLACEHOLDER');
			$codeIcon	= RSFormProHelper::getIcon('php');
		} else {
			$value = '<pre class="rsfp-preview-freetext">'.$this->escape($value).'</pre>';
		}

		return $codeIcon . $value;
	}
	
	// functions used for rendering in front view
	public function getFormInput() {
		$html = $this->getProperty('TEXT', '');
		$html = $this->isCode($html);
		
		return $html;
	}
}