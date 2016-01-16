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

class EasyDiscussControllerFoundry extends EasyDiscussController
{
	public function getResource()
	{
		if( $resources = JRequest::getVar( 'resource' ) )
		{
			foreach( $resources as &$resource )
			{
				$resource	= (object) $resource;
				$func		= 'get' . ucfirst( $resource->type );

				if( !method_exists( $this , $func ) )
				{
					continue;
				}
				
				$result		= self::$func( $resource->name );

				if( $result !== false )
				{
					$resource->content = $result;
				}
			}

			header('Content-type: text/x-json; UTF-8');
			$json = new Services_JSON();
			echo $json->encode( $resources );
		}

		exit;
	}

	/**
	 * Gets a view for the caller.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getView( $name = '', $type = '', $prefix = '', $config = array() )
	{
		$theme	= new DiscussThemes();
		$file	= $name . '.ejs';

		$output	= $theme->fetch( $file );

		return $output;
	}

	public function getLanguage( $lang )
	{
		// Load language support for front end and back end.
		JFactory::getLanguage()->load( JPATH_ADMINISTRATOR );
		JFactory::getLanguage()->load( JPATH_ROOT );

		return JText::_( strtoupper( $lang ) );
	}
}
