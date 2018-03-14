<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

class FD31_FoundryCompiler_EasyDiscuss extends FD31_FoundryCompiler_Foundry
{
	public $name = 'EasyDiscuss';

	public $path = DISCUSS_MEDIA;

	public function __construct($compiler)
	{
		$this->loadLanguage();

		return parent::__construct($compiler);
	}

	public function createModule($moduleName, $moduleType, $adapterName)
	{
		// Rollback to foundry script when the module type if library
		if ($moduleType=='library')
		{
			$adapterName = 'Foundry';
			$moduleType  = 'script';
		}

		if ($adapterName=='EasyDiscuss')
		{
			if ($moduleType!=='language')
			{
				$moduleName = 'easydiscuss/' . $moduleName;
			}
		}

		$module = new FD31_FoundryModule($this->compiler, $adapterName, $moduleName, $moduleType);

		return $module;
	}

	public function getPath($name, $type='script', $extension='')
	{
		switch ($type)
		{
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

		return $this->path . '/' . $folder . '/' . str_replace('easydiscuss/', '', $name) . '.' . $extension;
	}

	public function getLanguage($name)
	{
		return JText::_($name);
	}

	/**
	 * We cannot rely on PHP's Foundry object here because this might be called through CLI
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getView( $name )
	{
		$name	= str_replace('easydiscuss/', '', $name);

		if( defined( 'EASYDISCUSS_COMPONENT_CLI' ) )
		{
			// Break down the namespace to segments
			$segments	= explode( '/' , $name );

			// Determine the current location
			$location 	= $segments[ 0 ];

			unset( $segments[ 0 ] );

			// @TODO: We should read the db and see which is the default theme
			if( $segments[ 0 ] == 'media' )
			{
				$path 		= JPATH_ROOT . '/media/com_easydiscuss/scripts/media';
			}
			else
			{
				$path 		= JPATH_ROOT . '/components/com_easydiscuss/themes/simplistic';
			}

			$path 	= $path . '/' . implode( '/' , $segments ) . '.ejs';

			jimport( 'joomla.filesystem.file' );

			if( !JFile::exists( $path ) )
			{
				return '';
			}

			ob_start();
			include( $path );
			$contents	= ob_get_contents();
			ob_end_clean();
		}
		else
		{

			jimport( 'joomla.filesystem.file' );

			$path 		= JPATH_ROOT . '/components/com_easydiscuss/themes/simplistic/' . $name . '.ejs';

			$system 			= new stdClass();
			$system->config 	= DiscussHelper::getConfig();

			ob_start();
			include( $path );
			$contents 	= ob_get_contents();
			ob_end_clean();
			$template 	= new DiscussThemes();
			$contents	= $template->fetch( $name . '.ejs' );
		}

		return $contents;
	}

	private function loadLanguage()
	{
		// Load up language files
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT . '/administrator' );
	}

}
