<?php
/**
* @package RSJoomla! Adapter
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');

abstract class RSCommentsAdapterGrid {
	
	public static function nav() {
		return 'nav nav-tabs nav-stacked';
	}
	
	public static function inputGroup($input, $prepend = null, $append = null) {
		$html = array();
		
		$html[] = '<div class="input-prepend input-append">';
		
		if ($prepend) {
			$html[] = strpos($prepend, 'button') !== false ? $prepend : '<span class="add-on">'.$prepend.'</span>';
		}
		
		$html[] = $input;
		
		if ($append) {
			$html[] = strpos($append, 'button') !== false ? $append : '<span class="add-on">'.$append.'</span>';
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
		$html[] = '<label>'.$label.'</label>';
		$html[] = '</div>';
		$html[] = '<div class="controls">';
		$html[] = $text ? '<div class="rscomments-form-text">' : '';
		$html[] = $input;
		$html[] = $text ? '</div>' : '';
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
}