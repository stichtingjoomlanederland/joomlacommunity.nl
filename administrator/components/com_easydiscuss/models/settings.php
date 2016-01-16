<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class EasyDiscussModelSettings extends EasyDiscussAdminModel
{
	function &getThemes()
	{
		static $themes	= null;

		if( is_null( $themes ) )
		{
			$themes	= JFolder::folders( DISCUSS_SITE_THEMES );
		}

		return $themes;
	}

	function save( $data )
	{
		$config	= JTable::getInstance( 'Configs' , 'Discuss' );
		$config->load( 'config' );

		$registry 		= DiscussHelper::getRegistry();
		$registry->loadString( $this->_getParams() );

		foreach( $data as $index => $value )
		{
			$registry->set( $index , $value );
		}
		// Get the complete INI string
		$config->params	= $registry->toString( 'INI' , 'discuss' );

		// Save it
		if(!$config->store() )
		{
			return false;
		}
		return true;
	}

	function &_getParams( $key = 'config' )
	{
		static $params	= null;

		if( is_null( $params ) )
		{
			$db		= DiscussHelper::getDBO();

			$query	= 'SELECT ' . $db->nameQuote( 'params' ) . ' '
					. 'FROM ' . $db->nameQuote( '#__discuss_configs' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'name' ) . '=' . $db->Quote( $key );
			$db->setQuery( $query );

			$params	= $db->loadResult();
		}

		return $params;
	}

	function getConfig()
	{
		static $config	= null;

		if( is_null( $config ) )
		{
			$params		= $this->_getParams( 'config' );


			$config		= DiscussHelper::getRegistry( $params );
		}

		return $config;
	}
}
