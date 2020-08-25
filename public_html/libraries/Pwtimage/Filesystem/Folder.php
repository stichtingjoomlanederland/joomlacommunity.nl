<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2020 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

namespace Pwtimage\Filesystem;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder as CmsFolder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Filter\OutputFilter;
use Pwtimage\Pwtimage;

/**
 * Folder functions.
 *
 * @package   Pwtimage
 * @since     1.3.2
 */
class Folder extends Pwtimage
{
	/**
	 * Load the list of folders of given folder.
	 *
	 * @param   string  $folder  The folder to load subfolders for
	 *
	 * @return  array List of folders
	 *
	 * @since   1.0.0
	 */
	public function load(string $folder): array
	{
		$baseFolder = $this->getImageFolder(true);

		// Check if baseFolder and the requested folder are the same
		if ($folder === '/')
		{
			$folder = $baseFolder;
		}

		return CmsFolder::folders(JPATH_SITE . $folder);
	}

	/**
	 * Retrieve the image folder.
	 *
	 * @param   bool    $base        Set to return only the base folder, not the subfolders.
	 * @param   string  $sourcePath  The source folder where to store the image.
	 * @param   string  $subPath     The subfolder where to store the image.
	 *
	 * @return  string  The name of the image folder prefixed and suffixed with /.
	 *
	 * @since   1.0.0
	 */
	public function getImageFolder($base = false, $sourcePath = '', $subPath = ''): string
	{
		// Get the settings
		$sourcePath = $sourcePath !== '' ? $sourcePath : $this->getSetting('sourcePath', '/images');
		$subPath    = $subPath !== '' ? $subPath : $this->getSetting('subPath');

		// Construct the source path
		if ($sourcePath[0] !== '/')
		{
			$sourcePath = '/' . $sourcePath;
		}

		// Construct the sub path
		$subPath = $this->replaceVariables($subPath);

		if (isset($subPath[0]) && $subPath[0] !== '/')
		{
			$subPath = '/' . $subPath;
		}

		// Construct the full path
		$imageFolder = $sourcePath . $subPath;

		// Check | try to create thumbnail folder
		$mode = intval($this->getSetting('chmod', 0755), 8);

		if (!CmsFolder::exists(JPATH_SITE . $imageFolder))
		{
			CmsFolder::create(JPATH_SITE . $imageFolder, $mode);
		}
		else
		{
			@chmod(JPATH_SITE . $imageFolder, $mode);
		}

		return $base ? $sourcePath : $imageFolder;
	}

	/**
	 * Do a placeholder replacement.
	 *
	 * @param   string  $subPath  The path to replace the variables in
	 *
	 * @return  string  The replaced string.
	 *
	 * @since   1.0.0
	 */
	private function replaceVariables($subPath): string
	{
		$user     = Factory::getUser();
		$username = ($user->name) ? OutputFilter::stringURLSafe($user->name) : 'guest';
		$find     = ['{year}', '{month}', '{day}', '{Y}', '{m}', '{d}', '{W}', '{userid}', '{username}'];
		$replace  = [date('Y'), date('m'), date('d'), date('Y'), date('m'), date('d'), date('W'), $user->id, $username];

		return str_replace($find, $replace, $subPath);
	}

	/**
	 * Load the folders for the select picker on the edit tab.
	 *
	 * @param   string  $sourcePath  The path to look for folders
	 *
	 * @return  array The list of folders found
	 *
	 * @since   1.1.0
	 */
	public function loadSelectFolders(string $sourcePath): array
	{
		// Clean site path
		$sitePath = Path::clean(JPATH_SITE, '/');

		// Get the list of folders the user can choose from
		$folders = CmsFolder::folders($sitePath . $sourcePath, '.', true, true);

		if (!is_array($folders))
		{
			$folders = [];
		}

		foreach ($folders as $index => $folder)
		{
			$folder          = Path::clean($folder, '/');
			$folders[$index] = str_replace($sitePath . $sourcePath . '/', '', $folder);
		}

		// Add the current folder as default
		array_unshift($folders, '/');

		return $folders;
	}
}
