<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2020 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

namespace Pwtimage\Image;

defined('_JEXEC') or die;

use InvalidArgumentException;
use JEventDispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder as Cmsfolder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Utilities\ArrayHelper;
use Pwtimage\Filesystem\Folder;
use Pwtimage\Pwtimage;
use RuntimeException;
use stdClass;

/**
 * PWT Image.
 *
 * @package   Pwtimage
 * @since     1.3.2
 */
class Image
{
	/**
	 * Process an uploaded image.
	 *
	 * @param   array   $file            The uploaded file data.
	 * @param   string  $localFile       The uploaded file data.
	 * @param   string  $targetFile      The name of the target file.
	 * @param   string  $cropData        A JSON string with cropping details.
	 * @param   string  $ratio           The ratio to apply to the image.
	 * @param   integer $widths          The widths to resize the image to.
	 * @param   boolean $keepOriginal    Set if the original image sizes should be used.
	 * @param   string  $sourcePath      The source folder where to store the image.
	 * @param   string  $subPath         The subfolder where to store the image.
	 * @param   string  $origin          The origin of the field, so the profile can be loaded.
	 * @param   string  $backgroundColor The background color to use for cropping.
	 *
	 * @return  string  The created image.
	 *
	 * @since   1.0.0
	 */
	public function process(
		$file,
		$localFile,
		$targetFile,
		$cropData,
		$ratio,
		$widths,
		$keepOriginal = false,
		$sourcePath = null,
		$subPath = null,
		$origin = null,
		$backgroundColor = '#000000'
	)
	{
		// Clean site path
		$sitePath = Path::clean(JPATH_SITE, '/');

		// Remove any overlap in folder path
		$dirname = str_replace($sourcePath, '', dirname($localFile));

		if ($dirname && strpos($sitePath, $dirname) !== false)
		{
			$localFile = str_replace($dirname, '', $localFile);
		}

		// Check if local file exists
		if (!file_exists($sitePath . $localFile))
		{
			throw new InvalidArgumentException(Text::sprintf('COM_PWTIMAGE_FILE_MISSING', $localFile));
		}

		// Require needed libraries
		require_once JPATH_ADMINISTRATOR . '/components/com_pwtimage/helpers/pwtimage.php';

		// Check if the uploaded file is an array
		if (!is_array($file))
		{
			$file = array('tmp_name' => '');
		}

		$pwtImage = new Pwtimage($origin);

		// Set the memory limit if needed
		$memoryLimit = $pwtImage->getSetting('memoryLimit', false);

		if ($memoryLimit !== false)
		{
			ini_set('memory_limit', $memoryLimit . 'M');
		}

		// Get the base folder to work from
		$folderClass = new Folder;
		$baseFolder = $folderClass->getImageFolder(false, $sourcePath, $subPath);

		$mode = intval($pwtImage->getSetting('chmod', 0755), 8);

		if (!Cmsfolder::exists($sitePath . $baseFolder))
		{
			Cmsfolder::create($sitePath . $baseFolder, $mode);
		}
		else
		{
			@chmod($sitePath . $baseFolder, $mode);
		}

		// Get Variables
		$imageFolder = $sitePath . $baseFolder . '/';

		// Check if user uploaded a file or used a local file
		if (empty($file['tmp_name']) && $localFile)
		{
			$file['name'] = basename($localFile);
		}

		// Sanity check to see if we have a name
		if (!array_key_exists('name', $file))
		{
			throw new InvalidArgumentException(Text::_('COM_PWTIMAGE_FILENAME_MISSING'));
		}

		// Filename
		$filename = $this->formatFilename($file['name'], $pwtImage->getSetting('filenameFormat', '{random}'));

		// Path
		$originalFile = Path::clean($imageFolder . $filename, '/');

		// Do the upload if user uploaded a file
		if (!$localFile && $file['tmp_name'] && !File::upload($file['tmp_name'], $originalFile))
		{
			throw new RuntimeException(Text::_('Upload file error'));
		}

		if ($localFile && ($sitePath . $localFile !== $originalFile) && !File::copy($sitePath . $localFile, $originalFile))
		{
			throw new RuntimeException(Text::_('Upload file error'));
		}

		if ($keepOriginal)
		{
			$filePath = Path::clean($originalFile, '/');

			return str_replace($sitePath . '/', '', $filePath);
		}

		// Get the image details
		$imageDetails = getimagesize($originalFile);

		// Check if the user selected one or more sizes for resizing
		if ($widths !== 'null'
			&& $widths !== null
			&& $widths !== 'undefined'
			&& strpos($widths, ',')
		)
		{
			$widths = explode(',', $widths);
		}
		else
		{
			$widthSettings = $pwtImage->getSetting('width', []);
			$widths        = [];

			foreach ($widthSettings as $index => $widthSetting)
			{
				$widths[] = $widthSetting->width;
			}
		}

		$widths = ArrayHelper::toInteger($widths);

		// Make sure we have a width
		if (empty($widths))
		{
			// Get the width from the original file
			$widths = [$imageDetails[0]];
		}

		// Extract crop information
		$cropData = json_decode($cropData, false);

		// Get the ratio
		$ratio = explode('/', $ratio);

		// Validate the ratios
		if (empty($ratio[0]) || $ratio[0] === 'NaN')
		{
			$ratio[0] = 1;
		}

		if (!isset($ratio[1]) || $ratio[1] === 'NaN')
		{
			$ratio[1] = 1;
		}

		$ratio = ArrayHelper::toInteger($ratio, 1);

		// Set default settings if there is no cropping data
		if (!$cropData)
		{
			$cropData         = new stdClass;
			$cropData->x      = 0;
			$cropData->y      = 0;
			$cropData->height = $imageDetails[1];
			$cropData->width  = $imageDetails[0];

			if ($ratio[0] === $ratio[1])
			{
				$cropData->width  = $imageDetails[0];
				$cropData->height = $imageDetails[0];
			}
			elseif ($ratio[0] > $ratio[1])
			{
				$cropData->width  = $imageDetails[0];
				$cropData->height = $imageDetails[0] * ($ratio[1] / $ratio[0]);
			}
			else
			{
				$cropData->width  = $imageDetails[1] * ($ratio[0] / $ratio[1]);
				$cropData->height = $imageDetails[1];
			}

			$cropData->rotate = 0;
			$cropData->scaleX = 1;
			$cropData->scaleY = 1;
		}

		// Create the different size images
		$filePaths = array();

		// Set if we need to add the width to the image
		$appendWidth = false;

		if (count($widths) > 1)
		{
			$appendWidth = true;
		}

		// Generate the images and their widths
		foreach ($widths as $width)
		{
			// Get the target image name
			$outputName = $file['name'];

			if ($targetFile)
			{
				$outputName = $targetFile;
			}

			// Generate filename
			$filename = $this->formatFilename($outputName, $pwtImage->getSetting('filenameFormat'));

			// Add the width if needed
			if ($appendWidth)
			{
				$ext      = File::getExt($filename);
				$name     = basename($filename, '.' . $ext);
				$filename = $name . '_' . $width . '.' . $ext;
			}

			// Path
			$filePath = Path::clean($imageFolder . $filename, '/');

			// Image type
			$type = $this->getMimeType($originalFile);

			switch ($type)
			{
				case IMAGETYPE_GIF:
					$sourceImage = imagecreatefromgif($originalFile);
					break;

				case IMAGETYPE_JPEG:
					$sourceImage = imagecreatefromjpeg($originalFile);
					break;

				case IMAGETYPE_PNG:
				default:
					$sourceImage = imagecreatefrompng($originalFile);
					break;
			}

			if ($sourceImage === false)
			{
				File::delete($originalFile);
				throw new RuntimeException(Text::_('COM_PWTIMAGE_CANNOT_READ_SOURCE_FILE'));
			}

			if ($imageDetails[0] < $width && $pwtImage->getSetting('checkSize', 1))
			{
				$this->message = Text::_('COM_PWTIMAGE_NOT_MEET_WIDTH');
			}

			$this->cropImage(
				$sourceImage,
				$cropData,
				$imageDetails,
				$width,
				$ratio,
				$type,
				$filePath,
				$backgroundColor,
				$pwtImage->getSetting('dpi', '96')
			);

			$filePaths[] = str_replace($sitePath . '/', '', $filePath);

			$profileId = $pwtImage->getProfileId();

			PluginHelper::importPlugin('pwtimage');
			$dispatcher = JEventDispatcher::getInstance();
			$dispatcher->trigger('onAfterResizeImage', [end($filePaths), $width, $profileId]);
		}

		// Remove the original file as we are done processing
		File::delete($originalFile);

		return implode(', ', $filePaths);
	}

