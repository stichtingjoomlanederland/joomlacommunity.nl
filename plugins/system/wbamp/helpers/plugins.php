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
defined('_JEXEC') or die;

/**
 * Extensions helper
 *
 */
class WbampHelper_Plugins
{
	/**
	 * Read stored parameters for a plugin from the DB
	 *
	 * @param bool $forceRead
	 * @return JRegistry|null
	 */
	public static function getParams($folder, $element, $forceRead = false)
	{
		static $_params = array();

		$key = $folder . $element;

		if (!isset($_params[$key]) || $forceRead)
		{
			try
			{
				$oldParams = ShlDbHelper::selectResult('#__extensions', 'params', array('element' => $element, 'folder' => $folder, 'type' => 'plugin'));
				$_params[$key] = new JRegistry();
				$_params[$key]->loadString($oldParams);
			}
			catch (Exception $e)
			{
				$_params[$key] = new JRegistry();
				ShlSystem_Log::error('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, $e->getMessage());
			}
		}

		return $_params[$key];
	}
}
