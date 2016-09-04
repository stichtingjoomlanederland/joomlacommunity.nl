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
 *
 *
 */

defined('_JEXEC') or die();

/**
 * Allow access to basic information about the current
 * request. Can help extensions adjust their content
 * when rendering an AMP request
 *
 * Usage:
 *
 * Are we processing an AMP request?
 * boolean WbAMP::isAMPRequest()
 *
 * Fully qualified canonical for the current AMP request
 * Empty if not an AMP request
 * string WbAMP::getCanonicalUrl()
 *
 * Get AMP URL for a path
 * If path is empty, current request is used
 * if $full is true, the URL is fully qualified
 * WbAMP::getAMPUrl($path = '', $full = true)
 *
 * WbAMP::getShURL() // not implemented yet
 *
 * Class WbAMP
 *
 * @method bool isAMPRequest()
 * @method string getCanonicalUrl()
 * @method string getAMPUrl($path = '', $full = true)
 * @method string getShURL()
 */
class WbAMP
{
	/**
	 * Private instance of the class
	 *
	 * @var null|WbAMP
	 */
	private static $api = null;

	/**
	 * Stores the wbAMP manager
	 *
	 * @var null|object
	 */
	private $_manager = null;

	/**
	 * Public initialize function to create
	 * an instance of the class
	 *
	 * @param $manager
	 */
	public static function init($manager)
	{
		self::$api = new self($manager);
	}

	/**
	 * Stores the wbAMP Manager and a copy
	 * of ourselves
	 *
	 * WbAMP constructor.
	 * @param object $manager
	 */
	private function __construct($manager)
	{
		$this->_manager = $manager;
		self::$api = $this;
	}

	/**
	 * Magic method to fetch information from WbAMP manager
	 *
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 */
	public static function __callStatic($name, $arguments)
	{
		return self::$api->_manager->$name($arguments);
	}
}
