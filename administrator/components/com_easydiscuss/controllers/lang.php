<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class EasydiscussControllerLang extends EasyDiscussController
{
	public function getLanguage()
	{
		$languages	= JRequest::getVar( 'languages' );
		$result		= array();

		// If this is not an array, make it as an array.
		if( !is_array( $languages ) )
		{
			$languages	= array($languages);
		}

		foreach( $languages as $key )
		{
			$result[ $key ]	= JText::_( strtoupper( $key ) );
		}

		header('Content-type: text/x-json; UTF-8');

		echo json_encode($result);
		exit;
	}
}
