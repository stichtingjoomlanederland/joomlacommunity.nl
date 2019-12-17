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

if (file_exists(JPATH_ADMINISTRATOR . '/components/com_zoo/framework/classes/helper.php'))
{
	require_once JPATH_ADMINISTRATOR . '/components/com_zoo/framework/classes/helper.php';
}

if (file_exists(JPATH_ADMINISTRATOR . '/components/com_zoo/helpers/route.php'))
{
	require_once JPATH_ADMINISTRATOR . '/components/com_zoo/helpers/route.php';
}

/**
 * PWT Sitemap Zoo Plugin
 *
 * @since  1.0.0
 */
class PlgPwtSitemapZoo extends PwtSitemapPlugin
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
		$this->component = 'com_zoo';
		$this->views     = ['category'];
	}

	/**
	 * Run for every menuitem passed by Perfect Sitemap
	 *
	 * @param   stdClass  $item    Menu items
	 * @param   string    $format  Sitemap format that is rendered
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function onPwtSitemapBuildSitemap($item, $format)
	{
		$sitemapItems = [];

		if ($this->checkDisplayParameters($item, $format))
		{
			$catId = $item->params->get('category');
			$app   = App::getInstance('zoo');
			$items = $this->getItems($catId, $app);

			if (!empty($items))
			{
				foreach ($items as $zooItem)
				{
					$title    = $zooItem->name;
					$modified = null;

					if ($zooItem->modified !== Factory::getDbo()->getNullDate())
					{
						$modified = date('Y-m-d', strtotime($zooItem->modified));
					}

					$routehelper = new RouteHelper($app);

					$link = $routehelper->item($zooItem);

					$sitemapItems[] = new PwtSitemapItem($title, $link, $item->level + 1, $modified);
				}
			}
		}

		return $sitemapItems;
	}

	/**
	 * Get items by the category ID
	 *
	 * @param   int  $catId  category ID
	 *
	 * @param   App  $app    App class from zoo
	 *
	 * @return  array
	 *
	 * @since   1.3.0
	 */
	private function getItems($catId, $app)
	{
		$category = $app->table->category->getById($catId);

		return $app->table->item->getByCategory($category[0]->application_id, $catId, true);
	}
}
