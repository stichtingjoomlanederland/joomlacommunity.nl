<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProPrices
{
	protected $formId;
	protected $prices = array();
	
	public static function getInstance($formId) {
		static $inst = array();
		if (!isset($inst[$formId])) {
			$inst[$formId] = new RSFormProPrices($formId);
		}
		return $inst[$formId];
	}
	
	public function __construct($formId) {
		$this->formId = $formId;
	}
	
	public function addPrice($name, $value, $price) {
		if (!isset($this->prices[$name])) {
			$this->prices[$name] = array();
		}
		$this->prices[$name][$value] = $price;
	}
	
	public function getPrices() {
		return $this->prices;
	}
}