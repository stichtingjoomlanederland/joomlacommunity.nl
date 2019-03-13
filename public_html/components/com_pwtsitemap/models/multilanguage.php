<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

JLoader::register('PwtSitemapModelSitemap', JPATH_ROOT . '/components/com_pwtsitemap/models/sitemap.php');
JLoader::register('PwtMultilanguageSitemapItem', JPATH_ROOT . '/components/com_pwtsitemap/models/sitemap/pwtsitemapmultilanguageitem.php');
JLoader::register('MenusAssociationsHelper', JPATH_ROOT . '/administrator/components/com_menus/helpers/associations.php');

/**
 * PWT Sitemap Component Multi-language Model
 *
 * @since  1.0.0
 */
class PwtSitemapModelMultilanguage extends PwtSitemapModelSitemap
{
	public function __construct(array $config = array())
	{
		parent::__construct($config);

		$this->type = 'multilanguage';
	}

	/**
	 * Add a menu item to the sitemap
	 *
	 * @param   JMenuItem $menuitem Menu item to add to the sitemap
	 * @param   string    $group    Set the group the item belongs to
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function addMenuItemToSitemap($menuitem, $group)
	{
		$sitemapItem               = new PwtMultilanguageSitemapItem($menuitem->title, $menuitem->link, $menuitem->level, null);
		$sitemapItem->associations = $this->getAssociations($menuitem);

		$this->sitemap->addItem($sitemapItem);
	}

	/**
	 * Get language associated menu items
	 *
	 * @param   $menuitem  JMenuItem  Menu item to add
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	private function getAssociations($menuitem)
	{
		$helper = new MenusAssociationsHelper();

		// Get associations and map to JMenu objects
		$associations = array_map(function ($value) use ($helper) {
			return $helper->getItem('item', explode(':', $value->id)[0]);
		}, $helper->getAssociations('item', $menuitem->id));

		return $associations;
	}
}