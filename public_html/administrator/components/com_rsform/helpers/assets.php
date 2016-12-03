<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProAssetsEvent extends JEvent
{
	public function onBeforeRender() {
		RSFormProAssets::$replace = true;
	}
	
	public function onAfterRender() {
		RSFormProAssets::render();
	}
}

JFactory::getApplication()->registerEvent('onAfterRender', 'RSFormProAssetsEvent');

class RSFormProAssets
{
	// This flags checks if we need to run after the onAfterRender() event.
	public static $replace = false;
	
	public static $scripts 			= array();
	public static $inlineScripts 	= '';
	public static $styles 			= array();
	public static $inlineStyles 	= '';
	public static $customTags 		= '';

	// Holds the resources already loaded (CSS and JS) so they will not be loaded twice by the System Plugin
	public static $added = array();
	
	public static function addScript($path) {
		if (self::$replace) {
			self::$scripts[$path] = 1;
		} else {
			if (method_exists(JFactory::getDocument(), 'addScript')) {
				self::$added[$path] = 1;
				JFactory::getDocument()->addScript($path);
			}
		}
	}
	
	public static function addScriptDeclaration($script) {
		if (self::$replace) {
			if ($script) {
				self::$inlineScripts .= chr(13).$script;
			}
		} else {
			if (method_exists(JFactory::getDocument(), 'addScriptDeclaration')) {
				JFactory::getDocument()->addScriptDeclaration($script);
			}
		}
	}
	
	public static function addStyleSheet($path) {
		if (self::$replace) {
			self::$styles[$path] = 1;
		} else {
			if (method_exists(JFactory::getDocument(), 'addStyleSheet')) {
				self::$added[$path] = 1;
				JFactory::getDocument()->addStyleSheet($path);
			}
		}
	}
	
	public static function addStyleDeclaration($style) {
		if (self::$replace) {
			if ($style) {
				self::$inlineStyles .= chr(13).$style;
			}
		} else {
			if (method_exists(JFactory::getDocument(), 'addStyleDeclaration')) {
				JFactory::getDocument()->addStyleDeclaration($style);
			}
		}
	}
	
	public static function addCustomTag($custom) {
		if (self::$replace) {
			if ($custom) {
				self::$customTags .= chr(13).$custom;
			}
		} else {
			if (method_exists(JFactory::getDocument(), 'addCustomTag')) {
				JFactory::getDocument()->addCustomTag($custom);
			}
		}
	}
	
	public static function render() {
		if (self::$replace) {
			$body 		= self::getBody();
			$newHead 	= '';
			
			if (self::$scripts) {
				foreach (self::$scripts as $src => $tmp) {
					if (!isset(self::$added[$src])) {
						$newHead .= self::createScript($src);
					}
				}
				// Reset
				self::$scripts = array();
			}
			
			if (self::$styles) {
				foreach (self::$styles as $src => $tmp) {
					if (!isset(self::$added[$src])) {
						$newHead .= self::createStyleSheet($src);
					}
				}
				// Reset
				self::$styles = array();
			}
			
			if (self::$inlineStyles) {
				$newHead .= self::createStyleDeclaration(self::$inlineStyles);
				// Reset
				self::$inlineStyles = '';
			}
			
			if (self::$inlineScripts) {
				$newHead .= self::createScriptDeclaration(self::$inlineScripts);
				// Reset
				self::$inlineScripts = '';
			}
			
			if (self::$customTags) {
				$newHead .= self::$customTags."\n";
				// Reset
				self::$customTags = '';
			}
			
			if ($newHead) {
				$body = str_replace('</head>', $newHead.'</head>', $body);
				self::setBody($body);
			}
		}
	}

	protected static function getBody() {
		$jversion = new JVersion;
		if ($jversion->isCompatible('3.0')) {
			return JFactory::getApplication()->getBody();
		} else {
			return JResponse::getBody();
		}
	}

	protected static function setBody($body) {
		$jversion = new JVersion;
		if ($jversion->isCompatible('3.0')) {
			return JFactory::getApplication()->setBody($body);
		} else {
			return JResponse::setBody($body);
		}
	}

	protected static function isHTML5() {
		static $result;
		if ($result === null) {
			if (is_callable(array(JFactory::getDocument(), 'isHtml5'))) {
				$result = JFactory::getDocument()->isHtml5();
			} else {
				$result = false;
			}
		}

		return $result;
	}

	protected static function createScript($src) {
		$html = '<script';
		if (!self::isHTML5()) {
			$html .= ' type="text/javascript"';
		}
		$html .= ' src="' . $src . '"></script>' . "\n";
		return $html;
	}

	protected static function createScriptDeclaration($inlineScripts) {
		$html = '<script';
		if (!self::isHTML5()) {
			$html .= ' type="text/javascript"';
		}
		$html .= '>'. "\n". $inlineScripts. "\n". '</script>'. "\n";

		return $html;
	}

	protected static function createStyleSheet($src) {
		$html = '<link rel="stylesheet"';
		if (!self::isHTML5()) {
			$html .= ' type="text/css"';
		}
		$html .= ' href="' . $src . '" />' . "\n";
		return $html;
	}

	protected static function createStyleDeclaration($inlineStyles) {
		$html = '<style';
		if (!self::isHTML5()) {
			$html .= ' type="text/css"';
		}
		$html .= '>'. "\n". $inlineStyles. "\n". '</style>'. "\n";

		return $html;
	}
}