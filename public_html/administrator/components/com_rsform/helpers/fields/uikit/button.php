<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/button.php';

class RSFormProFieldUikitButton extends RSFormProFieldButton
{
	
	// @desc All buttons should have a class for easy styling
	public function getAttributes($type='button') {
		$attr = parent::getAttributes($type);
		
		if ($type == 'button') {
			$attr['class'] .= ' uk-button';
		} elseif ($type == 'reset') {
			$attr['class'] .= ' uk-button uk-button-danger';
		}
		
		return $attr;
	}
	
}