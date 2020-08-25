<?php
/**
* @package RSJoomla! Adapter
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');

abstract class RSCommentsAdapterGrid {
	
	public static function nav() {
		return 'nav flex-column';
	}
	
	public static function inputGroup($input, $prepend = null, $append = null) {
		$html = array();
		
		$html[] = '<div class="input-group">';
		
		if ($prepend) {
			$html[] = '<div class="input-group-prepend">';
			$html[] = strpos($prepend, 'button') !== false ? $prepend : '<span class="input-group-text">'.$prepend.'</span>';
			$html[] = '</div>';
		}
		
		$html[] = $input;
		
		if ($append) {
			$html[] = '<div class="input-group-append">';
			$html[] = strpos($append, 'button') !== false ? $append : '<span class="input-group-text">'.$append.'</span>';
			$html[] = '</div>';
		}
		
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
	
	public static function card() {
		return 'card';
	}
	
	public static function row() {
		return 'row';
	}

	public static function column($size) {
		return 'col-md-' . (int) $size;
	}

	public static function sidebar() {
		return '<div id="j-main-container" class="j-main-container">';
	}
	
	public static function renderField($label, $input, $text = false) {
		$html = array();
		
		$html[] = '<div class="control-group">';
		$html[] = '<div class="control-label">';
		$html[] = '<label>'.$label.'</label>';
		$html[] = '</div>';
		$html[] = '<div class="controls">';
		$html[] = $text ? '<div class="form-text">' : '';
		$html[] = $input;
		$html[] = $text ? '</div>' : '';
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
}