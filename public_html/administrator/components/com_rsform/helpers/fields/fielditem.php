<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProFieldItem
{
	public $label;
	public $value;	
	public $flags = array(
		'checked' 	=> false, // [c]
		'optgroup' 	=> false, // [g]
		'/optgroup' => false, // [/g]
		'disabled' 	=> false, // [d]
		'price'		=> false  // [p]
	);
	// @desc Holds the unmodified string, as it was sent
	protected $string;
	
	public function __construct($string) {
		$this->string = $string;
		
		// Check for known flags
		$this->flags['checked']   = $this->checkFlag('c');
		$this->flags['optgroup']  = $this->checkFlag('g');
		$this->flags['/optgroup'] = $this->checkFlag('/g');
		$this->flags['disabled']  = $this->checkFlag('d');
		$this->flags['price']  	  = $this->checkFlag('p');
		
		// Check for "value|label"
		if (strpos($this->string, '|') !== false) {
			list($this->value, $this->label) = explode('|', $this->string, 2);
		} else {
			$this->value = $this->label = $this->string;
		}
	}
	
	// @desc Checks if the flag is set and removes it from the string
	//		 This way we'll have a clean label & value
	protected function checkFlag($flag) {
		if ($flag == 'p') {
			$priceFlag 	= '#\[p([0-9\.\-\+]+)\]#s';
			if (preg_match($priceFlag, $this->string, $match)) {
				$this->string = str_replace($match[0], '', $this->string);
				return $match[1];
			}
		} else {
			if (strpos($this->string, '['.$flag.']') !== false) {
				$this->string = str_replace('['.$flag.']', '', $this->string);
				return true;
			}
		}
		
		return false;
	}
}