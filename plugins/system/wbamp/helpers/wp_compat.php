<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.4.2.551
 * @date        2016-07-19
 */

defined('_JEXEC') or die();

if (!defined('AMP__DIR__'))
{
	$basePath = realpath(realpath(__DIR__) . '/..');
	define('AMP__DIR__', $basePath . '/vendors/wp');
}

/**
 * https://codex.wordpress.org/Function_Reference/sanitize_key
 */
if (!function_exists('sanitize_key'))
{
	function sanitize_key($key)
	{
		$key = strtolower($key);
		$key = preg_replace('/[^a-z0-9_\-]/', '', $key);

		return $key;
	}
}

if (!function_exists('absint'))
{
	function absint($maybeint)
	{
		return abs(intval($maybeint));
	}
}

if (!function_exists('esc_attr'))
{
	function esc_attr($string)
	{
		return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
	}
}

if (!function_exists('set_url_scheme'))
{
	function set_url_scheme($url, $scheme = 'https')
	{
		$url = trim($url);
		if (substr($url, 0, 2) === '//')
		{
			$url = 'http:' . $url;
		}

		$url = preg_replace('#^\w+://#', $scheme . '://', $url);

		return $url;
	}
}

if (!class_exists('AMP_Image_Dimension_Extractor'))
{
	class AMP_Image_Dimension_Extractor
	{
		static public function extract($url)
		{
			$dimensions = WbampHelper_Media::getImageSize($url);
			return array($dimensions['width'], $dimensions['height']);
		}
	}
}

