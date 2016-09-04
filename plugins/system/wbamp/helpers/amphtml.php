<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.5.0.585
 * @date        2016-08-25
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
	 * @param string $menuStyle
	 * @return bool
	 */
	static public function isSlidingMenu($params, $menuStyle = '')
	{
		$menuStyle = empty($menuStyle) ? $params->get('menu_style', 'slide') : $menuStyle;

		return in_array($menuStyle, self::$slidingMenuStyles);
	}
}
