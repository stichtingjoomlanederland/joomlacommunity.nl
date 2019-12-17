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

if (file_exists(JPATH_ROOT . '/components/com_kunena/models/category.php'))
{
	require_once JPATH_ROOT . '/components/com_kunena/models/category.php';
}

if (file_exists(JPATH_ROOT . '/components/com_kunena/models/topics.php'))
{
	require_once JPATH_ROOT . '/components/com_kunena/models/topics.php';
}

/**
 * PWT Sitemap Contact
 *
 * @since  1.0.0
 */
class PlgPwtSitemapKunena extends PwtSitemapPlugin
{
	/**
	 * Populate the PWT sitemap plugin to use it a base class
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function populateSitemapPlugin()
	{
		$this->component = 'com_kunena';
		$this->views     = ['category', 'topics'];
	}

	/**
	 * Run for every menuitem passed
	 *
	 * @param   stdClass  $item    Menu items
	 * @param   string    $format  Sitemap format that is rendered
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function onPwtSitemapBuildSitemap($item, $format)
	{
		$sitemapItems = [];

		if ($this->checkDisplayParameters($item, $format))
		{
			// Get categories
			if (array_key_exists('layout', $item->query) && $item->query['layout'] === 'list' && $item->query['view'] === 'category')
			{
				if (!array_key_exists('catid', $item->query))
				{
					return [];
				}

				$items = $this->getCategories($item->query['catid']);

				if ($items !== false)
				{
					foreach ($items as $entries)
					{
						foreach ($entries as $entry)
						{
							
							if ($entry->parent_id > 0)
							{
								$link           = KunenaRoute::_('index.php?option=com_kunena&view=category&catid=' . $entry->id);
								$sitemapItems[] = new PwtSitemapItem($entry->name, $link, $item->level + 1);
							}
							
						}
					}
				}
			}

			// Get Topics
			if (array_key_exists('view', $item->query) && $item->query['view'] === 'topics')
			{
				if (array_key_exists('mode', $item->query))
				{
					$mode = $item->query['mode'];

					switch ($mode)
					{
						case 'noreplies':
						case 'replies':
						case 'topics':
						case 'stick':
						case 'locked':
						case 'unapproved':
						case 'deleted':
							break;
						default:
							return $sitemapItems;
					}

					$items = $this->getTopics($item->query['mode']);

					if ($items !== false)
					{
						foreach ($items as $entry)
						{
							$link           = KunenaRoute::getTopicUrl($entry, null, null, null);
							$sitemapItems[] = new PwtSitemapItem($entry->subject, $link, $item->level + 1);
						}
					}
				}
			}
		}

		return $sitemapItems;
	}

	/**
	 * Get all Categories
	 *
	 * @param   int  $id  Category id
	 *
	 * @return  mixed  stdClass on success, false otherwise
	 *
	 * @since   1.3.0
	 *
	 * @throws  Exception
	 */
	private function getCategories($id)
	{
		$categoryModel = new KunenaModelCategory(['ignore_request' => true]);
		$categoryModel->setState('item.id', $id);

		return $categoryModel->getCategories();
	}

	/**
	 * Get all Topics
	 *
	 * @param   string  $mode  The mode of menu item
	 *
	 * @return  mixed  stdClass on success, false otherwise
	 *
	 * @since   1.3.0
	 *
	 * @throws  Exception
	 */
	private function getTopics($mode)
	{
		$topicsModel = new KunenaModelTopics(['ignore_request' => true]);
		$topicsModel->setState('list.mode', $mode);

		return $topicsModel->getTopics();
	}

}
