<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/jquerycalendar.php';

class RSFormProFieldFoundationJqueryCalendar extends RSFormProFieldJqueryCalendar
{
	protected function setFieldOutput($input, $button, $container, $hidden, $layout) {
		if ($layout == 'FLAT') {
			return '<div class="row"><div class="medium-12 columns">'.$input.'</div>'.'<div class="medium-12 columns end">'.$container.'</div>'.$hidden.'</div>';
		} else {
			return '<div class="row"><div class="medium-6 columns">'.$input.'</div>'.'<div class="medium-6 columns">'.$button.'</div>'.$container.$hidden.'</div>';
		}
	}
	
	// @desc All calendars should have a 'rsform-calendar-box' class for easy styling
	//		 Since the calendar is composed of multiple items, we need to differentiate the attributes through the $type parameter
	public function getAttributes($type='input') {
		$attr = parent::getAttributes($type);
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		
		if ($type == 'button') {
			$attr['class'] .= 'button secondary';
		}
		
		return $attr;
	}
}