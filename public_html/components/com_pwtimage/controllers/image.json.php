<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2020 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use Pwtimage\Filesystem\File;
use Pwtimage\Filesystem\Folder;
use Pwtimage\Image\Image;

/**
 * PWT Image image controller.
 *
 * @since   1.0.0
 */
class PwtimageControllerImage extends BaseController
{
	/**
	 * Process an image selection.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function processImage()
	{
		// Check for request forgeries
		$this->checkToken() or jexit('Invalid Token');

		$canDo   = ContentHelper::getActions('com_pwtimage');
		$image   = '';
		$message = '';
		$error   = false;

		if ($canDo->get('core.edit'))
		{
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

			try
			{
				// Sanity check that we have an image
				if ($file === null && $localFile === null)
				{
					throw new InvalidArgumentException(Text::_('COM_PWTIMAGE_FILENAME_MISSING'));
				}

				// Remove '/' from localfile to get correct image path to store
				$image = substr($localFile, 1);

				if (!$useOriginal)
				{
					$image = (new Image)->process(
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
				}
			}
			catch (Exception $exception)
			{
				$message = $exception->getMessage();
				$error   = true;
			}
		}

		echo new JsonResponse($image, $message, $error);
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
		$this->checkToken() or jexit('Invalid Token');

		$folders = [];
		$files   = [];
		$folder  = $this->input->getString('folder', null);

		$canDo = ContentHelper::getActions('com_pwtimage');

		if ($canDo->get('pwtimage.accessfolder'))
		{
			$folderClass = new Folder;
			$fileClass   = new File;
			$folders     = $folderClass->load($folder);
			$files       = $fileClass->load($folder);
		}

		echo new JsonResponse(['folders' => $folders, 'files' => $files, 'basePath' => $folder]);
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
		$this->checkToken() or jexit('Invalid Token');

		$folders = [];
		$canDo   = ContentHelper::getActions('com_pwtimage');

		if ($canDo->get('pwtimage.accessfolder'))
		{
			$sourcePath = $this->input->getString('sourcePath', null);

			$folderClass = new Folder;

			$folders = $folderClass->loadSelectFolders($sourcePath);
		}

		echo new JsonResponse([$folders]);
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
		$this->checkToken() or jexit('Invalid Token');

		$response = [];
		$canDo    = ContentHelper::getActions('com_pwtimage');

		if ($canDo->get('core.edit'))
		{
			$path      = $this->input->getString('image', null);
			$fileClass = new File;

			$response = $fileClass->loadMetaData($path);
		}

		echo new JsonResponse($response);
	}
}
