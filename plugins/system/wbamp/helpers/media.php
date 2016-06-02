<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.3.1.490
 * @date        2016-05-18
 */

defined('_JEXEC') or die();

class WbampHelper_Media
{
	static private $rootPath   = '';
	static private $pathLength = 0;
	static private $rootUrl    = '';
	static private $rootLength = 0;

	/**
	 * Get an image size from the file
	 *
	 * @param $url
	 * @return array Width/height of the image, 0/0 if not found
	 */
	public static function getImageSize($url)
	{
		if (empty(self::$rootPath))
		{
			self::$rootPath = JUri::base(true);
			self::$pathLength = JString::strlen(self::$rootPath);
			self::$rootUrl = Juri::base();
			if (JFactory::getApplication()->isAdmin())
			{
				self::$rootUrl = str_replace('/administrator', '', self::$rootUrl);
				self::$rootPath = str_replace('/administrator', '', self::$rootPath);
			}
			self::$rootLength = JString::strlen(self::$rootUrl);
		}

		// default values ?
		$dimensions = array('width' => 0, 'height' => 0);

		// build the physical path from the URL
		$cleanedPath = substr($url, 0, self::$rootLength) == self::$rootUrl ? substr($url, self::$rootLength) : $url;
		$cleanedPath = !empty(self::$pathLength) && substr($cleanedPath, 0, self::$pathLength) == self::$rootPath ? substr($url, self::$pathLength) : $cleanedPath;

		$imagePath = JPATH_ROOT . '/' . $cleanedPath;

		if (file_exists($imagePath))
		{
			$imageInfos = getimagesize($imagePath);
			if (!empty($imageInfos))
			{
				$dimensions = array('width' => $imageInfos[0], 'height' => $imageInfos[1]);
			}
		}

		return $dimensions;
	}

	/**
	 * Finds a local image and read its dimensions
	 * If current dimensions as known by the caller are missing,
	 * they'll be replaced by the dimensions read from file
	 * This still allow overriding dimensions by user
	 *
	 * If the image is not local, then size cannot be read, and
	 * current dimensions will be unchanged.
	 *
	 * @param $url
	 * @param $currentDimensions
	 * @return mixed
	 */
	public static function findImageSizeIfMissing($url, $currentDimensions)
	{
		// no image, can't read size
		if (empty($url))
		{
			return $currentDimensions;
		}

		// at least one dimension missing, try read size from file
		if (empty($currentDimensions['width']) || empty($currentDimensions['height']))
		{
			$newDimensions = self::getImageSize($url);
		}

		foreach ($currentDimensions as $key => $value)
		{
			if (empty($value) && !empty($newDimensions[$key]))
			{
				$currentDimensions[$key] = $newDimensions[$key];
			}
		}

		return $currentDimensions;
	}

	/**
	 * Finds the name of a Joomla template based
	 * on its internal style id
	 *
	 * @param $templateId
	 * @return mixed
	 */
	public static function getTemplateName($templateId)
	{
		static $templates = array();

		if (!isset($templates[$templateId]))
		{
			if (empty($templateId))
			{
				$templateName = 'wbamp';
			}
			else
			{
				// get template name from style id
				try
				{
					$templateName = ShlDbHelper::selectResult('#__template_styles', 'template', array('id' => $templateId));
				}
				catch (Exception $e)
				{
					ShlSystem_Log::error('wbamp', __METHOD__ . ' ' . $e->getMessage());
					$templateName = 'wbamp';
				}
			}
			$templates[$templateId] = $templateName;
		}

		return $templates[$templateId];
	}
}
