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

use Joomla\CMS\Router\Route;

/**
 * PWT Sitemap Hikashop Plugin
 *
 * @since  1.3.0
 */
class PlgPwtSitemapHikashop extends PwtSitemapPlugin
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
		$this->component = 'com_hikashop';
		$this->views     = ['category', 'product'];
	}

	/**
	 * Run for every menuitem passed by Perfect Sitemap
	 *
	 * @param   StdClass  $item    Menu items
	 * @param   string    $format  Sitemap format that is rendered
	 *
	 * @return  array List of sitemap items
	 *
	 * @since   1.0.0
	 */
	public function onPwtSitemapBuildSitemap($item, $format)
	{
		// Get plugin settings
		$iShowProductcode = $this->params->get('show_productcode', 0);

		// Generate sitemap items
		$sitemapItems = [];
		$iCatId       = null;

		if ($this->checkDisplayParameters($item, $format))
		{
			// Get Category Id for category menu-type
			if ($item->query['view'] === 'category' && isset($item->params->get('hk_category')->category))
			{
				$iCatId   = $item->params->get('hk_category')->category;
				$iSubCats = $item->params->get('hk_category')->filter_type;
			}

			// Get Category Id for product menu-type
			if ($item->query['view'] === 'product' && isset($item->params->get('hk_product')->category))
			{
				$iCatId = $item->params->get('hk_product')->category;
				
				$iSubCats = $item->params->get('hk_product')->filter_type;
				
			}

			// Get products for category
			if ($iCatId)
			{
				$aProducts = $this->getProducts($iCatId, $iSubCats);

				foreach ($aProducts as $oProduct)
				{
					// Use canonical if set
					if ($oProduct->productCanonical)
					{
						$link = $oProduct->productCanonical;
					}
					else
					{
						$link = Route::_(
							'index.php?option=com_hikashop&ctrl=product&task=show&name=' .
							$oProduct->productAlias . '&cid=' . $oProduct->productId . '&Itemid=' . $item->id
						);
					}

					$title    = $iShowProductcode ? $oProduct->productCode . ' ' . $oProduct->productName : $oProduct->productName;
					$modified = date('Y-m-d', $oProduct->productModified);

					$sitemapItems[] = new PwtSitemapItem($title, $link, $item->level + 1, $modified);
				}
			}
		}

		return $sitemapItems;
	}

	/**
	 * Get products from the #__hikashop_product table
	 *
	 * @param   integer  $iCatId                Category id
	 *
	 * @param   integer  $displaySubcategories  If  1 or 3, subcategories will be included
	 *
	 * @return  stdClass
	 *
	 * @since   1.3.0
	 */
	private function getProducts($iCatId, $displaySubcategories)
	{
		$query = $this->db->getQuery(true);

		$query
			->select(
				$this->db->quoteName(
					[
						'product.product_id',
						'product.product_name',
						'product.product_code',
						'product.product_alias',
						'product.product_canonical',
						'product.product_modified'
					],
					[
						'productId',
						'productName',
						'productCode',
						'productAlias',
						'productCanonical',
						'productModified',
					]
				)
			)
			->from($this->db->quoteName('#__hikashop_product', 'product'))
			->leftJoin($this->db->quoteName('#__hikashop_product_category', 'product_category') . ' USING(product_id)')
			->leftJoin(
				$this->db->quoteName('#__hikashop_category', 'category') . ' ON ' .
				$this->db->quoteName('category.category_id') . ' = ' . $this->db->quoteName('product_category.category_id')
			)
			->where($this->db->quoteName('product.product_published') . ' = 1')
			->where($this->db->quoteName('product.product_type') . ' = ' . $this->db->quote('main'));

		// Display main cat or also include subcategories?
		if ((int) $displaySubcategories === 1 || (int) $displaySubcategories === 3)
		{
			$category = $this->getCategory($iCatId);
			$query->where(
				$this->db->quoteName('category.category_left') . ' >= ' . (int) $category->categoryLeft .
				' AND ' . $this->db->quoteName('category.category_right') . ' <= ' . (int) $category->categoryRight
			);
		}
		else
		{
			$query->where($this->db->quoteName('product_category.category_id') . ' = ' . (int) $iCatId);
		}

		$query->order($this->db->quoteName('product_id'));

		return $this->db->setQuery($query)->loadObjectList();
	}

	/**
	 * Method to get the category info
	 *
	 * @param   integer  $iCatId  Category id
	 *
	 * @return  stdClass
	 *
	 * @since   1.3.0
	 */
	private function getCategory($iCatId)
	{
		$query = $this->db->getQuery(true);

		$query
			->select($this->db->qn(['category_left', 'category_right'], ['categoryLeft', 'categoryRight']))
			->from($this->db->quoteName('#__hikashop_category'))
			->where($this->db->quoteName('category_id') . ' = ' . (int) $iCatId);

		return $this->db->setQuery($query)->loadObject();
	}
}
