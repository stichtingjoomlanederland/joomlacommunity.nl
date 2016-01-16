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

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'parent.php' );

class KomentoControllerFoundry extends KomentoParentController
{
	public function getResource()
	{
		$resources = JRequest::getVar( 'resource' );

		$component = JRequest::getCmd( 'kmtcomponent' );

		Komento::setCurrentComponent( $component );

		if( !is_array( $resources ) )
		{
			header('Content-type: text/x-json; UTF-8');
			echo '[]';
			exit;
		}

		foreach( $resources as &$resource )
		{
			$resource = (object) $resource;

			switch( $resource->type )
			{
				case 'language':
					$resource->content = JText::_( strtoupper( $resource->name ) );
					break;
				case 'view':
					$template		= Komento::getTheme();
					$out			= $template->fetch( $resource->name . '.ejs' );

					if( $out !== false )
					{
						$resource->content = $out;
					}
					break;
			}
		}

		Komento::getClass('json', 'KomentoJson');

		header('Content-type: text/x-json; UTF-8');
		$json = new KomentoJson();
		echo $json->encode( $resources );
		exit;
	}
}
