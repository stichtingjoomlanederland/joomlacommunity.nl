<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( KOMENTO_ROOT . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'view.php' );
jimport('joomla.html.parameter');

require_once( KOMENTO_CLASSES . DIRECTORY_SEPARATOR . 'themes.php' );
require_once( KOMENTO_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

class KomentoViewFlags extends KomentoView
{
	function mark()
	{
		$type		= JRequest::getVar('type');
		$id			= JRequest::getInt('id');
		$userId		= JFactory::getUser()->id;

		$ajax		= Komento::getHelper('Ajax');
		$commentObj	= Komento::getComment( $id );

		$commentObj->load( $id );
		$commentObj->flag = $type;
		$commentObj->flag_by = $userId;

		if( !$commentObj->save() )
		{
			$ajax->fail();
			$ajax->send();
		}

		$ajax->success();
		$ajax->send();
	}
}
