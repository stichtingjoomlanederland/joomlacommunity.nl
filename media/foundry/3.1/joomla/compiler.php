<?php
/**
 * @package		Foundry
 * @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Foundry is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . '/media/foundry/3.1/joomla/framework.php');

require_once(FD31_FOUNDRY_CLASSES . '/module.php');
require_once(FD31_FOUNDRY_LIB . '/cssmin.php');
require_once(FD31_FOUNDRY_LIB . '/closure.php');

jimport( 'joomla.filesystem.file' );

class FD31_FoundryCompiler
{
	private $modules  = array();
	private $adapters = array();
	public  $exclude  = array();

	public function __construct()
	{
	}

	public function getAdapter($adapterName='Foundry')
	{
		// If the adapter has been loaded, just return it.
		if (!empty($this->adapters[$adapterName])) {
			return $this->adapters[$adapterName];
		}

		// Try to get the adapter class
		$adapterClass = 'FD31_FoundryCompiler_' . $adapterName;

		if (!class_exists($adapterClass)) {

			// If the adapter class does not exist, try to load it.
			$adapterFile = JPATH_ROOT . '/administrator/components/com_' . strtolower($adapterName) . '/foundry.php';

			// If the adapter file is missing, stop.
			if (!file_exists($adapterFile)) {
				return null;
			}

			require_once($adapterFile);
		}

		// Create an instance of the adapter
		$this->adapters[$adapterName] = new $adapterClass($this);

		return $this->adapters[$adapterName];
	}

	public function getModule($moduleName, $moduleType='script', $adapterName='Foundry')
	{
		$adapter = $this->getAdapter($adapterName);

		// Create module instance
		$module = $adapter->createModule($moduleName, $moduleType, $adapterName);

		// Create adapter entry
		if (!array_key_exists($module->adapter, $this->modules)) {
			$this->modules[$module->adapter] = array();
		}

		// Create module type entry
		if (!array_key_exists($module->type, $this->modules[$module->adapter])) {
			$this->modules[$module->adapter][$module->type] = array();
		}

		// Create module entry
		if (!array_key_exists($module->name, $this->modules[$module->adapter][$module->type])) {

			// Store a reference to the module instance
			$this->modules[$module->adapter][$module->type][$module->name] = $module;

		} else {

			// Discard previously created module for existing one
			$module = $this->modules[$module->adapter][$module->type][$module->name];
		}

		return $module;
	}

	public function getDependencies($manifest, &$deps=array())
	{
		if (empty($manifest)) return;

		$manifests = (is_object($manifest)) ? array($manifest) : $manifest;

		foreach($manifests as $manifest) {

			$adapterName = (empty($manifest->adapter)) ? 'Foundry' : $manifest->adapter;
			$adapter = $this->getAdapter($adapterName);

			foreach($manifest as $moduleType => $moduleNames)
			{
				if ($moduleType=='adapter') continue;

				foreach($moduleNames as $moduleName) {

					// Create module entry
					$module = $this->getModule(
						$moduleName,
						$moduleType,
						$adapterName
					);

					if (!$module->added) {

						// Create an adapter entry
						if (!array_key_exists($module->adapter, $deps)) {
							$deps[$module->adapter] = array();
						}

						// Add it to the dependency tree
						if (!array_key_exists($module->type, $deps[$module->adapter])) {
							$deps[$module->adapter][$module->type] = array();
						};

						$deps[$module->adapter][$module->type][] = $module;

						$module->added = true;

						if ($module->type=='script') {

							// Crawl into module's dependencies
							$this->getDependencies($module->getManifest(), $deps);
						}
					}
				}
			}
		}

		return $deps;
	}

	public function getNames($modules)
	{
		$moduleNames = array();

		foreach ($modules as $module) {

			// If this is in the exclusion list, don't add it to the list.
			if (in_array($module->name, $this->exclude)) continue;

			$moduleNames[] = '"' . $module->name . '"';
		}

		 return '[' . implode(',', $moduleNames) . ']';
	}

	public function getData($modules)
	{
		$data = array();

		foreach ($modules as $module) {

			// If this is in the exclusion list, don't add it to the list.
			if (in_array($module->name, $this->exclude)) continue;

			$data[] = $module->getData();
		}

		return implode("\r\n", $data);
	}

	public function getJSONData($modules, $minify=false)
	{
		$data = array();

		foreach ($modules as $module) {

			// If this is in the exclusion list, don't add it to the list.
			if (in_array($module->name, $this->exclude)) continue;

			$data[$module->name] = $module->getData();
		}

		return json_encode($data);
	}

	public function getStylesheetData($stylesheets)
	{
		$data = new stdClass();
		$data->content = $this->minifyCSS($this->getData($stylesheets));

		return json_encode($data);
	}

	public function getManifest($file)
	{
		if (!JFile::exists($file)) {
			return null;
		}

		$content = JFile::read($file);

		if (empty($content)) {
			return null;
		}

		$manifest = json_decode($content);

		return $manifest;
	}

	public function getFoundry()
	{
		$foundry = FD31_FOUNDRY_PATH . '/scripts/foundry.js';

		$content = JFile::read($foundry);

		return $content;
	}

	public function build($compileMode='optimized', $deps=array(), $minify=false)
	{
		ob_start();
			include(FD31_FOUNDRY_PATH . '/joomla/compiler/' . $compileMode . '.php');
			$contents = ob_get_contents();
		ob_end_clean();

		if ($minify) {
			return $this->minifyJS($contents);
		}

		return $contents;
	}

	/*
		$manifest             Path to manifest file where dependencies will be crawled.

		$options['static']    Path to save static script (without extension). If this field is blank, static script won't be compiled.
		$options['optimized'] Path to save optimized script (without extension). If this field is blank, optimized script won't be compiled.
		$options['resources'] Path to save resources script (without extension). If this field is blank, optimized script won't be compiled.
		$options['minify']    Boolean to determine whether to minify script.
	*/

	public function compile($manifest="", $options)
	{
		$manifest = $this->getManifest($manifest);

		// If manifest is invalid, stop.
		if (empty($manifest)) {
			return;
		}

		// Build dependencies
		$deps = $this->getDependencies($manifest);

		$modes = array('static', 'optimized', 'resources');

		foreach($modes as $mode) {

			if (empty($options[$mode])) continue;

			$file = $options[$mode];

			// Uncompressed file
			$uncompressed = $this->build($mode, $deps);
			$state = JFile::write($file . '.js', $uncompressed);

			// Compressed file
			// We don't compress resources script.
			if (!empty($options["minify"]) && $options["minify"] && $mode!=='resources') {
				$compressed = $this->build($mode, $deps, true);
				$state = JFile::write($file . '.min.js', $compressed);
			}

			// Generate manifest file for resources
			if ($mode=='resources') {
				$resources_manifest = $this->build('resources_manifest', $deps);
				$state = JFile::write($file . '.json', $resources_manifest);
			}
		}

		return $options;
	}

	public function minifyJS($contents)
	{
		return FD31_ClosureCompiler::minify( $contents );
	}

	public function minifyCSS($contents)
	{
		return FD31_CssMin::minify($contents);
	}
}

