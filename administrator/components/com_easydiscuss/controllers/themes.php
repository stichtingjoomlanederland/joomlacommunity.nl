<?php
/**
 * @package		Easydiscuss
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Easydiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class EasydiscussControllerThemes extends EasyDiscussController
{
	public function getAjaxTemplate()
	{
		// Since this is the back end we need to load the front end's language file here.
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

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
			$template		= new DiscussThemes();
			$out			= $template->fetch( $file . '.ejs' );

			$obj			= new stdClass();
			$obj->name		= $file;
			$obj->content	= $out;

			$result[]		= $obj;
		}

		header('Content-type: text/javascript; UTF-8');
		echo json_encode($result);
		exit;
	}

	public function compile()
	{
		$config	= DiscussHelper::getConfig();
		$less = DiscussHelper::getHelper('less');

		// Force compile
		$less->compileMode = 'force';

		$name = JRequest::getCmd('name', null, 'GET');
		$type = JRequest::getCmd('type', null, 'GET');

		$result = new stdClass();

		if (isset($name) && isset($type)) {

			switch ($type) {
				case "admin":
					$result = $less->compileAdminStylesheet($name);
					break;

				case "site":
					$result = $less->compileSiteStylesheet($name);
					break;

				case "module":
					$result = $less->compileModuleStylesheet($name);
					break;

				default:
					$result->failed = true;
					$result->message = "Stylesheet type is invalid.";
			}

		} else {
			$result->failed = true;
			$result->message = "Insufficient parameters provided.";
		}

		header('Content-type: text/javascript; UTF-8');
		echo json_encode($result);
		exit;
	}
}
