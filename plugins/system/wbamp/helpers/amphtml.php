<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.6.0.607
 * @date        2016-10-31
 */
defined('_JEXEC') or die;

/**
 * Route helper
 *
 */
class WbampHelper_Amphtml
{
	static private $slidingMenuStyles = array('slide', 'slide-right');

	/**
	 * Render an amp sidebar, based on user set params
	 *
	 * @param array $displayData
	 * @param array $menuStyleAllowed list of allowed menus in that position
	 *
	 * @return string
	 */
	static public function getRenderedMenu($displayData, $menuStyleAllowed)
	{
		$renderedMenu = '';
		$menuStyle = strtolower($displayData['params']->get('menu_style', 'slide'));
		if (!empty($menuStyleAllowed) && !in_array($menuStyle, $menuStyleAllowed))
		{
			return $renderedMenu;
		}
		switch ($menuStyle)
		{
			case 'slide':
				$displayData['navigation_menu_side'] = 'left';
				$renderedMenu = ShlMvcLayout_Helper::render('wbamp.tags.sidebar', $displayData, WbampHelper_Runtime::$layoutsBasePaths);
				break;
			case 'slide-right':
				$displayData['navigation_menu_side'] = 'right';
				$renderedMenu = ShlMvcLayout_Helper::render('wbamp.tags.sidebar', $displayData, WbampHelper_Runtime::$layoutsBasePaths);
				break;
			case 'default':
				$renderedMenu = ShlMvcLayout_Helper::render('wbamp.menu_' . $menuStyle, $displayData, WbampHelper_Runtime::$layoutsBasePaths);
				break;
		}

		return $renderedMenu;
	}

	/**
	 * Check if a menu style is a sliding one, based on hardcoded list
	 * If no style provided, user selected style is read from params
	 *
	 * @param JRegistry $params global user set params object
	 * @param string    $menuStyle
	 *
	 * @return bool
	 */
	static public function isSlidingMenu($params, $menuStyle = '')
	{
		$menuStyle = empty($menuStyle) ? $params->get('menu_style', 'slide') : $menuStyle;

		return in_array($menuStyle, self::$slidingMenuStyles);
	}

	/**
	 * Validate an HTML tag dimension (width or height)
	 *
	 * - must be numeric (no % or other sign)
	 * - except when set in px, ie 250px is valid
	 *
	 * @param mixed $dimension
	 * @param int   $default value if invalid
	 *
	 * @return int
	 */
	public static function validateDimension($dimension, $default = 0)
	{
		$validated = self::isValidDimension($dimension) ? $dimension : $default;

		return $validated;
	}

	/**
	 * Finds out if an HTML tag dimension is valid (width or height)
	 *
	 * - must be numeric (no % or other sign)
	 * - except when set in px, ie 250px is valid
	 *
	 * @param mixed $dimension
	 *
	 * @return bool
	 */
	public static function isValidDimension($dimension)
	{
		$validated = is_numeric($dimension);
		if (!$validated && substr($dimension, -2) == 'px')
		{
			// try again without trailing px
			$dimension = substr($dimension, 0, -2);
			$validated = is_numeric($dimension);
		}

		return $validated;
	}
}
