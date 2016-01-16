<?php
/**
* @package      Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

class KomentoLegacy15
{
	static function loadRegistry( $id, $params )
	{
		$registry = JRegistry::getInstance( $id, $id );
		$registry->loadINI( $params );
		return $registry;
	}

	static function getToken()
	{
		return JUtility::getToken();
	}

	static function JFactory_getConfig( $key, $default = null )
	{
		$jconfig = JFactory::getConfig();
		return $jconfig->getValue( $key, $default );
	}

	static function JParameter_get( $params, $path, $default = null )
	{
		if( !$params instanceof JParameter )
		{
			return false;
		}

		return $params->getValue( $path, $default );
	}

	static function JParameter_exists( $params, $path )
	{
		if( !$params instanceof JParameter )
		{
			return false;
		}

		// Explode the registry path into an array
		if ($nodes = explode('.', $path))
		{
			// Get the namespace
			$count = count($nodes);

			if ($count < 2) {
				$namespace = $params->_defaultNameSpace;
			} else {
				$namespace = array_shift($nodes);
				$count--;
			}

			if (!isset($params->_registry[$namespace]))
			{
				return false;
			}

			for ($i = 0; $i < $count; $i ++)
			{
				if (isset($params->_registry[$namespace]['data']->$nodes[$i]))
				{
					return true;
				}
			}
		}

		return false;
	}
}

class KomentoLegacy16
{
	static function loadRegistry( $id, $params )
	{
		$registry = JRegistry::getInstance( $id );
		$registry->loadString( $params, 'INI' );
		return $registry;
	}

	static function getToken()
	{
		$session = JFactory::getSession();
		return $session->getFormToken();
	}

	static function JFactory_getConfig( $key, $default = null )
	{
		$jconfig = JFactory::getConfig();
		return $jconfig->get( $key, $default );
	}

	static function JParameter_get( $params, $path, $default = null )
	{
		if( !$params instanceof JRegistry )
		{
			return false;
		}

		return $params->get( $path );
	}

	static function JParameter_exists( $params, $path )
	{
		if( !$params instanceof JRegistry )
		{
			return false;
		}

		return $params->exists( $path );
	}
}
