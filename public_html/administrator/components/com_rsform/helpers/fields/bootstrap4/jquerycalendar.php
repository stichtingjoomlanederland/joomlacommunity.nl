<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/jquerycalendar.php';

class RSFormProFieldBootstrap4JqueryCalendar extends RSFormProFieldJqueryCalendar
{
	protected function setFieldOutput($input, $button, $container, $hidden, $layout) {
		if ($layout == 'FLAT') {
			return '<div class="row"><div class="col-md-12">'.$input.'</div>'.'<div class="col-md-12">'.$container.'</div>'.$hidden.'</div>';
		} else {
            return '<div class="input-group">'.$input.'<span class="input-group-btn">'.$button.'</span></div>'.$container.$hidden;
		}
	}
	
	// @desc All calendars should have a 'rsform-calendar-box' class for easy styling
	//		 Since the calendar is composed of multiple items, we need to differentiate the attributes through the $type parameter
	public function getAttributes($type='input') {
		$attr = parent::getAttributes($type);
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		
		if ($type == 'input') {
			$attr['class'] .= 'form-control';
		} elseif ($type == 'button') {
			$attr['class'] .= 'btn btn-secondary';
		}
		
		return $attr;
	}
}