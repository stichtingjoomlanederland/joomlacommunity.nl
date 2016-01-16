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

class KomentoControllerThemes extends KomentoParentController
{
	public function getAjaxTemplate()
	{
		$files	= JRequest::getVar( 'names' , '' );

		if( empty( $files ) )
		{
			return false;
		}

		// Ensure the integrity of each items submitted to be an array.
		if( !is_array( $files ) )
		{
			$files	= array( $files );
		}

		$result		= array();


		foreach( $files as $file )
		{
			$template		= Komento::getTheme();
			$out			= $template->fetch( $file . '.ejs' );

			$obj			= new stdClass();
			$obj->name		= $file;
			$obj->content	= $out;

			$result[]		= $obj;
		}

		Komento::getClass('json', 'KomentoJson');
		header('Content-type: text/x-json; UTF-8');
		$json = new KomentoJson();
		echo $json->encode( $result );
		exit;
	}
}
