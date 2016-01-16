<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class KomentoRegistry
{
	public $registry 	= null;

	public function __construct( $contents = '', $type = 'ini' )
	{
		$this->registry 	= new JRegistry();

		if( !empty( $contents ) )
		{
			$this->load( $contents, $type );
		}
	}

	public function extend( $extend )
	{
		if( !is_array( $extend ) )
		{
			$extend = $extend->toArray();
		}

		foreach( $extend as $index => $value )
		{
			// if( is_array( $value ) )
			// {
			// 	$tmpValue	= '';

			// 	for( $i = 0; $i < count( $value ); $i++ )
			// 	{
			// 		$tmpValue	.= $value[ $i ];

			// 		if( next( $value ) !== false )
			// 		{
			// 			$tmpValue	.= ',';
			// 		}
			// 	}

			// 	$value = $tmpValue;
			// }

			$this->set( $index, $value );
		}
	}

	public function bind( $data )
	{
		if( method_exists( $this->registry , 'bind' ) )
		{
			return $this->bind( $data );
		}

		return $this->bindData( $data );
	}

	public function load( $strData, $type = 'ini' )
	{
		if( $type === 'json' )
		{
			$json = $this->getJSON();

			$data = $json->decode( $strData );

			if( is_object( $data ) || is_array( $data ) )
			{
				foreach( $data as $key => $value )
				{
					$this->set( $key, $value );
				}
			}
		}

		if( $type === 'ini' )
		{
			if( $this->isJoomla15() )
			{

				$this->registry->loadINI( $strData , '' );
			}
			else
			{
				$this->registry->loadString( $strData );
			}
		}

		return $this->registry;
	}

	public function get( $key , $default = null )
	{
		if( $this->isJoomla15() )
		{
			return $this->registry->getValue( $key , $default );
		}

		$value = $this->registry->get( $key , $default );

		return $value;
	}

	public function set( $key , $value )
	{
		if( $this->isJoomla15() )
		{
			return $this->registry->setValue( $key , $value );
		}

		return $this->registry->set( $key , $value );
	}

	public function toString( $type = 'json' )
	{
		if( $this->isJoomla15() )
		{
			if( $type === 'ini' )
			{
				return $this->registry->toString( $type );
			}

			if( $type === 'json' )
			{
				return $this->getJSON()->encode( $this->registry->toObject() );
			}
		}

		return $this->registry->toString( $type );
	}

	// Prepare all the private methods to avoid depending on Komento class
	private function joomlaVersion()
	{
		$jVerArr	= explode('.', JVERSION);
		$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	private function isJoomla30()
	{
		return $this->joomlaVersion() >= '3.0';
	}

	private function isJoomla25()
	{
		return $this->joomlaVersion() >= '1.6' && $this->joomlaVersion() <= '2.5';
	}

	private function isJoomla15()
	{
		return $this->joomlaVersion() == '1.5';
	}

	private function getJSON()
	{
		static $json = null;

		if( is_null( $json ) )
		{
			require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'json.php' );

			$json = new KomentoJson();
		}

		return $json;
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
		return call_user_func_array( array( $this->registry , $method ) , $refArray );
	}
}
