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
	 * Get all the versions from joomla.org
	 *
	 * @param   string  $url  Update url to the XML file
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	private static function getJoomlaVersions($url)
	{
		// Check if the update url is available
		$http = JHttpFactory::getHttp();

		try
		{
			$response = $http->get($url);
		}
		catch (RuntimeException $e)
		{
			return null;
		}

		// Get all available Joomla! versions and filter for stable versions
		$xml      = simplexml_load_string($response->body);
		$versions = array();

		foreach ($xml->extension as $item)
		{
			$versions[] = (string) $item['version'];
		}

		return $versions;
	}

	/**
	 * Filter version for the latest versions per prefix
	 *
	 * @param   array  $versions  Versions to filter
	 * @param   array  $prefixes  Filter prefix
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	private static function getLatest($versions, $prefixes)
	{
		$latest_versions = array();

		foreach ($prefixes as $prefix)
		{
			$tmp = array_filter(
				$versions,
				function($elem) use ($prefix) {
					return substr($elem, 0, 1) == $prefix;
				}
			);

			if (empty($tmp))
			{
				continue;
			}

			$latest_versions[] = max($tmp);
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
		$response = self::getLatest(self::getJoomlaVersions($url), $prefixes);

		if ($response === null)
		{
			return false;
		}

		return json_encode($response);
	}
}
