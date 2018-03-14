<?php
/**
 * @package     Perfect_Sitemap
 * @subpackage  com_perfectsitemap
 *
 * @copyright   Copyright (C) 2017 Perfect Web Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Perfect Sitemap Component Model
 *
 * @since  1.0.0
 */
class PerfectSitemapModelSitemap extends JModelItem
{
	/**
	 * Method to get an object.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   1.0.0
	 */
	public function getItems()
	{
		$app           = JFactory::getApplication();
		$sitemap_items = array();
		$skipped_items = array();

		// Get menu items
		$menuitems = $app->getMenu()->getMenu();

		// Filter menu items and add articles
		foreach ($menuitems as $menuitem)
		{
			// Filter menu items
			if ($this->filter($menuitem))
			{
				$skipped_items[] = $menuitem->id;
				continue;
			}

			// Filter menu items we don't want to show for the display format and items where the parent is skipped
			$format = $app->input->getCmd('format', 'html');

			if ($menuitem->params->get('addto' . $format . 'sitemap', 1) == false || in_array($menuitem->parent_id, $skipped_items))
			{
				$skipped_items[] = $menuitem->id;
				continue;
			}

			// Convert menu item to a PerfectSitmapItem
			$menuitem->link             = 'index.php?Itemid=' . $menuitem->id;
			$menuitem->addtohtmlsitemap = $menuitem->params->get('addtohtmlsitemap', 1);
			$menuitem->addtoxmlsitemap  = $menuitem->params->get('addtoxmlsitemap', 1);

			// Add item to sitemap
			$sitemap_items[] = new PerfectSitemapItem($menuitem->title, $menuitem->link, $menuitem->level);

			// Trigger plugin event
			$results = JEventDispatcher::getInstance()->trigger('onPerfectSitemapBuildSitemap', array($menuitem, $format));

			foreach ($results as $result)
			{
				$sitemap_items = array_merge($sitemap_items, $result);
			}
		}

		return $sitemap_items;
	}

	/**
	 * Filter a menu item on content type, language and access
	 *
	 * @param $menuitem
	 *
	 * @return bool
	 */
	protected function filter($menuitem)
	{
		$aFilter                = array('separator', 'heading', 'url', 'alias');
		$lang                   = JFactory::getLanguage();
		$authorizedAccessLevels = JFactory::getUser()->getAuthorisedViewLevels();

		return (in_array($menuitem->type, $aFilter)
			|| ($menuitem->language != $lang->getTag() and $menuitem->language != '*')
			|| !in_array($menuitem->access, $authorizedAccessLevels)
		);
	}
}
