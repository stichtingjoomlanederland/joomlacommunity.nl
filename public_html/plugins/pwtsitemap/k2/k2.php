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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

if (file_exists(JPATH_ADMINISTRATOR . '/components/com_k2/models/model.php'))
{
	require_once JPATH_ADMINISTRATOR . '/components/com_k2/models/model.php';
}

if (file_exists(JPATH_SITE . '/components/com_k2/models/itemlist.php'))
{
	require_once JPATH_SITE . '/components/com_k2/models/itemlist.php';
}

if (file_exists(JPATH_SITE . '/components/com_k2/helpers/route.php'))
{
	require_once JPATH_SITE . '/components/com_k2/helpers/route.php';
}

/**
 * PWT Sitemap K2 Plugin
 *
 * @since  1.0.0
 */
class PlgPwtSitemapK2 extends PwtSitemapPlugin
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
		$this->component = 'com_k2';
		$this->views     = ['latest', 'itemlist'];
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

		if ($this->checkDisplayParameters($item, $format) && (int) $item->params->get('addk2to' . $format . 'sitemap', 1))
		{
			$k2items = $this->getK2Items($item);

			if ($k2items !== false)
			{
				foreach ($k2items as $k2item)
				{
					
					$link = Route::_(
						'index.php?option=com_k2&view=item&Itemid=' . $item->id .
						'&id=' . $k2item->id . ':' . urlencode($k2item->alias)
					);

					$modified = HTMLHelper::_(
						'date',
						$k2item->modified === '0000-00-00 00:00:00' ? $k2item->publish_up : $k2item->modified,
						'Y-m-d'
					);

					$sitemapItems[] = new PwtSitemapItem($k2item->title, $link, $item->level + 1, $modified);
					
				}
			}
		}

		return $sitemapItems;
	}

	/**
	 * Method to get items from K2
	 *
	 * @param   stdClass  $item  Menu item
	 *
	 * @return  array
	 *
	 * @since   1.3.0
	 *
	 * @throws  Exception
	 */
	private function getK2Items($item)
	{
		$items = [];

		if ($item->query['view'] === 'latest')
		{
			if ($item->params->get('source') === 1 && $item->params->get('categoryIDs'))
			{
				foreach ($item->params->get('categoryIDs') as $categoryId)
				{
					$newItems = $this->getK2ItemsCategory($categoryId);
					$items    = array_merge($items, $newItems);
				}
			}

			if ($item->params->get('source') === 0 && $item->params->get('userIDs'))
			{
				foreach ($item->params->get('userIDs') as $userId)
				{
					$model    = new K2ModelItemlist;
					$newItems = $model->getAuthorLatest(0, $item->params->get('latestItemsLimit'), (int) $userId);
					$items    = array_merge($items, $newItems);
				}
			}
		}

		if ($item->query['view'] === 'itemlist')
		{
			if ($item->params->get('categories'))
			{
				foreach ($item->params->get('categories') as $categoryId)
				{
					$newItems = $this->getK2ItemsCategory($categoryId);
					$items    = array_merge($items, $newItems);
				}
			}
		}

		return $items;
	}

	/**
	 * Method to get items by K2 category
	 *
	 * @param   integer  $categoryId  K2 category ID
	 *
	 * @return array
	 *
	 * @since 1.4.2
	 * @throws Exception
	 */
	private function getK2ItemsCategory($categoryId)
	{
		$model = new K2ModelItemlist;

		$jinput = Factory::getApplication()->input;
		$jinput->set('view', 'itemlist');
		$jinput->set('task', 'category');
		$jinput->set('featured', 1);
		$jinput->set('limit', 0);
		$jinput->set('clearFlag', true);

		// Need this legacy here for proper category setting
		JRequest::setVar('id', $categoryId);

		return $model->getData();
	}
}