	/**
	 * Format a given filename.
	 *
	 * @param   string $originalName The original filename of the uploaded file.
	 * @param   string $format       The format for the filename.
	 *
	 * @return  string  The formatted filename.
	 *
	 * @since   1.0.0
	 */
	private function formatFilename($originalName, $format): string
	{
		// Check if we have a format
		if (!$format)
		{
			return $originalName;
		}

		// Do some customizing
		$time     = time();
		$user     = Factory::getUser();
		$username = ($user->name) ? $user->name : 'guest';

		// Replace the name
		$extension = File::getExt($originalName);
		$filename  = str_replace('{name}', basename($originalName, $extension), $format);

		// Replace random
		$prefix   = substr(str_shuffle(md5(time())), 0, 10);
		$filename = str_replace('{random}', $prefix, $filename);

		// Replace year
		$filename = str_replace('{Y}', date('Y', $time), $filename);
		$filename = str_replace('{year}', date('Y', $time), $filename);
		$filename = str_replace('{y}', date('y', $time), $filename);

		// Replace month
		$filename = str_replace('{M}', date('M', $time), $filename);
		$filename = str_replace('{m}', date('m', $time), $filename);
		$filename = str_replace('{month}', date('m', $time), $filename);
		$filename = str_replace('{F}', date('F', $time), $filename);
		$filename = str_replace('{n}', date('n', $time), $filename);

		// Replace week
		$filename = str_replace('{W}', date('d', $time), $filename);

		// Replace day
		$filename = str_replace('{d}', date('d', $time), $filename);
		$filename = str_replace('{D}', date('D', $time), $filename);
		$filename = str_replace('{j}', date('j', $time), $filename);
		$filename = str_replace('{l}', date('l', $time), $filename);

		// Replace hour
		$filename = str_replace('{g}', date('g', $time), $filename);
		$filename = str_replace('{G}', date('G', $time), $filename);
		$filename = str_replace('{h}', date('h', $time), $filename);
		$filename = str_replace('{H}', date('H', $time), $filename);

		// Replace minute
		$filename = str_replace('{i}', date('i', $time), $filename);

		// Replace seconds
		$filename = str_replace('{s}', date('s', $time), $filename);

		// Replace user ID
		$filename = str_replace('{userid}', $user->id, $filename);

		// Replace username
		$filename = str_replace('{username}', $username, $filename);

		// Clean up the filename so it is a safe name
		$filename = str_replace(' ', '-', $filename);
		$filename = File::makeSafe($filename);

		// Add the file extension, this must be after makeSafe as it removes dots
		$filename .= '.' . $extension;

		return $filename;
	}

