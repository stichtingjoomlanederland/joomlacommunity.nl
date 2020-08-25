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

use Joomla\CMS\Filesystem\Folder;
use Pwtimage\Pwtimage;

/**
 * File functions.
 *
 * @package   Pwtimage
 * @since     1.3.2
 */
class File extends Pwtimage
{
	/**
	 * Load the list of files of given folder.
	 *
	 * @param   string  $folder The folder to get the files for
	 *
	 * @return  array List of files in the given folder
	 *
	 * @since   1.3.2
	 */
	public function load(string $folder): array
	{
		return Folder::files(
			JPATH_SITE . $folder,
			'(.' . implode('|.', explode(',', $this->allowedExtensions)) . ')'
		);
	}

	/**
	 * Returns meta data for a specified image
	 *
	 * @param   string  $imagePath  The image to get the metadata for
	 *
	 * @return  array The file meta data
	 *
	 * @since   1.3.0
	 */
	public function loadMetaData(string $imagePath): array
	{
		$response = [];

		if (file_exists(JPATH_SITE . $imagePath))
		{
			$response         = getimagesize(JPATH_SITE . $imagePath);
			$response['size'] = filesize(JPATH_SITE . $imagePath);
			$response['name'] = basename($imagePath);
		}

		return $response;
	}
}
