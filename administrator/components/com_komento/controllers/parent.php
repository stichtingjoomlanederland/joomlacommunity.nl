<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

require_once( KOMENTO_HELPER );

if( Komento::joomlaVersion() >= '3.0' )
{
	class KomentoParentController extends JControllerAdmin
	{
		function __construct($config = array())
		{
			return parent::__construct( $config );
		}
	}
}
else
{
	class KomentoParentController extends JController
	{
		function __construct($config = array())
		{
			return parent::__construct( $config );
		}
	}
}
