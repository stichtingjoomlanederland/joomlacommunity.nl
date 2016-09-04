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

class WbampModelElement_Ad
{
	private $scripts;
	private $renderer;

	/**
	 * Finds if any ads is to be displayed, so that the proper script is included
	 *
	 * @return mixed|string
	 */
	public function getData($currentData, $renderer)
	{
		$this->scripts = array();
		$this->renderer = $renderer;

		// if user set params to include adds at top or bottom, insert script
		// rendering will happen later
		if (WbampHelper_Runtime::$params->get('ads_location', 'hidden') != 'hidden')
		{
			$this->scripts = array(
				// Comment out next line for a temp fix for issue in Google AMP validator https://github.com/ampproject/amphtml/issues/3802
				'amp-ad' => sprintf(WbampModel_Renderer::AMP_SCRIPTS_PATTERN, 'ad', WbampModel_Renderer::AMP_SCRIPTS_VERSION)
			);
		}

		// search for embed tags in content
		if (!empty($currentData['main_content']))
		{
			$regex = '#{wbamp\-embed([^}]*)}#ium';
			$processedContent = preg_replace_callback($regex, array($this, '_processEmbededTag'), $currentData['main_content']);
		}

		// return processed content and possibly required AMP scripts
		$result = array(
			'data' => $processedContent,
			'scripts' => $this->scripts
		);

		return $result;
	}

	/**
	 * Preg replace callback, identify tags to replace
	 * them with the AMP version
	 *
	 * @param $match
	 * @return string
	 */
	private function _processEmbededTag($match)
	{
		// detect type we can handle
		if (!empty($match[1]))
		{
			$attributes = JUtility::parseAttributes($match[1]);
			$adType = empty($attributes['type']) ? '' : $attributes['type'];
			if ('ad' == $adType)
			{
				// proceed to extract tag and replace it
				unset($attributes['type']);
				return $this->_embedTag($attributes);
			}
		}

		return $match[0];
	}

	/**
	 * Builds an amp-ad tag, from a shortcode in content
	 * in one of 2 forms: with or without attributes
	 * @param $attributes
	 * @return string
	 */
	private function _embedTag($attributes)
	{
		$tag = '';

		if (empty($attributes))
		{
			// no attributes, we use the default ad type and params set by user
			$network = strtolower(WbampHelper_Runtime::$params->get('ads_network', ''));
			if (!empty($network))
			{
				$tag = ShlMvcLayout_Helper::render(
					'wbamp.ads-networks.' . $network,
					array(
						'params' => WbampHelper_Runtime::$params,
						'ad-id' => md5(mt_rand() . $network)
					),
					WbampHelper_Runtime::$layoutsBasePaths
				);
			}
		}
		else
		{
			// rename the ad-type to type, could not use it earlier
			// as it conflicts with our wbamp-embed syntax
			if (!empty($attributes['ad-type']))
			{
				$attributes['type'] = $attributes['ad-type'];
				unset($attributes['ad-type']);
			}

			// we have some attributes, use them directly to build the amp-ad tag
			$tag = ShlMvcLayout_Helper::render('wbamp.ads-networks.wbamp_ad_tag', array('attributes' => $attributes), WbampHelper_Runtime::$layoutsBasePaths);
		}

		if (!empty($tag) && empty($this->scripts))
		{
			// finally add script to execute the tag
			$this->scripts =
				array(
					// Comment out next line for a temp fix for issue in Google AMP validator https://github.com/ampproject/amphtml/issues/3802
					'amp-ad' => sprintf(WbampModel_Renderer::AMP_SCRIPTS_PATTERN, 'ad', WbampModel_Renderer::AMP_SCRIPTS_VERSION)
				);
		}
		return $tag;
	}
}
