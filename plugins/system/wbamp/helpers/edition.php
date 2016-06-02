<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.3.1.490
 * @date		2016-05-18
 */

defined('_JEXEC') or die();

class WbampHelper_Edition
{
	public static $version = '1.3.1.490';
	public static $id = 'full';
	public static $name = '';
	public static $url = 'https://weeblr.com/joomla-accelerated-mobile-pages/wbamp';

	public static function is($edition)
	{
		return $edition == self::id;
	}
}
