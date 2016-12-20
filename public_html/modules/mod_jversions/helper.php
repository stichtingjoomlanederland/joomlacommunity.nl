<?php
/**
 * @package     JVersions
 * @subpackage  mod_jversions
 *
 * @copyright   Copyright (C) 2016 Niels van der Veer. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_jversions
 *
 * @package     JVersions
 * @subpackage  mod_jversions
 *
 * @since       1.0.0
 */
class ModJVersionsHelper
{
	/**
	 * Get the latest Joomla! versions for all branches
	 *
	 * @return  stdClass
	 *
	 * @since   1.0.0
	 */
	private static function getLatestVersions()
	{
		// Check if the update url is available
		$http = JHttpFactory::getHttp();

		try
		{
			$response = $http->get("https://downloads.joomla.org/api/v1/latest/cms");
		}
		catch (RuntimeException $e)
		{
			return null;
		}

		return json_decode($response->body);
	}

	/**
	 * Filter version for the latest versions per prefix
	 *
	 * @param   stdClass  $versions  Versions to filter
	 * @param   array     $prefixes  Filter prefix
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	private static function getLatest($versions, $prefixes)
	{
		$latest_versions = array();

		foreach ($versions->branches as $branch)
		{
			if (in_array($branch->branch, $prefixes))
			{
				$latest_versions[] = $branch->version;
			}
		}

		return $latest_versions;
	}

	/**
	 * Get the latest Joomla! version
	 *
	 * @return mixed  array on success, false when there is no respone
	 *
	 * @since  1.0.0
	 */
	public static function getAjax()
	{
		// Get input variables
		$url      = JFactory::getApplication()->input->get('update_url', null, 'raw');
		$prefixes = JFactory::getApplication()->input->get('prefixes', null, 'raw');

		// Get response
		$response = self::getLatest(self::getLatestVersions(), $prefixes);

		if ($response === null)
		{
			return false;
		}

		return json_encode($response);
	}
}
