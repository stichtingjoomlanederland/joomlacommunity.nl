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

defined('_JEXEC') or die;

/**
 * Wrapper class for config and runtime data
 *
 */
class WbampHelper_Runtime
{
	public static $base             = null;
	public static $basePath         = null;
	public static $layoutsBasePaths = array();
	public static $params           = null;
	public static $joomlaConfig     = null;

	/**
	 * List of embedable tags
	 * @TODO implement: vine, add: pinterest, vimeo
	 * @var array
	 */
	public static $embedTags = array(

		// auto embeddable URLs
		'twitter' => array(
			'url_regexp' => '#http(s|):\/\/twitter\.com(\/\#\!\/|\/)([a-zA-Z0-9_]{1,20})\/status(es)*\/(\d+)#iu',
			'script' => 'twitter',
			'amp_tag' => 'amp-twitter')
		,

		'instagram' => array(
			'url_regexp' => '#https?:\/\/(?:www\.)?instagr(?:\.am|am\.com)/p/([a-zA-Z0-9]+)#iu',
			'script' => 'instagram',
			'amp_tag' => 'amp-instagram')
		,
		'vine' => array(
			'url_regexp' => '#https?:\/\/vine\.co/v/([^/?]+)#iu',
			'script' => 'vine',
			'amp_tag' => 'amp-vine')
		,
		'youtube' => array(
			'url_regexp' => '#(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v=|(?:embed|v|e)\/))([a-zA-Z0-9-&;=]+)#iu',
			'script' => 'youtube',
			'amp_tag' => 'amp-youtube')
		,
		'dailymotion' => array(
			'url_regexp' => '#https?:\/\/(?:www\.)?dailymotion\.com\/video\/([^_]+)#iu',
			'script' => 'dailymotion',
			'amp_tag' => 'amp-dailymotion')
		,
		'facebook' => array(
			'url_regexp' => '#https?:\/\/(?:www\.)?(?:facebook\.com\/)([^\/]+)\/(posts|videos)\/(\d+)#iu',
			'script' => 'facebook',
			'amp_tag' => 'amp-facebook')
		,

		// AMP extended elements
		'carousel' => array(
			'url_regexp' => '',
			'script' => 'carousel',
			'amp_tag' => 'amp-carousel')
		,
		'user-notification' => array(
			'url_regexp' => '',
			'script' => 'user-notification',
			'amp_tag' => 'amp-user-notification')
	);

	/**
	 * Detect if standalone mode is enabled and valid
	 *
	 * @return bool
	 */
	public static function isStandaloneMode()
	{
		return WbampHelper_Edition::$id == 'full' && self::$params->get('operation_mode', 'normal') == 'standalone';
	}
}
