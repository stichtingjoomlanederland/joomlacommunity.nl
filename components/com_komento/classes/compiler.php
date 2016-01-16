<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 StackIdeas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once( KOMENTO_FOUNDRY . '/joomla/compiler.php' );

class KomentoCompiler
{
	static $instance = null;

	public $resourceManifestFile;

	public function __construct()
	{
		$this->version		= (string) Komento::getHelper( 'Version' )->getLocalVersion();

		$this->resourceManifestFile = KOMENTO_RESOURCES . '/default-' . $this->version . '.json';
	}

	public static function getInstance()
	{
		if( is_null( self::$instance ) )
		{
			self::$instance	= new self();
		}

		return self::$instance;
	}

	/**
	 * Main compiler code that compiles javascript files.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool	Determines if the compiler should also minify the javascript codes
	 * @return
	 */
	public function compile( $minify=false )
	{
		$compiler	= new FD31_FoundryCompiler();

		// Create a master manifest containing all the scripts
		$manifest	= new stdClass();
		$manifest->adapter = 'Komento';
		$manifest->script = array();

		// Get a list of all the js files in the "scripts" folder
		jimport('joomla.filesystem.folder');

		// Get a list of scripts in the scripts folder
		$files = JFolder::files( KOMENTO_SCRIPTS , '.js$', true, true );

		// Normalize folder path
		$path = str_ireplace('\\', '/', KOMENTO_SCRIPTS);

		// Go through each of the manifest files.
		foreach ($files as $file)
		{
			// Normalize file path
			$file = str_ireplace('\\', '/', $file);

			// Remove the absolute path from the file name
			$file = str_ireplace($path . '/' , '', $file);

			// Excludes the files with the following patterns:
			// komento.static.js
			// komento.static.min.js
			// komento.optimized.js
			// komento.optimized.min.js
			// komento-x.x.x.static.js
			// komento-x.x.x.static.min.js
			// komento-x.x.x.optimized.js
			// komento-x.x.x.optimized.min.js
			// We use this pattern because we have komento.* scripts
			if (preg_match('/komento.*(static|optimized).*\.js/', $file)) continue;

			// Remove the .js extension from the file name.
			$file	= str_ireplace('.js', '', $file);

			// Add to the master manifest
			$manifest->script[] = $file;
		}


		// Write the manifest to a file
		$file     	= KOMENTO_SCRIPTS . '/manifest.json';
		$contents	= json_encode( $manifest );
		$contents 	= str_ireplace( '\\', '', $contents );
		$state    	= JFile::write( $file , $contents );

		// Set compiler options
		$options = array(
							"static"    => KOMENTO_SCRIPTS . '/komento-' . $this->version . '.static',
							"optimized" => KOMENTO_SCRIPTS . '/komento-' . $this->version . '.optimized',
							"resources" => KOMENTO_MEDIA   . '/resources/default-' . $this->version,
							"minify"    => $minify
						);

		$compiler->exclude = array(
									"ui/draggable",
									"ui/sortable",
									"ui/droppable",
									"ui/datepicker",
									"ui/timepicker",
									"flot",
									"sparkline",
									"plupload",
									"redactor",
									"moment"
								);

		// Compiler scripts
		return $compiler->compile($file, $options);
	}

	public function getResources()
	{
		static $resource;

		if (!empty($resource))
		{
			return $resource;
		}

		// Get manifest
		$manifest = $this->getResourcesManifest();

		// No manifest file found, use default js file.
		if (!$manifest)
		{
			return $this->getDefaultResources();
		}

		// Get current resource settings
		$settings = $this->getResourcesSettings();

		// Determine path based on the settings id
		$id   = $settings["id"];
		$path = KOMENTO_RESOURCES     . '/' . $id;
		$uri  = KOMENTO_RESOURCES_URI . '/' . $id;

		// Create resource object
		$resource = array(
			"id" => $id,
			"path" => $path . '.js',
			"uri"  => $uri  . '.js',
			"settings" => $settings
		);

		// Flag that determines if we can use this resource file
		$failed = false;

		// If the file hasn't been created
		$scriptFile   = $path . '.js';

		if (!JFile::exists($scriptFile))
		{
			// Compile the script file on-the-fly.
			if ($this->compileResources($scriptFile))
			{
				// Also save the settings into a json file
				$settingsFile = $path . '.settings.json';

				$jsonData = Komento::getHelper('json')->encode($settings);

				JFile::write( $settingsFile, $jsonData );

			// If unable to compile script
			}
			else
			{

				// Set flag to failed
				$failed = true;
			}
		}

		// If failed to compile resources
		if ($failed) {

			// Use default resource
			return $this->getDefaultResources();
		}

		return $resource;
	}

	public function getDefaultResources()
	{
		$file = '/default-' . $this->version . '.js';
		return array(
						"id"       => "default",
						"path"     => KOMENTO_RESOURCES     . $file,
						"uri"      => KOMENTO_RESOURCES_URI . $file,

						// TODO: Load from "default.settings.json"
						"settings" => null
					);
	}

	public function getResourcesManifest()
	{
		static $manifest;

		if (!empty($manifest))
		{
			return $manifest;
		}

		$file	= $this->resourceManifestFile;

		$manifest = false;

		$exists = JFile::exists( $file );

		if( !$exists )
		{
			return $manifest;
		}

		$data		= JFile::read($file);
		$manifest	= Komento::getHelper('json')->decode($data);

		return $manifest;
	}

	public function getResourcesSettings()
	{
		$config 	= Komento::getConfig();

		// Build a deterministic cache
		$settings = array(
							"language"	=> JFactory::getLanguage()->getTag(),
							"template"	=> array(
													"site"   => $config->get( 'layout_theme' )
											),
							"view"		=> array(),
							"modified"	=> filemtime($this->resourceManifestFile)
							);

		// Get manifest
		$manifest = $this->getResourcesManifest();

		if(isset($manifest[0]->view) && $manifest[0]->view)
		{
			foreach ($manifest[0]->view as $view)
			{
				$theme 		= Komento::getTheme();
				$path 		= $theme->resolve( $view . '.ejs' );

				// If the file still does not exist, we'll skip this
				if (!JFile::exists($path))
				{
					continue;
				}

				$settings["view"][] = array(
					"path"     => str_ireplace(JPATH_ROOT, '', $path),
					"modified" => filemtime($path)
				);
			}
		}

		// Build hash
		$settings["id"] = md5(serialize($settings));

		return $settings;
	}

	public function compileResources($file)
	{
		require_once( KOMENTO_FOUNDRY . '/joomla/compiler.php');

		$compiler = new FD31_FoundryCompiler();

		$manifest = $this->getResourcesManifest();
		$deps     = $compiler->getDependencies($manifest);

		$contents = $compiler->build('resources', $deps);

		$state = JFile::write($file, $contents);

		return $state;
	}

	public function purgeResources()
	{
        $files = JFolder::files( KOMENTO_RESOURCES , '.' , true, true);

		foreach( $files as $file )
		{
			if (strpos($file, 'default') !== false)
			{
				continue;
			}

			$state = JFile::delete( $file );
		}
	}
}
