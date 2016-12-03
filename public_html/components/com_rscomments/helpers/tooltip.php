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
		return self::j3() ? 'hasTooltip' : 'hasTip';
	}

	// Prepare the tooltip text
	public static function tooltipText($title, $content = '') {
		static $version;
		if (!$version) {
			$version = new JVersion();
		}
		
		if ($version->isCompatible('3.1.2')) {
			return JHtml::tooltipText($title, $content, 0, 0);
		} else {
			return $title.'::'.$content;
		}
	}

	// Load tooltip
	public static function tooltipLoad() {
		if (self::j3()) {
			$jversion = new JVersion();
			
			if ($jversion->isCompatible('3.3')) {
				JHtml::_('behavior.core');
			}
			
			JHtml::_('bootstrap.tooltip');
		} else {
			JHtml::_('behavior.tooltip');
		}
	}
}

// Load tooltips
RSTooltip::tooltipLoad();