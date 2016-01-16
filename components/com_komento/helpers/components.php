<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class KomentoComponentsHelper
{
	/**
	* Get Joomla extensions that can commentify!
	* return array
	*/
	public function getAvailableComponents()
	{
		static $components = array();

		if( empty( $components ) )
		{
			// find each component folders
			$folders	= JFolder::folders( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components', 'com_', false, false, array( '.svn', 'CVS', '.DS_Store', '__MACOSX', 'com_komento' ) );

			foreach( $folders as $folder )
			{
				if( JFile::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components'  . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'komento_plugin.php' ) )
				{
					$components[$folder]	= $folder;
				}
			}

			// find in plugins folder
			foreach( $folders as $folder )
			{
				if( JFile::exists( KOMENTO_ROOT . DIRECTORY_SEPARATOR . 'komento_plugins' . DIRECTORY_SEPARATOR . $folder . '.php' ) )
				{
					$components[$folder]	= $folder;
				}
			}


			// cleaning up duplicates
			$components = array_unique($components);

			// check against the Joomla extension table
			foreach( $components as $key => $component )
			{
				if( !JComponentHelper::isEnabled( $component ) )
				{
					unset( $components[$key] );
				}
			}
		}

		return $components;
	}

	/**
	 * @access	public
	 * @param	string	$optionName	The component element
	 * @return	boolean	True if the component is installed
	 */
	public static function isInstalled( $optionName )
	{
		self::_clean( $optionName );
		$componentName = substr($optionName, 4);

		if( $componentName && ( JFile::exists( JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.$optionName.DIRECTORY_SEPARATOR.'admin.'.$componentName.'.php') || JFile::exists( JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.$optionName.DIRECTORY_SEPARATOR.$componentName.'.php' ) ) )
		{
			return true;
		}
	}

	/**
	 * @access	public
	 * @param	string	$optionName	The component element
	 * @return	boolean	True if the component is installed and enabled
	 */
	public static function isEnabled( $componentName )
	{
		self::_clean( $componentName );

		$sql = Komento::getSql();

		$sql->select( '#__extensions' )
			->column( 'enabled' )
			->where( 'type', 'component' )
			->where( 'element', $componentName );

		return $sql->loadResult();
	}

	private static function _clean( &$componentName )
	{
		$componentName	= preg_replace('/[^A-Z0-9_\.-]/i', '', $componentName);
	}

	public function getSupportedComponents()
	{
		static $supported = array();

		if( empty( $supported ) )
		{
			$files = JFolder::files( KOMENTO_PLUGINS, 'com_', false, false, array( '.svn', 'CVS', '.DS_Store', '__MACOSX', 'com_sample.php', 'com_sampletemplate' ) );

			foreach( $files as $file )
			{
				// Remove the .php from the filename
				$tmp = explode( '.', $file );

				$supported[] = array_shift( $tmp );
			}
		}

		return $supported;
	}
}
