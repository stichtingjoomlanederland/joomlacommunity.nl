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
 * PWT Sitemap JoomGallery
 *
 * @since  1.3.0
 */
class PlgPwtSitemapJoomgallery extends PwtSitemapPlugin
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
		$this->component = 'com_joomgallery';
		$this->views     = ['category', 'gallery'];
	}

	/**
	 * Run for every menuitem passed
	 *
	 * @param   StdClass  $item    Menu items
	 * @param   string    $format  Sitemap format that is rendered
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 *
	 * @throws Exception
	 */
	public function onPwtSitemapBuildSitemap($item, $format)
	{
		$sitemapItems = [];

		if ($this->checkDisplayParameters($item, $format))
		{
			$this->includeFiles();
			$jinput = Factory::getApplication()->input;
			$jinput->set('limit', (int) $this->params->get('limit', 20));
			$categories = [];

			if (array_key_exists('view', $item->query) && $item->query['view'] === 'category')
			{
				$jinput->set('catid', $item->query['catid']);
				$categoryModel = new JoomGalleryModelCategory;

				$categories = $categoryModel->getCategories();
				$images     = $categoryModel->getImages();

				foreach ($images as $image)
				{
					$title = $image->imgtitle;
					$link  = Route::_(
						'index.php?option=com_joomgallery&view=detail&Itemid=' . $item->id . '&id=' . $image->id
					);

					$sitemapItems[] = new PwtSitemapItem($title, $link, $item->level + 1);
				}
			}
			elseif (array_key_exists('view', $item->query) && $item->query['view'] === 'gallery')
			{
				$categories = (new JoomGalleryModelGallery)->getCategories();
			}

			foreach ($categories as $category)
			{
				$title = $category->name;
				$link  = Route::_(
					'index.php?option=com_joomgallery&view=category&Itemid=' . $item->id . '&catid=' . $category->cid
				);

				$sitemapItems[] = new PwtSitemapItem($title, $link, $item->level + 1);
			}
		}

		return $sitemapItems;
	}

	/**
	 * Include all needed files to use the JoomGallery models and other functions of JoomGallery
	 * This is in a separate functions so that they are only included when used by pwtsitemap otherwise
	 * it breaks JoomGallery.
	 *
	 * @return  void
	 *
	 * @since   1.3.0
	 */
	private function includeFiles()
	{
		if (file_exists(JPATH_SITE . '/components/com_joomgallery/model.php'))
		{
			require_once JPATH_SITE . '/components/com_joomgallery/model.php';
		}

		if (file_exists(JPATH_SITE . '/components/com_joomgallery/models/category.php'))
		{
			require_once JPATH_SITE . '/components/com_joomgallery/models/category.php';
		}

		if (file_exists(JPATH_SITE . '/components/com_joomgallery/models/gallery.php'))
		{
			require_once JPATH_SITE . '/components/com_joomgallery/models/gallery.php';
		}

		if (file_exists(JPATH_SITE . '/components/com_joomgallery/helpers/ambit.php'))
		{
			require_once JPATH_SITE . '/components/com_joomgallery/helpers/ambit.php';
		}

		if (file_exists(JPATH_ADMINISTRATOR . '/components/com_joomgallery/helpers/config.php'))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_joomgallery/helpers/config.php';
		}

		if (file_exists(JPATH_ADMINISTRATOR . '/components/com_joomgallery/helpers/helper.php'))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_joomgallery/helpers/helper.php';
		}

		if (file_exists(JPATH_ADMINISTRATOR . '/components/com_joomgallery/tables/joomgalleryconfig.php'))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_joomgallery/tables/joomgalleryconfig.php';
		}

		if (file_exists(JPATH_ADMINISTRATOR . '/components/com_joomgallery/helpers/html/joomgallery.php'))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_joomgallery/helpers/html/joomgallery.php';
		}
	}
}
