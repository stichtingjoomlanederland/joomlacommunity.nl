<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/gmaps.php';

class RSFormProFieldBootstrap3GMaps extends RSFormProFieldGMaps
{
	// @desc Overridden here because we need autocomplete set to off
	public function getAttributes() {
		$attr = parent::getAttributes();
		if (!isset($attr['autocomplete'])) {
			$attr['autocomplete'] = 'off';
		}
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		$attr['class'] .= 'form-control';
		
		return $attr;
	}
}