<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

require_once( DISCUSS_CLASSES . '/lessc.inc.php' );

class DiscussLessHelper extends DiscussLessc
{
	public $force = false;

	public $allowTemplateOverride = true;

	public function compileStylesheet($in, $out, $settings=array())
	{
		$config = DiscussHelper::getConfig();
		$assets = DiscussHelper::getHelper('assets');

		// Prepare result object
		$result = new stdClass();
		$result->in      = $in;
		$result->in_uri  = $assets->toUri($in);
		$result->out     = $out;
		$result->out_uri = $assets->toUri($out);
		$result->cache   = null;
		$result->failed  = false;

		if ($this->compileMode=="off") {
			$result->cache = $this->getExistingCacheStructure($in);
			return $result;
		}

		// If incoming file does not exist, stop.
		if (!JFile::exists($result->in)) {
			$result->failed = true;
			$result->message = "Could not open main stylesheet file \"style.less\".";
			return $result;
		}

		// Force compile when target file does not exist.
		// This prevents less from failing to compile when
		// the css file was deleted but the cache file still retains.
		if (!JFile::exists($result->out) ) {
			$this->force = true;
		}

		if ($this->compileMode=="force") {
			$this->force = true;
		}

		// Used to build relative uris
		$out_folder = dirname($result->out_uri);

		// Used to ensure uris are absolute
		$out_ext = ($config->get('layout_compile_external_asset_path_type')=="absolute") ? $out_folder . '/' : "";

		// Default settings
		$defaultSettings = array(

			"importDir" => array(
				"media"   => $assets->path('media'  , 'styles'),
				"foundry" => $assets->path('foundry', 'styles')
			),

			"variables" => array(
				"root"     => "'" . $assets->fileUri("root") . "'",
				"root_uri" => "'" . $out_ext . $assets->relativeUri($assets->uri('root'), $out_folder) . "'"
			)
		);

		// Common locations
		$locations = array(
			"admin", "admin_base",
			"site", "site_base",
			"media","foundry"
		);

		// Also include template overrides
		if ($this->allowTemplateOverride)
		{
			$locations[] = "admin_override";
			$locations[] = "site_override";
		}

		// This creates a pair of variables for each location,
		// one of itself, one of the uri counterpart.
		foreach ($locations as $location) {
			$defaultSettings["variables"][$location]          = "'" . $assets->fileUri($location, 'styles') . "'";
			$defaultSettings["variables"][$location . '_uri'] = "'" . $out_ext . $assets->relativeUri($assets->uri($location, 'styles'), $out_folder) . "'";
		}

		// Mixin settings
		$settings = array_merge_recursive($settings, $defaultSettings);

		$this->setImportDir($settings["importDir"]);

		$this->setVariables($settings["variables"]);

		// Compile stylesheet
		try {

			$result->cache = $this->cachedCompileFile($in, $out, $this->force);

		} catch (Exception $ex) {

			$result->failed = true;
			$result->message = 'LESS Error: ' . $ex->getMessage() . 'error';
			DiscussHelper::setMessageQueue( $result->message );
		}

		return $result;
	}

	public function compileAdminStylesheet($theme_name)
	{
		$assets = DiscussHelper::getHelper('assets');

		// Not using this because it only returns the current theme
		// $assets->path('admin', 'styles');
		$admin          = DISCUSS_ADMIN_THEMES . '/' . strtolower($theme_name) . '/styles';
		$admin_base     = $assets->path('admin_base', 'styles');
		$admin_override = $assets->path('admin_override', 'styles');

		$in  = $admin . '/style.less';
		$out = $admin . '/style.css';

		$importDir = array(
			$admin,
			$admin_base
		);

		// Additional overrides
		$hasTemplateOverride = false;

		if ($this->allowTemplateOverride)
		{
			// Partial override
			if (JFile::exists($admin_override . '/override.less')) {
				$out = $admin_override . '/style.css';
				$hasTemplateOverride = true;
			}

			// Full override
			if (JFile::exists($admin_override . '/style.less')) {
				$in  = $admin_override . '/style.less';
				$out = $admin_override . '/style.css';
				$hasTemplateOverride = true;
			}

			// Add override folder to the stylesheet seek list
			if ($hasTemplateOverride) {
				$importDir = array_merge(
					array($admin_override),
					$importDir
				);
			}
		}

		// Build settings
		$settings = array(
			"importDir" => $importDir
		);

		// Compile
		$result = $this->compileStylesheet($in, $out, $settings);

		// Indicate if this was compiled to the override location
		$result->override = $hasTemplateOverride;

		// Offer failsafe alternative
		$result->failsafe     = $admin . '/style.failsafe.css';
		$result->failsafe_uri = $assets->toUri($result->failsafe);

		return $result;
	}

