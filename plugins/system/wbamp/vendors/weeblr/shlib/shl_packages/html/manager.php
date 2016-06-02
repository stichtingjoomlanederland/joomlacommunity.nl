<?php
/**
 * Shlib - programming library
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier 2015
 * @package      shlib
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      0.3.1.522
 * @date         2016-05-17
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die;

/**
 * Manages html helpers
 *
 */
class ShlHtml_Manager
{
	const ASSETS_PATH = '/media/plg_shlib';

	const DEV        = 0;
	const PRODUCTION = 1;
	static public $assetsMode = self::PRODUCTION;

	const SINGLE = 0;
	const BUNDLE = 1;
	static public $assetsBundling = self::BUNDLE;

	static private $_assetsVersions = array();
	static private $_manager        = null;

	public static function getInstance()
	{
		if (is_null(self::$_manager))
		{
			$manager = new ShlHtml_Manager;
			$manager::$assetsMode = plgSystemShlib::$__params->get('assets_mode', self::PRODUCTION);
			$manager::$assetsBundling = plgSystemShlib::$__params->get('assets_bundling', self::BUNDLE);
			self::$_manager = $manager;
		}

		return self::$_manager;
	}

	// @deprecated
	public function addAssets($document, $options = array())
	{
		$theme = empty($options['theme']) ? 'default' : $options['theme'];
		$document->addStyleSheet($this->getMediaLink('theme.' . $theme, 'css', $options));

		return $this;
	}

	// @deprecated
	public function addSpinnerAssets($document, $options = array())
	{
		$document->addStyleSheet($this->getMediaLink('spinner', 'css', $options));
		$document->addScript($this->getMediaLink('spinner', 'js', $options));

		return $this;
	}

	/**
	 * Insert a script file in current document, possibly minified/versioned/gzipped
	 *
	 * @param string $name JS file name, no extension
	 * @param array  $options
	 *                     document    a J! document object, default to JFactory::getDocument() if missing
	 *                     files_root  Root path to file location, default to JPATH_ROOT
	 *                     files_path  Subpath to file location, will be added to files_root, default to /media/plg_shlib
	 *                     url_root    Root URL to link files to, default to JURI::root(true)
	 * @return $this
	 */
	public function addScript($name, $options = array())
	{
		$document = empty($options['document']) ? JFactory::getDocument() : $options['document'];
		$document->addScript($this->getMediaLink($name, 'js', $options));

		return $this;
	}

	/**
	 * Insert a CSS file in current document, possibly minified/versioned/gzipped
	 *
	 * @param string $name JS file name, no extension
	 * @param array  $options
	 *                     document    a J! document object, default to JFactory::getDocument() if missing
	 *                     files_root  Root path to file location, default to JPATH_ROOT
	 *                     files_path  Subpath to file location, will be added to files_root, default to /media/plg_shlib
	 *                     url_root    Root URL to link files to, default to JURI::root(true)
	 * @return $this
	 */
	public function addStylesheet($name, $options = array())
	{
		$document = empty($options['document']) ? JFactory::getDocument() : $options['document'];
		$document->addStyleSheet($this->getMediaLink($name, 'css', $options));

		return $this;
	}

	/**
	 * Build ups the full URL to a CSS or JS file, possibly minified/versioned/gzipped
	 *
	 * @param string $name JS file name, no extension
	 * @param string $type js | css
	 * @param array  $options
	 *                     files_root  Root path to file location, default to JPATH_ROOT
	 *                     files_path  Subpath to file location, will be added to files_root, default to /media/plg_shlib
	 *                     url_root    Root URL to link files to, default to JURI::root(true)
	 * @return string
	 */
	public function getMediaLink($name, $type, $options = array())
	{
		$root = empty($options['files_root']) ? JPATH_ROOT : $options['files_root'];
		$path = empty($options['files_path']) ? self::ASSETS_PATH : $options['files_path'];
		$base = empty($options['url_root']) ? JURI::root(true) : $options['url_root'];
		$hash = md5($name . $type . print_r($options, true));

		if (self::$assetsMode == self::PRODUCTION && !isset(self::$_assetsVersions[$hash]))
		{
			self::$_assetsVersions[$hash] = '';
			$jsonFile = $root . $path . '/dist/' . $type . '/version.json';
			if (file_exists($jsonFile))
			{
				$rawJson = file_get_contents($jsonFile);
				$decoded = json_decode($rawJson, true);
				self::$_assetsVersions[$hash] = empty($decoded) ? '' : '/' . $decoded['currentVersion'];
			}
		}

		$mode = isset($options['assets_mode']) ? $options['assets_mode'] : self::$assetsMode;
		$bundling = isset($options['assets_bundling']) ? $options['assets_bundling'] : self::$assetsBundling;
		if ($mode == self::PRODUCTION)
		{
			$link = $base . $path . '/dist/'
				. $type
				. self::$_assetsVersions[$hash]
				. '/' . ($bundling ? 'bundle' : $name)
				. '.min.' . $type;
		}
		else
		{
			$link = $base . $path . '/dist/' . $type . '/raw/' . $name . '.' . $type;
		}

		return $link;
	}

}
