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

class WbampModelElement_Analytics
{
	/**
	 * Adds a Google Analytics tracking tag
	 * with various user-set options
	 *
	 */
	public function getData($displayData, $renderer)
	{
		$analyticsData = array();
		if (!WbampHelper_Runtime::$params->get('enable_analytics'))
		{
			return $analyticsData;
		}

		// build up the analytics parameters json object
		$analyticsData['vars'] = array(
			'account' => WbampHelper_Runtime::$params->get('analytics_webproperty_id')
		);
		$analyticsData['triggers'] = array(
			'wbTrackPageview' => array(
				'on' => 'visible',
				'request' => 'pageview'
			)
		);

		// optionally add social networks tracking
		if (WbampHelper_Runtime::$params->get('social_buttons_location', 'hidden') != 'hidden')
		{
			$types = WbampHelper_Runtime::$params->get('social_buttons_types');
			if (!empty($types))
			{
				foreach ($types as $type)
				{
					list($network, $action) = explode('_', $type);
					$socialData = array(
						'on' => 'click',
						'selector' => 'wbamp-button_' . $type . '_1',
						'request' => 'social',
						'vars' => array(
							'socialNetwork' => ucfirst($network),
							'socialAction' => ucfirst($action),
							'socialTarget' => $displayData['canonical']
						)
					);
					$analyticsData['triggers']['wbTrackSocialEvent_' . ucfirst($type)] = $socialData;
				}
			}
		}

		// finally link to amp analytics handler
		$result = array(
			'data' => $analyticsData,
			'scripts' => array(
				'amp-analytics' => sprintf(WbampModel_Renderer::AMP_SCRIPTS_PATTERN, 'analytics', WbampModel_Renderer::AMP_SCRIPTS_VERSION)
			)
		);

		return $result;
	}
}
