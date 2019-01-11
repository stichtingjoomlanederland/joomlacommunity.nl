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

/**
 * PWT Sitemap Object
 *
 * @since  1.0.0
 */
class PwtSitemap
{
	/**
	 * Array of PwtSitemapItem objects
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	public $sitemapItems;

	/**
	 * Internal counter for the amount of sitemap arrays
	 *
	 * @var    int
	 * @since  1.0.0
	 */
	public $sitemapArrays;

	/**
	 * Display Format of the sitemap, this can be XML or HTML
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	private $format;

	/**
	 * Maximum amount of items in the sitemap
	 *
	 * @var    int
	 * @since  1.0.0
	 */
	private $maxCount;

	/**
	 * Amount of items in the last array of sitemapArrays
	 *
	 * @var    int
	 * @since  1.0.0
	 */
	private $currentCount;

	/**
	 * Constructor
	 *
	 * @param   string  $format  The sitemap format (HTML/XML)
	 *
	 * @since  1.0.0
	 */
	public function __construct($format)
	{
		$this->format        = $format;
		$this->maxCount      = 50000;
		$this->currentCount  = 0;
		$this->sitemapItems  = array();
		$this->sitemapArrays = 0;
	}

	/**
	 * Add an item to the sitemap
	 *
	 * @param   mixed   $item   Array of PwtSitemapItem objects or a single object
	 * @param   string  $group  Set the group the item belongs to
	 *
	 * @since  1.0.0
	 */
	public function addItem($item, $group = '')
	{
		// Check if the group exist
		if (!isset($this->sitemapItems[$group]))
		{
			$this->sitemapItems[$group][$this->sitemapArrays] = array();
		}

		// If the amount of maximum sitemap items is exceeded, create a new array
		if (count($this->sitemapItems[$group][$this->sitemapArrays]) >= $this->maxCount)
		{
			$this->addSitemapArray($group);
		}

		// Add the new item or merge the array of new items
		if (is_array($item))
		{
			$diff = (count($item) + $this->currentCount) - $this->maxCount;

			// The maxCount limit is reached
			if ($diff > 0)
			{
				// Add first part of the array
				$this->sitemapItems[$group][$this->sitemapArrays] = array_merge($this->sitemapItems[$group][$this->sitemapArrays], array_slice($item, 0, count($item) - $diff));

				// Create new sitemap array and remaining items
				$this->addItem(array_slice($item, -$diff));
			}
			else
			{
				$this->sitemapItems[$group][$this->sitemapArrays] = array_merge($this->sitemapItems[$group][$this->sitemapArrays], $item);
				$this->currentCount                               = $this->currentCount + count($item);
			}
		}
		else
		{
			$this->sitemapItems[$group][$this->sitemapArrays][] = $item;
			$this->currentCount++;
		}
	}

	/**
	 * Get the items of the sitemap
	 *
	 * @param   int  $part  Part of the sitemap items to get
	 *
	 * @return  mixed  Array of PwtSitemapItem objects on success, false otherwise
	 *
	 * @since   1.0.0
	 */
	public function getSitemapItems($part = null)
	{
		if (is_int($part))
		{
			if ($part > $this->sitemapArrays)
			{
				return false;
			}

			return $this->sitemapItems[$part];
		}

		return $this->sitemapItems;
	}

	/**
	 * Add a new array to the internal sitemap array
	 *
	 * @param   string  $group  Set the group the item belongs to
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	private function addSitemapArray($group)
	{
		$this->sitemapItems[$group] = array();
		$this->currentCount         = 0;
		$this->sitemapArrays++;
	}

	/**
	 * Check if a sitemapindex is needed to display the sitemap
	 *
	 * @return  bool  True when a index is needed, false otherwise
	 *
	 * @since   1.0.0
	 */
	public function useSitemapIndex()
	{
		if ($this->sitemapArrays > 0)
		{
			return true;
		}

		return false;
	}
}