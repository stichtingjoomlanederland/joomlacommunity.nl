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

use Joomla\CMS\Component\Router\RouterBase;

/**
 * Routing class for PWT Sitemap
 *
 * @since  1.0.0
 */
class PwtSitemapRouter extends RouterBase
{
	/**
	 * Build the route for PWT Sitemap
	 *
	 * @param   array  $query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   1.0.0
	 */
	public function build(&$query)
	{
		$segments = [];

		// Check for view
		if (!isset($query['view']))
		{
			// We need to have a view in the query or it is an invalid URL
			return $segments;
		}

		// Check for pars (means the route is a multi-sitemap)
		if (isset($query['part']))
		{
			$segments[] = 'part-' . $query['part'];

			unset($query['part']);
		}

		// Handle view
		unset($query['view'], $query['layout'], $query['format']);

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  $segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   1.0.0
	 */
	public function parse(&$segments)
	{
		$vars = [];

		if (isset($segments[0]))
		{
			list($fds, $id) = explode('-', $segments[0]);

			$vars['part']   = $id;
			$vars['view']   = $this->menu->getActive()->query['view'];
			$vars['format'] = 'xml';
		}

		return $vars;
	}
}
