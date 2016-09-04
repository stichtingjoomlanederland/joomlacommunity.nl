<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.5.0.585
 * @date        2016-08-25
 */

defined('_JEXEC') or die();

class WbampHelper_Content
{
	/**
	 * Removes all {wbamp} tags from a string
	 *
	 * @param $content
	 * @return mixed
	 */
	public static function scrubRegularHtmlPage($content)
	{
		// shortcut
		if (empty($content) || strpos($content, '{wbamp') == false || strpos($content, '{wbamp-no-scrub}') != false)
		{
			$content = str_replace('{wbamp-no-scrub}', '', $content);

			return $content;
		}

		// remove content that should only be displayed on AMP pages
		$regExp = '#{wbamp-show start}.*{wbamp-show end}#iuUs';
		$content = preg_replace($regExp, '', $content);

		// remove all remaining {wbamp tags
		$regex = '#{wbamp([^}]*)}#um';
		$content = preg_replace($regex, '', $content);

		return $content;
	}
}
