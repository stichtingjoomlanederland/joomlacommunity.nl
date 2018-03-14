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
 * Perfect Sitemap Install script to active the plugins after install
 *
 * @since  1.4.0
 */
class Pkg_PerfectSitemapInstallerScript
{
	/**
	 * Enable plugin after install
	 *
	 * @return void
	 *
	 * @since  1.4.0
	 */
	public function install()
	{
		$db = JFactory::getDbo();
		$q  = $db->getQuery(true);

		// Fields to update
		$fields = array(
			$db->quoteName('enabled') . ' = 1'
		);

		// Conditions for update record
		$conditions = array(
			$db->qn('name') . ' = ' . $db->q('plg_system_perfectsitemap') . ' OR
			' . $db->qn('name') . ' = ' . $db->q('plg_perfectsitemap_content')
		);

		// Update extensions table
		$q
			->update($db->quoteName('#__extensions'))
			->set($fields)
			->where($conditions);

		$db->setQuery($q)->execute();
	}

	/**
	 * Uninstall the old `plg_system_perfectsitemap_content` plugin on update
	 * and enable the new plugin
	 *
	 * @return void
	 *
	 * @since  2.0.3
	 */
	public function postflight()
	{
		$db = JFactory::getDbo();
		$q  = $db->getQuery(true);

		// Enable new plugin
		$q
			->update($db->qn('#__extensions'))
			->set($db->qn('enabled') . '=' . '1')
			->where($db->qn('name') . '=' . $db->q('plg_perfectsitemap_content'));

		$db->setQuery($q)->execute();

		// Get extension_id and uninstall old plugin
		$q
			->clear()
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('name') . '=' . $db->q('plg_system_perfectsitemap_content'));

		$extension_id = $db->setQuery($q)->loadResult();

		if (!empty($extension_id))
		{
			try
			{
				JInstaller::getInstance()->uninstall('plugin', $extension_id);
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage("error", $e);
			}
		}
	}
}