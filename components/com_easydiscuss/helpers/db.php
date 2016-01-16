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

class DiscussDBHelper
{
	public $helper		= null;
	static $extConn 	= null;

	public function __construct()
	{
		$version 	= DiscussHelper::getJoomlaVersion();
		$className	= 'EasyDiscussDBJoomla15';

		if( $version >= '2.5' )
		{
			$className 	= 'EasyDiscussDBJoomla30';
		}

		$this->helper 	= new $className();
	}

	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}

		return call_user_func_array( array( $this->helper , $method ) , $refArray );
	}

	public function setVBConnection( $data = '' )
	{
		if( empty(self::$extConn) )
		{
			if( empty($data) )
			{
				$config   = DiscussHelper::getConfig();
				$driver   = $config->get( 'migrator_vBulletin_driver' );
				$host     = $config->get( 'migrator_vBulletin_host' );
				$user     = $config->get( 'migrator_vBulletin_user' );
				$password = $config->get( 'migrator_vBulletin_password' );
				$database = $config->get( 'migrator_vBulletin_name' );
				$prefix   = $config->get( 'migrator_vBulletin_prefix' );

				$data	= array( 'driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );

			}
		}

		self::$extConn = JDatabase::getInstance( $data );

		return self::$extConn;
	}

	public static function getVBConnection()
	{
		return self::$extConn;
	}
}

class EasyDiscussDbJoomla15
{
	public $db 		= null;

	public function __construct()
	{
		$this->db	= JFactory::getDBO();
	}

	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}

		return call_user_func_array( array( $this->db , $method ) , $refArray );
	}
}


class EasyDiscussDbJoomla30
{
	public $db 		= null;

	public function __construct()
	{
		$this->db	= JFactory::getDBO();
	}

	public function loadResultArray()
	{
		return $this->db->loadColumn();
	}

	public function nameQuote( $str )
	{
		return $this->db->quoteName( $str );
	}

	public function getEscaped($text, $extra = false)
	{
		return $this->db->escape($text, $extra);
	}

	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}

		return call_user_func_array( array( $this->db , $method ) , $refArray );
	}
}
