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

JLoader::register('NewsfeedsHelperRoute', JPATH_SITE . '/components/com_newsfeeds/helpers/route.php');
JLoader::register('NewsfeedsModelCategory', JPATH_ROOT . '/components/com_newsfeeds/models/category.php');
JLoader::register('NewsfeedsAssociationsHelper', JPATH_ROOT . '/administrator/components/com_newsfeeds/helpers/associations.php');

/**
 * PWT Sitemap Newsfeed plugin Plugin
 *
 * @since  1.0.0
 */
class PlgPwtSitemapNewsfeed extends PwtSitemapPlugin
{
	/**
	 * Populate the PWT Sitemap plugin to use it a base class
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function populateSitemapPlugin()
	{
		$this->component = 'com_newsfeeds';
		$this->views     = ['category'];
	}

	/**
	 * Run for every menuitem passed
	 *
	 * @param   stdClass  $item         Menu items
	 * @param   string    $format       Sitemap format that is rendered
	 * @param   string    $sitemapType  Type of the sitemap that is build
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function onPwtSitemapBuildSitemap($item, $format, $sitemapType)
	{
		$sitemapItems = [];

		if ($this->checkDisplayParameters($item, $format))
		{
			$newsfeeds = $this->getNewsfeeds($item->query['id']);

			if ($newsfeeds !== false)
			{
				foreach ($newsfeeds as $newsfeed)
				{
					$link = NewsfeedsHelperRoute::getNewsfeedRoute($newsfeed->id . ':' . $newsfeed->alias, $newsfeed->catid, $newsfeed->language);

					if ($sitemapType === 'multilanguage')
					{
						$item               = new PwtMultilanguageSitemapItem($newsfeed->name, $link, $item->level + 1);
						$item->associations = $this->getAssociatedNewesfeeds($newsfeed);

						$sitemapItems[] = $item;
					}
					else
					{
						$sitemapItems[] = new PwtSitemapItem($newsfeed->name, $link, $item->level + 1);
					}
				}
			}
		}

		return $sitemapItems;
	}

	/**
	 * Get all newsfeeds from a category
	 *
	 * @param   int  $id  Category id
	 *
	 * @return  mixed  stdClass on success, false otherwise
	 *
	 * @since   1.3.0
	 */
	private function getNewsfeeds($id)
	{
		$newsfeedsModel = new NewsfeedsModelCategory;

		// Calling getState before setState will prevent 'populateState' override the new state
		$newsfeedsModel->getState();
		$newsfeedsModel->setState('category.id', $id);

		return $newsfeedsModel->getItems();
	}

	/**
	 * Get language associated newsfeeds
	 *
	 * @param   stdClass  $newsfeed  The newsfeed to get associated feeds for
	 *
	 * @return  array
	 *
	 * @since   1.3.0
	 */
	private function getAssociatedNewesfeeds($newsfeed)
	{
		$helper       = new NewsfeedsAssociationsHelper;
		$associations = $helper->getAssociations('newsfeed', $newsfeed->id);

		// Map associations to Article objects
		$associations = array_map(
			static function ($value) use ($helper) {
				return $helper->getItem('newsfeed', explode(':', $value->id)[0]);
			},
			$associations
		);

		// Append links
		foreach ($associations as $language => $association)
		{
			$association->link = NewsfeedsHelperRoute::getNewsfeedRoute(
				$association->id . ':' . $association->alias,
				$association->catid,
				$association->language
			);
		}

		return $associations;
	}
}
