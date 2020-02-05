<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model\Scanner\Util;


defined('_JEXEC') or die;

abstract class Filesystem
{
	static $normalizedRoot;

	/**
	 * Normalizes a path.
	 *
	 * Windows paths are normalized by having their backslashes converted to forward slashes. This normalization is
	 * aware of UNC paths.
	 *
	 * Furthermore, multiple forward slashes will be squashed into a single forward slash, e.g. convert
	 * /var//www////html to /var/www/html
	 *
	 * @param   string  $path  The path to transform
	 *
	 * @return  string
	 */
	public static function normalizePath($path)
	{
		$isUNC = false;

		if (IS_WIN)
		{
			// Is this a UNC path?
			$prefix = substr($path, 0, 2);
			$isUNC  = in_array($prefix, ['//', '\\\\']);

			// Change potential windows directory separator
			$path = strtr($path, '\\', '/');
		}

		// Remove multiple slashes
		while (strpos($path, '//') !== false)
		{
			$path = str_replace('//', '/', $path);
		}

		// Remove trailing slashes
		$path = rtrim($path, '/');

		// Fix UNC paths
		if ($isUNC)
		{
			$path = '//' . ltrim($path, '/');
		}

		return $path;
	}

	/**
	 * Returns the normalized file path relative to the site's root
	 *
	 * @param   string  $path
	 *
	 * @return  string
	 */
	public static function relativePath($path)
	{
		if (empty(static::$normalizedRoot))
		{
			static::$normalizedRoot = static::normalizePath(JPATH_ROOT);
		}

		$path = static::normalizePath($path);

		if (strpos($path, static::$normalizedRoot) === 0)
		{
			$path = substr($path, strlen(static::$normalizedRoot));
			$path = ltrim($path, '\\' . DIRECTORY_SEPARATOR);
		}

		return $path;
	}

}