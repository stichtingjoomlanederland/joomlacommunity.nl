<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

abstract class RSTooltip {
	
	public static function j3() {
		return version_compare(JVERSION, '3.0', '>=');
	}

	// Get the tooltip class
	public static function tooltipClass() {
		return 'hasTooltip';
	}

	// Prepare the tooltip text
	public static function tooltipText($title, $content = '') {
		return JHtml::tooltipText($title, $content, 0, 0);
	}

	// Load tooltip
	public static function tooltipLoad() {	
		JHtml::_('behavior.core');
		JHtml::_('bootstrap.tooltip');
	}
}

// Load tooltips
RSTooltip::tooltipLoad();