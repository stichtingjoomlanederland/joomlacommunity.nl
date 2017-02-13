<?php
/**
 * @package     Perfect_Sitemap
 * @subpackage  plg_perfectsitemap_content
 *
 * @copyright   Copyright (C) 2017 Perfect Web Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * PerfectSitemapItem
 *
 * @since  2.0.0
 */
class PerfectSitemapItem
{
	/**
	 * Sitemap item title
	 *
	 * @var  string
	 */
	public $title;

	/**
	 * Sitemap item link
	 *
	 * @var string
	 */
	public $link;

	/**
	 * Sitemap item level
	 *
	 * @var  int
	 */
	public $level;

	/**
	 * Sitemap item modified date
	 *
	 * @var  int
	 */
	public $modified;

	/**
	 * PerfectSitemapItem constructor.
	 *
	 * @param  string $title    Title
	 * @param  string $link     URL
	 * @param  int    $level    Level
	 * @param  mixed  $modified Modification date
	 */
	public function __construct($title, $link, $level, $modified = null)
	{
		$this->title = $title;
		$this->link  = PerfectSitemapUrlHelper::getURL($link);
		$this->level = $level;

		if (!empty($modified))
		{
			$this->modified = JHtml::_('date', $modified, 'Y-m-d');
		}
	}
}