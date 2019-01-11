<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');
JLoader::register('ContentHelperQuery', JPATH_SITE . '/components/com_content/helpers/query.php');
JLoader::register('ContentAssociationsHelper', JPATH_ADMINISTRATOR . '/components/com_content/helpers/associations.php');
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

/**
 * PWT Sitemap Content Plugin
 *
 * @since  1.0.0
 */
class PlgPwtSitemapContent extends PwtSitemapPlugin
{
	/**
	 * Populate the PWT Sitemap Content plugin to use it a base class
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	function populateSitemapPlugin()
	{
		$this->component = 'com_content';
		$this->views     = ['category'];
	}

	/**
	 * Run for every menuitem passed
	 *
	 * @param   JMenuItem $item         Menu items
	 * @param   string    $format       Sitemap format that is rendered
	 * @param   string    $sitemap_type Type of sitemap that is generated
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function onPwtSitemapBuildSitemap($item, $format, $sitemap_type = 'default')
	{
		if ($this->checkDisplayParameters($item, $format, array('article')))
		{
			// Prepare article menu-item
			if ($item->query['view'] === 'article')
			{
				return $this->buildSitemapArticle($item, $format, $sitemap_type);
			}

			// Prepare category menu-item
			if ($item->query['view'] === 'category')
			{
				return $this->buildSitemapCategory($item, $format, $sitemap_type);
			}
		}

		return array();
	}

	/**
	 * Run before adding menu items to sitemap
	 *
	 * @param   JMenuItem $item Menu items
	 *
	 * @return  void
	 *
	 * @since   1.1.0
	 */
	public function onPwtSitemapAddMenuItemToSitemap($item)
	{
		// Don't add article items directly, we use buildSitemapArticle instead
		if (isset($item->query['view']) && $item->query['view'] == 'article')
		{
			$item->doNotAdd = true;
		}
	}

	/**
	 * Build sitemap for com_content article view
	 *
	 * @param   JMenuItem $item         Menu items
	 * @param   string    $format       Sitemap format that is rendered
	 * @param   string    $sitemap_type Type of sitemap that is generated
	 *
	 * @return  array
	 *
	 * @since   1.1.0
	 */
	public function buildSitemapArticle($item, $format, $sitemap_type)
	{
		// Save new items
		$sitemap_items = array();

		// Get article
		$article = JTable::getInstance('content');
		$article->load($item->query['id']);

		$link     = ContentHelperRoute::getArticleRoute($article->id . ':' . $article->alias, $article->catid, $article->language);
		$modified = HTMLHelper::_('date', $article->modified, 'Y-m-d');

		$sitemap_items[] = new PwtSitemapItem($item->title, $link, $item->level, $modified);

		return $sitemap_items;
	}

	/**
	 * Build sitemap for com_content category view
	 *
	 * @param   JMenuItem $item         Menu items
	 * @param   string    $format       Sitemap format that is rendered
	 * @param   string    $sitemap_type Type of sitemap that is generated
	 *
	 * @return  array
	 *
	 * @since   1.1.0
	 */
	public function buildSitemapCategory($item, $format, $sitemap_type)
	{
		// Save new items
		$sitemap_items = array();

		// Get articles for category
		$articles = $this->getArticles($item->query['id'], $item->language, $item->params);

		foreach ($articles as $article)
		{
			$oParam = new Registry;
			$oParam->loadString($article->metadata);

			if (strpos($oParam->get('robots'), 'noindex') !== false)
			{
				continue;
			}

			$link     = ContentHelperRoute::getArticleRoute($article->id . ':' . $article->alias, $article->catid, $article->language);
			$modified = HTMLHelper::_('date', $article->modified, 'Y-m-d');

			$sitemap_items[] = $this->convertToSitemapItem($article, $item, $link, $modified, $sitemap_type);
		}

		return $sitemap_items;
	}

