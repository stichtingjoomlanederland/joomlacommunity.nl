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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Dashboard model
 *
 * @since   1.0.0
 */
class PwtSitemapModelDashboard extends BaseDatabaseModel
{
	/**
	 * Check for the PWT sitemap menu items
	 *
	 * @return  array List of menu items
	 *
	 * @since   1.3.0
	 */
	public function checkForMenuItems()
	{
		$items = [];

		$menuItems = $this->getPwtSitemapMenuItems();

		foreach ($menuItems as $item)
		{
			// Remove index.php?option=com_pwtsitemap&view= from the link
			$link = substr($item->link, 37);

			switch ($link)
			{
				case 'sitemap':
					$items['sitemap'][] = $item;
					break;
				case 'sitemap&layout=sitemapxml&format=xml':
					$items['xmlsitemap'][] = $item;
					break;
				case 'multilanguage&format=xml':
					$items['multilingualsitemap'][] = $item;
					break;
				case 'image&format=xml':
					$items['imagesitemap'][] = $item;
					break;
			}
		}

		return $items;
	}

	/**
	 * Get PWT Sitemap menu items
	 *
	 * @return  array List with menu item id, path and link
	 *
	 * @since   1.3.0
	 */
	private function getPwtSitemapMenuItems()
	{
		$component = ComponentHelper::getComponent('com_pwtsitemap');

		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'menuItem.id',
						'menuItem.path',
						'menuItem.link'
					]
				)
			)
			->from($db->quoteName('#__menu', 'menuItem'))
			->where($db->quoteName('component_id') . ' = ' . (int) $component->id);

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * Get menu dropdown list
	 *
	 * @return  array List of select options of menu items
	 *
	 * @since   1.3.0
	 */
	public function getMenusList()
	{
		/** @var MenusModelMenus $menusModel */
		$menusModel = BaseDatabaseModel::getInstance('Menus', 'MenusModel');

		$menus = $menusModel->getItems();

		$elements = [HTMLHelper::_('select.option', '', Text::_('COM_PWTSITEMAP_DASHBOARD_MODEL_SELECT_MENU'))];

		foreach ($menus as $menu)
		{
			$elements[] = HTMLHelper::_('select.option', $menu->menutype, $menu->title);
		}

		return $elements;
	}

	/**
	 * Add Menu Items to robots.txt
	 *
	 * @return  mixed
	 * @since   1.3.0
	 * @throws  Exception
	 */
	public function addToRobots()
	{
		$robotsTxt = JPATH_ROOT . '/robots.txt';

		if (!file_exists($robotsTxt))
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_PWTSITEMAP_DASHBOARD_NO_ROBOTS_TXT'), 'error');

			return false;
		}

		$items = $this->checkForMenuItems();

		$xmlSiteMaps = array_merge(
			isset($items['xmlsitemap']) ? $items['xmlsitemap'] : [],
			isset($items['multilingualsitemap']) ? $items['multilingualsitemap'] : [],
			isset($items['imagesitemap']) ? $items['imagesitemap'] : []
		);

		if (count($xmlSiteMaps))
		{
			$myRobotArray = array_filter(
				file($robotsTxt),
				static function ($row) {
					return stripos($row, 'sitemap:') !== 0;
				}
			);

			foreach ($xmlSiteMaps as $xmlSiteMap)
			{
				$myRobotArray[] = "Sitemap: " . Route::_(Uri::root() . $xmlSiteMap->path, false) . "\r\n";
			}

			try
			{
				file_put_contents($robotsTxt, $myRobotArray);
			}
			catch (Exception $e)
			{
				Factory::getApplication()->enqueueMessage(Text::_('COM_PWTSITEMAP_DASHBOARD_NO_ROBOTS_TXT'), 'error');

				return false;
			}
		}
	}
}
