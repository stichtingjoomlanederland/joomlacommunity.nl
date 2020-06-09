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
use Joomla\CMS\Router\Route;

require_once JPATH_SITE . '/components/com_joomgallery/interface.php';

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
	public function onPwtSitemapBuildSitemap($item, $format, $sitemapType = 'default')
	{
		$sitemapItems = [];

		if ($this->checkDisplayParameters($item, $format) && (int) $item->params->get('addjoomgalleryto' . $format . 'sitemap', 1))
		{
			$joomInterface = new JoomInterface();
			$categories    = $this->getCategories();

			foreach ($categories as $category)
			{
				$title = $category->name;
				$link  = Route::_(
					'index.php?option=com_joomgallery&view=category&Itemid=' . $item->id . '&catid=' . $category->cid
				);

				if ($sitemapType === 'image')
				{
					$sitemapItem = new PwtSitemapImageItem($title, $link, $category->level + 1);

					$images = $joomInterface->getPicsByCategory($category->cid);

					foreach ($images as $image)
					{
						$sitemapItem->images[] = (object) [
							'url'     => PwtSitemapUrlHelper::getURL('/images/joomgallery/details/' . $image->catpath . '/' . $image->imgfilename),
							'caption' => $image->imgtitle
						];
					}

					$sitemapItems[] = $sitemapItem;
				}
				else
				{
					$sitemapItems[] = new PwtSitemapItem($title, $link, $category->level + 1);
				}
			}
		}

		return $sitemapItems;
	}

	/**
	 * Get the JoomGallery categories, simplified
	 *
	 * @return array|mixed
	 */
	private function getCategories()
	{
		// Creation of array
		$db   = Factory::getDBO();
		$user = Factory::getUser();

		// Read all categories from database
		$query = $db->getQuery(true)
			->select('c.cid, c.parent_id, c.name, c.level')
			->from(_JOOM_TABLE_CATEGORIES . ' AS c')
			->where('lft > 0')
			->where('c.published = 1')
			->order('c.lft');

		if (!$user->authorise('core.admin'))
		{
			$query->where('c.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
		}

		return $db->setQuery($query)->loadObjectList('cid');
	}
}
