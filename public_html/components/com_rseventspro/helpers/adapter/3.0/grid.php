<?php
/**
* @package RSJoomla! Adapter
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');

abstract class RSEventsproAdapterGrid {
	
	public static function nav($edit = false) {
		return $edit ? 'nav nav-tabs' : 'nav nav-tabs nav-stacked';
	}
	
	public static function inputGroup($input, $prepend = null, $append = null) {
		$html = array();
		
		$html[] = '<div class="input-prepend input-append">';
		
		if ($prepend) {
			$html[] = strpos($prepend, 'button') !== false || strpos($prepend, 'select') !== false ? $prepend : '<span class="add-on">'.$prepend.'</span>';
		}
		
		$html[] = $input;
		
		if ($append) {
			$html[] = strpos($append, 'button') !== false || strpos($append, 'select') !== false ? $append : '<span class="add-on">'.$append.'</span>';
		}
		
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
	
	public static function card() {
		return 'well';
	}

	public static function row() {
		return 'row-fluid';
	}

	public static function column($size) {
		return 'span' . (int) $size;
	}
	
	public static function styles($styles) {
		return count($styles) > 1 ? implode(' ', $styles) : implode('',$styles);
	}

	public static function sidebar() {
		return '<div id="j-sidebar-container" class="' . static::column(2) . '">' .
			JHtmlSidebar::render() .
			'</div>' .
			'<div id="j-main-container" class="' . static::column(10) . '">';
	}
	
	public static function renderField($label, $input, $text = false) {
		$html = array();
		
		$html[] = '<div class="control-group">';
		$html[] = '<div class="control-label">';
		$html[] = strpos($label, '<label') !== false ? $label : '<label>'.$label.'</label>';
		$html[] = '</div>';
		$html[] = '<div class="controls">';
		$html[] = $text ? '<div class="rsepro-form-text">' : '';
		$html[] = $input;
		$html[] = $text ? '</div>' : '';
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
}