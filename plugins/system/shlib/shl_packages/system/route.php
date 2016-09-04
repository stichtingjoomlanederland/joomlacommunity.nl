<?php
/**
 * Shlib - programming library
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier 2016
 * @package      shlib
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      0.3.1.580
 * @date        2016-08-25
 */

defined('_JEXEC') or die;

/**
 * Route helper
 *
 */
class ShlSystem_Route
{

	public static $canonicalDomain = null;

	/**
	 * Turn a relative-to-page URL into an absolute one, using the site canonical domain if any
	 *
	 * @param $url
	 * @param bool $forceDomain if URL is already absolute, we won't fully qualify it with a domain (if relativen we
	 * still prepend the full domain
	 * @param null $isAdmin If === true or === false, disable using JApplication::isAdmin, for testing
	 *
	 * @return string
	 */
	public static function absolutify($url, $forceDomain = false, $isAdmin = null)
	{
		// is it already absolute?
		if (self::isFullyQUalified($url))
		{
			return $url;
		}

		if (substr($url, 0, 1) == '/')
		{
			if ($forceDomain)
			{
				$url = self::getCanonicalDomain() . $url;
			}

			return $url;
		}

		// relative URL, make it fully qualified
		$base = JUri::base();
		if ($isAdmin === true || ($isAdmin !== false && JFactory::getApplication()->isAdmin()))
		{
			$base = JString::substr($base, 0, -14);
		}

		return $base . $url;
	}

	/**
	 * Finds if a URL is fully qualified, ie starts with a scheme
	 * Protocal-relative URLs are considered fully qualified
	 *
	 * @param $url
	 * @return bool
	 */
	public static function isFullyQUalified($url)
	{
		$isFullyQualified = substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://' || substr($url, 0, 2) == '//';
		return $isFullyQualified;
	}

	/**
	 * Builds and return the canonical domain of the page, taking into account
	 * the optional canonical domain in SEF plugin, and including the base path, if any
	 * Can be called from admin side, to get front end links, by passing true as param
	 *
	 *
	 * @param null $isAdmin If === true or === false, disable using JApplication::isAdmin, for testing
	 * @return null|string
	 */
	public static function getCanonicalDomain($isAdmin = null)
	{
		if (is_null(self::$canonicalDomain))
		{
			$sefPlgParams = ShlSystem_Joomla::getExtensionParams('plg_sef', array('type' => 'plugin', 'folder' => 'system', 'element' => 'sef'));
			$canonicalParam = JString::trim($sefPlgParams->get('domain', ''));
			if (empty($canonicalParam))
			{
				$base = JUri::base();
				if ($isAdmin === true || ($isAdmin !== false && JFactory::getApplication()->isAdmin()))
				{
					$base = JString::substr($base, 0, -14);
				}
				self::$canonicalDomain = $base;
			}
			else
			{
				self::$canonicalDomain = $canonicalParam;
			}
			self::$canonicalDomain = JString::trim(self::$canonicalDomain, '/');
		}

		return self::$canonicalDomain;
	}
}
