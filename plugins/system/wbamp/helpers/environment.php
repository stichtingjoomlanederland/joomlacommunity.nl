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
		self::disablePageSpeed();
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

	/**
	 * Send header to disable PageSpeed which can
	 * insert javascript on the fly, at least
	 * prior to June 2016. Google announced
	 * PS will not alter AMP page in an upcomnig release
	 */
	private static function disablePageSpeed()
	{
		header('PageSpeed: off');
	}
}
