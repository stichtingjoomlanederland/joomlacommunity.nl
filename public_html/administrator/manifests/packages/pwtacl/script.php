<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * PWT ACL Install script to active the plugins after install
 *
 * @since  1.0.0
 */
class Pkg_PwtAclInstallerScript
{
	/**
	 * Method to run before an install/update/uninstall method
	 *
	 * @param   string $type   The type of change (install, update or discover_install).
	 * @param   object $parent The class calling this method.
	 *
	 * @return  bool  True on success | False on failure
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
			$app->enqueueMessage(Text::sprintf('COM_ACL_PHP_VERSION_ERROR', phpversion()), 'error');

			return false;
		}

		// Check if the Joomla! version is correct
		$version = new Version;

		if (version_compare($version->getShortVersion(), '3.8', '<') === true)
		{
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::sprintf('COM_ACL_JOOMLA_VERSION_ERROR', $version->getShortVersion()), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Run after installing.
	 *
	 * @param   object $parent The calling class.
	 *
	 * @return  bool  True on success | False on failure.
	 *
	 * @since   1.0
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 */
	public function postflight($parent)
	{
		$app = Factory::getApplication();
		$db  = Factory::getDbo();

		// Enable the plugins
		$plugins             = array();
		$plugins['system'][] = 'pwtacl';

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
			$app->enqueueMessage(Text::sprintf('PKG_PWTACL_PLUGINS_NOT_ENABLED', $e->getMessage()), 'error');

			return false;
		}

		$app->enqueueMessage(Text::_('PKG_PWTACL_PLUGINS_ENABLED'));

		// Check for old ACL Manager package installations
		$this->removeExtension('package', 'pkg_aclmanager');

		// Check for old ACL Manager component installations
		$this->removeExtension('component', 'com_aclmanager');

		// Check for old ACL Manager plugin installations
		$this->removeExtension('plugin', 'aclmanager');

		// Remove .DS_Store files
		$this->cleanupSystemFiles();

		return true;
	}

	/**
	 * Method to remove extension
	 *
	 * @param   string $type    The extension type
	 * @param   string $element The extension element
	 *
	 * @since 3.0
	 * @return void
	 */
	private function removeExtension($type, $element)
	{
		$app = Factory::getApplication();
		$db  = Factory::getDbo();

		// Check if we can find an ACL Manager package
		$query = $db->getQuery(true)
			->select('extension_id')
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('type') . ' = ' . $db->quote($type))
			->where($db->quoteName('element') . ' = ' . $db->quote($element));

		$extensionId = $db->setQuery($query)->loadResult();

		// Special behaviour for ACL Manager to copy the settings
		if ($element == 'pkg_aclmanager' || $element == 'com_aclmanager')
		{
			// Get settings for com_aclmanager
			$query = $db->getQuery(true)
				->select('params')
				->from($db->quoteName('#__extensions'))
				->where($db->quoteName('element') . ' = ' . $db->quote('com_aclmanager'));

			$params = $db->setQuery($query)->loadResult();

			// Store settings for com_pwtacl
			$query = $db->getQuery(true)
				->update($db->quoteName('#__extensions'))
				->set($db->quoteName('params') . ' = ' . $db->quote($params))
				->where($db->quoteName('element') . ' = ' . $db->quote('com_pwtacl'));

			$db->setQuery($query)->execute();
		}

		// We found an extension, lets remove it
		if ($extensionId)
		{
			// We can now remove the extension
			$installer = Installer::getInstance();
			$installer->uninstall($type, $extensionId);

			// Display ACL Manager removal message
			$app->enqueueMessage(Text::_('PKG_PWTACL_ACLMANAGER_REMOVED'));
		}
	}

	/**
	 * Cleanup OS system files
	 *
	 * @return  void
	 *
	 * @since   2.5.0
	 */
	private function cleanupSystemFiles()
	{
		$folders = array(
			JPATH_ADMINISTRATOR . '/components/com_pwtacl',
			JPATH_SITE . '/media/com_pwtacl',
			JPATH_PLUGINS . '/system/pwtacl'
		);

		foreach ($folders as $folder)
		{
			$componentFiles = JFolder::files($folder, '.DS_Store', true, true, array(), array());
			$this->removeOldFiles($componentFiles);
		}
	}

	/**
	 * Function to remove old files
	 *
	 * @param   array $files Files to remove
	 *
	 * @return  void
	 *
	 * @since   2.5.0
	 */
	private function removeOldFiles($files)
	{
		if (!$files)
		{
			return;
		}

		// Remove the admin files
		foreach ($files as $file)
		{
			if (is_file($file))
			{
				JFile::delete($file);
			}
		}
	}
}
