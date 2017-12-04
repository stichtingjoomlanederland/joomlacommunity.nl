<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/lessc.inc.php');

class EasyDiscussLess extends EasyDiscuss
{
	public $force = false;
	public $allowTemplateOverride = true;

	public function __construct()
	{
		$this->config = ED::config();

		parent::__construct();
	}

	/**
	 * Main stylesheet compiler
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function compileStylesheet($options = array())
	{
		// Load the assets library
		$assets = ED::assets();

		// Prepare result object
		$result = new stdClass();
		$result->in = $options['source'];
		$result->in_uri = $assets->toUri($options['source']);
		$result->out = $options['output'];
		$result->out_uri = $assets->toUri($options['output']);
		$result->compressed = $options['compressed'];
		$result->compressed_uri = $assets->toUri($options['compressed']);
		$result->cache = null;
		$result->failed = false;

		$this->compileMode = 'force';

		// Try to increase memory_limit
		@ini_set('memory_limit', '512M');

		// Force compile when target file does not exist.
		// This prevents less from failing to compile when
		// the css file was deleted but the cache file still retains.
		$exists = JFile::exists($result->out);

		if (!$exists) {
			$this->force = true;
		}

		// Check for force compilation mode
		if ($this->compileMode == "force") {
			$this->force = true;
		}

		// Perform the compiling now
		$this->compile($options);

		return $result;
	}

	/**
	 * Responsible to compile less to css for the back end
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function compileAdminStylesheet($theme_name)
	{
		$assets = ED::assets();

		// Not using this because it only returns the current theme
		// $assets->path('admin', 'styles');
		$admin = DISCUSS_ADMIN_THEMES . '/' . strtolower($theme_name) . '/styles';
		$admin_base = $assets->path('admin_base', 'styles');

		$in  = $admin . '/style.less';
		$out = $admin . '/style.css';

		// Compile the less stylesheet
		$result = $this->compileStylesheet($in, $out);

		// Offer failsafe alternative
		$result->failsafe = $admin . '/style.failsafe.css';
		$result->failsafe_uri = $assets->toUri($result->failsafe);

		return $result;
	}

	/**
	 * Retrieves the cache path
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getCacheFolder($file, $compressed = false)
	{
		$path = $compressed ? 'minified' : 'standard';
		$folder = dirname($file) . '/cache';

		if (!JFolder::exists($folder)) {
			JFolder::create($folder);
		}

		$folder = dirname($file) . '/cache/' . $path;

		if (!JFolder::exists($folder)) {
			JFolder::create($folder);
		}

		return $folder;
	}

	/**
	 * 
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function build($source, $destination, $minified = false)
	{
		// Prepare minified first
		$cacheFolder = $this->getCacheFolder($source, $minified);

		// If the folder doesn't exist, create it first
		if (!JFolder::exists($cacheFolder)) {
			JFolder::create($cacheFolder);
		}

		// Less files to cache
		$files = array($source => JURI::root());
		$options = array('cache_dir' => $cacheFolder, 'cache_method' => 'serialize', 'compress' => $minified);

		// Compile the less and generate the css file
		$outputFile = Less_Cache::Get($files, $options);
		$outputFile = $cacheFolder . '/' . $outputFile;

		// Store the css codes now
		$contents = JFile::read($outputFile);

		// Save the contents now
		JFile::write($destination, $contents);
	}

	/**
	 * Compiles the less files into css
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function compile($options)
	{
		$source = $options['source'];
		$destination = $options['output'];
		$compressed = $options['compressed'];

		// Compile non minified version first
		$this->build($source, $destination, false);

		// Compile minified version
		$this->build($source, $compressed, true);
	}

	public function compileModuleStylesheet($module_name)
	{
		$assets = DiscussHelper::getHelper('assets');
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		$module              = $assets->path('module', $module_name) . DIRECTORY_SEPARATOR . 'styles';
		$module_uri          = $assets->uri('module', $module_name) . '/styles';
		$module_override     = $assets->path('module_override', $module_name) . DIRECTORY_SEPARATOR . 'styles';
		$module_override_uri = $assets->uri('module_override', $module_name) . '/styles';

		$in  = $module . '/style.less';
		$out = $module . '/style.css';

		$importDir = array($module);

		// Additional overrides
		$hasTemplateOverride = false;

		if ($this->allowTemplateOverride)
		{
			// Partial override
			if (JFile::exists($module_override . '/override.less')) {
				$out = $module_override . '/style.css';
				$hasTemplateOverride = true;
			}

			// Full override
			if (JFile::exists($module_override . '/style.less')) {
				$in  = $module_override . '/style.less';
				$out = $module_override . '/style.css';
				$hasTemplateOverride = true;
			}

			// Add override folder to the stylesheet seek list
			if ($hasTemplateOverride) {
				$importDir = array_merge(
					array($module_override),
					$importDir
				);
			}
		}

		// Used to build relative uris
		$out_folder = dirname($assets->toUri($out));

		// Used to ensure uris are absolute
		$out_ext = ($config->get('layout_compile_external_asset_path_type')=="absolute") ? $out_folder . "/" : "";

		// Variables
		$variables = array();
		$variables["module"]     = "'file://".$module."'";
		$variables["module_uri"] = "'".$out_ext.$assets->relativeUri($module_uri, $out_folder)."'";

		if ($hasTemplateOverride) {
			$variables["module_override"]     = "'file://".$module_uri."'";
			$variables["module_override_uri"] = "'".$out_ext.$assets->relativeUri($module_override_uri, $out_folder)."'";
		}

		// Build settings
		$settings = array(
			"importDir" => $importDir,
			"variables" => $variables
		);

		// Compile
		$result = $this->compileStylesheet($in, $out, $settings);

		// Indicate if this was compiled to the override location
		$result->override = $hasTemplateOverride;

		// Offer failsafe alternative
		$result->failsafe     = $module . '/style.failsafe.css';
		$result->failsafe_uri = $assets->toUri($result->failsafe);

		return $result;
	}
}
