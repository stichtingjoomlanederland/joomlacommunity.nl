<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/


defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/birthday.php';

class RSFormProFieldBootstrap3BirthDay extends RSFormProFieldBirthDay
{
	public function getFormInput() {
		$separator	= $this->getProperty('DATESEPARATOR');
		$items = parent::getFormInput();
		
		$items = explode($separator, $items);
		
		// extra classes for proper alignment
		foreach($items as $i => &$item) {
			$item = '<div class="col-sm-3 col-xs-12">'.$item.'</div>';
		}
		return '<div class="row">'.implode('', $items).'</div>';
	}
	
	// @desc All birthday select lists should have a 'rsform-select-box-small' class for easy styling
	public function getAttributes() {
		$attr = array();
		if ($attrs = $this->getProperty('ADDITIONALATTRIBUTES')) {
			$attr = $this->parseAttributes($attrs);
		}
		if (!isset($attr['class'])) {
			$attr['class'] = '';
		} else {
			$attr['class'] .= ' ';
		}
		$attr['class'] .= 'form-control';
		
		// Check for invalid here so that we can add 'rsform-error'
		if ($this->invalid[$this->processing]) {
			$attr['class'] .= ' rsform-error';
		}
		
		// Must add an onchange event when we don't allow incorrect dates eg. 31 feb
		if (($this->processing == 'm' || $this->processing == 'y') && ($this->hasAllFields && $this->getProperty('VALIDATION_ALLOW_INCORRECT_DATE', 'YES'))) {
			if (!isset($attr['onchange'])) {
				$attr['onchange'] = '';
			} else {
				$attr['onchange'] .= ' ';
			}
			$attr['onchange'] .= "rsfp_checkValidDate('".$this->name."');";
		}
		
		return $attr;
	}
}