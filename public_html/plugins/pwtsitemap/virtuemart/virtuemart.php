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
use Joomla\CMS\Router\Route;

/**
 * PWT Sitemap Virtuemart Plugin
 *
 * @since  1.0.0
 */
class PlgPwtSitemapVirtuemart extends PwtSitemapPlugin
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
		$this->component = 'com_virtuemart';
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
		// Generate sitemap items
		$sitemapItems = [];

		if ($this->checkDisplayParameters($item, $format))
		{
			$catId = $item->query['virtuemart_category_id'];

			$products = $this->getProducts($catId);

			if (!empty($products))
			{
				foreach ($products as $product)
				{
					
					$title = $product->product_name;
					$link  = Route::_($product->link);

					$modified = null;

					if ($product->modified_on !== Factory::getDbo()->getNullDate())
					{
						$modified = date('Y-m-d', $product->modified_on);
					}

					$sitemapItems[] = new PwtSitemapItem($title, $link, $item->level + 1, $modified);
					
				}
			}
		}

		return $sitemapItems;
	}

	/**
	 * Get product by the category ID
	 *
	 * @param   int  $catId  category ID
	 *
	 * @return  array
	 *
	 * @since   1.3.0
	 */
	private function getProducts($catId)
	{
		$productModel = VmModel::getModel('product');

		$ids = $productModel->sortSearchListQuery(true, $catId);

		return $productModel->getProducts($ids);
	}
}
