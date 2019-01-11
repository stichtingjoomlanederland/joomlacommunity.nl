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

defined('_JEXEC') or die;

/**
 * Pwtsitemap script file.
 *
 * @package  PWT_Sitemap
 * @since    1.0
 */
class Com_PwtsitemapInstallerScript
{
	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 * @return void
	 */
	public function uninstall(JAdapterInstance $adapter)
	{
		try
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->update('#__extensions')
				->set($db->quoteName('enabled') . ' = ' . $db->quote(0))
				->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
				->where($db->quoteName('element') . ' = ' . $db->quote('pwtsitemap'))
				->where($db->quoteName('folder') . ' = ' . $db->quote('system'));

			$db->setQuery($query);
			$db->execute();
		}
		catch (\Exception $e)
		{
		}

	}
}
