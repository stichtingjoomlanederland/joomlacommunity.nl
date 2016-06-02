<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.3.1.490
 * @date        2016-05-18
 */

// no direct access
defined('_JEXEC') or die;

class WbampModelElement_Socialbuttons
{
	public function getData($currentData, $renderer)
	{
		$socialData = array();
		if (WbampHelper_Runtime::$params->get('social_buttons_location', 'hidden') != 'hidden')
		{
			$socialData['types'] = WbampHelper_Runtime::$params->get('social_buttons_types');
			$socialData['theme'] = WbampHelper_Runtime::$params->get('social_buttons_theme', 'colors');
			$socialData['style'] = WbampHelper_Runtime::$params->get('social_buttons_style', 'rounded');
		}

		// return processed content and possibly required AMP scripts
		$result = array(
			'data' => $socialData,
			'scripts' => array()
		);

		return $result;
	}
}
