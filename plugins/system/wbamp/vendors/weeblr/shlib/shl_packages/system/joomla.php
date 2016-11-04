<?php
/**
 * Shlib - programming library
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier 2016
 * @package      shlib
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      0.3.1.587
 * @date        2016-10-31
 */

defined('_JEXEC') or die;

/**
 * Route helper
 *
 */
class ShlSystem_Joomla
{
	public static function getExtensionParams($extension, $options, $forceRead = false)
	{
		static $_params = array();

		if (!isset($_params[$extension]) || $forceRead)
		{
			try
			{
				$oldParams = ShlDbHelper::selectResult('#__extensions', 'params', $options);
				$_params[$extension] = new JRegistry();
				$_params[$extension]->loadString($oldParams);
			}
			catch (Exception $e)
			{
				$_params[$extension] = new JRegistry();
				ShlSystem_Log::error('shlib', '%s::%d: %s', __METHOD__, __LINE__, $e->getMessage());
			}
		}

		return $_params[$extension];
	}
}
