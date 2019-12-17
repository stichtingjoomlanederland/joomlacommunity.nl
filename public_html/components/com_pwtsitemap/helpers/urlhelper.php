<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * URLHelper class
 *
 * @since  1.0.0
 */
class PwtSitemapUrlHelper
{
	/**
	 * The current domain, this is filled via the component params
	 *
	 * @var    string
	 * @since  1.4.0
	 */
	static public $base;

	/**
	 * Static method to route a url to a SEF Url. It also decides if the url should be with https or not
	 *
	 * @param   string  $url  Url to route
	 *
	 * @return  string
	 *
	 * @since   1.0.0
	 */
	public static function getURL($url)
	{
		// Parse URL for scheme
		$parsedUrl = parse_url($url);

		// Leave external links as provided
		if (isset($parsedUrl['scheme']) && ($parsedUrl['scheme'] === 'https' || $parsedUrl['scheme'] === 'http' || strpos($url, 'www') === 0))
		{
			return $url;
		}

		// If we build from the CLI, we need a frontend url which also has the host
		if (PHP_SAPI === 'cli')
		{
			$currentPath = JPATH_BASE . '/cli';

			if (method_exists(Joomla\CMS\Router\Route::class, 'link'))
			{
				// We no longer have to check this, because it's checked at the start of the CLI run
				if (!isset(self::$base))
				{
					$params     = ComponentHelper::getParams('com_pwtsitemap');
					self::$base = rtrim($params->get('domain'), '/');
				}

				// Strip current path from the link
				$link = str_replace($currentPath, "", Route::link('site', $url));

				return self::$base . '/' . ltrim($link, '/');
			}

			return $url;
		}

		// Lets Route the internal URL
		return Uri::getInstance(Uri::base())->toString(['scheme', 'host']) . Route::_($url);
	}
}
