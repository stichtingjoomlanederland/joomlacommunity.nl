<?php
/**
 * Shlib - programming library
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier 2016
 * @package      shlib
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      0.3.1.580
 * @date         2016-08-25
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

	const DEV = 0;
	const PRODUCTION = 1;
	static public $assetsMode = self::PRODUCTION;

	const SINGLE = 0;
	const BUNDLE = 1;
	static public $assetsBundling = self::BUNDLE;

	static private $_assetsVersions = array();
	static private $_manager        = null;

	private $_deferredScripts = array();

	public static function getInstance()
	{
		if (is_null(self::$_manager))
		{
			$manager = new ShlHtml_Manager;
			$manager::$assetsMode = plgSystemShlib::$__params->get('assets_mode', self::PRODUCTION);
			$manager::$assetsBundling = plgSystemShlib::$__params->get('assets_bundling', self::BUNDLE);
			$manager->initDeferredScripts();
			self::$_manager = $manager;
		}

		return self::$_manager;
	}

	public function hasDeferredScripts()
	{
		return !empty($this->_deferredScripts['links']) || !empty($this->_deferredScripts['declarations']);
	}

	public function getDeferredScripts()
	{
		return $this->_deferredScripts;
	}

	public function initDeferredScripts()
	{
		$this->_deferredScripts['links'] = array();
		$this->_deferredScripts['declarations'] = array();
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
	 * @param array $options
	 *                     document    a J! document object, default to JFactory::getDocument() if missing
	 *                     files_root  Root path to file location, default to JPATH_ROOT
	 *                     files_path  Subpath to file location, will be added to files_root, default to /media/plg_shlib
	 *                     url_root    Root URL to link files to, default to JURI::root(true)
	 *                     position    bottom || empty
	 *                     weight      int used to order scripts when inserted, lower is inserted first, higher inserted last
	 *                     raw         bool use link directly, don't optimize it
	 * @return $this
	 */
	public function addScript($name, $options = array())
	{
		$document = empty($options['document']) ? JFactory::getDocument() : $options['document'];
		$link = empty($options['raw']) ? $this->getMediaLink($name, 'js', $options) : $name;
		if (!empty($options['position']) && $options['position'] == 'bottom')
		{
			$weight = empty($options['weight']) ? 10 : (int) $options['weight'];
			// compute key, avoid duplicate links
			$key = empty($options['raw']) ?
				$name
				. (empty($options['files_root']) ? '' : $options['files_root'])
				. (empty($options['files_path']) ? '' : $options['files_path'])
				. (empty($options['url_root']) ? '' : $options['url_root'])
				:
				$name;
			$this->_deferredScripts['links'][$key] = array(
				'weight' => $weight,
				'script' => $link,
				'options' => $options
			);
		}
		else
		{
			$document->addScript($link);
		}
		return $this;
	}

	/**
	 * Insert a raw script, possibly at the bottom of the page
	 *
	 * @param string $content JS content, without script tags
	 * @param array $options
	 *                     document    a J! document object, default to JFactory::getDocument() if missing
	 *                     position    bottom || empty
	 *                     weight      int used to order scripts when inserted, lower is inserted first, higher inserted last
	 * @return $this
	 */
	public function addScriptDeclaration($content, $options = array())
	{
		$document = empty($options['document']) ? JFactory::getDocument() : $options['document'];
		if (!empty($options['position']) && $options['position'] == 'bottom')
		{
			$weight = empty($options['weight']) ? 10 : (int) $options['weight'];
			$this->_deferredScripts['declarations'][] = array(
				'weight' => $weight,
				'script' => $content,
				'options' => $options
			);
		}
		else
		{
			$document->addScriptDeclaration($content);
		}
		return $this;
	}

	/**
	 * Insert a CSS file in current document, possibly minified/versioned/gzipped
	 *
	 * @param string $name JS file name, no extension
	 * @param array $options
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
	 * @param array $options
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
