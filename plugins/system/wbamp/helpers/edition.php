<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.6.0.607
 * @date		2016-10-31
 */

defined('_JEXEC') or die();

class WbampHelper_Edition
{
	public static $version = '1.6.0.607';
	public static $id = 'full';
	public static $name = '';
	public static $url = 'https://weeblr.com/joomla-accelerated-mobile-pages/wbamp';

	public static function is($edition)
	{
		return $edition == self::id;
	}
}
