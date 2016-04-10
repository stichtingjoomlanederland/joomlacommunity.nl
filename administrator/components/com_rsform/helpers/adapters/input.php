<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

if (!class_exists('RSInput')) {
	class RSInput {
		public static function create($source=null, $filter=null) {
			if (is_null($filter)) {
				$filter = JFilterInput::getInstance(array(), array(), 1, 1, 0);
			}
			
			return $input = new JInput($source, array('filter' => $filter));
		}
	}
}