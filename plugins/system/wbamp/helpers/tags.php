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

defined('_JEXEC') or die();

class WbampHelper_Tags
{
	public static function getYoutubeUrlData($match)
	{
		$url = $match[1];
		if (strpos($url, '&') !== false)
		{
			list($url) = explode('&', $url);
		}
		$displayData = array(
			'params' => WbampHelper_Runtime::$params,
			'joomla_config' => WbampHelper_Runtime::$joomlaConfig,
			'data' => array(
				'type' => 'youtube',
				'videoid' => empty($url) ? '' : $url
			)
		);

		return $displayData;
	}

	public static function getDailymotionUrlData($match)
	{
		$displayData = array(
			'params' => WbampHelper_Runtime::$params,
			'joomla_config' => WbampHelper_Runtime::$joomlaConfig,
			'data' => array(
				'type' => 'youtube',
				'videoid' => empty($match[1]) ? 0 : $match[1]
			)
		);

		return $displayData;
	}
	public static function getTwitterUrlData($match)
	{
		$displayData = array(
			'params' => WbampHelper_Runtime::$params,
			'joomla_config' => WbampHelper_Runtime::$joomlaConfig,
			'data' => array(
				'type' => 'twitter',
				'tweetid' => empty($match[5]) ? 0 : $match[5]
			)
		);

		return $displayData;
	}

	public static function getInstagramUrlData($match)
	{
		$displayData = array(
			'params' => WbampHelper_Runtime::$params,
			'joomla_config' => WbampHelper_Runtime::$joomlaConfig,
			'data' => array(
				'type' => 'instagram',
				'shortcode' => empty($match[1]) ? '' : $match[1]
			)
		);

		return $displayData;
	}

	public static function getFacebookUrlData($match)
	{
		$displayData = array(
			'params' => WbampHelper_Runtime::$params,
			'joomla_config' => WbampHelper_Runtime::$joomlaConfig,
			'data' => array(
				'type' => 'facebook',
				'user' => empty($match[1]) ? 0 : $match[1],
				'subtype' => empty($match[2]) ? 0 : $match[2],
				'id' => empty($match[3]) ? 0 : $match[3]
			)
		);

		return $displayData;
	}
}
