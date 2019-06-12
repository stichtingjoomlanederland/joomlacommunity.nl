<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/calendar.php';

class RSFormProFieldUikitCalendar extends RSFormProFieldCalendar
{	
	// @desc All calendars should have a 'rsform-calendar-box' class for easy styling
	// Since the calendar is composed of multiple items, we need to differentiate the attributes through the $type parameter
	public function getAttributes($type='input') {
		$attr = parent::getAttributes($type);
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		
		if ($type == 'button') {
			$attr['class'] .= 'uk-button uk-button-default';
		}

		return $attr;
	}
}