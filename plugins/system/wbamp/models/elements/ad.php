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

class WbampModelElement_Ad
{
	/**
	 * Finds if any ads is to be displayed, so that the proper script is included
	 *
	 * @return mixed|string
	 */
	public function getData($currentData, $renderer)
	{
		$scripts = array();
		if (WbampHelper_Runtime::$params->get('ads_location', 'hidden') != 'hidden')
		{
			$scripts = array(
				// Comment out next line for a temp fix for issue in Google AMP validator https://github.com/ampproject/amphtml/issues/3802
				'amp-ad' => sprintf(WbampModel_Renderer::AMP_SCRIPTS_PATTERN, 'ad', WbampModel_Renderer::AMP_SCRIPTS_VERSION)
			);
		}

		// return processed content and possibly required AMP scripts
		$result = array(
			'data' => '',
			'scripts' => $scripts
		);

		return $result;
	}
}
