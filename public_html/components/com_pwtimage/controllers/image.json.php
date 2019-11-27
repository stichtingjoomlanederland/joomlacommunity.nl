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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.image.image');

/**
 * PWT Image image controller.
 *
 * @since       1.0
 */
class PwtimageControllerImage extends BaseController
{
	/**
	 * Process an image selection.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function processImage()
	{
		// Check for request forgeries
		$this->checkRequestToken() or jexit('Invalid Token');

		$file            = $this->input->files->get('image', null);
		$localFile       = $this->input->getString('pwt-image-localFile', null);
		$targetFile      = $this->input->getString('pwt-image-targetFile', null);
		$cropData        = $this->input->getString('pwt-image-data', null);
		$ratio           = $this->input->getString('pwt-image-ratio', null);
		$width           = $this->input->getString('pwt-image-width', null);
		$sourcePath      = $this->input->getString('pwt-image-sourcePath', null);
		$subPath         = $this->input->getString('pwt-image-subPath', null);
		$backgroundColor = $this->input->getString('pwt-image-backgroundColor', '#000000');
		$origin          = $this->input->getString('pwt-image-origin', null);
		$useOriginal     = filter_var($this->input->getString('pwt-image-useOriginal', null), FILTER_VALIDATE_BOOLEAN);
		$keepOriginal    = filter_var($this->input->getString('pwt-image-keepOriginal', null), FILTER_VALIDATE_BOOLEAN);
		$image           = '';

		try
		{
			// Sanity check that we have an image
			if (is_null($file) && is_null($localFile))
			{
				throw new InvalidArgumentException(Text::_('COM_PWTIMAGE_FILENAME_MISSING'));
			}

			$message = '';
			$error   = false;

			if ($useOriginal)
			{
				// Remove '/' from localfile to get correct image path to store
				$image = substr($localFile, 1);
			}
			else
			{
				/** @var PwtimageModelImage $model */
				$model = $this->getModel('Image', 'PwtimageModel');

				$image   = $model->processImage(
					$file,
					$localFile,
					$targetFile,
					$cropData,
					$ratio,
					$width,
					$keepOriginal,
					$sourcePath,
					$subPath,
					$origin,
					$backgroundColor
				);
				$message = $model->getMessage();
			}
		}
		catch (Exception $e)
		{
			$message = $e->getMessage();
			$error   = true;
		}

		echo new JsonResponse($image, $message, $error);
	}

	/**
	 * Check if the token is valid.
	 *
	 * @param   string $method The request method being used.
	 *
	 * @return  boolean  True if token is valid, False if token is not valid.
	 *
	 * @since   1.1.0
	 */
	private function checkRequestToken($method = 'post')
	{
		// Check if we come from the backend
		if ($sessionId = $this->input->server->get('HTTP_X_CSRF_TOKEN', '', 'alnum'))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('userid'))
				->from($db->quoteName('#__session'))
				->where($db->quoteName('session_id') . ' = ' . $db->quote($sessionId));

			$db->setQuery($query);

			$userId = $db->loadResult();

			$params      = ComponentHelper::getParams('com_pwtimage');
			$guestUpload = $params->get('guestupload', 0);

			if (!$guestUpload && (!$userId || is_null($userId) || $userId < 1))
			{
				return false;
			}
		}
		else
		{
			// Coming in from the frontend
			return Session::checkToken($method);
		}

		return true;
	}

	/**
	 * Load the list of files and folders of given folder.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function loadFolder()
	{
		// Check for request forgeries
		$this->checkRequestToken() or jexit('Invalid Token');

		jimport('joomla.filesystem.folder');
		require_once JPATH_ADMINISTRATOR . '/components/com_pwtimage/helpers/pwtimage.php';

		$folder     = $this->input->getString('folder', null);
		$helper     = new PwtimageHelper;
		$baseFolder = $helper->getImageFolder(true);

		// Check if baseFolder and the requested folder are the same
		if ($folder === '/')
		{
			$folder = $baseFolder;
		}

		$folders = JFolder::folders(JPATH_SITE . $folder);
		$files   = JFolder::files(
			JPATH_SITE . $folder,
			'(.' . implode('|.', explode(',', ComponentHelper::getParams('com_media')->get('image_extensions', 'jpg,jpeg,png,gif,bmp'))) . ')'
		);

		echo new JsonResponse(array('folders' => $folders, 'files' => $files, 'basePath' => $folder));
	}

	/**
	 * Load the folders for the select picker on the edit tab.
	 *
	 * @return  void
	 *
	 * @since   1.1.0
	 */
	public function loadSelectFolders()
	{
		// Check for request forgeries
		$this->checkRequestToken() or jexit('Invalid Token');

		$sourcePath = $this->input->getString('sourcePath', null);

		// Clean site path
		$sitePath = JPath::clean(JPATH_SITE, '/');

		// Get the list of folders the user can choose from
		$folders = JFolder::folders($sitePath . $sourcePath, '.', true, true);

		if (!is_array($folders))
		{
			$folders = array();
		}

		foreach ($folders as $index => $folder)
		{
			$folder          = JPath::clean($folder, '/');
			$folders[$index] = str_replace($sitePath . $sourcePath . '/', '', $folder);
		}

		// Add the current folder as default
		array_unshift($folders, '/');

		echo new JsonResponse(array($folders));
	}

	/**
	 * Returns meta data for a specified image
	 *
	 * @return  void
	 *
	 * @since   1.3.0
	 */
	public function loadMetaData()
	{
		// Check for request forgeries
		$this->checkRequestToken() or jexit('Invalid Token');

		$response = array();
		$path     = $this->input->getString('image', null);

		if ($path)
		{
			if (file_exists(JPATH_SITE . $path))
			{
				$response         = getimagesize(JPATH_SITE . $path);
				$response['size'] = filesize(JPATH_SITE . $path);
				$response['name'] = basename($path);
			}
		}

		echo new JsonResponse($response);
	}
}