	/**
	 * Convert the given paramters to a PwtSitemapItem
	 *
	 * @param   $article       stdClass
	 * @param   $item          JMenuItem
	 * @param   $link          string
	 * @param   $modified      string
	 * @param   $sitemap_type  string
	 *
	 * @return  BasePwtSitemapItem
	 *
	 * @since   1.0.0
	 */
	private function convertToSitemapItem($article, $item, $link, $modified, $sitemap_type)
	{
		switch ($sitemap_type)
		{
			case "multilanguage":
				$sitemapItem               = new PwtMultilanguageSitemapItem($article->title, $link, $item->level + 1, $modified);
				$sitemapItem->associations = $this->getAssociatedArticles($article);

				return $sitemapItem;
				break;
			case "image":
				$sitemapItem         = new PwtSitemapImageItem($article->title, $link, $item->level + 1, $modified);
				$sitemapItem->images = $this->getArticleImages($article);

				return $sitemapItem;
				break;
			default:
				return new PwtSitemapItem($article->title, $link, $item->level + 1, $modified);
		}
	}

	/**
	 * Get articles from the #__content table
	 *
	 * @param   array  $categories Category id
	 * @param   string $language   Language prefix
	 *
	 * @return  stdClass
	 *
	 * @since   1.0.0
	 */
	private function getArticles($categories, $language, $params)
	{
		$globalParams = ComponentHelper::getParams('com_content');

		// Get ordering from menu
		$articleOrderby   = $params->get('orderby_sec', $globalParams->get('orderby_sec', 'rdate'));
		$articleOrderDate = $params->get('order_date', $globalParams->get('order_date', 'published'));
		$secondary        = ContentHelperQuery::orderbySecondary($articleOrderby, $articleOrderDate);

		// Get an instance of the generic articles model
		$articles = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		$articles->setState('params', $params);
		$articles->setState('filter.published', 1);
		$articles->setState('filter.access', 1);
		$articles->setState('filter.language', $language);
		$articles->setState('filter.category_id', $categories);
		$articles->setState('list.start', 0);
		$articles->setState('list.limit', 0);
		$articles->setState('list.ordering', $secondary . ', a.created DESC');
		$articles->setState('list.direction', '');

		// Include subcategories
		$showSubcategories = $params->get('show_subcategory_content', '0');

		if ($showSubcategories)
		{
			$articles->setState('filter.max_category_levels', $showSubcategories);
			$articles->setState('filter.subcategories', true);
		}

		// Get results
		return $articles->getItems();
	}

	/**
	 * Get language associated articles
	 *
	 * @param   $article  stdClass  Article to find associations
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	private function getAssociatedArticles($article)
	{
		$helper       = new ContentAssociationsHelper();
		$associations = $helper->getAssociations('article', $article->id);

		// Map associations to Article objects
		$associations = array_map(function ($value) use ($helper) {
			return $helper->getItem('article', explode(':', $value->id)[0]);
		}, $associations);

		// Append links
		foreach ($associations as $language => $association)
		{
			$association->link = ContentHelperRoute::getArticleRoute($association->id . ':' . $association->alias, $association->catid, $association->language);
		}

		return $associations;
	}

	/**
	 * Get the images of an article
	 *
	 * @param $article stdClass  Article
	 *
	 * @return array
	 *
	 * @since version
	 */
	private function getArticleImages($article)
	{
		$images        = [];
		$articleImages = json_decode($article->images);

		if (!empty($articleImages->image_intro))
		{
			$image          = new stdClass();
			$image->url     = PwtSitemapUrlHelper::getURL('/' . $articleImages->image_intro);
			$image->caption = (!empty($articleImages->image_intro_caption)) ? $articleImages->image_intro_caption : $articleImages->image_intro_alt;

			$images[] = $image;
		}

		if (!empty($articleImages->image_fulltext))
		{
			$image          = new stdClass();
			$image->url     = PwtSitemapUrlHelper::getURL('/' . $articleImages->image_fulltext);
			$image->caption = (!empty($articleImages->image_fulltext_caption)) ? $articleImages->image_fulltext_caption : $articleImages->image_fulltext_alt;

			$images[] = $image;
		}

		return $images;
	}
}