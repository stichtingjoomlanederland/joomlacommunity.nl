<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

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
	 * Static method to route a url to a SEF Url. It also decides if the url should be with https or not
	 *
	 * @param   string $url Url to route
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
		if (isset($parsedUrl['scheme']) && (($parsedUrl['scheme'] === 'https' || $parsedUrl['scheme'] === 'http' || strpos($url, 'www') === 0)))
		{
			return $url;
		}

		// Lets Route the internal URL
		return Uri::getInstance(Uri::base())->toString(array('scheme', 'host')) . Route::_($url);
	}
}
