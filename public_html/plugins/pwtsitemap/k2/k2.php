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
		$this->views     = ['latest'];
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
			$k2items = $this->getK2Items($item);

			if ($k2items !== false)
			{
				foreach ($k2items as $k2item)
				{
					
					$link           = Route::_(
						'index.php?option=com_k2&view=item&Itemid=' . $item->id .
						'&id=' . $k2item->id . ':' . urlencode($k2item->alias)
					);
					$modified       = HTMLHelper::_(
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

		$model = new K2ModelItemlist;

		switch ($item->params->get('source'))
		{
			// Category
			case 1 :
				if ($item->params->get('categoryIDs'))
				{
					foreach ($item->params->get('categoryIDs') as $categoryId)
					{
						$jinput = Factory::getApplication()->input;
						$jinput->set('view', 'itemlist');
						$jinput->set('task', 'category');
						$jinput->set('id', $categoryId);
						$jinput->set('featured', 1);
						$jinput->set('limit', $item->params->get('latestItemsLimit'));
						$jinput->set('clearFlag', true);

						$newItems = $model->getData();
						$items    = array_merge($items, $newItems);
					}
				}
				break;

			// User
			case 0:
				if ($item->params->get('userIDs'))
				{
					foreach ($item->params->get('userIDs') as $userId)
					{
						$newItems = $model->getAuthorLatest(0, $item->params->get('latestItemsLimit'), (int) $userId);
						$items    = array_merge($items, $newItems);
					}
				}
				break;
		}

		return $items;
	}
}
