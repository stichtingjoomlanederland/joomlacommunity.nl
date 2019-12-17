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
use Joomla\CMS\Language\Text;

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
			$vName === 'dashboard'
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_PWTSITEMAP_TITLE_MENUS'),
			'index.php?option=com_pwtsitemap&view=menus',
			$vName === 'menus'
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_PWTSITEMAP_TITLE_ITEMS'),
			'index.php?option=com_pwtsitemap&view=items',
			$vName === 'items'
		);
	}

	/**
	 * Save a menu item parameter
	 *
	 * @param   int     $itemId     Menu item id
	 * @param   string  $parameter  Parameter to change
	 * @param   mixed   $value      Value of parameter
	 *
	 * @return  boolean  True on success, false otherwise
	 *
	 * @since   1.0.0
	 */
	public static function saveMenuItemParameter($itemId, $parameter, $value)
	{
		// Get current parameters and set new
		$params               = self::getMenuItemParameters($itemId);
		$params->{$parameter} = $value;

		// Save parameters
		$params = json_encode($params);

		$db    = Factory::getDbo();
		$query = $db
			->getQuery(true)
			->clear()
			->update($db->quoteName('#__menu'))
			->set($db->quoteName('params') . '=' . $db->quote($params))
			->where($db->quoteName('id') . '=' . (int) $itemId);
		$db->setQuery($query)->execute();

		return true;
	}

	/**
	 * Get the parameter of a menu item
	 *
	 * @param   int  $itemId  Menu item id
	 *
	 * @return  stdClass The menu item parameters
	 *
	 * @since   1.0.0
	 */
	public static function getMenuItemParameters($itemId)
	{
		$db    = Factory::getDbo();
		$query = $db
			->getQuery(true)
			->select($db->quoteName('params'))
			->from($db->quoteName('#__menu'))
			->where($db->quoteName('id') . '=' . (int) $itemId);

		return json_decode($db->setQuery($query)->loadResult(), false);
	}

	/**
	 * Save a menu item parameter
	 *
	 * @param   int    $itemId  Menu item id
	 * @param   mixed  $value   Value of parameter
	 *
	 * @return  boolean  True on success, false otherwise
	 *
	 * @since   1.0.0
	 */
	public static function saveMenuItemRobots($itemId, $value)
	{
		// Get current parameters and set new
		$params         = self::getMenuItemParameters($itemId);
		$params->robots = $value;

		// Save parameters
		$params = json_encode($params);

		$db    = Factory::getDbo();
		$query = $db
			->getQuery(true)
			->clear()
			->update($db->quoteName('#__menu'))
			->set($db->quoteName('params') . '=' . $db->quote($params))
			->where($db->qn('id') . '=' . (int) $itemId);
		$db->setQuery($query)->execute();

		return true;
	}
}
