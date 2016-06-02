<?php
/**
 * @package     Joomla_Versions
 * @subpackage  mod_joomlaversions
 *
 * @copyright   Copyright (C) 2016 Joomla! Community. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_joomlaversions
 *
 * @package     Joomla_Versions
 * @subpackage  mod_joomlaversions
 *
 * @since       1.0.0
 */
class ModJoomlaVersionsHelper
{
	/**
	 * Get all the versions from joomla.org
	 *
	 * @return array
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
	 */
	private static function getLatest($versions, $prefixes)
	{
		$latest_versions = array();

		foreach ($prefixes as $prefix)
		{
			$tmp = array_filter($versions, function($elem) use ($prefix) {
				return substr($elem, 0, 1) == $prefix;
			});

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
	 * @return array
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