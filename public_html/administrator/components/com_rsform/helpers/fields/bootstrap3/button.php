<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/button.php';

class RSFormProFieldBootstrap3Button extends RSFormProFieldButton
{
	
	// @desc All buttons should have a class for easy styling
	public function getAttributes($type='button') {
		$attr = parent::getAttributes();
		
		if ($type == 'button') {
			$attr['class'] .= ' btn btn-default';
		} elseif ($type == 'reset') {
			$attr['class'] .= ' btn btn-danger';
		}
		
		return $attr;
	}
	
}