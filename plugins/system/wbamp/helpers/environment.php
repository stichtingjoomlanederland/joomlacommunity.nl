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

class WbampHelper_Environment
{
	/**
	 * Runs checks and take actions based on server configuration
	 * or other scripts or conditions that may be interfering
	 * with the AMP page
	 *
	 * Should be called only within the context of rendering
	 * an actuall AMP page, not on a regular HTML page
	 */
	public static function handleSpecificEnvironment()
	{
		self::disableNewRelic();
	}

	/**
	 * Disable NewRelic APM agent, which otherwise
	 * injects <script> tag in page
	 *
	 * @return void
	 */
	private static function disableNewRelic()
	{
		if (extension_loaded('newrelic') && function_exists('newrelic_disable_autorun'))
		{
			newrelic_disable_autorun();
		}
	}
}
