<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;

defined('_JEXEC') or die;

/**
 * Load the PWT Image installer.
 *
 * @since    1.0
 */
class Pkg_PwtimageInstallerScript
{
	/**
	 * Method to run before an install/update/uninstall method
	 *
	 * @param   string  $type    The type of change (install, update or discover_install).
	 * @param   object  $parent  The class calling this method.
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
			$app->enqueueMessage(Text::sprintf('COM_PWTIMAGE_PHP_VERSION_ERROR', phpversion()), 'error');

			return false;
		}

		// Check if the Joomla! version is correct
		$version = new Version;

		if (version_compare($version->getShortVersion(), '3.8', '<') === true)
		{
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::sprintf('COM_PWTIMAGE_JOOMLA_VERSION_ERROR', $version->getShortVersion()), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Run after installing.
	 *
	 * @param   object  $parent  The calling class.
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
		$plugins = array('editors-xtd', 'system', 'fields');

		$query = $db->getQuery(true)
			->update($db->quoteName('#__extensions'))
			->set($db->quoteName('enabled') . ' =  1');

		try
		{
			foreach ($plugins as $index => $plugin)
			{
				$query->clear('where')
					->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
					->where($db->quoteName('element') . ' = ' . $db->quote('pwtimage'))
					->where($db->quoteName('folder') . ' = ' . $db->quote($plugin));

				$db->setQuery($query)->execute();
			}

			// Unpublish the Joomla image plugin
			$query->clear('set')
				->clear('where')
				->set($db->quoteName('enabled') . ' =  0')
				->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
				->where($db->quoteName('element') . ' = ' . $db->quote('image'))
				->where($db->quoteName('folder') . ' = ' . $db->quote('editors-xtd'));
			$db->setQuery($query)->execute();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::sprintf('PKG_PWTIMAGE_PLUGINS_NOT_ENABLED', $e->getMessage()), 'error');

			return false;
		}

		$app->enqueueMessage(Text::_('PKG_PWTIMAGE_PLUGINS_ENABLED'));

		return true;
	}

	/**
	 * Method to run during an update. This actually belongs in the component script.php but putting this code there
	 * doesn't do anything. Sounds like a bug to me.
	 *
	 * @param   object  $parent  The class calling this method.
	 *
	 * @return  bool  True on success | False on failure.
	 *
	 * @since   1.1.0
	 */
	public function update($parent)
	{
		// Check if there are any settings to migrate. We do this by checking if the user has any profiles stored
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__pwtimage_profiles'));
		$db->setQuery($query, 0, 1);

		$profileId = $db->loadResult();

		if ($profileId)
		{
			return true;
		}

		// There is no profile found, so let's convert any existing settings
		$params = ComponentHelper::getParams('com_pwtimage');

		// Set the width to the new format
		$widths = explode("\r\n", $params->get('imageWidth'));
		$newWidths = array();

		foreach ($widths as $index => $width)
		{
			$newWidths['width' . $index]['width'] = $width;
		}

		$params->set('width', $newWidths);

		// Set that all media fields are converted
		$params->set('allMediaFields', '1');

		// Insert the parameters into the profile
		$query->clear()
			->insert($db->quoteName('#__pwtimage_profiles'))
			->columns(
				$db->quoteName(
					array(
						'name',
						'settings',
						'ordering',
						'published',
						'created',
						'created_by'
					)
				)
			)
			->values(
				$db->quote('Default profile') . ',' .
				$db->quote(json_encode($params->jsonSerialize())) . ',' .
				'1,' .
				'1,' .
				$db->quote((new Date)->toSql()) . ',' .
				(int) Factory::getUser()->id
			);
		$db->setQuery($query)->execute();

		$profileId = $db->insertid();

		// Add the extension to all to keep the current behaviour
		$query->clear()
			->insert($db->quoteName('#__pwtimage_extensions'))
			->columns($db->quoteName(array('profile_id', 'path')))
			->values($profileId . ',' . $db->quote('all'));
		$db->setQuery($query)->execute();
	}

	/**
	 * Run after installing.
	 *
	 * @param   object  $parent  The calling class.
	 *
	 * @return  bool  True on success | False on failure.
	 *
	 * @since   1.0
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 */
	public function uninstall($parent)
	{
		$db  = Factory::getDbo();

		$query = $db->getQuery(true)
			->update($db->quoteName('#__extensions'))
			->set($db->quoteName('enabled') . ' =  1')
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
			->where($db->quoteName('element') . ' = ' . $db->quote('image'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('editors-xtd'));
			$db->setQuery($query)->execute();

		return true;
	}
}
