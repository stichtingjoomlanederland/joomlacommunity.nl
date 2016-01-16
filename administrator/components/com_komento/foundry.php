<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

class FD31_FoundryCompiler_Komento extends FD31_FoundryCompiler_Foundry
{
	public $name = 'Komento';

	public $path = KOMENTO_MEDIA;

	public function __construct($compiler)
	{
		$this->loadLanguage();

		return parent::__construct($compiler);
	}

	public function createModule($moduleName, $moduleType, $adapterName)
	{
		// Rollback to foundry script when the module type if library
		if ($moduleType=='library') {
			$adapterName = 'Foundry';
			$moduleType  = 'script';
		}

		if ($adapterName=='Komento') {
			if ($moduleType!=='language') {
				$moduleName = 'komento/' . $moduleName;
			}
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

		return $this->path . '/' . $folder . '/' . str_replace('komento/', '', $name) . '.' . $extension;
	}

	public function getLanguage($name)
	{
		return JText::_($name);
	}

	public function getView( $name )
	{
		// Due to compatibility, we had to append komento/ infront of the views, but we do not want this when fetching template
		if( strpos( 'komento/', $name ) >= 0 )
		{
			$name = substr( $name, 8 );
		}

		$template = Komento::getTheme();
		$out = $template->fetch($name . '.ejs');

		$contents = '';

		if ($out !== false)
		{
			$contents = $out;
		}

		return $contents;
	}

	private function loadLanguage()
	{
		// Load up language files
		JFactory::getLanguage()->load( 'com_komento' , JPATH_ROOT );
		JFactory::getLanguage()->load( 'com_komento' , JPATH_ROOT . '/administrator' );
	}
}
