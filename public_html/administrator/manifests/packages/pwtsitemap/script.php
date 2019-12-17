<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;

defined('_JEXEC') or die;

/**
 * PWT Sitemap Install script to active the plugins after install
 *
 * @since  1.0.0
 */
class Pkg_PwtSitemapInstallerScript
{
	/**
	 * Method to run before an install/update/uninstall method
	 *
	 * @param   string  $type    The type of change (install, update or discover_install).
	 * @param   object  $parent  The class calling this method.
	 *
	 * @return  boolean  True on success | False on failure
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function preflight($type, $parent)
	{

		// Check if the PHP version is correct
		if (version_compare(phpversion(), '5.6', '<') === true)
		{
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::sprintf('COM_PWTSITEMAP_PHP_VERSION_ERROR', phpversion()), 'error');

			return false;
		}

		// Check if the Joomla! version is correct
		$version = new Version;

		if (version_compare($version->getShortVersion(), '3.8', '<') === true)
		{
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::sprintf('COM_PWTSITEMAP_JOOMLA_VERSION_ERROR', $version->getShortVersion()), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Run after installing.
	 *
	 * @param   object  $parent  The calling class.
	 *
	 * @return  boolean  True on success | False on failure.
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 */
	public function postflight($parent)
	{
		$app = Factory::getApplication();
		$db  = Factory::getDbo();

		// Enable the plugins
		$plugins                 = array();
		$plugins['system'][]     = 'pwtsitemap';
		$plugins['pwtsitemap'][] = 'contact';
		$plugins['pwtsitemap'][] = 'content';
		$plugins['pwtsitemap'][] = 'newsfeed';
		$plugins['pwtsitemap'][] = 'tag';

		$query = $db->getQuery(true)
			->update($db->quoteName('#__extensions'))
			->set($db->quoteName('enabled') . ' =  1');

		try
		{
			foreach ($plugins as $group => $plugin)
			{
				foreach ($plugin as $index => $item)
				{
					$query->clear('where')
						->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
						->where($db->quoteName('element') . ' = ' . $db->quote($item))
						->where($db->quoteName('folder') . ' = ' . $db->quote($group));

					$db->setQuery($query)->execute();
				}
			}
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::sprintf('PKG_PWTSITEMAP_PLUGINS_NOT_ENABLED', $e->getMessage()), 'error');

			return false;
		}

		$app->enqueueMessage(Text::_('PKG_PWTSITEMAP_PLUGINS_ENABLED'));

		return true;
	}

	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	public function install(JAdapterInstance $adapter)
	{
		self::addMenuTypesToPwtSiteMap($adapter);
	}

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	public function update(JAdapterInstance $adapter)
	{
		self::addMenuTypesToPwtSiteMap($adapter);
	}

	/**
	 * Check if pwtsitemap_menu_types has items. If not, get menu types and items to pwt table
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	public function addMenuTypesToPwtSiteMap(JAdapterInstance $adapter)
	{
		// Check if there are already any records in the table
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__pwtsitemap_menu_types'));
		$db->setQuery($query);
		$count = $db->loadResult();

		// Quit this method if there are already any records
		if ($count > 0)
		{
			return;
		}

		// Get all Menu Types and insert them with ordering in pwtsitemap_menu_types
		try
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('id'))
				->from($db->quoteName('#__menu_types'))
				->where($db->quoteName('client_id') . ' = 0');

			$db->setQuery($query);
			$results = $db->loadColumn();

			$query = $db->getQuery(true)
				->insert($db->quoteName('#__pwtsitemap_menu_types'))
				->columns(array($db->quoteName('menu_types_id'), $db->quoteName('ordering')));

			foreach ($results as $key => $result)
			{
				$query->values($result . ',' . ($key + 1));
			}

			$db->setQuery($query);
			$db->execute();
		}
		catch (\Exception $e)
		{
		}
	}
}
