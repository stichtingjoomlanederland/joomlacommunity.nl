<?php
/**
 * @package     Perfect_Sitemap
 * @subpackage  plg_perfectsitemap_easydiscuss
 *
 * @copyright   Copyright (C) 2017 Perfect Web Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Perfect Sitemap Easydiscuss Plugin
 *
 * @since  1.0.0
 */
class PlgPerfectSitemapEasydiscuss extends JPlugin
{
	/**
	 * Run for every menuitem passed by Perfect Sitemap
	 *
	 * @param   StdClass $item   Menu items
	 * @param   string   $format Sitemap format that is rendered
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function onPerfectSitemapBuildSitemap($item, $format)
	{
		// Empty sitemap items array
		$sitemap_items = array();

		// Call methods
		$this->getCategories($item, $sitemap_items);
		$this->getTags($item, $sitemap_items);

		return $sitemap_items;
	}

	/**
	 * Get all EasyDisucss categories for category menu items
	 *
	 * @param   stdClass  $item           Menu item
	 * @param   array     &$sitemap_items Array of PerfectSitemapItem objects
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	private function getCategories($item, &$sitemap_items)
	{
		// Only run for com_easydisucuss categories
		if ($item->query['option'] == 'com_easydiscuss' && $item->query['view'] == 'categories')
		{
			$db = JFactory::getDbo();
			$q  = $db->getQuery(true);
			$q
				->select("*")
				->from($db->qn("#__discuss_category"))
				->where($db->qn("published") . "=" . "1");

			$db->setQuery($q);
			$result = $db->loadObjectList();

			if (!empty($result))
			{
				foreach ($result as $category)
				{
					$sitemap_items[] = new PerfectSitemapItem($category->title, JRoute::_("index.php?option=com_easydiscuss&view=categories&layout=listings&category_id=" . $category->id) ,$item->level + 1);
				}
			}
		}
	}

	/**
	 * Get all EasyDiscuss tags for tag menu items
	 *
	 * @param   stdClass  $item           Menu item
	 * @param   array     &$sitemap_items Array of PerfectSitemapItem objects
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	private function getTags($item, &$sitemap_items)
	{
		// Only run for com_easydisucuss tags
		if ($item->query['option'] == 'com_easydiscuss' && $item->query['view'] == 'tags')
		{
			$db = JFactory::getDbo();
			$q  = $db->getQuery(true);
			$q
				->select("*")
				->from($db->qn("#__discuss_tags"))
				->where($db->qn("published") . "=" . "1");

			$db->setQuery($q);
			$result = $db->loadObjectList();

			if (!empty($result))
			{
				foreach ($result as $category)
				{
					$sitemap_items[] = new PerfectSitemapItem($category->title, JRoute::_("index.php?option=com_easydiscuss&view=tags&layout=tag&id=" . $category->id) ,$item->level + 1);
				}
			}
		}
	}
}