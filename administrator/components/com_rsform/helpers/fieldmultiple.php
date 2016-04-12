<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldMultiple extends RSFormProField
{
	// used in preview 
	public $codeIcon = '';

	// @desc Splits a string by newlines
	protected function explode($value) {
		// @new feature, array is fine as well
		if (!is_array($value)) {
			$value = str_replace(array("\r\n", "\r"), "\n", $value);
			$value = explode("\n", $value);
		
			return $value;
		} else {
			return $value;
		}
	}
	
	// @desc Returns the full name of the name HTML tag (eg. form[textbox])
	public function getName() {
		return $this->namespace.'['.$this->name.'][]';
	}
	
	
	// @desc Get the items list, if it exists
	//		 Used in select lists, checkbox groups and radio groups
	public function getItems() {
		// Get defined items list
		$items = array();
		if ($items = $this->getProperty('ITEMS')) {
			if ($this->preview) {
				if ($this->hasCode($items)) {
					$items 	= JText::_('RSFP_PHP_CODE_PLACEHOLDER');
					$codeIcon = RSFormProHelper::getIcon('php');
				}
			} else {
				// Check if it's a PHP code
				$items = $this->isCode($items);
			}
			// Split them by newline
			$items = $this->explode($items);
		}
		
		return $items;
	}
	
	public function getItemValue($item) {
		// Default value processing
		if (empty($item)) {
			return null;
		}
		
		// Value does not exist in request.
		if (!isset($this->value[$this->name])) {
			// Grab default [c]hecked value if no request present
			if ($item->flags['checked'] && (empty($this->value) || empty($this->value['formId']))) {
				return $item->value;
			}
		} else {
			// Value exists, grab it from request.
			$value = $this->value[$this->name];
			
			// Birthday field			
			if (!empty($this->processing) && isset($this->value[$this->name][$this->processing])) {
				$value = $this->value[$this->name][$this->processing];
			}
			
			// Found a value
			if (in_array($item->value, (array) $value)) {
				return $item->value;
			}
		}
		
		return null;
	}
	
	public function array_chunk_index($array, $indexes) {
		$i = 0; // stores the array index regarding if its an integer or a string
		$k = 0; // stores the chunks indexes, so that we can group them
		$chunks = array();
		foreach ($array as $value) {
			// create the chunk entry
			if (!isset($chunks[$k])) {
				$chunks[$k] = array();
			}
			
			// add the values to the current chunk
			$chunks[$k][] = $value;
			
			// if the $array index is found in the $indexes array, increment the chunk index so that the next value is set to the next chunk
			if (in_array($i, $indexes)) {
				$k++;
			}
			// increment the $array index
			$i++;
		}
		return $chunks;
	}
	
	public function splitItems($array, $columns) {
		$items = count($array);
		$rest = $items % $columns;
		
		$diff = $items - $columns;
		if ($diff < 0) {
			return array_chunk($array, 1);
		}
		
		if ($rest == 0) {
			$split = ceil(($items / $columns));
			return array_chunk($array, $split);
		} else {
			$step = floor(($items / $columns));
			$indexes = array();
			$index = 0;
			// condition to determine when to split the array
			while($index < ($items - 1)){
				if ($index == 0) {
					//set the first index where to split by the normal step + 1 item from the rest of the array
					$index = $step + ($rest > 0 ? 1 : 0);
				} else {
					$index = $index + $step + ($rest > 0 ? 1 : 0);
				}
				$rest--;
				$indexes[] = ($index - 1);
			}
			
			return $this->array_chunk_index($array, $indexes);
		}	
	}
}