class FD31_FoundryCompiler_Foundry {

	public $name = 'Foundry';

	public $path = FD31_FOUNDRY_PATH;

	public $compiler = null;

	public function __construct($compiler) {

		$this->compiler = $compiler;
	}

	public function createModule($moduleName, $moduleType, $adapterName)
	{
		if (empty($adapterName)) {
			$adapterName = $this->name;
		}

		// Rollback to foundry script when the module type if library
		if ($moduleType=='library') {
			$adapterName = 'Foundry';
			$moduleType  = 'script';
		}

		$module = new FD31_FoundryModule($this->compiler, $adapterName, $moduleName, $moduleType);

		return $module;
	}

	public function getPath($name, $type='script', $extension='')
	{
		switch ($type) {
			case 'script':
				$folder = 'scripts';
				break;

			case 'stylesheet':
				$folder = 'styles';
				break;

			case 'template':
				$folder = 'scripts';
				break;
		}

		return $this->path . '/' . $folder . '/' . $name . '.' . $extension;
	}

	private function getContent($name, $type='script', $extension='js')
	{
		$file = $this->getPath($name, $type, $extension);

		if (!JFile::exists($file)) {
			return null;
		}

		$content = JFile::read($file);

		return $content;
	}

	public function getManifest($name)
	{
		$manifestFile = $this->getPath($name, 'script', 'json');

		$manifest = $this->compiler->getManifest($manifestFile);

		return $manifest;
	}

	public function getScript($name)
	{
		$scriptContent = $this->getContent($name, 'script', 'js');

		return $scriptContent;
	}

	public function getStylesheet($name)
	{
		$stylesheetContent = $this->getContent($name, 'stylesheet', 'css');

		return $stylesheetContent;
	}

	public function getTemplate($name)
	{
		$templateContent = $this->getContent($name, 'template', 'htm');

		return $templateContent;
	}
}
