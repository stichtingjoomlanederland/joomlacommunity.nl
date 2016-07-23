<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.4.2.551
 * @date        2016-07-19
 */

// no direct access
defined('_JEXEC') or die;

class WbampModelElement_Navigation
{
	/**
	 * Renders a user-selected (optional) menu module in a simple layout
	 * to provide navigation.
	 * Optionally also, links in that menu can be turned into their AMP equivalent
	 *
	 * @return mixed|string
	 */
	public function getData($currentData, $renderer)
	{
		$renderedMenu = '';
		if (WbampHelper_Runtime::$params->get('menu_location', 'hidden') != 'hidden')
		{
			// let Joomla render this menu
			$renderedMenu = $renderer->renderModule(WbampHelper_Runtime::$params->get('navigation_menu'));
		}

		// return processed content and possibly required AMP scripts
		$result = array(
			'data' => $renderedMenu,
			'scripts' => array()
		);

		return $result;
	}
}
