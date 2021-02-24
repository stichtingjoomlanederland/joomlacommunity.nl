<?php
/**
* @package RSJoomla! Adapter
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');

abstract class RSEventsproAdapterGrid {
	
	public static function nav($edit = false) {
		return $edit ? 'nav nav-tabs flex-column' : 'nav flex-column';
	}
	
	public static function inputGroup($input, $prepend = null, $append = null) {
		$html = array();
		
		$html[] = '<div class="input-group">';
		
		if ($prepend) {
			$html[] = strpos($prepend, 'button') !== false || strpos($prepend, 'select') !== false ? $prepend : '<span class="input-group-text">'.$prepend.'</span>';
		}
		
		$html[] = $input;
		
		if ($append) {
			$html[] = strpos($append, 'button') !== false || strpos($append, 'select') !== false ? $append : '<span class="input-group-text">'.$append.'</span>';
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
	
	public static function styles($styles) {
		foreach ($styles as $i => $style) {
			if ($style == 'unstyled') $styles[$i] = 'list-unstyled';
			if ($style == 'inline') $styles[$i] = 'list-inline';
			if ($style == 'pull-left') $styles[$i] = 'float-start';
			if ($style == 'pull-right') $styles[$i] = 'float-end';
			if ($style == 'center') $styles[$i] = 'text-center';
			if ($style == 'muted') $styles[$i] = 'text-muted';
			if ($style == 'bar') $styles[$i] = 'progress-bar';
			if ($style == 'btn') $styles[$i] = 'btn btn-secondary';
			if ($style == 'btn-small') $styles[$i] = 'btn-sm';
		}
		
		return count($styles) > 1 ? implode(' ', $styles) : implode('',$styles);
	}

	public static function sidebar() {
		return '<div id="j-main-container" class="j-main-container">';
	}
	
	public static function renderField($label, $input, $text = false, $description = null) {
		$html = array();
		
		$html[] = '<div class="control-group">';
		$html[] = '<div class="control-label">';
		$html[] = strpos($label, '<label') !== false ? $label : '<label>'.$label.'</label>';
		$html[] = '</div>';
		$html[] = '<div class="controls">';
		$html[] = $text ? '<div class="form-text">' : '';
		$html[] = $input;
		$html[] = $text ? '</div>' : '';
		$html[] = $description ? '<small class="form-text text-muted">'.$description.'</small>' : '';
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
}