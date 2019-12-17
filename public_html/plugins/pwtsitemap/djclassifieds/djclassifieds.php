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

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;

JLoader::register('ContactHelperRoute', JPATH_SITE . '/components/com_contact/helpers/route.php');
JLoader::register('ContactModelCategory', JPATH_SITE . '/components/com_contact/models/category.php');
JLoader::register('ContactModelFeatured', JPATH_SITE . '/components/com_contact/models/featured.php');
BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_contact/models', 'ContactModel');

/**
 * PWT Sitemap DJ-Classifieds
 *
 * @since  1.0.0
 */
class PlgPwtSitemapDJClassifieds extends PwtSitemapPlugin
{
	/**
	 * @var DjclassifiedsModelItems
	 *
	 * @since   1.0.0
	 */
	private $model;

	/**
	 * PlgPwtSitemapDJClassifieds constructor.
	 *
	 * Overloaded to pre-load stuff for DJ Classifieds
	 *
	 * @param          $subject
	 * @param   array  $config
	 *
	 * @throws  Exception
	 */
	public function __construct($subject, array $config = [])
	{
		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_djclassifieds/models', 'DjclassifiedsModel');
		$this->model = BaseDatabaseModel::getInstance('Items', 'DjclassifiedsModel', ['ignore_request' => true]);

		require_once JPATH_ADMINISTRATOR . '/components/com_djclassifieds/lib/djimage.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_djclassifieds/lib/djseo.php';

		parent::__construct($subject, $config);
	}

	/**
	 * Populate the PWT sitemap plugin to use it a base class
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function populateSitemapPlugin()
	{
		$this->component = 'com_djclassifieds';
		$this->views     = ['items'];
	}

	/**
	 * Run for every menuitem passed
	 *
	 * @param   stdClass  $menuitem  Menu items
	 * @param   string    $format    Sitemap format that is rendered
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function onPwtSitemapBuildSitemap($menuitem, $format)
	{
		$sitemapItems = [];

		if ($this->checkDisplayParameters($menuitem, $format))
		{
			$items     = $this->model->getItems($menuitem->query['cid']);
			$mainCatId = $this->model->getMainCat($menuitem->query['cid']);

			if ($items !== false)
			{
				foreach ($items as $item)
				{
					$link = Route::_(
						DJClassifiedsSEO::getItemRoute(
							$item->id . ':' . $item->alias,
							$item->cat_id . ':' . $item->c_alias,
							$item->r_id . ':' . $item->r_name,
							$mainCatId,
							$item->extra_cats
						)
					);

					$sitemapItems[] = new PwtSitemapItem($item->name, $link, 2);
				}
			}
		}

		return $sitemapItems;
	}
}
