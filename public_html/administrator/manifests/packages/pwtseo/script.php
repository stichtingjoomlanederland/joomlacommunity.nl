<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * Load the PWT SEO installer.
 *
 * @since    1.0
 */
class Pkg_PwtSEOInstallerScript
{
	/**
	 * The last version where there was a change to the scoring. This updates the flag_outdated to show the user that the score is outdated
	 *
	 * @var   string
	 *
	 * @since 1.0.2
	 */
	private $lastScoreChange = '1.3.0.0';

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
			$app->enqueueMessage(Text::sprintf('COM_PWTSEO_PHP_VERSION_ERROR', phpversion()), 'error');

			return false;
		}

		// Check if the Joomla! version is correct
		$version = new JVersion;

		if (version_compare($version->getShortVersion(), '3.8', '<') === true)
		{
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::sprintf('COM_PWTSEO_JOOMLA_VERSION_ERROR', $version->getShortVersion()), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Method to run after an install/update/uninstall method
	 * $parent is the class calling this method
	 * $type is the type of change (install, update or discover_install)
	 *
	 * @param   string $type   The type of change (install, update or discover_install).
	 * @param   object $parent The class calling this method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function postflight($type, $parent)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->update($db->quoteName('#__extensions'))
			->set($db->quoteName('enabled') . ' = 1')
			->where(
				array(
					$db->quoteName('type') . ' = ' . $db->quote('plugin'),
					$db->quoteName('element') . ' = ' . $db->quote('pwtseo'),
					$db->quoteName('folder') . ' = ' . $db->quote('system')
				)
			);

		$db->setQuery($query)->execute();

		if ($type === 'update')
		{
			// Check if there's an update to the scoring, so we can notify the user
			$query
				->clear()
				->select('COUNT(*)')
				->from($db->quoteName('#__plg_pwtseo', 'seo'))
				->where('INET_ATON(CONCAT(' . $db->quoteName('version') . ', ".0")) < INET_ATON(' . $db->quote($this->lastScoreChange) . ')');

			try
			{
				$bHasOutdatedScores = $db->setQuery($query)->loadResult();

				if ($bHasOutdatedScores > 0)
				{
					$app = Factory::getApplication();
					$app->enqueueMessage(Text::sprintf('COM_PWTSEO_OUTDATED_SCORES'), 'warning');

					$query
						->clear()
						->update($db->quoteName('#__plg_pwtseo', 'seo'))
						->set($db->quoteName('flag_outdated') . ' = 1')
						->where('INET_ATON(CONCAT(' . $db->quoteName('version') . ', ".0")) < INET_ATON(' . $db->quote($this->lastScoreChange) . ')');

					$db->setQuery($query)->execute();
				}
			}
			catch (Exception $e)
			{
			}
		}
	}
}
