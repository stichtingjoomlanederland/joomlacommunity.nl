<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RSFormProDateValidations
{
	public static function none($day, $month, $year, $data = array()) {
		// no validation
		$valid = true;
		return $valid;
	}
	
	public static function fromtoday($day, $month, $year, $data = array()) {
		$today    = JFactory::getDate();
		$selected = JFactory::getDate($year.'-'.self::padding($month).'-'.self::padding($day).' 23:59:59');
		
		return $selected->toUnix() >= $today->toUnix();
	}
	
	public static function fromtomorrow($day, $month, $year, $data = array()) {
		$tomorrow = JFactory::getDate();
		$tomorrow->modify('+1 day');
		$selected = JFactory::getDate($year.'-'.self::padding($month).'-'.self::padding($day).' 23:59:59');
		
		return $selected->toUnix() >= $tomorrow->toUnix();
	}
	
	public static function beforetodayexcluding($day, $month, $year, $data = array()) {
		$today    = JFactory::getDate();
		$today->setTime(0, 0, 0);
		$selected = JFactory::getDate($year.'-'.self::padding($month).'-'.self::padding($day).' 00:00:00');
		
		return $selected->toUnix() < $today->toUnix();
	}
	
	public static function beforetodayincluding($day, $month, $year, $data = array()) {
		$today    = JFactory::getDate();
		$today->setTime(23, 59, 59);
		$selected = JFactory::getDate($year.'-'.self::padding($month).'-'.self::padding($day).' 00:00:00');
		
		return $selected->toUnix() < $today->toUnix();
	}
	
	protected static function padding($value) {
		return str_pad($value, 2, '0', STR_PAD_LEFT);
	}
}