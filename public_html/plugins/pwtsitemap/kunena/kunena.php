<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2020 Perfect Web Team. All rights reserved.
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
			if (array_key_exists('layout', $item->query)
				&& $item->query['layout'] === 'list'
				&& $item->query['view'] === 'category'
				&& (int) $item->params->get('addkunenato' . $format . 'sitemap', 1)
			)
			{
				$parentCategory = $item->query['catid'] ?? 0;
				$sitemapItems   = $this->getForumIndex($parentCategory, $item->level);
			}

			// Get topics for category
			if (!array_key_exists('layout', $item->query)
				&& $item->query['view'] === 'category'
				&& (int) $item->params->get('addkunenato' . $format . 'sitemap', 1)
			)
			{
				$items = $this->getCategoryTopics($item->query['catid']);

				if ($items !== false)
				{
					foreach ($items as $entry)
					{
						$link           = KunenaRoute::getTopicUrl($entry, null, null, null);
						$sitemapItems[] = new PwtSitemapItem($entry->subject, $link, $item->level + 1);
					}
				}
			}

			// Get Topics
			if (array_key_exists('view', $item->query)
				&& $item->query['view'] === 'topics'
				&& (int) $item->params->get('addkunenato' . $format . 'sitemap', 1)
			)
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
	 * @param   integer  $parentCategory  Category ID to start with
	 * @param   integer  $startLevel      Menu item start level
	 *
	 * @return array List of sitemap items
	 *
	 * @since   1.3.0
	 * @throws Exception
	 */
	private function getForumIndex($parentCategory, $startLevel)
	{
		$categoryTree = KunenaForumCategoryHelper::getCategoryTree($parentCategory);
		$categories   = KunenaForumCategoryHelper::getCategories();
		$categoryIds  = $this->getArrayKeysRecusrive($categoryTree);

		$sitemapItems = [];

		foreach ($categoryIds as $categoryId)
		{
			$sitemapItems[] = new PwtSitemapItem(
				$categories[$categoryId]->name,
				KunenaRoute::_('index.php?option=com_kunena&view=category&catid=' . $categories[$categoryId]->id),
				$startLevel + $categories[$categoryId]->level + 1
			);

			$topics = $this->getCategoryTopics($categoryId);

			foreach ($topics as $topic)
			{
				$sitemapItems[] = new PwtSitemapItem(
					$topic->subject,
					KunenaRoute::getTopicUrl($topic, null, null, null),
					$startLevel + $categories[$categoryId]->level + 2
				);
			}
		}

		return $sitemapItems;
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

	/**
	 * Get all Topics for category
	 *
	 * @param   integer  $category  The category ID
	 *
	 * @return  mixed  stdClass on success, false otherwise
	 *
	 * @since   1.4.3
	 *
	 * @throws  Exception
	 */
	private function getCategoryTopics($category)
	{
		$categoryModel = new KunenaModelCategory(['ignore_request' => true]);
		$categoryModel->setState('item.id', $category);
		$categoryModel->setState('list.start', 0);
		$categoryModel->setState('list.limit', 1000);

		return $categoryModel->getTopics();
	}

	/**
	 * Flatten the recusrive array
	 *
	 * @param   array  $array  Array to flatten
	 *
	 * @return array
	 *
	 * @since 1.4.3
	 */
	protected function getArrayKeysRecusrive(array $array)
	{
		$keys = array();

		foreach ($array as $key => $value)
		{
			$keys[] = $key;

			if (is_array($value))
			{
				$keys = array_merge($keys, self::getArrayKeysRecusrive($value));
			}
		}

		return $keys;
	}
}
