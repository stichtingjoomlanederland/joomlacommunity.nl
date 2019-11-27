<?php
/**
 * @package     PwtImageWatermark
 *
 * @copyright   Copyright (C) 2017 Perfect Web Team. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Plugin\CMSPlugin;

defined('_JEXEC') or die;

/**
 * PWT Image Watermark Plugin
 *
 * @since  1.3.2
 */
class PlgPwtimageWatermark extends CMSPlugin
{
	/**
	 * Automatic load plugin language files
	 *
	 * @var boolean
	 *
	 * @since 1.3.2
	 */
	protected $autoloadLanguage = true;

	/**
	 * Adds a watermark to a given image
	 *
	 * @param   string $imageName The full path and name of the image to watermark.
	 * @param   int    $imageSize The size of the image.
	 * @param   int    $profile   The ID of the profile
	 *
	 * @return  boolean
	 *
	 * @since   1.3.2
	 */
	public function onAfterResizeImage($imageName, $imageSize, $profile)
	{
		try
		{
			return $this->addWatermark($imageName, $profile);
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/**
	 * Add a watermark to an image.
	 *
	 * @param   string $imageName The full path of the image to watermark
	 * @param   int    $profile   The ID of the profile
	 *
	 * @return  boolean  True on success | False on failure.
	 *
	 * @since   1.3.2
	 *
	 * @throws  InvalidArgumentException
	 */
	private function addWatermark($imageName, $profile)
	{
		$watermark = JPATH_SITE . '/' . $this->params->get('watermark_image');
		$profiles  = $this->params->get('profiles', array());
		$imageName = JPATH_SITE . '/' . $imageName;
		$result    = false;

		// Clear the cache
		clearstatcache();

		// Check if the files exist
		if (file_exists($imageName) && file_exists($watermark) && in_array($profile, $profiles))
		{
			jimport('joomla.filesystem.file');
			$mimeType = $this->getImageType($imageName);
			$image    = $this->createImage($mimeType, $imageName);
			$ext      = JFile::getExt($imageName);

			if ($image)
			{
				$mimeStamp = $this->getImageType($watermark);
				$stamp     = $this->createImage($mimeStamp, $watermark);

				// Set the margins for the stamp and get the height/width of the stamp image
				$marge_right  = $this->params->get('full_watermark_right', 0);
				$marge_bottom = $this->params->get('full_watermark_bottom', 0);
				$sx           = imagesx($stamp);
				$sy           = imagesy($stamp);

				// Copy the stamp image onto our photo using the margin offsets and the photo
				// width to calculate positioning of the stamp.
				imagecopy(
					$image,
					$stamp,
					imagesx($image) - $sx - $marge_right,
					imagesy($image) - $sy - $marge_bottom,
					0,
					0,
					imagesx($stamp),
					imagesy($stamp)
				);

				// Save the new image
				switch (strtolower($ext))
				{
					case "gif":
						$result = imagegif($image, $imageName);
						break;
					case "jpg":
					case "jpeg":
						$result = imagejpeg($image, $imageName, 100);
						break;
					case "png":
						$result = imagepng($image, $imageName);
						break;
					default:
						throw new InvalidArgumentException('PLG_PWTIMAGE_WATERMARK_UNKNOWN_EXTENSION');
						break;
				}

				imagedestroy($image);
			}
		}

		return $result;
	}

	/**
	 * Get the image type.
	 *
	 * @param   string  $imageName  The name of the image to get the type for
	 *
	 * @return  string  The mime type for a given image.
	 *
	 * @since   1.3.2
	 *
	 * @throws  InvalidArgumentException
	 */
	private function getImageType($imageName)
	{
		switch (strtolower(JFile::getExt($imageName)))
		{
			case 'jpg':
			case 'jpeg':
				$mimeType = 'image/jpeg';
				break;
			case 'png':
				$mimeType = 'image/png';
				break;
			case 'gif':
				$mimeType = 'image/gif';
				break;
			case 'bmp':
				$mimeType = 'image/bmp';
				break;
			default:
				throw new InvalidArgumentException('PLG_PWTIMAGE_WATERMARK_UNKOWN_IMAGE');
				break;
		}

		return $mimeType;
	}

	/**
	 * Create an image object.
	 *
	 * @param   string  $mime_type  The mime type of the image
	 * @param   string  $imagename  The full path image to create
	 *
	 * @return  mixed  Image resource on success | False on failure.
	 *
	 * @since   1.3.2
	 */
	private function createImage($mime_type, $imagename)
	{
		$image = null;

		switch ($mime_type)
		{
			case 'image/gif':
				if (function_exists('imagecreatefromgif'))
				{
					$image = @imagecreatefromgif($imagename);
				}
				else
				{
					return false;
				}
				break;
			case 'image/jpg':
			case 'image/jpeg':
				if (function_exists('imagecreatefromjpeg'))
				{
					$image = @imagecreatefromjpeg($imagename);
				}
				else
				{
					return false;
				}
				break;
			case 'image/png':
				if (function_exists('imagecreatefrompng'))
				{
					$image = @imagecreatefrompng($imagename);
				}
				else
				{
					return false;
				}
				break;
			default:
				return false;
				break;
		}

		return $image;
	}
}
