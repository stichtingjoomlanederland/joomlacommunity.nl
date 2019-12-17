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
	public $sitemapItems = [];

	/**
	 * Internal counter for the amount of sitemap arrays
	 *
	 * @var    integer
	 * @since  1.0.0
	 */
	public $sitemapArrays = 0;

	/**
	 * Maximum amount of items in the sitemap
	 *
	 * @var    integer
	 * @since  1.0.0
	 */
	private $maxCount = 50000;

	/**
	 * Amount of items in the last array of sitemapArrays
	 *
	 * @var    integer
	 * @since  1.0.0
	 */
	private $currentCount = 0;

	/**
	 * Add an item to the sitemap
	 *
	 * @param   mixed   $item   Array of PwtSitemapItem objects or a single object
	 * @param   string  $group  Set the group the item belongs to
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function addItem($item, $group = '')
	{
		// Check if the group exist
		if (!isset($this->sitemapItems[$group]))
		{
			$this->sitemapArrays                              = 0;
			$this->currentCount                               = 0;
			$this->sitemapItems[$group][$this->sitemapArrays] = [];
		}

		// Get the count of the last sitemapArray
		$sitemapArrayCount = count($this->sitemapItems[$group][$this->sitemapArrays]);

		// If the amount of maximum sitemap items is exceeded, create a new array
		if ($sitemapArrayCount >= $this->maxCount)
		{
			$this->addSitemapArray($group);
		}

		// Add the new item or merge the array of new items
		if (is_array($item))
		{
			// Get the item count
			$itemCount = count($item);

			// Get the difference to reach maxCount
			$diff = ($itemCount + $this->currentCount) - $this->maxCount;

			// The maxCount limit is reached
			if ($diff > 0)
			{
				// Check if last sitemapArray is not full
				$cut = $this->maxCount - $this->currentCount;

				if ($diff < $this->maxCount && $sitemapArrayCount !== $this->maxCount)
				{
					$cut = count($item) - $diff;
				}

				// Slice the item array to add it to the array
				$slice = array_slice($item, 0, $cut);

				// If sitemapArray is not empty, merge it
				$this->sitemapItems[$group][$this->sitemapArrays] = $slice;

				if ($sitemapArrayCount > 0 && isset($this->sitemapItems[$group][$this->sitemapArrays]))
				{
					$this->sitemapItems[$group][$this->sitemapArrays] = array_merge($this->sitemapItems[$group][$this->sitemapArrays], $slice);
				}

				// Create new sitemap array and remaining items
				$this->addItem(array_slice($item, -$diff), $group);
			}
			else
			{
				$this->sitemapItems[$group][$this->sitemapArrays] = array_merge($this->sitemapItems[$group][$this->sitemapArrays], $item);

				$this->currentCount += $itemCount;
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

		if (!isset($this->sitemapItems[0]))
		{
			return reset($this->sitemapItems)[0];
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
		$this->sitemapItems[$group] = isset($this->sitemapItems[$group]) ? $this->sitemapItems[$group] : [];
		$this->currentCount         = 0;
		$this->sitemapArrays++;
	}

	/**
	 * Check if a sitemapindex is needed to display the sitemap
	 *
	 * @return  boolean  True when a index is needed, false otherwise
	 *
	 * @since   1.0.0
	 */
	public function useSitemapIndex()
	{
		return $this->sitemapArrays > 0;
	}
}
