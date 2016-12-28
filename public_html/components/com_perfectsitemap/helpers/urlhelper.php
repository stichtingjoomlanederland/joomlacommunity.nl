<?php
/**
 * @package     Perfect_Sitemap
 * @subpackage  com_perfectsitemap
 *
 * @copyright   Copyright (C) 2016 Perfect Web Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @since       1.4.2
 */

defined('_JEXEC') or die;

/**
 * URLHelper class
 *
 * @since  1.4.2
 */
class URLHelper
{
	/**
	 * Static method to route a url to a SEF Url. It also decides if the url should  me with https or not
	 *
	 * @param   string  $url  Url to route
	 *
	 * @return  string
	 *
	 * @since  1.4.2
	 */
	public static function getURL($url)
	{
		$isSSL = (JFactory::getConfig()->get('force_ssl') == 2);

		return JRoute::_($url, true, ($isSSL ?: -1));
	}
}
