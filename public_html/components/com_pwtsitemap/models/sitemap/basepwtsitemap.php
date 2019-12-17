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

/**
 * PWT Sitemap Interface
 *
 * @since  1.0.0
 */
abstract class BasePwtSitemap
{
	/**
	 * Add an item to the sitemap
	 *
	 * @param   mixed  $item  Array of PwtSitemapItem objects or a single object
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	abstract public function addItem($item);

	/**
	 * Get the items of the sitemap
	 *
	 * @param   int  $part  Part of the sitemap items to get
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	abstract public function getSitemapItems($part = null);
}