	/**
	 * Get the image type of a given file.
	 *
	 * @param   string $sFilePath The path to the file
	 *
	 * @return  integer The image type.
	 *
	 * @since   1.1.0
	 */
	private function getMimeType($sFilePath)
	{
		// Exif_imagetype requires the file to be at least 12 bytes
		if (function_exists('exif_imagetype') && filesize($sFilePath) > 11)
		{
			$type = exif_imagetype($sFilePath);
		}
		else
		{
			switch (strtolower(File::getExt($sFilePath)))
			{
				case 'gif':
					$type = IMAGETYPE_GIF;
					break;
				case 'jpg':
				case 'jpeg':
					$type = IMAGETYPE_JPEG;
					break;
				default:
				case 'png':
					$type = IMAGETYPE_PNG;
					break;
			}
		}

		return $type;
	}

	/**
	 * Crop an image to the desired size.
	 *
	 * @param   resource $sourceImage     The source image from which to generate the cropped image
	 * @param   object   $cropData        The details of the crop specifications to apply to the image
	 * @param   array    $size            The original sizes of the source image
	 * @param   integer  $width           The new width to apply to the cropped image
	 * @param   array    $ratio           The ratio of the new image
	 * @param   string   $type            The type of image it is
	 * @param   string   $filePath        The name of the image to create
	 * @param   string   $backgroundColor The hexadecimal value of the background color to use for cropping
	 * @param   string   $dpi             The dpi for the new image
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	private function cropImage($sourceImage, $cropData, $size, $width, $ratio, $type, $filePath, $backgroundColor, $dpi)
	{
		// Get the RGB values for the background color
		list($red, $green, $blue) = sscanf($backgroundColor, "#%02x%02x%02x");

		// Natural height
		$originalHeight = $size[1];
		$originalWidth  = $size[0];
		$croppedHeight  = $originalHeight;
		$croppedWidth   = $originalWidth;

		// Flip vertical
		if ($cropData->scaleY === '-1')
		{
			imageflip($sourceImage, IMG_FLIP_VERTICAL);
		}

		// Flip horizontal
		if ($cropData->scaleX === '-1')
		{
			imageflip($sourceImage, IMG_FLIP_HORIZONTAL);
		}

		// Rotate the source image
		if (is_numeric($cropData->rotate) && (int) $cropData->rotate !== 0)
		{
			// PHP's degrees is opposite to CSS's degrees
			$newImage = imagerotate($sourceImage, -$cropData->rotate, imagecolorallocatealpha($sourceImage, $red, $green, $blue, 127));

			imagedestroy($sourceImage);
			$sourceImage = $newImage;

			$deg = abs($cropData->rotate) % 180;
			$arc = ($deg > 90 ? (180 - $deg) : $deg) * M_PI / 180;

			$croppedWidth  = $originalWidth * cos($arc) + $originalHeight * sin($arc);
			$croppedHeight = $originalWidth * sin($arc) + $originalHeight * cos($arc);

			// Fix rotated image miss 1px issue when degrees < 0
			$croppedWidth--;
			$croppedHeight--;
		}

		$finalWidth  = $width;
		$finalHeight = $ratio[1] / $ratio[0] * (int) $finalWidth;

		// If free form is used
		if ($ratio[0] === 1 && $ratio[1] === 1)
		{
			// We resize the cropped height in an equal ratio to how we crop the width
			$finalHeight = (int) ($cropData->height / ($cropData->width / $width));
		}

		$sourceWidth  = 0;
		$sourceHeight = 0;
		$destinationX = 0;
		$destinationW = 0;
		$destinationY = 0;
		$destinationH = 0;

		// Resize width
		if ($cropData->x <= -$cropData->width || $cropData->x > $croppedWidth)
		{
			$cropData->x = $sourceWidth = $destinationX = $destinationW = 0;
		}
		elseif ($cropData->x <= 0)
		{
			$destinationX = -$cropData->x;
			$cropData->x  = 0;
			$sourceWidth  = $destinationW = min($croppedWidth, $cropData->width + $cropData->x);
		}
		elseif ($cropData->x <= $croppedWidth)
		{
			$destinationX = 0;
			$sourceWidth  = $destinationW = min($cropData->width, $croppedWidth - $cropData->x);
		}

		// Resize height
		if ($sourceWidth <= 0 || $cropData->y <= -$cropData->height || $cropData->y > $croppedHeight)
		{
			$cropData->y = $sourceHeight = $destinationY = $destinationH = 0;
		}
		elseif ($cropData->y <= 0)
		{
			$destinationY = -$cropData->y;
			$cropData->y  = 0;
			$sourceHeight = $destinationH = min($croppedHeight, $cropData->height + $cropData->y);
		}
		elseif ($cropData->y <= $croppedHeight)
		{
			$destinationY = 0;
			$sourceHeight = $destinationH = min($cropData->height, $croppedHeight - $cropData->y);
		}

		// Scale to destination position and size
		$scaleratio   = $cropData->width / $finalWidth;
		$destinationX /= $scaleratio;
		$destinationY /= $scaleratio;
		$destinationW /= $scaleratio;
		$destinationH /= $scaleratio;

		if (!$this->enoughMemory($finalWidth, $finalHeight))
		{
			throw new RuntimeException(Text::_('COM_PWTIMAGE_NOT_ENOUGH_MEMORY'));
		}

		$destinationImage = imagecreatetruecolor($finalWidth, $finalHeight);

		// Add transparent background to destination image
		imagefill($destinationImage, 0, 0, imagecolorallocatealpha($destinationImage, $red, $green, $blue, 127));
		imagesavealpha($destinationImage, true);

		imagecopyresampled(
			$destinationImage,
			$sourceImage,
			$destinationX,
			$destinationY,
			$cropData->x,
			$cropData->y,
			$destinationW,
			$destinationH,
			$sourceWidth,
			$sourceHeight
		);

		// Since php 7.2
		if (function_exists('imageresolution') && $dpi > 0)
		{
			imageresolution($destinationImage, $dpi);
		}

		switch ($type)
		{
			case IMAGETYPE_GIF:
				imageGIF($destinationImage, $filePath);
				break;

			case IMAGETYPE_JPEG:
				imageJPEG($destinationImage, $filePath);
				break;

			case IMAGETYPE_PNG:
				imagePNG($destinationImage, $filePath);
				break;
		}

		// Clean up
		imagedestroy($destinationImage);
		imagedestroy($sourceImage);
	}

	/**
	 * Utility function to determine if there is enough memory left to create image prior to creating image to avoid fatal error.
	 * Thanks to http://php.net/manual/en/function.imagecreatetruecolor.php#99623
	 *
	 * @param   integer $width   The width of the image to create
	 * @param   integer $height  The height of the image to create
	 * @param   integer $channel The amount of channels that will be used (rgb is 3)
	 *
	 * @return  boolean True if enough memory, false otherwise.
	 *
	 * @since   1.1.0
	 */
	private function enoughMemory($width, $height, $channel = 3)
	{
		$limit = ini_get('memory_limit');

		if (preg_match('/^(\d+)(.)$/', $limit, $matches))
		{
			if ($matches[2] === 'M')
			{
				// When nnnM -> nnn MB
				$limit = $matches[1] * 1024 * 1024;
			}
			elseif ($matches[2] === 'K')
			{
				// When nnnK -> nnn KB
				$limit = $matches[1] * 1024;
			}
		}

		return ($width * $height * $channel * 1.7 < $limit - memory_get_usage());
	}
}