	public function compileSiteStylesheet($theme_name)
	{
		$assets = DiscussHelper::getHelper('assets');

		// Not using this because it only returns the current theme
		// $assets->path('site', 'styles');
		$site          = DISCUSS_SITE_THEMES . '/' . strtolower($theme_name) . '/styles';
		$site_base     = $assets->path('site_base', 'styles');
		$site_override = $assets->path('site_override', 'styles');

		$in  = $site . '/style.less';
		$out = $site . '/style.css';

		$importDir = array(
			$site,
			$site_base
		);

		// Additional overrides
		$hasTemplateOverride = false;

		if ($this->allowTemplateOverride)
		{
			// Partial override
			if (JFile::exists($site_override . '/override.less')) {
				$out = $site_override . '/style.css';
				$hasTemplateOverride = true;
			}

			// Full override
			if (JFile::exists($site_override . '/style.less')) {
				$in  = $site_override . '/style.less';
				$out = $site_override . '/style.css';
				$hasTemplateOverride = true;
			}

			// Add override folder to the stylesheet seek list
			if ($hasTemplateOverride) {
				$importDir = array_merge(
					array($site_override),
					$importDir
				);
			}
		}

		// Build settings
		$settings = array(
			"importDir" => $importDir
		);

		// Compile
		$result = $this->compileStylesheet($in, $out, $settings);

		// Indicate if this was compiled to the override location
		$result->override = $hasTemplateOverride;

		// Offer failsafe alternative
		$result->failsafe     = $site . '/style.failsafe.css';
		$result->failsafe_uri = $assets->toUri($result->failsafe);

		return $result;
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

	public function getCachePath($in)
	{
		return dirname($in) . DIRECTORY_SEPARATOR . basename($in) . '.cache';
	}

	public function getExistingCacheStructure($in)
	{
		// Get cache file for the provided source
		$cachePath = $this->getCachePath($in);

		// If cache file exists, retrieve cache structure.
		if (JFile::exists($cachePath))
		{
			$cacheContent = JFile::read($cachePath);
			$cacheStructure = unserialize($cacheContent);
			return $cacheStructure;
		} else {
			return null;
		}
	}

	/**
	 * Parses all the files in the queue and return a valid css file.
	 *
	 * @access	public
	 * @param	Array		An array of options. ( 'destination' => '/path/to/css/file' )
	 * @return	string		The compiled output.
	 */
	public function cachedCompileFile($in, $out, $force=false)
	{
		// Check if source file exists
		if (!JFile::exists($in)) {
			$this->throwError('Source less file does not exist.');
			return null;
		}

		$cachePath = $this->getCachePath($in);

		// Try to get existing cache structure
		$cacheStructure = $this->getExistingCacheStructure($in);

		// If it doesn't exist, just pass in the source path
		if ($force || is_null($cacheStructure)) {
			$cacheStructure = $in;
		}

		// Compile stylesheet
		$newCacheStructure = $this->cachedCompile($cacheStructure, $force);

		if (!is_array($cacheStructure) || $newCacheStructure['updated'] > $cacheStructure['updated'])
		{
			$cacheContent = serialize($newCacheStructure);

			// Write cache file & stylesheet file.
			JFile::write($cachePath, $cacheContent);
			JFile::write($out, $newCacheStructure['compiled']);
		}

		// Return cache structure
		return $newCacheStructure;
	}
}
