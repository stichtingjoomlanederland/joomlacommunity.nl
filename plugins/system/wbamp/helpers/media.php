<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.6.0.607
 * @date        2016-10-31
 */

defined('_JEXEC') or die();

class WbampHelper_Media
{
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
			$newDimensions = ShlHtmlContent_Image::getImageSize($url);
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
