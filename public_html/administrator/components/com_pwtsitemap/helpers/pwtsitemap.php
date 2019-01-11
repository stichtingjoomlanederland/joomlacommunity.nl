<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * PWT Sitemap helper
 *
 * @since  1.0.0
 */
abstract class PwtSitemapHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			Text::_('COM_PWTSITEMAP_TITLE_DASHBOARD'),
			'index.php?option=com_pwtsitemap&view=dashboard',
			$vName == 'dashboard'
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_PWTSITEMAP_TITLE_MENUS'),
			'index.php?option=com_pwtsitemap&view=menus',
			$vName == 'menus'
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_PWTSITEMAP_TITLE_ITEMS'),
			'index.php?option=com_pwtsitemap&view=items',
			$vName == 'items'
		);
	}

	/**
	 * Get the paraemter of a menu item
	 *
	 * @param   int  $itemId  Menu item id
	 *
	 * @return  stdClass
	 *
	 * @since   1.0.0
	 */
	public static function GetMenuItemParameters($itemId)
	{
		$db = Factory::getDbo();
		$q  = $db
			->getQuery(true)
			->select($db->qn('params'))
			->from($db->qn('#__menu'))
			->where($db->qn('id') . '=' . (int) $itemId);

		return json_decode($db->setQuery($q)->loadResult());
	}

	/**
	 * Save a menu item parameter
	 *
	 * @param   int     $itemId     Menu item id
	 * @param   string  $parameter  Parameter to change
	 * @param   mixed   $value      Value of parameter
	 *
	 * @return  bool  True on success, false otherwise
	 *
	 * @since   1.0.0
	 */
	public static function SaveMenuItemParameter($itemId, $parameter, $value)
	{
		// Get current parameters and set new
		$params               = self::GetMenuItemParameters($itemId);
		$params->{$parameter} = $value;

		// Save parameters
		$params = json_encode($params);

		$db = Factory::getDbo();
		$q  = $db
			->getQuery(true)
			->clear()
			->update($db->quoteName('#__menu'))
			->set($db->quoteName('params') . '=' . $db->quote($params))
			->where($db->quoteName('id') . '=' . (int) $itemId);
		$db->setQuery($q)->execute();

		return true;
	}
}
