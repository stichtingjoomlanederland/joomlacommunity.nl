<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.5.0.585
 * @date        2016-08-25
 */

// no direct access
defined('_JEXEC') or die;

class WbampModelElement_Analytics
{
	const EVENT_TRACKING_RULE_SEPARATOR = '|';

	/**
	 * Adds a Google Analytics tracking tag
	 * with various user-set options
	 *
	 */
	public function getData($pageData, $renderer)
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
		$analyticsData = $this->socialNetworksTracking($analyticsData, $pageData);

		// optionally add social other events tracking
		$analyticsData = $this->eventsTracking($analyticsData, $pageData);

		// finally link to amp analytics handler
		$result = array(
			'data' => $analyticsData,
			'scripts' => array(
				'amp-analytics' => sprintf(WbampModel_Renderer::AMP_SCRIPTS_PATTERN, 'analytics', WbampModel_Renderer::AMP_SCRIPTS_VERSION)
			)
		);

		return $result;
	}

	/**
	 * Optionally adds social netowrks buttons tracking instructions to the Analytics json-ld snippet
	 *
	 * @param array $analyticsData Current set of analytics data
	 * @param array $pageData Current available data about the page being rendered
	 * @return mixed
	 */
	private function socialNetworksTracking($analyticsData, $pageData)
	{
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
							'socialTarget' => $pageData['canonical']
						)
					);
					$analyticsData['triggers']['wbTrackSocialEvent_' . ucfirst($type)] = $socialData;
				}
			}
		}

		return $analyticsData;
	}

	/**
	 * Optionally adds events tracking instructions to the Analytics json-ld snippet
	 *
	 * @param array $analyticsData Current set of analytics data
	 * @param array $pageData Current available data about the page being rendered
	 * @return mixed
	 */
	private function eventsTracking($analyticsData, $pageData)
	{
		$eventsTrackingDefinitions = ShlSystem_Strings::stringToCleanedArray(WbampHelper_Runtime::$params->get('analytics_tracked_events', ''), "\n");
		if (!empty($eventsTrackingDefinitions))
		{
			if ($eventsTrackingDefinitions[0] == '-')
			{
				// globally disabled, by using a - as the first line
				return $analyticsData;
			}

			foreach ($eventsTrackingDefinitions as $eventsTrackingDefinition)
			{
				if (';' == JString::substr($eventsTrackingDefinition, 0, 1))
				{
					// line starts with a ;. It's a comment, skip
					continue;
				}
				$def = ShlSystem_Strings::stringToCleanedArray($eventsTrackingDefinition, self::EVENT_TRACKING_RULE_SEPARATOR);
				$uniqueId = md5($eventsTrackingDefinition);
				$valid = false;
				if (WbampHelper_Route::pathRuleMatch($def[0], $pageData['amp_path']))
				{
					switch ($def[1])
					{
						// click, css_selector, eventCategory, eventAction [,eventLabel] [,eventValue]
						case 'click':
							if (count($def) >= 5)
							{
								// build an id, based on the event action
								$vars = array(
									'eventCategory' => $def[3],
									'eventAction' => $def[4]
								);
								if (!empty($def[5]))
								{
									$vars['eventLabel'] = $def[5];
								}
								if (!empty($def[6]))
								{
									$vars['eventValue'] = (int) $def[6];
								}

								$eventData = array(
									'on' => 'click',
									'selector' => $def[2],
									'request' => 'event',
									'vars' => $vars
								);

								$valid = true;
							}
							break;
						// scroll, verticalBoundaries [,horizontalBoundaries]
						case 'scroll':
							if (count($def) >= 6)
							{
								// scrollSpec must be an array
								$bits = ShlSystem_Strings::stringToCleanedArray(JString::trim($def[2], '[]'), ',');
								$scrollSpec = array(
									'verticalBoundaries' => $bits
								);
								$bits = ShlSystem_Strings::stringToCleanedArray(JString::trim($def[3], '[]'), ',');
								$scrollSpec['horizontalBoundaries'] = $bits;

								$vars = array(
									'eventCategory' => $def[4],
									'eventAction' => $def[5]
								);
								if (!empty($def[6]))
								{
									$vars['eventLabel'] = $def[6];
								}
								if (!empty($def[7]))
								{
									$vars['eventValue'] = (int) $def[7];
								}

								$eventData = array(
									'on' => 'scroll',
									'request' => 'event',
									'scrollSpec' => $scrollSpec,
									'vars' => $vars
								);
								$valid = true;
							}
							break;
					}
				}

				// append to list of triggers
				if ($valid)
				{
					$analyticsData['triggers']['wbampTrackEvent_' . $uniqueId] = $eventData;
				}
			}
		}

		return $analyticsData;
	}

}
