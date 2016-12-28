<?php
/**
 * @package     Perfect_Sitemap
 * @subpackage  com_perfectsitemap
 *
 * @copyright   Copyright (C) 2016 Perfect Web Team. All rights reserved.
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
			$db->quoteName('name') . ' = ' . $db->quote('plg_system_perfectsitemap') . ' OR
			' . $db->quoteName('name') . ' = ' . $db->quote('plg_system_perfectsitemap_content')
		);

		// Update extensions table
		$q->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
		$db->setQuery($q);
		$db->execute();
	}
}