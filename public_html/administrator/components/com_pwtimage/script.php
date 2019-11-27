<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\UserGroupsHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

/**
 * Load the PWT Image installer.
 *
 * @since    1.0
 */
class Com_PwtimageInstallerScript
{
	/**
	 * Method to run before an install/update/uninstall method
	 *
	 * @param   string  $type    The type of change (install, update or discover_install).
	 * @param   object  $parent  The class calling this method.
	 *
	 * @return  boolean  True on success | False on failure
	 *
	 * @since   1.0
	 *
	 * @throws  Exception
	 */
	public function preflight($type, $parent)
	{
		// Check if the PHP version is correct
		if (version_compare(phpversion(), '5.6', '<') === true)
		{
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::sprintf('COM_PWTIMAGE_PHP_VERSION_ERROR', phpversion()), 'error');

			return false;
		}

		// Check if the Joomla! version is correct
		$version = new JVersion;

		if (version_compare($version->getShortVersion(), '3.8', '<') === true)
		{
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::sprintf('COM_PWTIMAGE_JOOMLA_VERSION_ERROR', $version->getShortVersion()), 'error');

			return false;
		}

		// Clean out any old files
		$this->cleanFiles();

		return true;
	}

	/**
	 * Clean up old files.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	private function cleanFiles()
	{
		// List of old files to remove
		$files = array(
		);

		JFile::delete($files);

		$folders = array(
		);

		foreach ($folders as $folder)
		{
			if (JFolder::exists($folder))
			{
				JFolder::delete($folder);
			}
		}
	}

	/**
	 * Perform post-flight operations.
	 *
	 * @param   string  $type    The type of change (install, update or discover_install).
	 * @param   object  $parent  The class calling this method.
	 *
	 * @return  void
	 *
	 * @since   1.1.0
	 */
	public function postflight($type, $parent)
	{
		// Check if the all profile has no user groups
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('settings'))
			->from($db->quoteName('#__pwtimage_profiles'))
			->where($db->quoteName('name') . ' = ' . $db->quote('all'));
		$db->setQuery($query);

		$profile    = (new Registry($db->loadResult()));
		$userGroups = $profile->get('usergroups', array());

		if (empty($userGroups))
		{
			$profile->set('usergroups', array_keys(UserGroupsHelper::getInstance()->getAll()));

			$query->clear()
				->update($db->quoteName('#__pwtimage_profiles'))
				->set($db->quoteName('settings') . ' = ' . $db->quote($profile->toString()))
				->where($db->quoteName('name') . ' = ' . $db->quote('all'));
			$db->setQuery($query)->execute();
		}
	}
}
